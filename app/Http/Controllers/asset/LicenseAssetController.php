<?php

namespace App\Http\Controllers\asset;

use App\Http\Controllers\Controller;
use App\Models\asset\Asset;
use App\Models\history\HistoryAssign;
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

class LicenseAssetController extends Controller
{
    public function index()
    {
        $datatable_route = route('asset.license.dataTable');

        $can_create = User::find(Auth::user()->id)->hasRole('admin');

        return view('asset.license.index', compact('datatable_route', 'can_create'));
    }

    public function dataTable()
    {
        /**
         * Get All Asset
         */
        $asset = Asset::whereNull('deleted_by')->whereNull('deleted_at')->where('tipe', 2)->get();

        /**
         * Datatable Configuration
         */
        $dataTable = DataTables::of($asset)
            ->addIndexColumn()
            ->addColumn('kategori', function ($data) {
                return $data->kategori ? $data->kategori->nama : '-';
            })
            ->addColumn('brand', function ($data) {
                return $data->brand ? $data->brand->nama : '-';
            })

            ->addColumn('status', function ($data) {
                if (!is_null($data->ditugaskan_ke)) {
                    if (User::find(Auth::user()->id)->hasRole('admin')) {
                        return '<span class="badge badge-danger">Ditugaskan Ke ' . $data->assignTo->nama . '</span>';
                    } else {
                        return '<span class="badge badge-danger">Sudah Ditugaskan</span>';
                    }
                } elseif ($data->status == 3) {
                    return '<span class="badge badge-danger">Kerusakan Berat</span>';
                } elseif ($data->status == 5) {
                    return '<span class="badge badge-danger">Lisensi Expired</span>';
                } else {
                    return '<span class="badge badge-success">Tersedia</span>';
                }
            })
            ->addColumn('aksi', function ($data) {
                $btn_action = '<div align="center">';

                /**
                 * Validation Role Has Access Edit and Delete
                 */

                $btn_action = '<div align="center">';
                $btn_action .= '<a href="' . route('asset.license.show', ['id' => $data->id]) . '" class="btn btn-sm btn-primary" title="Detail">Detail</a>';

                if (User::find(Auth::user()->id)->hasRole('admin')) {
                    $btn_action .= '<a href="' . route('asset.license.edit', ['id' => $data->id]) . '" class="btn btn-sm btn-warning ml-2" title="Edit">Edit</a>';
                    $btn_action .= '<button class="btn btn-sm btn-danger ml-2" onclick="destroyRecord(' . $data->id . ')" title="Hapus">Hapus</button>';
                } elseif (User::find(Auth::user()->id)->hasRole('staff')) {
                    if (is_null($data->ditugaskan_ke) && is_null($data->ditugaskan_pada) && $data->status != 5) {
                        $btn_action .= '<a href="' . route('submission.create', ['tipe' => 'assign', 'asset' => $data->id]) . '" class="btn btn-sm btn-danger ml-2" title="Ditugaskan Ke Saya">Ditugaskan Ke Saya</a>';
                    }
                }
                $btn_action .= '</div>';
                return $btn_action;
            })
            ->only(['nama', 'brand', 'kategori', 'status', 'aksi'])
            ->rawColumns(['aksi', 'status'])
            ->make(true);

        return $dataTable;
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = CategoryAssets::whereNull('deleted_by')->whereNull('deleted_at')->where('tipe', 2)->get();
        $brands = Brand::whereNull('deleted_by')->whereNull('deleted_at')->get();
        $manufactures = Manufacture::whereNull('deleted_by')->whereNull('deleted_at')->get();
        $users = User::whereNull('deleted_at')->role('staff')->get();
        return view('asset.license.create', compact('categories', 'brands', 'users', 'manufactures'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'id_kategori_aset' => 'nullable|integer|exists:kategori_aset,id',
                'barcode_code' => 'required|string',
                'nama' => 'required|string',
                'status' => 'required|integer',
                'nilai' => 'nullable|integer|min:0',
                'expired_pada' => 'nullable|date',
                'deskripsi' => 'nullable|string',
                'lampiran.*' => 'nullable|file|mimes:jpg,jpeg,png|max:10240',
                'id_brand' => 'required|integer|exists:brand,id',
                'id_manufaktur' => 'required|integer|exists:manufaktur,id',
                'tanggal_pengambilan' => 'nullable|date',
                'tanggal_akhir_garansi' => 'nullable|date|after_or_equal:tanggal_pengambilan',
                'durasi_garansi' => 'nullable|integer|min:0',
            ]);

            $barcode_check = Asset::whereNull('deleted_by')
                ->whereNull('deleted_at')
                ->where('barcode_code', $request->barcode_code)
                ->first();

            if (is_null($barcode_check)) {
                DB::beginTransaction();

                $asset = Asset::lockForUpdate()->create([
                    'nama' => $request->nama,
                    'id_kategori_aset' => $request->id_kategori_aset,
                    'tipe' => 2,
                    'barcode_code' => $request->barcode_code,
                    'status' => $request->status,
                    'nilai' => $request->value,
                    'expired_pada' => $request->expired_pada,
                    'deskripsi' => $request->deskripsi,
                    'id_brand' => $request->id_brand,
                    'id_manufaktur' => $request->id_manufaktur,
                    'tanggal_pengambilan' => $request->tanggal_pengambilan,
                    'tanggal_akhir_garansi' => $request->tanggal_akhir_garansi,
                    'durasi_garansi' => $request->durasi_garansi,
                    'created_by' => Auth::user()->id,
                    'updated_by' => Auth::user()->id,
                ]);

                if ($asset) {
                    $path = 'public/asset/license';
                    $path_store = 'storage/asset/license';

                    if (!Storage::exists($path)) {
                        Storage::makeDirectory($path);
                    }

                    $attachments = [];

                    if ($request->hasFile('lampiran')) {
                        foreach ($request->file('lampiran') as $file) {
                            // Menggunakan nama file asli dengan uniqid untuk menghindari duplikasi nama
                            $file_name = $asset->id . '-' . uniqid() . '-' . strtotime(date('Y-m-d H:i:s')) . '.' . $file->getClientOriginalExtension();
                            $file->storePubliclyAs($path, $file_name);
                            $attachments[] = $path_store . '/' . $file_name;
                        }
                    }

                    $asset->update([
                        'lampiran' => json_encode($attachments),
                    ]);

                    DB::commit();
                    return redirect()
                        ->route('asset.license.index')
                        ->with(['success' => 'Berhasil Tambah Aset Lisensi']);
                } else {
                    DB::rollBack();
                    return redirect()
                        ->back()
                        ->with(['failed' => 'Gagal Tambah Aset Lisensi'])
                        ->withInput();
                }
            } else {
                return redirect()
                    ->back()
                    ->with(['failed' => 'Barcode Sudah Tersedia'])
                    ->withInput();
            }
        } catch (Exception $e) {
            dd($e->getLine(), $e->getMessage(), $e->getFile());
        }
    }

    public function show(Request $request, string $id)
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
                    return response()->json(['success' => true, 'data' => $asset], 200);
                }

                return view('asset.license.detail', compact('asset', 'users'));
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
            $license = Asset::find($id);

            if (!is_null($license)) {
                $categories = CategoryAssets::whereNull('deleted_by')->whereNull('deleted_at')->where('tipe', 2)->get();
                $brands = Brand::whereNull('deleted_by')->whereNull('deleted_at')->get();
                $manufactures = Manufacture::whereNull('deleted_by')->whereNull('deleted_at')->get();
                $users = User::whereNull('deleted_at')->role('staff')->get();

                return view('asset.license.edit', compact('license', 'categories', 'brands', 'users', 'manufactures'));
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
                'id_kategori_aset' => 'nullable|integer|exists:kategori_aset,id',
                'barcode_code' => 'required|string',
                'nama' => 'required|string',
                'status' => 'required|integer',
                'nilai' => 'nullable|integer|min:0',
                'expired_pada' => 'nullable|date',
                'deskripsi' => 'nullable|string',
                'lampiran.*' => 'nullable|file|mimes:jpg,jpeg,png|max:10240',
                'ditugaskan_ke' => 'nullable',
                'ditugaskan_pada' => 'nullable',
                'id_brand' => 'required|integer|exists:brand,id',
                'id_manufaktur' => 'required|integer|exists:manufaktur,id',
                'tanggal_pengambilan' => 'nullable|date',
                'tanggal_akhir_garansi' => 'nullable|date|after_or_equal:tanggal_pengambilan',
                'durasi_garansi' => 'nullable|integer|min:0',
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
                        'nama' => $request->nama,
                        'id_kategori_aset' => $request->id_kategori_aset,
                        'tipe' => 2,
                        'barcode_code' => $request->barcode_code,
                        'status' => $request->status,
                        'nilai' => $request->value,
                        'expired_pada' => $request->expired_pada,
                        'deskripsi' => $request->deskripsi,
                        'ditugaskan_ke' => $request->ditugaskan_ke,
                        'ditugaskan_pada' => $request->ditugaskan_pada,
                        'id_brand' => $request->id_brand,
                        'id_manufaktur' => $request->id_manufaktur,
                        'tanggal_pengambilan' => $request->tanggal_pengambilan,
                        'tanggal_akhir_garansi' => $request->tanggal_akhir_garansi,
                        'durasi_garansi' => $request->durasi_garansi,
                        'created_by' => Auth::user()->id,
                        'updated_by' => Auth::user()->id,
                    ]);

                    /**
                     * Validation Update asset Record
                     */
                    if ($asset_update) {
                        DB::commit();
                        return redirect()
                            ->route('asset.license.index')
                            ->with(['success' => 'Berhasil Mengubah aset']);
                    } else {
                        /**
                         * Failed Store Record
                         */
                        DB::rollBack();
                        return redirect()
                            ->back()
                            ->with(['failed' => 'Gagal Mengubah aset'])
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
                    ->with(['failed' => 'Barcode Suadh Tersedia'])
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
                'lampiran' => 'required',
            ]);

            DB::beginTransaction();

            $asset = Asset::find($id);

            // Image Path
            $path = 'public/asset/license';
            $path_store = 'storage/asset/license';

            if (!is_null($asset->lampiran)) {
                $attachment_collection = json_decode($asset->lampiran);

                foreach ($request->file('lampiran') as $file) {
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
                            ->with(['failed' => 'Gagal Upload Lampiran'])
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

                foreach ($request->file('lampiran') as $file) {
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
                            ->with(['failed' => 'Gagal Upload Lampiran'])
                            ->withInput();
                    }
                }
            }

            // Update Record for Lampiran
            $asset_attachment = $asset->update([
                'lampiran' => json_encode($attachment_collection),
            ]);

            // Validation Update Lampiran Asset Record
            if ($asset_attachment) {
                DB::commit();
                return redirect()
                    ->back()
                    ->with(['success' => 'Berhasil Tambah Lampiran']);
            } else {
                // Failed and Rollback
                DB::rollBack();
                return redirect()
                    ->back()
                    ->with(['failed' => 'Gagal Tambah Lampiran'], 400);
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
            $path = 'public/asset/license';
            $path_store = 'storage/asset/license';

            $attachment_collection = json_decode($asset->lampiran);

            $new_attachment_collection = [];

            foreach ($attachment_collection as $lampiran) {
                if ($request->file_name != $lampiran) {
                    array_push($new_attachment_collection, $lampiran);
                } else {
                    $file_name = explode($path_store . '/', $lampiran)[count(explode($path_store . '/', $lampiran)) - 1];
                    Storage::delete($path . '/' . $file_name);

                    if (Storage::exists($path . '/' . $file_name)) {
                        return response()->json(['failed' => 'Gagal Hapus File'], 400);
                    }
                }
            }

            if (empty($new_attachment_collection)) {
                // Update Record for Lampiran
                $asset_attachment = $asset->update([
                    'lampiran' => null,
                ]);
            } else {
                // Update Record for Lampiran
                $asset_attachment = $asset->update([
                    'lampiran' => json_encode($new_attachment_collection),
                ]);
            }

            // Validation Update Lampiran Asset Record
            if ($asset_attachment) {
                DB::commit();
                return response()->json(['success' => 'Berhasil Ubah Lampiran'], 200);
            } else {
                // Failed and Rollback
                DB::rollBack();
                return response()->json(['failed' => 'Gagal Ubah Lampiran'], 400);
            }
        } catch (\Exception $e) {
            return response()->json(['failed' => $e->getMessage()], 400);
        }
    }

    public function assignTo(Request $request, string $id)
    {
        try {
            $license = Asset::find($id);

            if (!is_null($license)) {
                /**
                 * Begin Transaction
                 */
                DB::beginTransaction();

                /**
                 * Update Asset Record
                 */
                $add_assign = $license->update([
                    'ditugaskan_ke' => $request->ditugaskan_ke,
                    'ditugaskan_pada' => now(),
                ]);

                /**
                 * Validation Update Asset Record
                 */
                if ($add_assign) {
                    $path = 'public/asset/license/proof_assign';
                    $path_store = 'storage/asset/license/proof_assign';

                    // Check Exsisting Path
                    if (!Storage::exists($path)) {
                        // Create new Path Directory
                        Storage::makeDirectory($path);
                    }

                    $proof_assign_attachment = [];

                    foreach ($request->file('lampiran') as $file) {
                        // File Upload Configuration
                        $file_name = $license->id . '-proof-assign-' . uniqid() . '-' . strtotime(date('Y-m-d H:i:s')) . '.' . $file->getClientOriginalExtension();

                        // Uploading File
                        $file->storePubliclyAs($path, $file_name);

                        // Check Upload Success
                        if (Storage::exists($path . '/' . $file_name)) {
                            $proof_assign_attachment['bukti_penugasan'][] = $path_store . '/' . $file_name;
                        } else {
                            // Failed and Rollback
                            DB::rollBack();
                            return redirect()
                                ->back()
                                ->with(['failed' => 'Gagal Upload Lampiran'])
                                ->withInput();
                        }
                    }

                    if (empty($proof_assign_attachment)) {
                        // Update Record for Lampiran
                        $proof_assign_attachment = null;
                    } else {
                        // Update Record for Lampiran
                        $proof_assign_attachment = json_encode($proof_assign_attachment);
                    }

                    $history_assign = HistoryAssign::create([
                        'id_aset' => $id,
                        'ditugaskan_ke' => $request->ditugaskan_ke,
                        'ditugaskan_pada' => now(),
                        'latest' => true,
                        'lampiran' => $proof_assign_attachment,
                        'created_by' => Auth::user()->id,
                        'updated_by' => Auth::user()->id,
                    ]);

                    /**
                     * Validation Tambah history Record
                     */
                    if ($history_assign) {
                        DB::commit();
                        return redirect()->back()->with('success', 'Penugasan Berhasil Ditambahkan');
                    } else {
                        /**
                         * Failed Store Record
                         */
                        DB::rollBack();
                        return redirect()->back()->with('failed', 'Gagal Tambah Penugasan');
                    }
                } else {
                    /**
                     * Failed Store Record
                     */
                    DB::rollBack();
                    return redirect()->back()->with('failed', 'Gagal Tambah Penugasan');
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

    public function returnAsset(Request $request, string $id)
    {
        try {
            $license = Asset::find($id);

            if (!is_null($license)) {
                $last_assign = HistoryAssign::where('id_aset', $id)->whereNull('deleted_by')->whereNull('dikembalikan_oleh')->whereNull('dikembalikan_pada')->whereNull('deleted_by')->whereNotNull('latest')->first();
                /**
                 * Begin Transaction
                 */
                DB::beginTransaction();

                /**
                 * Update Asset Record
                 */
                $remove_assign = $license->update([
                    'ditugaskan_ke' => null,
                    'ditugaskan_pada' => null,
                ]);

                /**
                 * Validation Update Asset Record
                 */
                if ($remove_assign) {
                    $path = 'public/asset/license/proof_return_assign';
                    $path_store = 'storage/asset/license/proof_return_assign';

                    // Check Exsisting Path
                    if (!Storage::exists($path)) {
                        // Create new Path Directory
                        Storage::makeDirectory($path);
                    }

                    $proof_return_assign_attachment['bukti_penugasan'] = json_decode($last_assign->lampiran)->bukti_penugasan;

                    foreach ($request->file('lampiran') as $file) {
                        // File Upload Configuration
                        $file_name = $license->id . '-proof-return-assign-' . uniqid() . '-' . strtotime(date('Y-m-d H:i:s')) . '.' . $file->getClientOriginalExtension();

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
                                ->with(['failed' => 'Gagal Upload Lampiran'])
                                ->withInput();
                        }
                    }

                    $proof_return_assign_attachment = json_encode($proof_return_assign_attachment);

                    $history_assign = HistoryAssign::where('id_aset', $id)
                        ->whereNull('deleted_by')
                        ->whereNull('dikembalikan_oleh')
                        ->whereNull('dikembalikan_pada')
                        ->whereNull('deleted_by')
                        ->whereNotNull('latest')
                        ->update([
                            'dikembalikan_oleh' => Auth::user()->id,
                            'dikembalikan_pada' => now(),
                            'latest' => null,
                            'lampiran' => $proof_return_assign_attachment,
                            'updated_by' => Auth::user()->id,
                        ]);

                    /**
                     * Validation Tambah history Record
                     */
                    if ($history_assign) {
                        DB::commit();
                        return redirect()->back()->with('success', 'Pengembalian Aset Berhasil Ditambahkan');
                    } else {
                        /**
                         * Failed Store Record
                         */
                        DB::rollBack();
                        return redirect()->back()->with('failed', 'Gagal Tambah Pengembalian Aset');
                    }
                } else {
                    /**
                     * Failed Store Record
                     */
                    DB::rollBack();
                    return redirect()->back()->with('failed', 'Gagal Tambah Pengembalian Aset');
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
                session()->flash('success', 'Aset Berhasil Dihapus');
            } else {
                /**
                 * Failed Store Record
                 */
                DB::rollBack();
                session()->flash('failed', 'Gagal Menghapus Aset');
            }
        } catch (Exception $e) {
            session()->flash('failed', $e->getMessage());
        }
    }
}
