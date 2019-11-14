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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group(['middeware' => 'api', 'prefix' => 'auth',], function
($router) {
    Route::post('register', 'AuthController@register');
    Route::post('login', 'AuthController@login');

    //cara pasang middleware di route
    Route::get('me', 'AuthController@me')->middleware('api.auth');

    Route::group(['middleware' => 'api.auth'], function($router) {
    Route::post('refresh', 'AuthController@refresh');
    Route::post('logout', 'AuthController@logout');
    Route::put('update_name', 'AuthController@updateName');
    Route::post('change_password', 'AuthController@changePassword');
    });

});
