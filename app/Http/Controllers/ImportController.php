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
        $file = $request->file('excel_file');
        $import = new DatasetImport();
        Excel::import($import, $file);

        // Dapatkan penyakit tanpa kasus
        $penyakitTanpaKasus = $import->getPenyakitTanpaKasus(); // Ambil penyakit tanpa kasus
        $importResults = $import->getDatasets();
        $countPenyakitTanpaKasus = count($penyakitTanpaKasus);

        return redirect()->route('import.detail')
            ->with('success', 'Data berhasil diimpor')
            ->with('penyakit_tanpa_kasus', $penyakitTanpaKasus)
            ->with('count_penyakit_tanpa_kasus', $countPenyakitTanpaKasus)
            ->with('import_results', $importResults); // Menyimpan hasil import di session
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
