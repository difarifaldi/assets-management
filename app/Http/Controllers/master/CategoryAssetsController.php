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
            ->addColumn('type', function ($data) {
                return $data->type == 1 ? 'Physical Asset' : ($data->type == 2 ? 'License Asset' : '-');
            })
            ->addColumn('action', function ($data) {
                $btn_action = '<div align="center">';

                /**
                 * Validation Role Has Access Edit and Delete
                 */

                if (User::find(Auth::user()->id)->hasRole('admin')) {

                    $btn_action .= '<a href="' . route('master.category.edit', ['id' => $data->id]) . '" class="btn btn-sm btn-warning ml-2" title="Edit">Edit</a>';
                    $btn_action .= '<button class="btn btn-sm btn-danger ml-2" onclick="destroyRecord(' . $data->id . ')" title="Delete">Delete</button>';
                }
                $btn_action .= '</div>';
                return $btn_action;
            })
            ->only(['name', 'type', 'action'])
            ->rawColumns(['action'])
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
                'name' => 'required|string',
                'type' => 'required',
            ]);

            DB::beginTransaction();

            /**
             * Create CategoryAssets Record
             */
            $category = CategoryAssets::lockforUpdate()->create([
                'name' => $request->name,
                'type' => $request->type,
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
                    ->with(['success' => 'Successfully Add Category Asset']);
            } else {
                /**
                 * Failed Store Record
                 */
                DB::rollBack();
                return redirect()
                    ->back()
                    ->with(['failed' => 'Failed Add Category Asset'])
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
                'type' => 'required',

            ]);

            $category = CategoryAssets::find($id);

            if (!is_null($category)) {
                /**
                 * Begin Transaction
                 */
                DB::beginTransaction();

                /**
                 * Update CategoryAssets Record
                 */
                $category_update = CategoryAssets::where('id', $id)->update([
                    'name' => $request->name,
                    'type' => $request->type,
                    'updated_by' => Auth::user()->id,
                ]);

                /**
                 * Validation Update CategoryAssets Record
                 */
                if ($category_update) {
                    DB::commit();
                    return redirect()
                        ->route('master.category.index')
                        ->with(['success' => 'Successfully Update Category Asset']);
                } else {
                    /**
                     * Failed Store Record
                     */
                    DB::rollBack();
                    return redirect()
                        ->back()
                        ->with(['failed' => 'Failed Update Category Asset'])
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
             * Update CategoryAssets Record
             */
            $category_destroy = CategoryAssets::where('id', $id)->update([
                'deleted_by' => Auth::user()->id,
                'deleted_at' => date('Y-m-d H:i:s'),
            ]);

            /**
             * Validation Update CategoryAssets Record
             */
            if ($category_destroy) {
                DB::commit();
                session()->flash('success', 'Category Asset Successfully Deleted');
            } else {
                /**
                 * Failed Store Record
                 */
                DB::rollBack();
                session()->flash('failed', 'Failed Delete Category Asset');
            }
        } catch (Exception $e) {
            session()->flash('failed', $e->getMessage());
        }
    }
}
