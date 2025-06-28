<?php

namespace App\Services;

use App\Models\Dataset;
use App\Models\JenisPenyakit;
use Illuminate\Support\Collection;

class KMeansService
{
    protected int $k;
    protected int $maxIterations = 100;
    protected float $tolerance = 1e-6; // Toleransi untuk konvergensi
    protected array $iterationDetails = [];
    public function __construct(int $k = 3)
    {
        $this->k = $k;
    }

    public function run(): array
    {
        // 1. Ambil dan transform data
        $data = Dataset::all()->map(function ($item) {
            // Mengambil ID jenis penyakit berdasarkan namanya
            // Deteksi pintar: samakan lowercase dan hapus spasi untuk pencocokan
            $penyakitName = strtolower(preg_replace('/\s+/', '', $item->jenis_penyakit));
            $penyakitId = JenisPenyakit::all()
                ->first(function ($jp) use ($penyakitName) {
                    return strtolower(preg_replace('/\s+/', '', $jp->name)) === $penyakitName;
                })?->id;

            return [
                'usia' => $this->mapUsia($item->kelompok_usia),
                'jk' => $this->mapKelamin($item->jenis_kelamin),
                'penyakit_id' => $penyakitId, // Gunakan ID penyakit yang ditemukan
                'id' => $item->id,
                'dataset_id' => $item->id,
            ];
        });
        // dd($data->toArray());
        // Filter out items where penyakit_id is null
        $data = $data->filter(fn($item) => $item['penyakit_id'] !== null);

        if ($data->isEmpty()) {
            throw new \Exception('Data tidak ditemukan atau semua jenis penyakit tidak valid');
        }

        $points = $data->map(fn($item) => [$item['usia'], $item['jk'], $item['penyakit_id']])->values()->all();

        // 2. Inisialisasi centroid dengan K-means++ atau random dengan seed
        $centroids = $this->initializeCentroids($points, $this->k);

        $previousCentroids = null;
        $clusters = [];

        for ($iteration = 0; $iteration < $this->maxIterations; $iteration++) {
            $clusters = array_fill(0, $this->k, []);

            // 3. Assign data ke centroid terdekat
            foreach ($points as $idx => $point) {
                $distances = array_map(fn($c) => $this->euclideanDistance($point, $c), $centroids);
                $clusterIndex = array_keys($distances, min($distances))[0];
                $clusters[$clusterIndex][] = $idx;
            }

            // 4. Update centroid
            $newCentroids = [];
            foreach ($clusters as $cluster) {
                if (empty($cluster)) {
                    // Jika cluster kosong, reinisialisasi dengan point random
                    $newCentroids[] = $points[array_rand($points)];
                } else {
                    $clusterPoints = array_map(fn($idx) => $points[$idx], $cluster);
                    $newCentroids[] = $this->calculateCentroid($clusterPoints);
                }
            }

            // 5. Cek konvergensi dengan toleransi
            if ($this->hasConverged($centroids, $newCentroids)) {
                break;
            }

            $previousCentroids = $centroids;
            $centroids = $newCentroids;
        }

        // 6. Hasil akhir
        $clusterResults = [];
        $labels = array_fill(0, count($points), -1);

        foreach ($clusters as $i => $cluster) {
            foreach ($cluster as $idx) {
                $clusterResults[] = [
                    'dataset_id' => $data[$idx]['id'],
                    'cluster' => $i + 1, // Mulai dari 1 bukan 0
                    'pasien' => Dataset::find($data[$idx]['id'])->pasien ?? 'Unknown'
                ];
                $labels[$idx] = $i;
            }
        }

        $result = [
            'clusters' => $clusterResults,
            'centroids' => $centroids,
            'metrics' => [
                'silhouette' => $this->calculateSilhouetteScore($points, $labels, $this->k),
                'wcss' => $this->calculateWCSS($clusters, $centroids, $points),
                'dbi' => $this->calculateDaviesBouldinIndex($clusters, $centroids, $points),
            ],
            'iterations' => $iteration + 1,
            'iteration_details' => $this->iterationDetails // Tambahkan detail iterasi
        ];

        return $result;
    }

    public function getIterationDetails(): array
    {
        return $this->iterationDetails;
    }

    /**
     * Inisialisasi centroid dengan metode K-means++
     */
    private function initializeCentroids(array $points, int $k): array
    {
        $centroids = [];
        $n = count($points);

        // Pilih centroid pertama secara random
        $centroids[] = $points[array_rand($points)];

        // Pilih centroid selanjutnya berdasarkan jarak terjauh
        for ($i = 1; $i < $k; $i++) {
            $distances = [];

            foreach ($points as $point) {
                $minDistance = INF;
                foreach ($centroids as $centroid) {
                    $distance = $this->euclideanDistance($point, $centroid);
                    $minDistance = min($minDistance, $distance);
                }
                $distances[] = $minDistance * $minDistance; // Kuadrat jarak
            }

            // Pilih berdasarkan probabilitas proporsional dengan jarak
            $totalDistance = array_sum($distances);
            if ($totalDistance == 0) {
                $centroids[] = $points[array_rand($points)];
            } else {
                $rand = mt_rand() / mt_getrandmax() * $totalDistance;
                $sum = 0;
                foreach ($distances as $idx => $distance) {
                    $sum += $distance;
                    if ($sum >= $rand) {
                        $centroids[] = $points[$idx];
                        break;
                    }
                }
            }
        }

        return $centroids;
    }

    /**
     * Cek apakah algoritma sudah konvergen
     */
    private function hasConverged(array $oldCentroids, array $newCentroids): bool
    {
        if (count($oldCentroids) !== count($newCentroids)) {
            return false;
        }

        for ($i = 0; $i < count($oldCentroids); $i++) {
            $distance = $this->euclideanDistance($oldCentroids[$i], $newCentroids[$i]);
            if ($distance > $this->tolerance) {
                return false;
            }
        }

        return true;
    }

    private function euclideanDistance(array $a, array $b): float
    {
        if (count($a) !== count($b)) {
            throw new \InvalidArgumentException('Dimensi array tidak sama');
        }

        return sqrt(array_sum(array_map(fn($x, $y) => pow($x - $y, 2), $a, $b)));
    }

    private function calculateCentroid(array $points): array
    {
        if (empty($points)) {
            throw new \InvalidArgumentException('Tidak ada points untuk menghitung centroid');
        }

        $dimensions = count($points[0]);
        $sums = array_fill(0, $dimensions, 0);

        foreach ($points as $point) {
            foreach ($point as $dim => $val) {
                $sums[$dim] += $val;
            }
        }

        return array_map(fn($sum) => $sum / count($points), $sums);
    }

    private function calculateWCSS(array $clusters, array $centroids, array $points): float
    {
        $totalWCSS = 0;

        foreach ($clusters as $clusterIndex => $cluster) {
            if (empty($cluster)) continue;

            $centroid = $centroids[$clusterIndex];

            foreach ($cluster as $idx) {
                $point = $points[$idx];
                $distance = $this->euclideanDistance($point, $centroid);
                $totalWCSS += pow($distance, 2);
            }
        }

        return $totalWCSS;
    }

    /**
     * Hitung Davies-Bouldin Index untuk evaluasi cluster
     */
    private function calculateDaviesBouldinIndex(array $clusters, array $centroids, array $points): float
    {
        $k = count($clusters);
        $db = 0;

        for ($i = 0; $i < $k; $i++) {
            if (empty($clusters[$i])) continue;

            $maxRatio = 0;
            $si = $this->calculateIntraClusterDistance($clusters[$i], $centroids[$i], $points);

            for ($j = 0; $j < $k; $j++) {
                if ($i === $j || empty($clusters[$j])) continue;

                $sj = $this->calculateIntraClusterDistance($clusters[$j], $centroids[$j], $points);
                $dij = $this->euclideanDistance($centroids[$i], $centroids[$j]);

                if ($dij > 0) {
                    $ratio = ($si + $sj) / $dij;
                    $maxRatio = max($maxRatio, $ratio);
                }
            }

            $db += $maxRatio;
        }

        return $k > 0 ? $db / $k : 0;
    }

    private function calculateIntraClusterDistance(array $cluster, array $centroid, array $points): float
    {
        if (empty($cluster)) return 0;

        $totalDistance = 0;
        foreach ($cluster as $idx) {
            $totalDistance += $this->euclideanDistance($points[$idx], $centroid);
        }

        return $totalDistance / count($cluster);
    }

    public function evaluateRangeK(int $maxK = 6): array
    {
        // Transform data
        $data = Dataset::all()->map(function ($item) {
            return [
                'usia' => $item->kelompok_usia,
                'jk' => $item->jenis_kelamin,
                'penyakit' => $item->jenis_penyakit,
                'id' => $item->id,
                'dataset_id' => $item->dataset_id
            ];
        });

        if ($data->isEmpty()) {
            throw new \Exception('Data tidak ditemukan untuk evaluasi');
        }

        $points = $data->map(fn($item) => [$item['usia'], $item['jk'], $item['penyakit']])->values()->all();
        $results = [];

        for ($k = 1; $k <= min($maxK, count($points)); $k++) {
            $bestWCSS = INF;
            $bestSilhouette = -1;

            // Jalankan beberapa kali untuk mendapatkan hasil terbaik
            for ($run = 0; $run < 5; $run++) {
                $centroids = $this->initializeCentroids($points, $k);
                $clusters = [];

                for ($i = 0; $i < $this->maxIterations; $i++) {
                    $clusters = array_fill(0, $k, []);

                    foreach ($points as $idx => $point) {
                        $distances = array_map(fn($c) => $this->euclideanDistance($point, $c), $centroids);
                        $clusterIndex = array_keys($distances, min($distances))[0];
                        $clusters[$clusterIndex][] = $idx;
                    }

                    $newCentroids = [];
                    foreach ($clusters as $cluster) {
                        if (empty($cluster)) {
                            $newCentroids[] = $points[array_rand($points)];
                        } else {
                            $clusterPoints = array_map(fn($idx) => $points[$idx], $cluster);
                            $newCentroids[] = $this->calculateCentroid($clusterPoints);
                        }
                    }

                    if ($this->hasConverged($centroids, $newCentroids)) break;
                    $centroids = $newCentroids;
                }

                $wcss = $this->calculateWCSS($clusters, $centroids, $points);

                if ($wcss < $bestWCSS) {
                    $bestWCSS = $wcss;

                    if ($k > 1) {
                        $labels = array_fill(0, count($points), -1);
                        foreach ($clusters as $clusterIdx => $cluster) {
                            foreach ($cluster as $pointIdx) {
                                $labels[$pointIdx] = $clusterIdx;
                            }
                        }
                        $bestSilhouette = $this->calculateSilhouetteScore($points, $labels, $k);
                    }
                }
            }

            $results[] = [
                'k' => $k,
                'wcss' => round($bestWCSS, 2),
                'silhouette' => $k > 1 ? round($bestSilhouette, 3) : 'N/A'
            ];
        }

        return $results;
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

    private function calculateSilhouetteScore(array $points, array $labels, int $k): float
    {
        $n = count($points);
        if ($n <= 1 || $k <= 1) return 0;

        $silhouetteSum = 0;
        $validPoints = 0;

        for ($i = 0; $i < $n; $i++) {
            if ($labels[$i] === -1) continue; // Skip unlabeled points

            $pi = $points[$i];
            $cluster_i = $labels[$i];

            // Hitung rata-rata jarak intra-cluster (a)
            $intraDistSum = 0;
            $intraCount = 0;

            for ($j = 0; $j < $n; $j++) {
                if ($i === $j || $labels[$j] !== $cluster_i) continue;
                $intraDistSum += $this->euclideanDistance($pi, $points[$j]);
                $intraCount++;
            }

            $a = $intraCount > 0 ? $intraDistSum / $intraCount : 0;

            // Hitung rata-rata jarak ke cluster terdekat (b)
            $minInterDist = INF;

            for ($otherCluster = 0; $otherCluster < $k; $otherCluster++) {
                if ($otherCluster === $cluster_i) continue;

                $interDistSum = 0;
                $interCount = 0;

                for ($j = 0; $j < $n; $j++) {
                    if ($labels[$j] !== $otherCluster) continue;
                    $interDistSum += $this->euclideanDistance($pi, $points[$j]);
                    $interCount++;
                }

                if ($interCount > 0) {
                    $avgInterDist = $interDistSum / $interCount;
                    $minInterDist = min($minInterDist, $avgInterDist);
                }
            }

            $b = $minInterDist === INF ? 0 : $minInterDist;

            // Hitung silhouette coefficient
            if ($a === 0 && $b === 0) {
                $silhouette = 0;
            } else {
                $silhouette = ($b - $a) / max($a, $b);
            }

            $silhouetteSum += $silhouette;
            $validPoints++;
        }

        return $validPoints > 0 ? $silhouetteSum / $validPoints : 0;
    }
}
