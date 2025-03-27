<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\LeadController;

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

// Routes pour l'API de la plateforme centrale
// Ces routes utilisent un token API personnalisé via les headers, pas Sanctum
Route::prefix('v1')->group(function () {
    // Routes pour les leads
    Route::get('/leads', [LeadController::class, 'index']);
    Route::post('/leads', [LeadController::class, 'store']);

    // Route spéciale pour synchroniser les leads - doit être avant les routes avec paramètres
    Route::post('/leads/sync', [LeadController::class, 'sync']);

    // Routes avec paramètres
    Route::get('/leads/{id}', [LeadController::class, 'show']);
    Route::put('/leads/{id}', [LeadController::class, 'update']);
});
