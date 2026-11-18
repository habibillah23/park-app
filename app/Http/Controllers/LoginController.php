<?php

namespace App\Http\Controllers\Auth;

use App\Helpers\ActivityLogger;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\View\View;

class LoginController extends Controller
{
    public function showLoginForm(): View
    {
        return view('auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            return back()->withErrors([
                'email' => 'Email atau password salah.',
            ])->onlyInput('email');
        }

        $request->session()->regenerate();

        ActivityLogger::log('Login ke sistem');

        // Jika sebelumnya mengakses halaman tertentu (misalnya form booking),
        // kembali ke sana. Jika tidak, arahkan sesuai role.
        return redirect()->intended($this->homeFor(Auth::user()->role));
    }

    public function logout(Request $request): RedirectResponse
    {
        ActivityLogger::log('Logout dari sistem');

        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    private function homeFor(string $role): string
    {
        if ($role === 'pelanggan' && Route::has('booking.index')) {
            return route('booking.index');
        }

        if ($role === 'pelanggan') {
            return route('login');
        }

        return route('dashboard');
    }
}
