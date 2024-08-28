<?php

namespace App\Http\Controllers\asset;

use App\Http\Controllers\Controller;
use App\Models\asset\Asset;
use App\Models\master\CategoryAssets;
use App\Models\master\Merk;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class AssetController extends Controller
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
        $categories = CategoryAssets::whereNull('deleted_at')->get();
        $merks = Merk::whereNull('deleted_at')->get();
        return view('asset.physical.create', compact('categories', 'merks'));
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
                'category_asset_id' => 'nullable|integer|exists:category_assets,id',
                'type' => 'required',
                'barcode_code' => 'required|string|unique:assets,barcode_code',
                'name' => 'required|string',
                'status' => 'required|integer',
                'value' => 'nullable|integer|min:0',
                'exipired_at' => 'nullable|date',
                'description' => 'nullable|string',
                'attachment' => 'nullable',
                'merk_id' => 'required|integer|exists:merks,id',
                'purchase_date' => 'nullable|date',
                'warranty_end_date' => 'nullable|date|after_or_equal:purchase_date',
                'warranty_duration' => 'nullable|integer|min:0',
            ]);


            DB::beginTransaction();

            /**
             * Create Asset Record
             */
            $asset = Asset::lockforUpdate()->create([
                'name' => $request->name,
                'category_asset_id' => $request->category_asset_id,
                'type' => $request->type,
                'barcode_code' => $request->barcode_code,
                'status' => $request->status,
                'value' => $request->value,
                'exipired_at' => $request->exipired_at,
                'description' => $request->description,
                'attachment' => $request->attachment,
                'merk_id' => $request->merk_id,
                'purchase_date' => $request->purchase_date,
                'warranty_end_date' => $request->warranty_end_date,
                'warranty_duration' => $request->warranty_duration,
                'created_by' => Auth::user()->id,
                'updated_by' => Auth::user()->id,
            ]);

            /**
             * Validation Create Asset Record
             */
            if ($asset) {
                DB::commit();
                return redirect()
                    ->route('asset.physical.create')
                    ->with(['success' => 'Successfully Add Physical Asset']);
            } else {
                /**
                 * Failed Store Record
                 */
                DB::rollBack();
                return redirect()
                    ->back()
                    ->with(['failed' => 'Failed Add Physical Asset'])
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


    /**
     * Update the specified resource in storage.
     */
}
