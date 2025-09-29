<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Notification; // Import model
use Illuminate\Support\Facades\Auth; // Import Auth untuk data user login

class NotificationController extends Controller
{
    /**
     * Menampilkan daftar notifikasi milik user yang sedang login.
     */
    public function index()
    {
        $notifications = Notification::where('author_id', auth()->id()) // Hanya ambil notifikasi milik user ini
            ->latest('created_at') // Urutkan dari yang paling baru
            ->paginate(15); // Ambil 15 notifikasi per halaman

        return view('notifications.index', compact('notifications'));
    }

    /**
     * Menandai satu notifikasi sebagai telah dibaca.
     */
    public function markAsRead(Notification $notification)
    {
        // Keamanan: Pastikan notifikasi ini benar-benar milik user yang login
        if ($notification->author_id === auth()->id() && is_null($notification->read_at)) {
            $notification->update(['read_at' => now()]);
        }

        return back()->with('success', 'Notification marked as read.');
    }

    /**
     * Menandai SEMUA notifikasi yang belum dibaca sebagai telah dibaca.
     */
    public function markAllAsRead()
    {
        Notification::where('author_id', auth()->id())
            ->whereNull('read_at') // Hanya yang belum dibaca
            ->update(['read_at' => now()]); // Update semua sekaligus

        return back()->with('success', 'All notifications marked as read.');
    }
}