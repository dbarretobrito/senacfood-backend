<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\CategoriasController;
use App\Http\Controllers\FavoritosController;
use App\Http\Controllers\GroqController;
use App\Http\Controllers\IngredientesController;
use App\Http\Controllers\ReceitasController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login'])->name('login');

Route::group(['middleware' => 'auth:sanctum'], function (){
    Route::get('users', [UserController::class, 'index']);
    Route::get('user/{id}', [UserController::class, 'show']);
    Route::delete('users/{id}', [UserController::class, 'destroy']);
    Route::patch('users/edit/{id}', [UserController::class, 'update']);
    
    Route::get('receitas', [ReceitasController::class, 'index']);
    Route::post('receitas', [ReceitasController::class, 'store']);
    Route::get('receita/{id}', [ReceitasController::class, 'show']);
    Route::patch('receitas/edit/{id}', [ReceitasController::class, 'update']);
    Route::delete('receitas/{id}', [ReceitasController::class, 'destroy']);

    Route::get('categorias', [CategoriasController::class, 'index']);
    Route::post('categorias', [CategoriasController::class, 'store']);
    Route::get('categoria/{id}', [CategoriasController::class, 'show']);
    Route::patch('categoria/edit/{id}', [CategoriasController::class, 'update']);
    Route::delete('categorias/{id}', [CategoriasController::class, 'destroy']);

    Route::get('ingredientes', [IngredientesController::class, 'index']);
    Route::post('ingredientes', [IngredientesController::class, 'store']);
    Route::get('ingrediente/{id}', [IngredientesController::class, 'show']);
    Route::patch('ingredientes/edit/{id}', [IngredientesController::class, 'update']);
    Route::delete('ingredientes/{id}', [IngredientesController::class, 'destroy']);

    Route::get('/favoritos', [FavoritosController::class, 'index']);
    Route::post('/receitas/{receitaId}/favorito', [FavoritosController::class, 'toggle']);

    Route::get('insight', [GroqController::class, 'insightPerfil']);
    Route::get('ingredientes/insight', [GroqController::class, 'insightIngredientes']);
    Route::get('receitas/insight', [GroqController::class, 'insightReceitas']);

    Route::post('logout', [AuthController::class, 'logout']);
});