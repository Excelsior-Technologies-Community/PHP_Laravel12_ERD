<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ERDController;

Route::get('/', [ERDController::class, 'index']);
Route::get('/export-pdf', [ERDController::class, 'exportPDF']);

// API Routes for AJAX functionality
Route::get('/table-details/{tableName}', [ERDController::class, 'getTableDetails']);
Route::get('/diagram-data', [ERDController::class, 'getDiagramData']);