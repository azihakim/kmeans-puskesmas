<?php

use App\Http\Controllers\ClusteringController;
use App\Http\Controllers\ImportController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('/admin');
});
Route::get('/cluster-kmeans', [ClusteringController::class, 'proses'])->name('cluster.kmeans');
Route::get('/elbow', [ClusteringController::class, 'elbow'])->name('cluster.elbow');
Route::get('/import', [ImportController::class, 'index'])->name('import');
Route::post('/import', [ImportController::class, 'import'])->name('import.store');
