<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::group(['middleware' => 'api', 'prefix' => 'auth'], function ($router) {
    Route::post('login', 'AuthController@login');
    Route::post('logout', 'AuthController@logout');
    Route::post('refresh', 'AuthController@refresh');
    Route::get('user-profile', 'AuthController@userProfile');
    Route::post('password-reset', 'PasswordResetController@forgot');
    Route::post('change-password', 'PasswordResetController@passwordResetProcess');
});

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware('auth:api')->group(function () {
    Route::get('dashboard/{guid}', 'HomeController@dashboard');

    Route::post('contacts/store', 'UserController@store');
    Route::get('contacts/{guid}', 'UserController@index');
    Route::post('contacts/{guid}/update', 'UserController@update');
    Route::post('contacts/{guid}/delete', 'UserController@delete');

    Route::get('borderels', 'PartijregController@getBorderels');
    Route::get('loten', 'PartijregController@getLoten');
    Route::get('templates', 'PartijregController@getTemplates');

    Route::post('delivery/store', 'PartijregController@storeDelivery');
    Route::get('delivery/all', 'DeliveryController@index');
    Route::get('delivery/{id}', 'DeliveryController@show');
});


