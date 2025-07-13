<?php

namespace App\Http\Controllers;

use App\Models\Dataset;
use App\Models\TransformedDataset;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;


class KMeansManualCalculationController extends Controller
{
    // Data yang diberikan
    // private $data = [
    //     ['usia' => 4, 'jk' => 1, 'penyakit_id' => 1],
    //     ['usia' => 5, 'jk' => 2, 'penyakit_id' => 1],
    //     ['usia' => 4, 'jk' => 1, 'penyakit_id' => 236],
    //     ['usia' => 8, 'jk' => 1, 'penyakit_id' => 148],
    //     ['usia' => 8, 'jk' => 2, 'penyakit_id' => 148],
    //     ['usia' => 9, 'jk' => 1, 'penyakit_id' => 209],
    //     ['usia' => 10, 'jk' => 2, 'penyakit_id' => 233],
    //     ['usia' => 10, 'jk' => 1, 'penyakit_id' => 233],
    //     ['usia' => 9, 'jk' => 2, 'penyakit_id' => 6]
    // ];
    private $data = [];

    public function manualKMeansCalculation()
    {
        // $dataset = Dataset::all();
        // Ambil data dari view_hasil_transform (pastikan TransformedDataset model diarahkan ke view ini)
        $dataset = TransformedDataset::all();

        // Transformasi $dataset ke array numerik sesuai kebutuhan KMeans
        $this->data = $dataset->map(function ($item) {
            return [
                'pasien' => $item->pasien,
                'usia' => (int) $item->usia_id,
                'jk' => (int) $item->kelamin_id,
                'penyakit_id' => (int) $item->penyakit_id
            ];
        })->toArray();

        // Konversi data ke array numerik untuk perhitungan
        $points = array_map(function ($item) {
            return [
                $item['usia'],
                $item['jk'],
                $item['penyakit_id']
            ];
        }, $this->data);

        // Jumlah cluster
        $k = 3;

        // Inisialisasi centroid awal (manual)
        $centroids = $this->initializeCentroids($points, $k);

        // Variabel untuk menyimpan detail iterasi
        $iterationDetails = [];

        // Proses iterasi manual
        for ($iteration = 0; $iteration < 4; $iteration++) {
            // Hitung jarak setiap point ke centroid
            $distanceMatrix = $this->calculateDistanceMatrix($points, $centroids);

            // Penugasan cluster
            $clusterAssignments = $this->assignToClusters($distanceMatrix);

            // Hitung centroid baru
            $newCentroids = $this->calculateNewCentroids($points, $clusterAssignments);

            // Simpan detail iterasi
            $iterationDetails[] = [
                'iteration' => $iteration + 1,
                'centroids' => $centroids,
                'distance_matrix' => $distanceMatrix,
                'cluster_assignments' => $clusterAssignments,
                'new_centroids' => $newCentroids
            ];

            // Cek konvergensi
            if ($this->checkConvergence($centroids, $newCentroids)) {
                break;
            }

            // Update centroids
            $centroids = $newCentroids;
        }

        // Visualisasi hasil
        $results = $this->visualizeResults($iterationDetails, $points);
        $wcss = $this->calculateWCSSForMultipleK($results['iterations']);
        $calculatePatientStats = $this->calculatePatientStats();
        // dd($calculatePatientStats);

        return view('clustering', [
            'calculatePatientStats' => $calculatePatientStats,
            'original_data' => $results['original_data'],
            'transformed_data' => $results['transformed_data'],
            'iterations' => $results['iterations'],
            'clusters' => $results['iterations'][count($results['iterations']) - 1]['clusters'], // Mengambil cluster dari iterasi terakhir
            'centroids' => $results['iterations'][count($results['iterations']) - 1]['new_centroids'] // Menambahkan centroid terakhir
        ]);
    }

    private function calculatePatientStats()
    {
        $stats = [
            'jumlah_per_penyakit' => [],
            'jumlah_per_jenis_kelamin' => [
                'laki_laki' => 0,
                'perempuan' => 0,
            ],
            'jumlah_per_usia' => [],
        ];

        foreach ($this->data as $patient) {
            // Hitung jumlah pasien berdasarkan penyakit
            $penyakit = $patient['penyakit_id'];
            if (!isset($stats['jumlah_per_penyakit'][$penyakit])) {
                $stats['jumlah_per_penyakit'][$penyakit] = 0;
            }
            $stats['jumlah_per_penyakit'][$penyakit]++;

            // Hitung jumlah pasien berdasarkan jenis kelamin
            if ($patient['jk'] == 1) {
                // Laki-laki
                $stats['jumlah_per_jenis_kelamin']['laki_laki']++;
            } elseif ($patient['jk'] == 2) {
                // Perempuan
                $stats['jumlah_per_jenis_kelamin']['perempuan']++;
            }

            // Hitung jumlah pasien berdasarkan usia
            $usia = $patient['usia'];
            if (!isset($stats['jumlah_per_usia'][$usia])) {
                $stats['jumlah_per_usia'][$usia] = 0;
            }
            $stats['jumlah_per_usia'][$usia]++;
        }

        return $stats;
    }

    public function updateDatasetClusters(Request $request)
    {
        $clustersJson = $request->input('cluster_assignments', '');

        // Decode the JSON string into an associative array
        $clusters = json_decode($clustersJson, true);

        // Check if decoding was successful and if the result is an array
        if (!is_array($clusters)) {
            return response()->json(['status' => 'error', 'message' => 'Invalid cluster data'], 400);
        }

        foreach ($clusters as $cluster) {
            $clusterNumber = $cluster['cluster'] ?? null;
            if (is_null($clusterNumber)) {
                \Log::warning('Cluster number is null', $cluster);
                continue;
            }

            if (!isset($cluster['members']) || !is_array($cluster['members'])) {
                \Log::warning('Members are missing or not an array', $cluster);
                continue;
            }

            foreach ($cluster['members'] as $member) {
                $pasienName = $member['pasien'] ?? null;
                if (is_null($pasienName)) {
                    \Log::warning('Pasien name is null', $member);
                    continue;
                }

                $updatedRows = Dataset::where('pasien', $pasienName)->update(['cluster' => $clusterNumber]);
                if ($updatedRows === 0) {
                    \Log::warning('No rows updated for pasien: ' . $pasienName);
                }
            }
        }
        return redirect()->route('dataset.index')->with('success', 'Hasil cluster berhasil disimpan!');
    }

    private function mapUsia(string $rentang): int
    {
        return match (trim($rentang)) {
            '0-7 hari' => 1,
            '8-28 hari' => 2,
            '1-11 bln' => 3,
            '1-4 thn' => 4,
            '5-9 thn' => 5,
            '10-14 thn' => 6,
            '15-19 thn' => 7,
            '20-44 thn' => 8,
            '45-59 thn' => 9,
            '> 59' => 10,
        };
    }

    private function mapKelamin(string $jenisKelamin): int
    {
        return match (trim($jenisKelamin)) {
            'Laki-laki' => 1,
            'Perempuan' => 2,
        };
    }

    private function mapPenyakitId(string $penyakitName): int
    {
        $penyakitId = \App\Models\JenisPenyakit::all()
            ->first(function ($jp) use ($penyakitName) {
                return strtolower(preg_replace('/\s+/', '', $jp->name)) === $penyakitName;
            })?->id;

        return (int) $penyakitId;
    }

    private function initializeCentroids(array $points, int $k)
    {
        // Inisialisasi centroid manual
        return [
            $points[0],  // Centroid 1: point pertama
            $points[4],  // Centroid 2: point kelima
            $points[8]   // Centroid 3: point terakhir
        ];
    }

    private function calculateDistanceMatrix(array $points, array $centroids)
    {
        $distanceMatrix = [];

        foreach ($points as $pointIndex => $point) {
            $distances = [];

            foreach ($centroids as $centroidIndex => $centroid) {
                // Hitung jarak Euclidean
                $distance = $this->euclideanDistance($point, $centroid);
                $distances[$centroidIndex] = $distance;
            }

            $distanceMatrix[$pointIndex] = $distances;
        }

        return $distanceMatrix;
    }

    private function assignToClusters(array $distanceMatrix)
    {
        $clusterAssignments = [];

        foreach ($distanceMatrix as $pointIndex => $distances) {
            // Temukan jarak minimal (cluster terdekat)
            $closestCluster = array_keys($distances, min($distances))[0];
            $clusterAssignments[$closestCluster][] = $pointIndex;
        }

        return $clusterAssignments;
    }

    private function calculateNewCentroids(array $points, array $clusterAssignments)
    {
        $newCentroids = [];

        foreach ($clusterAssignments as $clusterIndex => $memberIndices) {
            // Ambil point-point dalam cluster
            $clusterPoints = array_map(fn($idx) => $points[$idx], $memberIndices);

            // Hitung rata-rata untuk setiap dimensi
            $newCentroid = array_map(function ($dimension) use ($clusterPoints) {
                return array_sum(array_column($clusterPoints, $dimension)) / count($clusterPoints);
            }, array_keys($points[0]));

            $newCentroids[] = $newCentroid;
        }

        return $newCentroids;
    }

    private function euclideanDistance(array $point1, array $point2)
    {
        return sqrt(
            pow($point1[0] - $point2[0], 2) +  // Jarak usia
                pow($point1[1] - $point2[1], 2) +  // Jarak jenis kelamin
                pow($point1[2] - $point2[2], 2)    // Jarak penyakit ID
        );
    }

    private function checkConvergence(array $oldCentroids, array $newCentroids, float $tolerance = 0.001)
    {
        foreach ($oldCentroids as $index => $oldCentroid) {
            $distance = $this->euclideanDistance($oldCentroid, $newCentroids[$index]);

            if ($distance > $tolerance) {
                return false;
            }
        }

        return true;
    }

    private function visualizeResults(array $iterationDetails, array $points)
    {
        $result = [
            'original_data' => Dataset::select('pasien', 'jenis_penyakit', 'kelompok_usia', 'jenis_kelamin')
                ->get()
                ->toArray(),
            'transformed_data' => $this->data,
            'iterations' => []
        ];

        foreach ($iterationDetails as $iteration) {
            $iterationResult = [
                'iteration_number' => $iteration['iteration'],
                'initial_centroids' => $iteration['centroids'],
                'new_centroids' => $iteration['new_centroids'],
                'clusters' => []
            ];

            // Detail cluster
            foreach ($iteration['cluster_assignments'] as $clusterIndex => $memberIndices) {
                $clusterMembers = array_map(function ($idx) use ($points) {
                    // Ambil data pasien berdasarkan index dan kembalikan sebagai bagian dari member
                    $pasienData = $this->data[$idx] ?? null; // Dapatkan data pasien

                    return [
                        'index' => $idx,
                        'pasien' => $pasienData ? $pasienData['pasien'] : 'Unknown', // Tambahkan nama pasien
                        'point' => $points[$idx]
                    ];
                }, $memberIndices);

                $iterationResult['clusters'][] = [
                    'cluster' => $clusterIndex + 1,
                    'members' => $clusterMembers,
                    'size' => count($memberIndices)
                ];
            }

            $result['iterations'][] = $iterationResult;
        }

        return $result;
    }

    // Contoh cara menampilkan perhitungan detail
    public function displayDetailedCalculation()
    {
        $result = $this->manualKMeansCalculation();

        // Tampilkan detail setiap iterasi
        foreach ($result['iterations'] as $iteration) {
            echo "Iterasi {$iteration['iteration_number']}:\n";

            // Tampilkan centroid
            echo "Centroid Awal: " . json_encode($iteration['initial_centroids']) . "\n";
            echo "Centroid Baru: " . json_encode($iteration['new_centroids']) . "\n";

            // Tampilkan detail cluster
            foreach ($iteration['clusters'] as $cluster) {
                echo "Cluster {$cluster['cluster']}:\n";
                echo "Ukuran: {$cluster['size']}\n";
                echo "Anggota: " . json_encode($cluster['members']) . "\n\n";
            }
        }
    }

    public function calculateWCSSForMultipleK()
    {
        $dataset = Dataset::all();

        // Transformasi $dataset ke array numerik sesuai kebutuhan KMeans
        $this->data = $dataset->map(function ($item) {
            return [
                'pasien' => $item->pasien,
                'usia' => $this->mapUsia($item->kelompok_usia),
                'jk' => $this->mapKelamin($item->jenis_kelamin),
                'penyakit_id' => $this->mapPenyakitId(strtolower(preg_replace('/\s+/', '', $item->jenis_penyakit)))
            ];
        })->toArray();

        // Konversi data ke array numerik untuk perhitungan
        $points = array_map(function ($item) {
            return [
                $item['usia'],
                $item['jk'],
                $item['penyakit_id']
            ];
        }, $this->data);

        $wcssValues = [];

        // Hitung WCSS untuk k dari 1 sampai 6
        for ($k = 1; $k <= 6; $k++) {
            // Inisialisasi centroid awal (manual) - adjusted for k
            $centroids = $this->initializeCentroids($points, $k);  // Make sure this supports dynamic k

            $iterationDetails = [];

            for ($iteration = 0; $iteration < 4; $iteration++) {
                // Hitung jarak setiap point ke centroid
                $distanceMatrix = $this->calculateDistanceMatrix($points, $centroids);

                // Penugasan cluster
                $clusterAssignments = $this->assignToClusters($distanceMatrix);

                // Hitung centroid baru
                $newCentroids = $this->calculateNewCentroids($points, $clusterAssignments);

                // Hitung WCSS untuk iterasi ini
                $wcss = $this->calculateWCSS($points, $clusterAssignments, $newCentroids);

                // Simpan detail iterasi
                $iterationDetails[] = [
                    'iteration' => $iteration + 1,
                    'centroids' => $centroids,
                    'distance_matrix' => $distanceMatrix,
                    'cluster_assignments' => $clusterAssignments,
                    'new_centroids' => $newCentroids,
                    'wcss' => $wcss // Store WCSS for this iteration
                ];

                // Cek konvergensi
                if ($this->checkConvergence($centroids, $newCentroids)) {
                    break;
                }

                // Update centroids
                $centroids = $newCentroids;
            }

            // Store the WCSS value for this value of k
            $wcssValues[$k] = end($iterationDetails)['wcss'];  // Store the last WCSS value
        }

        // Return the WCSS values for each k
        return $wcssValues;
    }

    private function calculateWCSS(array $points, array $clusterAssignments, array $centroids)
    {
        $wcss = 0;

        foreach ($clusterAssignments as $clusterIndex => $memberIndices) {
            foreach ($memberIndices as $index) {
                // Calculate the Euclidean distance from point to its centroid
                $distance = $this->euclideanDistance($points[$index], $centroids[$clusterIndex]);
                $wcss += pow($distance, 2); // Add squared distance to WCSS
            }
        }

        return $wcss;
    }
}
