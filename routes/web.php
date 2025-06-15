<?php

use App\Http\Controllers\ClusteringController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('/admin');
});
Route::get('/cluster-kmeans', [ClusteringController::class, 'proses'])->name('cluster.kmeans');
Route::get('/elbow', [ClusteringController::class, 'elbow'])->name('cluster.elbow');
