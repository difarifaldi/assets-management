<?php

namespace App\Http\Controllers;

use App\Models\master\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AuthController extends Controller
{
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function login()
    {
        return view('auth.login');
    }

    public function forgot()
    {
        return view('auth.forgot.index');
    }

    public function authenticate(Request $request)
    {
        try {
            $email_or_username = $request->input('username');
            $field = filter_var($email_or_username, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';
            $request->merge([$field => $email_or_username]);

            $request->validate([
                $field => 'required',
                'password' => 'required',
            ]);

            $user = User::where($field, $email_or_username)->first();

            if (!is_null($user)) {
                if (Auth::attempt([$field => $email_or_username, 'password' => $request->password], isset($request->remember))) {
                    $request->session()->regenerate();
                    // For Request Url
                    $intended_url = session()->pull('url.intended', route('home'));
                    return redirect()->to($intended_url);
                } else {
                    return redirect()
                        ->back()
                        ->withErrors(['username' => 'These credentials do not match our records.'])
                        ->withInput();
                }
            } else {
                return redirect()
                    ->back()
                    ->withErrors(['username' => 'These credentials do not match our records.'])
                    ->withInput();
            }
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function confirmation(Request $request)
    {
        try {
            $request->validate([
                'email' => 'required',
            ]);

            $user = User::where('email', $request->email)->first();

            if (!is_null($user) && $user->roles[0]->name == 'staff') {
                $data['user'] = $user;
                return view('auth.forgot.password', $data);
            } else {
                return redirect()
                    ->back()
                    ->withErrors(['email' => 'Account not registered.'])
                    ->withInput();
            }
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function setPassword(Request $request, $id)
    {
        try {
            $request->validate([
                'password' => 'required',
                're_password' => 'required',
            ]);

            if ($request->password != $request->re_password) {
                return redirect()
                    ->route('forgot.index')
                    ->with(['failed' => 'Password Not Match']);
            }

            /**
             * Begin Transaction
             */
            DB::beginTransaction();

            /**
             * Update User Record
             */
            $user_update = User::where('id', $id)->update([
                'password' => bcrypt($request->password),
            ]);

            /**
             * Validation Update User Password
             */
            if ($user_update) {
                DB::commit();
                return redirect()
                    ->route('login')
                    ->with(['success' => 'Successfully Change Password']);
            } else {
                /**
                 * Failed Store Record
                 */
                DB::rollBack();
                return redirect()
                    ->route('forgot.index')
                    ->with(['failed' => 'Failed Change Password']);
            }
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }
}
