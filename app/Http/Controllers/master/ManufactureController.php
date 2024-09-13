<?php

namespace App\Http\Controllers\master;

use App\Http\Controllers\Controller;
use App\Models\master\Manufacture;
use App\Models\master\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class ManufactureController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $datatable_route = route('master.manufacture.dataTable');


        $can_create = User::find(Auth::user()->id)->hasRole('admin');

        return view('master.manufacture.index', compact('datatable_route', 'can_create'));
    }

    public function dataTable()
    {
        /**
         * Get All Manufacture
         */
        $manufacture = Manufacture::whereNull('deleted_by')->whereNull('deleted_at')->get();

        /**
         * Datatable Configuration
         */
        $dataTable = DataTables::of($manufacture)
            ->addIndexColumn()
            ->addColumn('action', function ($data) {
                $btn_action = '<div align="center">';

                /**
                 * Validation Role Has Access Edit and Delete
                 */

                if (User::find(Auth::user()->id)->hasRole('admin')) {

                    $btn_action .= '<button class="btn btn-sm btn-warning " onclick="updateRecord(' . $data->id . ')" title="Edit">Edit</button>';
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
        return view('master.manufacture.create');
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
             * Create Manufacture Record
             */
            $manufacture = Manufacture::lockforUpdate()->create([
                'name' => $request->name,
                'address' => $request->address,
                'created_by' => Auth::user()->id,
                'updated_by' => Auth::user()->id,
            ]);

            /**
             * Validation Create Manufacture Record
             */
            if ($manufacture) {
                DB::commit();
                // return redirect()
                //     ->route('master.manufacture.index')
                //     ->with(['success' => 'Successfully Add Manufacture']);
                session()->flash('success', 'Successfully Add Manufacture');
                return response()->json(['data', $manufacture], 200);
            } else {
                /**
                 * Failed Store Record
                 */
                DB::rollBack();
                return redirect()
                    ->back()
                    ->with(['failed' => 'Failed Add Manufacture'])
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
            $manufacture = Manufacture::find($id); // Pastikan ini sesuai dengan model Anda

            if ($manufacture) {
                return response()->json($manufacture); // Mengembalikan data dalam bentuk JSON
            } else {
                return response()->json(['error' => 'Data not found'], 404); // Kembalikan error jika 
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

            $manufacture = Manufacture::find($id);

            if (!is_null($manufacture)) {
                /**
                 * Begin Transaction
                 */
                DB::beginTransaction();

                /**
                 * Update Manufacture Record
                 */
                $manufacture_update = Manufacture::where('id', $id)->update([
                    'name' => $request->name,
                    'address' => $request->address,
                    'updated_by' => Auth::user()->id,
                ]);

                /**
                 * Validation Update Manufacture Record
                 */
                if ($manufacture_update) {
                    DB::commit();
                    session()->flash('success', 'Successfully Edit Manufacture');
                    return response()->json(['data', $manufacture], 200);
                } else {
                    /**
                     * Failed Store Record  
                     */
                    DB::rollBack();
                    session()->flash('failed', 'Failed Add Manufacture');
                    return response()->json(['message', 'Failed'], 400);
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
             * Update Manufacture Record
             */
            $manufacture_destroy = Manufacture::where('id', $id)->update([
                'deleted_by' => Auth::user()->id,
                'deleted_at' => date('Y-m-d H:i:s'),
            ]);

            /**
             * Validation Update Manufacture Record
             */
            if ($manufacture_destroy) {
                DB::commit();
                session()->flash('success', 'Manufacture Successfully Deleted');
            } else {
                /**
                 * Failed Store Record
                 */
                DB::rollBack();
                session()->flash('failed', 'Failed Delete Manufacture');
            }
        } catch (Exception $e) {
            session()->flash('failed', $e->getMessage());
        }
    }
}
