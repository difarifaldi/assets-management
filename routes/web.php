<?php

use App\Http\Controllers\asset\LicenseAssetController;
use App\Http\Controllers\asset\PhysicalAssetController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\master\CategoryAssetsController;
use App\Http\Controllers\master\DivisionController;
use App\Http\Controllers\master\ManufactureController;
use App\Http\Controllers\master\BrandController;
use App\Http\Controllers\master\UserController;
use App\Http\Controllers\SubmissionFormController;
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

        Route::resource('brand', BrandController::class, ['except' => ['index', 'show']])->parameters(['brand' => 'id']);

        Route::resource('category', CategoryAssetsController::class, ['except' => ['index', 'show']])->parameters(['category' => 'id']);

        Route::resource('division', DivisionController::class, ['except' => ['index', 'show']])->parameters(['division' => 'id']);

        Route::group(['controller' => UserController::class, 'prefix' => 'user', 'as' => 'user.'], function () {
            Route::get('datatable', 'dataTable')->name('dataTable');
        });
        Route::resource('user', UserController::class)->parameters(['user' => 'id']);
    });

    Route::group(['prefix' => 'asset', 'as' => 'asset.'], function () {

        Route::resource('physical', PhysicalAssetController::class, ['except' => ['index', 'show']])->parameters(['physical' => 'id']);
    });

    Route::group(['prefix' => 'asset', 'as' => 'asset.'], function () {

        Route::resource('license', LicenseAssetController::class, ['except' => ['index', 'show']])->parameters(['license' => 'id']);
    });
});

Route::group(['middleware' => ['role:admin|staff']], function () {
    Route::group(['prefix' => 'master', 'as' => 'master.'], function () {

        Route::group(['controller' => ManufactureController::class, 'prefix' => 'manufacture', 'as' => 'manufacture.'], function () {
            Route::get('datatable', 'dataTable')->name('dataTable');
        });
        Route::resource('manufacture', ManufactureController::class, ['except' => ['create', 'store', 'edit', 'update', 'destroy']])->parameters(['manufacture' => 'id']);


        Route::group(['controller' => BrandController::class, 'prefix' => 'brand', 'as' => 'brand.'], function () {
            Route::get('datatable', 'dataTable')->name('dataTable');
        });
        Route::resource('brand', BrandController::class, ['except' => ['create', 'store', 'edit', 'update', 'destroy']])->parameters(['brand' => 'id']);


        Route::group(['controller' => CategoryAssetsController::class, 'prefix' => 'category', 'as' => 'category.'], function () {
            Route::get('datatable', 'dataTable')->name('dataTable');
        });
        Route::resource('category', CategoryAssetsController::class, ['except' => ['create', 'store', 'edit', 'update', 'destroy']])->parameters(['category' => 'id']);


        Route::group(['controller' => DivisionController::class, 'prefix' => 'division', 'as' => 'division.'], function () {
            Route::get('datatable', 'dataTable')->name('dataTable');
        });
        Route::resource('division', DivisionController::class, ['except' => ['create', 'store', 'edit', 'update', 'destroy']])->parameters(['division' => 'id']);
    });

    Route::group(['prefix' => 'asset', 'as' => 'asset.'], function () {
        Route::group(['controller' => PhysicalAssetController::class, 'prefix' => 'physical', 'as' => 'physical.'], function () {
            Route::get('datatable', 'dataTable')->name('dataTable');
        });
        Route::resource('physical', PhysicalAssetController::class, ['except' => ['create', 'store', 'edit', 'update', 'destroy']])->parameters(['physical' => 'id']);
    });

    Route::group(['prefix' => 'asset', 'as' => 'asset.'], function () {
        Route::group(['controller' => LicenseAssetController::class, 'prefix' => 'license', 'as' => 'license.'], function () {
            Route::get('datatable', 'dataTable')->name('dataTable');
        });
        Route::resource('license', LicenseAssetController::class, ['except' => ['create', 'store', 'edit', 'update', 'destroy']])->parameters(['license' => 'id']);
    });

    Route::group(['prefix' => 'submission', 'as' => 'submission.'], function () {
        Route::group(['controller' => SubmissionFormController::class, 'prefix' => 'submission', 'as' => 'submission.'], function () {
            Route::get('datatable', 'dataTable')->name('dataTable');
        });

        Route::get('index', [SubmissionFormController::class, 'index'])->name('index');
        Route::post('approve', [SubmissionFormController::class, 'approve'])->name('approve');
        Route::get('{type}/{asset}', [SubmissionFormController::class, 'create'])->name('create');
        Route::post('{type}/store', [SubmissionFormController::class, 'store'])->name('store');
    });
});
