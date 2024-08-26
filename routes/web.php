<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\master\CategoryAssetsController;
use App\Http\Controllers\master\DivisionController;
use App\Http\Controllers\master\ManufactureController;
use App\Http\Controllers\master\UserController;
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

// Route::group(['middleware' => ['role:staff']], function () {
//     Route::resource('manufacture', ManufactureController::class, ['except' => ['create', 'store', 'edit', 'update', 'destroy']])->parameters(['manufacture' => 'id']);
// });


Route::group(['middleware' => ['role:admin']], function () {
    Route::group(['prefix' => 'master', 'as' => 'master.'], function () {

        Route::resource('manufacture', ManufactureController::class, ['except' => ['index', 'show']])->parameters(['manufacture' => 'id']);

        Route::resource('category', CategoryAssetsController::class, ['except' => ['index', 'show']])->parameters(['category' => 'id']);

        Route::resource('division', DivisionController::class, ['except' => ['index', 'show']])->parameters(['division' => 'id']);

        Route::group(['controller' => UserController::class, 'prefix' => 'user', 'as' => 'user.'], function () {
            Route::get('datatable', 'dataTable')->name('dataTable');
        });
        Route::resource('user', UserController::class)->parameters(['user' => 'id']);
    });
});

Route::group(['middleware' => ['role:admin|staff']], function () {
    Route::group(['prefix' => 'master', 'as' => 'master.'], function () {

        Route::group(['controller' => ManufactureController::class, 'prefix' => 'manufacture', 'as' => 'manufacture.'], function () {
            Route::get('datatable', 'dataTable')->name('dataTable');
        });
        Route::resource('manufacture', ManufactureController::class, ['except' => ['create', 'store', 'edit', 'update', 'destroy']])->parameters(['manufacture' => 'id']);


        Route::group(['controller' => CategoryAssetsController::class, 'prefix' => 'category', 'as' => 'category.'], function () {
            Route::get('datatable', 'dataTable')->name('dataTable');
        });
        Route::resource('category', CategoryAssetsController::class, ['except' => ['create', 'store', 'edit', 'update', 'destroy']])->parameters(['category' => 'id']);


        Route::group(['controller' => DivisionController::class, 'prefix' => 'division', 'as' => 'division.'], function () {
            Route::get('datatable', 'dataTable')->name('dataTable');
        });
        Route::resource('division', DivisionController::class, ['except' => ['create', 'store', 'edit', 'update', 'destroy']])->parameters(['division' => 'id']);
    });
});
