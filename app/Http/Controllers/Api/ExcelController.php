<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\ExcelUpload;

class ExcelController extends Controller
{
    public function uploadExcel(Request $request)
    {
        // Validate the uploaded file
        $request->validate([
            'file' => 'required|mimes:xlsx,csv',
        ]);

        // Import the file using the ExcelUpload import class
        $file = $request->file('file');
        Excel::import(new ExcelUpload, $file);

        return response()->json(['message' => 'File uploaded and data imported successfully']);
    }
}
