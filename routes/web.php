<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

use App\Http\Controllers\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\BorrowingController;
use App\Http\Controllers\CheckingController;
use App\Http\Controllers\CheckItemController;
use App\Http\Controllers\VehicleController;
use App\Http\Controllers\TeamController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\UseReportController;


// =======================================
// ✅ GUEST (belum login)
// =======================================
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'login'])->name('login');
    Route::post('/login', [LoginController::class, 'storelogin'])->name('login.process');
    Route::get('/', fn() => redirect()->route('login'));
});


// =======================================
// ✅ AUTH (sudah login)
// =======================================
Route::middleware('auth')->group(function () {

    // Logout
    Route::get('/logout', [LoginController::class, 'logout'])->name('logout');

    // Redirect dashboard berdasarkan role
    Route::get('/home', function () {
        return match (strtolower(Auth::user()->role)) {
            'admin'     => redirect()->route('admin.dashboard'),
            'pegawai'   => redirect()->route('pegawai.dashboard'),
            'sumda'     => redirect()->route('sumda.dashboard'),
            'ketua tim' => redirect()->route('ketuatim.dashboard'),
            default     => redirect()->route('logout'),
        };
    })->name('home');

    // =======================================
    // ✅ DASHBOARD KHUSUS PER ROLE
    // =======================================
    Route::get('/admin/dashboard', [DashboardController::class, 'admin'])
        ->name('admin.dashboard')->middleware('userAccess:admin');

    Route::get('/sumda/dashboard', [DashboardController::class, 'sumda'])
        ->name('sumda.dashboard')->middleware('userAccess:sumda');

    Route::get('/ketuatim/dashboard', [DashboardController::class, 'ketuatim'])
        ->name('ketuatim.dashboard')->middleware('userAccess:ketua tim');

    Route::get('/pegawai/dashboard', [DashboardController::class, 'pegawai'])
        ->name('pegawai.dashboard')->middleware('userAccess:pegawai');


    // =======================================
    // ✅ ROUTE UTAMA (TIDAK BERDASARKAN ROLE)
    // =======================================

    // Users → Admin Only
    Route::resource('/users', UserController::class)
        ->middleware('userAccess:admin');

    // Teams → Admin Only
    Route::resource('/teams', TeamController::class)
        ->middleware('userAccess:admin');

    // Vehicles → Admin, Sumda, Pegawai
    Route::resource('/vehicles', VehicleController::class)
        ->middleware('userAccess:admin,sumda,pegawai');

    // Borrowings
    Route::resource('/borrowings', BorrowingController::class)
        ->middleware('userAccess:admin,sumda,pegawai');

    // Checkings → Admin Only
    Route::resource('/checkings', CheckingController::class)
        ->middleware('userAccess:admin');

    // Check Item → Ketua Tim
    Route::resource('/checkitem', CheckItemController::class)
        ->middleware('userAccess:ketua tim');

    // Attendance → Ketua Tim
    Route::resource('/attendance', AttendanceController::class)
        ->middleware('userAccess:ketua tim');

    // Reports → Admin + Pegawai
    Route::get('/reports', [UseReportController::class, 'index'])
        ->name('reports.index')
        ->middleware('userAccess:admin,pegawai');
    // Reports (UseReport)
    Route::prefix('reports')->middleware('userAccess:admin,pegawai')->group(function () {
        Route::get('/', [UseReportController::class, 'index'])->name('reports.index');
        Route::get('/create/{borrow_id}', [UseReportController::class, 'create'])->name('reports.create');
        Route::post('/store/{borrow_id}', [UseReportController::class, 'store'])->name('reports.store');
        Route::get('/{id}', [UseReportController::class, 'show'])->name('reports.show');
    });
    Route::patch('/borrowings/{id}/cancel', [BorrowingController::class, 'cancel'])
    ->name('borrowings.cancel');
    Route::post('/borrowings/{id}/approve', [BorrowingController::class, 'approve'])
    ->name('borrowings.approve');
    Route::post('/borrowings/{id}/reject', [BorrowingController::class, 'reject'])
        ->name('borrowings.reject');
    // =======================================
    // ✅ USE REPORTS (Laporan Penggunaan Kendaraan)
    // =======================================
    Route::get('/usereports', [UseReportController::class, 'index'])
        ->name('usereports.index');

    Route::get('/usereports/create/{borrow_id}', [UseReportController::class, 'create'])
        ->name('usereports.create');

    Route::post('/usereports/store/{borrow_id}', [UseReportController::class, 'store'])
        ->name('usereports.store');

    Route::get('/usereports/{id}/edit', [UseReportController::class, 'edit'])
        ->name('usereports.edit');
    Route::put('/usereports/{id}', [UseReportController::class, 'update'])
        ->name('usereports.update');
    Route::get('/usereports/{id}', [UseReportController::class, 'show'])
        ->name('usereports.show');


});
