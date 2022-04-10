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

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::middleware(['XSS'])->group(function () {
    /** requires client credential validation, must use post under the group */
    Route::middleware(['validate_client'])->group(function () {
        Route::post('/user/create', 'UserController@create');
        Route::post('/user/login',  'UserController@login');

        /** requires access token validation, must use post under the group */
        Route::middleware(['verify_user'])->group(function () {
            Route::post('/artwork/create',   'ArtworkController@create');
            Route::post('/artwork/history',  'ArtworkController@history');
            Route::post('/artwork/transact', 'ArtworkController@transact');
            Route::post('/artwork/update',   'ArtworkController@update');

            Route::post('/money/history',  'MoneyController@history');
            Route::post('/money/transact', 'MoneyController@transact');

            Route::post('/user/balance', 'UserController@balance');
            Route::post('/user/update',  'UserController@update');
        });
    });

    Route::get('/test/200', 'TestingController@test200');
    Route::get('/test/404', 'TestingController@test404');
    Route::get('/test/500', 'TestingController@test500');

    Route::get('/artwork', 'ArtworkController@list');
    Route::get('/user', 'UserController@list');
});
