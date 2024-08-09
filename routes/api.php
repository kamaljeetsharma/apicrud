<?php

use App\Http\Controllers\Api\Authcontroller;
use App\Http\Controllers\Api\Postcontroller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
 Route::post('signup',[Authcontroller::class,'signup']);
 Route::post('login',[Authcontroller::class,'login']);
 Route::middleware('auth:sanctum')->group(function(){

 
 Route::post('logout',[Authcontroller::class,'logout']);

 Route::apiResource('Posts',Postcontroller::class);
});