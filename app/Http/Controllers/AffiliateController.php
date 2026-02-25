<?php

namespace App\Http\Controllers;

use App\Models\AffiliateProfile;
use App\Models\AffiliateWithdrawal;
use App\Services\AffiliateService;
use App\Services\NotificationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Str;
use Illuminate\View\View;

class AffiliateController extends Controller
{
    public function __construct(protected AffiliateService $affiliateService) {}


    public function captureReferral(Request $request): RedirectResponse
    {
        $code    = $request->query('ref');
        $profile = AffiliateProfile::where('referral_code', $code)->where('status', 'active')->first();

        if ($profile) {
            Cookie::queue('affiliate_ref', $code, 60 * 24 * 30); // 30 days
        }

        return redirect()->away(url('/'));
    }

    // GET /affiliate/register
    public function showRegisterForm(): View|RedirectResponse
    {
        if (! auth()->check()) {
            return redirect()->route('login')->with('info', 'Login terlebih dahulu untuk mendaftar sebagai affiliate.');
        }

        $user = auth()->user();

        if ($user->affiliateProfile) {
            return redirect()->route('affiliate.dashboard');
        }

        return view('affiliate.register');
    }

    // POST /affiliate/register
    public function register(Request $request): RedirectResponse
    {
        $user = $request->user();

        if ($user->affiliateProfile) {
            return redirect()->route('affiliate.dashboard');
        }

        $data = $request->validate([
            'bank_name'           => 'required|string|max:100',
            'bank_account_number' => 'required|string|max:50',
            'bank_account_holder' => 'required|string|max:255',
        ]);

        // Generate unique referral code
        do {
            $code = strtoupper(Str::random(8));
        } while (AffiliateProfile::where('referral_code', $code)->exists());

        AffiliateProfile::create([
            'user_id'             => $user->id,
            'referral_code'       => $code,
            'commission_rate'     => 10,
            'balance'             => 0,
            'total_earned'        => 0,
            'status'              => 'pending',
            'bank_name'           => $data['bank_name'],
            'bank_account_number' => $data['bank_account_number'],
            'bank_account_holder' => $data['bank_account_holder'],
        ]);

        // Update user role
        $user->update(['role' => 'affiliate']);

        return redirect()->route('affiliate.dashboard')
            ->with('success', 'Pendaftaran affiliate berhasil! Tunggu persetujuan admin.');
    }

    // GET /affiliate/dashboard
    public function dashboard(Request $request): View|RedirectResponse
    {
        $user      = $request->user();
        $affiliate = $user->affiliateProfile;

        if (! $affiliate) {
            return redirect()->route('affiliate.register.form');
        }

        $stats       = $this->affiliateService->getStats($affiliate);
        $chartData   = $this->affiliateService->getChartData($affiliate);
        $referralLink = url('/') . '?ref=' . $affiliate->referral_code;
        $products    = \App\Models\Product::active()->orderBy('name')->get();

        $recentOrders = \App\Models\Order::where('affiliate_id', $user->id)
            ->latest()
            ->take(5)
            ->get();

        $pendingWithdrawal = AffiliateWithdrawal::where('affiliate_id', $user->id)
            ->where('status', 'pending')
            ->latest()
            ->first();

        return view('affiliate.dashboard', compact('affiliate', 'stats', 'chartData', 'referralLink', 'recentOrders', 'pendingWithdrawal', 'products'));
    }

    // POST /affiliate/withdraw
    public function requestWithdrawal(Request $request): RedirectResponse
    {
        $user      = $request->user();
        $affiliate = $user->affiliateProfile;

        if (! $affiliate || $affiliate->status !== 'active') {
            return back()->with('withdraw_error', 'Akun affiliate tidak aktif.');
        }

        if (! $affiliate->bank_account_number) {
            return back()->with('withdraw_error', 'Lengkapi data rekening terlebih dahulu sebelum mencairkan.');
        }

        $hasPending = AffiliateWithdrawal::where('affiliate_id', $user->id)
            ->where('status', 'pending')
            ->exists();

        if ($hasPending) {
            return back()->with('withdraw_error', 'Anda sudah memiliki permintaan pencairan yang sedang diproses.');
        }

        $minAmount = 50000;
        $balance   = (float) $affiliate->balance;

        if ($balance < $minAmount) {
            return back()->with('withdraw_error', 'Saldo minimum pencairan adalah Rp ' . number_format($minAmount, 0, ',', '.') . '. Saldo Anda saat ini Rp ' . number_format($balance, 0, ',', '.') . '.');
        }

        try {
            $withdrawal = $this->affiliateService->processWithdrawal($affiliate, $balance, [
                'bank_name'           => $affiliate->bank_name,
                'bank_account_number' => $affiliate->bank_account_number,
                'bank_account_holder' => $affiliate->bank_account_holder,
            ]);

            app(NotificationService::class)->notifyAffiliateWithdrawal($affiliate, $withdrawal);

            return back()->with('withdraw_success', 'Permintaan pencairan *Rp ' . number_format($balance, 0, ',', '.') . '* berhasil diajukan. Admin akan memproses dalam 1×24 jam.');
        } catch (\InvalidArgumentException $e) {
            return back()->with('withdraw_error', $e->getMessage());
        }
    }

    /** PUT /affiliate/payout */
    public function updatePayout(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'bank_name'           => 'required|string|max:100',
            'bank_account_number' => 'required|string|max:50',
            'bank_account_holder' => 'required|string|max:255',
        ]);

        $request->user()->affiliateProfile->update($data);

        return back()->with('success', 'Data rekening berhasil diperbarui.');
    }
}
