<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Pengajar;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    /**
     * Show login form
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Handle login request
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials, $request->filled('remember'))) {
            $request->session()->regenerate();

            /** @var User $user */
            $user = Auth::user();

            // Redirect based on role
            if ($user->isAdmin()) {
                return redirect()->intended('/admin/dashboard');
            } elseif ($user->isPengajar()) {
                return redirect()->intended('/pengajar/dashboard');
            } else {
                return redirect()->intended('/pelajar/dashboard');
            }
        }

        return back()->withErrors([
            'email' => 'Email atau password salah.',
        ])->onlyInput('email');
    }

    /**
     * Show registration form
     */
    public function showRegisterForm()
    {
        return view('auth.register');
    }

    /**
     * Handle registration request
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:pengajar,pelajar',
            'umur' => 'nullable|integer|min:10|max:100',
            'alamat' => 'required|string',
            'no_telepon' => 'required|string|max:20',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // Create user
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'umur' => $request->umur,
            'alamat' => $request->alamat,
            'no_telepon' => $request->no_telepon,
        ]);

        // If role is pengajar, create pengajar profile
        if ($request->role === 'pengajar') {
            Pengajar::create([
                'user_id' => $user->id,
                'mata_pelajaran' => $request->mata_pelajaran ?? '',
                'pendidikan_terakhir' => $request->pendidikan_terakhir ?? '',
                'pengalaman_tahun' => $request->pengalaman_tahun ?? 0,
                'deskripsi' => $request->deskripsi ?? '',
                'tarif_per_jam' => $request->tarif_per_jam ?? 0,
            ]);
        }

        // Auto login after registration
        Auth::login($user);

        // Redirect based on role
        if ($user->isPengajar()) {
            return redirect('/pengajar/dashboard')->with('success', 'Registrasi berhasil! Silakan lengkapi profil Anda.');
        } else {
            return redirect('/pelajar/dashboard')->with('success', 'Registrasi berhasil!');
        }
    }

    /**
     * Handle logout request
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')->with('success', 'Anda telah logout.');
    }
}
