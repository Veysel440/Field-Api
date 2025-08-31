<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    AuthController,
    HealthController,
    CustomerController,
    AssetController,
    WorkOrderController,
    ChecklistController,
    AttachmentController,
    MapController
};

/**
 * Public
 */
Route::prefix('auth')->group(function () {
    Route::get ('/csrf',   [AuthController::class, 'csrf']);           // XSRF cookie
    Route::post('/login',  [AuthController::class, 'login'])->middleware('throttle:10,1');
    Route::post('/refresh',[AuthController::class, 'refresh'])->middleware('throttle:20,1');
});

Route::get('/healthz', [HealthController::class, 'healthz']);
Route::get('/ready',   [HealthController::class, 'ready']);

/**
 * Protected
 */
Route::middleware(['auth:sanctum'])->group(function () {
    // session
    Route::get ('/auth/me',     [AuthController::class, 'me']);
    Route::post('/auth/logout', [AuthController::class, 'logout']);

    // map bbox (SRID 4326), sadece giriş yapmış kullanıcılar
    Route::get('/assets/map',      [MapController::class, 'assetsBbox']);
    Route::get('/work-orders/map', [MapController::class, 'workOrdersBbox']);

    // customers
    Route::get ('/customers', [CustomerController::class, 'index'])->middleware('throttle:60,1');
    Route::post('/customers', [CustomerController::class, 'store'])
        ->middleware(['idem','role:admin|tech','permission:customer.create']);

    // assets
    Route::get ('/assets', [AssetController::class, 'index'])->middleware('throttle:60,1');
    Route::post('/assets', [AssetController::class, 'store'])
        ->middleware(['idem','role:admin|tech','permission:asset.create']);

    // work orders
    Route::get  ('/work-orders',              [WorkOrderController::class, 'index'])->middleware('throttle:60,1');
    Route::post ('/work-orders',              [WorkOrderController::class, 'store'])
        ->middleware(['idem','role:admin|tech','permission:workorder.create']);
    Route::get  ('/work-orders/{workOrder}',  [WorkOrderController::class, 'show']);
    Route::patch('/work-orders/{workOrder}',  [WorkOrderController::class, 'update'])
        ->middleware(['role:admin|tech','permission:workorder.update']);
    Route::patch('/work-orders/{workOrder}/assign', [WorkOrderController::class, 'assign'])
        ->middleware(['role:admin','permission:workorder.update']);

    // attachments
    Route::get ('/attachments', [AttachmentController::class, 'index'])->middleware('throttle:60,1');
    Route::post('/attachments', [AttachmentController::class, 'store'])
        ->middleware(['idem','role:admin|tech','throttle:api']);
    Route::get ('/work-orders/{workOrder}/attachments', [AttachmentController::class, 'listWO']);

    // checklist
    Route::get  ('/work-orders/{workOrder}/checklist',                 [ChecklistController::class, 'woList']);
    Route::patch('/work-orders/{workOrder}/checklist/{itemId}',        [ChecklistController::class, 'woToggle'])
        ->middleware(['role:admin|tech']);
    Route::post ('/work-orders/{workOrder}/checklist/attach-template', [ChecklistController::class, 'woAttachTemplate'])
        ->middleware(['role:admin|tech']);

    Route::get ('/checklists/templates',  [ChecklistController::class, 'templatesIndex']);
    Route::post('/checklists/templates',  [ChecklistController::class, 'templatesStore'])
        ->middleware(['role:admin|tech']);
});
