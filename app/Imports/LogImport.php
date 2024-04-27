<?php

namespace App\Imports;

use App\Models\LogPrinter;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithBatchInserts;

class LogImport implements ToModel, WithHeadingRow, WithChunkReading, WithBatchInserts
{

    use Importable;

    private $printer;

    public function __construct($printer)
    {
        $this->printer = $printer;
    }


    public function model(array $row)
    {
        // dd($row);
        $fullday = $row['year'] . "-" . sprintf("%02d",$row['month']) . "-" . sprintf("%02d", $row['day']);
        $timeValueFromExcel = $row['end_time'];
        // แปลงค่าเวลาให้เป็นชั่วโมง:นาที:วินาที
        $hours = floor($timeValueFromExcel * 24);
        $minutes = floor(($timeValueFromExcel * 24 * 60) % 60);
        $seconds = floor(($timeValueFromExcel * 24 * 60 * 60) % 60);
        // แสดงผลลัพธ์
        $time = sprintf("%02d:%02d:%02d", $hours, $minutes, $seconds);
        //dd($time);
        $existingRecord = LogPrinter::where('printername', $this->printer)
        ->where('jobnumber', $row['job_number'])->exists();

        if (!$existingRecord) {
            return new LogPrinter([
                'printername'  => $this->printer,
                'date'  => $fullday,
                'time' => $time,
                'jobtype' => $row['job_type'],
                'pc_name' => $row['pc_name'],
                'code_user' => $row['user_id'],
                'username' => $row['user_name'],
                'filename' => $row['file_name'],
                'jobstatus' => $row['job_processing_status'],
                'jobnumber' => $row['job_number'],
                'total_color' => $row['total_color_impressions'],
                'total_bw' => $row['total_bw_impressions'],
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
