<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class IndexController extends Controller
{
    //

    public function index()
    {
        // if not login, redirect to login
        if (!Auth::guard('web')->check()) {
            return view('login');
        } else {

            // $module =
            $modules = $this->http->get('modules');

            $response = $modules->json();
            if ($modules->successful()) {
                return view('index', ['years' => $response['data']]);
            } else {
                return view('error', ['response' => $response]);
            }
        }
    }

    public function login(Request $request)
    {
        // login

        // attempt to login
        if (Auth::guard('web')->attempt($request->only(['email', 'password']), $request->has('remember'))) {
            // if success, redirect to home
            return redirect()->intended('/');
        } else {
            // if fail, redirect to login with error message
            return redirect()->back()->withErrors(['message' => '用户名或密码错误'])->withInput();
        }
    }

    public function logout()
    {
        // logout
        Auth::guard('web')->logout();
        return redirect()->route('login');
    }
}
