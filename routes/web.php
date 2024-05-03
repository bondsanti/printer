<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CustomAuthController;
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

// Route::get('/', function () {
//     return view('app');
// });
// Route::get('/report', function () {
//     return view('app');
// })

Route::get('Yj101dhjfdp376435xAzxc0091Dsdfk_astRqsdfk/{id}&{token}',[CustomAuthController::class,'AllowLoginConnect']);


Route::middleware(['alreadyLogin'])->group(function () {

    Route::get('/login',[CustomAuthController::class,'login']);
    Route::post('/login/auth',[CustomAuthController::class,'loginUser'])->name('loginUser');

});

Route::middleware(['isLogin'])->group(function () {

    //Route::any('/{any}', fn() => view('app'))->where('any', '.*');
    Route::get('/', function () {
        return view('app');
    });
    Route::get('/report', function () {
        return view('app');
    });
    Route::get('/users', function () {
        return view('app');
    });

    Route::get('/role/users',[CustomAuthController::class,'getUser']);
    Route::get('/logout/auth',[CustomAuthController::class,'logoutUser'])->name('logoutUser');

});
