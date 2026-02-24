<?php

namespace App\Http\Controllers;

use App\Models\Affiliate;
use App\Models\AffiliateConversion;
use App\Models\AffiliateReferralClick;
use App\Models\Order;
use App\Services\NotificationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;

class AffiliateController extends Controller
{
    public function __construct(private NotificationService $notif) {}
    /**
     * GET /affiliate/register
     * Harus login terlebih dahulu.
     */
    public function showRegisterForm(): View|RedirectResponse
    {
        if (! Auth::check()) {
            return redirect()->route('login')
                ->with('info', 'Silakan login dulu untuk mendaftar sebagai affiliate.');
        }

        $existing = Affiliate::where('user_id', Auth::id())->first();
        if ($existing) {
            return redirect()->route('affiliate.dashboard')
                ->with('info', 'Anda sudah terdaftar sebagai affiliate.');
        }

        return view('affiliate.register');
    }

    /**
     * POST /affiliate/register
     * Gunakan user yang sudah login.
     */
    public function register(Request $request): RedirectResponse
    {
        if (! Auth::check()) {
            return redirect()->route('login');
        }

        $validated = $request->validate([
            'payout_method'         => 'required|in:bank_bca,bank_bri,bank_bni,bank_mandiri,ovo,gopay,dana,shopeepay,manual',
            'payout_account_number' => 'required|string|max:100',
            'payout_account_name'   => 'required|string|max:255',
        ]);

        $user = Auth::user();

        DB::transaction(function () use ($user, $validated) {
            if (Affiliate::where('user_id', $user->id)->exists()) {
                return;
            }

            do {
                $code = strtoupper(Str::random(6));
            } while (Affiliate::where('referral_code', $code)->exists());

            Affiliate::create([
                'user_id'               => $user->id,
                'referral_code'         => $code,
                'status'                => 'pending',   // Admin must approve first
                'commission_rate'       => 10.00,
                'payout_method'         => $validated['payout_method'],
                'payout_account_number' => $validated['payout_account_number'],
                'payout_account_name'   => $validated['payout_account_name'],
            ]);

            // Role stays as customer until admin approves
        });

        return redirect()->route('affiliate.dashboard')
            ->with('success', 'Pendaftaran affiliate berhasil! Akun Anda sedang menunggu persetujuan admin. Anda akan mendapat notifikasi via Telegram setelah diapprove.');
    }

    /**
     * GET /affiliate/dashboard
     * Harus login & sudah mendaftar sebagai affiliate.
     */
    public function dashboard(): View|RedirectResponse
    {
        if (! Auth::check()) {
            return redirect()->route('login')
                ->with('info', 'Silakan login untuk melihat dashboard affiliate.');
        }

        $affiliate = Affiliate::where('user_id', Auth::id())->with('user')->first();

        if (! $affiliate) {
            return redirect()->route('affiliate.register.form')
                ->with('info', 'Daftarkan diri Anda sebagai affiliate terlebih dahulu.');
        }

        $totalClicks      = AffiliateReferralClick::where('affiliate_id', $affiliate->id)->count();
        $totalConversions = AffiliateConversion::where('affiliate_id', $affiliate->id)->count();
        $conversionRate   = $totalClicks > 0 ? round(($totalConversions / $totalClicks) * 100, 1) : 0;
        $totalCommission  = AffiliateConversion::where('affiliate_id', $affiliate->id)->sum('commission_amount');

        // Commission breakdown by status
        $commissionPending  = AffiliateConversion::where('affiliate_id', $affiliate->id)->where('status', 'pending')->sum('commission_amount');
        $commissionApproved = AffiliateConversion::where('affiliate_id', $affiliate->id)->where('status', 'approved')->sum('commission_amount');
        $commissionPaid     = AffiliateConversion::where('affiliate_id', $affiliate->id)->where('status', 'paid')->sum('commission_amount');

        // Conversions for table (recent 20)
        $conversions = AffiliateConversion::where('affiliate_id', $affiliate->id)
            ->with('order')
            ->latest()
            ->limit(20)
            ->get();

        $recentOrders = Order::with(['items'])
            ->where('affiliate_id', $affiliate->id)
            ->whereIn('order_status', ['processing', 'shipped', 'delivered'])
            ->latest()
            ->limit(10)
            ->get();

        $chartData = $this->buildChartData($affiliate->id);

        $stats = [
            'total_clicks'      => $totalClicks,
            'total_conversions' => $totalConversions,
            'conversion_rate'   => $conversionRate,
            'total_commission'  => $totalCommission,
        ];

        $referralLink = url('/?ref=' . $affiliate->referral_code);

        return view('affiliate.dashboard', compact(
            'affiliate', 'stats', 'recentOrders', 'chartData', 'referralLink',
            'conversions', 'commissionPending', 'commissionApproved', 'commissionPaid'
        ));
    }

    /**
     * POST /affiliate/request-payout
     * Request disbursement of all approved (confirmed) commissions.
     */
    public function requestPayout(): RedirectResponse
    {
        $affiliate = Affiliate::where('user_id', Auth::id())
            ->where('status', 'approved')
            ->firstOrFail();

        $approved = AffiliateConversion::where('affiliate_id', $affiliate->id)
            ->where('status', 'approved')
            ->get();

        if ($approved->isEmpty()) {
            return back()->with('error', 'Tidak ada komisi yang siap dicairkan saat ini.');
        }

        $total = $approved->sum('commission_amount');

        // Mark all approved conversions as paid
        AffiliateConversion::where('affiliate_id', $affiliate->id)
            ->where('status', 'approved')
            ->update(['status' => 'paid', 'paid_at' => now()]);

        // Notify affiliate + admin
        $this->notif->notifyAffiliatePayoutRequested($affiliate, (float) $total);

        return back()->with(
            'success',
            '✅ Permintaan pencairan sebesar Rp ' . number_format($total, 0, ',', '.') .
            ' berhasil diajukan! Dana akan ditransfer dalam 1–3 hari kerja.'
        );
    }

    /**
     * PUT /affiliate/payout
     * Simpan data rekening / e-wallet untuk pencairan komisi.
     */
    public function updatePayout(Request $request): RedirectResponse
    {
        if (! Auth::check()) {
            return redirect()->route('login');
        }

        $affiliate = Affiliate::where('user_id', Auth::id())->firstOrFail();

        $validated = $request->validate([
            'payout_method'         => 'required|in:bank_bca,bank_bri,bank_bni,bank_mandiri,ovo,gopay,dana,shopeepay,manual',
            'payout_account_name'   => 'required|string|max:255',
            'payout_account_number' => 'required|string|max:100',
        ]);

        $affiliate->update($validated);

        return redirect()->route('affiliate.dashboard')
            ->with('success', 'Data rekening berhasil disimpan.');
    }

    /**
     * Capture referral click dari ?ref=CODE parameter.
     */
    public function captureReferral(Request $request): RedirectResponse
    {
        $code      = $request->query('ref');
        $affiliate = Affiliate::where('referral_code', $code)->where('status', 'approved')->first();

        if (! $affiliate) {
            return redirect('/');
        }

        // BLOCK SELF-REFERRAL: affiliate cannot click their own link
        if (Auth::check() && Auth::id() === $affiliate->user_id) {
            return redirect('/')
                ->with('info', 'Anda tidak dapat menggunakan link referral milik sendiri.');
        }

        AffiliateReferralClick::create([
            'affiliate_id'           => $affiliate->id,
            'referral_code_snapshot' => $code,
            'anonymized_ip'          => $request->ip(),
            'user_agent'             => $request->userAgent(),
            'landing_url'            => $request->fullUrl(),
            'is_attributed'          => false,
            'expires_at'             => now()->addDays(30),
        ]);

        $affiliate->increment('total_clicks');

        return redirect('/')
            ->cookie('affiliate_ref', $code, 60 * 24 * 30);
    }

    // -------------------------------------------------------------------------
    // Private helpers
    // -------------------------------------------------------------------------

    private function buildChartData(int $affiliateId): array
    {
        $days   = 7;
        $labels = [];
        $clicks = [];
        $convs  = [];

        for ($i = $days - 1; $i >= 0; $i--) {
            $date     = now()->subDays($i)->format('Y-m-d');
            $labels[] = now()->subDays($i)->format('d/m');

            $clicks[] = AffiliateReferralClick::where('affiliate_id', $affiliateId)
                ->whereDate('created_at', $date)
                ->count();

            $convs[] = AffiliateConversion::where('affiliate_id', $affiliateId)
                ->whereDate('created_at', $date)
                ->count();
        }

        return compact('labels', 'clicks', 'convs');
    }
}
