<?php

namespace App\Http\Controllers\asset;

use App\Http\Controllers\Controller;
use App\Models\asset\Asset;
use App\Models\master\CategoryAssets;
use App\Models\master\Brand;
use App\Models\master\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;

class PhysicalAssetController extends Controller
{
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
        $asset = Asset::whereNull('deleted_by')->whereNull('deleted_at')->where('type', 1)->get();

        /**
         * Datatable Configuration
         */
        $dataTable = DataTables::of($asset)
            ->addIndexColumn()
            ->addColumn('category', function ($data) {

                return $data->category ? $data->category->name : '-';
            })
            ->addColumn('brand', function ($data) {

                return $data->brand ? $data->brand->name : '-';
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
                } else if (User::find(Auth::user()->id)->hasRole('staff')) {
                    $btn_action .= '<a href="' . route('submission.create', ['type' => 'checkout', 'asset' => $data->id]) . '" class="btn btn-sm btn-warning ml-2" title="Check Out">Check Out</a>';
                    $btn_action .= '<a href="' . route('submission.create', ['type' => 'assign', 'asset' => $data->id]) . '" class="btn btn-sm btn-danger mt-1 ml-2" title="Assign To Me">Assign To Me</a>';
                }
                $btn_action .= '</div>';
                return $btn_action;
            })
            ->only(['name', 'brand', 'category', 'assignTo', 'action'])
            ->rawColumns(['action'])
            ->make(true);

        return $dataTable;
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = CategoryAssets::whereNull('deleted_at')->where('type', 1)->get();
        $brands = Brand::whereNull('deleted_at')->get();
        $users = User::whereNull('deleted_at')->role('staff')->get();
        return view('asset.physical.create', compact('categories', 'brands', 'users'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'category_asset_id' => 'nullable|integer|exists:category_assets,id',
                'barcode_code' => 'required|string',
                'name' => 'required|string',
                'status' => 'required|integer',
                'value' => 'nullable|integer|min:0',
                'description' => 'nullable|string',
                'attachment.*' => 'nullable|file|mimes:jpg,jpeg,png|max:10240',
                'brand_id' => 'required|integer|exists:brands,id',
                'purchase_date' => 'nullable|date',
                'warranty_end_date' => 'nullable|date|after_or_equal:purchase_date',
                'warranty_duration' => 'nullable|integer|min:0',
            ]);

            $barcode_check = Asset::whereNull('deleted_at')
                ->where('barcode_code', $request->barcode_code)
                ->first();
            if (is_null($barcode_check)) {
                DB::beginTransaction();

                $asset = Asset::lockForUpdate()->create([
                    'name' => $request->name,
                    'category_asset_id' => $request->category_asset_id,
                    'type' => 1,
                    'barcode_code' => $request->barcode_code,
                    'status' => $request->status,
                    'value' => $request->value,
                    'description' => $request->description,
                    'brand_id' => $request->brand_id,
                    'purchase_date' => $request->purchase_date,
                    'warranty_end_date' => $request->warranty_end_date,
                    'warranty_duration' => $request->warranty_duration,
                    'created_by' => Auth::user()->id,
                    'updated_by' => Auth::user()->id,
                ]);



                if ($asset) {
                    $path = 'public/asset/physical';
                    $path_store = 'storage/asset/physical';

                    if (!Storage::exists($path)) {
                        Storage::makeDirectory($path);
                    }

                    $attachments = [];

                    if ($request->hasFile('attachment')) {
                        foreach ($request->file('attachment') as $file) {
                            // Menggunakan nama file asli dengan uniqid untuk menghindari duplikasi nama
                            $file_name = $asset->id . '_' . uniqid() . '_' . $file->getClientOriginalName();
                            $file->storePubliclyAs($path, $file_name);
                            $attachments[] = $path_store . '/' . $file_name;
                        }
                    }

                    $asset->update([
                        'attachment' => json_encode($attachments),
                    ]);

                    DB::commit();
                    return redirect()
                        ->route('asset.physical.index')
                        ->with(['success' => 'Successfully Add Physical Asset']);
                } else {
                    DB::rollBack();
                    return redirect()
                        ->back()
                        ->with(['failed' => 'Failed Add Physical Asset'])
                        ->withInput();
                }
            } else {
                return redirect()
                    ->back()
                    ->with(['failed' => 'Barcode Already Exist'])
                    ->withInput();
            }
        } catch (Exception $e) {
            dd($e->getLine(), $e->getMessage(), $e->getFile());
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

    public function edit(string $id)
    {
        try {

            $physical = Asset::find($id);


            if (!is_null($physical)) {
                $categories = CategoryAssets::whereNull('deleted_at')->where('type', 1)->get();
                $brands = Brand::whereNull('deleted_at')->get();
                $users = User::whereNull('deleted_at')->role('staff')->get();

                return view('asset.physical.edit', compact('physical', 'categories', 'brands', 'users'));
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

    public function update(Request $request, string $id)
    {

        try {
            /**
             * Validation Request Body Variables
             */
            $request->validate([
                'category_asset_id' => 'nullable|integer|exists:category_assets,id',
                'barcode_code' => 'required|string',
                'name' => 'required|string',
                'status' => 'required|integer',
                'value' => 'nullable|integer|min:0',
                'description' => 'nullable|string',
                'attachment.*' => 'nullable|file|mimes:jpg,jpeg,png|max:10240',
                'assign_to' => 'nullable',
                'assign_at' => 'nullable',
                'brand_id' => 'required|integer|exists:brands,id',
                'purchase_date' => 'nullable|date',
                'warranty_end_date' => 'nullable|date|after_or_equal:purchase_date',
                'warranty_duration' => 'nullable|integer|min:0',

            ]);

            $barcode_check = Asset::whereNull('deleted_at')
                ->where('barcode_code', $request->barcode_code)
                ->where('id', '!=', $id)
                ->first();
            if (is_null($barcode_check)) {
                $asset = Asset::find($id);

                if (!is_null($asset)) {
                    /**
                     * Begin Transaction
                     */
                    DB::beginTransaction();

                    /**
                     * Update asset Record
                     */
                    $asset_update = Asset::where('id', $id)->update([
                        'name' => $request->name,
                        'category_asset_id' => $request->category_asset_id,
                        'type' => 1,
                        'barcode_code' => $request->barcode_code,
                        'status' => $request->status,
                        'value' => $request->value,
                        'description' => $request->description,
                        'assign_to' => $request->assign_to,
                        'assign_at' => $request->assign_at,
                        'brand_id' => $request->brand_id,
                        'purchase_date' => $request->purchase_date,
                        'warranty_end_date' => $request->warranty_end_date,
                        'warranty_duration' => $request->warranty_duration,
                        'created_by' => Auth::user()->id,
                        'updated_by' => Auth::user()->id,
                    ]);

                    /**
                     * Validation Update asset Record
                     */
                    if ($asset_update) {
                        DB::commit();
                        return redirect()
                            ->route('asset.physical.index')
                            ->with(['success' => 'Successfully Update asset']);
                    } else {
                        /**
                         * Failed Store Record
                         */
                        DB::rollBack();
                        return redirect()
                            ->back()
                            ->with(['failed' => 'Failed Update asset'])
                            ->withInput();
                    }
                } else {
                    return redirect()
                        ->back()
                        ->with(['failed' => 'Invalid Request!']);
                }
            } else {
                return redirect()
                    ->back()
                    ->with(['failed' => 'Barcode Already Exist'])
                    ->withInput();
            }
        } catch (Exception $e) {
            return redirect()
                ->back()
                ->with(['failed' => $e->getMessage()])
                ->withInput();
        }
    }

    public function destroy(string $id)
    {
        try {
            /**
             * Begin Transaction
             */
            DB::beginTransaction();

            /**
             * Update Asset Record
             */
            $asset_destroy = Asset::where('id', $id)->update([
                'deleted_at' => date('Y-m-d H:i:s'),
            ]);

            /**
             * Validation Update Asset Record
             */
            if ($asset_destroy) {
                DB::commit();
                session()->flash('success', 'Asset Successfully Deleted');
            } else {
                /**
                 * Failed Store Record
                 */
                DB::rollBack();
                session()->flash('failed', 'Failed Delete Asset');
            }
        } catch (Exception $e) {
            session()->flash('failed', $e->getMessage());
        }
    }
}
