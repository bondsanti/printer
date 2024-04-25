<?php

namespace App\Http\Controllers;

use App\Models\LogPrinter;
use App\Imports\LogImport;
use App\Models\User;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;


class ExcelImportController extends Controller
{
    public function import(Request $request)
    {

        if ($request->hasFile('excel_file')) {

            $file = $request->file('excel_file');
            $printer = $request->printer;

            if ($printer) {
                Excel::import(new LogImport($printer), $file);
                return response()->json(['message' => 'Imported successfully'], 200);
            } else {
                return response()->json(['message' => 'No Selected Printer'], 400);
            }
        } else {
            return response()->json(['message' => 'No file uploaded'], 400);
        }
    }

    public function getData(Request $request)
    {
        $currentYear = Carbon::now()->year;

        //$users = User::with('dep')->take(10)->get();

        $data = LogPrinter::with(['user_ref:code,name_th,name_eng'])
        ->whereYear('date', $currentYear)
        ->orderByDesc('id')
        ->take(1000)->get();

        return response()->json(['data' => $data]);
    }


    public function getBarChartbyYear(Request $request)
    {
        $currentYear = Carbon::now()->year;
        $query = LogPrinter::whereYear('date', $currentYear)->where('jobstatus', 'Done');

        if ($request->has('printers')) {
            $printers = $request->printers;
            $query->whereIn('printername', $printers);
        }

        $data = $query->select(
            DB::raw('MONTH(date) as month'),
            DB::raw('SUM(total_color) as total_color'),
            DB::raw('SUM(total_bw) as total_bw')
        )
            ->groupBy(DB::raw('MONTH(date)'))
            ->orderBy('month')
            ->get();

        // สร้างข้อมูลสำหรับเดือนที่ไม่มีข้อมูลโดยให้ค่าเป็น 0
        $monthsWithData = $data->pluck('month')->toArray();
        $months = range(1, 12);
        $missingMonths = array_diff($months, $monthsWithData);

        foreach ($missingMonths as $missingMonth) {
            $data->push([
                'month' => $missingMonth,
                'total_color' => 0,
                'total_bw' => 0
            ]);
        }

        // เรียงข้อมูลตามเดือน
        $data = $data->sortBy('month')->values();

        return response()->json(['data' => $data]);
    }


    public function getPieChartbyYear(Request $request)
    {
        $currentYear = Carbon::now()->year;
        $query = LogPrinter::whereYear('date', $currentYear)->where('jobstatus', 'Done');


        $data = $query->select(
            'username',
            DB::raw('SUM(total_color) as total_color'),
            DB::raw('SUM(total_bw) as total_bw')
        )
            ->groupBy('username')
            ->get();


        return response()->json(['data' => $data]);
    }

    public function getBarChartbyYearwithUser(Request $request)
    {
        $currentYear = Carbon::now()->year;
        $query = LogPrinter::whereYear('date', $currentYear)->where('jobstatus', 'Done');

        if ($request->has('printers')) {
            $printers = $request->printers;
            $query->whereIn('printername', $printers);
        }

        $data = $query->select(
            'username',
            DB::raw('SUM(total_color) as total_color'),
            DB::raw('SUM(total_bw) as total_bw'),
            DB::raw('MONTH(date) as month')
        )
            ->groupBy('username', DB::raw('MONTH(date)'))
            ->orderBy('month')
            ->take(10) // เลือกเฉพาะ 5 รายการแรก
            ->get();

        return response()->json(['data' => $data]);
    }
}
