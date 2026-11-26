<?php

namespace App\Http\Controllers;

use App\Models\Facility;
use App\Models\Borrowing;

class DashboardController extends Controller
{
    public function index()
    {
        $u = auth()->user();

        // Statistik utama: tetap dihitung dari database
        $stats = [
            'Total fasilitas' => Facility::count(),
            'Tersedia' => Facility::where('status', 'tersedia')->count(),
            'Dalam pemeliharaan' => Facility::where('status', 'pemeliharaan')->count(),
        ];

        if ($u->role === 'admin') {
            $stats['Menunggu persetujuan'] = Borrowing::where('status', 'menunggu')->count();
            $stats['Berlangsung hari ini'] = Borrowing::where('status', 'disetujui')
                ->where('start_at', '<=', now())
                ->where('end_at', '>=', now())
                ->count();
            $stats['Total pengajuan'] = Borrowing::count();
        } elseif ($u->role !== 'petugas') {
            $stats['Pengajuan saya'] = Borrowing::where('user_id', $u->id)->count();
        }

        // Aktivitas terbaru. Admin melihat semua, pengguna lain hanya miliknya.
        $recentBorrowings = Borrowing::with(['user', 'facility'])
            ->when($u->role !== 'admin', function ($query) use ($u) {
                $query->where('user_id', $u->id);
            })
            ->latest()
            ->take(8)
            ->get();

        // Data operasional untuk admin: yang perlu diputuskan dan jadwal terdekat
        $pendingQueue = collect();
        $upcomingSchedule = collect();

        if ($u->role === 'admin') {
            $pendingQueue = Borrowing::with(['user', 'facility'])
                ->where('status', 'menunggu')
                ->oldest()
                ->take(6)
                ->get();

            $upcomingSchedule = Borrowing::with(['user', 'facility'])
                ->where('status', 'disetujui')
                ->where('end_at', '>=', now())
                ->orderBy('start_at')
                ->take(6)
                ->get();
        }

        // Fasilitas yang sedang tidak bisa dipinjam (admin dan petugas)
        $attentionFacilities = Facility::whereIn('status', ['pemeliharaan', 'tidak_dapat_digunakan'])
            ->orderBy('name')
            ->take(6)
            ->get();

        return view('dashboard', compact(
            'stats',
            'recentBorrowings',
            'pendingQueue',
            'upcomingSchedule',
            'attentionFacilities'
        ));
    }
}
