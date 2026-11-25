<?php
use App\Http\Controllers\{AuthController, DashboardController, FacilityController, BorrowingController, SawController};
use App\Http\Middleware\RoleMiddleware as Role;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('landing');
});
Route::middleware('guest')->group(function () {
    Route::view('/login','auth.login')->name('login');
    Route::post('/login',[AuthController::class,'login']);
    Route::view('/register','auth.register');
    Route::post('/register',[AuthController::class,'register']);
});
Route::middleware('auth')->group(function () {
    Route::post('/logout',[AuthController::class,'logout']);
    Route::get('/dashboard',[DashboardController::class,'index']);
    Route::get('/facilities',[FacilityController::class,'index'])->name('facilities.index');
    Route::middleware(Role::class.':admin')->group(function () {
        Route::resource('facilities', FacilityController::class)->except(['index','show']);
        Route::post('/borrowings/{borrowing}/approve',[BorrowingController::class,'approve']);
        Route::post('/borrowings/{borrowing}/reject',[BorrowingController::class,'reject']);
        Route::get('/saw',[SawController::class,'index']);
        Route::post('/saw/weights',[SawController::class,'weights']);
        Route::post('/saw/score',[SawController::class,'score']);
    });
    // DAFTAR PEMINJAMAN
    Route::middleware(Role::class.':admin,mahasiswa,organisasi')
        ->get('/borrowings', [BorrowingController::class, 'index'])
        ->name('borrowings.index');

    // FORM PENGAJUAN — HARUS SEBELUM ROUTE DETAIL
    Route::middleware(Role::class.':mahasiswa,organisasi')->group(function () {
        Route::get('/borrowings/create', [BorrowingController::class, 'create']);
        Route::post('/borrowings', [BorrowingController::class, 'store']);
        Route::post('/borrowings/{borrowing}/cancel', [BorrowingController::class, 'cancel']);
    });

    // DETAIL PEMINJAMAN
    Route::middleware(Role::class.':admin,mahasiswa,organisasi')
        ->get('/borrowings/{borrowing}', [BorrowingController::class, 'show'])
        ->name('borrowings.show');
});
