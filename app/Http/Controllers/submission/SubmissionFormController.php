<?php

namespace App\Http\Controllers\submission;

use App\Http\Controllers\Controller;
use App\Models\asset\Asset;
use App\Models\history\HistoryAssign;
use App\Models\history\HistoryCheckInOut;
use App\Models\master\User;
use App\Models\submission\SubmissionForm;
use App\Models\submission\SubmissionFormItemAsset;
use App\Models\submission\SubmissionFormsCheckoutDate;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;

class SubmissionFormController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $datatable_route = route('submission.dataTable');

        $can_create = User::find(Auth::user()->id)->hasRole('admin');

        return view('submission.index', compact('datatable_route', 'can_create'));
    }

    public function dataTable()
    {
        /**
         * Get All submission
         */
        if (User::find(Auth::user()->id)->hasRole('staff')) {
            $submission = SubmissionForm::whereNull('deleted_by')
                ->whereNull('deleted_at')
                ->where('created_by', Auth::user()->id)
                ->orderBy('created_at', 'desc')
                ->get();
        } else {
            $submission = SubmissionForm::whereNull('deleted_by')->whereNull('deleted_at')->orderBy('created_at', 'desc')->get();
        }

        /**
         * Datatable Configuration
         */
        $dataTable = DataTables::of($submission)
            ->addIndexColumn()
            ->addColumn('created_at', function ($data) {
                return date('d F Y H:i:s', strtotime($data->created_at));
            })
            ->addColumn('tipe', function ($data) {
                return $data->tipe == 1 ? 'penugasan' : ($data->tipe == 2 ? 'peminjaman ' : '-');
            })
            ->addColumn('status', function ($data) {
                if ($data->diterima_oleh != null && $data->diterima_pada != null) {
                    return '<div class="badge badge-success">Diterima</div>';
                } elseif ($data->ditolak_oleh != null && $data->ditolak_pada != null) {
                    return '<div class="badge badge-danger">Ditolak</div>';
                } else {
                    return '<div class="badge badge-warning">Sedang Proses</div>';
                }
            })
            ->addColumn('created_by', function ($data) {
                return $data->createdBy->nama;
            })

            ->addColumn('aksi', function ($data) {
                $btn_action = '<div align="center">';
                $btn_action .= '<a href="' . route('submission.show', ['id' => $data->id]) . '" class="btn btn-sm btn-primary" title="Detail">Detail</a>';

                /**
                 * Validation Role Has Access Edit and Delete
                 */
                if (User::find(Auth::user()->id)->hasRole('staff')) {
                    if (!isset($data->diterima_pada) && !isset($data->ditolak_pada)) {
                        if ($data->tipe == 1) {
                            $btn_action .= '<a href="' . route('submission.edit', ['id' => $data->id]) . '" class="btn btn-sm btn-warning ml-2" title="Edit">Edit</a>';
                        } else {
                            $btn_action .= '<a href="' . route('submission.edit', ['tipe' => 'peminjaman', 'id' => $data->id]) . '" class="btn btn-sm btn-warning ml-2" title="Edit">Edit</a>';
                        }

                        $btn_action .= '<button class="btn btn-sm btn-danger ml-2" onclick="destroyRecord(' . $data->id . ')" title="Hapus">Hapus</button>';
                    }
                } else {
                    if (!isset($data->diterima_pada) && !isset($data->ditolak_pada)) {
                        $btn_action .= '<button class="btn btn-sm btn-danger ml-2" onclick="rejectedRecord(' . $data->id . ')"title="Ditolak">Ditolak</button>';
                        $btn_action .= '<button class="btn btn-sm btn-success ml-2" onclick="approvedRecord(' . $data->id . ')" title="Diterima">Diterima</button>';
                    }
                }
                $btn_action .= '</div>';
                return $btn_action;
            })
            ->only(['tipe', 'deskripsi', 'status', 'created_at', 'created_by', 'aksi'])
            ->rawColumns(['status', 'aksi'])
            ->make(true);

        return $dataTable;
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request, string $type)
    {
        try {
            if (in_array($type, ['assign', 'checkouts'])) {
                if (isset($request->asset)) {
                    $asset = Asset::find($request->asset);

                    if (!is_null($asset)) {
                        return view('submission.' . $type . '.asset.create', compact('asset'));
                    } else {
                        return redirect()
                            ->back()
                            ->with(['failed' => 'Invalid Request!']);
                    }
                } else {
                    if ($type == 'assign') {
                        $assets = Asset::whereNull('deleted_by')
                            ->whereNull('deleted_at')
                            ->whereNull('ditugaskan_ke')
                            ->whereNull('ditugaskan_pada')
                            ->whereNull('dipinjam_oleh')
                            ->whereNull('dipinjam_pada')
                            ->whereNotIn('status', [3, 4, 5])
                            ->get();
                    } else {
                        $assets = Asset::whereNull('deleted_by')
                            ->whereNull('deleted_at')
                            ->whereNull('ditugaskan_ke')
                            ->whereNull('ditugaskan_pada')
                            ->whereNull('dipinjam_oleh')
                            ->whereNull('dipinjam_pada')
                            ->whereNotIn('status', [3, 4, 5])
                            ->where('tipe', 1)
                            ->get();
                    }

                    return view('submission.' . $type . '.form.create', compact('assets'));
                }
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
     * Store a newly created resource in storage.
     */
    public function store(int $type, Request $request)
    {
        try {
            /**
             * Validation Request Body Variables
             */
            $request->validate([
                'deskripsi' => 'required',
            ]);

            /**
             * Checking Type Requested
             */
            if (in_array($type, [1, 2])) {
                DB::beginTransaction();

                /**
                 * Create SubmissionForm Record
                 */
                $submission = SubmissionForm::lockforUpdate()->create([
                    'tipe' => $type,
                    'deskripsi' => $request->deskripsi,
                    'created_by' => Auth::user()->id,
                    'updated_by' => Auth::user()->id,
                ]);

                /**
                 * Validation Create SubmissionForm Record
                 */
                if ($submission) {
                    /**
                     * Has Lampiran
                     */
                    if ($request->hasFile('lampiran')) {
                        $path = 'public/submission/' . $submission->id;
                        $path_store = 'storage/submission/' . $submission->id;

                        if (!Storage::exists($path)) {
                            Storage::makeDirectory($path);
                        }

                        $file_name = $submission->id . '-' . uniqid() . '-' . strtotime(date('Y-m-d H:i:s')) . '.' . $request->file('lampiran')->getClientOriginalExtension();
                        $request->file('lampiran')->storePubliclyAs($path, $file_name);
                        $attachment = $path_store . '/' . $file_name;

                        $submision_attachment = $submission->update([
                            'lampiran' => $attachment,
                        ]);

                        $assets_request = [];
                        foreach ($request->assets as $asset) {
                            array_push($assets_request, [
                                'id_form_pengajuan' => $submission->id,
                                'id_aset' => $asset['id'],
                            ]);
                        }

                        $submission_form_item_assets = SubmissionFormItemAsset::insert($assets_request);

                        if ($submision_attachment && $submission_form_item_assets) {
                            if (Storage::exists($path . '/' . $file_name)) {
                                /**
                                 * Form as Checkout
                                 */
                                if ($type == 2) {
                                    $date_request = [];
                                    array_push($date_request, [
                                        'id_form_pengajuan' => $submission->id,
                                        'tanggal_pengajuan_peminjaman_aset' => $request->tanggal_pengajuan_peminjaman_aset,
                                        'tanggal_pengembalian_aset' => $request->tanggal_pengembalian_aset,
                                    ]);

                                    $submissionFormCheckoutDate = SubmissionFormsCheckoutDate::insert($date_request);

                                    if ($submissionFormCheckoutDate) {
                                        DB::commit();
                                        return redirect()
                                            ->route('submission.index')
                                            ->with(['success' => 'Berhasil Menambahkan Pengajuan Peminjaman']);
                                    } else {
                                        /**
                                         * Gagal Store Record
                                         */
                                        DB::rollBack();
                                        return redirect()
                                            ->back()
                                            ->with(['failed' => 'Gagal Menambahkan Pengajuan Peminjaman'])
                                            ->withInput();
                                    }
                                } else {
                                    DB::commit();
                                    return redirect()
                                        ->route('submission.index')
                                        ->with(['success' => 'Berhasil Menambahkan Pengajuan Penugasan']);
                                }
                            } else {
                                /**
                                 * Gagal Store Record
                                 */
                                DB::rollBack();
                                return redirect()
                                    ->back()
                                    ->with(['failed' => 'Gagal Upload Lampiran'])
                                    ->withInput();
                            }
                        } else {
                            /**
                             * Gagal Store Record
                             */
                            DB::rollBack();
                            return redirect()
                                ->back()
                                ->with(['failed' => 'Gagal Menambahkan Pengajuan'])
                                ->withInput();
                        }
                    } else {
                        $assets_request = [];
                        foreach ($request->assets as $asset) {
                            array_push($assets_request, [
                                'id_form_pengajuan' => $submission->id,
                                'id_aset' => $asset['id'],
                            ]);
                        }

                        $submission_form_item_assets = SubmissionFormItemAsset::insert($assets_request);

                        if ($submission_form_item_assets) {
                            /**
                             * Form as Checkout
                             */
                            if ($type == 2) {
                                $date_request = [];
                                array_push($date_request, [
                                    'id_form_pengajuan' => $submission->id,
                                    'tanggal_pengajuan_peminjaman_aset' => $request->tanggal_pengajuan_peminjaman_aset,
                                    'tanggal_pengembalian_aset' => $request->tanggal_pengajuan_peminjaman_aset,
                                ]);

                                $submissionFormCheckoutDate = SubmissionFormsCheckoutDate::insert($date_request);

                                if ($submissionFormCheckoutDate) {
                                    DB::commit();
                                    return redirect()
                                        ->route('submission.index')
                                        ->with(['success' => 'Berhasil Menambahkan Pengajuan Peminjaman']);
                                } else {
                                    /**
                                     * Gagal Store Record
                                     */
                                    DB::rollBack();
                                    return redirect()
                                        ->back()
                                        ->with(['failed' => 'Gagal Menambahkan Pengajuan Peminjaman'])
                                        ->withInput();
                                }
                            } else {
                                DB::commit();
                                return redirect()
                                    ->route('submission.index')
                                    ->with(['success' => 'Berhasil Menambahkan Pengajuan Penugasan']);
                            }
                        } else {
                            /**
                             * Gagal Store Record
                             */
                            DB::rollBack();
                            return redirect()
                                ->back()
                                ->with(['failed' => 'Gagal Menambahkan Pengajuan'])
                                ->withInput();
                        }
                    }
                } else {
                    /**
                     * Gagal Store Record
                     */
                    DB::rollBack();
                    return redirect()
                        ->back()
                        ->with(['failed' => 'Gagal Menambahkan Pengajuan'])
                        ->withInput();
                }
            } else {
                return redirect()
                    ->back()
                    ->with(['failed' => 'Invalid Request!'])
                    ->withInput();
            }
        } catch (Exception $e) {
            dd($e->getLine(), $e->getFile());
            return redirect()
                ->back()
                ->with(['failed' => $e->getMessage()])
                ->withInput();
        }
    }

    public function update(Request $request, string $id)
    {
        try {
            $request->validate([
                'deskripsi' => 'required',
            ]);

            $submission = SubmissionForm::find($id);

            if (!is_null($submission)) {
                DB::beginTransaction();

                // Update deskripsi
                $submission_update = SubmissionForm::where('id', $id)->update([
                    'deskripsi' => $request->deskripsi,
                ]);

                if ($submission_update) {
                    if ($request->hasFile('lampiran')) {
                        $path = 'public/submission/' . $submission->id;
                        $path_store = 'storage/submission/' . $submission->id;

                        if (!Storage::exists($path)) {
                            Storage::makeDirectory($path);
                        }

                        $file_name = $submission->id . '-' . uniqid() . '-' . strtotime(date('Y-m-d H:i:s')) . '.' . $request->file('lampiran')->getClientOriginalExtension();

                        // Hapus file yang sudah ada jika ada
                        if (Storage::exists($path . '/' . $file_name)) {
                            Storage::delete($path . '/' . $file_name);
                        }

                        // Simpan file yang diunggah
                        $request->file('lampiran')->storePubliclyAs($path, $file_name);
                        $attachment = $path_store . '/' . $file_name;

                        // Update lampiran
                        $submission_attachment = $submission->update([
                            'lampiran' => $attachment,
                        ]);
                    }

                    // Hapus semua aset lama
                    SubmissionFormItemAsset::where('id_form_pengajuan', $submission->id)->delete();

                    // Menyimpan aset baru
                    if (is_array($request->assets) && !empty($request->assets)) {
                        foreach ($request->assets as $asset) {
                            // Memastikan bahwa $asset adalah array dan memiliki kunci 'id'
                            if (is_array($asset) && isset($asset['id'])) {
                                SubmissionFormItemAsset::create([
                                    'id_form_pengajuan' => $submission->id,
                                    'id_aset' => $asset['id'],
                                ]);
                            } else {
                                return redirect()->back()->with(['failed' => 'Invalid asset format']);
                            }
                        }
                    }

                    // Menangani pengisian form checkout
                    if ($submission->tipe == 2) {
                        $date_request = [
                            'id_form_pengajuan' => $submission->id,
                            'tanggal_pengajuan_peminjaman_aset' => $request->tanggal_pengajuan_peminjaman_aset,
                            'tanggal_pengembalian_aset' => $request->tanggal_pengembalian_aset,
                        ];

                        // Hapus tanggal checkout yang lama
                        SubmissionFormsCheckoutDate::where('id_form_pengajuan', $submission->id)->delete();
                        $submissionFormCheckoutDate = SubmissionFormsCheckoutDate::create($date_request);

                        if ($submissionFormCheckoutDate) {
                            DB::commit();
                            return redirect()
                                ->route('submission.index')
                                ->with(['success' => 'Berhasil Mengubah Pengajuan Peminjaman']);
                        } else {
                            DB::rollBack();
                            return redirect()
                                ->back()
                                ->with(['failed' => 'Gagal Mengubah Pengajuan Peminjaman'])
                                ->withInput();
                        }
                    } else {
                        DB::commit();
                        return redirect()
                            ->route('submission.index')
                            ->with(['success' => 'Berhasil Mengubah Pengajuan Penugasan']);
                    }
                } else {
                    DB::rollBack();
                    return redirect()
                        ->back()
                        ->with(['failed' => 'Gagal Mengubah Pengajuan'])
                        ->withInput();
                }
            } else {
                return redirect()->back()->with(['failed' => 'Invalid Request']);
            }
        } catch (Exception $e) {
            return redirect()->back()->with(['failed' => $e->getMessage()]);
        }
    }


    public function edit(Request $request, string $id)
    {
        try {
            $submission = SubmissionForm::find($id);
            $submissionItems = SubmissionFormItemAsset::where('id_form_pengajuan', $submission->id)->get();
            if (!is_null($submission)) {
                $submissionAssetIds = $submissionItems->pluck('id_aset')->toArray();
                if ($submission->tipe == 1) {
                    $type = 'assign';
                    $assets = Asset::whereNull('deleted_by')
                        ->whereNull('deleted_at')
                        ->whereNull('ditugaskan_ke')
                        ->whereNull('ditugaskan_pada')
                        ->whereNull('dipinjam_oleh')
                        ->whereNull('dipinjam_pada')
                        ->whereNotIn('status', [3, 4, 5])
                        ->whereNotIn('id', $submissionAssetIds)
                        ->get();
                } else {
                    $type = 'checkouts';
                    $assets = Asset::whereNull('deleted_by')
                        ->whereNull('deleted_at')
                        ->whereNull('ditugaskan_ke')
                        ->whereNull('ditugaskan_pada')
                        ->whereNull('dipinjam_oleh')
                        ->whereNull('dipinjam_pada')
                        ->whereNotIn('status', [3, 4, 5])
                        ->where('tipe', 1)
                        ->whereNotIn('id', $submissionAssetIds)
                        ->get();
                }
                return view('submission.' . $type . '.form.edit', compact('assets', 'submission'));
            } else {
                return redirect()
                    ->back()
                    ->with(['failed' => 'Invalid Request!']);
            }
        } catch (Exception $e) {
            return redirect()->back()->with(['failed' => $e->getMessage()]);
        }
    }
    public function show(request $request, string $id)
    {
        try {
            /**
             * Get Pengajuan Data from id
             */
            $submission = SubmissionForm::find($id);

            /**
             * Validation Pengajuan id
             */
            if (!is_null($submission)) {

                if (User::find(Auth::user()->id)->hasRole('staff')) {
                    if ($submission->created_by != Auth::user()->id) {
                        return redirect()
                            ->back()
                            ->with(['failed' => 'Invalid Request!']);
                    }
                }

                if ($submission->tipe == 1) {
                    return view('submission.assign.detail', compact('submission'));
                } else {
                    return view('submission.checkouts.detail', compact('submission'));
                }
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

    public function approve(Request $request)
    {
        try {
            $submission = SubmissionForm::find($request->id);

            if (!is_null($submission)) {
                /**
                 * Begin Transaction
                 */
                DB::beginTransaction();

                /**
                 * Update SubmissionForm Record
                 */
                $approved_submission = $submission->update([
                    'diterima_oleh' => Auth::user()->id,
                    'diterima_pada' => now(),
                ]);

                /**
                 * Validation Update SubmissionForm Record
                 */
                if ($approved_submission) {
                    DB::commit();
                    $submission_result = SubmissionForm::find($request->id);
                    session()->flash('success', 'Submission Berhasil Diterima');
                    return response()->json(['data', $submission_result], 200);
                } else {
                    /**
                     * Gagal Store Record
                     */
                    DB::rollBack();
                    session()->flash('failed', 'Submission Gagal Diterima');
                    return response()->json(['message', 'Failed'], 400);
                }
            } else {
                session()->flash('failed', 'Invalid Request!');
                return response()->json(['message', 'Invalid Request!'], 404);
            }
        } catch (Exception $e) {
            session()->flash('failed', $e->getMessage());
            return response()->json(['message', 'Failed!'], 400);
        }
    }

    public function reject(Request $request)
    {
        try {
            $submission = SubmissionForm::find($request->id);

            if (!is_null($submission)) {
                /**
                 * Begin Transaction
                 */
                DB::beginTransaction();

                /**
                 * Update SubmissionForm Record
                 */
                $rejected_submission = $submission->update([
                    'ditolak_oleh' => Auth::user()->id,
                    'ditolak_pada' => now(),
                    'alasan' => $request->alasan,
                ]);

                /**
                 * Validation Update SubmissionForm Record
                 */
                if ($rejected_submission) {
                    DB::commit();
                    $submission_result = SubmissionForm::find($request->id);
                    session()->flash('success', 'Submission Berhasil Ditolak');
                    return response()->json(['data', $submission_result], 200);
                } else {
                    /**
                     * Gagal Store Record
                     */
                    DB::rollBack();
                    session()->flash('failed', 'Submission Gagal Ditolak');
                    return response()->json(['message', 'Failed'], 400);
                }
            } else {
                session()->flash('failed', 'Invalid Request!');
                return response()->json(['message', 'Invalid Request!'], 404);
            }
        } catch (Exception $e) {
            session()->flash('failed', $e->getMessage());
            return response()->json(['message', 'Failed!'], 400);
        }
    }

    public function assignTo(Request $request, string $id)
    {
        try {
            $submission = SubmissionForm::find($id);

            if (!is_null($submission)) {
                /**
                 * Begin Transaction
                 */
                DB::beginTransaction();

                /**
                 * Update Penugasan Status Asset Record
                 */
                $add_assign = Asset::where('id', $request->id_aset)->update([
                    'ditugaskan_ke' => $submission->created_by,
                    'ditugaskan_pada' => now(),
                ]);

                /**
                 * Validation Update Asset Record
                 */
                if ($add_assign) {
                    $path = 'public/asset/physical/bukti_penugasan';
                    $path_store = 'storage/asset/physical/bukti_penugasan';

                    // Check Exsisting Path
                    if (!Storage::exists($path)) {
                        // Create new Path Directory
                        Storage::makeDirectory($path);
                    }

                    $proof_assign_attachment = [];

                    foreach ($request->file('lampiran') as $file) {
                        // File Upload Configuration
                        $file_name = $request->id_aset . '-proof-assign-' . uniqid() . '-' . strtotime(date('Y-m-d H:i:s')) . '.' . $file->getClientOriginalExtension();

                        // Uploading File
                        $file->storePubliclyAs($path, $file_name);

                        // Check Upload Success
                        if (Storage::exists($path . '/' . $file_name)) {
                            $proof_assign_attachment['bukti_penugasan'][] = $path_store . '/' . $file_name;
                        } else {
                            // Gagal and Rollback
                            DB::rollBack();
                            return redirect()
                                ->back()
                                ->with(['failed' => 'Gagal Upload Lampiran'])
                                ->withInput();
                        }
                    }

                    if (empty($proof_assign_attachment)) {
                        // Update Data for Lampiran
                        $proof_assign_attachment = null;
                    } else {
                        // Update Data for Lampiran
                        $proof_assign_attachment = json_encode($proof_assign_attachment);
                    }

                    $history_assign = HistoryAssign::create([
                        'id_aset' => $request->id_aset,
                        'id_form_pengajuan' => $id,
                        'ditugaskan_ke' => $submission->created_by,
                        'ditugaskan_pada' => now(),
                        'latest' => true,
                        'lampiran' => $proof_assign_attachment,
                        'created_by' => Auth::user()->id,
                        'updated_by' => Auth::user()->id,
                    ]);

                    /**
                     * Validation Menambahkan history Record
                     */
                    if ($history_assign) {
                        DB::commit();
                        return redirect()->back()->with('success', 'Penugasan Berhasil Ditambah');
                    } else {
                        /**
                         * Gagal Store Record
                         */
                        DB::rollBack();
                        return redirect()->back()->with('failed', 'Gagal Menambah Data Penugasan');
                    }
                } else {
                    /**
                     * Gagal Store Record
                     */
                    DB::rollBack();
                    return redirect()->back()->with('failed', 'Gagal Menambah Penugasan');
                }
            } else {
                session()->flash('failed', 'Invalid Request!');
                return response()->json(['message', 'Invalid Request!'], 404);
            }
        } catch (\Exception $e) {
            return redirect()->back()->with('failed', $e->getMessage());
        }
    }

    public function checkOut(Request $request, string $id)
    {
        try {
            $submission = SubmissionForm::find($id);

            if (!is_null($submission)) {
                /**
                 * Begin Transaction
                 */
                DB::beginTransaction();

                /**
                 * Update Penugasan Status Asset Record
                 */
                $add_check_out = Asset::where('id', $request->id_aset)->update([
                    'dipinjam_oleh' => $submission->created_by,
                    'dipinjam_pada' => now(),
                ]);

                /**
                 * Validation Update Asset Record
                 */
                if ($add_check_out) {
                    $path = 'public/asset/physical/proof_peminjaman';
                    $path_store = 'storage/asset/physical/proof_peminjaman';

                    // Check Exsisting Path
                    if (!Storage::exists($path)) {
                        // Create new Path Directory
                        Storage::makeDirectory($path);
                    }

                    $proof_check_out_attachment = [];

                    foreach ($request->file('lampiran') as $file) {
                        // File Upload Configuration
                        $file_name = $request->id_aset . '-proof-check-out-' . uniqid() . '-' . strtotime(date('Y-m-d H:i:s')) . '.' . $file->getClientOriginalExtension();

                        // Uploading File
                        $file->storePubliclyAs($path, $file_name);

                        // Check Upload Success
                        if (Storage::exists($path . '/' . $file_name)) {
                            $proof_check_out_attachment['proof_peminjaman'][] = $path_store . '/' . $file_name;
                        } else {
                            // Gagal and Rollback
                            DB::rollBack();
                            return redirect()
                                ->back()
                                ->with(['failed' => 'Gagal Upload Lampiran'])
                                ->withInput();
                        }
                    }

                    if (empty($proof_check_out_attachment)) {
                        // Update Data for Lampiran
                        $proof_check_out_attachment = null;
                    } else {
                        // Update Data for Lampiran
                        $proof_check_out_attachment = json_encode($proof_check_out_attachment);
                    }

                    $history_check_out = HistoryCheckInOut::create([
                        'id_aset' => $request->id_aset,
                        'id_form_pengajuan' => $id,
                        'dipinjam_oleh' => $submission->created_by,
                        'dipinjam_pada' => now(),
                        'latest' => true,
                        'lampiran' => $proof_check_out_attachment,
                        'created_by' => Auth::user()->id,
                        'updated_by' => Auth::user()->id,
                    ]);

                    /**
                     * Validation Menambahkan history Record
                     */
                    if ($history_check_out) {
                        DB::commit();
                        return redirect()->back()->with('success', 'Peminjaman Berhasil Ditambahkan');
                    } else {
                        /**
                         * Gagal Store Record
                         */
                        DB::rollBack();
                        return redirect()->back()->with('failed', 'Gagal Menambahkan Data Peminjaman');
                    }
                } else {
                    /**
                     * Gagal Store Record
                     */
                    DB::rollBack();
                    return redirect()->back()->with('failed', 'Gagal Menambahkan Peminjaman');
                }
            } else {
                session()->flash('failed', 'Invalid Request!');
                return response()->json(['message', 'Invalid Request!'], 404);
            }
        } catch (\Exception $e) {
            return redirect()->back()->with('failed', $e->getMessage());
        }
    }

    public function checkIn(Request $request, string $id)
    {

        try {
            $submission = SubmissionForm::find($id);

            if (!is_null($submission)) {
                $last_check_out = HistoryCheckInOut::where('id_aset', $request->id_aset)->whereNull('deleted_by')->whereNull('pengembalian_oleh')->whereNull('pengembalian_pada')->whereNull('deleted_by')->whereNotNull('latest')->first();
                /**
                 * Begin Transaction
                 */

                DB::beginTransaction();

                $remove_check_out = Asset::where('id', $request->id_aset)->update([
                    'dipinjam_oleh' => null,
                    'dipinjam_pada' => null,
                ]);

                /**
                 * Validation Update Asset Record
                 */
                if ($remove_check_out) {
                    $path = 'public/asset/physical/proof_check_in';
                    $path_store = 'storage/asset/physical/proof_check_in';

                    // Check Exsisting Path
                    if (!Storage::exists($path)) {
                        // Create new Path Directory
                        Storage::makeDirectory($path);
                    }

                    $proof_check_in_attachment['proof_peminjaman'] = json_decode($last_check_out->lampiran)->proof_peminjaman;



                    foreach ($request->file('lampiran') as $file) {
                        // File Upload Configuration
                        $file_name = $request->id_aset . '-proof-check-in-' . uniqid() . '-' . strtotime(date('Y-m-d H:i:s')) . '.' . $file->getClientOriginalExtension();

                        // Uploading File
                        $file->storePubliclyAs($path, $file_name);

                        // Check Upload Success
                        if (Storage::exists($path . '/' . $file_name)) {
                            $proof_check_in_attachment['bukti_pengembalian'][] = $path_store . '/' . $file_name;
                        } else {
                            // Gagal and Rollback
                            DB::rollBack();
                            return redirect()
                                ->back()
                                ->with(['failed' => 'Gagal Upload Lampiran'])
                                ->withInput();
                        }
                    }

                    $proof_check_in_attachment = json_encode($proof_check_in_attachment);

                    $history_check_in = HistoryCheckInOut::where('id_aset', $request->id_aset)
                        ->whereNull('deleted_by')
                        ->whereNull('pengembalian_oleh')
                        ->whereNull('pengembalian_pada')
                        ->whereNull('deleted_by')
                        ->whereNotNull('latest')
                        ->update([
                            'pengembalian_oleh' => Auth::user()->id,
                            'pengembalian_pada' => now(),
                            'latest' => null,
                            'lampiran' => $proof_check_in_attachment,
                            'updated_by' => Auth::user()->id,
                        ]);

                    /**
                     * Validation Menambahkan history Record
                     */
                    if ($history_check_in) {
                        DB::commit();
                        return redirect()->back()->with('success', 'Pengembalian Berhasil Ditambahkan');
                    } else {
                        /**
                         * Gagal Store Record
                         */
                        DB::rollBack();
                        return redirect()->back()->with('failed', 'Gagal Menambahkan Pengembalian');
                    }
                } else {
                    /**
                     * Gagal Store Record
                     */
                    DB::rollBack();
                    return redirect()->back()->with('failed', 'Gagal Menambahkan Pengembalian');
                }
            } else {
                /**
                 * Gagal Store Record
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
            DB::beginTransaction();
            $submission_destroy = SubmissionForm::where('id', $id)->update([
                'deleted_by' => Auth::user()->id,
                'deleted_at' => date('Y-m-d H:i:s'),
            ]);

            /**
             * Validation Update brand Record
             */
            if ($submission_destroy) {
                DB::commit();
                session()->flash('success', 'Pengajuan Berhasil Dihapus');
            } else {
                /**
                 * Gagal Store Record
                 */
                DB::rollBack();
                session()->flash('failed', 'Gagal Hapus Pengajuan');
            }
        } catch (Exception $e) {
            session()->flash('failed', $e->getMessage());
        }
    }
}
