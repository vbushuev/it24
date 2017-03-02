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

//Route::get('/', function () {return view('welcome');});
Route::get('/','WebController@index');

Auth::routes();

Route::get('/panel', 'HomeController@index');
Route::get('/panel/goods', 'HomeController@goods');
Route::get('/data/supplies', 'DataController@supplies');
Route::get('/data/uploads', 'DataController@uploads');
Route::get('/data/goods', 'DataController@goods');
Route::get('/data/categories', 'DataController@categories');
