<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AffiliateController extends Controller
{
    /**
     * Show affiliate registration form.
     *
     * GET /affiliate/register
     *
     * TODO [PHASE 2 - Ghufron]
     */
    public function showRegisterForm()
    {
        // TODO [PHASE 2 - Ghufron]: return view('affiliate.register');
    }

    /**
     * Process affiliate registration.
     *
     * POST /affiliate/register
     *
     * TODO [PHASE 2 - Ghufron]: Implementasi sesuai TASK G1
     *
     * Requirements:
     * - Input: name, telegram_id, payout_method
     * - Generate unique referral_code
     * - Return referral link: https://domain.com/?ref=CODE
     */
    public function register(Request $request)
    {
        // TODO [PHASE 2 - Ghufron]: Implementasi logic di sini
    }

    /**
     * Show affiliate dashboard.
     *
     * GET /affiliate/dashboard
     *
     * TODO [PHASE 2 - Ghufron]: Implementasi sesuai TASK G3
     *
     * Display:
     * - Total clicks
     * - Total conversions
     * - Conversion rate
     * - Total commission
     * - Recent orders
     * - Chart.js graph
     */
    public function dashboard(Request $request)
    {
        // TODO [PHASE 2 - Ghufron]: Implementasi logic di sini
    }

    /**
     * Capture referral click from ?ref=CODE URL parameter.
     *
     * GET /  (middleware / via web route)
     *
     * TODO [PHASE 2 - Ghufron]: Implementasi sesuai TASK G2
     *
     * Flow:
     * - Store cookie: affiliate_ref (expire 30 hari)
     * - Insert into affiliate_referral_clicks
     */
    public function captureReferral(Request $request)
    {
        // TODO [PHASE 2 - Ghufron]: Implementasi logic di sini
    }
}
