<?php

namespace App\Http\Controllers;

use App\Models\Facility;
use App\Models\Borrowing;
use Illuminate\Support\Carbon;

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
            $stats['Total pengajuan'] = Borrowing::count();
        } else {
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

        // Grafik peminjaman per bulan pada tahun berjalan
        $monthlyLabels = [];
        $monthlyBorrowings = [];

        for ($month = 1; $month <= 12; $month++) {
            $date = Carbon::create(now()->year, $month, 1);

            $monthlyLabels[] = $date->locale('id')->translatedFormat('M');

            $monthlyBorrowings[] = Borrowing::whereYear('created_at', now()->year)
                ->whereMonth('created_at', $month)
                ->when($u->role !== 'admin', function ($query) use ($u) {
                    $query->where('user_id', $u->id);
                })
                ->count();
        }

        // Komposisi status fasilitas
        $facilityStatusLabels = [
            'Tersedia',
            'Dipinjam',
            'Pemeliharaan',
            'Tidak dapat digunakan',
        ];

        $facilityStatusValues = [
            Facility::where('status', 'tersedia')->count(),
            Facility::where('status', 'dipinjam')->count(),
            Facility::where('status', 'pemeliharaan')->count(),
            Facility::where('status', 'tidak_dapat_digunakan')->count(),
        ];

        return view('dashboard', compact(
            'stats',
            'recentBorrowings',
            'monthlyLabels',
            'monthlyBorrowings',
            'facilityStatusLabels',
            'facilityStatusValues'
        ));
    }
}
