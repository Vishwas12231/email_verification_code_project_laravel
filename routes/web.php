<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;

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

Route::get('/', function () {
    return view('welcome');
});


Route::get('/registeration', [UserController::class,'loadRegister']);
Route::post('/register', [UserController::class,'studentRegister'])->name('studentRegister');
Route::get('/login', function(){
    return redirect('/');
});
Route::get('/', [UserController::class, 'loadLogin']);
Route::post('/login', [UserController::class, 'userLogin'])->name('userLogin');

Route::get('/verification/{id}', [UserController::class, 'verification']);
Route::post('/verified', [UserController::class, 'verifiedOtp'])->name('verificationOtp');
Route::get('/dashboard', [UserController::class, 'loadDashboard']);
Route::get('/resend-otp', [UserController::class, 'resendOtp'])->name('resendOtp');
