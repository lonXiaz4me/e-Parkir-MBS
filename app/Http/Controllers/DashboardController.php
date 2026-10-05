<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        // ── FIX #6: Eager-load all relationships the dashboard needs ─────────
        //
        //    PREVIOUS BUG: The controller did nothing but return the view.
        //    The blade then called auth()->user()->applications and
        //    auth()->user()->payments in multiple places, each triggering
        //    a fresh SQL query because the relationships were never loaded.
        //
        //    With just 10 applications this could fire 20+ queries per page
        //    load. As the user's application history grows, so does the query
        //    count — a classic N+1 problem.
        //
        //    FIX: Load the user once with all needed relationships in a single
        //    query using with(), then derive all counts and collections from
        //    the already-loaded data in PHP — zero extra DB hits.
        //
        $user = Auth::user()->load(['applications', 'payments']);

        // ── Derive stats from the already-loaded collection (no extra queries)
        $allApps        = $user->applications;
        $totalCount     = $allApps->count();
        $pendingCount   = $allApps->where('app_status', 'pending')->count();
        $approvedCount  = $allApps->where('app_status', 'approved')->count();
        $completedCount = $allApps->where('app_status', 'completed')->count();
        $rejectedCount  = $allApps->where('app_status', 'not_approved')->count();

        // ── Derive payment stats from the already-loaded collection
        $allPayments  = $user->payments;
        $totalBil     = $allPayments->count();
        $paidCount    = $allPayments->where('payment_status', 'paid')->count();
        $failedCount  = $allPayments->where('payment_status', 'failed')->count();
        $totalPaid    = $allPayments->where('payment_status', 'paid')->sum('total_amt');
        $totalUnpaid  = $allPayments->where('payment_status', 'failed')->sum('total_amt');

        return view('auth.dashboard', compact(
            'user',
            'allApps',
            'totalCount',
            'pendingCount',
            'approvedCount',
            'completedCount',
            'rejectedCount',
            'allPayments',
            'totalBil',
            'paidCount',
            'failedCount',
            'totalPaid',
            'totalUnpaid',
        ));
    }
}