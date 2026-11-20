@extends('layouts.app')@section('content')
<div class="d-flex justify-content-between mb-3"><h4>Peminjaman</h4>@if(auth()->user()->role!=='admin')<a class="btn btn-primary" href="/borrowings/create">Ajukan</a>@endif</div>
<div class="card"><div class="table-responsive"><table class="table mb-0"><thead><tr><th>Pemohon</th><th>Fasilitas</th><th>Unit</th><th>Mulai</th><th>Selesai</th><th>Tujuan</th><th>Status</th><th></th></tr></thead><tbody>
@forelse($items as $b)<tr><td>{{ $b->user->name }}</td><td>{{ $b->facility->name }}</td><td>{{ $b->units }}</td><td>{{ $b->start_at->format('d/m/Y H:i') }}</td><td>{{ $b->end_at->format('d/m/Y H:i') }}</td><td>{{ $b->purpose }}</td>
<td><span class="badge bg-secondary">{{ $b->status }}</span>@if($b->reject_reason)<div class="small text-danger">{{ $b->reject_reason }}</div>@endif</td>
<td>@if(auth()->user()->isAdmin() && $b->status==='menunggu')
<form class="d-inline" method="post" action="/borrowings/{{ $b->id }}/approve">@csrf<button class="btn btn-sm btn-success">Setujui</button></form>
<form class="d-inline" method="post" action="/borrowings/{{ $b->id }}/reject">@csrf<input name="reject_reason" placeholder="Alasan" required class="form-control form-control-sm d-inline w-auto"><button class="btn btn-sm btn-danger">Tolak</button></form>
@elseif($b->user_id===auth()->id() && $b->status==='menunggu')<form method="post" action="/borrowings/{{ $b->id }}/cancel">@csrf<button class="btn btn-sm btn-outline-danger">Batalkan</button></form>@endif</td></tr>
@empty<tr><td colspan="8" class="text-center text-muted py-4">Belum ada pengajuan.</td></tr>@endforelse</tbody></table></div></div>
<div class="mt-3">{{ $items->links() }}</div>@endsection
