<?php
namespace App\Http\Controllers;
use App\Models\{Borrowing, Facility};
use App\Services\BorrowingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
class BorrowingController extends Controller {
    public function __construct(private BorrowingService $svc) {}
    public function index() {
        $u = auth()->user();
        // Dasar query mengikuti hak akses pengguna.
        $scope = Borrowing::query()
            ->when(
                $u->role !== 'admin',
                fn($q) => $q->where('user_id', $u->id)
            );

        // Statistik seluruh data, bukan hanya 15 data per halaman.
        $totalBorrowings = (clone $scope)->count();

        $pendingBorrowings = (clone $scope)
            ->where('status', 'menunggu')
            ->count();

        $approvedBorrowings = (clone $scope)
            ->where('status', 'disetujui')
            ->count();

        // Daftar peminjaman tetap menggunakan query dan pagination lama.
        $items = Borrowing::with('facility', 'user')
            ->when(
                $u->role !== 'admin',
                fn($q) => $q->where('user_id', $u->id)
            )
            ->latest()
            ->paginate(15);

        return view('borrowings.index', compact(
            'items',
            'totalBorrowings',
            'pendingBorrowings',
            'approvedBorrowings'
        ));
    }
    public function show(Borrowing $borrowing)
    {
        $user = auth()->user();

        // Mahasiswa dan organisasi hanya boleh melihat pengajuannya sendiri.
        abort_unless(
            $user->role === 'admin' || $borrowing->user_id === $user->id,
            403
        );

        $borrowing->load(['user', 'facility']);

        return view('borrowings.show', compact('borrowing'));
    }

    public function create() { return view('borrowings.create', ['facilities'=>Facility::whereIn('status',['tersedia','dipinjam'])->orderBy('name')->get()]); }
    public function store(Request $r) {
        $d = $r->validate(['facility_id'=>'required|exists:facilities,id','units'=>'required|integer|min:1',
            'start_at'=>'required|date|after:now','end_at'=>'required|date|after:start_at','purpose'=>'required|max:255']);
        $f = Facility::findOrFail($d['facility_id']);
        if (in_array($f->status, ['pemeliharaan','tidak_dapat_digunakan'], true))
            return back()->withInput()->with('err','Fasilitas sedang tidak dapat dipinjam.');
        if ($this->svc->available($f, $d['start_at'], $d['end_at']) < $d['units'])
            return back()->withInput()->with('err','Unit tidak mencukupi pada rentang waktu tersebut (bentrok jadwal).');
        Borrowing::create($d + ['user_id'=>auth()->id(),'status'=>'menunggu']);
        return redirect()->route('borrowings.index')->with('ok','Pengajuan dikirim.');
    }
    public function approve(Borrowing $borrowing) {
        return DB::transaction(function () use ($borrowing) {
            $f = Facility::lockForUpdate()->find($borrowing->facility_id);
            $b = Borrowing::lockForUpdate()->find($borrowing->id);
            if ($b->status !== 'menunggu') return back()->with('err','Pengajuan sudah diproses.');
            if ($this->svc->available($f, $b->start_at, $b->end_at, $b->id) < $b->units)
                return back()->with('err','Bentrok jadwal: unit tidak mencukupi, pengajuan tidak dapat disetujui.');
            $b->update(['status'=>'disetujui']);
            return back()->with('ok','Pengajuan disetujui.');
        });
    }
    public function reject(Request $r, Borrowing $borrowing) {
        $d = $r->validate(['reject_reason'=>'required|max:255']);
        $borrowing->update($d + ['status'=>'ditolak']);
        return back()->with('ok','Pengajuan ditolak.');
    }
    public function cancel(Borrowing $borrowing) {
        abort_unless($borrowing->user_id === auth()->id() && $borrowing->status === 'menunggu', 403);
        $borrowing->update(['status'=>'dibatalkan']); return back()->with('ok','Pengajuan dibatalkan.');
    }
}
