<?php

namespace App\Http\Controllers\history;

use App\Http\Controllers\Controller;
use App\Models\HistoryMaintence;
use Exception;
use Illuminate\Http\Request;

class HistoryMaintenceController extends Controller
{
    public function show(Request $request, String $id)
    {

        try {
            $historyMaintence = HistoryMaintence::where('id', $id)->first();
            if (!is_null($historyMaintence)) {


                return view('asset.history.maintence.detail', compact('historyMaintence'));
            } else {
                return redirect()
                    ->back()
                    ->with(['failed' => 'Invalid Request!']);
            }
        } catch (Exception $e) {
            return redirect()->back()->with(['failed', $e->getMessage()]);
        }
    }
}
