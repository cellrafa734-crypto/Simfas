@extends('layouts.app')@section('content')
<div class="card mx-auto" style="max-width:420px"><div class="card-body"><h4>Masuk</h4>
<form method="post" action="/login">@csrf
<input class="form-control mb-2" name="email" type="email" placeholder="Email" value="{{ old('email') }}" required>
<input class="form-control mb-3" name="password" type="password" placeholder="Password" required>
<button class="btn btn-primary w-100">Masuk</button></form>
<a class="d-block mt-3" href="/register">Registrasi mahasiswa</a></div></div>@endsection
