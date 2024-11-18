<?php

namespace App\Http\Controllers\master;

use App\Http\Controllers\Controller;
use App\Models\master\Division;
use App\Models\master\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class DivisionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $datatable_route = route('master.division.dataTable');


        $can_create = User::find(Auth::user()->id)->hasRole('admin');

        return view('master.division.index', compact('datatable_route', 'can_create'));
    }

    public function dataTable()
    {
        /**
         * Get All Division
         */
        $division = Division::whereNull('deleted_at')->get();

        /**
         * Datatable Configuration
         */
        $dataTable = DataTables::of($division)
            ->addIndexColumn()
            ->addColumn('aksi', function ($data) {
                $btn_action = '<div align="center">';

                /**
                 * Validation Role Has Access Edit and Delete
                 */

                if (User::find(Auth::user()->id)->hasRole('admin')) {

                    $btn_action .= '<a href="' . route('master.division.edit', ['id' => $data->id]) . '" class="btn btn-sm btn-warning ml-2" title="Edit">Edit</a>';
                    $btn_action .= '<button class="btn btn-sm btn-danger ml-2" onclick="destroyRecord(' . $data->id . ')" title="Delete">Delete</button>';
                }
                $btn_action .= '</div>';
                return $btn_action;
            })
            ->only(['nama', 'alamat', 'aksi'])
            ->rawColumns(['aksi'])
            ->make(true);

        return $dataTable;
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('master.division.create');
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
            ]);

            DB::beginTransaction();

            /**
             * Create Division Record
             */
            $division = Division::lockforUpdate()->create([
                'nama' => $request->nama,
            ]);

            /**
             * Validation Create Division Record
             */
            if ($division) {
                DB::commit();
                return redirect()
                    ->route('master.division.index')
                    ->with(['success' => 'Berhasil Tambah Divisi']);
            } else {
                /**
                 * Gagal Store Record
                 */
                DB::rollBack();
                return redirect()
                    ->back()
                    ->with(['failed' => 'Gagal Tambah Divisi'])
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
             * Get Division Record from id
             */
            $division = Division::find($id);

            /**
             * Validation Division id
             */
            if (!is_null($division)) {
                return view('master.division.edit', compact('division'));
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
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            /**
             * Validation Request Body Variables
             */
            $request->validate([
                'nama' => 'required|string',

            ]);

            $division = Division::find($id);

            if (!is_null($division)) {
                /**
                 * Begin Transaction
                 */
                DB::beginTransaction();

                /**
                 * Update Division Record
                 */
                $category_update = Division::where('id', $id)->update([
                    'nama' => $request->nama,
                ]);

                /**
                 * Validation Update Division Record
                 */
                if ($category_update) {
                    DB::commit();
                    return redirect()
                        ->route('master.division.index')
                        ->with(['success' => 'Berhasil Ubah Divisi']);
                } else {
                    /**
                     * Gagal Store Record
                     */
                    DB::rollBack();
                    return redirect()
                        ->back()
                        ->with(['failed' => 'Gagal Ubah Divisi'])
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
             * Update Division Record
             */
            $category_destroy = Division::where('id', $id)->update([
                'deleted_at' => date('Y-m-d H:i:s'),
            ]);

            /**
             * Validation Update Division Record
             */
            if ($category_destroy) {
                DB::commit();
                session()->flash('success', 'Division Berhasil Hapus');
            } else {
                /**
                 * Gagal Store Record
                 */
                DB::rollBack();
                session()->flash('failed', 'Gagal Hapus Divisi');
            }
        } catch (Exception $e) {
            session()->flash('failed', $e->getMessage());
        }
    }
}
