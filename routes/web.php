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
// 
Route::get('/', 'App\Http\Controllers\PageController@index');
Route::resource('/blog', 'App\Http\Controllers\PostController');

Route::get('/tag/{tag_name}', 'App\Http\Controllers\TagController@index');


Route::post('/comment/create', 'App\Http\Controllers\CommentController@store');
Route::get('/comment/{comment_id}/edit', 'App\Http\Controllers\CommentController@edit');
Route::patch('/comment/{comment_id}', 'App\Http\Controllers\CommentController@update');

Auth::routes(); //tailwind auth 으로 설치하면서 생김

Route::get('/home', [\App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::post('/upload', [\App\Http\Controllers\UploadController::class, 'store']);

Route::get('/search', 'App\Http\Controllers\SearchController@index');

Route::get('/devnote', 'App\Http\Controllers\DevnoteController@index');
Route::get('/devnote/create', 'App\Http\Controllers\DevnoteController@create');
Route::post('/devnote/imgupload', 'App\Http\Controllers\DevnoteController@imgupload')->name('devnote.imgupload');
Route::post('/devnote', 'App\Http\Controllers\DevnoteController@store');
Route::get('/devnote/{slug}/edit', 'App\Http\Controllers\DevnoteController@edit');
Route::patch('/devnote/{slug}', 'App\Http\Controllers\DevnoteController@update'); //update (patch 메소드)로 함
Route::delete('/devnote/{id}', 'App\Http\Controllers\DevnoteController@destroy'); //delete (delete 메소드)
Route::get('/devnote/{slug}', 'App\Http\Controllers\DevnoteController@show');

Route::resource('/portfolio', 'App\Http\Controllers\PortfolioController');
// Auth::routes();

// Route::get('/home', [\App\Http\Controllers\HomeController::class, 'index'])->name('home');

