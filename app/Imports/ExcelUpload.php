<?php

namespace App\Imports;

use App\Models\Excel;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ExcelUpload implements ToModel,WithHeadingRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new Excel([
            'name'        => $row['name'],
            'email'       => $row['email'],
            'phone'       => $row['phone'],
            'gender'      => $row['gender'],

            'date_of_birth' => Carbon::parse($row['date_of_birth'])->format('Y-m-d'),
        ]);
    }
}
