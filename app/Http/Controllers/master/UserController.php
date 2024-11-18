<?php

namespace App\Http\Controllers\master;

use App\Http\Controllers\Controller;
use App\Models\asset\Asset;
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
            ->MenambahkanIndexColumn()
            ->MenambahkanColumn('role', function ($data) {
                /**
                 * User Role Configuration
                 */
                $exploded_raw_role = explode('-', $data->getRoleNames()[0]);
                $user_role = ucwords(implode(' ', $exploded_raw_role));
                return $user_role;
            })
            ->MenambahkanColumn('divisi', function ($data) {
                return $data->divisi ? $data->divisi->nama : '-';
            })
            ->MenambahkanColumn('aksi', function ($data) {
                $btn_action = '<div align="center">';
                $btn_action .= '<a href="' . route('master.user.show', ['id' => $data->id]) . '" class="btn btn-sm btn-primary" title="Detail">Detail</a>';
                $btn_action .= '<a href="' . route('master.user.edit', ['id' => $data->id]) . '" class="btn btn-sm btn-warning ml-2" title="Edit">Edit</a>';

                /**
                 * Validation User Logged In Equals with User Record id
                 */
                if (Auth::user()->id != $data->id) {
                    $btn_action .= '<button class="btn btn-sm btn-danger ml-2" onclick="destroyRecord(' . $data->id . ')" title="Hapus">Hapus</button>';
                }
                $btn_action .= '</div>';
                return $btn_action;
            })
            ->only(['nama', 'email', 'role', 'divisi', 'aksi'])
            ->rawColumns(['aksi'])
            ->make(true);

        return $dataTable;
    }

    public function create()
    {
        $roles = Role::all();
        $divisi = Division::whereNull('deleted_at')->get();
        return view('master.user.create', compact('roles', 'divisi'));
    }

    public function store(Request $request)
    {
        try {
            /**
             * Validation Request Body Variables
             */
            $request->validate([
                'username' => 'required',
                'nik' => 'numeric',
                'id_divisi' => 'required',
                'nama' => 'required|string',
                'email' => 'required|email',
                'noHP' => 'required',
                'alamat' => 'nullable',
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
                    'id_divisi' => $request->id_divisi,
                    'nama' => $request->nama,
                    'email' => $request->email,
                    'noHP' => $request->noHP,
                    'alamat' => $request->alamat,
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
                        ->with(['success' => 'Berhasil Menambahkan Pengguna']);
                } else {
                    /**
                     * Gagal Store Record
                     */
                    DB::rollBack();
                    return redirect()
                        ->back()
                        ->with(['failed' => 'Gagal Menambahkan Pengguna'])
                        ->withInput();
                }
            } else {
                return redirect()
                    ->back()
                    ->with(['failed' => 'Email Atau Password Sudah Tersedia'])
                    ->withInput();
            }
        } catch (Exception $e) {
            return redirect()
                ->back()
                ->with(['failed' => $e->getMessage()])
                ->withInput();
        }
    }

    public function show(string $id = null)
    {
        try {
            if (!is_null($id)) {
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

                    /**
                     * Get Asset Assigning and Check Out by User
                     */
                    $assets = Asset::where('ditugaskan_ke', $user->id)->orWhere('dipinjam_oleh', $user->id)->get();

                    return view('master.user.detail', compact('user', 'user_role', 'assets'));
                } else {
                    return redirect()
                        ->back()
                        ->with(['failed' => 'Invalid Request!']);
                }
            } else {
                /**
                 * Get User Record from current user
                 */
                $user = User::find(Auth::user()->id);

                /**
                 * User Role Configuration
                 */
                $exploded_raw_role = explode('-', $user->getRoleNames()[0]);
                $user_role = ucwords(implode(' ', $exploded_raw_role));


                /**
                 * Get Asset Assigning and Check Out by User
                 */
                $assets = Asset::where('ditugaskan_ke', $user->id)->orWhere('dipinjam_oleh', $user->id)->get();

                return view('master.user.detail', compact('user', 'user_role', 'assets'));
            }
        } catch (Exception $e) {
            return redirect()
                ->back()
                ->with(['failed' => $e->getMessage()]);
        }
    }

    public function edit(string $id = null)
    {
        try {
            if (!is_null($id)) {
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
                    $divisi = Division::whereNull('deleted_at')->get();

                    /**
                     * Disabled Edit Role with Same User Logged in
                     */
                    $role_disabled = $id == Auth::user()->id ? 'disabled' : '';

                    return view('master.user.edit', compact('user', 'roles', 'divisi', 'role_disabled'));
                } else {
                    return redirect()
                        ->back()
                        ->with(['failed' => 'Invalid Request!']);
                }
            } else {
                /**
                 * Get User Record from current user
                 */
                $user = User::find(Auth::user()->id);

                /**
                 * Get All Role
                 */
                $roles = Role::all();
                $divisi = Division::whereNull('deleted_at')->get();

                /**
                 * Disabled Edit Role with Same User Logged in
                 */
                $role_disabled = $id == Auth::user()->id ? 'disabled' : '';

                return view('master.user.edit', compact('user', 'roles', 'divisi', 'role_disabled'));
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
                'nama' => 'required|string',
                'email' => 'required|email',
                'username' => 'required',
                'roles' => 'required',
                'nik' => 'numeric',
                'id_divisi' => 'required',
                'noHP' => 'required',
                'alamat' => 'nullable',
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

                        if ($request->password != $request->re_password) {
                            return redirect()
                                ->back()
                                ->with(['failed' => 'Password Tidak Sesuai'])
                                ->withInput();
                        }

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
                            'id_divisi' => $request->id_divisi,
                            'nama' => $request->nama,
                            'email' => $request->email,
                            'noHP' => $request->noHP,
                            'alamat' => $request->alamat,
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
                            'id_divisi' => $request->id_divisi,
                            'nama' => $request->nama,
                            'email' => $request->email,
                            'noHP' => $request->noHP,
                            'alamat' => $request->alamat,
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
                                ->with(['success' => 'Berhasil Mengubah Data Pengguna']);
                        } else {
                            /**
                             * Gagal Store Record
                             */
                            DB::rollBack();
                            return redirect()
                                ->back()
                                ->with(['failed' => 'Gagal Mengubah Data Pengguna'])
                                ->withInput();
                        }
                    } else {
                        /**
                         * Validation Update User Record
                         */
                        if ($user_update) {
                            DB::commit();
                            if (User::find(Auth::user()->id)->hasRole('admin')) {
                                return redirect()
                                    ->route('master.user.index')
                                    ->with(['success' => 'Berhasil Mengubah Data Pengguna']);
                            } else {
                                return redirect()
                                    ->route('my-account.show')
                                    ->with(['success' => 'Berhasil Data Pengguna']);
                            }
                        } else {
                            /**
                             * Gagal Store Record
                             */
                            DB::rollBack();
                            return redirect()
                                ->back()
                                ->with(['failed' => 'Gagal Mengubah Data Pengguna'])
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
                    ->with(['failed' => 'Email Atau Password Sudah Tersedia'])
                    ->withInput();
            }
        } catch (Exception $e) {
            return redirect()
                ->back()
                ->with(['failed' => $e->getMessage()]);
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
                session()->flash('success', 'User Berhasil Dihapus');
            } else {
                /**
                 * Gagal Store Record
                 */
                DB::rollBack();
                session()->flash('failed', 'Gagal Hapus Pengguna');
            }
        } catch (Exception $e) {
            session()->flash('failed', $e->getMessage());
        }
    }
}
