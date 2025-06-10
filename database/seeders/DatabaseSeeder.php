<?php

namespace Database\Seeders;

use App\Models\Dataset;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@test.com',
        ]);

        // Seeder untuk tabel jenis_penyakit
        $jenisPenyakit = [
            ['id' => 1, 'name' => 'Demam Berdarah'],
            ['id' => 2, 'name' => 'Malaria'],
            ['id' => 3, 'name' => 'TBC'],
            ['id' => 4, 'name' => 'ISPA'],
            ['id' => 5, 'name' => 'Diare'],
            ['id' => 6, 'name' => 'Hipertensi'],
            ['id' => 7, 'name' => 'Diabetes'],
        ];
        \DB::table('jenis_penyakits')->insert($jenisPenyakit);

        Dataset::truncate();
        $data = [
            ['pasien' => 'Ani', 'jenis_penyakit' => '1', 'kelompok_usia' => '1-4 tahun', 'jenis_kelamin' => 'Perempuan'],
            ['pasien' => 'Budi', 'jenis_penyakit' => '2', 'kelompok_usia' => '5-9 tahun', 'jenis_kelamin' => 'Laki-laki'],
            ['pasien' => 'Chika', 'jenis_penyakit' => '3', 'kelompok_usia' => '20-44 tahun', 'jenis_kelamin' => 'Perempuan'],
            ['pasien' => 'Dedi', 'jenis_penyakit' => '1', 'kelompok_usia' => '10-14 tahun', 'jenis_kelamin' => 'Laki-laki'],
            ['pasien' => 'Euis', 'jenis_penyakit' => '4', 'kelompok_usia' => '1-4 tahun', 'jenis_kelamin' => 'Perempuan'],
            ['pasien' => 'Farhan', 'jenis_penyakit' => '5', 'kelompok_usia' => '15-19 tahun', 'jenis_kelamin' => 'Laki-laki'],
            ['pasien' => 'Gita', 'jenis_penyakit' => '6', 'kelompok_usia' => '45-59 tahun', 'jenis_kelamin' => 'Perempuan'],
            ['pasien' => 'Heri', 'jenis_penyakit' => '7', 'kelompok_usia' => '>59 tahun', 'jenis_kelamin' => 'Laki-laki'],
            ['pasien' => 'Intan', 'jenis_penyakit' => '3', 'kelompok_usia' => '20-44 tahun', 'jenis_kelamin' => 'Perempuan'],
            ['pasien' => 'Joko', 'jenis_penyakit' => '2', 'kelompok_usia' => '5-9 tahun', 'jenis_kelamin' => 'Laki-laki'],
        ];

        foreach ($data as $item) {
            Dataset::create($item);
        }
    }
}
