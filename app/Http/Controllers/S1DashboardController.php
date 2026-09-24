<?php

namespace App\Http\Controllers;

use App\Models\User;

class S1DashboardController extends Controller
{
    public function index()
    {
        $pendingCount  = User::where('role', 'cadet')->where('status', 'pending')->count();
        $approvedCount = User::where('role', 'cadet')->where('status', 'approved')->count();
        $rejectedCount = User::where('role', 'cadet')->where('status', 'rejected')->count();

        $recentPending = User::where('role', 'cadet')
            ->where('status', 'pending')
            ->with('cadet')
            ->latest()
            ->take(5)
            ->get();

        return view('s1.dashboard', compact(
            'pendingCount', 'approvedCount', 'rejectedCount', 'recentPending'
        ));
    }
}