<?php

use App\Http\Controllers\ExcelImportController;
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

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::post('/import-excel', [ExcelImportController::class, 'import']);
Route::get('/data', [ExcelImportController::class, 'getData']);
Route::get('/data/chart', [ExcelImportController::class, 'getBarChartbyYear']);
Route::get('/data/piechart', [ExcelImportController::class, 'getPieChartbyYear']);
Route::get('/data/chart/users', [ExcelImportController::class, 'getBarChartbyYearwithUser']);

