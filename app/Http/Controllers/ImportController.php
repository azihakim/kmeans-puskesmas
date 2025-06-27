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
        $penyakitTanpaKasus = $import->getPenyakitTanpaKasus();
        dd($penyakitTanpaKasus);
        return redirect()->back()
            ->with('success', 'Data berhasil diimpor')
            ->with('penyakit_tanpa_kasus', $penyakitTanpaKasus);
    }
}
