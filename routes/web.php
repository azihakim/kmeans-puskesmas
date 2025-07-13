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
Route::get('/import/detail', [ImportController::class, 'detailImport'])->name('import.detail');
Route::get('/dataset/empty', [\App\Http\Controllers\Admin\DatasetCrudController::class, 'emptyDataset'])->name('dataset.empty');
Route::post('/clusters/store', [\App\Http\Controllers\KMeansManualCalculationController::class, 'updateDatasetClusters'])->name('clusters.store');

Route::get('/kmeans/manual', [\App\Http\Controllers\KMeansManualCalculationController::class, 'manualKMeansCalculation'])->name('kmeans.manual');
