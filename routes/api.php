<?php

use Illuminate\Http\Request;
use App\Http\Controllers\payments\MpesaController;
use App\Http\Controllers\PaypalPaymentController;
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
//mpesa routes
Route::post('/getToken', [MpesaController::class, 'getAccessToken']);
Route::post('/stkpush', [MpesaController::class, 'stkPush']);
Route::post('/register', [MpesaController::class, 'MpesaRegisterurl']);
Route::post('/confirmation', [MpesaController::class, 'mpesaConfirmation']);
Route::post('/b2cendpoint', [MpesaController::class, 'b2cRequest']);

//paypal routes



    Route::post('/order/create',[PaypalPaymentController::class,'create']);
    Route::post('/order/capture/',[PaypalPaymentController::class,'capture']);

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });
