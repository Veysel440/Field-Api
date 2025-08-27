<?php

use App\Http\Controllers\{AuthController, CustomerController, AssetController, WorkOrderController, ChecklistController, AttachmentController};

Route::post('/auth/login', [AuthController::class, 'login']);
Route::get('/auth/me', [AuthController::class, 'me'])->middleware('auth:sanctum');
Route::post('/auth/refresh', [AuthController::class, 'refresh']);
Route::post('/auth/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');


Route::get('/assets/map', [AssetController::class, 'mapPoints']);
Route::get('/work-orders/map', [WorkOrderController::class, 'mapPoints']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/customers', [CustomerController::class, 'index']);
    Route::post('/customers', [CustomerController::class, 'store']);

    Route::get('/assets', [AssetController::class, 'index']);
    Route::post('/assets', [AssetController::class, 'store']);

    Route::get('/work-orders', [WorkOrderController::class, 'index']);
    Route::post('/work-orders', [WorkOrderController::class, 'store']);
    Route::get('/work-orders/{workOrder}', [WorkOrderController::class, 'show']);

    Route::get('/work-orders/{workOrder}/checklist', [ChecklistController::class, 'woList']);
    Route::patch('/work-orders/{workOrder}/checklist/{itemId}', [ChecklistController::class, 'woToggle']);
    Route::post('/work-orders/{workOrder}/checklist/attach-template', [ChecklistController::class, 'woAttachTemplate']);
    Route::get('/checklists/templates', [ChecklistController::class, 'templatesIndex']);
    Route::post('/checklists/templates', [ChecklistController::class, 'templatesStore']);

    Route::get('/attachments', [AttachmentController::class, 'index']);
    Route::post('/attachments', [AttachmentController::class, 'store']);
});
