<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DrosophilaController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/login', 'AuthController@login');

Route::get('/drosophila', [DrosophilaController::class, 'index']);
Route::post('/drosophila', [DrosophilaController::class, 'create']);
Route::delete('/drosophila/{id}', [DrosophilaController::class, 'delete']);
Route::get('/drosophila/mixed', [DrosophilaController::class, 'mixed']);

Route::fallback(function(){
    return response()->json([
        'message' => 'Page Not Found. If error persists, contact',
    ], 404);
});
