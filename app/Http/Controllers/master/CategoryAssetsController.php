<?php

namespace App\Http\Controllers\master;

use App\Http\Controllers\Controller;
use App\Models\master\CategoryAssets;

use App\Models\master\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class CategoryAssetsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $datatable_route = route('master.category.dataTable');


        $can_create = User::find(Auth::user()->id)->hasRole('admin');

        return view('master.category.index', compact('datatable_route', 'can_create'));
    }

    public function dataTable()
    {
        /**
         * Get All CategoryAssets
         */
        $category = CategoryAssets::whereNull('deleted_by')->whereNull('deleted_at')->get();

        /**
         * Datatable Configuration
         */
        $dataTable = DataTables::of($category)
            ->addIndexColumn()
            ->addColumn('tipe', function ($data) {
                return $data->tipe == 1 ? 'Aset Fisik' : ($data->tipe == 2 ? 'Aset Lisensi' : '-');
            })
            ->addColumn('aksi', function ($data) {
                $btn_action = '<div align="center">';

                /**
                 * Validation Role Has Access Edit and Hapus
                 */

                if (User::find(Auth::user()->id)->hasRole('admin')) {

                    $btn_action .= '<a href="' . route('master.category.edit', ['id' => $data->id]) . '" class="btn btn-sm btn-warning ml-2" title="Edit">Edit</a>';
                    $btn_action .= '<button class="btn btn-sm btn-danger ml-2" onclick="destroyRecord(' . $data->id . ')" title="Hapus">Hapus</button>';
                }
                $btn_action .= '</div>';
                return $btn_action;
            })
            ->only(['nama', 'tipe', 'aksi'])
            ->rawColumns(['aksi'])
            ->make(true);

        return $dataTable;
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('master.category.create');
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
                'nama' => 'required|string',
                'tipe' => 'required',
            ]);

            DB::beginTransaction();

            /**
             * Create CategoryAssets Record
             */
            $category = CategoryAssets::lockforUpdate()->create([
                'nama' => $request->nama,
                'tipe' => $request->tipe,
                'created_by' => Auth::user()->id,
                'updated_by' => Auth::user()->id,
            ]);

            /**
             * Validation Create CategoryAssets Record
             */
            if ($category) {
                DB::commit();
                return redirect()
                    ->route('master.category.index')
                    ->with(['success' => 'Berhasil Menambahkan Kategori Aset']);
            } else {
                /**
                 * Gagal Store Record
                 */
                DB::rollBack();
                return redirect()
                    ->back()
                    ->with(['failed' => 'Gagal Menambahkan Kategori Aset'])
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
    public function show(String $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        try {
            /**
             * Get CategoryAssets Record from id
             */
            $category = CategoryAssets::find($id);

            /**
             * Validation CategoryAssets id
             */
            if (!is_null($category)) {
                return view('master.category.edit', compact('category'));
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
     * Ubah the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            /**
             * Validation Request Body Variables
             */
            $request->validate([
                'nama' => 'required|string',
                'tipe' => 'required',

            ]);

            $category = CategoryAssets::find($id);

            if (!is_null($category)) {
                /**
                 * Begin Transaction
                 */
                DB::beginTransaction();

                /**
                 * Ubah CategoryAssets Record
                 */
                $category_update = CategoryAssets::where('id', $id)->update([
                    'nama' => $request->nama,
                    'tipe' => $request->tipe,
                    'updated_by' => Auth::user()->id,
                ]);

                /**
                 * Validation Ubah CategoryAssets Record
                 */
                if ($category_update) {
                    DB::commit();
                    return redirect()
                        ->route('master.category.index')
                        ->with(['success' => 'Berhasil Ubah Kategori Aset']);
                } else {
                    /**
                     * Gagal Store Record
                     */
                    DB::rollBack();
                    return redirect()
                        ->back()
                        ->with(['failed' => 'Gagal Ubah Kategori Aset'])
                        ->withInput();
                }
            } else {
                return redirect()
                    ->back()
                    ->with(['failed' => 'Invalid Request!']);
            }
        } catch (Exception $e) {
            return redirect()
                ->back()
                ->with(['failed' => $e->getMessage()])
                ->withInput();
        }
    }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(String $id)
    {
        try {
            /**
             * Begin Transaction
             */
            DB::beginTransaction();

            /**
             * Ubah CategoryAssets Record
             */
            $category_destroy = CategoryAssets::where('id', $id)->update([
                'deleted_by' => Auth::user()->id,
                'deleted_at' => date('Y-m-d H:i:s'),
            ]);

            /**
             * Validation Ubah CategoryAssets Record
             */
            if ($category_destroy) {
                DB::commit();
                session()->flash('success', 'Category Asset Berhasil Dihapus');
            } else {
                /**
                 * Gagal Store Record
                 */
                DB::rollBack();
                session()->flash('failed', 'Gagal Hapus Kategori Aset');
            }
        } catch (Exception $e) {
            session()->flash('failed', $e->getMessage());
        }
    }
}
