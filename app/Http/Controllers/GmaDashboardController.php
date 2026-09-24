<?php

namespace App\Http\Controllers;

use App\Models\EnrollmentSetting;
use App\Models\PhysicalFitness;
use App\Models\User;

class GmaDashboardController extends Controller
{
    public function index()
    {
        $setting = EnrollmentSetting::getActive();

        $totalCadets = User::where('role', 'cadet')->where('status', 'approved')->count();

        $pftTested = 0;
        $pftPassed = 0;
        $pftFailed = 0;

        if ($setting) {
            $pftScores = PhysicalFitness::where('enrollment_setting_id', $setting->id)->get();
            $pftTested = $pftScores->count();
            $pftPassed = $pftScores->where('overall', '>=', 66.66)->count();
            $pftFailed = $pftScores->where('overall', '<', 66.66)->count();
        }

        return view('gma.dashboard', compact(
            'setting', 'totalCadets', 'pftTested', 'pftPassed', 'pftFailed'
        ));
    }
}