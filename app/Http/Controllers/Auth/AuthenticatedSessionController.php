<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Str;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        try {
            $request->authenticate();
        } catch (ValidationException $e) {
            $messages = $e->errors();
            $emailErrors = $messages['email'] ?? [];
            $combined = implode(' ', $emailErrors);

            // Jika pesan throttle, arahkan ke halaman login dengan flash message agar tampil elegan
            if ($combined && (Str::contains(Str::lower($combined), 'terlalu banyak percobaan') || Str::contains($combined, trans('auth.throttle')))) {
                return redirect()->route('login')
                    ->with('rate_limit_blocked', true)
                    ->with('error', $emailErrors[0] ?? 'Terlalu banyak percobaan login. Silakan coba lagi nanti.');
            }

            // Untuk error lain, tetap lempar agar ditangani default
            throw $e;
        }

        $request->session()->regenerate();

        return redirect()->intended(route('dashboard', absolute: false));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
