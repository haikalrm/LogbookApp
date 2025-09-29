<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Position;

class PositionController extends Controller
{
    public function index()
    {
        $positions = Position::paginate(10);
        return view('positions.index', compact('positions'));
    }

    public function update(Request $request)
    {
        // Validasi input
        $validator = validator($request->all(), [
            'position_name' => 'required|string|max:255',
            'position_id' => 'required|integer', // Validasi position_id
        ]);

        // Jika validasi gagal
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal: ' . $validator->errors()->first()
            ]);
        }

        // Jika position_id > 0, lakukan update, jika tidak maka buat posisi baru
        if ($request->position_id > 0) {
            // Update posisi yang ada
            $position = Position::find($request->position_id);

            if (!$position) {
                return response()->json([
                    'success' => false,
                    'message' => 'Posisi tidak ditemukan.'
                ]);
            }

            $position->name = $request->position_name;
            if ($position->save()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Posisi berhasil diperbarui.'
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal memperbarui posisi.'
                ]);
            }
        } else {
            // Jika position_id = 0, buat posisi baru
            $position = new Position();
            $position->name = $request->position_name;

            if ($position->save()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Posisi baru berhasil ditambahkan.'
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal menambahkan posisi.'
                ]);
            }
        }
    }

    public function delete(Request $request)
    {
        $validator = validator($request->all(), [
            'position_id' => 'required|integer|exists:positions,no',
        ]);

        if ($validator->fails()) {
            session()->flash('errorMessage', 'Posisi tidak ditemukan');
            
            return response()->json([
                'success' => false,
                'message' => 'Posisi tidak ditemukan'
            ]);
        }

        $position = Position::find($request->position_id);
        if ($position && $position->delete()) {
            session()->flash('successMessage', 'Posisi berhasil dihapus');
            
            return response()->json([
                'success' => true,
                'message' => 'Posisi berhasil dihapus'
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Gagal menghapus posisi'
        ]);
    }
}
