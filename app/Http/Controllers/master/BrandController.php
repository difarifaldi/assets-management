<?php

namespace App\Http\Controllers\master;

use App\Http\Controllers\Controller;
use App\Models\master\Brand;
use App\Models\master\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class BrandController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $datatable_route = route('master.brand.dataTable');


        $can_create = User::find(Auth::user()->id)->hasRole('admin');

        return view('master.brand.index', compact('datatable_route', 'can_create'));
    }

    public function dataTable()
    {
        /**
         * Get All brand
         */
        $brand = Brand::whereNull('deleted_by')->whereNull('deleted_at')->get();

        /**
         * Datatable Configuration
         */
        $dataTable = DataTables::of($brand)
            ->addIndexColumn()
            ->addColumn('action', function ($data) {
                $btn_action = '<div align="center">';

                /**
                 * Validation Role Has Access Edit and Delete
                 */

                if (User::find(Auth::user()->id)->hasRole('admin')) {

                    $btn_action .= '<a href="' . route('master.brand.edit', ['id' => $data->id]) . '" class="btn btn-sm btn-warning ml-2" title="Edit">Edit</a>';
                    $btn_action .= '<button class="btn btn-sm btn-danger ml-2" onclick="destroyRecord(' . $data->id . ')" title="Delete">Delete</button>';
                }
                $btn_action .= '</div>';
                return $btn_action;
            })
            ->only(['name', 'address', 'action'])
            ->rawColumns(['action'])
            ->make(true);

        return $dataTable;
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('master.brand.create');
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
            ]);

            DB::beginTransaction();

            /**
             * Create brand Record
             */
            $brand = Brand::lockforUpdate()->create([
                'name' => $request->name,
                'created_by' => Auth::user()->id,
                'updated_by' => Auth::user()->id,
            ]);

            /**
             * Validation Create brand Record
             */
            if ($brand) {
                DB::commit();
                return redirect()
                    ->route('master.brand.index')
                    ->with(['success' => 'Successfully Add brand']);
            } else {
                /**
                 * Failed Store Record
                 */
                DB::rollBack();
                return redirect()
                    ->back()
                    ->with(['failed' => 'Failed Add brand'])
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
             * Get brand Record from id
             */
            $brand = Brand::find($id);

            /**
             * Validation brand id
             */
            if (!is_null($brand)) {
                return view('master.brand.edit', compact('brand'));
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
                'name' => 'required|string',

            ]);

            $brand = Brand::find($id);

            if (!is_null($brand)) {
                /**
                 * Begin Transaction
                 */
                DB::beginTransaction();

                /**
                 * Update brand Record
                 */
                $brand_update = Brand::where('id', $id)->update([
                    'name' => $request->name,
                    'updated_by' => Auth::user()->id,
                ]);

                /**
                 * Validation Update brand Record
                 */
                if ($brand_update) {
                    DB::commit();
                    return redirect()
                        ->route('master.brand.index')
                        ->with(['success' => 'Successfully Update brand']);
                } else {
                    /**
                     * Failed Store Record
                     */
                    DB::rollBack();
                    return redirect()
                        ->back()
                        ->with(['failed' => 'Failed Update brand'])
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
             * Update brand Record
             */
            $brand_destroy = Brand::where('id', $id)->update([
                'deleted_by' => Auth::user()->id,
                'deleted_at' => date('Y-m-d H:i:s'),
            ]);

            /**
             * Validation Update brand Record
             */
            if ($brand_destroy) {
                DB::commit();
                session()->flash('success', 'brand Successfully Deleted');
            } else {
                /**
                 * Failed Store Record
                 */
                DB::rollBack();
                session()->flash('failed', 'Failed Delete brand');
            }
        } catch (Exception $e) {
            session()->flash('failed', $e->getMessage());
        }
    }
}
