<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Logbook;
use App\Models\LogbookItem;
use App\Models\LogbookTeknisi;
use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class LogbookController extends Controller
{
    /**
     * Get all logbooks with pagination and filters
     */
    public function index(Request $request): JsonResponse
    {
        $query = Logbook::with(['unit', 'createdBy', 'approvedBy', 'signedBy', 'items', 'teknisi']);

        // Filter by unit
        if ($request->has('unit_id') && $request->unit_id) {
            $query->where('unit_id', $request->unit_id);
        }

        // Filter by date range
        if ($request->has('start_date') && $request->start_date) {
            $query->whereDate('date', '>=', $request->start_date);
        }
        
        if ($request->has('end_date') && $request->end_date) {
            $query->whereDate('date', '<=', $request->end_date);
        }

        // Filter by shift
        if ($request->has('shift') && $request->shift) {
            $query->where('shift', $request->shift);
        }

        // Filter by approval status
        if ($request->has('is_approved') && $request->is_approved !== null) {
            $query->where('is_approved', $request->boolean('is_approved'));
        }

        // Filter by created by user (for user's own logbooks)
        if ($request->has('my_logbooks') && $request->boolean('my_logbooks')) {
            $query->where('created_by', Auth::id());
        }

        // Sort by date (newest first by default)
        $sortBy = $request->get('sort_by', 'date');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $perPage = $request->get('per_page', 15);
        $logbooks = $query->paginate($perPage);

        return response()->json([
            'status' => 'success',
            'data' => $logbooks
        ]);
    }

    /**
     * Get specific logbook by ID
     */
    public function show($id): JsonResponse
    {
        $logbook = Logbook::with([
            'unit', 
            'createdBy', 
            'approvedBy', 
            'signedBy', 
            'items.teknisi', 
            'teknisi.user'
        ])->find($id);

        if (!$logbook) {
            return response()->json([
                'status' => 'error',
                'message' => 'Logbook not found'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $logbook
        ]);
    }

    /**
     * Create new logbook
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'unit_id' => 'required|exists:units,id',
            'date' => 'required|date',
            'judul' => 'required|string|max:255',
            'shift' => 'required|in:1,2,3',
            'catatan' => 'nullable|string',
            'items' => 'nullable|array',
            'items.*.judul' => 'required|string|max:255',
            'items.*.catatan' => 'nullable|string',
            'items.*.tanggal_kegiatan' => 'required|date',
            'items.*.mulai' => 'required|date_format:H:i',
            'items.*.selesai' => 'required|date_format:H:i|after:items.*.mulai',
            'items.*.tools' => 'nullable|string',
            'items.*.teknisi' => 'required|exists:users,id',
            'teknisi' => 'nullable|array',
            'teknisi.*' => 'exists:users,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        DB::beginTransaction();
        try {
            // Create logbook
            $logbook = Logbook::create([
                'unit_id' => $request->unit_id,
                'date' => $request->date,
                'judul' => $request->judul,
                'shift' => $request->shift,
                'created_by' => Auth::id(),
                'catatan' => $request->catatan,
            ]);

            // Create logbook items
            if ($request->has('items') && is_array($request->items)) {
                foreach ($request->items as $item) {
                    LogbookItem::create([
                        'logbook_id' => $logbook->id,
                        'judul' => $item['judul'],
                        'catatan' => $item['catatan'] ?? null,
                        'tanggal_kegiatan' => $item['tanggal_kegiatan'],
                        'mulai' => $item['mulai'],
                        'selesai' => $item['selesai'],
                        'tools' => $item['tools'] ?? null,
                        'teknisi' => $item['teknisi'],
                    ]);
                }
            }

            // Add teknisi to logbook
            if ($request->has('teknisi') && is_array($request->teknisi)) {
                foreach ($request->teknisi as $teknisiId) {
                    LogbookTeknisi::create([
                        'logbook_id' => $logbook->id,
                        'user_id' => $teknisiId,
                    ]);
                }
            }

            DB::commit();

            $logbook->load(['unit', 'createdBy', 'items.teknisi', 'teknisi.user']);

            return response()->json([
                'status' => 'success',
                'message' => 'Logbook created successfully',
                'data' => $logbook
            ], 201);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to create logbook',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update logbook
     */
    public function update(Request $request, $id): JsonResponse
    {
        $logbook = Logbook::find($id);

        if (!$logbook) {
            return response()->json([
                'status' => 'error',
                'message' => 'Logbook not found'
            ], 404);
        }

        // Check if user can edit this logbook
        $user = Auth::user();
        if ($logbook->created_by !== $user->id && $user->access_level !== 'admin') {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized to edit this logbook'
            ], 403);
        }

        // If logbook is already approved, only admin can edit
        if ($logbook->is_approved && $user->access_level !== 'admin') {
            return response()->json([
                'status' => 'error',
                'message' => 'Cannot edit approved logbook'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'unit_id' => 'sometimes|exists:units,id',
            'date' => 'sometimes|date',
            'judul' => 'sometimes|string|max:255',
            'shift' => 'sometimes|in:1,2,3',
            'catatan' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $logbook->update($request->only([
            'unit_id', 'date', 'judul', 'shift', 'catatan'
        ]));

        $logbook->load(['unit', 'createdBy', 'approvedBy', 'signedBy', 'items.teknisi', 'teknisi.user']);

        return response()->json([
            'status' => 'success',
            'message' => 'Logbook updated successfully',
            'data' => $logbook
        ]);
    }

    /**
     * Delete logbook
     */
    public function destroy($id): JsonResponse
    {
        $logbook = Logbook::find($id);

        if (!$logbook) {
            return response()->json([
                'status' => 'error',
                'message' => 'Logbook not found'
            ], 404);
        }

        $user = Auth::user();
        
        // Check if user can delete this logbook
        if ($logbook->created_by !== $user->id && $user->access_level !== 'admin') {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized to delete this logbook'
            ], 403);
        }

        // If logbook is approved, only admin can delete
        if ($logbook->is_approved && $user->access_level !== 'admin') {
            return response()->json([
                'status' => 'error',
                'message' => 'Cannot delete approved logbook'
            ], 403);
        }

        DB::beginTransaction();
        try {
            // Delete related items and teknisi
            $logbook->items()->delete();
            $logbook->teknisi()->delete();
            
            // Delete logbook
            $logbook->delete();

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Logbook deleted successfully'
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to delete logbook',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Approve logbook
     */
    public function approve(Request $request, $id): JsonResponse
    {
        $user = Auth::user();
        
        if ($user->access_level !== 'admin') {
            return response()->json([
                'status' => 'error',
                'message' => 'Only admin can approve logbooks'
            ], 403);
        }

        $logbook = Logbook::find($id);

        if (!$logbook) {
            return response()->json([
                'status' => 'error',
                'message' => 'Logbook not found'
            ], 404);
        }

        $logbook->update([
            'is_approved' => true,
            'approved_by' => $user->id,
        ]);

        $logbook->load(['unit', 'createdBy', 'approvedBy', 'signedBy']);

        return response()->json([
            'status' => 'success',
            'message' => 'Logbook approved successfully',
            'data' => $logbook
        ]);
    }

    /**
     * Sign logbook
     */
    public function sign(Request $request, $id): JsonResponse
    {
        $user = Auth::user();
        
        if ($user->access_level !== 'admin') {
            return response()->json([
                'status' => 'error',
                'message' => 'Only admin can sign logbooks'
            ], 403);
        }

        $logbook = Logbook::find($id);

        if (!$logbook) {
            return response()->json([
                'status' => 'error',
                'message' => 'Logbook not found'
            ], 404);
        }

        if (!$logbook->is_approved) {
            return response()->json([
                'status' => 'error',
                'message' => 'Logbook must be approved before signing'
            ], 400);
        }

        $logbook->update([
            'signed_by' => $user->id,
            'signed_at' => now(),
        ]);

        $logbook->load(['unit', 'createdBy', 'approvedBy', 'signedBy']);

        return response()->json([
            'status' => 'success',
            'message' => 'Logbook signed successfully',
            'data' => $logbook
        ]);
    }

    /**
     * Get logbook statistics
     */
    public function statistics(): JsonResponse
    {
        $stats = [
            'total_logbooks' => Logbook::count(),
            'approved_logbooks' => Logbook::where('is_approved', true)->count(),
            'pending_logbooks' => Logbook::where('is_approved', false)->count(),
            'signed_logbooks' => Logbook::whereNotNull('signed_by')->count(),
            'this_month' => Logbook::whereMonth('date', now()->month)
                                   ->whereYear('date', now()->year)
                                   ->count(),
            'this_week' => Logbook::whereBetween('date', [now()->startOfWeek(), now()->endOfWeek()])->count(),
            'by_shift' => [
                'shift_1' => Logbook::where('shift', '1')->count(),
                'shift_2' => Logbook::where('shift', '2')->count(),
                'shift_3' => Logbook::where('shift', '3')->count(),
            ]
        ];

        return response()->json([
            'status' => 'success',
            'data' => $stats
        ]);
    }
}
