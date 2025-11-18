<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\Validator;
use App\Models\RecentDevice; // Pastikan model ini di-import

class AccountController extends Controller
{
    public function settings(Request $request)
    {
        $user = $request->user()->load('position');

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'data' => $user
            ]);
        }

        return view('account.settings', ['user' => $user]);
    }

    public function updateDetails(Request $request)
    {
        $user = $request->user();
        
        $validator = Validator::make($request->all(), [
            'fullname'     => ['required', 'string', 'max:255'],
            'email'        => ['required', 'email', 'max:255', 'unique:users,email,'.$user->id],
            'phone_number' => ['nullable', 'string', 'max:20'],
            'address'      => ['nullable', 'string', 'max:255'],
            'city'         => ['nullable', 'string', 'max:255'],
            'country'      => ['nullable', 'string', 'max:255'],
            'signature'    => ['nullable', 'string'],
        ]);

        if ($validator->fails()) {
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
            }
            return back()->withErrors($validator)->withInput();
        }

        $validated = $validator->validated();

        $user->name = $validated['fullname'];
        
        if ($request->has('signature') && !empty($validated['signature'])) {
            $user->signature = $validated['signature'];
        }

        $user->update([
            'fullname' => $validated['fullname'],
            'email' => $validated['email'],
            'phone_number' => $validated['phone_number'],
            'address' => $validated['address'],
            'city' => $validated['city'],
            'country' => $validated['country'],
        ]);
        
        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Profile updated successfully',
                'data' => $user->fresh()
            ]);
        }

        return back()->with('successMessage', 'Account details updated successfully!');
    }

    public function security(Request $request)
    {
        $user = $request->user();
        
        $recentDevices = RecentDevice::where('user_id', $user->id)
                            ->orderBy('last_login', 'desc')
                            ->take(5)
                            ->get();

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'data' => [
                    'last_password_change' => $user->updated_at,
                    'recent_devices' => $recentDevices
                ]
            ]);
        }

        return view('account.security', [
            'user' => $user,
            'recentDevices' => $recentDevices
        ]);
    }
    
    public function updatePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'current_password' => ['required', 'current_password'],
            'password' => [
                'required', 
                'confirmed',
                Password::min(8)->mixedCase()->numbers()->symbols()
            ],
        ]);

        if ($validator->fails()) {
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
            }
            return back()->withErrors($validator)->with('errorMessage', 'Gagal mengganti password.');
        }

        $request->user()->update([
            'password' => Hash::make($request->password),
        ]);

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Password updated successfully!']);
        }

        return back()->with('successMessage', 'Password updated successfully!');
    }
}