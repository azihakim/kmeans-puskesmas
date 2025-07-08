<?php

namespace App\Http\Controllers;

use App\Models\Dataset;
use Illuminate\Support\Facades\Log;

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

    public function manualKMeansCalculation()
    {
        $dataset = Dataset::all();
        // Transformasi $dataset ke array numerik sesuai kebutuhan KMeans
        $this->data = $dataset->map(function ($item) {
            return [
                'usia' => $this->mapUsia($item->kelompok_usia),
                'jk' => $this->mapKelamin($item->jenis_kelamin),
                'penyakit_id' => $this->mapPenyakitId(strtolower(preg_replace('/\s+/', '', $item->jenis_penyakit)))
            ];
        })->toArray();
        // dd($this->data);
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
            // Tahap 1: Hitung jarak setiap point ke centroid
            $distanceMatrix = $this->calculateDistanceMatrix($points, $centroids);

            // Tahap 2: Assignment cluster
            $clusterAssignments = $this->assignToClusters($distanceMatrix);

            // Tahap 3: Hitung centroid baru
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

            // Update centroid
            $centroids = $newCentroids;
        }

        // Visualisasi hasil
        return $this->visualizeResults($iterationDetails, $points);
    }

    private function mapUsia(string $rentang): int
    {
        return match (trim($rentang)) {
            '0-7 hari' => 1,
            '8-28 hari' => 2,
            '1-11 bulan' => 3,
            '1-4 thn' => 4,
            '5-9 thn' => 5,
            '10-14 thn' => 6,
            '15-19 thn' => 7,
            '20-44 thn' => 8,
            '45-59 thn' => 9,
            '> 59 thn' => 10,
            default => 0
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
            'original_data' => $this->data,
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
                    return [
                        'index' => $idx,
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
}
