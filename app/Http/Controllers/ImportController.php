<?php

namespace App\Http\Controllers;

use App\Imports\DatasetImport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ImportController extends Controller
{
    public function index()
    {
        return view('import_excell');
    }

    public function import(Request $request)
    {
        // Validasi input, pastikan file ada dan bertipe yang benar
        $request->validate([
            'excel_file' => 'required|file|mimes:xlsx,xls|max:2048', // Anda dapat mengatur batas ukuran file sesuai kebutuhan.
        ]);

        // Ambil file dari request
        $file = $request->file('excel_file');

        // Memastikan file tidak kosong
        if (!$file) {
            return redirect()->back()->withErrors(['excel_file' => 'File tidak ditemukan.']);
        }

        // Simpan proses import di dalam blok try-catch untuk menangkap potensi error
        try {
            $import = new DatasetImport();
            Excel::import($import, $file);

            // Dapatkan penyakit tanpa kasus
            $penyakitTanpaKasus = $import->getPenyakitTanpaKasus();
            $importResults = $import->getDatasets();
            $countPenyakitTanpaKasus = count($penyakitTanpaKasus);

            return redirect()->route('import.detail')
                ->with('success', 'Data berhasil diimpor')
                ->with('penyakit_tanpa_kasus', $penyakitTanpaKasus)
                ->with('count_penyakit_tanpa_kasus', $countPenyakitTanpaKasus)
                ->with('import_results', $importResults);
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['excel_file' => 'Terjadi kesalahan saat mengimpor data: ' . $e->getMessage()]);
        }
    }

    public function detailImport()
    {
        $penyakitTanpaKasus = session('penyakit_tanpa_kasus', []);
        $countPenyakitTanpaKasus = session('count_penyakit_tanpa_kasus', 0);

        // Ambil hasil import dari session jika ada
        $importResults = session('import_results', []); // Anda dapat menyimpan hasil seperti nama, deskripsi, dll.

        return view('detailImport', compact('penyakitTanpaKasus', 'countPenyakitTanpaKasus', 'importResults'));
    }
}
