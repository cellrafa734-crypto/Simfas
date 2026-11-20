@extends('layouts.app')@section('content')
<div class="d-flex justify-content-between mb-3"><h4>Fasilitas</h4>@if(auth()->user()->isAdmin())<a class="btn btn-primary" href="/facilities/create">Tambah</a>@endif</div>
<div class="card"><div class="table-responsive"><table class="table mb-0"><thead><tr><th>Kode</th><th>Nama</th><th>Lokasi</th><th>Unit</th><th>Status</th><th>Kondisi</th><th></th></tr></thead><tbody>
@forelse($items as $f)<tr><td>{{ $f->code }}</td><td>{{ $f->name }}</td><td>{{ $f->location }}</td><td>{{ $f->units }}</td><td><span class="badge bg-secondary">{{ $f->status }}</span></td><td>{{ $f->condition }}</td>
<td class="text-end">@if(auth()->user()->isAdmin())<a class="btn btn-sm btn-outline-secondary" href="/facilities/{{ $f->id }}/edit">Ubah</a>
<form class="d-inline" method="post" action="/facilities/{{ $f->id }}" onsubmit="return confirm('Hapus fasilitas?')">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger">Hapus</button></form>@endif</td></tr>
@empty<tr><td colspan="7" class="text-center text-muted py-4">Belum ada fasilitas.</td></tr>@endforelse</tbody></table></div></div>
<div class="mt-3">{{ $items->links() }}</div>@endsection
