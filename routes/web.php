<?php

use App\Http\Controllers\asset\LicenseAssetController;
use App\Http\Controllers\asset\PhysicalAssetController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\master\CategoryAssetsController;
use App\Http\Controllers\master\DivisionController;
use App\Http\Controllers\master\ManufactureController;
use App\Http\Controllers\master\BrandController;
use App\Http\Controllers\master\UserController;
use App\Http\Controllers\Submission\SubmissionFormController;
use App\Http\Controllers\Submission\SubmissionFormsCheckoutDateController;
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

Route::group(['middleware' => ['role:admin']], function () {
    Route::group(['prefix' => 'master', 'as' => 'master.'], function () {
        Route::group(['controller' => ManufactureController::class, 'prefix' => 'manufacture', 'as' => 'manufacture.'], function () {
            Route::get('datatable', 'dataTable')->name('dataTable');
        });
        Route::resource('manufacture', ManufactureController::class)->parameters(['manufacture' => 'id']);

        Route::group(['controller' => BrandController::class, 'prefix' => 'brand', 'as' => 'brand.'], function () {
            Route::get('datatable', 'dataTable')->name('dataTable');
        });
        Route::resource('brand', BrandController::class)->parameters(['brand' => 'id']);

        Route::group(['controller' => CategoryAssetsController::class, 'prefix' => 'category', 'as' => 'category.'], function () {
            Route::get('datatable', 'dataTable')->name('dataTable');
        });
        Route::resource('category', CategoryAssetsController::class)->parameters(['category' => 'id']);

        Route::group(['controller' => DivisionController::class, 'prefix' => 'division', 'as' => 'division.'], function () {
            Route::get('datatable', 'dataTable')->name('dataTable');
        });
        Route::resource('division', DivisionController::class)->parameters(['division' => 'id']);
        Route::group(['controller' => UserController::class, 'prefix' => 'user', 'as' => 'user.'], function () {
            Route::get('datatable', 'dataTable')->name('dataTable');
        });
        Route::resource('user', UserController::class)->parameters(['user' => 'id']);
    });

    Route::group(['prefix' => 'asset', 'as' => 'asset.'], function () {
        Route::group(['controller' => PhysicalAssetController::class, 'prefix' => 'physical', 'as' => 'physical.'], function () {
            Route::match(['put', 'patch'], 'upload-image/{id}', 'uploadImage')->name('uploadImage');
            Route::match(['put', 'patch'], 'destroy-image/{id}', 'destroyImage')->name('destroyImage');
            Route::match(['put', 'patch'], 'assign-to/{id}', 'assignTo')->name('assignTo');
            Route::match(['put', 'patch'], 'maintence/{id}', 'maintence')->name('maintence');
            Route::match(['put', 'patch'], 'return-asset/{id}', 'returnAsset')->name('returnAsset');
        });
        Route::resource('physical', PhysicalAssetController::class, ['except' => ['index', 'show']])->parameters(['physical' => 'id']);

        Route::group(['controller' => LicenseAssetController::class, 'prefix' => 'license', 'as' => 'license.'], function () {
            Route::match(['put', 'patch'], 'upload-image/{id}', 'uploadImage')->name('uploadImage');
            Route::match(['put', 'patch'], 'destroy-image/{id}', 'destroyImage')->name('destroyImage');
            Route::match(['put', 'patch'], 'assign-to/{id}', 'assignTo')->name('assignTo');
            Route::match(['put', 'patch'], 'maintence/{id}', 'maintence')->name('maintence');
            Route::match(['put', 'patch'], 'return-asset/{id}', 'returnAsset')->name('returnAsset');
        });
        Route::resource('license', LicenseAssetController::class, ['except' => ['index', 'show']])->parameters(['license' => 'id']);
    });

    Route::group(['controller' => SubmissionFormController::class, 'prefix' => 'submission', 'as' => 'submission.'], function () {
        Route::post('approve', 'approve')->name('approve');
        Route::post('reject', 'reject')->name('reject');
        Route::match(['put', 'patch'], 'assign-to/{id}', 'assignTo')->name('assignTo');
    });
});

Route::group(['middleware' => ['role:staff']], function () {
    Route::group(['controller' => SubmissionFormController::class, 'prefix' => 'submission', 'as' => 'submission.'], function () {
        Route::get('datatable', 'dataTable')->name('dataTable');
        Route::get('create/{type}', 'create')->name('create');
        Route::post('store/{type}', 'store')->name('store');
        Route::match(['put', 'patch'], 'check-out/{id}', 'checkOut')->name('checkOut');
    });
    Route::resource('submission', SubmissionFormController::class, ['except' => ['index', 'create', 'store']])->parameters(['submission' => 'id']);
});

Route::group(['middleware' => ['role:admin|staff']], function () {
    Route::group(['prefix' => 'asset', 'as' => 'asset.'], function () {
        Route::group(['controller' => PhysicalAssetController::class, 'prefix' => 'physical', 'as' => 'physical.'], function () {
            Route::get('datatable', 'dataTable')->name('dataTable');
        });
        Route::resource('physical', PhysicalAssetController::class, ['except' => ['create', 'store', 'edit', 'update', 'destroy']])->parameters(['physical' => 'id']);

        Route::group(['controller' => LicenseAssetController::class, 'prefix' => 'license', 'as' => 'license.'], function () {
            Route::get('datatable', 'dataTable')->name('dataTable');
        });
        Route::resource('license', LicenseAssetController::class, ['except' => ['create', 'store', 'edit', 'update', 'destroy']])->parameters(['license' => 'id']);
    });

    Route::group(['controller' => SubmissionFormController::class, 'prefix' => 'submission', 'as' => 'submission.'], function () {
        Route::get('datatable', 'dataTable')->name('dataTable');
    });
    Route::resource('submission', SubmissionFormController::class, ['except' => ['create', 'store', 'update', 'destroy']])->parameters(['submission' => 'id']);

    Route::get('my-account', [UserController::class, 'show'])->name('myAccount');
});
