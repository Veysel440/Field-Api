<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\AssetController;
use App\Http\Controllers\WorkOrderController;
use App\Http\Controllers\ChecklistController;
use App\Http\Controllers\AttachmentController;


Route::post('/auth/login',   [AuthController::class, 'login']);
Route::post('/auth/refresh', [AuthController::class, 'refresh']);
Route::get ('/auth/csrf', fn () => response()->json(['ok' => true]));


Route::get('/assets/map',       [AssetController::class,    'mapPoints']);
Route::get('/work-orders/map',  [WorkOrderController::class,'mapPoints']);


Route::middleware('auth:sanctum')->group(function () {
    // Auth
    Route::get ('/auth/me',     [AuthController::class, 'me']);
    Route::post('/auth/logout', [AuthController::class, 'logout']);


    Route::get ('/customers', [CustomerController::class, 'index']);
    Route::post('/customers', [CustomerController::class, 'store'])
        ->middleware('role:admin|tech');


    Route::get ('/assets', [AssetController::class, 'index']);
    Route::post('/assets', [AssetController::class, 'store'])
        ->middleware('role:admin|tech');


    Route::get ('/work-orders',               [WorkOrderController::class, 'index']);
    Route::post('/work-orders',               [WorkOrderController::class, 'store'])
        ->middleware('role:admin|tech');
    Route::get ('/work-orders/{workOrder}',   [WorkOrderController::class, 'show']);
    Route::patch('/work-orders/{workOrder}',  [WorkOrderController::class, 'update'])
        ->middleware('role:admin|tech');


    Route::get ('/attachments', [AttachmentController::class, 'index']);
    Route::post('/attachments', [AttachmentController::class, 'store'])
        ->middleware(['role:admin|tech','throttle:api']);
    Route::get ('/work-orders/{workOrder}/attachments', [AttachmentController::class, 'listWO']);


    Route::get  ('/work-orders/{workOrder}/checklist',                      [ChecklistController::class, 'woList']);
    Route::patch('/work-orders/{workOrder}/checklist/{itemId}',             [ChecklistController::class, 'woToggle'])
        ->middleware('role:admin|tech');
    Route::post ('/work-orders/{workOrder}/checklist/attach-template',      [ChecklistController::class, 'woAttachTemplate'])
        ->middleware('role:admin|tech');
    Route::get  ('/checklists/templates',                                   [ChecklistController::class, 'templatesIndex']);
    Route::post ('/checklists/templates',                                   [ChecklistController::class, 'templatesStore'])
        ->middleware('role:admin|tech');
});
