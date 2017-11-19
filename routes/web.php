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

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

Route::get('/notes/new', function () {
	return view('store/notes/new');
});

Route::prefix('api')->group(function () {
	Route::resource('store/vaults', 'Store\VaultController');
	Route::resource('store/notes', 'Store\NoteController');
	Route::resource('store/websitelogins', 'Store\WebsiteLoginController');
});