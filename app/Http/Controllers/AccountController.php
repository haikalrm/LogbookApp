<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\Validator;

class AccountController extends Controller
{
    /**
     * Menampilkan halaman utama pengaturan akun (tab Account).
     */
    public function settings(Request $request)
    {
        return view('account.settings', [
            'user' => $request->user()
        ]);
    }

    /**
     * Menampilkan halaman pengaturan keamanan (tab Security).
     */
    public function security(Request $request)
    {
        $user = $request->user();
        $recentDevices = $user->recentDevices()->take(5)->get();

        return view('account.security', [
            'user' => $user,
            'recentDevices' => $recentDevices
        ]);
    }

    /**
     * Menampilkan halaman pengaturan notifikasi (tab Notifications).
     */
    public function notifications(Request $request)
    {
        $user = $request->user();
        $notifications = $user->notifications()->paginate(15);

        return view('account.notifications', [
            'user' => $user,
            'notifications' => $notifications
        ]);
    }

    /**
     * Meng-handle update data profil dari tab Account.
     */
    public function updateDetails(Request $request)
    {
        $user = $request->user();
        
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone_number' => ['nullable', 'string', 'max:20'],
            'address' => ['nullable', 'string', 'max:255'],
            'city' => ['nullable', 'string', 'max:255'],
            'state' => ['nullable', 'string', 'max:255'],
            'zip_code' => ['nullable', 'string', 'max:10'],
            'country' => ['nullable', 'string', 'max:255'],
            'profile_picture' => ['nullable', 'image', 'max:1024'], // max 1MB
        ]);

        if ($request->hasFile('profile_picture')) {
			$extension = $request->file('profile_picture')->getClientOriginalExtension();
			$fileName = uniqid() . '.' . $extension;
			$path = $request->file('profile_picture')->move(public_path('assets/img/profile'), $fileName);
			$validated['profile_picture'] = 'assets/img/profile/' . $fileName;
        }

        $user->update($validated);
        
        session()->flash('successMessage', 'Account details updated successfully!');
		return back();
    }
	
	public function updatePassword(Request $request)
	{
		// Validasi manual menggunakan Validator
		$validator = Validator::make($request->all(), [
			'current_password' => ['required', 'current_password'],
			'password' => [
				'required',
				Password::min(8)->mixedCase()->numbers()->symbols(),  // Pastikan password memenuhi syarat
				'confirmed'
			],
		]);

		// Cek apakah validasi gagal
		if ($validator->fails()) {
			session()->flash('errorMessages', $validator->errors()->all());
			return back()->withErrors($validator)->withInput();  // Kembali dengan pesan kesalahan
		}

		// Cek apakah password lama sesuai dengan yang ada di database
		if (!Hash::check($request->input('current_password'), $request->user()->password)) {
			session()->flash('errorMessage', 'Current password is incorrect.');
			return back();
		}

		// Update password baru
		$request->user()->update([
			'password' => Hash::make($request->input('password')),
		]);

		// Set pesan sukses jika password berhasil diupdate
		session()->flash('successMessage', 'Password updated successfully!');

		return back();
	}

	// Fungsi untuk memeriksa kekuatan password
	private function isPasswordStrongEnough($password)
	{
		return preg_match('/[A-Z]/', $password) &&  // Check for at least one uppercase letter
			   preg_match('/[a-z]/', $password) &&  // Check for at least one lowercase letter
			   preg_match('/\d/', $password) &&    // Check for at least one number
			   preg_match('/[\W_]/', $password);   // Check for at least one symbol
	}
}