<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Position;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

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
		$validated = $request->validate([
			'modalAddressFirstName' => 'required|string',
			'modalAddressLastName' => 'required|string',
			'modalUsername' => 'required|alpha_num|unique:users,username',
			'modalAddressEmail' => 'required|email|unique:users,email',  // Ensure email is unique
			'modalAddressCountry' => 'required|string',
			'inputGroupSelect01' => 'required|string',
			'modalAddressAddress1' => 'required|string',
			'modalPhoneNumber' => 'required|string',
			'modalAddressCity' => 'required|string',
			'modalAddressState' => 'required|string',
			'modalAddressZipCode' => 'required|string',
			'signature' => 'required|string',
			'customRadioIcon-01' => 'required|integer',
		]);

		try {
			// Retrieve validated data
			$firstName = $request->input('modalAddressFirstName');
			$lastName = $request->input('modalAddressLastName');
			$username = strtolower($request->input('modalUsername'));
			$gelar = $request->input('modalGelar', '');
			$email = $request->input('modalAddressEmail');
			$country = $request->input('modalAddressCountry');
			$position = $request->input('inputGroupSelect01');
			$address1 = $request->input('modalAddressAddress1');
			$address2 = $request->input('modalAddressAddress2', '');
			$phoneNumber = $request->input('modalPhoneNumber');
			$city = $request->input('modalAddressCity');
			$state = $request->input('modalAddressState');
			$zipCode = $request->input('modalAddressZipCode');
			$technician = $request->has('technician') ? 1 : 0;
			$access_level = $request->input('customRadioIcon-01');
			$signature = $request->input('signature');
			$password = Hash::make('defaultpassword'); // Default password

			// Check if email already exists
			$existingUser = User::where('email', $email)->first();
			if ($existingUser) {
				return response()->json(['success' => false, 'message' => 'Email already exists. Please use a different email.']);
			}

			// Create the user
			$user = User::create([
				'name' => $firstName . ' ' . $lastName,
				'gelar' => $gelar,
				'email' => $email,
				'password' => $password,
				'access_level' => $access_level,
				'profile_picture' => 'default.png',
				'position' => $position,
				'technician' => $technician,
				'signature' => $signature,
				'country' => $country,
				'phone_number' => $phoneNumber,
				'address' => $address1 . ' ' . $address2,
				'city' => $city,
				'state' => $state,
				'zip_code' => $zipCode,
				'joined' => now(),
			]);

			// Success response
			$response = [
				'success' => true,
				'message' => 'User berhasil ditambahkan. Default password: "defaultpassword"',
			];
			session()->flash('successMessage', 'User added successfully.');

			return response()->json(['success' => true, 'message' => 'User added successfully']);
		} catch (\Exception $e) {
			Log::error('Gagal menambahkan user: ' . $e->getMessage());
			$response['message'] = 'Gagal menambahkan user';
		}

        // Validate the input
        $validator = Validator::make($request->all(), [
            'firstname' => 'required|string',
            'lastname' => 'required|string',
            'username' => 'required|string|unique:users,username',
            'email' => 'required|email|unique:users,email',
            'signature' => 'required|string',
            'access_level' => 'required|integer',
            'position' => 'required|string',
            'address' => 'required|string',
            'phone_number' => 'required|string',
            'country' => 'required|string',
            'city' => 'required|string',
            'state' => 'required|string',
            'zip_code' => 'required|string',
        ]);
    }

    public function edit($id)
    {
        // Get user details
        $user = User::findOrFail($id);
        return response()->json(['success' => true, 'user' => $user]);
    }

    public function update(Request $request, $id)
    {
        // Validate the input
        $validator = Validator::make($request->all(), [
            'firstname' => 'required|string',
            'lastname' => 'required|string',
            'username' => 'required|string|unique:users,username,' . $id,
            'email' => 'required|email|unique:users,email,' . $id,
            'signature' => 'required|string',
            'access_level' => 'required|integer',
            'position' => 'required|string',
            'address' => 'required|string',
            'phone_number' => 'required|string',
            'country' => 'required|string',
            'city' => 'required|string',
            'state' => 'required|string',
            'zip_code' => 'required|string',
        ]);

        // If validation fails
        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->errors()->first()]);
        }

        // Find user and update
        $user = User::findOrFail($id);
        $user->fullname = $request->firstname . ' ' . $request->lastname;
        $user->username = $request->username;
        $user->email = $request->email;
        $user->signature = $request->signature;
        $user->access_level = $request->access_level;
        $user->position = $request->position;
        $user->address = $request->address;
        $user->phone_number = $request->phone_number;
        $user->country = $request->country;
        $user->city = $request->city;
        $user->state = $request->state;
        $user->zip_code = $request->zip_code;
        $user->save();

        // Flash success message to the session
        session()->flash('successMessage', 'User updated successfully.');

        return response()->json(['success' => true, 'message' => 'User updated successfully']);
    }

    public function destroy($id)
    {
        if (Auth::id() == $id) {
            // Flash error message to session
            session()->flash('errorMessage', 'You cannot delete yourself.');

            return response()->json(['success' => false, 'message' => 'You cannot delete yourself.']);
        }

        // Find user and delete
        $user = User::findOrFail($id);
        $user->delete();

        // Flash success message to session
        session()->flash('successMessage', 'User deleted successfully.');

        return response()->json(['success' => true, 'message' => 'User deleted successfully']);
    }
}
