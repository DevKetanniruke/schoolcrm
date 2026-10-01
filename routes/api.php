<?php

use App\Http\Controllers\Api\V1\AcademicYearController;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\CampusController;
use App\Http\Controllers\Api\V1\DepartmentController;
use App\Http\Controllers\Api\V1\DivisionController;
use App\Http\Controllers\Api\V1\OrganizationController;
use App\Http\Controllers\Api\V1\SchoolClassController;
use App\Http\Controllers\Api\V1\SchoolController;
use App\Http\Controllers\Api\V1\SubjectController;
use App\Http\Controllers\Api\V1\SystemStatusController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes - Version 1
|--------------------------------------------------------------------------
*/

Route::prefix('v1')->group(function () {
    // Public routes
    Route::get('/status', [SystemStatusController::class, 'status']);
    Route::post('/auth/login', [AuthController::class, 'login']);

    // Authenticated Sanctum API routes
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/auth/me', [AuthController::class, 'me']);
        Route::post('/auth/logout', [AuthController::class, 'logout']);

        // Multi-tenant Hierarchy & Domain API Endpoints
        Route::apiResource('organizations', OrganizationController::class);
        Route::apiResource('schools', SchoolController::class);
        Route::apiResource('campuses', CampusController::class);
        Route::apiResource('academic-years', AcademicYearController::class);
        Route::apiResource('departments', DepartmentController::class);
        Route::apiResource('classes', SchoolClassController::class);
        Route::apiResource('divisions', DivisionController::class);
        Route::apiResource('subjects', SubjectController::class);
    });
});
