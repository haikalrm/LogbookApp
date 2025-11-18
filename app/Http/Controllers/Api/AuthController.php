<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\RecentDevice;
use Jenssegers\Agent\Agent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'login' => 'required|string',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'message' => 'Validation failed'], 422);
        }

        $throttleKey = 'login_api:' . $request->ip();
        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            return response()->json([
                'status' => 'error',
                'message' => 'Terlalu banyak percobaan. Coba lagi dalam ' . $seconds . ' detik.'
            ], 429);
        }

        $loginType = filter_var($request->login, FILTER_VALIDATE_EMAIL) ? 'email' : 'name';
        $user = User::where($loginType, $request->login)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            RateLimiter::hit($throttleKey, 60); 
            return response()->json(['status' => 'error', 'message' => 'Invalid credentials'], 401);
        }

        RateLimiter::clear($throttleKey);

        try {
            $agent = new Agent();
            
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
                ->where('ip_address', $request->ip())
                ->where('user_agent', $request->userAgent())
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
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                    'device_type' => $deviceInfo,
                    'os' => $osInfo,
                    'browser' => $browserInfo,
                    'country' => 'Indonesia',
                    'last_login' => now(),
                ]);
            }
        } catch (\Exception $e) {
            Log::error("Gagal simpan recent device di API: " . $e->getMessage());
        }

        $token = $user->createToken($request->device_name ?? 'Mobile App')->plainTextToken;

        return response()->json([
            'status' => 'success',
            'message' => 'Login successful',
            'data' => [
                'user' => $user->load('position'),
                'token' => $token
            ]
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['status' => 'success', 'message' => 'Logged out successfully']);
    }
}