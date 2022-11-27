<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\DeviceAllowController;
use App\Http\Controllers\DeviceController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HostController;
use App\Http\Controllers\IndexController;
use App\Http\Controllers\ReplyController;
use App\Http\Controllers\ServerController;
use App\Http\Controllers\WorkOrderController;
use App\Http\Controllers\UserController;

Route::view('/login', 'login')->name('login');
Route::post('/login', [IndexController::class, 'login']);


// 登入后的路由
Route::group(['middleware' => 'auth:web'], function () {
    Route::get('/', [IndexController::class, 'index'])->name('index');

    Route::resource('users', UserController::class);
    Route::resource('servers', ServerController::class);
    Route::resource('hosts', HostController::class);
    Route::resource('admins', AdminController::class);
    Route::resource('work-orders', WorkOrderController::class);
    Route::resource('work-orders.replies', ReplyController::class);
    Route::get('/logout', [IndexController::class, 'logout'])->name('logout');
});
