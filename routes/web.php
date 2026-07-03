<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ERDController;

Route::get('/', [ERDController::class, 'index'])->name('erd.index');
Route::get('/export-pdf', [ERDController::class, 'exportPDF'])->name('erd.export');
Route::get('/table-details/{tableName}', [ERDController::class, 'getTableDetails'])->name('erd.details');
Route::get('/diagram-data', [ERDController::class, 'getDiagramData'])->name('erd.diagram');
Route::get('/table-stats', [ERDController::class, 'getTableStats'])->name('erd.stats');