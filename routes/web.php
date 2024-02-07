<?php

use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
 */
// Add this to routes/web.php
Route::get('/flush-sessions', function () {
    session()->forget(['isEmailVerified', 'email_otp', 'isEmailVerified_expires_at', 'email_otp_expires_at']);
    return response()->json(['success' => 'Sessions flushed successfully.']);
});

Route::get('/', function () {
    return view('welcome');
});
Route::post('user/submit', [UserController::class, 'submit'])->name('user.submit');
Route::post('sendemail/otp', [UserController::class, 'emailOTP'])->name('otp.email');
Route::post('verifyemail/otp', [UserController::class, 'verifyemailOTP'])->name('verify.otpemail');
Route::post('sendphone/otp', [UserController::class, 'phoneOTP'])->name('otp.phone');

Route::post('resend/otp', [UserController::class, 'resendOTP'])->name('otp.resend');
