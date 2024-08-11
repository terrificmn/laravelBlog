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

// api prefix가 붙음.
Route::get('test', function() {
    echo "hello";
    return 'hello world';
});

/// api 전용 ////TODO: 추후 다시 작업 (web쪽 완료 후)
// Route::get('git', 'App\Http\Controllers\ApiController@index');
// Route::put('git/get_token', 'App\Http\Controllers\ApiController@getToken');
// Route::put("git/set_token", 'App\Http\Controllers\ApiController@setToken');
// Route::put("git/set_pwd", 'App\Http\Controllers\ApiController@setPwd');


// Route::put("git/get_token", function(Request $request) {
//     if($request["password"]) {
//         $pass = $request["password"];
//         return "pwssword $pass string confirmed";
//     }
//     if($request["json"]) {
//         return "json string confirmed";
//     } else {
//         return "not json";
//     }
//     return "hello git token";
// });

// Route::put("git/set_info", function(Request $request) {
//     if($request["password"]) {
//         $pass = $request["password"];
//         return "pwssword $pass string confirmed";
//     }
//     if($request["json"]) {
//         return "json string confirmed";
//     } else {
//         return "not json";
//     }
//     return "hello git token";
// });

// Route::get($uri, $callback);
// Route::post($uri, $callback);
// Route::patch($uri, $callback);
// Route::delete($uri, $callback);
// Route::options($uri, $callback);
