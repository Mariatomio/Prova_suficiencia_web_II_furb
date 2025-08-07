<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ComandaController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\AuthController;

/* Route::middleware('auth:sanctum')->get('/comandas/receba', [ComandaController::class, 'index']); */
//Login
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->post('/logout', [AuthController::class, 'logout']);

Route::middleware('auth:sanctum')->get('/comandas/minha', function (Request $request) {
    try {
        return $request->user()->load('comandas.produtos');
    } catch (\Throwable $th) {
        return response()->json([
            'error' => true,
            'message' => 'Erro ao carregar comandas',
            'details' => $th->getMessage()
        ], 500);
    }
});



//Comanda
Route::get('/comandas', [ComandaController::class, 'index']);
Route::get('/comandas/{id}', [ComandaController::class, 'show']);
Route::post('/comandas', [ComandaController::class, 'store']);
Route::put('/comandas/{id}', [ComandaController::class, 'update']);
Route::delete('/comandas/{id}', [ComandaController::class, 'destroy']);
