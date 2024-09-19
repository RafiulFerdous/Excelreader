<?php

namespace App\Imports;

use App\Models\Excel;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\BeforeImport;
use Maatwebsite\Excel\Events\BeforeSheet;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Events\BeforeWriting;
use Illuminate\Support\Facades\Storage;

class ExcelUpload implements ToModel,WithHeadingRow, WithEvents
{

    protected $invalidRows = [];
    protected $currentRow = 1;

    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        $this->currentRow++;
        $validator = Validator::make($row, [
            'email' => 'required|email',
            'phone' => ['required', 'regex:/^\+880\d{9}$/'],
            'gender' => 'required',
        ]);

        if ($validator->fails()) {

            if ($validator->errors()->has('email')) {
                $this->invalidRows[] = [
                    'email'   => $row['email'],
                    'row'     => $this->currentRow,
                    'column'  => 'email',
                    'error'   => $validator->errors()->first('email'),
                ];
            }

            // Check phone validation
            if ($validator->errors()->has('phone')) {
                $this->invalidRows[] = [
                    'phone'   => $row['phone'],
                    'row'     => $this->currentRow,
                    'column'  => 'phone',
                    'error'   => $validator->errors()->first('phone'),
                ];
            }

            // Check gender validation
            if ($validator->errors()->has('gender')) {
                $this->invalidRows[] = [
                    'gender'  => $row['gender'],
                    'row'     => $this->currentRow,
                    'column'  => 'gender',
                    'error'   => $validator->errors()->first('gender'),
                ];
            }
//            return null;
        }
        if (isset($row['date_of_birth'])) {
            try {

                if (is_numeric($row['date_of_birth'])) {

                    $dob = Carbon::createFromFormat('Y-m-d', \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['date_of_birth'])->format('Y-m-d'));
                } else {

                    $dob = Carbon::createFromFormat('Y-m-d', $row['date_of_birth']);
                }


                if ($dob->isFuture()) {
                    throw new \Exception('future date not allowed');
                }


                $age = Carbon::now()->diffInYears($dob);
                if ($age < 18) {
                    throw new \Exception('Under 18');
                }


                $row['date_of_birth'] = $dob->format('Y-m-d');
            } catch (\Exception $e) {

                $this->invalidRows[] = [
                    'value'   => $dob,
                    'row'     => $this->currentRow,
                    'column'  => 'date_of_birth',
                    'error'   => $e->getMessage(),
                ];



//                $row['date_of_birth'] = null;
            }
        }


        return new Excel([
            'name'        => $row['name'],
            'email'       => $row['email'],
            'phone'       => $row['phone'],
            'gender'      => $row['gender'],

//            'date_of_birth' => Carbon::parse($row['date_of_birth'])->format('Y-m-d'),
            'date_of_birth' => $dob,
        ]);
    }

    public function getInvalidRows()
    {
        return $this->invalidRows;
    }

    public function registerEvents(): array
    {
        return [
            BeforeImport::class => function (BeforeImport $event) {
                $this->currentRow = 1;
            },

            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $highestRow = $sheet->getHighestRow();
                $highestColumn = $sheet->getHighestColumn();


                $sheet->setCellValue($highestColumn . '1', 'Error');


                foreach ($this->invalidRows as $error) {
                    $sheet->setCellValue(
                        'E' . $error['row'],
                        $error['error']
                    );
                }
            },
        ];
    }


}
