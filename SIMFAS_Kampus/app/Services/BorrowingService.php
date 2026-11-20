<?php
namespace App\Services;

use App\Models\Borrowing;
use App\Models\Facility;
use Carbon\Carbon;

class BorrowingService
{
    /** Menghitung kapasitas minimum yang tersisa pada seluruh rentang waktu yang diminta. */
    public function available(Facility $f, $start, $end, ?int $excludeId = null): int
    {
        $startAt = Carbon::parse($start);
        $endAt = Carbon::parse($end);
        $bookings = Borrowing::where('facility_id', $f->id)
            ->whereIn('status', ['disetujui', 'sedang_digunakan'])
            ->where('start_at', '<', $endAt)
            ->where('end_at', '>', $startAt)
            ->when($excludeId, fn ($q) => $q->where('id', '!=', $excludeId))
            ->get(['units', 'start_at', 'end_at']);

        // Sweep-line: hitung unit yang benar-benar aktif bersamaan, bukan menjumlahkan
        // semua booking yang sekadar bersinggungan dengan rentang permintaan.
        $events = [];
        foreach ($bookings as $booking) {
            $bookingStart = Carbon::parse($booking->start_at)->max($startAt);
            $bookingEnd = Carbon::parse($booking->end_at)->min($endAt);
            $events[] = [$bookingStart->timestamp, 1, (int) $booking->units];
            $events[] = [$bookingEnd->timestamp, 0, (int) $booking->units];
        }
        // Pada waktu yang sama, proses akhir sebelum mulai (interval [mulai, selesai)).
        usort($events, fn ($a, $b) => ($a[0] <=> $b[0]) ?: ($a[1] <=> $b[1]));
        $active = 0;
        $peak = 0;
        foreach ($events as [, $type, $units]) {
            $active += $type === 1 ? $units : -$units;
            $peak = max($peak, $active);
        }

        return max(0, (int) $f->units - $peak);
    }
}
