<?php

namespace App\Http\Controllers;

use App\Models\Dataset;
use App\Models\JenisPenyakit;
use App\Services\KMeansService;
use Illuminate\Http\Request;

class ClusteringController extends Controller
{
    // public function proses()
    // {
    //     $kmeans = new KMeansService(3); // Jumlah cluster
    //     $hasil = $kmeans->run();
    //     dd($hasil);
    //     foreach ($hasil['clusters'] as $data) {
    //         Dataset::where('id', $data['dataset_id'])->update([
    //             'cluster' => $data['cluster']
    //         ]);
    //     }

    //     $datasets = Dataset::orderBy('id')->get();

    //     return view('hasil-clustering', compact('datasets'));
    // }

    public function proses()
    {
        $kmeans = new KMeansService(3); // Jumlah cluster
        $hasil = $kmeans->run();

        $clusters = $hasil['clusters'];
        $centroids = $hasil['centroids'];
        $metrics = $hasil['metrics'];
        $iterations = $hasil['iterations'];

        // Ambil dataset & jenis penyakit dari DB
        $dataset = Dataset::all();
        $jenisPenyakit = JenisPenyakit::all();

        // Gabungkan cluster ke data pasien
        $datasetClustered = $dataset->map(function ($item) use ($clusters) {
            $matched = collect($clusters)->firstWhere('dataset_id', $item->id);
            if ($matched) {
                $item->cluster = $matched['cluster'];
            }
            return $item;
        });

        // Kirim semua data ke view
        return view('clustering', [
            'clusters' => $clusters,
            'centroids' => $centroids,
            'metrics' => $metrics,
            'iterations' => $iterations,
            'datasetClustered' => $datasetClustered,
            'jenisPenyakit' => $jenisPenyakit,
        ]);
    }




    public function elbow()
    {
        $kmeans = new \App\Services\KMeansService();
        $evaluasi = $kmeans->evaluateRangeK(); // Dapatkan WCSS dari K=1 s.d. 6

        return view('hasil-elbow', compact('evaluasi'));
    }
}
