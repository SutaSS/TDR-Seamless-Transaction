<?php

namespace App\Http\Controllers;

use App\Jobs\SendTelegramNotification;
use App\Models\Affiliate;
use App\Models\Notification;
use App\Models\Order;
use App\Models\OrderStatusHistory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AdminController extends Controller
{
    // -------------------------------------------------------------------------
    // Auth
    // -------------------------------------------------------------------------

    public function showLogin(): View
    {
        return view('admin.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        // Guard: cek role admin
        if (Auth::attempt(['email' => $credentials['email'], 'password' => $credentials['password']])) {
            if (Auth::user()->role === 'admin') {
                $request->session()->regenerate();
                return redirect()->route('admin.dashboard');
            }
            Auth::logout();
        }

        return back()->withErrors(['email' => 'Email atau password salah, atau akun bukan admin.'])->withInput();
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('admin.login');
    }

    // -------------------------------------------------------------------------
    // Dashboard
    // -------------------------------------------------------------------------

    public function dashboard(): View
    {
        $stats = [
            'total_orders'     => Order::count(),
            'pending_orders'   => Order::where('order_status', 'pending')->count(),
            'paid_orders'      => Order::where('order_status', 'paid')->count(),
            'shipped_orders'   => Order::where('order_status', 'shipped')->count(),
            'delivered_orders' => Order::where('order_status', 'delivered')->count(),
            'total_affiliates' => Affiliate::count(),
            'total_revenue'    => Order::whereIn('order_status', ['paid', 'shipped', 'delivered'])->sum('total_amount'),
        ];

        $recentOrders = Order::with(['customer', 'affiliate'])
            ->latest()
            ->limit(10)
            ->get();

        $affiliatePerformance = Affiliate::withCount('conversions')
            ->withSum('conversions', 'commission_amount')
            ->orderByDesc('total_conversions')
            ->limit(5)
            ->get();

        return view('admin.dashboard', compact('stats', 'recentOrders', 'affiliatePerformance'));
    }

    // -------------------------------------------------------------------------
    // Orders
    // -------------------------------------------------------------------------

    public function orders(Request $request): View
    {
        $query = Order::with(['customer', 'affiliate', 'payment'])->latest();

        if ($request->filled('status')) {
            $query->where('order_status', $request->status);
        }

        $orders = $query->paginate(20);

        return view('admin.orders', compact('orders'));
    }

    public function showOrder(Order $order): View
    {
        $order->load(['customer', 'affiliate', 'items', 'payment', 'statusHistories.changedBy', 'conversion']);
        return view('admin.order-detail', compact('order'));
    }

    /**
     * PUT /admin/orders/{order}/status
     *
     * Rules:
     * - pending → paid (via webhook, but fallback allowed)
     * - paid → shipped (tracking_number REQUIRED)
     * - shipped → delivered
     */
    public function updateStatus(Request $request, Order $order): RedirectResponse
    {
        $rules = ['status' => 'required|in:paid,shipped,delivered'];

        if ($request->status === 'shipped') {
            $rules['tracking_number']  = 'required|string|max:100';
            $rules['shipping_provider'] = 'nullable|string|max:100';
        }

        $validated = $request->validate($rules);

        $oldStatus = $order->order_status;
        $newStatus = $validated['status'];

        // Validate transition
        $allowed = [
            'pending'  => ['paid'],
            'paid'     => ['shipped'],
            'shipped'  => ['delivered'],
            'delivered'=> [],
        ];

        if (! in_array($newStatus, $allowed[$oldStatus] ?? [])) {
            return back()->withErrors(['status' => "Status tidak bisa diubah dari {$oldStatus} ke {$newStatus}"]);
        }

        $updateData = ['order_status' => $newStatus];

        if ($newStatus === 'shipped') {
            $updateData['tracking_number']   = $validated['tracking_number'];
            $updateData['shipping_provider'] = $validated['shipping_provider'] ?? null;
        }

        if ($newStatus === 'delivered') {
            $updateData['delivered_at'] = now();
        }

        $order->update($updateData);

        // Log status history
        OrderStatusHistory::create([
            'order_id'        => $order->id,
            'from_status'     => $oldStatus,
            'to_status'       => $newStatus,
            'changed_by_user_id' => Auth::id(),
            'note'            => $request->input('note'),
            'changed_at'      => now(),
        ]);

        // Insert notification & dispatch job
        $eventType = match ($newStatus) {
            'shipped'   => 'order.shipped',
            'delivered' => 'order.delivered',
            default     => 'order.status_updated',
        };

        $msgMap = [
            'shipped'   => "*Pesanan Dikirim!*\nNomor: #{$order->order_number}\nNoResi: " . ($validated['tracking_number'] ?? '-'),
            'delivered' => "*Pesanan Tiba!*\nNomor: #{$order->order_number}\nTerima kasih sudah berbelanja di TDR HPZ.",
            'default'     => "*Update Pesanan*\nNomor: #{$order->order_number}\nStatus: {$newStatus}",
        ];

        $recipientId = $order->affiliate?->user_id ?? $order->customer_user_id;
        $chatId      = $order->affiliate?->user?->telegram_chat_id ?? $order->customer?->telegram_chat_id;

        $notif = Notification::create([
            'user_id'                    => $recipientId,
            'order_id'                   => $order->id,
            'event_type'                 => $eventType,
            'channel'                    => 'telegram',
            'recipient_chat_id_snapshot' => $chatId,
            'message_body'               => $msgMap[$newStatus] ?? $msgMap['default'],
            'status'                     => 'queued',
        ]);

        SendTelegramNotification::dispatch($notif->id);

        return back()->with('success', "Status pesanan berhasil diubah ke {$newStatus}.");
    }

    // -------------------------------------------------------------------------
    // Affiliates
    // -------------------------------------------------------------------------

    public function affiliates(): View
    {
        $affiliates = Affiliate::with('user')
            ->withCount('referralClicks')
            ->withCount('conversions')
            ->orderByDesc('total_commission_amount')
            ->paginate(20);

        return view('admin.affiliates', compact('affiliates'));
    }

    // -------------------------------------------------------------------------
    // Notifications log
    // -------------------------------------------------------------------------

    public function notifications(): View
    {
        $notifications = Notification::with(['user', 'order'])
            ->latest()
            ->paginate(30);

        return view('admin.notifications', compact('notifications'));
    }
}
