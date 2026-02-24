<?php

namespace App\Http\Controllers;

use App\Models\Affiliate;
use App\Models\AffiliateConversion;
use App\Models\AffiliateReferralClick;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;

class AffiliateController extends Controller
{
    /**
     * GET /affiliate/register
     */
    public function showRegisterForm(): View
    {
        return view('affiliate.register');
    }

    /**
     * POST /affiliate/register
     *
     * - Buat User (role affiliate) jika email belum ada
     * - Generate referral_code unik
     * - Buat Affiliate record
     * - Redirect ke dashboard dengan referral link
     */
    public function register(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name'          => 'required|string|max:255',
            'email'         => 'required|email|max:255',
            'telegram_id'   => 'nullable|string|max:100',
            'payout_method' => 'nullable|in:bank,ewallet,manual',
        ]);

        $user = DB::transaction(function () use ($validated) {
            // Buat atau ambil user
            $user = User::firstOrCreate(
                ['email' => $validated['email']],
                [
                    'name'               => $validated['name'],
                    'password_hash'      => bcrypt(Str::random(16)),
                    'role'               => 'affiliate',
                    'telegram_chat_id'   => $validated['telegram_id'] ?? null,
                    'is_active'          => true,
                ]
            );

            // Pastikan belum punya affiliate account
            if (Affiliate::where('user_id', $user->id)->exists()) {
                return $user; // sudah ada, skip
            }

            // Generate unique referral_code (6 karakter uppercase)
            do {
                $code = strtoupper(Str::random(6));
            } while (Affiliate::where('referral_code', $code)->exists());

            Affiliate::create([
                'user_id'         => $user->id,
                'referral_code'   => $code,
                'status'          => 'active',
                'commission_rate' => 10.00,
                'payout_method'   => $validated['payout_method'] ?? 'manual',
            ]);

            return $user;
        });

        $affiliate = Affiliate::where('user_id', $user->id)->first();

        return redirect()->route('affiliate.dashboard')
            ->with('success', 'Registrasi berhasil!')
            ->with('referral_link', url('/?ref=' . $affiliate->referral_code))
            ->with('affiliate_code', $affiliate->referral_code)
            ->with('user_email', $user->email);
    }

    /**
     * GET /affiliate/dashboard
     *
     * Tampilkan statistik affiliate:
     * - Total clicks, conversions, conversion rate, commission
     * - Recent orders
     */
    public function dashboard(Request $request): View|RedirectResponse
    {
        $email = session('user_email') ?? $request->query('email');

        if (! $email) {
            return redirect()->route('affiliate.register')
                ->with('info', 'Masukkan email untuk melihat dashboard.');
        }

        $user      = User::where('email', $email)->first();
        $affiliate = $user ? Affiliate::where('user_id', $user->id)->with('user')->first() : null;

        if (! $affiliate) {
            return redirect()->route('affiliate.register')
                ->with('error', 'Affiliate tidak ditemukan. Silakan daftar terlebih dahulu.');
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

        // Data chart (last 7 days)
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
     * Set cookie 30 hari + insert ke affiliate_referral_clicks.
     * Dipanggil dari homepage route jika ada ?ref= param.
     */
    public function captureReferral(Request $request): RedirectResponse
    {
        $code      = $request->query('ref');
        $affiliate = Affiliate::where('referral_code', $code)->where('status', 'active')->first();

        if (! $affiliate) {
            return redirect('/');
        }

        // Insert click record
        AffiliateReferralClick::create([
            'affiliate_id'            => $affiliate->id,
            'referral_code_snapshot'  => $code,
            'anonymized_ip'           => $request->ip(),
            'user_agent'              => $request->userAgent(),
            'landing_url'             => $request->fullUrl(),
            'is_attributed'           => false,
            'expires_at'              => now()->addDays(30),
        ]);

        // Update total_clicks counter
        $affiliate->increment('total_clicks');

        // Simpan cookie & redirect ke checkout
        return redirect('/checkout')
            ->cookie('affiliate_ref', $code, 60 * 24 * 30); // 30 hari
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
