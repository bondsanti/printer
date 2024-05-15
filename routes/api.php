<?php

use App\Http\Controllers\ExcelImportController;
use App\Http\Controllers\ReportController;
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

Route::post('/import-excel', [ExcelImportController::class, 'importData']);
Route::get('/data', [ExcelImportController::class, 'getData']);
Route::get('/data/chart', [ExcelImportController::class, 'getBarChartbyYear']);
Route::get('/data/chart/dep', [ExcelImportController::class, 'getBarChartbyYearWithDep']);
Route::get('/data/chartpie', [ExcelImportController::class, 'getPieChartbyYearWithPrinter']);
Route::get('/data/chartsimidonut', [ExcelImportController::class, 'getSimiDonutChartbyYearWithUser']);

Route::get('/report/data',[ReportController::class,'getData']);
Route::get('/report/data/department',[ReportController::class,'getDepartment']);
Route::get('/report/data/user',[ReportController::class,'getUser']);
Route::get('/report/data/chart/dep',[ReportController::class,'getBarChartbyDep']);
Route::get('/report/data/chartpie',[ReportController::class,'getPieChartbyPrinter']);
Route::get('/report/data/chart/users',[ReportController::class,'getBarChartbyUser']);
Route::get('/report/data/printer',[ReportController::class,'getDataPrinter']);

Route::get('/data/quota', [ExcelImportController::class, 'getQuota']);
Route::post('/quota/import-excel', [ExcelImportController::class, 'importQuota']);
