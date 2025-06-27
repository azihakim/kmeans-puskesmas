<?php

namespace App\Imports;

use App\Models\Dataset;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class DatasetImport implements ToModel, WithHeadingRow
{
    private $penyakitTanpaKasus = [];

    public function model(array $row)
    {
        $datasets = [];

        // Daftar kolom usia dengan nama sesuai array
        $ageColumns = [
            '0_7_hari',
            '8_28_hari',
            '1_11_bln',
            '1_4_thn',
            '5_9_thn',
            '10_14_thn',
            '15_19_thn',
            '20_44_thn',
            '45_59_thn',
            '59'
        ];

        // Pastikan baris memiliki jenis penyakit
        if (empty($row['jenis_penyakit'])) {
            return null;
        }

        // Cek apakah ada pasien di semua kelompok usia
        $totalPasien = 0;
        foreach ($ageColumns as $ageColumn) {
            $totalPasien += (int)$row[$ageColumn];
        }

        // Jika tidak ada pasien, tambahkan ke daftar penyakit tanpa kasus
        if ($totalPasien == 0) {
            $this->penyakitTanpaKasus[] = $row['jenis_penyakit'];
            return null;
        }

        // Iterasi melalui kolom usia
        foreach ($ageColumns as $ageColumn) {
            // Mapping nama kolom usia yang lebih readable
            $readableAgeColumn = match ($ageColumn) {
                '0_7_hari' => '0-7 hari',
                '8_28_hari' => '8-28 hari',
                '1_11_bln' => '1-11 bln',
                '1_4_thn' => '1-4 thn',
                '5_9_thn' => '5-9 thn',
                '10_14_thn' => '10-14 thn',
                '15_19_thn' => '15-19 thn',
                '20_44_thn' => '20-44 thn',
                '45_59_thn' => '45-59 thn',
                '59' => '> 59',
                default => $ageColumn
            };

            // Hitung total pasien di kelompok usia ini
            $totalPasienDiKelompokUsia = (int)$row[$ageColumn];

            // Jumlah pasien laki-laki dan perempuan dari kolom L dan P
            $totalLakiLaki = (int)$row['l'];
            $totalPerempuan = (int)$row['p'];

            // Jika ada pasien di kelompok usia ini
            if ($totalPasienDiKelompokUsia > 0) {
                // Distribusikan pasien secara proporsional
                $distribusi = $this->distributePatients(
                    $totalPasienDiKelompokUsia,
                    $totalLakiLaki,
                    $totalPerempuan
                );

                // Buat dataset sesuai distribusi
                foreach ($distribusi as $jenisKelamin => $jumlah) {
                    for ($i = 0; $i < $jumlah; $i++) {
                        $datasets[] = new Dataset([
                            'pasien' => 'P' . (count($datasets) + 1),
                            'jenis_penyakit' => $row['jenis_penyakit'],
                            'kelompok_usia' => $readableAgeColumn,
                            'jenis_kelamin' => $jenisKelamin
                        ]);
                    }
                }
            }
        }

        return $datasets;
    }

    /**
     * Mendistribusikan pasien berdasarkan proporsi L dan P
     */
    private function distributePatients(int $totalPasien, int $totalLakiLaki, int $totalPerempuan): array
    {
        // Jika total pasien 0, kembalikan array kosong
        if ($totalPasien == 0) {
            return [];
        }

        // Jika total L dan P 0, distribusi acak
        if ($totalLakiLaki == 0 && $totalPerempuan == 0) {
            $lakiLaki = floor($totalPasien / 2);
            return [
                'Laki-laki' => $lakiLaki,
                'Perempuan' => $totalPasien - $lakiLaki
            ];
        }

        // Hitung proporsi
        $totalKelamin = $totalLakiLaki + $totalPerempuan;
        $proporsiLakiLaki = $totalLakiLaki / $totalKelamin;

        // Distribusikan pasien
        $lakiLaki = round($totalPasien * $proporsiLakiLaki);
        $perempuan = $totalPasien - $lakiLaki;

        return [
            'Laki-laki' => $lakiLaki,
            'Perempuan' => $perempuan
        ];
    }

    /**
     * Mendapatkan daftar penyakit tanpa kasus
     *
     * @return array
     */
    public function getPenyakitTanpaKasus(): array
    {
        return $this->penyakitTanpaKasus;
    }
}
