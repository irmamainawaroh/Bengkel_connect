<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;

class AuthController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | LOGIN
    |--------------------------------------------------------------------------
    */

    public function login(Request $request)
    {
        $data = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $data['email'])->first();

        if (!$user || !Hash::check($data['password'], $user->password)) {
            return back()->with('error', 'Email atau password salah');
        }

        Session::put('id_user', $user->id);
        Session::put('nama', $user->name);
        Session::put('role', $user->role ?? 'customer');

        if ($user->role === 'admin') {
            return redirect('/admin/dashboard');
        }

        if ($user->role === 'mekanik') {
            return redirect('/mekanik/dashboard');
        }

        return redirect('/customer/dashboard');
    }


    /*
    |--------------------------------------------------------------------------
    | REGISTER
    |--------------------------------------------------------------------------
    */

    public function register(Request $request)
    {
        $data = $request->validate([
            'nama' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'no_hp' => 'required|string|max:20',
            'plat_nomor' => 'required|string|max:20',
        ]);

        User::create([
            'name' => $data['nama'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'no_hp' => $data['no_hp'],
            'plat_nomor' => $data['plat_nomor'],
            'role' => 'customer',
        ]);

        return redirect('/login')
            ->with('success', 'Registrasi berhasil');
    }

    /*
    |--------------------------------------------------------------------------
    | LOGOUT
    |--------------------------------------------------------------------------
    */

    public function logout()
    {
        Session::flush();

        return redirect('/login');
    }
}

