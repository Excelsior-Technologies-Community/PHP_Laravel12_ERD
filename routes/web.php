<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ERDController;

Route::get('/', [ERDController::class, 'index']);
Route::get('/export-pdf', [ERDController::class, 'exportPDF']);