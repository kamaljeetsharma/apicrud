<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\api\Authcontroller;
use Laravel\Sanctum\Sanctum;

Route::get('/', function () {
    return view('welcome');
});

Route::view('/register','register');// routes/web.php

//Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
//Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
Route::view('/login','login')->name('login');
//Route::view('/new','dashboard');
Route::view('/password','resetpassword');
Route::view('/kamal','forgotpassword');
Route::view('/dashboard','dashboard');
Route::view('/otp','validateotp');
Route::view('/logout','logout');
Route::view('/sahil','admin.display');
//Route::view('/crud','admin.delete');
Route::view('/edit','admin.edit');
Route::view('/delete','admin.delete');
Route::get('/users', [AuthController::class, 'index']);
Route::view('/edit','admin.edit');
Route::delete('/user/{id}', [AuthController::class, 'destroy'])->name('deleteUser');

Route::get('admin-page',function(){
 return view('admin.index');
});

Route::get('new-page',function(){
    return view('admin.newpage');
   });



   /*Route::middleware('auth:sanctum')->group(function () {
    Route::get('/users', [AuthController::class, 'index']);
    Route::view('/edit','admin.edit');
   });*/