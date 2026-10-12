<?php

use App\Http\Controllers\Admin;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\PasswordResetController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Customer;
use App\Http\Controllers\LandingController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| SIMTAG - Sistem Informasi Survei PDAM Tirta Pinang Kota Pangkalpinang
|--------------------------------------------------------------------------
*/

// ------------------------------------------------------------------
// Publik: landing page & autentikasi
// ------------------------------------------------------------------
Route::get('/', [LandingController::class, 'index'])->name('home');

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'create'])->name('login');
    Route::post('/login', [LoginController::class, 'store'])->middleware('throttle:10,1')->name('login.store');

    Route::get('/register', [RegisterController::class, 'create'])->name('register');
    Route::post('/register', [RegisterController::class, 'store'])->middleware('throttle:5,1')->name('register.store');

    Route::get('/lupa-password', [PasswordResetController::class, 'request'])->name('password.request');
    Route::post('/lupa-password', [PasswordResetController::class, 'sendOtp'])->middleware('throttle:5,1')->name('password.email');
    Route::get('/reset-password', [PasswordResetController::class, 'reset'])->name('password.reset');
    Route::post('/reset-password', [PasswordResetController::class, 'update'])->middleware('throttle:10,1')->name('password.update');
});

Route::post('/logout', [LoginController::class, 'destroy'])->middleware('auth')->name('logout');

// ------------------------------------------------------------------
// Pelanggan
// ------------------------------------------------------------------
Route::prefix('app')->name('app.')->middleware(['auth', 'role:customer'])->group(function () {
    Route::get('/', fn () => redirect()->route('app.dashboard'));
    Route::get('/dashboard', [Customer\DashboardController::class, 'index'])->name('dashboard');

    Route::get('/survei', [Customer\SurveyController::class, 'index'])->name('surveys.index');
    Route::get('/survei/{survey}', [Customer\SurveyController::class, 'show'])->name('surveys.show');
    Route::post('/survei/{survey}', [Customer\SurveyController::class, 'store'])->name('surveys.store');

    Route::get('/pengaduan', [Customer\ComplaintController::class, 'index'])->name('complaints.index');
    Route::get('/pengaduan/buat', [Customer\ComplaintController::class, 'create'])->name('complaints.create');
    Route::post('/pengaduan', [Customer\ComplaintController::class, 'store'])->middleware('throttle:10,1')->name('complaints.store');
    Route::get('/pengaduan/{complaint}', [Customer\ComplaintController::class, 'show'])->name('complaints.show');

    Route::get('/riwayat', [Customer\HistoryController::class, 'index'])->name('history');

    Route::get('/profil', [Customer\ProfileController::class, 'edit'])->name('profile');
    Route::put('/profil', [Customer\ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profil/password', [Customer\ProfileController::class, 'password'])->name('profile.password');
});

// ------------------------------------------------------------------
// Admin & Petugas
// ------------------------------------------------------------------
Route::prefix('admin')->name('admin.')->middleware(['auth', 'role:admin,staff'])->group(function () {
    Route::get('/', fn () => redirect()->route('admin.dashboard'));
    Route::get('/dashboard', [Admin\DashboardController::class, 'index'])->name('dashboard');

    // Pelanggan
    Route::resource('pelanggan', Admin\CustomerController::class)
        ->parameters(['pelanggan' => 'customer'])
        ->names('customers');
    Route::post('/pelanggan/{customer}/reset-password', [Admin\CustomerController::class, 'resetPassword'])->name('customers.reset-password');

    // Survei
    Route::resource('survei', Admin\SurveyController::class)
        ->parameters(['survei' => 'survey'])
        ->names('surveys');
    Route::post('/survei/{survey}/publish', [Admin\SurveyController::class, 'publish'])->name('surveys.publish');
    Route::post('/survei/{survey}/close', [Admin\SurveyController::class, 'close'])->name('surveys.close');
    Route::post('/survei/{survey}/duplicate', [Admin\SurveyController::class, 'duplicate'])->name('surveys.duplicate');
    Route::get('/survei/{survey}/hasil', [Admin\SurveyResultController::class, 'show'])->name('surveys.results');
    Route::get('/survei/{survey}/export', [Admin\SurveyResultController::class, 'export'])->name('surveys.export');
    Route::get('/survei/{survey}/cetak', [Admin\SurveyResultController::class, 'print'])->name('surveys.print');

    // Pertanyaan survei
    Route::post('/survei/{survey}/pertanyaan', [Admin\SurveyQuestionController::class, 'store'])->name('surveys.questions.store');
    Route::put('/survei/{survey}/pertanyaan/{question}', [Admin\SurveyQuestionController::class, 'update'])->name('surveys.questions.update')->scopeBindings();
    Route::delete('/survei/{survey}/pertanyaan/{question}', [Admin\SurveyQuestionController::class, 'destroy'])->name('surveys.questions.destroy')->scopeBindings();
    Route::post('/survei/{survey}/pertanyaan/{question}/pindah/{direction}', [Admin\SurveyQuestionController::class, 'move'])
        ->whereIn('direction', ['up', 'down'])->name('surveys.questions.move')->scopeBindings();

    // Jawaban responden
    Route::get('/survei/{survey}/respon/{response}', [Admin\SurveyResultController::class, 'response'])->name('surveys.responses.show')->scopeBindings();
    Route::delete('/survei/{survey}/respon/{response}', [Admin\SurveyResultController::class, 'destroyResponse'])->name('surveys.responses.destroy')->scopeBindings();

    // Pengaduan
    Route::get('/pengaduan', [Admin\ComplaintController::class, 'index'])->name('complaints.index');
    Route::get('/pengaduan/{complaint}', [Admin\ComplaintController::class, 'show'])->name('complaints.show');
    Route::put('/pengaduan/{complaint}', [Admin\ComplaintController::class, 'update'])->name('complaints.update');
    Route::delete('/pengaduan/{complaint}', [Admin\ComplaintController::class, 'destroy'])->name('complaints.destroy');

    // Laporan kepuasan
    Route::get('/laporan', [Admin\ReportController::class, 'index'])->name('reports.index');
    Route::get('/laporan/export', [Admin\ReportController::class, 'export'])->name('reports.export');

    // Profil akun sendiri
    Route::get('/profil', [Admin\ProfileController::class, 'edit'])->name('profile');
    Route::put('/profil', [Admin\ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profil/password', [Admin\ProfileController::class, 'password'])->name('profile.password');

    // Khusus administrator
    Route::middleware('role:admin')->group(function () {
        Route::resource('pengguna', Admin\UserController::class)
            ->parameters(['pengguna' => 'user'])
            ->except('show')
            ->names('users');
        Route::get('/log-aktivitas', [Admin\ActivityLogController::class, 'index'])->name('logs.index');
    });
});
