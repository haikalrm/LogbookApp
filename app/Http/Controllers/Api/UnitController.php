<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class UnitController extends Controller
{
    /**
     * Get all units
     */
    public function index(): JsonResponse
    {
        $units = Unit::with('logbooks')->get();

        return response()->json([
            'status' => 'success',
            'data' => $units
        ]);
    }

    /**
     * Get specific unit
     */
    public function show($id): JsonResponse
    {
        $unit = Unit::with(['logbooks.createdBy', 'logbooks.approvedBy'])->find($id);

        if (!$unit) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unit not found'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $unit
        ]);
    }

    /**
     * Create new unit (admin only)
     */
    public function store(Request $request): JsonResponse
    {
        $user = $request->user();
        
        if ($user->access_level !== 'admin') {
            return response()->json([
                'status' => 'error',
                'message' => 'Only admin can create units'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'nama' => 'required|string|max:255|unique:units,nama'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $unit = Unit::create([
            'nama' => $request->nama
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Unit created successfully',
            'data' => $unit
        ], 201);
    }

    /**
     * Update unit (admin only)
     */
    public function update(Request $request, $id): JsonResponse
    {
        $user = $request->user();
        
        if ($user->access_level !== 'admin') {
            return response()->json([
                'status' => 'error',
                'message' => 'Only admin can update units'
            ], 403);
        }

        $unit = Unit::find($id);

        if (!$unit) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unit not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'nama' => 'required|string|max:255|unique:units,nama,' . $id
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $unit->update([
            'nama' => $request->nama
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Unit updated successfully',
            'data' => $unit
        ]);
    }

    /**
     * Delete unit (admin only)
     */
    public function destroy(Request $request, $id): JsonResponse
    {
        $user = $request->user();
        
        if ($user->access_level !== 'admin') {
            return response()->json([
                'status' => 'error',
                'message' => 'Only admin can delete units'
            ], 403);
        }

        $unit = Unit::find($id);

        if (!$unit) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unit not found'
            ], 404);
        }

        // Check if unit has logbooks
        if ($unit->logbooks()->count() > 0) {
            return response()->json([
                'status' => 'error',
                'message' => 'Cannot delete unit that has logbooks'
            ], 400);
        }

        $unit->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Unit deleted successfully'
        ]);
    }
}
