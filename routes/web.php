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
use App\Http\Controllers\NotificationController;


// ======================================================
// ⭕ GUEST (BELUM LOGIN)
// ======================================================
Route::middleware('guest')->group(function () {
    Route::get('/', fn() => redirect()->route('login'));
    Route::get('/login', [LoginController::class, 'login'])->name('login');
    Route::post('/login', [LoginController::class, 'storelogin'])->name('login.process');
});


// ======================================================
// ⭕ AUTH (SUDAH LOGIN)
// ======================================================
Route::middleware('auth')->group(function () {

    // LOGOUT
    Route::get('/logout', [LoginController::class, 'logout'])->name('logout');

    // REDIRECT DASHBOARD BERDASARKAN ROLE
    Route::get('/home', function () {
        return match (strtolower(Auth::user()->role)) {
            'admin' => redirect()->route('admin.dashboard'),
            'kepala sumber daya' => redirect()->route('kepalasumberdaya.dashboard'),
            'ketua tim' => redirect()->route('ketuatim.dashboard'),
            'pegawai' => redirect()->route('pegawai.dashboard'),
            default => redirect()->route('logout'),
        };
    })->name('home');


    // ======================================================
    // ⭕ DASHBOARD PER ROLE
    // ======================================================
    Route::middleware(['userAccess:admin'])->get('/admin/dashboard', [DashboardController::class, 'admin'])->name('admin.dashboard');
    Route::middleware(['userAccess:kepala sumber daya'])->get('/kepalasumberdaya/dashboard', [DashboardController::class, 'kepalasumberdaya'])->name('kepalasumberdaya.dashboard');
    Route::middleware(['userAccess:ketua tim'])->get('/ketuatim/dashboard', [DashboardController::class, 'ketuatim'])->name('ketuatim.dashboard');
    Route::middleware(['userAccess:pegawai'])->get('/pegawai/dashboard', [DashboardController::class, 'pegawai'])->name('pegawai.dashboard');


    // ======================================================
    // ⭕ USERS (HANYA ADMIN)
    // ======================================================
    Route::resource('/users', UserController::class)->middleware(['userAccess:admin']);


    // ======================================================
    // ⭕ PROFILE (semua role bisa edit profil sendiri)
    // ======================================================
    Route::get('/profil', [ProfileController::class, 'show'])->name('profile.show');
    Route::post('/profil/update', [ProfileController::class, 'update'])->name('profile.update');


    // ======================================================
    // ⭕ TEAMS
    // Admin + Kepala Sumber Daya → full access
    // Ketua Tim + Pegawai → hanya index + show
    // ======================================================
    Route::middleware(['userAccess:admin,kepala sumber daya'])->group(function () {

        // PERHATIKAN: route spesifik dulu
        Route::get('/teams/create', [TeamController::class, 'create'])->name('teams.create');
        Route::get('/teams/manage', [TeamController::class, 'manage'])->name('teams.manage');
        Route::post('/teams/manage/save', [TeamController::class, 'saveManage'])->name('teams.manage.save');

        // Resource actions
        Route::post('/teams', [TeamController::class, 'store'])->name('teams.store');
        Route::get('/teams/{id}/edit', [TeamController::class, 'edit'])->name('teams.edit');
        Route::put('/teams/{id}', [TeamController::class, 'update'])->name('teams.update');
        Route::delete('/teams/{id}', [TeamController::class, 'destroy'])->name('teams.destroy');
    });
    Route::get('/teams', [TeamController::class, 'index'])
        ->middleware(['userAccess:admin,kepala sumber daya,ketua tim,pegawai'])
        ->name('teams.index');

    Route::get('/teams/{id}', [TeamController::class, 'show'])
        ->middleware(['userAccess:admin,kepala sumber daya,ketua tim,pegawai'])
        ->name('teams.show');


    // ======================================================
    // ⭕ VEHICLES
    // ======================================================
    Route::middleware(['userAccess:admin,kepala sumber daya,ketua tim,pegawai'])->group(function () {
        Route::get('/vehicles', [VehicleController::class, 'index'])->name('vehicles.index');
        Route::get('/vehicles/{vehicle}', [VehicleController::class, 'show'])->name('vehicles.show');
        Route::put('/vehicles/{vehicle}', [VehicleController::class, 'update'])->name('vehicles.update');
    });

    Route::middleware(['userAccess:admin,kepala sumber daya'])->group(function () {
        Route::get('/vehicles/create', [VehicleController::class, 'create'])->name('vehicles.create');
        Route::post('/vehicles', [VehicleController::class, 'store'])->name('vehicles.store');
        Route::get('/vehicles/{vehicle}/edit', [VehicleController::class, 'edit'])->name('vehicles.edit');
        Route::delete('/vehicles/{vehicle}', [VehicleController::class, 'destroy'])->name('vehicles.destroy');
    });

    Route::post('/vehicles/{id}/oil-change', [OilChangeController::class, 'store'])
        ->middleware(['userAccess:admin,kepala sumber daya,ketua tim,pegawai'])
        ->name('oilchange.store');


    // ======================================================
    // ⭕ BORROWINGS
    // ======================================================
    Route::resource('/borrowings', BorrowingController::class)
        ->middleware(['userAccess:admin,kepala sumber daya,ketua tim,pegawai']);

    Route::patch('/borrowings/{id}/cancel', [BorrowingController::class, 'cancel'])
        ->name('borrowings.cancel');

    Route::post('/borrowings/{id}/approve', [BorrowingController::class, 'approve'])
        ->middleware(['userAccess:admin,kepala sumber daya'])
        ->name('borrowings.approve');

    Route::post('/borrowings/{id}/reject', [BorrowingController::class, 'reject'])
        ->middleware(['userAccess:admin,kepala sumber daya'])
        ->name('borrowings.reject');


    // ======================================================
    // ⭕ USE REPORTS
    // ======================================================
    Route::prefix('usereports')->group(function () {
        Route::get('/', [UseReportController::class, 'index'])->name('usereports.index');
        Route::get('/create/{borrow_id}', [UseReportController::class, 'create'])->name('usereports.create');
        Route::post('/store/{borrow_id}', [UseReportController::class, 'store'])->name('usereports.store');
        Route::get('/{id}/edit', [UseReportController::class, 'edit'])->name('usereports.edit');
        Route::put('/{id}', [UseReportController::class, 'update'])->name('usereports.update');
        Route::get('/{id}', [UseReportController::class, 'show'])->name('usereports.show');
    });


    // ======================================================
    // ⭕ BORROW REPORTS (PDF, Excel, CSV, JSON)
    // ======================================================
    Route::get('/reports/borrow', [BorrowReportController::class, 'form'])
        ->name('reports.borrow.form');

    Route::post('/reports/borrow/generate', [BorrowReportController::class, 'generate'])
        ->name('reports.borrow.generate');


    // ======================================================
    // ⭕ CHECKINGS
    // ======================================================
    Route::middleware(['userAccess:admin,kepala sumber daya,ketua tim,pegawai'])->group(function () {
        Route::get('/checkings', [CheckingController::class, 'index'])->name('checkings.index');
        Route::get('/checkings/{checking}', [CheckingController::class, 'show'])->name('checkings.show');
        Route::get('/checkings/create', [CheckingController::class, 'create'])
            ->middleware('userAccess:ketua tim')
            ->name('checkings.create');
        Route::post('/checkings', [CheckingController::class, 'store'])
            ->middleware('userAccess:ketua tim')
            ->name('checkings.store');
        Route::get('/checkings/{checking}/edit', [CheckingController::class, 'edit'])
            ->middleware('userAccess:pegawai,ketua tim')
            ->name('checkings.edit');
        Route::put('/checkings/{checking}', [CheckingController::class, 'update'])
            ->middleware('userAccess:pegawai,ketua tim')
            ->name('checkings.update');
        Route::delete('/checkings/{checking}', [CheckingController::class, 'destroy'])
            ->middleware('userAccess:ketua tim')
            ->name('checkings.destroy');
    });


    // ======================================================
    // ⭕ CHECK ITEM
    // ======================================================
    Route::middleware(['userAccess:pegawai,ketua tim,admin,kepala sumber daya'])->group(function () {

        // Detail laporan (SHOW)
        Route::get('/checkitems/{id}', [CheckItemController::class, 'show'])
            ->name('checkitems.show');

        // Form edit
        Route::get('/checkitems/{id}/edit', [CheckItemController::class, 'edit'])
            ->middleware('userAccess:pegawai,ketua tim')
            ->name('checkitems.edit');

        // Update hasil cek → HARUS PUT/PATCH
        Route::put('/checkitems/{id}', [CheckItemController::class, 'update'])
            ->middleware('userAccess:pegawai,ketua tim')
            ->name('checkitems.update');

        // Hapus (jika kamu butuh destroy)
        Route::delete('/checkitems/{id}', [CheckItemController::class, 'destroy'])
            ->middleware('userAccess:pegawai,ketua tim')
            ->name('checkitems.destroy');
    });


    // ======================================================
    // ⭕ ATTENDANCES
    // ======================================================
    Route::middleware(['userAccess:ketua tim'])->group(function () {

        // CREATE FORM
        Route::get('/attendances/create/{check_id}', [AttendanceController::class, 'create'])
            ->name('attendances.create');

        // STORE (SAVE)
        Route::post('/attendances/{check_id}', [AttendanceController::class, 'store'])
            ->name('attendances.store');

        // EDIT FORM
        Route::get('/attendances/{check_id}/edit', [AttendanceController::class, 'edit'])
            ->name('attendances.edit');

        // UPDATE (SAVE PERUBAHAN)
        Route::put('/attendances/{check_id}', [AttendanceController::class, 'update'])
            ->name('attendances.update');
    });


    // ======================================================
    // ⭕ SCHEDULES
    // ======================================================
    Route::middleware(['userAccess:admin,kepala sumber daya'])->group(function () {
        Route::post('/schedules/generate', [ScheduleController::class, 'generate'])->name('schedules.generate');
    });

    Route::get('/schedules', [ScheduleController::class, 'index'])
        ->middleware(['userAccess:admin,kepala sumber daya,ketua tim,pegawai'])
        ->name('schedules.index');

    Route::get('/schedules/today', [ScheduleController::class, 'today'])
        ->middleware(['userAccess:admin,kepala sumber daya,ketua tim,pegawai'])
        ->name('schedules.today');

    // ======================================================
    // ⭕ NOTIFICATIONS
    // ======================================================
    Route::get('/notifications/read/{id}', [NotificationController::class, 'markRead'])
    ->name('notifications.read');

    Route::post('/notifications/read-all', [NotificationController::class, 'markAll'])
        ->name('notifications.read-all');
});
