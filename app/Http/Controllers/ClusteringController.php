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

        // Ambil hasil clustering
        $clusters = $hasil['clusters'];
        $centroids = $hasil['centroids'];
        $metrics = $hasil['metrics'];
        $iterations = $hasil['iterations'];
        $dataTransformed = $hasil['data_transformed'];

        // Ambil dataset & jenis penyakit dari DB
        $dataset = Dataset::all()->keyBy('id'); // Mengubah dataset menjadi koleksi yang diindeks berdasarkan ID
        $jenisPenyakit = JenisPenyakit::all()->pluck('name', 'id'); // Ambil jenis penyakit dan memetakannya menjadi ID dan nama

        // Gabungkan cluster ke data pasien
        foreach ($clusters as $cluster) {
            if (isset($dataset[$cluster['dataset_id']])) {
                $dataset[$cluster['dataset_id']]->cluster = $cluster['cluster'];
            }
        }

        // Tambahkan 'P' sebelum dataset_id pada setiap data di dataTransformed
        foreach ($dataTransformed as &$data) {
            $data['dataset_id'] = 'P' . $data['dataset_id'];
        }
        unset($data);
        // dd($clusters);
        // Kirim semua data ke view
        return view('clustering', [
            'clusters' => $clusters,
            'centroids' => $centroids,
            'metrics' => $metrics,
            'iterations' => $iterations,
            'datasetClustered' => $dataset->values(), // Menggunakan values() untuk mengembalikan collection sebagai array
            'jenisPenyakit' => $jenisPenyakit,
            'dataTransformed' => $dataTransformed,
        ]);
    }


    public function elbow()
    {
        $kmeans = new \App\Services\KMeansService();
        $evaluasi = $kmeans->evaluateRangeK(); // Dapatkan WCSS dari K=1 s.d. 6

        return view('hasil-elbow', compact('evaluasi'));
    }

    public function store(Request $request)
    {
        // Ambil data cluster dari request (misalnya dari session jika Anda menyimpannya di session sebelumnya)
        $clusters = $request->input('clusters');
        if (!$clusters) {
            return redirect()->back()->withErrors(['error' => 'Tidak ada data cluster untuk disimpan']);
        }

        // Jika clusters berupa JSON string, decode ke array
        if (is_string($clusters)) {
            $clusters = json_decode($clusters, true);
        }

        // Update tabel Dataset dengan cluster yang baru
        foreach ($clusters as $data) {
            Dataset::where('id', $data['dataset_id'])->update([
                'cluster' => $data['cluster']
            ]);
        }

        return redirect()->route('dataset.index')->with('success', 'Hasil cluster berhasil disimpan!');
    }
}
