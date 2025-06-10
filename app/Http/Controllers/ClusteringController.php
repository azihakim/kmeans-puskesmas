<?php

namespace App\Http\Controllers;

use App\Models\Dataset;
use App\Services\KMeansService;
use Illuminate\Http\Request;

class ClusteringController extends Controller
{
    public function proses()
    {
        $kmeans = new KMeansService(3); // Jumlah cluster
        $hasil = $kmeans->run();
        dd($hasil);
        foreach ($hasil['clusters'] as $data) {
            Dataset::where('id', $data['dataset_id'])->update([
                'cluster' => $data['cluster']
            ]);
        }

        $datasets = Dataset::orderBy('id')->get();

        return view('hasil-clustering', compact('datasets'));
    }

    public function elbow()
    {
        $kmeans = new \App\Services\KMeansService();
        $evaluasi = $kmeans->evaluateRangeK(); // Dapatkan WCSS dari K=1 s.d. 6

        return view('hasil-elbow', compact('evaluasi'));
    }
}
