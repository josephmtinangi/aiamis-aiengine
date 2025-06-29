<?php

use App\Http\Controllers\AnomalyDetectionController;
use App\Http\Controllers\DownloadsController;
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

Route::get('anomaly-detection', [AnomalyDetectionController::class, 'index']);
Route::post('anomaly-detection/detect', [AnomalyDetectionController::class, 'detect']);

Route::get('downloads/download', [DownloadsController::class, 'download']);
Route::get('downloads/read', [DownloadsController::class, 'read']);
