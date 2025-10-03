<?php

// app/Http/Controllers/LogbookItemController.php

namespace App\Http\Controllers;

use App\Models\LogbookItem;
use App\Models\Logbook;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class LogbookItemController extends Controller
{
    public function create($unit_id, $logbook_id)
    {
        $logbook = Logbook::findOrFail($logbook_id);
        // Hitung nomor urut berikutnya berdasarkan jumlah item yang ada
        $next_report_number = LogbookItem::where('logbook_id', $logbook_id)->count() + 1;
        return view('logbook_items.create', compact('logbook', 'unit_id', 'next_report_number'));
    }

    public function store(Request $request, $unit_id, $logbook_id)
    {
        // Cek apakah sudah mencapai batas maksimal 5 items
        $currentItemsCount = LogbookItem::where('logbook_id', $logbook_id)->count();
        if ($currentItemsCount >= 5) {
            return redirect()->back()->with('errorMessage', 'Maksimal 5 content per logbook sudah tercapai!');
        }

        try {
            Log::info('LogbookItem Store Started:', [
                'unit_id' => $unit_id,
                'logbook_id' => $logbook_id,
                'request_data' => $request->all()
            ]);
            
            $validated = $request->validate([
                'judul' => 'required|string|min:5|max:255',
                'catatan' => 'required|string|min:10|max:1000',
                'tanggal_kegiatan' => 'required|date',
                'tools' => 'required|string|max:255',
                'teknisi' => 'required|integer|exists:users,id',
                'mulai' => 'required|date_format:Y-m-d\TH:i',
                'selesai' => 'required|date_format:Y-m-d\TH:i|after:mulai',
            ]);
            
            Log::info('Validation passed:', ['validated' => $validated]);

            $logbookItem = new LogbookItem();
            $logbookItem->logbook_id = $logbook_id;
            $logbookItem->judul = $validated['judul'];
            $logbookItem->catatan = $validated['catatan'];
            $logbookItem->tanggal_kegiatan = $validated['tanggal_kegiatan'];
            $logbookItem->tools = $validated['tools'];
            $logbookItem->teknisi = $validated['teknisi'];
            $logbookItem->mulai = $validated['mulai'];
            $logbookItem->selesai = $validated['selesai'];
            $saved = $logbookItem->save();
            
            // Debug
            Log::info('LogbookItem Store Debug:', [
                'saved' => $saved,
                'item_id' => $logbookItem->id,
                'logbook_id' => $logbook_id,
                'data' => $validated
            ]);

            Cache::forget('logbook_items_' . $logbook_id);

            // Check if it's an AJAX request
            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Item logbook berhasil ditambahkan!',
                    'item' => $logbookItem
                ]);
            }

            return redirect()->route('logbook.edit.content', ['unit_id' => $unit_id, 'logbook_id' => $logbook_id])
                             ->with('successMessage', 'Item logbook berhasil ditambahkan!');

        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation Error Details:', [
                'errors' => $e->errors(),
                'messages' => $e->getMessage(),
                'request_data' => $request->all()
            ]);
            
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Terjadi kesalahan validasi data.',
                    'errors' => $e->errors()
                ], 422);
            }
            
            return redirect()->back()
                           ->withErrors($e->errors())
                           ->withInput()
                           ->with('errorMessage', 'Terjadi kesalahan validasi data.');
        } catch (\Exception $e) {
            Log::error('Store LogbookItem Error:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all()
            ]);
            
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Terjadi kesalahan saat menyimpan data.',
                    'error' => $e->getMessage()
                ], 500);
            }
            
            return redirect()->back()
                           ->withInput()
                           ->with('errorMessage', 'Terjadi kesalahan saat menyimpan data.');
        }
    }

    public function edit($unit_id, $logbook_id, $item_id)
    {
        $logbook = Logbook::findOrFail($logbook_id);
        $logbookItem = LogbookItem::where('logbook_id', $logbook_id)->findOrFail($item_id);
        return view('logbook_items.edit', compact('logbook', 'logbookItem', 'unit_id'));
    }

    public function update(Request $request, $unit_id, $logbook_id, $item_id)
    {
        try {
            $validated = $request->validate([
                'judul' => 'required|string|min:5|max:255',
                'catatan' => 'required|string|min:10|max:1000',
                'tanggal_kegiatan' => 'required|date',
                'tools' => 'required|string|max:255',
                'teknisi' => 'required|integer|exists:users,id',
                'mulai' => 'required|date',
                'selesai' => 'required|date|after_or_equal:mulai',
            ]);

            $logbookItem = LogbookItem::where('logbook_id', $logbook_id)->findOrFail($item_id);
            $logbookItem->judul = $validated['judul'];
            $logbookItem->catatan = $validated['catatan'];
            $logbookItem->tanggal_kegiatan = $validated['tanggal_kegiatan'];
            $logbookItem->tools = $validated['tools'];
            $logbookItem->teknisi = $validated['teknisi'];
            $logbookItem->mulai = $validated['mulai'];
            $logbookItem->selesai = $validated['selesai'];
            $logbookItem->save();

            // Clear cache
            Cache::forget('logbook_items_' . $logbook_id);

            // Check if it's an AJAX request
            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Item logbook berhasil diperbarui!',
                    'item' => $logbookItem
                ]);
            }

            return redirect()->route('logbook.view', ['unit_id' => $unit_id, 'logbook_id' => $logbook_id])
                             ->with('successMessage', 'Item logbook berhasil diperbarui!');
                             
        } catch (\Illuminate\Validation\ValidationException $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Terjadi kesalahan validasi data.',
                    'errors' => $e->errors()
                ], 422);
            }
            
            return redirect()->back()
                           ->withErrors($e->errors())
                           ->withInput();
                           
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Terjadi kesalahan saat mengupdate data.',
                    'error' => $e->getMessage()
                ], 500);
            }
            
            return redirect()->back()
                           ->withInput()
                           ->with('errorMessage', 'Terjadi kesalahan saat mengupdate data.');
        }
    }

    public function destroy($unit_id, $logbook_id, $item_id)
    {
        try {
            $logbookItem = LogbookItem::where('logbook_id', $logbook_id)->findOrFail($item_id);
            
            // Log the delete action
            Log::info('LogbookItem Delete:', [
                'item_id' => $item_id,
                'logbook_id' => $logbook_id,
                'unit_id' => $unit_id,
                'judul' => $logbookItem->judul
            ]);
            
            $logbookItem->delete();

            // Clear cache
            Cache::forget('logbook_items_' . $logbook_id);

            return response()->json([
                'success' => true, 
                'message' => 'Logbook item berhasil dihapus!'
            ]);
            
        } catch (\Exception $e) {
            Log::error('Delete LogbookItem Error:', [
                'message' => $e->getMessage(),
                'item_id' => $item_id,
                'logbook_id' => $logbook_id,
                'unit_id' => $unit_id
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menghapus data.'
            ], 500);
        }
    }
}
