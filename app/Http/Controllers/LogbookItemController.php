<?php

// app/Http/Controllers/LogbookItemController.php

namespace App\Http\Controllers;

use App\Models\LogbookItem;
use App\Models\Logbook;
use Illuminate\Http\Request;

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
        $validated = $request->validate([
            'judul' => 'required|string|min:5|max:255',
            'catatan' => 'required|string|min:10|max:1000',
            'tanggal_kegiatan' => 'required|date',
            'tools' => 'required|string|max:255',
            'teknisi' => 'required|integer|exists:users,id',
            'mulai' => 'required|date',
            'selesai' => 'required|date|after:mulai',
        ]);

        $logbookItem = new LogbookItem();
        $logbookItem->logbook_id = $logbook_id;
        $logbookItem->judul = $validated['judul'];
        $logbookItem->catatan = $validated['catatan'];
        $logbookItem->tanggal_kegiatan = $validated['tanggal_kegiatan'];
        $logbookItem->tools = $validated['tools'];
        $logbookItem->teknisi = $validated['teknisi'];
        $logbookItem->mulai = $validated['mulai'];
        $logbookItem->selesai = $validated['selesai'];
        $logbookItem->save();

        return redirect()->route('logbook.view', ['unit_id' => $unit_id, 'logbook_id' => $logbook_id])
                         ->with('successMessage', 'Item logbook berhasil ditambahkan!');
    }

    public function destroy($unit_id, $logbook_id, $item_id)
    {
        $logbookItem = LogbookItem::where('logbook_id', $logbook_id)->findOrFail($item_id);
        $logbookItem->delete();

        return response()->json(['success' => true, 'message' => 'Logbook item berhasil dihapus']);
    }
}
