<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use App\Models\Notification;
use App\Models\Unit;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Gunakan View Composer agar lebih terorganisir dan efisien
        // Ini hanya akan berjalan untuk view yang benar-benar membutuhkan data ini.
        // Ganti ['layouts.app', 'partials.sidebar'] dengan nama view layout utama atau sidebar Anda.
        View::composer(['layouts.app', '*', 'notifications.*'], function ($view) {
            
            // Cek dulu apakah ada user yang login, jika tidak, tidak perlu query database.
            if (Auth::check()) {
                $userId = Auth::id();

                // CARA PENGAMBILAN DATA YANG BENAR (Sama seperti di Controller)
                
                // 1. Hitung notifikasi yang belum dibaca
                $unreadCount = Notification::where('author_id', $userId)
                                           ->whereNull('read_at') // Kolom untuk cek sudah dibaca atau belum
                                           ->count();

                // 2. Ambil 5 notifikasi terakhir untuk ditampilkan di dropdown header
                $notifications = Notification::where('author_id', $userId)
                                             ->latest() // Mengurutkan berdasarkan 'created_at' (paling baru)
                                             ->take(5)
                                             ->get();
                
                // 3. Ambil data units (ini sudah benar)
                $units = Unit::all();

                // Bagikan data yang sudah benar ke view
                $view->with(compact('unreadCount', 'notifications', 'units'));

            } else {
                // Jika tidak ada user login, kirim data default agar tidak error
                $view->with([
                    'unreadCount'   => 0,
                    'notifications' => collect(), // Collection kosong
                    'units'         => Unit::all(), // Mungkin Anda tetap butuh ini
                ]);
            }
        });
    }
}
