<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\CounselorController;
use App\Http\Controllers\Admin\TokenController;
use App\Http\Controllers\Admin\MindfulnessAudioController;
use App\Http\Controllers\Admin\EducationController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\PhqQuestionController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('/counselors', [CounselorController::class, 'index'])->name('counselors.index');
    Route::post('/counselors', [CounselorController::class, 'store'])->name('counselors.store');
    Route::delete('/counselors/{id}', [CounselorController::class, 'destroy'])->name('counselors.destroy');
    Route::get('/tokens', [TokenController::class, 'index'])->name('tokens.index');
    Route::post('/tokens', [TokenController::class, 'store'])->name('tokens.store');
    Route::delete('/tokens/{id}', [TokenController::class, 'destroy'])->name('tokens.destroy');
    Route::resource('audio', MindfulnessAudioController::class);
    Route::resource('education', EducationController::class);
    Route::resource('phq-questions', PhqQuestionController::class);
});

require __DIR__ . '/auth.php';
