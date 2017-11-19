<?php

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

Auth::routes();

Route::get('/', 'HomeController@index')->name('home');

Route::prefix('api')->middleware('auth')->group(function () {
	Route::resource('store/vaults', 'Store\VaultController');
	Route::get('store/vaults/{id}/all', 'Store\VaultController@all');
	Route::resource('store/notes', 'Store\NoteController');
	Route::resource('store/websitelogins', 'Store\WebsiteLoginController');
});