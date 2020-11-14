<?php

Route::redirect('/', '/coupons');
Route::get('/coupons', 'Frontend\CouponsController@index')->name('frontend.coupons.index');
Auth::routes();

Route::group(['prefix' => 'admin', 'as' => 'admin.', 'namespace' => 'Admin', 'middleware' => ['auth', 'admin']], function () {
    Route::get('/', 'HomeController@index')->name('home');
    // Permissions
    Route::delete('permissions/destroy', 'PermissionsController@massDestroy')->name('permissions.massDestroy');
    Route::resource('permissions', 'PermissionsController');

    // Roles
    Route::delete('roles/destroy', 'RolesController@massDestroy')->name('roles.massDestroy');
    Route::resource('roles', 'RolesController');

    // Users
    Route::delete('users/destroy', 'UsersController@massDestroy')->name('users.massDestroy');
    Route::resource('users', 'UsersController');

    // Coupons
    Route::delete('coupons/destroy', 'CouponsController@massDestroy')->name('coupons.massDestroy');
    Route::post('coupons/media', 'CouponsController@storeMedia')->name('coupons.storeMedia');
    Route::post('coupons/ckmedia', 'CouponsController@storeCKEditorImages')->name('coupons.storeCKEditorImages');
    Route::post('coupons/{coupon}/add-codes', 'CouponsController@generateCodes')->name('coupons.generateCodes');
    Route::resource('coupons', 'CouponsController');

    // Codes
    Route::delete('codes/destroy', 'CodesController@massDestroy')->name('codes.massDestroy');
    Route::resource('codes', 'CodesController');

    // Purchases
    Route::delete('purchases/destroy', 'PurchasesController@massDestroy')->name('purchases.massDestroy');
    Route::resource('purchases', 'PurchasesController');
});
Route::group(['prefix' => 'profile', 'as' => 'profile.', 'namespace' => 'Auth', 'middleware' => ['auth']], function () {
// Change password
    if (file_exists(app_path('Http/Controllers/Auth/ChangePasswordController.php'))) {
        Route::get('password', 'ChangePasswordController@edit')->name('password.edit');
        Route::post('password', 'ChangePasswordController@update')->name('password.update');
        Route::post('profile', 'ChangePasswordController@updateProfile')->name('password.updateProfile');
        Route::post('profile/destroy', 'ChangePasswordController@destroy')->name('password.destroyProfile');
    }
});
Route::group(['as' => 'frontend.', 'namespace' => 'Frontend', 'middleware' => ['auth']], function () {
    Route::redirect('/home', '/coupons')->name('home');

    // Coupons
    Route::resource('coupons', 'CouponsController')->only('show');

    // Codes
    Route::post('codes/{code}/purchase', 'CodesController@purchase')->name('codes.purchase');

    // Purchases
    Route::resource('purchases', 'PurchasesController')->only(['index', 'show']);

    Route::get('frontend/profile', 'ProfileController@index')->name('profile.index');
    Route::post('frontend/profile', 'ProfileController@update')->name('profile.update');
    Route::post('frontend/profile/destroy', 'ProfileController@destroy')->name('profile.destroy');
    Route::post('frontend/profile/password', 'ProfileController@password')->name('profile.password');
});
