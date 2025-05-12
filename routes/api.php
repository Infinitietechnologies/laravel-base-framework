<?php

use App\Http\Controllers\Api\Admin\Auth\AdminAuthController;
use App\Http\Controllers\Api\Admin\UserController;
use App\Http\Controllers\Api\BrandController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\FaqController;
use App\Http\Controllers\Api\FollowingMerchantController;
use App\Http\Controllers\Api\Member\Auth\MemberAuthController;
use App\Http\Controllers\Api\Merchant\MerchantAuthController;
use App\Http\Controllers\Api\MerchantController;
use App\Http\Controllers\Api\ProductConditionController;
use App\Http\Controllers\Api\SettingController;
use App\Http\Controllers\Api\CountryController;
use App\Http\Controllers\Api\StoreController;
use App\Http\Controllers\Api\TaxController;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;
use Spatie\Permission\Middleware\RoleMiddleware;

// categories
Route::prefix('categories')->group(function () {
    Route::group(['middleware' => ['auth:sanctum', RoleMiddleware::class . ':super_admin']], function () {
        Route::get('available', [CategoryController::class, 'availableCategories']);
        Route::get('statuses', [CategoryController::class, 'availableCategoryStatuses']);
        Route::post('/', [CategoryController::class, 'store']);
        Route::post('/{id}/update', [CategoryController::class, 'update']);
        Route::delete('/{id}', [CategoryController::class, 'destroy']);
    });
    Route::apiResource('/', CategoryController::class)->except(['store', 'update', 'destroy'])->parameters([
        '' => 'category'
    ]);
});

//brands
Route::prefix('brands')->group(function () {
    Route::get('statuses', [BrandController::class, 'statuses']);
    Route::post('{id}', [BrandController::class, 'update']);
    Route::apiResource('/', BrandController::class)->parameters([
        '' => 'brand'
    ]);
});

//faqs
Route::prefix('faqs')->group(function () {
    Route::group(['middleware' => ['auth:sanctum', RoleMiddleware::class . ':super_admin']], function () {
        Route::post('/', [FaqController::class, 'store']);
        Route::put('/{id}', [FaqController::class, 'update']);
        Route::delete('/{id}', [FaqController::class, 'destroy']);
    });
    Route::apiResource('/', FaqController::class)->except(['store', 'update', 'destroy'])->parameters(['' => 'faq']);
});

// admin
Route::prefix('admin')->middleware("auth:sanctum")->group(function () {
    Route::get('/current-user', [UserController::class, 'currentUser']);
    Route::apiResource('users', UserController::class)->parameters(['' => 'user']);
    Route::post('/users/{user}/update', [UserController::class, 'update']);
});

//settings
Route::prefix('settings')->group(function () {
    Route::group(['middleware' => ['auth:sanctum', RoleMiddleware::class . ':super_admin']], function () {
        Route::post('/', [SettingController::class, 'store']);
    });
    Route::ApiResource('/', SettingController::class)->except('store')->parameters(['' => 'setting']);
});

//Admin Authentication
Route::prefix('admin')->group(function () {
    Route::post('/login', [AdminAuthController::class, 'login']);
    Route::get('/logout', [AdminAuthController::class, 'logout'])->middleware('auth:sanctum');
});

//Members Authentication
Route::prefix('members')->group(function () {
    Route::post('/register', [MemberAuthController::class, 'register']);
    Route::post('/login', [MemberAuthController::class, 'login']);
    Route::get('/logout', [MemberAuthController::class, 'logout'])->middleware('auth:sanctum');
    Route::get('/current-user', [MemberAuthController::class, 'currentUser'])->middleware('auth:sanctum');
});

//merchants
Route::prefix('merchants')->group(function () {
    Route::post('/login', [MerchantAuthController::class, 'login']);

    Route::get('/available', [MerchantController::class, 'getAvailableMerchants']);
    Route::group(['middleware' => ['auth:sanctum', RoleMiddleware::class . ':super_admin']], function () {
        Route::get('/statuses', [MerchantController::class, 'getStatuses']);
        Route::post('/{id}/verification-status', [MerchantController::class, 'updateVerificationStatus']);
        Route::post('/{id}', [MerchantController::class, 'update']);
        Route::post('/{id}/visibility-status', [MerchantController::class, 'updateVisibilityStatus']);
    });
    Route::get('/logout', [MerchantAuthController::class, 'logout'])->middleware('auth:sanctum');
    Route::get('/current-user', [MerchantAuthController::class, 'currentUser'])->middleware('auth:sanctum');
    Route::apiResource('/', MerchantController::class)->except('update')->parameters(['' => 'merchant']);
});

//following merchants
Route::prefix('following-merchants')->group(function () {
    Route::group(['middleware' => ['auth:sanctum', RoleMiddleware::class . ':super_admin']], function () {
        Route::get('/', [FollowingMerchantController::class, 'index']);
    });
    Route::get('user', [FollowingMerchantController::class, 'getByUserId'])->middleware('auth:sanctum');
    Route::apiResource('/', FollowingMerchantController::class)->except('index')->parameters(['' => 'following_merchant'])->middleware('auth:sanctum');
});

//stores
Route::prefix('stores')->group(function () {
    Route::get('available', [StoreController::class, 'availableStores']);
    Route::group(['middleware' => ['auth:sanctum', RoleMiddleware::class . ':super_admin']], function () {
        Route::get('statuses', [StoreController::class, 'statuses']);
        Route::post('{id}/verification-status', [StoreController::class, 'changeVerificationStatus']);
    });
    Route::group(['middleware' => ['auth:sanctum', RoleMiddleware::class . ':merchant']], function () {
        Route::post('/', [StoreController::class, 'store']);
        Route::put('/{id}', [StoreController::class, 'update']);
        Route::delete('/{id}', [StoreController::class, 'destroy']);
    });
    Route::apiResource('/', StoreController::class)->except(['store', 'update', 'destroy'])->parameters(['' => 'store']);
});

// product conditions
Route::prefix('product-conditions')->group(function () {
    Route::get('/alignments', [ProductConditionController::class, 'alignments']);
    Route::get('/category/{categoryId}', [ProductConditionController::class, 'getByCategory']);
    Route::apiResource('/', ProductConditionController::class)->parameters(['' => 'product_condition']);
});

// Taxes
Route::prefix('taxes')->group(function () {
    Route::post('{id}', [TaxController::class, 'update']);
    Route::apiResource('/', TaxController::class)->parameters(['' => 'tax']);
});

Route::get('countries', [CountryController::class, 'index']);

Route::get('migrate', function () {
    Artisan::call('migrate');
});
Route::get('dbseed', function () {
    Artisan::call('db:seed --class=CountriesSeeder');
});

// example
Route::get('/admin-role', function () {
    return 'This is the admin area';
    //16|i9YexMXJDJeIPCmTQ5ZZA76G1xQ59n0yLwZiOBIXdf9bcc55
})->middleware('auth:sanctum', RoleMiddleware::class . ':admin');

Route::get('/user-role', function () {
    return 'This is the user area';
    //15|bxkdvKuBd0fdEwcz92JPyz8RToF7aOtbOQZE90BW697c72a0
})->middleware('auth:sanctum', RoleMiddleware::class . ':user');

//Route::get("dbseed", function () {
//    Artisan::call("db:seed --class=RolesAndPermissionsSeeder");
//    redirect('');
//});


