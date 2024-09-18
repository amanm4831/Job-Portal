<?php

use App\Http\Controllers\admin\UserController;
use GuzzleHttp\Middleware;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\JobsController;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\admin\DashboardController;

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
Route::get('/jobs', [JobsController::class, 'index'])->name('jobs');
Route::get('/jobs/detail/{id}', [JobsController::class, 'details'])->name('jobs.detail');
Route::post('/apply-job', [JobsController::class, 'applyJob'])->name('jobs.apply');
Route::post('/saveJobs', [JobsController::class, 'saveJobs'])->name('jobs.save');





Route::group(['middleware' => 'guest'], function () {

    Route::get('/register', [AccountController::class, 'Registeration'])->name("account.register");

    Route::post('/process-registeration', [AccountController::class, 'processRegisteration'])->name('processRegisteration');

    Route::get('/login', [AccountController::class, 'login'])->name('account.login');

    Route::post('/authenticate', [AccountController::class, 'authenticate'])->name('account.authenticate');


});


Route::group(['middleware' => 'auth'], function () {

    Route::get('/profile', [AccountController::class, 'profile'])->name('account.profile');

    Route::put('/updateProfile', [AccountController::class, 'updateProfile'])->name('account.updateProfile');

    Route::get('/logout', [AccountController::class, 'logout'])->name('account.logout');

    Route::post('/updateProfilePic', [AccountController::class, 'updateProfilePic'])->name('account.updateProfilePic');

    Route::get('/create-job', [AccountController::class, 'createJob'])->name('account.createJob');

    Route::post('/save-job', [AccountController::class, 'saveJob'])->name('account.saveJob');

    Route::get('/my-jobs', [AccountController::class, 'myJobs'])->name('account.myJobs');

    Route::get('/my-jobs/edit/{jobId}', [AccountController::class, 'editJob'])->name('account.editJob');

    Route::post('/update-job/{jobId}', [AccountController::class, 'updateJob'])->name('account.updateJob');

    Route::post('/delete-job', [AccountController::class, 'deleteJob'])->name('account.deleteJob');

    Route::get('/my-job-applications', [JobsController::class, 'myJobApplications'])->name('account.jobApplications');

    Route::post('/remove-job', [AccountController::class, 'removeJobs'])->name('account.removeJobs');

    Route::get('/saved-jobs', [AccountController::class, 'savedJobs'])->name('account.savedJobs');

    Route::post('/remove-saved-job', [AccountController::class, 'removeSavedJob'])->name('account.removeSavedJob');

    Route::post('/updatePassword', [AccountController::class, 'updatePassword'])->name('account.updatePassword');

});

Route::group(['middleware'=>'checkRole'], function(){
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');
    Route::get('/users', [UserController::class, 'index'])->name('admin.users.list');
    Route::get('/users/{id}', [UserController::class, 'edit'])->name('admin.users.edit');
    Route::put('/users/{id}', [UserController::class, 'update'])->name('admin.users.update');
    Route::delete('/users/{id}', [UserController::class, 'destroy'])->name('admin.users.destroy');
});

