<?php

namespace App\Http\Controllers\asset;

use App\Http\Controllers\Controller;
use App\Models\asset\Asset;
use App\Models\history\HistoryAssign;
use App\Models\history\HistoryMaintence;
use App\Models\master\CategoryAssets;
use App\Models\master\Brand;
use App\Models\master\Manufacture;
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

            ->addColumn('status', function ($data) {
                if (!is_null($data->assign_to) && !is_null($data->assign_at)) {
                    if (User::find(Auth::user()->id)->hasRole('admin')) {
                        return '<span class="badge badge-danger">Assign To ' . $data->assignTo->name . '</span>';
                    } else {
                        return '<span class="badge badge-danger">Assigned</span>';
                    }
                } elseif (!is_null($data->check_out_by) && !is_null($data->check_out_at)) {
                    if (User::find(Auth::user()->id)->hasRole('admin')) {
                        return ' <span class="badge badge-danger">Check Out By ' . $data->checkOut->name . '</span>';
                    } else {
                        return '<span class="badge badge-danger">Checked Out</span>';
                    }
                } elseif ($data->status == 3) {
                    return '<span class="badge badge-danger">Major Damage</span>';
                } elseif ($data->status == 4) {
                    return '<span class="badge badge-danger">On Maintence</span>';
                } else {
                    return '<span class="badge badge-success">Available</span>';
                }
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
                } elseif (User::find(Auth::user()->id)->hasRole('staff')) {
                    if (is_null($data->assign_to) && is_null($data->assign_at) && is_null($data->check_out_by) && is_null($data->check_out_at) && $data->status != 4) {
                        $btn_action .= '<a href="' . route('submission.create', ['type' => 'checkouts', 'asset' => $data->id]) . '" class="btn btn-sm btn-warning ml-2" title="Check Out">Check Out</a>';
                        $btn_action .= '<a href="' . route('submission.create', ['type' => 'assign', 'asset' => $data->id]) . '" class="btn btn-sm btn-danger ml-2" title="Assign To Me">Assign To Me</a>';
                    }
                }
                $btn_action .= '</div>';
                return $btn_action;
            })
            ->only(['name', 'brand', 'category', 'status', 'action'])
            ->rawColumns(['status', 'action'])
            ->make(true);

        return $dataTable;
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = CategoryAssets::whereNull('deleted_by')->whereNull('deleted_at')->where('type', 1)->get();
        $brands = Brand::whereNull('deleted_by')->whereNull('deleted_at')->get();
        $manufactures = Manufacture::whereNull('deleted_by')->whereNull('deleted_at')->get();
        $users = User::whereNull('deleted_at')->role('staff')->get();
        return view('asset.physical.create', compact('categories', 'brands', 'users', 'manufactures'));
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
                'manufacture_id' => 'required|integer|exists:manufactures,id',
                'purchase_date' => 'nullable|date',
                'warranty_end_date' => 'nullable|date|after_or_equal:purchase_date',
                'warranty_duration' => 'nullable|integer|min:0',
            ]);

            $barcode_check = Asset::whereNull('deleted_by')
                ->whereNull('deleted_at')
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
                    'manufacture_id' => $request->manufacture_id,
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
                            $file_name = $asset->id . '-' . uniqid() . '-' . strtotime(date('Y-m-d H:i:s')) . '.' . $file->getClientOriginalExtension();
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
            return redirect()
                ->back()
                ->with(['failed' => $e->getMessage()]);
        }
    }

    public function show(request $request, string $id)
    {
        try {
            /**
             * Get User Record from id
             */
            $asset = Asset::find($id);

            $users = User::whereNull('deleted_at')->role('staff')->get();

            /**
             * Validation Asset id
             */
            if (!is_null($asset)) {
                /**
                 * Asset Role Configuration
                 */

                if ($request->ajax()) {
                    $asset = Asset::with('category')->find($id);
                    return response()->json(['success' => true, 'data' => $asset], 200);
                }

                return view('asset.physical.detail', compact('asset', 'users'));
            } else {
                if ($request->ajax()) {
                    return response()->json(['success' => false, 'message' => 'Invalid Request!'], 400);
                }

                return redirect()
                    ->back()
                    ->with(['failed' => 'Invalid Request!']);
            }
        } catch (Exception $e) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 400);
            }

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
                $categories = CategoryAssets::whereNull('deleted_by')->whereNull('deleted_at')->where('type', 1)->get();
                $brands = Brand::whereNull('deleted_by')->whereNull('deleted_at')->get();
                $manufactures = Manufacture::whereNull('deleted_by')->whereNull('deleted_at')->get();
                $users = User::whereNull('deleted_at')->role('staff')->get();

                return view('asset.physical.edit', compact('physical', 'categories', 'brands', 'users', 'manufactures'));
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
                'manufacture_id' => 'required|integer|exists:manufactures,id',
                'purchase_date' => 'nullable|date',
                'warranty_end_date' => 'nullable|date|after_or_equal:purchase_date',
                'warranty_duration' => 'nullable|integer|min:0',
            ]);

            $barcode_check = Asset::whereNull('deleted_by')
                ->whereNull('deleted_at')
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
                        'manufacture_id' => $request->manufacture_id,
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

    /**
     * Update the specified resource in storage.
     */
    public function uploadImage(Request $request, string $id)
    {
        try {
            // Request Validation
            $request->validate([
                'attachment' => 'required',
            ]);

            DB::beginTransaction();

            $asset = Asset::find($id);

            // Image Path
            $path = 'public/asset/physical';
            $path_store = 'storage/asset/physical';

            if (!is_null($asset->attachment)) {
                $attachment_collection = json_decode($asset->attachment);

                foreach ($request->file('attachment') as $file) {
                    // File Upload Configuration
                    $file_name = $asset->id . '-' . uniqid() . '-' . strtotime(date('Y-m-d H:i:s')) . '.' . $file->getClientOriginalExtension();

                    // Uploading File
                    $file->storePubliclyAs($path, $file_name);

                    // Check Upload Success
                    if (Storage::exists($path . '/' . $file_name)) {
                        array_push($attachment_collection, $path_store . '/' . $file_name);
                    } else {
                        // Failed and Rollback
                        DB::rollBack();
                        return redirect()
                            ->back()
                            ->with(['failed' => 'Failed Upload Attachment'])
                            ->withInput();
                    }
                }
            } else {
                // Check Exsisting Path
                if (!Storage::exists($path)) {
                    // Create new Path Directory
                    Storage::makeDirectory($path);
                }

                $attachment_collection = [];

                foreach ($request->file('attachment') as $file) {
                    // File Upload Configuration
                    $file_name = $asset->id . '-' . uniqid() . '-' . strtotime(date('Y-m-d H:i:s')) . '.' . $file->getClientOriginalExtension();

                    // Uploading File
                    $file->storePubliclyAs($path, $file_name);

                    // Check Upload Success
                    if (Storage::exists($path . '/' . $file_name)) {
                        array_push($attachment_collection, $path_store . '/' . $file_name);
                    } else {
                        // Failed and Rollback
                        DB::rollBack();
                        return redirect()
                            ->back()
                            ->with(['failed' => 'Failed Upload Attachment'])
                            ->withInput();
                    }
                }
            }

            // Update Record for Attachment
            $asset_attachment = $asset->update([
                'attachment' => json_encode($attachment_collection),
            ]);

            // Validation Update Attachment Asset Record
            if ($asset_attachment) {
                DB::commit();
                return redirect()
                    ->back()
                    ->with(['success' => 'Successfully Add Attachment']);
            } else {
                // Failed and Rollback
                DB::rollBack();
                return redirect()
                    ->back()
                    ->with(['failed' => 'Failed Add Attachment'], 400);
            }
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with(['failed' => $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * Remove the specified resource in storage.
     */
    public function destroyImage(Request $request, string $id)
    {
        try {
            // Request Validation
            $request->validate([
                'file_name' => 'required',
            ]);

            DB::beginTransaction();

            $asset = Asset::find($id);

            // Image Path
            $path = 'public/asset/physical';
            $path_store = 'storage/asset/physical';

            $attachment_collection = json_decode($asset->attachment);

            $new_attachment_collection = [];

            foreach ($attachment_collection as $attachment) {
                if ($request->file_name != $attachment) {
                    array_push($new_attachment_collection, $attachment);
                } else {
                    $file_name = explode($path_store . '/', $attachment)[count(explode($path_store . '/', $attachment)) - 1];
                    Storage::delete($path . '/' . $file_name);

                    if (Storage::exists($path . '/' . $file_name)) {
                        return response()->json(['failed' => 'Failed Remove File'], 400);
                    }
                }
            }

            if (empty($new_attachment_collection)) {
                // Update Record for Attachment
                $asset_attachment = $asset->update([
                    'attachment' => null,
                ]);
            } else {
                // Update Record for Attachment
                $asset_attachment = $asset->update([
                    'attachment' => json_encode($new_attachment_collection),
                ]);
            }

            // Validation Update Attachment Asset Record
            if ($asset_attachment) {
                DB::commit();
                return response()->json(['success' => 'Successfully Updated Attachment'], 200);
            } else {
                // Failed and Rollback
                DB::rollBack();
                return response()->json(['failed' => 'Failed Updated Attachment'], 400);
            }
        } catch (\Exception $e) {
            return response()->json(['failed' => $e->getMessage()], 400);
        }
    }

    public function assignTo(Request $request, string $id)
    {
        try {
            $physical = Asset::find($id);

            if (!is_null($physical)) {
                /**
                 * Begin Transaction
                 */
                DB::beginTransaction();

                /**
                 * Update Asset Record
                 */
                $add_assign = $physical->update([
                    'assign_to' => $request->assign_to,
                    'assign_at' => now(),
                ]);

                /**
                 * Validation Update Asset Record
                 */
                if ($add_assign) {
                    $path = 'public/asset/physical/proof_assign';
                    $path_store = 'storage/asset/physical/proof_assign';

                    // Check Exsisting Path
                    if (!Storage::exists($path)) {
                        // Create new Path Directory
                        Storage::makeDirectory($path);
                    }

                    $proof_assign_attachment = [];

                    foreach ($request->file('attachment') as $file) {
                        // File Upload Configuration
                        $file_name = $physical->id . '-proof-assign-' . uniqid() . '-' . strtotime(date('Y-m-d H:i:s')) . '.' . $file->getClientOriginalExtension();

                        // Uploading File
                        $file->storePubliclyAs($path, $file_name);

                        // Check Upload Success
                        if (Storage::exists($path . '/' . $file_name)) {
                            $proof_assign_attachment['proof_assign'][] = $path_store . '/' . $file_name;
                        } else {
                            // Failed and Rollback
                            DB::rollBack();
                            return redirect()
                                ->back()
                                ->with(['failed' => 'Failed Upload Attachment'])
                                ->withInput();
                        }
                    }

                    if (empty($proof_assign_attachment)) {
                        // Update Record for Attachment
                        $proof_assign_attachment = null;
                    } else {
                        // Update Record for Attachment
                        $proof_assign_attachment = json_encode($proof_assign_attachment);
                    }

                    $history_assign = HistoryAssign::create([
                        'assets_id' => $id,
                        'assign_to' => $request->assign_to,
                        'assign_at' => now(),
                        'latest' => true,
                        'attachment' => $proof_assign_attachment,
                        'created_by' => Auth::user()->id,
                        'updated_by' => Auth::user()->id,
                    ]);

                    /**
                     * Validation Add history Record
                     */
                    if ($history_assign) {
                        DB::commit();
                        return redirect()->back()->with('success', 'Assign Successfully Add');
                    } else {
                        /**
                         * Failed Store Record
                         */
                        DB::rollBack();
                        return redirect()->back()->with('failed', 'Failed Add Record Assign');
                    }
                } else {
                    /**
                     * Failed Store Record
                     */
                    DB::rollBack();
                    return redirect()->back()->with('failed', 'Failed Add Assign');
                }
            } else {
                /**
                 * Failed Store Record
                 */
                DB::rollBack();
                return redirect()->back()->with('failed', 'Invalid Request!');
            }
        } catch (\Exception $e) {
            return redirect()->back()->with('failed', $e->getMessage());
        }
    }

    public function maintence(Request $request, string $id)
    {
        try {
            $physical = Asset::find($id);

            if (!is_null($physical)) {
                DB::beginTransaction();

                $path = 'public/asset/physical/proof_maintence';
                $path_store = 'storage/asset/physical/proof_maintence';

                // Check Exsisting Path
                if (!Storage::exists($path)) {
                    // Create new Path Directory
                    Storage::makeDirectory($path);
                }

                $proof_maintence_attachment = [];

                foreach ($request->file('attachment') as $file) {
                    // File Upload Configuration
                    $file_name = $physical->id . '-proof-maintence-' . uniqid() . '-' . strtotime(date('Y-m-d H:i:s')) . '.' . $file->getClientOriginalExtension();

                    // Uploading File
                    $file->storePubliclyAs($path, $file_name);

                    // Check Upload Success
                    if (Storage::exists($path . '/' . $file_name)) {
                        $proof_maintence_attachment['proof_maintence'][] = $path_store . '/' . $file_name;
                    } else {
                        // Failed and Rollback
                        DB::rollBack();
                        return redirect()
                            ->back()
                            ->with(['failed' => 'Failed Upload Attachment'])
                            ->withInput();
                    }
                }

                if (empty($proof_maintence_attachment)) {
                    // Update Record for Attachment
                    $proof_maintence_attachment = null;
                } else {
                    // Update Record for Attachment
                    $proof_maintence_attachment = json_encode($proof_maintence_attachment);
                }

                $history_maintence = HistoryMaintence::create([
                    'assets_id' => $id,
                    'description' => $request->description,
                    'date' => $request->date,
                    'status' => $request->status,
                    'latest' => true,
                    'attachment' => $proof_maintence_attachment,
                    'created_by' => Auth::user()->id,
                    'updated_by' => Auth::user()->id,
                ]);

                /**
                 * Validation Add history Record
                 */
                if ($history_maintence) {
                    DB::commit();
                    return redirect()->back()->with('success', 'Maintence Successfully Add');
                } else {
                    /**
                     * Failed Store Record
                     */
                    DB::rollBack();
                    return redirect()->back()->with('failed', 'Failed Add Maintence');
                }
            } else {
                /**
                 * Failed Store Record
                 */
                DB::rollBack();
                return redirect()->back()->with('failed', 'Invalid Request!');
            }
        } catch (Exception $e) {
            return redirect()
                ->back()
                ->with(['failed' => $e->getMessage()]);
        }
    }

    public function returnAsset(Request $request, string $id)
    {
        try {
            $physical = Asset::find($id);

            if (!is_null($physical)) {
                $last_assign = HistoryAssign::where('assets_id', $id)->whereNull('deleted_by')->whereNull('return_by')->whereNull('return_at')->whereNull('deleted_by')->whereNotNull('latest')->first();
                /**
                 * Begin Transaction
                 */
                DB::beginTransaction();

                /**
                 * Update Asset Record
                 */
                $remove_assign = $physical->update([
                    'assign_to' => null,
                    'assign_at' => null,
                ]);

                /**
                 * Validation Update Asset Record
                 */
                if ($remove_assign) {
                    $path = 'public/asset/physical/proof_return_assign';
                    $path_store = 'storage/asset/physical/proof_return_assign';

                    // Check Exsisting Path
                    if (!Storage::exists($path)) {
                        // Create new Path Directory
                        Storage::makeDirectory($path);
                    }

                    $proof_return_assign_attachment['proof_assign'] = json_decode($last_assign->attachment)->proof_assign;

                    foreach ($request->file('attachment') as $file) {
                        // File Upload Configuration
                        $file_name = $physical->id . '-proof-return-assign-' . uniqid() . '-' . strtotime(date('Y-m-d H:i:s')) . '.' . $file->getClientOriginalExtension();

                        // Uploading File
                        $file->storePubliclyAs($path, $file_name);

                        // Check Upload Success
                        if (Storage::exists($path . '/' . $file_name)) {
                            $proof_return_assign_attachment['proof_return_assign'][] = $path_store . '/' . $file_name;
                        } else {
                            // Failed and Rollback
                            DB::rollBack();
                            return redirect()
                                ->back()
                                ->with(['failed' => 'Failed Upload Attachment'])
                                ->withInput();
                        }
                    }

                    $proof_return_assign_attachment = json_encode($proof_return_assign_attachment);

                    $history_assign = HistoryAssign::where('assets_id', $id)
                        ->whereNull('deleted_by')
                        ->whereNull('return_by')
                        ->whereNull('return_at')
                        ->whereNull('deleted_by')
                        ->whereNotNull('latest')
                        ->update([
                            'return_by' => Auth::user()->id,
                            'return_at' => now(),
                            'latest' => null,
                            'attachment' => $proof_return_assign_attachment,
                            'updated_by' => Auth::user()->id,
                        ]);

                    /**
                     * Validation Add history Record
                     */
                    if ($history_assign) {
                        DB::commit();
                        return redirect()->back()->with('success', 'Return Asset Successfully Add');
                    } else {
                        /**
                         * Failed Store Record
                         */
                        DB::rollBack();
                        return redirect()->back()->with('failed', 'Failed Add Return Asset');
                    }
                } else {
                    /**
                     * Failed Store Record
                     */
                    DB::rollBack();
                    return redirect()->back()->with('failed', 'Failed Add Return Asset');
                }
            } else {
                /**
                 * Failed Store Record
                 */
                DB::rollBack();
                return redirect()->back()->with('failed', 'Invalid Request!');
            }
        } catch (\Exception $e) {
            return redirect()->back()->with('failed', $e->getMessage());
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
