@extends('layouts.app')@section('content')
<div class="card"><div class="card-body"><h4>Ajukan Peminjaman</h4>
<form method="post" action="/borrowings">@csrf<div class="row g-2">
<div class="col-md-6"><label>Fasilitas</label><select class="form-select" name="facility_id">@foreach($facilities as $f)<option value="{{ $f->id }}" @selected(old('facility_id')==$f->id)>{{ $f->name }} ({{ $f->units }} unit)</option>@endforeach</select></div>
<div class="col-md-6"><label>Jumlah unit</label><input class="form-control" type="number" min="1" name="units" value="{{ old('units',1) }}"></div>
<div class="col-md-6"><label>Mulai</label><input class="form-control" type="datetime-local" name="start_at" value="{{ old('start_at') }}" required></div>
<div class="col-md-6"><label>Selesai</label><input class="form-control" type="datetime-local" name="end_at" value="{{ old('end_at') }}" required></div>
<div class="col-12"><label>Tujuan / nama kegiatan</label><input class="form-control" name="purpose" value="{{ old('purpose') }}" required></div></div>
<button class="btn btn-primary mt-3">Kirim</button></form></div></div>@endsection
