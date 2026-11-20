@extends('layouts.app')@section('content')
<div class="card"><div class="card-body"><h4>{{ $f->exists ? 'Ubah' : 'Tambah' }} Fasilitas</h4>
<form method="post" action="{{ $f->exists ? '/facilities/'.$f->id : '/facilities' }}">@csrf @if($f->exists)@method('PUT')@endif
<div class="row g-2">
@foreach(['code'=>'Kode inventaris','name'=>'Nama','category'=>'Kategori','location'=>'Lokasi','units'=>'Jumlah unit'] as $k=>$l)
<div class="col-md-6"><label>{{ $l }}</label><input class="form-control" name="{{ $k }}" value="{{ old($k, $f->$k ?? ($k==='units'?1:'')) }}" required></div>@endforeach
<div class="col-md-6"><label>Status</label><select class="form-select" name="status">@foreach(['tersedia','dipinjam','pemeliharaan','tidak_dapat_digunakan'] as $o)<option @selected(old('status',$f->status)===$o)>{{ $o }}</option>@endforeach</select></div>
<div class="col-md-6"><label>Kondisi</label><select class="form-select" name="condition">@foreach(['baik','rusak_ringan','rusak_sedang','rusak_berat'] as $o)<option @selected(old('condition',$f->condition)===$o)>{{ $o }}</option>@endforeach</select></div>
<div class="col-12"><label>Deskripsi</label><textarea class="form-control" name="description">{{ old('description',$f->description) }}</textarea></div></div>
<button class="btn btn-primary mt-3">Simpan</button> <a href="/facilities" class="btn btn-link">Batal</a></form></div></div>@endsection
