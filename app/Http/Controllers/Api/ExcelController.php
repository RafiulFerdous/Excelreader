<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\ExcelUpload;
use Illuminate\Support\Facades\Storage;

class ExcelController extends Controller
{
    public function uploadExcel(Request $request)
    {
        $file = $request->file('file');

        $request->validate([
            'file' => 'required|mimes:xlsx,csv',
        ]);


        $import = new ExcelUpload();


//        Excel::import($import, $request->file('file'));
        Excel::import($import, $file);


        $invalidRows = $import->getInvalidRows();
        $data = Excel::toArray(new ExcelUpload(), $file)[0];

        $export = new \App\Exports\ExcelErrorExport($data, $invalidRows);


        $tempFilePath = 'temp_import.xlsx';
        Excel::store($export, $tempFilePath, 'public');

        $fullPath = storage_path('app/public/' . $tempFilePath);


        if (!file_exists($fullPath)) {
            return response()->json([
                'message' => 'File creation failed.',
            ], 500);
        }

//        return response()->download($fullPath)->deleteFileAfterSend(true);
//        $dob=Carbon::createFromFormat('d-m-y', 2022-1-2);
//        $age = Carbon::now()->diffInYears($dob);


        return response()->json([
            'message' => 'File processed successfully',
            'invalid_rows' => $invalidRows,
//            'current_date'=>Carbon::now(),
//            'age'=>$age
        ]);
    }
}
