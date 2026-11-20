@extends('layouts.app')@section('content')
<h4>Prioritas Pemeliharaan (SAW)</h4>
<p class="text-muted">Hasil berupa rekomendasi; keputusan tetap pada admin. Skala nilai 1–5.</p>
<div class="card mb-3"><div class="card-body"><h6>Bobot kriteria (total saat ini: {{ $sum }})</h6>
<form method="post" action="/saw/weights">@csrf<div class="row g-2">
@foreach($criteria as $c)<div class="col-md-3"><label>{{ $c->name }} ({{ $c->type }})</label><input class="form-control" name="weights[{{ $c->id }}]" value="{{ $c->weight }}"></div>@endforeach</div>
<button class="btn btn-primary mt-2">Simpan bobot</button></form></div></div>
<div class="card mb-3"><div class="card-body"><h6>Input penilaian</h6>
<form method="post" action="/saw/score">@csrf<div class="row g-2">
<div class="col-md-3"><select class="form-select" name="facility_id">@foreach($all as $f)<option value="{{ $f->id }}">{{ $f->name }}</option>@endforeach</select></div>
@foreach($criteria as $c)<div class="col-md-2"><input class="form-control" type="number" step="0.1" min="1" max="5" name="values[{{ $c->id }}]" placeholder="{{ $c->name }}" required></div>@endforeach
<div class="col-md-1"><button class="btn btn-primary">OK</button></div></div></form></div></div>
@php $tables=['Matriks keputusan (X)'=>$X,'Matriks ternormalisasi (R)'=>$R]; @endphp
@foreach($tables as $title=>$M)<div class="card mb-3"><div class="card-body"><h6>{{ $title }}</h6><div class="table-responsive"><table class="table table-sm"><thead><tr><th>Fasilitas</th>@foreach($criteria as $c)<th>{{ $c->name }}</th>@endforeach</tr></thead><tbody>
@foreach($facilities as $f)<tr><td>{{ $f->name }}</td>@foreach($criteria as $c)<td>{{ round($M[$f->id][$c->id],4) }}</td>@endforeach</tr>@endforeach</tbody></table></div></div></div>@endforeach
<div class="card"><div class="card-body"><h6>Perankingan (V = Σ w·r)</h6><table class="table table-sm"><thead><tr><th>Rank</th><th>Fasilitas</th><th>Nilai V</th></tr></thead><tbody>
@php $i=1; @endphp @foreach($V as $fid=>$v)<tr><td>{{ $i++ }}</td><td>{{ $facilities->firstWhere('id',$fid)->name }}</td><td>{{ round($v,4) }}</td></tr>@endforeach</tbody></table></div></div>
@endsection
