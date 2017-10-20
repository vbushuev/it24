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
Route::get('/panel/catalog', 'HomeController@catalog');

Route::get('/panel/mygoods', 'ClientController@goods');
Route::get('/user/data/goods', 'ClientController@getgoods');
Route::get('/user/data/catalogs', 'ClientController@getcatalogs');
Route::get('/user/data/catalog/add', 'ClientController@addcatalog');
Route::get('/user/data/catalog/edit', 'ClientController@editcatalog');
Route::get('/user/data/catalog/delete', 'ClientController@deletecatalog');
Route::get('/user/data/catalog/copy', 'ClientController@copycatalog');
Route::get('/user/data/catalog/link', 'ClientController@linkcatalog');
Route::get('/user/data/catalog/unlink', 'ClientController@unlinkcatalog');


Route::get('/profile', 'HomeController@profile');
Route::get('/support', 'HomeController@support');
Route::post('/support', 'HomeController@support');

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
Route::get('/data/goodpage', 'DataController@goodPage');
Route::get('/data/good/update', 'DataController@goodupdate');
Route::get('/data/good/adds', 'DataController@goodAdds');
Route::get('/data/goodsfordownload', 'DataController@goodsfordownload');

Route::get('/data/catalogs', 'DataController@catalogs');
Route::get('/data/brands', 'DataController@brands');

Route::get('/data/users', 'DataController@users');
Route::get('/data/user/add', 'DataController@useradd');
Route::get('/data/user/edit', 'DataController@useredit');
Route::get('/data/user/remove', 'DataController@userdel');

Route::get('/data/roles', 'DataController@roles');

Route::get('/data/catalog', 'DataController@catalog');
Route::get('/data/catalog/add', 'DataController@catalogadd');
Route::get('/data/catalog/edit', 'DataController@catalogedit');
Route::get('/data/catalog/path', 'DataController@catalogpath');
Route::get('/data/catalog/link', 'DataController@cataloglink');
Route::get('/data/catalog/unlink', 'DataController@catalogunlink');
Route::get('/data/catalog/remove', 'DataController@catalogremove');

Route::get('/data/categories', 'DataController@categories');

Route::get('/download', 'DataController@export');
Route::get('/command/uploads', function () {$exitCode = Artisan::queue('command:uploads');});
Route::get('/command/downloads', function () {$exitCode = Artisan::queue('command:downloads');});
