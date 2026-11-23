<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AuthController extends Controller
{
    /**
     * Show the admin login form.
     */
    public function showLoginForm(Request $request): View|RedirectResponse
    {
        if ($request->session()->get('is_admin')) {
            return redirect()->route('admin.dashboard');
        }

        return view('auth.login');
    }

    /**
     * Handle the admin login request.
     */
    public function login(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
        ], [
            'username.required' => 'Username admin wajib diisi.',
            'password.required' => 'Password admin wajib diisi.',
        ]);

        $adminUser = (string) config('admin.username');
        $adminPass = (string) config('admin.password');

        if (
            hash_equals($adminUser, $validated['username']) &&
            hash_equals($adminPass, $validated['password'])
        ) {
            $request->session()->regenerate();
            $request->session()->put('is_admin', true);
            $request->session()->put('admin_username', $validated['username']);

            return redirect()->intended(route('admin.dashboard'))
                ->with('success', 'Selamat datang di Admin Panel MainYuk!');
        }

        return back()
            ->withInput($request->only('username'))
            ->withErrors(['credentials' => 'Username atau password admin salah!']);
    }

    /**
     * Handle logout.
     */
    public function logout(Request $request): RedirectResponse
    {
        $request->session()->forget(['is_admin', 'admin_username']);
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Anda telah berhasil logout.');
    }
}
