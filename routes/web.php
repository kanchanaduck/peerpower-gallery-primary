<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

/* Route::get('/', function () {
    return view('welcome');
}); */
Route::get('/', 'HomeController@index');

Auth::routes();

Route::middleware('auth')->group(function () {
    Route::get('/home', 'HomeController@index')->name('home');
    Route::get('main', 'ImageController@main')->name('main');
    Route::get('gallery', 'ImageController@index')->name('gallery');
    Route::get('lists', 'ImageController@lists')->name('gallery.lists');
    Route::get('group', 'ImageController@group')->name('gallery.group');
    Route::post('store', 'ImageController@store')->name('gallery.store');
});

