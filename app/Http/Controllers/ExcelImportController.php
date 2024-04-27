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
    public function importData(Request $request)
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

        $data = LogPrinter::with(['user_ref:code,name_th,name_eng,department_id', 'user_ref.dep_ref:id,name'])
            ->wherein('jobtype', ['Print', 'Copy'])
            ->whereYear('date', $currentYear)
            ->orderByDesc('id')
            ->take(300)->get();

        return response()->json(['data' => $data],200);
    }

    public function getBarChartbyYear(Request $request)
    {
        $currentYear = Carbon::now()->year;
        $query = LogPrinter::whereYear('date', $currentYear)->where('jobstatus', 'Done')->wherein('jobtype', ['Print', 'Copy']);

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

        $data->each(function ($item) {
            $item->total = $item->total_color + $item->total_bw;
        });
        // สร้างข้อมูลสำหรับเดือนที่ไม่มีข้อมูลโดยให้ค่าเป็น 0
        $monthsWithData = $data->pluck('month')->toArray();
        $months = range(1, 12);
        $missingMonths = array_diff($months, $monthsWithData);

        foreach ($missingMonths as $missingMonth) {
            $data->push([
                'month' => $missingMonth,
                'total_color' => 0,
                'total_bw' => 0,
                'total' => 0
            ]);
        }

        // เรียงข้อมูลตามเดือน
        $data = $data->sortBy('month')->values();

        return response()->json(['data' => $data],200);
    }

    public function getBarChartbyYearWithDep(Request $request)
    {
        $currentYear = Carbon::now()->year;
        $query = LogPrinter::with(['user_ref:code,name_th,name_eng,department_id','user_ref.dep_ref:id,name'])
            ->whereYear('date', $currentYear)->where('jobstatus', 'Done')->wherein('jobtype', ['Print', 'Copy']);

        if ($request->has('printers')) {
            $printers = $request->printers;
            $query->whereIn('printername', $printers);
        }

        $data = $query->get();
        $data->each(function ($item) {
            $item->total = $item->total_color + $item->total_bw;
        });

        $departmentSums = $data->groupBy('user_ref.department_id')->map(function ($items, $departmentId) {
            $departmentName = $items->first()['user_ref']['dep_ref']['name'] ?? '-'; // ให้ค่าเป็น "-" ถ้า dep_ref มีค่าเป็น null
            return [
                'department_id' => $departmentId,
                'department_name' => $departmentName,
                'total_color' => $items->sum('total_color'),
                'total_bw' => $items->sum('total_bw'),
                'total' => $items->sum('total')
            ];
        })->values();

        return response()->json(['data' => $departmentSums],200);

    }

    public function getPieChartbyYearWithPrinter(Request $request)
    {
        $currentYear = Carbon::now()->year;
        $query = LogPrinter::whereYear('date', $currentYear)->where('jobstatus', 'Done')->whereIn('jobtype', ['Print', 'Copy']);

        // ตรวจสอบว่ามีการเลือก total_bw หรือ total_color หรือไม่
        $selectedColors = $request->colors ?? [];
        $hasTotalBW = in_array('total_bw', $selectedColors);
        $hasTotalColor = in_array('total_color', $selectedColors);

        if ($hasTotalBW && !$hasTotalColor) {
            // ถ้าเลือก total_bw เท่านั้น
            $query->select(
                DB::raw('printername as printer'),
                DB::raw('SUM(total_bw) as total'),
            );
        } elseif ($hasTotalColor && !$hasTotalBW) {
            // ถ้าเลือก total_color เท่านั้น
            $query->select(
                DB::raw('printername as printer'),
                DB::raw('SUM(total_color) as total'),
            );
        } elseif ($hasTotalBW && $hasTotalColor) {
            // ถ้าเลือกทั้ง total_bw และ total_color
            $query->select(
                DB::raw('printername as printer'),
                DB::raw('SUM(total_color + total_bw) as total'),
            );
        } else {
            // ถ้าไม่เลือกเลยหรือเลือกเฉพาะ total_bw เท่านั้น
            $query->select(
                DB::raw('printername as printer'),
                DB::raw('0 as total'),
            );
        }

        $data = $query->groupBy('printername')->get();

        return response()->json(['data' => $data],200);
    }

    public function getSimiDonutChartbyYearWithUser(Request $request)
    {
        $currentYear = Carbon::now()->year;
        $query = LogPrinter::with(['user_ref:code,name_th,name_eng'])
            ->whereYear('date', $currentYear)->where('jobstatus', 'Done')->wherein('jobtype', ['Print', 'Copy']);

        if ($request->has('printers')) {
            $printers = $request->printers;
            $query->whereIn('printername', $printers);
        }

        $data = $query->get();

        $data->each(function ($item) {
            $item->total = $item->total_color + $item->total_bw;
        });

        $UserSums = $data->groupBy('user_ref.code')->map(function ($items, $code) {
            $UserName = $items->first()['user_ref']['name_eng'] ?? $items->first()['username']; // ให้ค่าเป็น "-" ถ้า user_ref มีค่าเป็น null
            return [
                'code' => $code,
                'user' => $UserName,
                // 'total_color' => $items->sum('total_color'),
                // 'total_bw' => $items->sum('total_bw'),
                'total' => $items->sum('total')
            ];
        })->sortByDesc('total')->take(10)->values();

        return response()->json(['data' => $UserSums],200);
    }

}
