<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Position;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    public function index()
    {
        // Fetch all users with pagination
        $users = User::paginate(10);
		$positions = Position::all();
        return view('users.index', compact('users', 'positions'));
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'modalAddressFirstName' => 'required|string|max:255',
                'modalAddressLastName' => 'required|string|max:255',
                'modalGelar' => 'nullable|string|max:50',
                'modalUsername' => 'required|alpha_num|unique:users,name|max:255',
                'modalAddressEmail' => 'required|email|unique:users,email|max:255',
                'modalAddressCountry' => 'required|string|max:255',
                'inputGroupSelect01' => 'required|string|max:255',
                'modalAddressAddress1' => 'required|string|max:255',
                'modalAddressAddress2' => 'nullable|string|max:255',
                'modalPhoneNumber' => 'required|string|max:20',
                'modalAddressCity' => 'required|string|max:255',
                'modalAddressState' => 'required|string|max:255',
                'modalAddressZipCode' => 'required|string|max:10',
                'signature' => 'required|string',
                'customRadioIcon-01' => 'required|integer|in:0,1,2',
            ]);

            $user = User::create([
                'name' => strtolower($validated['modalUsername']),
                'fullname' => $validated['modalAddressFirstName'] . ' ' . $validated['modalAddressLastName'],
                'gelar' => $validated['modalGelar'] ?? '',
                'email' => $validated['modalAddressEmail'],
                'password' => Hash::make('defaultpassword'),
                'access_level' => $validated['customRadioIcon-01'],
                'profile_picture' => 'default.png',
                'position' => $validated['inputGroupSelect01'],
                'technician' => $request->has('technician') ? 1 : 0,
                'signature' => $validated['signature'],
                'country' => $validated['modalAddressCountry'],
                'phone_number' => $validated['modalPhoneNumber'],
                'address' => $validated['modalAddressAddress1'] . ' ' . ($validated['modalAddressAddress2'] ?? ''),
                'city' => $validated['modalAddressCity'],
                'state' => $validated['modalAddressState'],
                'zip_code' => $validated['modalAddressZipCode'],
                'joined' => now(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'User berhasil ditambahkan. Default password: "defaultpassword"'
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error: ' . $e->getMessage(),
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Gagal menambahkan user: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal menambahkan user: ' . $e->getMessage()
            ], 500);
        }
    }

    public function edit($id)
    {
        try {
            $user = User::findOrFail($id);
            
            // Split fullname into first and last name
            $nameParts = explode(' ', $user->fullname, 2);
            $firstName = $nameParts[0] ?? '';
            $lastName = $nameParts[1] ?? '';
            
            return response()->json([
                'success' => true,
                'user' => [
                    'id' => $user->id,
                    'first_name' => $firstName,
                    'last_name' => $lastName,
                    'gelar' => $user->gelar,
                    'username' => $user->name,
                    'email' => $user->email,
                    'country' => $user->country,
                    'position' => $user->position,
                    'address1' => $user->address,
                    'address2' => '',
                    'phone_number' => $user->phone_number,
                    'city' => $user->city,
                    'state' => $user->state,
                    'zip_code' => $user->zip_code,
                    'technician' => $user->technician,
                    'access_level' => $user->access_level,
                    'signature' => $user->signature,
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'User not found'
            ], 404);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $user = User::findOrFail($id);
            
            $validated = $request->validate([
                'editFirstName' => 'required|string|max:255',
                'editLastName' => 'required|string|max:255',
                'editGelar' => 'nullable|string|max:50',
                'editUsername' => 'required|alpha_num|unique:users,name,' . $id . ',id|max:255',
                'editEmail' => 'required|email|unique:users,email,' . $id . ',id|max:255',
                'editCountry' => 'required|string|max:255',
                'editPosition' => 'required|string|max:255',
                'editAddress1' => 'required|string|max:255',
                'editAddress2' => 'nullable|string|max:255',
                'editPhoneNumber' => 'required|string|max:20',
                'editCity' => 'required|string|max:255',
                'editState' => 'required|string|max:255',
                'editZipCode' => 'required|string|max:10',
                'editSignature' => 'required|string',
                'editRadioIcon-01' => 'required|integer|in:0,1,2',
            ]);

            $user->update([
                'name' => strtolower($validated['editUsername']),
                'fullname' => $validated['editFirstName'] . ' ' . $validated['editLastName'],
                'gelar' => $validated['editGelar'] ?? '',
                'email' => $validated['editEmail'],
                'access_level' => $validated['editRadioIcon-01'],
                'position' => $validated['editPosition'],
                'technician' => $request->has('editTechnician') ? 1 : 0,
                'signature' => $validated['editSignature'],
                'country' => $validated['editCountry'],
                'phone_number' => $validated['editPhoneNumber'],
                'address' => $validated['editAddress1'] . ' ' . ($validated['editAddress2'] ?? ''),
                'city' => $validated['editCity'],
                'state' => $validated['editState'],
                'zip_code' => $validated['editZipCode'],
            ]);

            return response()->json([
                'success' => true,
                'message' => 'User berhasil diperbarui'
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error: ' . $e->getMessage(),
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Gagal mengupdate user: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengupdate user: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            if (Auth::id() == $id) {
                return response()->json([
                    'success' => false,
                    'message' => 'You cannot delete yourself.'
                ], 403);
            }

            $user = User::findOrFail($id);
            $user->delete();

            return response()->json([
                'success' => true,
                'message' => 'User berhasil dihapus'
            ]);

        } catch (\Exception $e) {
            Log::error('Gagal menghapus user: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus user'
            ], 500);
        }
    }
}
