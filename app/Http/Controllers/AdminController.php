<?php

namespace App\Http\Controllers;

use App\Models\AffiliateCommission;
use App\Models\AffiliateProfile;
use App\Models\AffiliateWithdrawal;
use App\Models\NotificationLog;
use App\Models\Order;
use App\Models\Product;
use App\Models\TrackingLog;
use App\Models\User;
use App\Services\AffiliateService;
use App\Services\NotificationService;
use App\Services\OrderService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class AdminController extends Controller
{
    public function __construct(
        protected NotificationService $notificationService,
        protected OrderService        $orderService,
        protected AffiliateService    $affiliateService,
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

        $todayStart = now()->startOfDay();
        $monthStart = now()->startOfMonth();

        $stats = [
            'orders_today'           => Order::where('created_at', '>=', $todayStart)->count(),
            'orders_month'           => Order::where('created_at', '>=', $monthStart)->count(),
            'revenue_today'          => Order::where('status', 'completed')->where('completed_at', '>=', $todayStart)->sum('total_amount'),
            'revenue_month'          => Order::where('status', 'completed')->where('completed_at', '>=', $monthStart)->sum('total_amount'),
            'total_orders'           => Order::count(),
            'total_revenue'          => Order::whereNotNull('payment_verified_at')->sum('total_amount'),
            'pending_orders'         => Order::where('status', 'pending')->count(),
            'verified_orders'        => Order::where('status', 'verified')->count(),
            'processing_orders'      => Order::where('status', 'processing')->count(),
            'shipped_orders'         => Order::where('status', 'shipped')->count(),
            'completed_orders'       => Order::where('status', 'completed')->count(),
            'cancelled_orders'       => Order::where('status', 'cancelled')->count(),
            'total_affiliates'       => AffiliateProfile::count(),
            'active_affiliates'      => AffiliateProfile::where('status', 'active')->count(),
            'pending_affiliates'     => AffiliateProfile::where('status', 'pending')->count(),
            'pending_withdrawals'    => AffiliateWithdrawal::where('status', 'pending')->count(),
            'commission_month'       => AffiliateCommission::where('status', 'earned')->where('earned_at', '>=', $monthStart)->sum('amount'),
            'total_affiliate_balance'=> AffiliateProfile::sum('balance'),
            'active_products'        => Product::where('is_active', true)->count(),
        ];

        $recentOrders = Order::with(['customer', 'affiliate.affiliateProfile'])->latest()->take(10)->get();

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
        $order->fill($updates)->saveQuietly();

        $statusLabel = [
            'processing' => 'Sedang Diproses',
            'shipped'    => 'Pesanan Dikirim',
            'completed'  => 'Pesanan Diterima',
            'cancelled'  => 'Pesanan Dibatalkan',
        ][$data['status']] ?? ucfirst($data['status']);

        TrackingLog::create([
            'order_id'     => $order->id,
            'status_title' => $statusLabel,
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
            'event'                    => 'required|string',
            'shipping_tracking_number' => 'nullable|string|max:100',
        ]);

        $event = $request->event;

        // Map event → new order status
        $statusMap = [
            'order.processing' => 'processing',
            'order.shipped'    => 'shipped',
            'order.delivered'  => 'completed',
        ];

        if (isset($statusMap[$event])) {
            $newStatus = $statusMap[$event];
            $updates   = ['status' => $newStatus];

            if ($newStatus === 'shipped') {
                $updates['shipped_at'] = now();
                if ($request->filled('shipping_tracking_number')) {
                    $updates['shipping_tracking_number'] = $request->shipping_tracking_number;
                }
            }
            if ($newStatus === 'completed') $updates['completed_at'] = now();

            // Normal update — OrderObserver fires notification automatically
            $order->update($updates);

            $label = [
                'processing' => 'Sedang Diproses',
                'shipped'    => 'Pesanan Dikirim',
                'completed'  => 'Pesanan Diterima',
            ][$newStatus];

            TrackingLog::create([
                'order_id'     => $order->id,
                'status_title' => $label,
                'description'  => 'Status diperbarui melalui notifikasi manual.',
            ]);
        } else {
            // Non-status-changing events (e.g. payment.confirmed) → send manually
            $this->notificationService->notifyOrderStatus($order, $event);
        }

        return back()->with('success', 'Notifikasi terkirim dan status pesanan diperbarui.');
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

    // ─── Affiliate Withdrawals ──────────────────────────────────────────────

    /** GET /admin/withdrawals */
    public function withdrawals(Request $request): View
    {
        $this->ensureAdmin();

        $allowed = ['pending', 'completed', 'rejected', 'all'];
        $status  = in_array($request->get('status'), $allowed) ? $request->get('status') : 'pending';
        $withdrawals = AffiliateWithdrawal::with(['affiliate.affiliateProfile'])
            ->when($status !== 'all', fn ($q) => $q->where('status', $status))
            ->latest()
            ->paginate(20)
            ->appends(['status' => $status]);

        $pendingCount = AffiliateWithdrawal::pending()->count();

        return view('admin.withdrawals', compact('withdrawals', 'status', 'pendingCount'));
    }

    /** POST /admin/withdrawals/{withdrawal}/approve */
    public function approveWithdrawal(AffiliateWithdrawal $withdrawal): RedirectResponse
    {
        $this->ensureAdmin();

        $profile = null;

        DB::transaction(function () use ($withdrawal, &$profile) {
            // Pessimistic lock — prevents double-approval on concurrent requests
            $locked = AffiliateWithdrawal::lockForUpdate()->find($withdrawal->id);

            if ($locked->status !== 'pending') {
                return;
            }

            $locked->update([
                'status'       => 'completed',
                'processed_at' => now(),
                'processed_by' => Auth::id(),
            ]);

            $withdrawal->refresh();
            $profile = $withdrawal->affiliate->affiliateProfile ?? null;
        });

        if ($withdrawal->status !== 'completed') {
            return back()->with('error', 'Permintaan ini sudah diproses.');
        }

        if ($profile) {
            $this->notificationService->notifyAffiliateWithdrawalProcessed($profile, $withdrawal, true);
        }

        return back()->with('success', 'Pencairan berhasil disetujui dan affiliate telah diberitahu.');
    }

    /** POST /admin/withdrawals/{withdrawal}/reject */
    public function rejectWithdrawal(Request $request, AffiliateWithdrawal $withdrawal): RedirectResponse
    {
        $this->ensureAdmin();

        $validated = $request->validate([
            'rejection_reason' => 'nullable|string|max:500',
        ]);

        $reason  = trim($validated['rejection_reason'] ?? '') ?: 'Ditolak oleh admin.';
        $profile = null;

        DB::transaction(function () use ($withdrawal, $reason, &$profile) {
            // Pessimistic lock — prevents double-refund on concurrent requests
            $locked = AffiliateWithdrawal::lockForUpdate()->find($withdrawal->id);

            if ($locked->status !== 'pending') {
                return;
            }

            // Refund the balance back to the affiliate profile
            $profile = $withdrawal->affiliate->affiliateProfile ?? null;
            if ($profile) {
                $profile->increment('balance', $withdrawal->amount);
            }

            $locked->update([
                'status'           => 'rejected',
                'processed_at'     => now(),
                'processed_by'     => Auth::id(),
                'rejection_reason' => $reason,
            ]);

            $withdrawal->refresh();
        });

        if ($withdrawal->status !== 'rejected') {
            return back()->with('error', 'Permintaan ini sudah diproses.');
        }

        if ($profile) {
            $this->notificationService->notifyAffiliateWithdrawalProcessed($profile, $withdrawal, false, $reason);
        }

        return back()->with('success', 'Pencairan ditolak dan saldo affiliate telah dikembalikan.');
    }

    // ─── Helper ────────────────────────────────────────────────────────────

    private function ensureAdmin(): void
    {
        abort_unless(Auth::check() && Auth::user()->role === 'admin', 403, 'Akses ditolak.');
    }

    // ─── Products ──────────────────────────────────────────────────────────

    /** GET /admin/products */
    public function products(Request $request): View
    {
        $this->ensureAdmin();

        $products = Product::withTrashed()
            ->when($request->search, fn ($q, $v) => $q->where('name', 'like', "%{$v}%")->orWhere('brand', 'like', "%{$v}%"))
            ->latest()
            ->paginate(20)
            ->appends($request->only('search'));

        return view('admin.products', compact('products'));
    }

    /** GET /admin/products/create */
    public function createProduct(): View
    {
        $this->ensureAdmin();
        return view('admin.product-form', ['product' => null]);
    }

    /** POST /admin/products */
    public function storeProduct(Request $request): RedirectResponse
    {
        $this->ensureAdmin();

        $data = $request->validate([
            'name'            => 'required|string|max:255',
            'brand'           => 'nullable|string|max:100',
            'type'            => 'nullable|string|max:100',
            'category'        => 'nullable|string|max:100',
            'description'     => 'nullable|string',
            'technical_specs' => 'nullable|string',
            'price'           => 'required|numeric|min:0',
            'stock'           => 'required|integer|min:0',
            'master_video_url'=> 'nullable|url|max:500',
            'thumbnail_url'   => 'nullable|url|max:500',
            'is_active'       => 'boolean',
        ]);

        $data['is_active'] = $request->boolean('is_active', true);

        Product::create($data);

        return redirect()->route('admin.products')->with('success', 'Produk berhasil ditambahkan.');
    }

    /** GET /admin/products/{product}/edit */
    public function editProduct(Product $product): View
    {
        $this->ensureAdmin();
        return view('admin.product-form', compact('product'));
    }

    /** PUT /admin/products/{product} */
    public function updateProduct(Request $request, Product $product): RedirectResponse
    {
        $this->ensureAdmin();

        $data = $request->validate([
            'name'            => 'required|string|max:255',
            'brand'           => 'nullable|string|max:100',
            'type'            => 'nullable|string|max:100',
            'category'        => 'nullable|string|max:100',
            'description'     => 'nullable|string',
            'technical_specs' => 'nullable|string',
            'price'           => 'required|numeric|min:0',
            'stock'           => 'required|integer|min:0',
            'master_video_url'=> 'nullable|url|max:500',
            'thumbnail_url'   => 'nullable|url|max:500',
            'is_active'       => 'boolean',
        ]);

        $data['is_active'] = $request->boolean('is_active');

        $product->update($data);

        return redirect()->route('admin.products')->with('success', 'Produk berhasil diperbarui.');
    }

    /** DELETE /admin/products/{product} */
    public function deleteProduct(Product $product): RedirectResponse
    {
        $this->ensureAdmin();
        $product->delete();
        return back()->with('success', 'Produk berhasil dihapus (soft delete).');
    }

    // ─── Users ─────────────────────────────────────────────────────────────

    /** GET /admin/users */
    public function users(Request $request): View
    {
        $this->ensureAdmin();

        $users = User::withCount('orders')
            ->when($request->search, fn ($q, $v) => $q->where('name', 'like', "%{$v}%")->orWhere('email', 'like', "%{$v}%"))
            ->when($request->role, fn ($q, $v) => $q->where('role', $v))
            ->latest()
            ->paginate(25)
            ->appends($request->only('search', 'role'));

        return view('admin.users', compact('users'));
    }

    // ─── Audit Log ─────────────────────────────────────────────────────────

    /** GET /admin/audit-log */
    public function auditLog(Request $request): View
    {
        $this->ensureAdmin();

        $logs = TrackingLog::with('order.customer')
            ->when($request->search, fn ($q, $v) =>
                $q->where('status_title', 'like', "%{$v}%")
                  ->orWhere('description', 'like', "%{$v}%")
                  ->orWhereHas('order', fn ($oq) => $oq->where('order_number', 'like', "%{$v}%"))
            )
            ->orderByDesc('created_at')
            ->paginate(30)
            ->appends($request->only('search'));

        return view('admin.audit-log', compact('logs'));
    }

    // ─── Commissions ───────────────────────────────────────────────────────

    /** GET /admin/commissions */
    public function commissions(Request $request): View
    {
        $this->ensureAdmin();

        $commissions = AffiliateCommission::with(['order.customer', 'affiliate.affiliateProfile'])
            ->when($request->status, fn ($q, $v) => $q->where('status', $v))
            ->latest()
            ->paginate(25)
            ->appends($request->only('status'));

        $summary = [
            'pending' => AffiliateCommission::where('status', 'pending')->sum('amount'),
            'earned'  => AffiliateCommission::where('status', 'earned')->sum('amount'),
        ];

        return view('admin.commissions', compact('commissions', 'summary'));
    }
}
