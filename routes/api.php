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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('image', 'Api\ImageController@index');
Route::get('image_group', 'Api\ImageController@image_group');
Route::get('image/{id}', 'Api\ImageController@show');
Route::post('image', 'Api\ImageController@store');
Route::put('image/{id}', 'Api\ImageController@update');
Route::delete('image/{id}', 'Api\ImageController@destroy');