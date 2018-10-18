<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::group(['middleware' => 'auth:api'], function () {
    Route::get('logout', 'Auth\LoginController@logout')->name('logout');
    Route::get('user', 'Auth\LoginController@getUser')->name('getUser');
    Route::post('profile/update-password', 'Auth\ProfileController@updatePassword');
    Route::get('profile/delete-user', 'Auth\ProfileController@deleteUser');
});

Route::group(['middleware' => 'guest:api'], function () {
    Route::get('refresh', 'Auth\LoginController@refresh');
    Route::post('login', 'Auth\LoginController@login')->name('login');
    Route::post('register', 'Auth\RegisterController@create')->name('register');
    Route::post('password/email', 'Auth\ForgotPasswordController@sendResetLinkEmail')->name('sendPasswordReset');
    Route::post('password/reset', 'Auth\ResetPasswordController@reset')->name('passwordReset');
    Route::get('confirmation', 'Auth\ConfirmAccountController@confirmAccount');
    Route::get('confirmation/resend', 'Auth\ConfirmAccountController@resendConfirmationEmail');
});

Route::group(['middleware' => ['auth:api', 'is-active']], function () {
    Route::post('profile', 'Auth\ProfileController@update')->name('UpdateProfile');
    Route::resource('users', 'Api\UsersController', ['except' => ['create', 'edit']]);
    Route::resource('roles', 'Api\RolesController', ['except' => ['create', 'edit']]);
    Route::resource('permissions', 'Api\PermissionsController', ['only' => ['index']]);
    Route::resource('photos', 'Api\PhotosController', ['except' => ['create', 'edit']]);
});
