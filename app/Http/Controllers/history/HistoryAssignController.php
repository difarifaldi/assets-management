<?php

namespace App\Http\Controllers\history;

use App\Http\Controllers\Controller;
use App\Models\HistoryAssign;
use App\Http\Requests\StoreHistoryAssignRequest;
use App\Http\Requests\UpdateHistoryAssignRequest;
use Exception;
use Illuminate\Http\Request;

class HistoryAssignController extends Controller
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
    public function store(StoreHistoryAssignRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, String $id)
    {

        try {
            $historyAssign = HistoryAssign::where('id', $id)->first();
            if (!is_null($historyAssign)) {


                return view('asset.history.assign.detail', compact('historyAssign'));
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
    public function edit(Request $request, String $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateHistoryAssignRequest $request, HistoryAssign $historyAssign)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(HistoryAssign $historyAssign)
    {
        //
    }
}
