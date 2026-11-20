@extends('layouts.app')@section('content')
<h4 class="mb-3">Dashboard {{ ucfirst(auth()->user()->role) }}</h4>
<div class="row g-3">@foreach($stats as $k=>$v)<div class="col-6 col-md-4"><div class="card"><div class="card-body"><div class="text-muted">{{ $k }}</div><div class="stat">{{ $v }}</div></div></div></div>@endforeach</div>
@if(auth()->user()->role==='petugas')<div class="alert alert-info mt-4">Modul tugas pemeliharaan petugas belum diimplementasikan pada versi ini.</div>@endif
@endsection
