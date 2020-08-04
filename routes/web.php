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


Route::get('/', function () {
    return redirect('articles');
});

Route::resource('articles', 'ArticleController');

Route::post('articles/destroy', [
    'as' => 'articles.destroys',
    'uses' => 'ArticleController@destroys'
]);

Route::get('/home', 'HomeController@index')->name('home');

Route::get('auth/register', [
    'as' => 'users.create',
    'uses' => 'UserController@create'
]);
Route::post('auth/register', [
    'as' => 'users.store',
    'uses' => 'UserController@store'
]);
Route::get('auth/confirm/{code}', [
    'as' => 'users.confirm',
    'uses' => 'UserController@confirm'
])->where('code', '[\pL\-\pN]{60}');


Route::get('auth/login', [
    'as' => 'sessions.create',
    'uses' => 'SessionController@create'
]);
Route::post('auth/login', [
    'as' => 'sessions.store',
    'uses' => 'SessionController@store'
]);
Route::get('auth/logout', [
    'as' => 'sessions.destroy',
    'uses' => 'SessionController@destroy'
]);


