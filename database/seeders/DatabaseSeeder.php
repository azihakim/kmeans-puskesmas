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
            'name' => 'Admin',
            'email' => 'admin@test.com',
            'role' => 'admin',
        ]);
        User::factory()->create([
            'name' => 'Pimpinan',
            'email' => 'pimpinan@test.com',
            'role' => 'pimpinan',
        ]);

        // Seeder untuk tabel jenis_penyakit
        $jenisPenyakit = [
            ['id' => 1, 'name' => 'Alergi makanan'],
            ['id' => 2, 'name' => 'Chikungunyah'],
            ['id' => 3, 'name' => 'a. Demam Berdarah'],
            ['id' => 4, 'name' => 'b. Demam Dengue'],
            ['id' => 5, 'name' => 'Filariasis'],
            ['id' => 6, 'name' => 'Infeksi pada Umbilikus'],
            ['id' => 7, 'name' => 'Kandidiasis Mulut'],
            ['id' => 8, 'name' => 'Keracunanan Makanan'],
            ['id' => 9, 'name' => 'Lepra'],
            ['id' => 10, 'name' => 'Leptopirosis'],
            ['id' => 11, 'name' => 'Malaria'],
            ['id' => 12, 'name' => 'Morbili (Campak)'],
            ['id' => 13, 'name' => 'Reaksi Anafilaktik'],
            ['id' => 14, 'name' => 'Syok'],
            ['id' => 15, 'name' => 'TB selain Paru (Ektra Paru)'],
            ['id' => 16, 'name' => 'Tuberkulosis (TB) Paru'],
            ['id' => 17, 'name' => 'Tuberkulosis dengan HIV'],
            ['id' => 18, 'name' => 'Varisela'],
            ['id' => 19, 'name' => 'Anemia defisiensi besi'],
            ['id' => 20, 'name' => 'HIV / AIDS tanpa komplikasi'],
            ['id' => 21, 'name' => 'Leukemia'],
            ['id' => 22, 'name' => 'Limfadenitis'],
            ['id' => 23, 'name' => 'Limfoma Maligna'],
            ['id' => 24, 'name' => 'Lupus Eritematosus Sistemik'],
            ['id' => 25, 'name' => 'Thalasemia'],
            ['id' => 26, 'name' => 'Ankilostomiosis'],
            ['id' => 27, 'name' => 'Apendisitis Akut'],
            ['id' => 28, 'name' => 'Askariasis'],
            ['id' => 29, 'name' => 'Atresia (ani) dengan atau tanpa fistula'],
            ['id' => 30, 'name' => 'Bibir dan Langit- Langit sumbing'],
            ['id' => 31, 'name' => 'Bibir Sumbing'],
            ['id' => 32, 'name' => 'Demam Tifoid'],
            ['id' => 33, 'name' => 'Disentri Basiler dan Disentri Amuba'],
            ['id' => 34, 'name' => 'Gstritis'],
            ['id' => 35, 'name' => 'Gastroentritis (Kolera dan Giardiasis)'],
            ['id' => 36, 'name' => 'Gastroschisis'],
            ['id' => 37, 'name' => 'Hemoroid grade 1-2'],
            ['id' => 38, 'name' => 'Hepatitis A'],
            ['id' => 39, 'name' => 'Hepatitis B'],
            ['id' => 40, 'name' => 'Hepatitis C'],
            ['id' => 41, 'name' => 'Intoleransi Makanan'],
            ['id' => 42, 'name' => 'Kolesistitis'],
            ['id' => 43, 'name' => 'Langit-LANGIT Sumbing'],
            ['id' => 44, 'name' => 'Malabsorbsi Makanan'],
            ['id' => 45, 'name' => 'Ompalocele'],
            ['id' => 46, 'name' => 'Parotitis'],
            ['id' => 47, 'name' => 'Pendarahan Gastrointetinal'],
            ['id' => 48, 'name' => 'Peritonitis'],
            ['id' => 49, 'name' => 'Refluks Gastroesofageal'],
            ['id' => 50, 'name' => 'Skistosomiasis'],
            ['id' => 51, 'name' => 'Strongiloidiasis'],
            ['id' => 52, 'name' => 'Taeniasis'],
            ['id' => 53, 'name' => 'Ulkus Mulut'],
            ['id' => 54, 'name' => 'Astigmatisme'],
            ['id' => 55, 'name' => 'Benda Asing Di Konjngtiva'],
            ['id' => 56, 'name' => 'Blefaritis'],
            ['id' => 57, 'name' => 'Buta Senja'],
            ['id' => 58, 'name' => 'Epikleritis'],
            ['id' => 59, 'name' => 'Glaukoma Akut'],
            ['id' => 60, 'name' => 'Glaukoma Kronis'],
            ['id' => 61, 'name' => 'Hifema'],
            ['id' => 62, 'name' => 'Hipermetropia'],
            ['id' => 63, 'name' => 'Hordeolum'],
            ['id' => 64, 'name' => 'Katarak Kongenital'],
            ['id' => 65, 'name' => 'Katarak pada pasien dewasa'],
            ['id' => 66, 'name' => 'a. Konjungtivitis Alergi'],
            ['id' => 67, 'name' => 'b. Konjungtivitis Infeksi'],
            ['id' => 68, 'name' => 'Laserasi Kelopak mata'],
            ['id' => 69, 'name' => 'Low Vision'],
            ['id' => 70, 'name' => 'Mata Kering'],
            ['id' => 71, 'name' => 'Miopia Ringan'],
            ['id' => 72, 'name' => 'Perdarahan Sub Konjungtiva'],
            ['id' => 73, 'name' => 'Presbiopia'],
            ['id' => 74, 'name' => 'Pterygium'],
            ['id' => 75, 'name' => 'Retinoblastoma'],
            ['id' => 76, 'name' => 'Retinopati Diabetik'],
            ['id' => 77, 'name' => 'Trauma Kimia Mata'],
            ['id' => 78, 'name' => 'Trikiasis'],
            ['id' => 79, 'name' => 'Benda Asing Di Telinga'],
            ['id' => 80, 'name' => 'Mastoiditis'],
            ['id' => 81, 'name' => 'Otitis Eksterna'],
            ['id' => 82, 'name' => 'Otitis Media Akut'],
            ['id' => 83, 'name' => 'Otitis media Supuratif Kronik'],
            ['id' => 84, 'name' => 'Presbiakusis'],
            ['id' => 85, 'name' => 'Serumen prop'],
            ['id' => 86, 'name' => 'Tuli akibat bising'],
            ['id' => 87, 'name' => 'Tuli Kongenital'],
            ['id' => 88, 'name' => 'Angina pektoris stabil'],
            ['id' => 89, 'name' => 'Cardiorespiratory arrest'],
            ['id' => 90, 'name' => 'gagal jantung akut dan kronik'],
            ['id' => 91, 'name' => 'Hypertensi esensial'],
            ['id' => 92, 'name' => 'Infark miokard'],
            ['id' => 93, 'name' => 'Takikardia'],
            ['id' => 94, 'name' => 'Artitis Reumatoid'],
            ['id' => 95, 'name' => 'Artritis, osteoartriris'],
            ['id' => 96, 'name' => 'Fraktrur terbuka'],
            ['id' => 97, 'name' => 'Fraktur tertutup'],
            ['id' => 98, 'name' => 'Lipoma'],
            ['id' => 99, 'name' => 'Osteoporosis'],
            ['id' => 100, 'name' => 'Osteosarkoma'],
            ['id' => 101, 'name' => 'Polimialgia reumatik'],
            ['id' => 102, 'name' => 'Reduction deformity'],
            ['id' => 103, 'name' => 'Talipes'],
            ['id' => 104, 'name' => 'Vulnus'],
            ['id' => 105, 'name' => 'Anencephaly'],
            ['id' => 106, 'name' => 'Bells Palsy'],
            ['id' => 107, 'name' => 'Delirium'],
            ['id' => 108, 'name' => 'Epilepsi'],
            ['id' => 109, 'name' => 'Kejang Demam'],
            ['id' => 110, 'name' => 'Meningo / encephalocele'],
            ['id' => 111, 'name' => 'Migren'],
            ['id' => 112, 'name' => 'Neuroblastoma'],
            ['id' => 113, 'name' => 'Rabies'],
            ['id' => 114, 'name' => 'Status epileptikus'],
            ['id' => 115, 'name' => 'Stroke'],
            ['id' => 116, 'name' => 'Tension Headache'],
            ['id' => 117, 'name' => 'Tetanus'],
            ['id' => 118, 'name' => 'Tetanus neonatorum'],
            ['id' => 119, 'name' => 'Transient Ischemic Attack (TIA)'],
            ['id' => 120, 'name' => 'Vertigo'],
            ['id' => 121, 'name' => 'Demensia'],
            ['id' => 122, 'name' => 'Gangguan Anxietas'],
            ['id' => 123, 'name' => 'gangguan Campuran Anxietas dan depresi'],
            ['id' => 124, 'name' => 'Gangguan Depresi'],
            ['id' => 125, 'name' => 'Gangguan penggunaan Napza'],
            ['id' => 126, 'name' => 'Gangguan perkembangan dan perilaku pada anak dan remaja'],
            ['id' => 127, 'name' => 'Gangguan Psikotik'],
            ['id' => 128, 'name' => 'Gangguan Somatoform'],
            ['id' => 129, 'name' => 'Insomnia'],
            ['id' => 130, 'name' => 'ISPA'],
            ['id' => 131, 'name' => 'Asma Bronkial'],
            ['id' => 132, 'name' => 'Asfiksia'],
            ['id' => 133, 'name' => 'Benda asing di hidung'],
            ['id' => 134, 'name' => 'Bronkitis akut (usia < 15 tahun)'],
            ['id' => 135, 'name' => 'Bronkitis akut (usia >15 tahun)'],
            ['id' => 136, 'name' => 'Difteria'],
            ['id' => 137, 'name' => 'Epistaksis'],
            ['id' => 138, 'name' => 'Faringitis Akut'],
            ['id' => 139, 'name' => 'Furunkel pada Hidung'],
            ['id' => 140, 'name' => 'Influenza'],
            ['id' => 141, 'name' => 'Kanker Nasofaring'],
            ['id' => 142, 'name' => 'Kanker paru'],
            ['id' => 143, 'name' => 'Laringitis Akut'],
            ['id' => 144, 'name' => 'Penyakit Paru Obstruktif Kronis'],
            ['id' => 145, 'name' => 'Pertusis (batuk rejan)'],
            ['id' => 146, 'name' => 'Pneumonia Aspirasi'],
            ['id' => 147, 'name' => 'a. Bronkopneumonia'],
            ['id' => 148, 'name' => 'b. Pneumonia'],
            ['id' => 149, 'name' => 'Pneumotoraks'],
            ['id' => 150, 'name' => 'Rinitis Akut'],
            ['id' => 151, 'name' => 'Rinitis Alergi'],
            ['id' => 152, 'name' => 'Rinitis Vasomotor'],
            ['id' => 153, 'name' => 'Sinusitis Akut'],
            ['id' => 154, 'name' => 'Status Asmatikus'],
            ['id' => 155, 'name' => 'a. Tonsilitis Akut'],
            ['id' => 156, 'name' => 'b. Tonsilitis Kronis'],
            ['id' => 157, 'name' => 'Acne Vulgaris Ringan'],
            ['id' => 158, 'name' => 'Cutaneus Larva Migrans'],
            ['id' => 159, 'name' => 'Dermatitis Atopik'],
            ['id' => 160, 'name' => 'Dermatitis kontak alergi'],
            ['id' => 161, 'name' => 'Dermatitis Kontak iritan'],
            ['id' => 162, 'name' => 'Dermatitis Numularis'],
            ['id' => 163, 'name' => 'Dermatitis perioral'],
            ['id' => 164, 'name' => 'Dermatitis popok'],
            ['id' => 165, 'name' => 'Dermatitis Seboroik'],
            ['id' => 166, 'name' => 'a. Tinea capitis dan tinea barbea'],
            ['id' => 167, 'name' => 'b. Tinea corporis'],
            ['id' => 168, 'name' => 'c. Tinea cruris'],
            ['id' => 169, 'name' => 'd. Tinea manuum'],
            ['id' => 170, 'name' => 'e. Tinea pedis'],
            ['id' => 171, 'name' => 'f. Tinea unguium'],
            ['id' => 172, 'name' => 'Erisipelas'],
            ['id' => 173, 'name' => 'Eritrasma'],
            ['id' => 174, 'name' => 'Exanthematous drug Eruption'],
            ['id' => 175, 'name' => 'Fixed Drug Eruption'],
            ['id' => 176, 'name' => 'Frambusia RDT (+) konfirmasi/probable'],
            ['id' => 177, 'name' => 'Herpes simplek'],
            ['id' => 178, 'name' => 'Herpes zooster'],
            ['id' => 179, 'name' => 'Hidradeniis supuratif'],
            ['id' => 180, 'name' => 'Liken simpleks kronik (Neurodermatitis Sirkumkripta)'],
            ['id' => 181, 'name' => 'Luka bakar derajat I dan II'],
            ['id' => 182, 'name' => 'Miliaria'],
            ['id' => 183, 'name' => 'Moluskum Kontagiosum'],
            ['id' => 184, 'name' => 'Pedikulosis kapitis'],
            ['id' => 185, 'name' => 'Pedikulosis Pubis'],
            ['id' => 186, 'name' => 'a. Abses, furuncke dan carbuncle'],
            ['id' => 187, 'name' => 'b. Impetigo'],
            ['id' => 188, 'name' => 'c. Pioderma'],
            ['id' => 189, 'name' => 'Pitiriasis Rosea'],
            ['id' => 190, 'name' => 'Pitiriasis Versikolor'],
            ['id' => 191, 'name' => 'Reaksi Gigitan Serangga'],
            ['id' => 192, 'name' => 'Sindrom Stevens Johnson'],
            ['id' => 193, 'name' => 'Skabies'],
            ['id' => 194, 'name' => 'Skrofuloderma'],
            ['id' => 195, 'name' => 'Ulkus pada tungkai'],
            ['id' => 196, 'name' => 'Urtikaria'],
            ['id' => 197, 'name' => 'Veruka vulgaris'],
            ['id' => 198, 'name' => 'Diabetes Mellitus Tipe 1'],
            ['id' => 199, 'name' => 'Diabetes Mellitus Tipe 2'],
            ['id' => 200, 'name' => 'Hiperglikemia Hiperesmolar Non Ketotik'],
            ['id' => 201, 'name' => 'Hiperurismia - Gout Arthritis'],
            ['id' => 202, 'name' => 'Hipoglikemia'],
            ['id' => 203, 'name' => 'Hipertiroid kongenital'],
            ['id' => 204, 'name' => 'Lipidemia'],
            ['id' => 205, 'name' => 'Malnutrisi energi protein'],
            ['id' => 206, 'name' => 'Obesitas'],
            ['id' => 207, 'name' => 'Tirotoksikosis'],
            ['id' => 208, 'name' => 'Epispadia'],
            ['id' => 209, 'name' => 'Fimosis'],
            ['id' => 210, 'name' => 'Hipertropi Prostat'],
            ['id' => 211, 'name' => 'Hypospadia'],
            ['id' => 212, 'name' => 'Infeksi saluran kemih'],
            ['id' => 213, 'name' => 'Parafimosis'],
            ['id' => 214, 'name' => 'Penyakit Ginjal Kronik'],
            ['id' => 215, 'name' => 'Pielonefritis tanpa komplikasi'],
            ['id' => 216, 'name' => 'a. Abortus Inkomplit'],
            ['id' => 217, 'name' => 'b. Abortus Komplit'],
            ['id' => 218, 'name' => 'Anemia defisiensi besi pada kehamilan'],
            ['id' => 219, 'name' => 'Cracked Nippple'],
            ['id' => 220, 'name' => 'Eklampsi'],
            ['id' => 221, 'name' => 'Hiperemesis gravidarum'],
            ['id' => 222, 'name' => 'Inverted Nipple'],
            ['id' => 223, 'name' => 'Kanker serviks'],
            ['id' => 224, 'name' => 'Kehamilan Normal'],
            ['id' => 225, 'name' => 'Ketuban pecah dini'],
            ['id' => 226, 'name' => 'Mastitis'],
            ['id' => 227, 'name' => 'Perdarahan post partum'],
            ['id' => 228, 'name' => 'Persalinan lama'],
            ['id' => 229, 'name' => 'Pre eklampsia'],
            ['id' => 230, 'name' => 'Ruftur perinium tingkat 1-2'],
            ['id' => 231, 'name' => 'Tumor payudara'],
            ['id' => 232, 'name' => 'Fluor albus'],
            ['id' => 233, 'name' => 'Sifilis'],
            ['id' => 234, 'name' => 'Gonore'],
            ['id' => 235, 'name' => 'Vaginitis'],
            ['id' => 236, 'name' => 'Vulvitis'],
        ];
        \DB::table('jenis_penyakits')->insert($jenisPenyakit);

        // Dataset::truncate();
        // $data = [
        //     ['pasien' => 'Ani', 'jenis_penyakit' => '1', 'kelompok_usia' => '1-4 tahun', 'jenis_kelamin' => 'Perempuan'],
        //     ['pasien' => 'Budi', 'jenis_penyakit' => '2', 'kelompok_usia' => '5-9 tahun', 'jenis_kelamin' => 'Laki-laki'],
        //     ['pasien' => 'Chika', 'jenis_penyakit' => '3', 'kelompok_usia' => '20-44 tahun', 'jenis_kelamin' => 'Perempuan'],
        //     ['pasien' => 'Dedi', 'jenis_penyakit' => '1', 'kelompok_usia' => '10-14 tahun', 'jenis_kelamin' => 'Laki-laki'],
        //     ['pasien' => 'Euis', 'jenis_penyakit' => '4', 'kelompok_usia' => '1-4 tahun', 'jenis_kelamin' => 'Perempuan'],1
        //     ['pasien' => 'Farhan', 'jenis_penyakit' => '5', 'kelompok_usia' => '15-19 tahun', 'jenis_kelamin' => 'Laki-laki'],
        //     ['pasien' => 'Gita', 'jenis_penyakit' => '6', 'kelompok_usia' => '45-59 tahun', 'jenis_kelamin' => 'Perempuan'],
        //     ['pasien' => 'Heri', 'jenis_penyakit' => '7', 'kelompok_usia' => '>59 tahun', 'jenis_kelamin' => 'Laki-laki'],
        //     ['pasien' => 'Intan', 'jenis_penyakit' => '3', 'kelompok_usia' => '20-44 tahun', 'jenis_kelamin' => 'Perempuan'],
        //     ['pasien' => 'Joko', 'jenis_penyakit' => '2', 'kelompok_usia' => '5-9 tahun', 'jenis_kelamin' => 'Laki-laki'],
        // ];

        // foreach ($data as $item) {
        //     Dataset::create($item);
        // }

        $kelompokUsia = [
            ['id' => 1, 'name' => '0-7 hari'],
            ['id' => 2, 'name' => '8-28 hari'],
            ['id' => 3, 'name' => '1-11 bln'],
            ['id' => 4, 'name' => '1-4 thn'],
            ['id' => 5, 'name' => '5-9 thn'],
            ['id' => 6, 'name' => '10-14 thn'],
            ['id' => 7, 'name' => '15-19 thn'],
            ['id' => 8, 'name' => '20-44 thn'],
            ['id' => 9, 'name' => '45-59 thn'],
            ['id' => 10, 'name' => '> 59 thn'],
        ];

        // \DB::table('kelompok_usias')->insert($kelompokUsia);

        $testData = [
            // Cluster 1: Anak-anak dengan penyakit ringan
            ['pasien' => 'P1', 'usia' => '1-4 thn', 'jk' => 'Laki-laki', 'jenis_penyakit' => 'Alergi makanan'],
            ['pasien' => 'P2', 'usia' => '5-9 thn', 'jk' => 'Perempuan', 'jenis_penyakit' => 'Alergi makanan'],
            ['pasien' => 'P3', 'usia' => '1-4 thn', 'jk' => 'Laki-laki', 'jenis_penyakit' => 'Vulvitis'],

            // Cluster 2: Dewasa dengan penyakit berat
            ['pasien' => 'P4', 'usia' => '20-44 thn', 'jk' => 'Laki-laki', 'jenis_penyakit' => 'b. Pneumonia'],
            ['pasien' => 'P5', 'usia' => '20-44 thn', 'jk' => 'Perempuan', 'jenis_penyakit' => 'b. Pneumonia'],
            ['pasien' => 'P6', 'usia' => '45-59 thn', 'jk' => 'Laki-laki', 'jenis_penyakit' => 'Fimosis'],

            // Cluster 3: Lansia dengan penyakit kompleks
            ['pasien' => 'P7', 'usia' => '> 59 thn', 'jk' => 'Perempuan', 'jenis_penyakit' => 'Sifilis'],
            ['pasien' => 'P8', 'usia' => '> 59 thn', 'jk' => 'Laki-laki', 'jenis_penyakit' => 'Sifilis'],
            ['pasien' => 'P9', 'usia' => '45-59 thn', 'jk' => 'Perempuan', 'jenis_penyakit' => 'Infeksi pada Umbilikus']
        ];
        foreach ($testData as $data) {
            Dataset::create([
                'pasien' => $data['pasien'],
                'kelompok_usia' => $data['usia'],
                'jenis_kelamin' => $data['jk'],
                'jenis_penyakit' => $data['jenis_penyakit']
            ]);
        }
    }
}
