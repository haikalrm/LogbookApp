<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\RecentDevice;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;
use Jenssegers\Agent\Agent;
use Illuminate\Support\Facades\Log;

class AuthenticatedSessionController extends Controller
{
    public function create()
    {
        return view('auth.login');
    }

    public function store(LoginRequest $request): RedirectResponse
    {
        $throttleKey = 'login_web:' . $request->ip();

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            
            throw ValidationException::withMessages([
                'login' => trans('auth.throttle', [
                    'seconds' => $seconds,
                    'minutes' => ceil($seconds / 60),
                ]),
            ]);
        }

        $loginField = $request->input('login') ?? $request->input('email');
        $loginType = filter_var($loginField, FILTER_VALIDATE_EMAIL) ? 'email' : 'name';

        $credentials = [
            $loginType => $loginField,
            'password' => $request->input('password')
        ];

        if (!Auth::attempt($credentials, $request->boolean('remember'))) {
            RateLimiter::hit($throttleKey);

            throw ValidationException::withMessages([
                'login' => trans('auth.failed'),
            ]);
        }

        RateLimiter::clear($throttleKey);
        $request->session()->regenerate();

        try {
            $user = Auth::user();
            $agent = new Agent();
            
            $ip = $request->ip();
            $userAgent = $request->userAgent();
            
            $platform = $agent->platform();
            $platformVer = $agent->version($platform);
            $osInfo = $platform ? ($platform . ' ' . $platformVer) : 'Unknown OS';

            $deviceModel = $agent->device(); 
            $deviceType = $agent->isDesktop() ? 'Desktop' : ($agent->isPhone() ? 'Phone' : 'Tablet');
            $deviceInfo = $deviceModel ? ($deviceModel . ' (' . $deviceType . ')') : $deviceType;

            $browser = $agent->browser();
            $browserVer = $agent->version($browser);
            $browserInfo = $browser ? ($browser . ' ' . $browserVer) : 'Unknown Browser';

            $existingDevice = RecentDevice::where('user_id', $user->id)
                ->where('ip_address', $ip)
                ->where('user_agent', $userAgent)
                ->first();

            if ($existingDevice) {
                $existingDevice->update([
                    'last_login' => now(),
                    'device_type' => $deviceInfo,
                    'os' => $osInfo,
                    'browser' => $browserInfo,
                ]);
            } else {
                RecentDevice::create([
                    'user_id' => $user->id,
                    'ip_address' => $ip,
                    'user_agent' => $userAgent,
                    'device_type' => $deviceInfo,
                    'os' => $osInfo,
                    'browser' => $browserInfo,
                    'country' => 'Indonesia',
                    'last_login' => now(),
                ]);
            }
        } catch (\Exception $e) {
            Log::error($e->getMessage());
        }

        return redirect()->intended(RouteServiceProvider::HOME);
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}