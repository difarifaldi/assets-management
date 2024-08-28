<?php

namespace App\Http\Controllers\asset;

use App\Http\Controllers\Controller;
use App\Models\asset\Asset;
use App\Models\master\CategoryAssets;
use App\Models\master\Merk;
use App\Models\master\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;

class AssetController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $datatable_route = route('asset.physical.dataTable');


        $can_create = User::find(Auth::user()->id)->hasRole('admin');

        return view('asset.physical.index', compact('datatable_route', 'can_create'));
    }

    public function dataTable()
    {
        /**
         * Get All Asset
         */
        $asset = Asset::whereNull('deleted_by')->whereNull('deleted_at')->get();

        /**
         * Datatable Configuration
         */
        $dataTable = DataTables::of($asset)
            ->addIndexColumn()
            ->addColumn('category', function ($data) {

                return $data->category ? $data->category->name : '-';
            })
            ->addColumn('merk', function ($data) {

                return $data->merk ? $data->merk->name : '-';
            })

            ->addColumn('assignTo', function ($data) {

                return $data->assignTo ? $data->assignTo->name : '-';
            })
            ->addColumn('action', function ($data) {
                $btn_action = '<div align="center">';

                /**
                 * Validation Role Has Access Edit and Delete
                 */

                $btn_action = '<div align="center">';
                $btn_action .= '<a href="' . route('asset.physical.show', ['id' => $data->id]) . '" class="btn btn-sm btn-primary" title="Detail">Detail</a>';

                if (User::find(Auth::user()->id)->hasRole('admin')) {
                    $btn_action .= '<a href="' . route('asset.physical.edit', ['id' => $data->id]) . '" class="btn btn-sm btn-warning ml-2" title="Edit">Edit</a>';
                    $btn_action .= '<button class="btn btn-sm btn-danger ml-2" onclick="destroyRecord(' . $data->id . ')" title="Delete">Delete</button>';
                }
                $btn_action .= '</div>';
                return $btn_action;
            })
            ->only(['name', 'merk', 'category', 'assignTo', 'action'])
            ->rawColumns(['action'])
            ->make(true);

        return $dataTable;
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = CategoryAssets::whereNull('deleted_at')->get();
        $merks = Merk::whereNull('deleted_at')->get();
        $users = User::whereNull('deleted_at')->role('staff')->get();
        return view('asset.physical.create', compact('categories', 'merks', 'users'));
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
                'assign_to' => 'nullable',
                'assign_at' => 'nullable',
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
                'assign_to' => $request->assign_to,
                'assign_at' => $request->assign_at,
                'merk_id' => $request->merk_id,
                'purchase_date' => $request->purchase_date,
                'warranty_end_date' => $request->warranty_end_date,
                'warranty_duration' => $request->warranty_duration,
                'created_by' => Auth::user()->id,
                'updated_by' => Auth::user()->id,
            ]);

            if ($asset) {
                $path = 'public/asset/physical';
                $path_store = 'storage/asset/physical';

                // Check Exsisting Path
                if (!Storage::exists($path)) {
                    // Create new Path Directory
                    Storage::makeDirectory($path);
                }

                $exploded_name = explode(' ', strtolower($request->name));
                $file_name_config = implode('_', $exploded_name);
                $file = $request->file('attachment');
                $file_name = $asset->id . '_' . $file_name_config . '.' . $file->getClientOriginalExtension();

                // Uploading File
                $file->storePubliclyAs($path, $file_name);

                // Check Upload Success
                if (Storage::exists($path . '/' . $file_name)) {
                    // Update Record for Attachment
                    $asset_update = Asset::where('id', $asset->id)->update([
                        'attachment' => $path_store . '/' . $file_name,
                    ]);
                    if ($asset_update) {
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
                } else {
                    // Failed and Rollback
                    DB::rollBack();
                    return redirect()
                        ->back()
                        ->with(['failed' => 'Gagal Upload Lampiran Surat Masuk'])
                        ->withInput();
                }
            } else {
                // Failed and Rollback
                DB::rollBack();
                return redirect()
                    ->back()
                    ->with(['failed' => 'Gagal Tambah Surat Masuk'])
                    ->withInput();
            }
            /**
             * Validation Create Asset Record
             */
        } catch (Exception $e) {
            return redirect()
                ->back()
                ->with(['failed' => $e->getMessage()])
                ->withInput();
        }
    }

    public function show(string $id)
    {
        try {
            /**
             * Get User Record from id
             */
            $asset = Asset::find($id);

            /**
             * Validation Asset id
             */
            if (!is_null($asset)) {
                /**
                 * Asset Role Configuration
                 */

                return view('asset.physical.detail', compact('asset'));
            } else {
                return redirect()
                    ->back()
                    ->with(['failed' => 'Invalid Request!']);
            }
        } catch (Exception $e) {
            return redirect()
                ->back()
                ->with(['failed' => $e->getMessage()]);
        }
    }

    /**
     * Display the specified resource.
     */


    /**
     * Update the specified resource in storage.
     */
}
