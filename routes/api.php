<?php

Route::group(['prefix' => 'v1', 'as' => 'api.', 'namespace' => 'Api\V1\Admin', 'middleware' => ['auth:api']], function () {
    // Permissions
    Route::apiResource('permissions', 'PermissionsApiController');

    // Roles
    Route::apiResource('roles', 'RolesApiController');

    // Users
    Route::apiResource('users', 'UsersApiController');

    // Coupons
    Route::post('coupons/media', 'CouponsApiController@storeMedia')->name('coupons.storeMedia');
    Route::apiResource('coupons', 'CouponsApiController');

    // Codes
    Route::apiResource('codes', 'CodesApiController');

    // Purchases
    Route::apiResource('purchases', 'PurchasesApiController');
});
