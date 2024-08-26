<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ManufactureController;
use Illuminate\Support\Facades\Route;

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

Route::get('login', [AuthController::class, 'login'])->name('login');
Route::post('authenticate', [AuthController::class, 'authenticate'])->name('authenticate');
Route::get('logout', [AuthController::class, 'logout'])->name('logout');

Route::group(['middleware' => 'auth'], function () {
    Route::get('/', function () {
        return view('home');
    })->name('home');
});

Route::group(['middleware' => ['role:staff']], function () {
    Route::resource('manufacture', ManufactureController::class, ['except' => ['create', 'store', 'edit', 'update', 'destroy']])->parameters(['manufacture' => 'id']);
});
Route::group(['middleware' => ['role:admin']], function () {

    Route::group(['controller' => ManufactureController::class, 'prefix' => 'manufacture', 'as' => 'manufacture.'], function () {
        Route::get('datatable', 'dataTable')->name('dataTable');
    });
    Route::resource('manufacture', ManufactureController::class)->parameters(['manufacture' => 'id']);
});
