<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\AgentController;

Route::get('/user', function (Request $request) {
  return $request->user();
})->middleware('auth:sanctum');

Route::post('login', [AuthController::class, 'login']);
Route::middleware('auth:sanctum')->post('logout', [AuthController::class, 'logout']);

Route::middleware('auth:sanctum')->group(function () {
  Route::get('agent/transactions', [AgentController::class, 'getTransactions']);
  Route::get('super-agent/transactions', [AgentController::class, 'getSuperAgentTransactions']);
  Route::get('agent/me', [AgentController::class, 'me']);
  Route::get('super-agent/top-active', [AgentController::class, 'topActiveAgents']);
  Route::get('super-agent/terminals', [AgentController::class, 'getSuperTerminals']);
});
