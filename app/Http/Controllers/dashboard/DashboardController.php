<?php

namespace App\Http\Controllers\dashboard;

use App\Http\Controllers\Controller;
use App\Models\asset\Asset;

class DashboardController extends Controller
{
    public function index()
    {
        $totalValue = Asset::whereNull('deleted_by')->whereNull('deleted_at')->sum('nilai');
        $allAsset = Asset::whereNull('deleted_by')->whereNull('deleted_at')->count();
        $physical = Asset::whereNull('deleted_by')->whereNull('deleted_at')->where('tipe', 1)->count();
        $license = Asset::whereNull('deleted_by')->whereNull('deleted_at')->where('tipe', 2)->count();
        return view('dashboard', compact('allAsset', 'physical', 'license', 'totalValue'));
    }
}
