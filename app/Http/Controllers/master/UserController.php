<?php

namespace App\Http\Controllers\master;

use App\Http\Controllers\Controller;
use App\Models\master\Division;
use App\Models\master\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Yajra\DataTables\Facades\DataTables;

class UserController extends Controller
{
    public function index()
    {
        /**
         * Datatable Route
         */
        $datatable_route = route('master.user.dataTable');
        return view('master.user.index', compact('datatable_route'));
    }

    public function dataTable()
    {
        /**
         * Get All User
         */
        $users = User::whereNull('deleted_at')->get();

        /**
         * Datatable Configuration
         */
        $dataTable = DataTables::of($users)
            ->addIndexColumn()
            ->addColumn('role', function ($data) {
                /**
                 * User Role Configuration
                 */
                $exploded_raw_role = explode('-', $data->getRoleNames()[0]);
                $user_role = ucwords(implode(' ', $exploded_raw_role));
                return $user_role;
            })
            ->addColumn('division', function ($data) {

                return $data->division ? $data->division->name : '-';
            })
            ->addColumn('action', function ($data) {
                $btn_action = '<div align="center">';
                $btn_action .= '<a href="' . route('master.user.show', ['id' => $data->id]) . '" class="btn btn-sm btn-primary" title="Detail">Detail</a>';
                $btn_action .= '<a href="' . route('master.user.edit', ['id' => $data->id]) . '" class="btn btn-sm btn-warning ml-2" title="Edit">Edit</a>';

                /**
                 * Validation User Logged In Equals with User Record id
                 */
                if (Auth::user()->id != $data->id) {
                    $btn_action .= '<button class="btn btn-sm btn-danger ml-2" onclick="destroyRecord(' . $data->id . ')" title="Delete">Delete</button>';
                }
                $btn_action .= '</div>';
                return $btn_action;
            })
            ->only(['name', 'email', 'role', 'division', 'action'])
            ->rawColumns(['action'])
            ->make(true);

        return $dataTable;
    }

    public function create()
    {
        $roles = Role::all();
        $division = Division::whereNull('deleted_at')->get();
        return view('master.user.create', compact('roles', 'division'));
    }

    public function store(Request $request)
    {
        try {
            /**
             * Validation Request Body Variables
             */
            $request->validate([
                'username' => 'required',
                'nik' => 'numeric|digits:16',
                'division_id' => 'required',
                'name' => 'required|string',
                'email' => 'required|email',
                'phone' => 'required|numeric',
                'address' => 'nullable',
                'roles' => 'required',
                'password' => 'required',
                're_password' => 'required|same:password',
            ]);

            /**
             * Validation Unique Field Record
             */
            $username_check = User::whereNull('deleted_at')
                ->where('username', $request->username)
                ->first();
            $email_check = User::whereNull('deleted_at')
                ->where('email', $request->email)
                ->first();

            /**
             * Validation Unique Field Record
             */
            if (is_null($username_check) && is_null($email_check)) {
                /**
                 * Begin Transaction
                 */
                DB::beginTransaction();

                /**
                 * Create User Record
                 */
                $user = User::lockforUpdate()->create([
                    'username' => $request->username,
                    'nik' => $request->nik,
                    'division_id' => $request->division_id,
                    'name' => $request->name,
                    'email' => $request->email,
                    'phone' => $request->phone,
                    'address' => $request->address,
                    'password' => bcrypt($request->password),
                ]);

                /**
                 * Assign Role of User Based on Requested
                 */
                $model_has_role = $user->assignRole($request->roles);

                /**
                 * Validation Create User Record and Assign Role User
                 */
                if ($user && $model_has_role) {
                    DB::commit();
                    return redirect()
                        ->route('master.user.index')
                        ->with(['success' => 'Successfully Add User']);
                } else {
                    /**
                     * Failed Store Record
                     */
                    DB::rollBack();
                    return redirect()
                        ->back()
                        ->with(['failed' => 'Failed Add User'])
                        ->withInput();
                }
            } else {
                return redirect()
                    ->back()
                    ->with(['failed' => 'Email or Username Already Exist'])
                    ->withInput();
            }
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
            $user = User::find($id);

            /**
             * Validation User id
             */
            if (!is_null($user)) {
                /**
                 * User Role Configuration
                 */
                $exploded_raw_role = explode('-', $user->getRoleNames()[0]);
                $user_role = ucwords(implode(' ', $exploded_raw_role));

                return view('master.user.detail', compact('user', 'user_role'));
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
            /**
             * Get User Record from id
             */
            $user = User::find($id);

            /**
             * Validation User id
             */
            if (!is_null($user)) {
                /**
                 * Get All Role
                 */
                $roles = Role::all();
                $division = Division::whereNull('deleted_at')->get();

                /**
                 * Disabled Edit Role with Same User Logged in
                 */
                $role_disabled = $id == Auth::user()->id ? 'disabled' : '';

                return view('master.user.edit', compact('user', 'roles', 'division', 'role_disabled'));
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
                'name' => 'required|string',
                'email' => 'required|email',
                'username' => 'required',
                'roles' => 'required',
                'username' => 'required',
                'nik' => 'numeric|digits:16',
                'division_id' => 'required',
                'name' => 'required|string',
                'email' => 'required|email',
                'phone' => 'required|numeric|min_digits:11',
                'address' => 'nullable',
                'roles' => 'required',
            ]);

            /**
             * Validation Unique Field Record
             */
            $username_check = User::whereNull('deleted_at')
                ->where('username', $request->username)
                ->where('id', '!=', $id)
                ->first();
            $email_check = User::whereNull('deleted_at')
                ->where('email', $request->email)
                ->where('id', '!=', $id)
                ->first();

            /**
             * Validation Unique Field Record
             */
            if (is_null($username_check) && is_null($email_check)) {
                /**
                 * Get User Record from id
                 */
                $user = User::find($id);

                /**
                 * Validation User id
                 */
                if (!is_null($user)) {
                    /**
                     * Validation Password Request
                     */
                    if (isset($request->password)) {
                        /**
                         * Validation Request Body Variables
                         */
                        $request->validate([
                            'password' => 'required',
                            're_password' => 'required|same:password',
                        ]);

                        /**
                         * Begin Transaction
                         */
                        DB::beginTransaction();

                        /**
                         * Update User Record
                         */
                        $user_update = User::where('id', $id)->update([
                            'username' => $request->username,
                            'nik' => $request->nik,
                            'division_id' => $request->division_id,
                            'name' => $request->name,
                            'email' => $request->email,
                            'phone' => $request->phone,
                            'address' => $request->address,
                            'password' => bcrypt($request->password),
                        ]);
                    } else {
                        /**
                         * Begin Transaction
                         */
                        DB::beginTransaction();

                        /**
                         * Update User Record
                         */
                        $user_update = User::where('id', $id)->update([
                            'username' => $request->username,
                            'nik' => $request->nik,
                            'division_id' => $request->division_id,
                            'name' => $request->name,
                            'email' => $request->email,
                            'phone' => $request->phone,
                            'address' => $request->address,
                        ]);
                    }

                    /**
                     * Validation Update Role Equals Default
                     */
                    if ($user->getRoleNames()[0] != $request->roles) {
                        /**
                         * Assign Role of User Based on Requested
                         */
                        $model_has_role_delete = $user->removeRole($user->getRoleNames()[0]);

                        /**
                         * Assign Role of User Based on Requested
                         */
                        $model_has_role_update = $user->assignRole($request->roles);

                        /**
                         * Validation Update User Record and Update Assign Role User
                         */
                        if ($user_update && $model_has_role_delete && $model_has_role_update) {
                            DB::commit();
                            return redirect()
                                ->route('master.user.index')
                                ->with(['success' => 'Successfully Update User']);
                        } else {
                            /**
                             * Failed Store Record
                             */
                            DB::rollBack();
                            return redirect()
                                ->back()
                                ->with(['failed' => 'Failed Update User'])
                                ->withInput();
                        }
                    } else {
                        /**
                         * Validation Update User Record
                         */
                        if ($user_update) {
                            DB::commit();
                            return redirect()
                                ->route('master.user.index')
                                ->with(['success' => 'Successfully Update User']);
                        } else {
                            /**
                             * Failed Store Record
                             */
                            DB::rollBack();
                            return redirect()
                                ->back()
                                ->with(['failed' => 'Failed Update User'])
                                ->withInput();
                        }
                    }
                } else {
                    return redirect()
                        ->back()
                        ->with(['failed' => 'Invalid Request!']);
                }
            } else {
                return redirect()
                    ->back()
                    ->with(['failed' => 'Email or Username Already Exist'])
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
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            /**
             * Begin Transaction
             */
            DB::beginTransaction();

            /**
             * Update User Record
             */
            $user_destroy = User::where('id', $id)->update([
                'deleted_at' => date('Y-m-d H:i:s'),
            ]);

            /**
             * Validation Update User Record
             */
            if ($user_destroy) {
                DB::commit();
                session()->flash('success', 'User Successfully Deleted');
            } else {
                /**
                 * Failed Store Record
                 */
                DB::rollBack();
                session()->flash('failed', 'Failed Delete User');
            }
        } catch (Exception $e) {
            session()->flash('failed', $e->getMessage());
        }
    }
}
