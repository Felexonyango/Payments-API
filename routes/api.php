<?php

use Illuminate\Http\Request;
use App\Http\Controllers\payments\MpesaController;
use App\Http\Controllers\payments\MpesaResponseController;
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

Route::post('/getToken', [MpesaController::class, 'getAccessToken']);
Route::post('/validation', [MpesaController::class, 'MpesaRegisterurl']);
Route::post('/confirmation', [MpesaController::class, 'MpesaRegisterurl']);



// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });
