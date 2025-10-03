<?php

// app/Http/Controllers/LogbookController.php

namespace App\Http\Controllers;

use App\Models\Logbook;
use App\Models\Unit;
use App\Models\LogbookItem;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

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
		// Cari logbook berdasarkan ID dan relasi logbookItems dengan teknisi
		$logbook = Logbook::with(['items.teknisi_user'])->findOrFail($logbook_id);

		// Ambil data logbookItems yang sesuai dengan logbook_id
		$logbookItems = $logbook->items;

		// Mengirim data ke view, termasuk unit_id
		return view('logbook.view', compact('logbook', 'logbookItems', 'unit_id'));
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
    // Debug tambahan untuk melihat masalah
    Log::info('Store method called', [
        'request_data' => $request->all(),
        'unit_id' => $unit_id,
        'user_authenticated' => auth()->check(),
        'user_id' => auth()->id()
    ]);

    try {
        $validated = $request->validate([
            'nameWithTitle' => 'required|string|min:5|max:64',
            'dateWithTitle' => 'required|date',
            'radio_shift' => 'required|in:1,2,3',
        ]);

        // Debug: cek data yang masuk
        Log::info('Store Logbook Data:', [
            'unit_id' => $unit_id,
            'validated' => $validated,
            'user_id' => auth()->id()
        ]);

        // Simpan Logbook
        $logbook = new Logbook();
        $logbook->unit_id = $unit_id;
        $logbook->judul = $validated['nameWithTitle'];
        $logbook->date = $validated['dateWithTitle']; // sesuai migration
        $logbook->shift = $validated['radio_shift'];
        $logbook->created_by = auth()->id(); // sesuai migration
        $logbook->is_approved = 0; // sesuai migration
        $saved = $logbook->save();

        // Debug: cek apakah data tersimpan
        Log::info('Logbook Save Result:', [
            'saved' => $saved,
            'logbook_id' => $logbook->id
        ]);

    // Menambahkan item logbook pertama (optional, bisa dihapus jika tidak perlu)
    // $no_report = LogbookItem::where('logbook_id', $logbook->id)->max('no_report') + 1;
    // $logbookItem = new LogbookItem();
    // $logbookItem->logbook_id = $logbook->id;
    // $logbookItem->no_report = $no_report;
    // $logbookItem->save();

    // Notifikasi
    $notification = new Notification();
    $notification->author_id = auth()->id();
    $notification->title = 'New Logbook Added';
    $notification->body = auth()->user()->name . ' added a new logbook titled ' . $logbook->judul;
    $notification->profile = auth()->user()->profile_picture ?? 'default.png';
    $notification->link = route('logbook.index', $unit_id);
    $notification->save();

    // Notify all users
    $users = User::all();
    foreach ($users as $user) {
        // Menggunakan attach dengan proper pivot table
        DB::table('user_notifications')->insert([
            'user_id' => $user->id,
            'notification_id' => $notification->id,
            'status' => 0,
            'created_at' => now(),
            'updated_at' => now()
        ]);
    }

    session()->flash('successMessage', 'Logbook berhasil ditambahkan!');
    return redirect()->route('logbook.index', $unit_id);

    } catch (\Exception $e) {
        Log::error('Error creating logbook:', [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
        
        session()->flash('errorMessage', 'Gagal membuat logbook: ' . $e->getMessage());
        return redirect()->back()->withInput();
    }
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
            'radio_shift' => 'required|in:1,2,3',
        ]);

        $logbook = Logbook::findOrFail($logbook_id);
        $logbook->judul = $validated['nameWithTitle'];
        $logbook->date = $validated['dateWithTitle']; // sesuai migration
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

    public function editContent($unit_id, $logbook_id)
    {
        $unit = Unit::findOrFail($unit_id);
        $logbook = Logbook::findOrFail($logbook_id);
        // Query langsung untuk items terbaru
        $logbookItems = LogbookItem::with('teknisi_user')
                                  ->where('logbook_id', $logbook_id)
                                  ->orderBy('created_at', 'asc')
                                  ->get();
        
        return view('logbook.edit-content', compact('unit', 'logbook', 'logbookItems', 'unit_id'));
    }
}
