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
        return view('logbook.item.create', compact('logbook', 'unit_id'));
    }

    public function store(Request $request, $unit_id, $logbook_id)
    {
        $validated = $request->validate([
            'tanggal_kegiatan' => 'required|date',
            'peralatan' => 'required|string|max:255',
            'uraian' => 'required|string|min:10|max:255',
            'teknisi' => 'required|integer|exists:users,id',
            'mulai' => 'required|date_format:Y-m-d H:i:s',
            'selesai' => 'required|date_format:Y-m-d H:i:s',
        ]);

        $no_report = LogbookItem::where('logbook_id', $logbook_id)->max('no_report') + 1;

        $logbookItem = new LogbookItem();
        $logbookItem->logbook_id = $logbook_id;
        $logbookItem->no_report = $no_report;
        $logbookItem->tanggal_kegiatan = $validated['tanggal_kegiatan'];
        $logbookItem->peralatan = $validated['peralatan'];
        $logbookItem->uraian = $validated['uraian'];
        $logbookItem->teknisi = $validated['teknisi'];
        $logbookItem->mulai = $validated['mulai'];
        $logbookItem->selesai = $validated['selesai'];
        $logbookItem->save();

        return redirect()->route('logbook.item.create', [$unit_id, $logbook_id])
                         ->with('successMessage', 'Logbook item berhasil ditambahkan!');
    }

    public function destroy($unit_id, $logbook_id, $item_id)
    {
        $logbookItem = LogbookItem::where('logbook_id', $logbook_id)->findOrFail($item_id);
        $logbookItem->delete();

        return response()->json(['success' => true, 'message' => 'Logbook item berhasil dihapus']);
    }
	
	public function teknisi()
	{
		return $this->belongsTo(User::class, 'teknisi');  // Menyesuaikan dengan kolom teknisi di logbook_items
	}
}
