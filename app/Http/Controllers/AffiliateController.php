<?php

namespace App\Http\Controllers;

use App\Models\Affiliate;
use App\Models\AffiliateConversion;
use App\Models\AffiliateReferralClick;
use App\Models\Order;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;

class AffiliateController extends Controller
{
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
            'payout_method' => 'nullable|in:bank,ewallet,manual',
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
                'user_id'         => $user->id,
                'referral_code'   => $code,
                'status'          => 'approved',
                'commission_rate' => 10.00,
                'payout_method'   => $validated['payout_method'] ?? 'manual',
            ]);

            $user->update(['role' => 'affiliate']);
        });

        return redirect()->route('affiliate.dashboard')
            ->with('success', 'Selamat! Anda kini terdaftar sebagai affiliate.');
    }

    /**
     * GET /affiliate/dashboard
     * Harus login & sudah jadi affiliate.
     */
    public function dashboard(): View|RedirectResponse
    {
        if (! Auth::check()) {
            return redirect()->route('login')
                ->with('info', 'Silakan login untuk melihat dashboard affiliate.');
        }

        $affiliate = Affiliate::where('user_id', Auth::id())->with('user')->first();

        if (! $affiliate) {
            return redirect()->route('affiliate.register')
                ->with('info', 'Daftarkan diri Anda sebagai affiliate terlebih dahulu.');
        }

        $totalClicks      = AffiliateReferralClick::where('affiliate_id', $affiliate->id)->count();
        $totalConversions = AffiliateConversion::where('affiliate_id', $affiliate->id)->count();
        $conversionRate   = $totalClicks > 0 ? round(($totalConversions / $totalClicks) * 100, 1) : 0;
        $totalCommission  = AffiliateConversion::where('affiliate_id', $affiliate->id)->sum('commission_amount');

        $recentOrders = Order::with(['items'])
            ->where('affiliate_id', $affiliate->id)
            ->whereIn('order_status', ['paid', 'shipped', 'delivered'])
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

        return view('affiliate.dashboard', compact('affiliate', 'stats', 'recentOrders', 'chartData', 'referralLink'));
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
