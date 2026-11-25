<?php
namespace App\Http\Controllers;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
class AuthController extends Controller {
    public function login(Request $r) {
        $c = $r->validate(['email'=>'required|email','password'=>'required']);
        if (Auth::attempt($c)) { $r->session()->regenerate(); return redirect('/dashboard'); }
        return back()->withErrors(['email'=>'Email atau password salah.'])->onlyInput('email');
    }
    public function register(Request $r) {
        $d = $r->validate(['name'=>'required|max:100','email'=>'required|email|unique:users','password'=>'required|min:8|confirmed']);
        $u = User::create($d + ['role'=>'mahasiswa']);
        Auth::login($u); return redirect('/dashboard');
    }
    public function logout(Request $r) {
        Auth::logout(); $r->session()->invalidate(); $r->session()->regenerateToken(); return redirect('/login');
    }
}
