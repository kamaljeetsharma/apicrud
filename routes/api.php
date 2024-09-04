<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\Api\Authcontroller;
use App\Http\Controllers\Api\Postcontroller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Laravel\Sanctum\Sanctum;
use PharIo\Manifest\AuthorCollection;

 Route::post('/signup',[Authcontroller::class,'signup']);


//  Route::get('/',[Authcontroller::class,'signup']);
 Route::post('/login',[Authcontroller::class,'login']);
 Route::post('/forgotpassword', [AuthController::class, 'forgotPassword']);
Route::post('/april', [AuthController::class, 'resetPasswordWithOtp']);
Route::post('/may', [AuthController::class, 'otpverification']); 
 Route::get('/logout', [AuthController::class, 'logout']);
 Route::get('/dashboard', [AuthController::class, 'dashboard']);
 Route::post('/edit',[AdminController::class,'reset']);
 Route::post('/update',[AuthController::class,'updateProfile']);
 Route::get('/admin-page',[Authcontroller::class,'admin.index']);
 
 Route::post('/change',[AuthController::class,'updatePassword']);

 //Route::post('/job',[AuthController::class,'index']);

 



