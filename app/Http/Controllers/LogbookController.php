<?php

// app/Http/Controllers/LogbookController.php

namespace App\Http\Controllers;

use App\Models\Logbook;
use App\Models\Unit;
use App\Models\LogbookItem;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\Request;

class LogbookController extends Controller
{
    public function create($unit_id)
    {
        $unit = Unit::findOrFail($unit_id);
        return view('logbook.create', compact('unit'));
    }
	
	public function index($unit_id)
    {
        $unit = Unit::findOrFail($unit_id);
        $logbooks = Logbook::where('unit_id', $unit_id)->get();
        return view('logbook.index', compact('unit', 'logbooks'));
    }
	
	public function show($unit_id, $logbook_id)
	{
		// Cari logbook berdasarkan ID dan relasi logbookItems
		$logbook = Logbook::with('items.teknisi')->findOrFail($logbook_id);

		// Ambil data logbookItems yang sesuai dengan logbook_id
		$logbookItems = $logbook->items;

		// Hitung durasi dan lainnya
		$durations = [];
		foreach ($logbookItems as $item) {
			$mulai = Carbon::parse($item->mulai);
			$selesai = Carbon::parse($item->selesai);
			$time_diff = $mulai->diff($selesai);
			$durations[] = $time_diff->format('%d hari %h jam %i menit');
		}

		// Ambil nama teknisi dengan menggunakan relasi
		$teknisi = [];
		foreach ($logbookItems as $item) {
			$teknisi[] = $item->teknisi ? $item->teknisi->name : 'Nama tidak ditemukan';  // Pastikan teknisi ada
		}

		// Mengirim data ke view, termasuk unit_id
		return view('logbook.view', compact('logbook', 'logbookItems', 'durations', 'teknisi', 'unit_id'));
	}
	
	public function approve($unit_id, $logbook_id)
    {
        // Pastikan logbook ditemukan
        $logbook = Logbook::findOrFail($logbook_id);

        // Memastikan hanya bisa di-approve oleh user dengan level akses yang sesuai
        if (auth()->user()->access_level >= 1) { // misalnya, hanya user dengan access_level 1 atau lebih tinggi yang bisa approve
            // Update status logbook menjadi approved
            $logbook->update([
                'is_approved' => 1,  // 1 berarti approved
                'approved_by' => auth()->user()->id,
				'signed_by' => auth()->user()->id,
            ]);

            // Redirect ke halaman dashboard dengan pesan sukses
            return redirect()->route('logbook.index', $unit_id)->with('successMessage', 'Telah memperbarui status logbook menjadi disetujui');
        } else {
            // Jika user tidak memiliki hak akses
            return redirect()->route('logbook.index', $unit_id)->with('errorMessage', 'Anda tidak memiliki hak akses untuk menyetujui logbook ini');
        }
    }

    public function store(Request $request, $unit_id)
    {
        $validated = $request->validate([
            'nameWithTitle' => 'required|string|min:5|max:64',
            'dateWithTitle' => 'required|date',
            'radio_shift' => 'required|in:1,2,3',
        ]);

        // Simpan Logbook
        $logbook = new Logbook();
        $logbook->unit_id = $unit_id;
        $logbook->judul = $validated['nameWithTitle'];
        $logbook->tanggal_kegiatan = $validated['dateWithTitle'];
        $logbook->shift = $validated['radio_shift'];
        $logbook->author_id = auth()->id();
        $logbook->status = 0; // Pending
        $logbook->save();

        // Menambahkan item logbook pertama
        $no_report = LogbookItem::where('logbook_id', $logbook->id)->max('no_report') + 1;

        $logbookItem = new LogbookItem();
        $logbookItem->logbook_id = $logbook->id;
        $logbookItem->no_report = $no_report;
        $logbookItem->save();

        // Notifikasi
        $notification = new Notification();
        $notification->author_id = auth()->id();
        $notification->title = 'New Logbook Added';
        $notification->body = auth()->user()->name . ' added a new logbook titled ' . $logbook->judul;
        $notification->profile = auth()->user()->profile_picture;
        $notification->link = route('logbook.index', $unit_id);
        $notification->save();

        // Notify all users
        $users = User::all();
        foreach ($users as $user) {
            $user->notifications()->attach($notification->id, ['status' => 0]);
        }

        session()->flash('successMessage', 'Logbook berhasil ditambahkan!');

		return redirect()->route('logbook.index', $unit_id);
    }

    public function edit($unit_id, $logbook_id)
    {
        $unit = Unit::findOrFail($unit_id);
        $logbook = Logbook::findOrFail($logbook_id);
        return view('logbook.edit', compact('unit', 'logbook'));
    }

    public function update(Request $request, $unit_id, $logbook_id)
    {
        $validated = $request->validate([
            'nameWithTitle' => 'required|string|min:5|max:64',
            'dateWithTitle' => 'required|date',
            'radio_shift' => 'required|in:0,1,2',
        ]);

        $logbook = Logbook::findOrFail($logbook_id);
        $logbook->judul = $validated['nameWithTitle'];
        $logbook->tanggal_kegiatan = $validated['dateWithTitle'];
        $logbook->shift = $validated['radio_shift'];
        $logbook->save();

        return redirect()->route('logbook.index', $unit_id)->with('successMessage', 'Logbook berhasil diperbarui!');
    }

    public function destroy($unit_id, $logbook_id)
    {
        $logbook = Logbook::findOrFail($logbook_id);
        $logbook->delete();

        return response()->json(['success' => true, 'message' => 'Logbook berhasil dihapus']);
    }
}
