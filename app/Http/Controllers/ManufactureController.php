<?php

namespace App\Http\Controllers;

use App\Models\Manufacture;
use App\Http\Requests\StoreManufactureRequest;
use App\Http\Requests\UpdateManufactureRequest;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ManufactureController extends Controller
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
        return view('manufacture.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            /**
             * Validation Request Body Variables
             */
            $request->validate([
                'name' => 'required|string',
                'address' => 'required',
            ]);

            DB::beginTransaction();

            /**
             * Create Manufacture Record
             */
            $manufacture = Manufacture::lockforUpdate()->create([
                'name' => $request->name,
                'address' => $request->address,
                'created_by' => Auth::user()->id,
                'updated_by' => Auth::user()->id,
            ]);

            /**
             * Validation Create Manufacture Record
             */
            if ($manufacture) {
                DB::commit();
                return redirect()
                    ->route('manufacture.create')
                    ->with(['success' => 'Successfully Add Manufacture']);
            } else {
                /**
                 * Failed Store Record
                 */
                DB::rollBack();
                return redirect()
                    ->back()
                    ->with(['failed' => 'Failed Add Manufacture'])
                    ->withInput();
            }
        } catch (Exception $e) {
            return redirect()
                ->back()
                ->with(['failed' => $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Manufacture $manufacture)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Manufacture $manufacture)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateManufactureRequest $request, Manufacture $manufacture)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Manufacture $manufacture)
    {
        //
    }
}
