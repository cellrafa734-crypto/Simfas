<!doctype html><html lang="id"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>SIMFAS Kampus</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<style>:root{--bg:#7a1f2b;--iv:#faf6ee}body{background:var(--iv)}.navbar{background:var(--bg)}.btn-primary{background:var(--bg);border-color:var(--bg)}.btn-primary:hover{background:#5e1620}.card{border:0;box-shadow:0 1px 4px #0001}.stat{font-size:2rem;color:var(--bg);font-weight:700}</style></head><body>
<nav class="navbar navbar-expand-md navbar-dark mb-4"><div class="container">
<a class="navbar-brand" href="/dashboard">SIMFAS Kampus</a>
@auth<div class="navbar-nav me-auto">
<a class="nav-link" href="/dashboard">Dashboard</a><a class="nav-link" href="/facilities">Fasilitas</a>
@if(in_array(auth()->user()->role,['admin','mahasiswa','organisasi']))<a class="nav-link" href="/borrowings">Peminjaman</a>@endif
@if(auth()->user()->role==='admin')<a class="nav-link" href="/saw">Prioritas SAW</a>@endif</div>
<form method="post" action="/logout">@csrf<span class="text-white me-2">{{ auth()->user()->name }} ({{ auth()->user()->role }})</span><button class="btn btn-sm btn-outline-light">Keluar</button></form>@endauth
</div></nav>
<div class="container pb-5">
@if(session('ok'))<div class="alert alert-success">{{ session('ok') }}</div>@endif
@if(session('err'))<div class="alert alert-danger">{{ session('err') }}</div>@endif
@if($errors->any())<div class="alert alert-danger">@foreach($errors->all() as $e)<div>{{ $e }}</div>@endforeach</div>@endif
@yield('content')</div></body></html>
