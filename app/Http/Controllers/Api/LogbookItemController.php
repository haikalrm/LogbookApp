<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\LogbookItem;
use App\Models\Logbook;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class LogbookItemController extends Controller
{
    /**
     * Get all logbook items for a specific logbook
     */
    public function index(Request $request, $logbookId): JsonResponse
    {
        $logbook = Logbook::find($logbookId);
        
        if (!$logbook) {
            return response()->json([
                'status' => 'error',
                'message' => 'Logbook not found'
            ], 404);
        }

        $items = LogbookItem::with('teknisi')
            ->where('logbook_id', $logbookId)
            ->orderBy('tanggal_kegiatan')
            ->orderBy('mulai')
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $items
        ]);
    }

    /**
     * Get specific logbook item
     */
    public function show($id): JsonResponse
    {
        $item = LogbookItem::with(['logbook', 'teknisi'])->find($id);

        if (!$item) {
            return response()->json([
                'status' => 'error',
                'message' => 'Logbook item not found'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $item
        ]);
    }

    /**
     * Create new logbook item
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'logbook_id' => 'required|exists:logbooks,id',
            'catatan' => 'nullable|string',
            'tanggal_kegiatan' => 'required|date',
            'mulai' => 'required|date_format:H:i',
            'selesai' => 'required|date_format:H:i|after:mulai',
            'tools' => 'nullable|string',
            'teknisi' => 'required|exists:users,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        // Check if logbook exists and user has permission
        $logbook = Logbook::find($request->logbook_id);
        $user = Auth::user();
        
        if ($logbook->created_by !== $user->id && $user->access_level !== 'admin') {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized to add items to this logbook'
            ], 403);
        }

        // Check if logbook is approved
        if ($logbook->is_approved && $user->access_level !== 'admin') {
            return response()->json([
                'status' => 'error',
                'message' => 'Cannot add items to approved logbook'
            ], 403);
        }

        $item = LogbookItem::create($request->all());
        $item->load(['logbook', 'teknisi']);

        return response()->json([
            'status' => 'success',
            'message' => 'Logbook item created successfully',
            'data' => $item
        ], 201);
    }

    /**
     * Update logbook item
     */
    public function update(Request $request, $id): JsonResponse
    {
        $item = LogbookItem::find($id);

        if (!$item) {
            return response()->json([
                'status' => 'error',
                'message' => 'Logbook item not found'
            ], 404);
        }

        $user = Auth::user();
        $logbook = $item->logbook;

        // Check permissions
        if ($logbook->created_by !== $user->id && $user->access_level !== 'admin') {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized to edit this logbook item'
            ], 403);
        }

        if ($logbook->is_approved && $user->access_level !== 'admin') {
            return response()->json([
                'status' => 'error',
                'message' => 'Cannot edit items in approved logbook'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'catatan' => 'nullable|string',
            'tanggal_kegiatan' => 'sometimes|date',
            'mulai' => 'sometimes|date_format:H:i',
            'selesai' => 'sometimes|date_format:H:i|after:mulai',
            'tools' => 'nullable|string',
            'teknisi' => 'sometimes|exists:users,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $item->update($request->only([
            'judul', 'catatan', 'tanggal_kegiatan', 'mulai', 'selesai', 'tools', 'teknisi'
        ]));

        $item->load(['logbook', 'teknisi']);

        return response()->json([
            'status' => 'success',
            'message' => 'Logbook item updated successfully',
            'data' => $item
        ]);
    }

    /**
     * Delete logbook item
     */
    public function destroy($id): JsonResponse
    {
        $item = LogbookItem::find($id);

        if (!$item) {
            return response()->json([
                'status' => 'error',
                'message' => 'Logbook item not found'
            ], 404);
        }

        $user = Auth::user();
        $logbook = $item->logbook;

        // Check permissions
        if ($logbook->created_by !== $user->id && $user->access_level !== 'admin') {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized to delete this logbook item'
            ], 403);
        }

        if ($logbook->is_approved && $user->access_level !== 'admin') {
            return response()->json([
                'status' => 'error',
                'message' => 'Cannot delete items from approved logbook'
            ], 403);
        }

        $item->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Logbook item deleted successfully'
        ]);
    }

    /**
     * Get items by technician
     */
    public function getByTeknisi(Request $request): JsonResponse
    {
        $teknisiId = $request->get('teknisi_id', Auth::id());
        
        $query = LogbookItem::with(['logbook.unit', 'teknisi'])
            ->where('teknisi', $teknisiId);

        // Filter by date range
        if ($request->has('start_date') && $request->start_date) {
            $query->whereDate('tanggal_kegiatan', '>=', $request->start_date);
        }
        
        if ($request->has('end_date') && $request->end_date) {
            $query->whereDate('tanggal_kegiatan', '<=', $request->end_date);
        }

        $items = $query->orderBy('tanggal_kegiatan', 'desc')
                      ->orderBy('mulai', 'desc')
                      ->paginate($request->get('per_page', 15));

        return response()->json([
            'status' => 'success',
            'data' => $items
        ]);
    }

    /**
     * Get items summary for a technician
     */
    public function teknisiSummary(Request $request): JsonResponse
    {
        $teknisiId = $request->get('teknisi_id', Auth::id());
        
        $summary = [
            'total_items' => LogbookItem::where('teknisi', $teknisiId)->count(),
            'this_month' => LogbookItem::where('teknisi', $teknisiId)
                                       ->whereMonth('tanggal_kegiatan', now()->month)
                                       ->whereYear('tanggal_kegiatan', now()->year)
                                       ->count(),
            'this_week' => LogbookItem::where('teknisi', $teknisiId)
                                      ->whereBetween('tanggal_kegiatan', [
                                          now()->startOfWeek(), 
                                          now()->endOfWeek()
                                      ])
                                      ->count(),
            'total_hours' => LogbookItem::where('teknisi', $teknisiId)
                                        ->selectRaw('SUM(TIME_TO_SEC(TIMEDIFF(selesai, mulai))/3600) as total_hours')
                                        ->value('total_hours') ?? 0
        ];

        return response()->json([
            'status' => 'success',
            'data' => $summary
        ]);
    }
}
