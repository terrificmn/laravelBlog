<?php

use App\Http\Controllers\PageController;
use App\Http\Controllers\PostController;
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

//Route::get('/', [PageController::class, 'index']);
Route::get('/', 'App\Http\Controllers\PageController@index');
Route::resource('/blog', 'App\Http\Controllers\PostController');

Auth::routes(); //tailwind auth 으로 설치하면서 생김

Route::get('/home', [\App\Http\Controllers\HomeController::class, 'index'])->name('home');


// Auth::routes();

// Route::get('/home', [\App\Http\Controllers\HomeController::class, 'index'])->name('home');
