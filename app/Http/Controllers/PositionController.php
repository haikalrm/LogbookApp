<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Position;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class PositionController extends Controller
{
    public function index(Request $request)
    {
        try {
            $positions = Position::paginate(10);
            
            if ($request->wantsJson()) {
                 return response()->json(['success' => true, 'data' => $positions]);
            }

            return view('positions.index', compact('positions'));
        } catch (\Exception $e) {
             return back()->with('errorMessage', 'Gagal memuat data jabatan.');
        }
    }

    public function update(Request $request)
	{
		try {
			if (auth()->user()->access_level != 2) {
				return response()->json(['success' => false, 'message' => 'Akses ditolak.'], 403);
			}

			$validator = Validator::make($request->all(), [
				'position_name' => 'required|string|max:255',
				'position_id'   => 'required|integer',
			]);

			if ($validator->fails()) {
				return response()->json([
					'success' => false, 
					'message' => $validator->errors()->first()
				], 422);
			}

			if ($request->position_id > 0) {
				$position = Position::find($request->position_id);
				if (!$position) {
					return response()->json(['success' => false, 'message' => 'Posisi tidak ditemukan'], 404);
				}
				$position->name = $request->position_name;
				$position->save();
				$message = 'Posisi berhasil diperbarui.';
			} else {
				$position = new Position();
				$position->name = $request->position_name;
				$position->user_id = auth()->id(); //
				$position->save();
				$message = 'Posisi baru berhasil ditambahkan.';
			}

			return response()->json(['success' => true, 'message' => $message]);

		} catch (\Exception $e) {
			Log::error('Position Update Error: ' . $e->getMessage());
			return response()->json([
				'success' => false, 
				'message' => 'Gagal menyimpan: ' . $e->getMessage()
			], 500);
		}
	}

    public function delete(Request $request)
	{
		try {
			if (auth()->user()->access_level != 2) {
				return response()->json(['success' => false, 'message' => 'Forbidden'], 403);
			}

			$validator = Validator::make($request->all(), [
				'position_id' => 'required|integer|exists:positions,id',
			]);

			if ($validator->fails()) {
				return response()->json(['success' => false, 'message' => 'Posisi tidak ditemukan'], 404);
			}

			$position = Position::find($request->position_id);

			\App\Models\User::where('position_id', $position->id)->update(['position_id' => null]);

			$position->delete();

			return response()->json(['success' => true, 'message' => 'Posisi berhasil dihapus']);

		} catch (\Exception $e) {
			Log::error('Position Delete Error: ' . $e->getMessage());
			return response()->json(['success' => false, 'message' => 'Gagal menghapus: ' . $e->getMessage()], 500);
		}
	}
}