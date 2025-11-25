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
use App\Http\Controllers\OilChangeController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\BorrowReportController;


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

    // // Teams → Admin Only
    // Route::resource('/teams', TeamController::class)
    //     ->middleware('userAccess:admin');
    // // Custom routes for managing teams
    // Route::middleware('userAccess:admin')->group(function () {

    //     Route::get('/teams/manage', [TeamController::class, 'manage'])
    //         ->name('teams.manage');

    //     Route::post('/teams/manage/save', [TeamController::class, 'saveManage'])
    //         ->name('teams.manage.save');

    // });

// Custom routes for managing teams → harus sebelum resource!
Route::get('/teams/manage', [TeamController::class, 'manage'])
    ->name('teams.manage');

Route::post('/teams/manage/save', [TeamController::class, 'saveManage'])
    ->name('teams.manage.save');

// Resource route
Route::resource('/teams', TeamController::class);


    // Vehicles → Admin, Sumda, Pegawai
    Route::resource('/vehicles', VehicleController::class)
        ->middleware('userAccess:admin,sumda,pegawai');

    // Borrowings
    Route::resource('/borrowings', BorrowingController::class)
        ->middleware('userAccess:admin,sumda,pegawai');

    // Checkings → Admin Only
    Route::resource('/checkings', CheckingController::class)
        ->middleware('userAccess:admin');

    Route::get('/reports/borrow', [BorrowReportController::class, 'form'])->name('reports.borrow.form');
    Route::post('/reports/borrow/generate', [BorrowReportController::class, 'generate'])->name('reports.borrow.generate');
    // Check Item → Ketua Tim
    Route::resource('/checkitem', CheckItemController::class)
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

    // CHECKINGS
    Route::get('/checkings', [CheckingController::class, 'index'])->name('checkings.index');
    Route::get('/checkings/create', [CheckingController::class, 'create'])->name('checkings.create');
    Route::post('/checkings', [CheckingController::class, 'store'])->name('checkings.store');

    Route::get('/checkings/{id}', [CheckingController::class, 'show'])->name('checkings.show');
    Route::get('/checkings/{id}/edit', [CheckingController::class, 'edit'])->name('checkings.edit');
    Route::put('/checkings/{id}', [CheckingController::class, 'update'])->name('checkings.update');
    Route::delete('/checkings/{id}', [CheckingController::class, 'destroy'])->name('checkings.destroy');


    // CHECK ITEMS (Item pengecekan kendaraan)
    Route::get('/checkitems/{check_id}/{vehicle_id}/create', [CheckItemController::class, 'create'])->name('checkitems.create');
    Route::post('/checkitems/{check_id}/{vehicle_id}', [CheckItemController::class, 'store'])->name('checkitems.store');

    Route::get('/checkitems/{id}', [CheckItemController::class, 'show'])->name('checkitems.show');
    Route::get('/checkitems/{id}/edit', [CheckItemController::class, 'edit'])->name('checkitems.edit');
    Route::put('/checkitems/{id}', [CheckItemController::class, 'update'])->name('checkitems.update');

    // ATTENDANCES
    Route::get('/attendances/{check_id}/create', [AttendanceController::class, 'create'])->name('attendances.create');
    Route::post('/attendances/{check_id}', [AttendanceController::class, 'store'])->name('attendances.store');

    Route::get('/attendances/{id}/edit', [AttendanceController::class, 'edit'])->name('attendances.edit');
    Route::put('/attendances/{id}', [AttendanceController::class, 'update'])->name('attendances.update');

    Route::post('/vehicles/{id}/oil-change', [OilChangeController::class, 'store'])
    ->name('oilchange.store');
Route::get('/tes', function () {
    return 'OK';
});

Route::get('/schedules', [ScheduleController::class, 'index'])->name('schedules.index');

Route::post('/schedules/generate', [ScheduleController::class, 'generate'])
    ->name('schedules.generate');

Route::get('/schedules/today', [ScheduleController::class, 'today'])
    ->name('schedules.today');

Route::get('/profil', [ProfileController::class, 'show'])->name('profile.show');
Route::post('/profil/update', [ProfileController::class, 'update'])->name('profile.update');


});
