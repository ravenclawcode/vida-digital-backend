<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CounselorController;
use App\Http\Controllers\Api\AudioController;
use App\Http\Controllers\Api\EducationController;
use App\Http\Controllers\Api\MedicationController;
use App\Http\Controllers\Api\MoodController;
use App\Http\Controllers\Api\HomeController;
use App\Http\Controllers\Api\CommunityController;
use App\Http\Controllers\Api\ChatbotController;
use App\Http\Controllers\Api\PhqCodeController;
use App\Http\Controllers\Api\PhqController;

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    Route::get('/user', [AuthController::class, 'getUser']);
    Route::post('/user/update-profile', [AuthController::class, 'updateProfile']);
    Route::post('/user/change-password', [AuthController::class, 'changePassword']);
    Route::post('/education/{id}/like', [EducationController::class, 'like']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/medications/today', [MedicationController::class, 'getDailySchedule']);
    Route::post('/medications', [MedicationController::class, 'store']);
    Route::post('/medications/{id}/status', [MedicationController::class, 'updateStatus']);
    Route::put('/medications/{id}', [MedicationController::class, 'update']);
    Route::delete('/medications/{id}', [MedicationController::class, 'destroy']);
    Route::get('/moods/weekly', [MoodController::class, 'getWeeklySummary']);
    Route::post('/moods', [MoodController::class, 'store']);
    Route::delete('/moods/{id}', [MoodController::class, 'destroy']);
    Route::get('/audio', [AudioController::class, 'index']);
    Route::get('/home', [HomeController::class, 'getDashboard']);
    Route::get('/community', [CommunityController::class, 'index']);
    Route::post('/community', [CommunityController::class, 'store']);
    Route::delete('/community/{id}', [CommunityController::class, 'destroy']);
    Route::post('/community/{id}/like', [CommunityController::class, 'toggleLike']);
    Route::post('/community/{id}/comment', [CommunityController::class, 'storeComment']);
    Route::get('/chat/history', [ChatbotController::class, 'getHistory']);
    Route::post('/chat/send', [ChatbotController::class, 'sendMessage']);
    Route::delete('/chat/clear', [ChatbotController::class, 'clearHistory']);
    Route::post('/phq-generate', [PhqCodeController::class, 'generate']);
    Route::post('/phq-validate', [PhqCodeController::class, 'validateCode']);
    Route::get('/phq-questions', [PhqController::class, 'getQuestionsForApp']);
    Route::post('/phq-mark-used', [PhqCodeController::class, 'markAsUsed']);
});

Route::post('/validate-token', [AuthController::class, 'checkToken']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);
Route::get('/counselors', [CounselorController::class, 'index']);
Route::get('/education', [EducationController::class, 'index']);
Route::post('/password/send-otp', [AuthController::class, 'sendOtp']);
Route::post('/password/verify-otp', [AuthController::class, 'verifyOtp']);
Route::post('/password/reset', [AuthController::class, 'resetPassword']);
