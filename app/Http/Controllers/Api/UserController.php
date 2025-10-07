<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Position;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    /**
     * Get all users (admin only)
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        
        if ($user->access_level !== 'admin') {
            return response()->json([
                'status' => 'error',
                'message' => 'Only admin can view all users'
            ], 403);
        }

        $query = User::with('position');

        // Filter by access level
        if ($request->has('access_level') && $request->access_level) {
            $query->where('access_level', $request->access_level);
        }

        // Filter by position
        if ($request->has('position') && $request->position) {
            $query->where('position', $request->position);
        }

        // Search by name or email
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('username', 'like', "%{$search}%");
            });
        }

        $users = $query->paginate($request->get('per_page', 15));

        return response()->json([
            'status' => 'success',
            'data' => $users
        ]);
    }

    /**
     * Get specific user
     */
    public function show($id): JsonResponse
    {
        $user = User::with(['position', 'notifications', 'recentDevices'])->find($id);

        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'User not found'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $user
        ]);
    }

    /**
     * Create new user (admin only)
     */
    public function store(Request $request): JsonResponse
    {
        $user = $request->user();
        
        if ($user->access_level !== 'admin') {
            return response()->json([
                'status' => 'error',
                'message' => 'Only admin can create users'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'gelar' => 'nullable|string|max:255',
            'position' => 'nullable|integer|exists:positions,no',
            'access_level' => 'required|in:admin,operator,viewer',
            'phone_number' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:255',
            'state' => 'nullable|string|max:255',
            'zip_code' => 'nullable|string|max:10',
            'country' => 'nullable|string|max:255',
            'technician' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $newUser = User::create([
            'name' => $request->name,
            'gelar' => $request->gelar,
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'access_level' => $request->access_level,
            'position' => $request->position,
            'phone_number' => $request->phone_number,
            'address' => $request->address,
            'city' => $request->city,
            'state' => $request->state,
            'zip_code' => $request->zip_code,
            'country' => $request->country,
            'technician' => $request->boolean('technician', false),
            'joined' => now(),
        ]);

        $newUser->load('position');

        return response()->json([
            'status' => 'success',
            'message' => 'User created successfully',
            'data' => $newUser
        ], 201);
    }

    /**
     * Update user (admin only)
     */
    public function update(Request $request, $id): JsonResponse
    {
        $currentUser = $request->user();
        
        if ($currentUser->access_level !== 'admin') {
            return response()->json([
                'status' => 'error',
                'message' => 'Only admin can update users'
            ], 403);
        }

        $targetUser = User::find($id);

        if (!$targetUser) {
            return response()->json([
                'status' => 'error',
                'message' => 'User not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|string|max:255',
            'username' => 'sometimes|string|max:255|unique:users,username,' . $id,
            'email' => 'sometimes|string|email|max:255|unique:users,email,' . $id,
            'password' => 'sometimes|string|min:8',
            'gelar' => 'nullable|string|max:255',
            'position' => 'nullable|integer|exists:positions,no',
            'access_level' => 'sometimes|in:admin,operator,viewer',
            'phone_number' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:255',
            'state' => 'nullable|string|max:255',
            'zip_code' => 'nullable|string|max:10',
            'country' => 'nullable|string|max:255',
            'technician' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $updateData = $request->only([
            'name', 'gelar', 'username', 'email', 'access_level', 'position',
            'phone_number', 'address', 'city', 'state', 'zip_code', 'country', 'technician'
        ]);

        if ($request->has('password')) {
            $updateData['password'] = Hash::make($request->password);
        }

        $targetUser->update($updateData);
        $targetUser->load('position');

        return response()->json([
            'status' => 'success',
            'message' => 'User updated successfully',
            'data' => $targetUser
        ]);
    }

    /**
     * Delete user (admin only)
     */
    public function destroy(Request $request, $id): JsonResponse
    {
        $currentUser = $request->user();
        
        if ($currentUser->access_level !== 'admin') {
            return response()->json([
                'status' => 'error',
                'message' => 'Only admin can delete users'
            ], 403);
        }

        $targetUser = User::find($id);

        if (!$targetUser) {
            return response()->json([
                'status' => 'error',
                'message' => 'User not found'
            ], 404);
        }

        // Prevent deleting own account
        if ($currentUser->id === $targetUser->id) {
            return response()->json([
                'status' => 'error',
                'message' => 'Cannot delete your own account'
            ], 400);
        }

        // Check if user has logbooks
        $logbooksCount = $targetUser->hasMany(\App\Models\Logbook::class, 'created_by')->count();
        if ($logbooksCount > 0) {
            return response()->json([
                'status' => 'error',
                'message' => 'Cannot delete user that has created logbooks'
            ], 400);
        }

        $targetUser->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'User deleted successfully'
        ]);
    }

    /**
     * Get technicians list
     */
    public function technicians(): JsonResponse
    {
        $technicians = User::where('technician', true)
                          ->orWhere('access_level', 'admin')
                          ->with('position')
                          ->get();

        return response()->json([
            'status' => 'success',
            'data' => $technicians
        ]);
    }

    /**
     * Get positions list
     */
    public function positions(): JsonResponse
    {
        $positions = Position::all();

        return response()->json([
            'status' => 'success',
            'data' => $positions
        ]);
    }
}
