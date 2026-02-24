<?php

namespace App\Http\Controllers;

use App\Models\Affiliate;
use App\Models\Notification;
use App\Models\Order;
use App\Models\OrderStatusHistory;
use App\Services\NotificationService;
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
            'total_orders'       => Order::count(),
            'pending_orders'     => Order::where('order_status', 'pending')->count(),
            'processing_orders'  => Order::where('order_status', 'processing')->count(),
            'shipped_orders'     => Order::where('order_status', 'shipped')->count(),
            'delivered_orders'   => Order::where('order_status', 'delivered')->count(),
            'cancelled_orders'   => Order::where('order_status', 'cancelled')->count(),
            'paid_orders'        => Order::where('payment_status', 'paid')->count(),
            'total_affiliates'   => Affiliate::count(),
            'pending_affiliates' => Affiliate::where('status', 'pending')->count(),
            'total_revenue'      => Order::where('payment_status', 'paid')->sum('total_amount'),
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
     * Admin hanya bisa mengubah order_status:
     *   processing → shipped  (butuh tracking_number)
     *   shipped    → delivered
     *
     * Pembayaran (unpaid → paid) bukan tugas admin — dikerjakan oleh
     * Midtrans webhook (POST /api/webhook/payment) yang akan meng-update
     * payment_status dan memicu notifikasi via OrderObserver.
     */
    public function updateStatus(Request $request, Order $order): RedirectResponse
    {
        $rules = ['status' => 'required|in:shipped,delivered'];

        if ($request->status === 'shipped') {
            $rules['tracking_number']   = 'required|string|max:100';
            $rules['shipping_provider'] = 'nullable|string|max:100';
        }

        $validated = $request->validate($rules);

        $newStatus = $validated['status'];
        $oldStatus = $order->order_status;

        $allowed = [
            'processing' => ['shipped'],
            'shipped'    => ['delivered'],
        ];

        if (! in_array($newStatus, $allowed[$oldStatus] ?? [])) {
            return back()->withErrors(['status' => "Status tidak bisa diubah dari {$oldStatus} ke {$newStatus}."]);
        }

        $updateData = ['order_status' => $newStatus];

        if ($newStatus === 'shipped') {
            $updateData['tracking_number']   = $validated['tracking_number'];
            $updateData['shipping_provider'] = $validated['shipping_provider'] ?? null;
        }

        if ($newStatus === 'delivered') {
            $updateData['delivered_at'] = now();
        }

        // Update triggers OrderObserver → NotificationService → kirim Telegram
        $order->update($updateData);

        OrderStatusHistory::create([
            'order_id'           => $order->id,
            'from_status'        => $oldStatus,
            'to_status'          => $newStatus,
            'changed_by_user_id' => Auth::id(),
            'note'               => $request->input('note'),
            'changed_at'         => now(),
        ]);

        return back()->with('success', "Status pesanan berhasil diubah ke {$newStatus}.");
    }

    /**
     * PUT /admin/orders/{order}/tracking — Update/add resi without changing status.
     * Useful when admin forgot to add resi, or resi needs correction.
     */
    public function updateTracking(Request $request, Order $order): RedirectResponse
    {
        abort_unless(in_array($order->order_status, ['shipped', 'delivered']), 403, 'Resi hanya bisa diupdate pada pesanan yang sudah dikirim.');

        $validated = $request->validate([
            'tracking_number'   => 'required|string|max:100',
            'shipping_provider' => 'nullable|string|max:100',
        ]);

        $order->update([
            'tracking_number'   => $validated['tracking_number'],
            'shipping_provider' => $validated['shipping_provider'] ?? $order->shipping_provider,
        ]);

        return back()->with('success', 'Nomor resi berhasil diperbarui.');
    }

    /**
     * PUT /admin/orders/{order}/simulate-payment  [LOCAL / DEV ONLY]
     *
     * Simulasi webhook settlement dari Midtrans untuk testing di localhost.
     * Hanya aktif ketika APP_ENV=local.
     */
    public function simulatePayment(Order $order, NotificationService $notif): RedirectResponse
    {
        abort_unless(app()->isLocal(), 403, 'Hanya tersedia di environment local.');

        if ($order->payment_status === 'paid') {
            return back()->withErrors(['simulate' => 'Pembayaran sudah berstatus paid.']);
        }

        // Simulasi persis seperti yang dilakukan WebhookController saat terima settlement
        $order->update([
            'payment_status' => 'paid',
            'paid_at'        => now(),
        ]);

        // Observer akan otomatis kirim notif payment.confirmed
        // AdvanceOrderStatus command akan ubah pending→processing dalam 1 menit

        return back()->with('success', '✅ Simulasi pembayaran berhasil! Observer akan mengirim notifikasi Telegram. Order akan otomatis jadi "processing" dalam ~1 menit (jalankan php artisan orders:advance-status).');
    }

    // -------------------------------------------------------------------------
    // Manual Telegram notification
    // -------------------------------------------------------------------------

    public function sendNotification(Request $request, Order $order, NotificationService $notif): RedirectResponse
    {
        $validated = $request->validate([
            'event' => 'required|in:payment.confirmed,order.processing,order.shipped,order.delivered,order.cancelled',
        ]);

        $chatId = $order->customer?->telegram_chat_id;

        if (! $chatId) {
            return back()->withErrors(['notify' => 'Pelanggan belum mengisi Telegram Chat ID.']);
        }

        $notif->notifyOrderStatus($order, $validated['event']);

        return back()->with('success', 'Notifikasi Telegram berhasil dikirim ke ' . $chatId);
    }

    // -------------------------------------------------------------------------
    // Affiliates
    // -------------------------------------------------------------------------

    public function affiliates(Request $request): View
    {
        $query = Affiliate::with('user')
            ->withCount('referralClicks')
            ->withCount('conversions')
            ->orderByDesc('created_at');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $affiliates   = $query->paginate(20);
        $pendingCount = Affiliate::where('status', 'pending')->count();

        return view('admin.affiliates', compact('affiliates', 'pendingCount'));
    }

    public function approveAffiliate(Affiliate $affiliate): RedirectResponse
    {
        $affiliate->update([
            'status'      => 'approved',
            'approved_at' => now(),
        ]);

        // Promote user role to 'affiliate'
        $affiliate->user?->update(['role' => 'affiliate']);

        // Notify affiliate via Telegram if connected
        if ($affiliate->user?->telegram_chat_id) {
            $name = $affiliate->user->name;
            $code = $affiliate->referral_code;
            $msg  = "*TDR-HPZ Affiliate* 🎉\n\n"
                  . "Selamat *{$name}*!\n\n"
                  . "Pendaftaran affiliate Anda telah *disetujui*.\n"
                  . "Kode referral Anda: `{$code}`\n\n"
                  . "Mulai bagikan link Anda dan dapatkan komisi 10% dari setiap pembelian!";
            app(\App\Services\TelegramService::class)->sendMessage($affiliate->user->telegram_chat_id, $msg);
        }

        return back()->with('success', 'Affiliate ' . ($affiliate->user?->name ?? '') . ' berhasil diapprove.');
    }

    public function rejectAffiliate(Affiliate $affiliate): RedirectResponse
    {
        $oldStatus = $affiliate->status;
        $affiliate->update([
            'status'      => 'rejected',
            'approved_at' => null,
        ]);

        // Demote user role back to 'customer'
        $affiliate->user?->update(['role' => 'customer']);

        // Notify affiliate via Telegram if was approved (notify suspension)
        if ($oldStatus === 'approved' && $affiliate->user?->telegram_chat_id) {
            $name = $affiliate->user->name;
            $msg  = "*TDR-HPZ Affiliate*\n\n"
                  . "Halo *{$name}*, akun affiliate Anda telah *dinonaktifkan* oleh admin.\n"
                  . "Hubungi admin untuk informasi lebih lanjut.";
            app(\App\Services\TelegramService::class)->sendMessage($affiliate->user->telegram_chat_id, $msg);
        }

        return back()->with('success', 'Affiliate ' . ($affiliate->user?->name ?? '') . ' telah ditolak/dinonaktifkan.');
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
