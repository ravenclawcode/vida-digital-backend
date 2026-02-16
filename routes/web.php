<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\CounselorController;
use App\Http\Controllers\Admin\TokenController;
use App\Http\Controllers\Admin\MindfulnessAudioController;
use App\Http\Controllers\Admin\EducationController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\PhqQuestionController;
use App\Http\Controllers\Admin\SupervisorManagementController;
use App\Http\Controllers\Supervisor\SupervisorController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/dashboard', [AdminController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware(['auth'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth', 'role:1'])->group(function () {
    Route::get('/counselors', [CounselorController::class, 'index'])->name('counselors.index');
    Route::post('/counselors', [CounselorController::class, 'store'])->name('counselors.store');
    Route::delete('/counselors/{id}', [CounselorController::class, 'destroy'])->name('counselors.destroy');
    Route::get('/tokens', [TokenController::class, 'index'])->name('tokens.index');
    Route::post('/tokens', [TokenController::class, 'store'])->name('tokens.store');
    Route::delete('/tokens/{id}', [TokenController::class, 'destroy'])->name('tokens.destroy');
    Route::resource('audio', MindfulnessAudioController::class);
    Route::resource('education', EducationController::class);
    Route::resource('phq-questions', PhqQuestionController::class);
    Route::get('/supervisor-management', [SupervisorManagementController::class, 'index'])->name('supervisor-management.index');
    Route::post('/supervisor-management', [SupervisorManagementController::class, 'store'])->name('supervisor-management.store');
    Route::delete('/supervisor-management/{id}', [SupervisorManagementController::class, 'destroy'])->name('supervisor-management.destroy');
});

Route::middleware(['auth', 'role:4'])->prefix('supervisor')->name('supervisor.')->group(function () {
    Route::get('/dashboard', [SupervisorController::class, 'dashboard'])->name('dashboard');
    Route::get('/monitoring-chat', [SupervisorController::class, 'monitoringChat'])->name('monitoring-chat');
    Route::get('/catatan-soap', [SupervisorController::class, 'catatanSoap'])->name('catatan-soap');
});

require __DIR__ . '/auth.php';
