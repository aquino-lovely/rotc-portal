<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\CadetProfileController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Auth\ChangePasswordController;
use App\Http\Controllers\Cadet\ProfileController as CadetOwnProfileController;
use App\Http\Controllers\Cadet\DashboardController as CadetDashboardController;
use App\Http\Controllers\Cadet\EnrollmentController as CadetEnrollmentController;
use App\Http\Controllers\Admin\EnrollmentController as AdminEnrollmentController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\PerformanceController;
use App\Http\Controllers\Admin\SerialNumberController;
use App\Http\Controllers\Admin\QrAttendanceController;
use App\Http\Controllers\Cadet\QrScanController;
use App\Http\Controllers\Admin\AttendanceController;
use App\Http\Controllers\Cadet\AttendanceController as CadetAttendanceController;
use App\Http\Controllers\Cadet\GradeController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\StaffAccountController;
use App\Http\Controllers\GmaDashboardController;
use App\Http\Controllers\S1DashboardController;
use App\Http\Controllers\AdminController;

Route::get('/dashboard', function () {
    $user = auth()->user();

    return match ($user->role) {
        'super_admin' => redirect()->route('staff.index'),
        'admin'       => redirect()->route('admin.dashboard'),
        'S1'          => redirect()->route('s1.dashboard'),
        'GMA'         => redirect()->route('gma.dashboard'),
        default => app(CadetDashboardController::class)->index(request()),    };
})->name('dashboard');

// Profile routes
Route::middleware(['auth', 'force.password'])->group(function () {
    Route::get('/my-profile', [CadetProfileController::class, 'show'])->name('cadet.profile');
    Route::get('/my-profile', [CadetProfileController::class, 'showOwn'])->name('cadet.profile');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('/my-profile/edit', [CadetOwnProfileController::class, 'edit'])->name('cadet.profile.edit');
    Route::patch('/my-profile', [CadetOwnProfileController::class, 'update'])->name('cadet.profile.update');
    Route::get('/scan', [QrScanController::class, 'index'])->name('cadet.scan');
    Route::post('/scan/process', [QrScanController::class, 'process'])->name('cadet.scan.process');
});

// Change password
Route::middleware('auth')->group(function () {
    Route::get('/change-password',  [ChangePasswordController::class, 'show'])->name('password.change');
    Route::post('/change-password', [ChangePasswordController::class, 'update'])->name('password.update');
});

require __DIR__.'/auth.php';

// Admin-only routes
Route::middleware(['auth', 'role:admin'])->prefix('admin')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');
    Route::get('/serial-numbers', [SerialNumberController::class, 'index'])->name('admin.serial.index');
    Route::post('/serial-numbers', [SerialNumberController::class, 'update'])->name('admin.serial.update');
    Route::get('/reports', [ReportController::class, 'index'])->name('admin.reports.index');
    Route::post('/reports/generate', [ReportController::class, 'generate'])->name('admin.reports.generate');
    Route::post('/enrollment/manual/{user}', [AdminEnrollmentController::class, 'enrollManually'])->name('admin.enrollment.manual');
    Route::post('/enrollment-settings/{setting}/finalize', [AdminEnrollmentController::class, 'finalizeTerm'])->name('admin.enrollment.settings.finalize');
Route::post('/enrollment-settings/{setting}/unfinalize', [AdminEnrollmentController::class, 'unfinalizeTerm'])->name('admin.enrollment.settings.unfinalize');
    
});
Route::middleware(['auth', 'role:admin,S1'])->prefix('admin')->group(function () {
    Route::get('/cadets', [CadetProfileController::class, 'index'])->name('admin.cadets.index');
    Route::get('/cadets/{user}', [CadetProfileController::class, 'show'])->name('admin.cadets.show');
    Route::post('/cadets/{user}/approve', [CadetProfileController::class, 'approve'])->name('admin.cadets.approve');
    Route::post('/cadets/{user}/reject', [CadetProfileController::class, 'reject'])->name('admin.cadets.reject');
    Route::post('/cadets/{user}/rank-company', [CadetProfileController::class, 'updateRankCompany'])->name('admin.cadets.rank-company');

    Route::get('/attendance', [AttendanceController::class, 'index'])->name('admin.attendance.index');
    Route::post('/attendance/{userId}/status', [AttendanceController::class, 'updateStatus'])->name('admin.attendance.updateStatus');
    Route::get('/excuse-requests', [AttendanceController::class, 'excuseRequests'])->name('admin.attendance.excuses');
    Route::post('/excuse-requests/{excuseRequest}/approve', [AttendanceController::class, 'approveExcuse'])->name('admin.attendance.excuses.approve');
    Route::post('/excuse-requests/{excuseRequest}/reject', [AttendanceController::class, 'rejectExcuse'])->name('admin.attendance.excuses.reject');
});


// QR Attendance — admin, GMA, AND S1 (only definition, dupe removed)
Route::middleware(['auth', 'role:admin,GMA,S1'])->prefix('admin')->group(function () {
    Route::get('/performance', [PerformanceController::class, 'index'])->name('admin.performance.index');
    Route::get('/qr-attendance', [QrAttendanceController::class, 'index'])->name('admin.qr.index');
    Route::post('/qr-attendance/generate', [QrAttendanceController::class, 'generate'])->name('admin.qr.generate');
    Route::post('/qr-attendance/close', [QrAttendanceController::class, 'close'])->name('admin.qr.close');
    Route::get('/qr-attendance/scans', [QrAttendanceController::class, 'scans'])->name('admin.qr.scans');
});

Route::middleware(['auth', 'force.password'])->group(function () {
    Route::get('/enrollment', [CadetEnrollmentController::class, 'index'])->name('cadet.enrollment');
    Route::post('/enrollment', [CadetEnrollmentController::class, 'store'])->name('cadet.enrollment.store');
    Route::get('/my-attendance', [CadetAttendanceController::class, 'index'])->name('cadet.attendance');
    Route::post('/my-attendance/excuse', [CadetAttendanceController::class, 'submitExcuse'])->name('cadet.attendance.excuse');
    Route::get('/my-grades', [GradeController::class, 'index'])->name('cadet.grades');
});

Route::middleware(['auth', 'force.password', 'role:GMA'])->group(function () {
    Route::get('/gma/dashboard', [GmaDashboardController::class, 'index'])->name('gma.dashboard');
});

// Shared: Physical Fitness (PFT) — admin AND GMA
Route::middleware(['auth', 'force.password', 'role:admin,GMA'])->group(function () {
    Route::get('/performance', [PerformanceController::class, 'index'])->name('admin.performance.index');
    Route::post('/performance/pft', [PerformanceController::class, 'savePft'])->name('admin.performance.savePft');
    
});

Route::middleware(['auth', 'force.password', 'role:S1'])->group(function () {
    Route::get('/s1/dashboard', [S1DashboardController::class, 'index'])->name('s1.dashboard');
});

// Admin enrollment routes — admin AND S1
Route::middleware(['auth', 'role:admin,S1'])->prefix('admin')->group(function () {
    Route::get('/enrollment', [AdminEnrollmentController::class, 'index'])->name('admin.enrollment.index');
    Route::get('/enrollment/{enrollment}', [AdminEnrollmentController::class, 'show'])->name('admin.enrollment.show');
    Route::post('/enrollment/{enrollment}/finalize', [AdminEnrollmentController::class, 'finalize'])->name('admin.enrollment.finalize');
    Route::post('/enrollment/{enrollment}/reject', [AdminEnrollmentController::class, 'reject'])->name('admin.enrollment.reject');
    Route::get('/enrollment-settings', [AdminEnrollmentController::class, 'settings'])->name('admin.enrollment.settings');
    Route::put('/enrollment-settings/{setting}', [AdminEnrollmentController::class, 'updateSetting'])->name('admin.enrollment.settings.update');
    Route::post('/enrollment-settings', [AdminEnrollmentController::class, 'saveSettings'])->name('admin.enrollment.settings.save');
    Route::post('/performance/quizzes', [PerformanceController::class, 'saveQuizzes'])->name('admin.performance.saveQuizzes');
    Route::post('/performance/quizzes/add', [PerformanceController::class, 'addQuiz'])->name('admin.performance.addQuiz');
    Route::post('/performance/grades', [PerformanceController::class, 'saveGrades'])->name('admin.performance.saveGrades');
});

Route::middleware(['auth', 'role:super_admin'])->group(function () {
    Route::get('/staff', [StaffAccountController::class, 'index'])->name('staff.index');
    Route::get('/staff/create', [StaffAccountController::class, 'create'])->name('staff.create');
    Route::post('/staff', [StaffAccountController::class, 'store'])->name('staff.store');
    Route::delete('/staff/{staff}', [StaffAccountController::class, 'destroy'])->name('staff.destroy');     
     Route::post('/staff/{user}/reset-password', [StaffAccountController::class, 'resetPassword'])->name('staff.reset-password');
});

