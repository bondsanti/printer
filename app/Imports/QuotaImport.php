<?php

namespace App\Imports;

use App\Models\Quota;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithBatchInserts;

class QuotaImport implements ToModel, WithHeadingRow, WithChunkReading, WithBatchInserts
{

    use Importable;


    public function model(array $row)
    {
        //dd($row);
        $existingRecord = Quota::where('code_user', $row['code'])->exists();

        if (!$existingRecord) {
            return new Quota([
                'name' => $row['name'],
                'department' => $row['department'],
                'code_user' => $row['code'],
                'printername' => 'FujiEmpire',
                'total_color_24' => $row['color_24'],
                'total_bw_24' => $row['bw_24'],
                'total_color_25' => $row['color_25'],
                'total_bw_25' => $row['bw_25'],
            ]);
        } else {
            return null;
        }
    }

    public function headingRow(): int
    {
        return 1;
    }
    public function batchSize(): int
    {
        return 1000;
    }
    public function chunkSize(): int
    {
        return 1000;
    }
}
