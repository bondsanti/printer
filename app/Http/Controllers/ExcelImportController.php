<?php

namespace App\Http\Controllers;

use App\Models\LogPrinter;
use App\Imports\LogImport;
use App\Imports\QuotaImport;
use App\Models\Quota;
use App\Models\User;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use GuzzleHttp\Client;

class ExcelImportController extends Controller
{

    private function addApiDataToUser($data)
    {
        if (!$data) {
            return;
        }

        $client = new Client();
        $url = env('API_URL');
        $token = env('API_TOKEN_AUTH');

        $userIds = $data->pluck('code_user')->toArray();
        $userIdsString = implode(',', array_unique($userIds));

        try {

            $userResponse = $client->request('GET', $url . '/get-users/code/' . $userIdsString, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $token
                ]
            ]);

            if ($userResponse->getStatusCode() == 200) {
                $userApiResponse = json_decode($userResponse->getBody()->getContents(), true);

                if (isset($userApiResponse['data']['data'])) {
                    $userData = $userApiResponse['data']['data'];
                } else {
                    $userData = [];
                }
            } else {
                $userData = [];
            }

            foreach ($data as $item) {
                $userApiData = collect($userData)->firstWhere('code', $item->code_user);

                if (is_object($item)) {
                    $item->apiDataUser = $userApiData ? [
                        'code' => $userApiData['code'],
                        'name_th' => $userApiData['name_th'],
                        'department_id' => $userApiData['department_id'],
                        'department' => $userApiData['department'] ?: "Other",
                        'active' => $userApiData['active'],
                    ] : [
                        'code' => $item->code_user,
                        'name_th' => $item->username,
                        'department_id' => 0,
                        'department' => 'Other',
                        'active' => 1,
                    ];
                }
            }
        } catch (\Exception $e) {
            // Handle exception by setting apiDataUser to null for all items
            foreach ($data as $item) {
                if (is_object($item)) {
                    $item->apiDataUser = [
                        'code' => $item->code_user,
                        'name_th' => $item->username,
                        'department_id' => 0,
                        'department' => 'Other',
                        'active' => 1,
                    ];
                }
            }
        }
    }


    public function importData(Request $request)
    {

        if ($request->hasFile('excel_file')) {

            $file = $request->file('excel_file');
            $printer = $request->printer;

            if ($printer) {
                Excel::import(new LogImport($printer), $file);
                Log::addLog($request->session()->get('loginId'), 'ImportExcel', $printer);

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

        $data = LogPrinter::select('id', 'jobtype', 'date', 'username', 'time',  'jobstatus', 'code_user', 'printername', 'jobnumber', 'total_color', 'total_bw')
            ->wherein('jobtype', ['Print', 'Copy'])
            ->whereYear('date', $currentYear)
            ->orderByDesc('id')
            ->take(300)->get();

        $this->addApiDataToUser($data);

        return response()->json(['data' => $data], 200);
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

        return response()->json(['data' => $data], 200);
    }

    public function getBarChartbyYearWithDep(Request $request)
    {
        $currentYear = Carbon::now()->year;
        $query = LogPrinter::select('id', 'jobtype', 'date', 'username', 'time', 'jobstatus', 'code_user', 'printername', 'jobnumber', 'total_color', 'total_bw')
            ->whereYear('date', $currentYear)
            ->where('jobstatus', 'Done')
            ->whereIn('jobtype', ['Print', 'Copy']);

        if ($request->has('printers')) {
            $printers = $request->printers;
            $query->whereIn('printername', $printers);
        }

        $data = $query->get();
        $this->addApiDataToUser($data);

        $data->each(function ($item) {
            $item->total = $item->total_color + $item->total_bw;
        });


        $departmentSums = $data->groupBy(function ($item) {
            return $item->apiDataUser['department_id'] ?? 0; // Set to 0 if department_id is null
        })->map(function ($items, $departmentId) {
            $departmentName = $items->first()->apiDataUser['department'] ?? 'Other'; // Default to 'Other' if department is null

            // Check for department_id = 0 or department_name = 'Other'
            if ($departmentId == 0 || $departmentName == 'Other') {
                $departmentId = 0;
                $departmentName = 'Other';
            }

            return [
                'department_id' => $departmentId,
                'department_name' => $departmentName,
                'total_color' => $items->sum('total_color'),
                'total_bw' => $items->sum('total_bw'),
                'total' => $items->sum('total'),
            ];
        })->values();

        // Sort by total in descending order
        $sortedDepartmentSums = $departmentSums->sortByDesc('total')->values();

        return response()->json(['data' => $sortedDepartmentSums], 200);
    }

    public function getPieChartbyYearWithPrinter(Request $request)
    {
        $currentYear = Carbon::now()->year;
        $query = LogPrinter::select('id', 'jobtype', 'date', 'username', 'time',  'jobstatus', 'code_user', 'printername', 'jobnumber', 'total_color', 'total_bw')
            ->whereYear('date', $currentYear)
            ->where('jobstatus', 'Done')
            ->whereIn('jobtype', ['Print', 'Copy']);

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

        return response()->json(['data' => $data], 200);
    }

    public function getSimiDonutChartbyYearWithUser(Request $request)
    {
        $currentYear = Carbon::now()->year;
        $query = LogPrinter::select('id', 'jobtype', 'date', 'username', 'time', 'jobstatus', 'code_user', 'printername', 'jobnumber', 'total_color', 'total_bw')
            ->whereYear('date', $currentYear)
            ->where('jobstatus', 'Done')
            ->whereIn('jobtype', ['Print', 'Copy']);

        if ($request->has('printers')) {
            $printers = $request->printers;
            $query->whereIn('printername', $printers);
        }

        $data = $query->get();
        $this->addApiDataToUser($data);

        $data->each(function ($item) {
            $item->total = $item->total_color + $item->total_bw;
        });

        $userSums = $data->groupBy(function ($item) {
            return $item->apiDataUser['name_th'] ?? 'Other';
        })->map(function ($items, $name) {
            return [
                'name_th' => $name,
                'total_color' => $items->sum('total_color'),
                'total_bw' => $items->sum('total_bw'),
                'total' => $items->sum('total')
            ];
        });

        // Sort by total in descending order
        $sortedUserSums = $userSums->sortByDesc('total')->take(10)->values();

        return response()->json(['data' => $sortedUserSums], 200);
    }

    public function importQuota(Request $request)
    {
        if ($request->hasFile('excel_file')) {

            $file = $request->file('excel_file');
            Excel::import(new QuotaImport, $file);
            Log::addLog($request->session()->get('loginId'), 'ImportExcel', $file);
            return response()->json(['message' => 'Imported successfully'], 200);
        }
    }

    public function getQuota(Request $request)
    {


        $data = Quota::select('name', 'code_user', 'department', 'total_color_24', 'total_bw_24', 'total_color_25', 'total_bw_25')->get();
        $this->addApiDataToUser($data);

        return response()->json(['data' => $data], 200);
    }
}
