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
Route::get('/panel/suppliers', 'HomeController@suppliers');
Route::get('/panel/schedules', 'HomeController@schedules');
Route::get('/panel/users', 'HomeController@users');
Route::get('/panel/downloads', 'HomeController@downloads');

Route::get('/data/suppliers', 'DataController@suppliers');
Route::get('/data/supplierupdate', 'DataController@supplierupdate');

Route::get('/data/schedules', 'DataController@schedules');
Route::get('/data/schedule/add', 'DataController@scheduleadd');
Route::get('/data/schedule/edit', 'DataController@scheduleedit');
Route::get('/data/schedule/delete', 'DataController@scheduledel');

Route::get('/data/downloads', 'DataController@downloads');
Route::get('/data/download/progress', 'DataController@downloadprogress');

Route::get('/data/uploads', 'DataController@uploads');
Route::get('/data/uploads/progress', 'DataController@uploadsProgress');

Route::get('/data/goods', 'DataController@goods');
Route::get('/data/categories', 'DataController@categories');
Route::get('/data/brands', 'DataController@brands');

Route::get('/data/users', 'DataController@users');
Route::get('/data/user/add', 'DataController@useradd');
Route::get('/data/user/edit', 'DataController@useredit');
Route::get('/data/user/del', 'DataController@userdel');

Route::get('/data/roles', 'DataController@roles');
