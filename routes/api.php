<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\IoTController;

/*
|--------------------------------------------------------------------------
| API Routes for IoT Devices
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your IoT sensors.
| These routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group.
|
*/

// IoT sensor data endpoints
Route::prefix('iot')->group(function () {
    // Health monitoring
    Route::post('/health-log', [IoTController::class, 'storeHealthLog']);
    Route::post('/bulk-health-logs', [IoTController::class, 'bulkHealthLogs']);

    // Milk production
    Route::post('/milk-production', [IoTController::class, 'storeMilkProduction']);

    // Cattle status
    Route::get('/cattle/{tagId}/status', [IoTController::class, 'getCattleStatus']);
});

// Optional: Add authentication for IoT devices using Laravel Sanctum
// Route::middleware('auth:sanctum')->group(function () {
//     // Protected IoT routes
// });
