<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\ValidationException;

class AuthenticatedSessionController extends Controller
{
    public function create()
    {
        return view('auth.login');
    }

    public function store(Request $request)
    {
        // Validasi email, password, dan hCaptcha response
        $request->validate([
            'email'               => 'required|string|email',
            'password'            => 'required|string',
            // 'h-captcha-response'  => 'required', // ganti g-recaptcha-response dengan h-captcha-response, comment this line for now
        ]);

        // Kirimkan permintaan verifikasi ke hCaptcha (commented out)
        /*
        $response = Http::asForm()->post('https://hcaptcha.com/siteverify', [
            'secret'   => env('HCAPTCHA_SECRET_KEY'),  // Gunakan HCAPTCHA_SECRET_KEY yang benar di .env
            'response' => $request->input('h-captcha-response'),
            'remoteip' => $request->ip(),
        ]);

        // Debugging: Melihat respon dari hCaptcha
        \Log::info('hCaptcha Response:', $response->json());

        // Cek apakah hCaptcha berhasil diverifikasi
        if (!$response->json('success')) {
            throw ValidationException::withMessages([
                'captcha' => __('HCaptcha not valid, try again.'),
            ]);
        }
        */

        // Verifikasi kredensial login pengguna
        if (!Auth::attempt($request->only('email', 'password'), $request->boolean('remember'))) {
            throw ValidationException::withMessages([
                'email' => __('Email or password invalid.'),
            ]);
        }

        // Regenerasi session untuk login yang lebih aman
        $request->session()->regenerate();
        return redirect()->intended(RouteServiceProvider::HOME);
    }

    public function destroy(Request $request)
    {
        // Logout dan hapus session
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
