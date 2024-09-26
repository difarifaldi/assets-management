<?php

namespace App\Http\Controllers\history;

use App\Http\Controllers\Controller;
use App\Models\HistoryCheckInOut;
use App\Http\Requests\StoreHistoryCheckInOutRequest;
use App\Http\Requests\UpdateHistoryCheckInOutRequest;
use Exception;
use Illuminate\Http\Request;

class HistoryCheckInOutController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreHistoryCheckInOutRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, String $id)
    {

        try {
            $historyCheckInOut = HistoryCheckInOut::where('id', $id)->first();
            if (!is_null($historyCheckInOut)) {


                return view('asset.history.checkout.detail', compact('historyCheckInOut'));
            } else {
                return redirect()
                    ->back()
                    ->with(['failed' => 'Invalid Request!']);
            }
        } catch (Exception $e) {
            return redirect()->back()->with(['failed', $e->getMessage()]);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(HistoryCheckInOut $historyCheckInOut)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateHistoryCheckInOutRequest $request, HistoryCheckInOut $historyCheckInOut)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(HistoryCheckInOut $historyCheckInOut)
    {
        //
    }
}
