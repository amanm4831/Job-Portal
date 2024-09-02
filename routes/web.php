<?php

use GuzzleHttp\Middleware;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\AccountController;

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

// Route::get('/', function () {
//     return view('welcome');
// });

Route::get('/', [HomeController::class, 'index'])->name("home");






Route::group(['middleware' => 'guest'], function () {

    Route::get('/register', [AccountController::class, 'Registeration'])->name("account.register");

    Route::post('/process-registeration', [AccountController::class, 'processRegisteration'])->name('processRegisteration');

    Route::get('/login', [AccountController::class, 'login'])->name('account.login');

    Route::post('/authenticate', [AccountController::class, 'authenticate'])->name('account.authenticate');


});


Route::group(['middleware' => 'auth'], function () {

    Route::get('/profile', [AccountController::class, 'profile'])->name('account.profile');

    Route::get('/updateProfile', [AccountController::class, 'updateProfile'])->name('account.updateProfile');

    Route::get('/logout', [AccountController::class, 'logout'])->name('account.logout');

});

