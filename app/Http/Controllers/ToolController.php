<?php

namespace App\Http\Controllers;

use App\Models\Tool;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ToolController extends Controller
{
    public function index()
    {
        $tools = Tool::all();

        return view('tools.index', compact('tools'));
    }
    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'tools_name' => 'required|string|max:255',
            'peralatan_id' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal: ' . $validator->errors()->first()
            ]);
        }

        // Jika ID lebih besar dari 0, cari data untuk update
        $tool = $request->peralatan_id > 0 ? Tool::find($request->peralatan_id) : new Tool;
        $tool->name = $request->tools_name;

        if ($tool->save()) {
            session()->flash('successMessage', $request->peralatan_id > 0 ? 'Peralatan berhasil diperbarui' : 'Peralatan berhasil ditambahkan');
            return response()->json([
                'success' => true,
                'message' => 'Peralatan berhasil disimpan'
            ]);
        }

        session()->flash('errorMessage', 'Gagal menyimpan peralatan');
        return response()->json([
            'success' => false,
            'message' => 'Gagal menyimpan peralatan'
        ]);
    }

    // Delete method
    public function delete(Request $request)
	{
		$validator = Validator::make($request->all(), [
			'peralatan_id' => 'required|integer|exists:alat,id',
		]);

		if ($validator->fails()) {
			return response()->json([
				'success' => false,
				'message' => 'Peralatan tidak ditemukan'
			]);
		}

		$tool = Tool::find($request->peralatan_id);
		if ($tool && $tool->delete()) {
			return response()->json([
				'success' => true,
				'message' => 'Peralatan berhasil dihapus'
			]);
		}

		return response()->json([
			'success' => false,
			'message' => 'Gagal menghapus peralatan'
		]);
	}

}
