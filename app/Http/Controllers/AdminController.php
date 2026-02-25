<?php

namespace App\Http\Controllers;

use App\Models\AffiliateProfile;
use App\Models\NotificationLog;
use App\Models\Order;
use App\Models\TrackingLog;
use App\Services\NotificationService;
use App\Services\OrderService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class AdminController extends Controller
{
    public function __construct(
        protected NotificationService $notificationService,
        protected OrderService        $orderService,
    ) {}

    // Auth
    public function showLogin(): View|RedirectResponse
    {
        if (Auth::check() && Auth::user()->role === 'admin') {
            return redirect()->route('admin.dashboard');
        }

        return view('admin.login');
    }

    /** POST /admin/login */
    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials)) {
            if (Auth::user()->role !== 'admin') {
                Auth::logout();
                return back()->withErrors(['email' => 'Akses ditolak.']);
            }

            $request->session()->regenerate();
            return redirect()->route('admin.dashboard');
        }

        return back()->withErrors(['email' => 'Email atau password salah.'])->onlyInput('email');
    }

    /** POST /admin/logout */
    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }

    // Dashboard

    /** GET /admin/dashboard */
    public function dashboard(): View
    {
        $this->ensureAdmin();

        $stats = [
            'total_orders'      => Order::count(),
            'total_revenue'     => Order::whereNotNull('payment_verified_at')->sum('total_amount'),
            'pending_orders'    => Order::where('status', 'pending')->count(),
            'total_affiliates'  => AffiliateProfile::count(),
            'pending_affiliates'=> AffiliateProfile::where('status', 'pending')->count(),
            'verified_orders'   => Order::where('status', 'verified')->count(),
            'processing_orders' => Order::where('status', 'processing')->count(),
            'shipped_orders'    => Order::where('status', 'shipped')->count(),
            'completed_orders'  => Order::where('status', 'completed')->count(),
            'cancelled_orders'  => Order::where('status', 'cancelled')->count(),
        ];

        $recentOrders = Order::with('customer')->latest()->take(10)->get();

        $affiliatePerformance = AffiliateProfile::with(['user'])
            ->withCount('commissions as conversions_count')
            ->withSum('commissions as commissions_sum_amount', 'amount')
            ->orderByDesc('commissions_sum_amount')
            ->take(5)
            ->get();

        return view('admin.dashboard', compact('stats', 'recentOrders', 'affiliatePerformance'));
    }

    // Orders
    /** GET /admin/orders */
    public function orders(Request $request): View
    {
        $this->ensureAdmin();

        $orders = Order::with(['customer', 'affiliate.affiliateProfile'])
            ->when($request->status, fn ($q, $v) => $q->where('status', $v))
            ->latest()
            ->paginate(20);

        return view('admin.orders', compact('orders'));
    }

    /** GET /admin/orders/{order} */
    public function showOrder(Order $order): View
    {
        $this->ensureAdmin();

        $order->load(['customer', 'affiliate.affiliateProfile', 'items', 'trackingLogs', 'commission']);

        return view('admin.order-detail', compact('order'));
    }

    /** PUT /admin/orders/{order}/status */
    public function updateStatus(Request $request, Order $order): RedirectResponse
    {
        $this->ensureAdmin();

        $data = $request->validate([
            'status'                   => 'required|in:processing,shipped,completed,cancelled',
            'shipping_tracking_number' => 'nullable|string|max:100',
            'note'                     => 'nullable|string|max:500',
        ]);

        $updates = ['status' => $data['status']];

        if ($data['status'] === 'shipped') {
            $updates['shipped_at'] = now();
            // courier already stored on order — keep as-is; update resi if provided
            if (! empty($data['shipping_tracking_number'])) {
                $updates['shipping_tracking_number'] = $data['shipping_tracking_number'];
            }
        }

        if ($data['status'] === 'completed') {
            $updates['completed_at'] = now();
        }

        if ($data['status'] === 'cancelled') {
            $updates['cancelled_at'] = now();
            if ($data['note']) {
                $updates['cancellation_reason'] = $data['note'];
            }
        }

        // Suppress observer so notification fires once (explicit call below, with note)
        Order::withoutObservers(fn () => $order->update($updates));

        TrackingLog::create([
            'order_id'     => $order->id,
            'status_title' => 'Status Diperbarui: ' . ucfirst($data['status']),
            'description'  => $data['note'] ?? null,
        ]);

        $event = match($data['status']) {
            'processing' => 'order.processing',
            'shipped'    => 'order.shipped',
            'completed'  => 'order.delivered',
            default      => null,
        };

        if ($event) {
            $this->notificationService->notifyOrderStatus($order->fresh(), $event, $data['note'] ?? null);
        }

        // On manual complete, also notify affiliate balance credited
        if ($data['status'] === 'completed') {
            $this->notificationService->notifyAffiliateBalanceCredited($order->fresh());
        }

        return back()->with('success', 'Status pesanan berhasil diperbarui.');
    }

    /** POST /admin/orders/{order}/notify */
    public function sendNotification(Request $request, Order $order): RedirectResponse
    {
        $this->ensureAdmin();

        $request->validate([
            'event' => 'required|string',
        ]);

        $this->notificationService->notifyOrderStatus($order, $request->event);

        return back()->with('success', 'Notifikasi berhasil dikirim.');
    }

    /** POST /admin/orders/{order}/simulate-payment (local only) */
    public function simulatePayment(Order $order): RedirectResponse
    {
        $this->ensureAdmin();

        abort_unless(! app()->isProduction(), 403, 'Hanya tersedia di environment non-production.');

        $this->orderService->verifyPayment($order, 'SIMULATED-' . now()->timestamp);

        return back()->with('success', 'Simulasi pembayaran berhasil diproses.');
    }

    // Affiliate
    
    /** GET /admin/affiliates */
    public function affiliates(Request $request): View
    {
        $this->ensureAdmin();

        $affiliates = AffiliateProfile::with('user')
            ->withCount('commissions as conversions_count')
            ->withCount('affiliateClicks as referral_clicks_count')
            ->withSum('commissions as total_commission_amount', 'amount')
            ->when($request->status, fn ($q, $v) => $q->where('status', $v))
            ->latest()
            ->paginate(20);

        $pendingCount = AffiliateProfile::where('status', 'pending')->count();

        return view('admin.affiliates', compact('affiliates', 'pendingCount'));
    }

    /** POST /admin/affiliates/{affiliate}/approve */
    public function approveAffiliate(AffiliateProfile $affiliate): RedirectResponse
    {
        $this->ensureAdmin();

        $affiliate->update([
            'status'      => 'active',
            'approved_at' => now(),
            'approved_by' => Auth::id(),
        ]);

        // Notify affiliate user
        if ($affiliate->user?->telegram_chat_id) {
            $this->notificationService->notifyAffiliateApproved($affiliate);
        }

        return back()->with('success', 'Affiliate ' . $affiliate->user?->name . ' berhasil diapprove.');
    }

    /** POST /admin/affiliates/{affiliate}/reject */
    public function rejectAffiliate(AffiliateProfile $affiliate): RedirectResponse
    {
        $this->ensureAdmin();

        $affiliate->update(['status' => 'suspended']);

        return back()->with('success', 'Affiliate ' . $affiliate->user?->name . ' ditolak/dinonaktifkan.');
    }

    // ─── Notifications ─────────────────────────────────────────────────────

    /** GET /admin/notifications */
    public function notifications(Request $request): View
    {
        $this->ensureAdmin();

        $notifications = NotificationLog::with(['order', 'user'])
            ->latest()
            ->paginate(30);

        return view('admin.notifications', compact('notifications'));
    }

    // ─── Helper ────────────────────────────────────────────────────────────

    private function ensureAdmin(): void
    {
        abort_unless(Auth::check() && Auth::user()->role === 'admin', 403, 'Akses ditolak.');
    }
}
