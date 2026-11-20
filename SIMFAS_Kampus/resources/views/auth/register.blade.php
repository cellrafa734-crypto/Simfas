@extends('layouts.app')@section('content')
<div class="card mx-auto" style="max-width:420px"><div class="card-body"><h4>Registrasi Mahasiswa</h4>
<form method="post" action="/register">@csrf
<input class="form-control mb-2" name="name" placeholder="Nama" value="{{ old('name') }}" required>
<input class="form-control mb-2" name="email" type="email" placeholder="Email" value="{{ old('email') }}" required>
<input class="form-control mb-2" name="password" type="password" placeholder="Password (min. 8)" required>
<input class="form-control mb-3" name="password_confirmation" type="password" placeholder="Ulangi password" required>
<button class="btn btn-primary w-100">Daftar</button></form></div></div>@endsection
