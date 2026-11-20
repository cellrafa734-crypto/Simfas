<?php
namespace App\Http\Controllers;
use App\Models\{Facility, Borrowing};
class DashboardController extends Controller {
    public function index() {
        $u = auth()->user();
        $stats = ['Total fasilitas'=>Facility::count(), 'Tersedia'=>Facility::where('status','tersedia')->count(),
                  'Dalam pemeliharaan'=>Facility::where('status','pemeliharaan')->count()];
        if ($u->role === 'admin') {
            $stats['Menunggu persetujuan'] = Borrowing::where('status','menunggu')->count();
            $stats['Total pengajuan'] = Borrowing::count();
        } else {
            $stats['Pengajuan saya'] = Borrowing::where('user_id',$u->id)->count();
        }
        return view('dashboard', compact('stats'));
    }
}
