<?php

namespace App\Http\Controllers;

use App\Models\LogPrinter;
use App\Models\Quota;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use GuzzleHttp\Client;
use Illuminate\Http\Request;

class ReportController extends Controller
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


    public function getData(Request $request)
    {
        $query = LogPrinter::where('jobstatus', 'Done')->whereIn('jobtype', ['Print', 'Copy']);

        if ($request->has('startDate') && $request->has('endDate')) {
            $startDate = date('Y-m-d', strtotime($request->startDate));
            $endDate = date('Y-m-d', strtotime($request->endDate));
            $query->whereBetween('date', [$startDate, $endDate]);
        }

        if ($request->has('code')) {
            $code = $request->code;
            $query->whereIn('code_user', is_array($code) ? $code : [$code]);
        }

        $data = $query->get();
        $this->addApiDataToUser($data);

        $departments = [];

        foreach ($data as $item) {
            $departmentId = $item->apiDataUser['department_id'] ?? 0;
            $departmentName = $item->apiDataUser['department'] ?? 'Other';
            $userCode = $item->apiDataUser['code'] ?? 'Unknown';
            $userName = $item->apiDataUser['name_th'] ?? 'Unknown';

            if (!isset($departments[$departmentId])) {
                $departments[$departmentId] = [
                    'department_id' => $departmentId,
                    'department_name' => $departmentName,
                    'total_bw' => 0,
                    'total_color' => 0,
                    'total' => 0,
                    'users' => []
                ];
            }

            if (!isset($departments[$departmentId]['users'][$userCode])) {
                $departments[$departmentId]['users'][$userCode] = [
                    'code' => $userCode,
                    'name' => $userName,
                    'total_bw' => 0,
                    'total_color' => 0,
                    'total' => 0,
                    'quota' => [
                        'total_color_24' => 0,
                        'total_bw_24' => 0,
                        'total_color_25' => 0,
                        'total_bw_25' => 0,
                    ]
                ];
            }


            $departments[$departmentId]['users'][$userCode]['total_bw'] += $item->total_bw;
            $departments[$departmentId]['users'][$userCode]['total_color'] += $item->total_color;
            $departments[$departmentId]['users'][$userCode]['total'] += $item->total_bw + $item->total_color;


            $quota = Quota::where('code_user', $userCode)->first();

            if ($quota) {
                $departments[$departmentId]['users'][$userCode]['quota'] = [
                    'total_color_24' => $quota->total_color_24,
                    'total_bw_24' => $quota->total_bw_24,
                    'total_color_25' => $quota->total_color_25,
                    'total_bw_25' => $quota->total_bw_25,
                ];
            }


            $departments[$departmentId]['total_bw'] += $item->total_bw;
            $departments[$departmentId]['total_color'] += $item->total_color;
            $departments[$departmentId]['total'] += $item->total_bw + $item->total_color;
            $departments[$departmentId]['total_color'] += $item->total_color;
        }

        $formattedData = [];
        foreach ($departments as $department) {
            $formattedUsers = array_values($department['users']);
            $formattedData[] = [
                'department_id' => $department['department_id'],
                'department_name' => $department['department_name'],
                'total_bw' => $department['total_bw'],
                'total_color' => $department['total_color'],
                'total' => $department['total'],
                'users' => $formattedUsers
            ];
        }

        return response()->json(['data' => $formattedData], 200);
    }


    public function getDepartment(Request $request)
    {
        $query = LogPrinter::select('id', 'jobtype', 'date', 'username', 'time', 'jobstatus', 'code_user', 'printername', 'jobnumber', 'total_color', 'total_bw')
            ->where('jobstatus', 'Done')->wherein('jobtype', ['Print', 'Copy']);
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

    public function getUser(Request $request)
    {
        $query = LogPrinter::select('id', 'jobtype', 'date', 'username', 'time', 'jobstatus', 'code_user', 'printername', 'jobnumber', 'total_color', 'total_bw')
            ->where('jobstatus', 'Done')->wherein('jobtype', ['Print', 'Copy']);
        $data = $query->get();
        $this->addApiDataToUser($data);


        $userSums = $data->groupBy(function ($item) {
            return $item->apiDataUser['name_th'] ?? 'Other';
        })->map(function ($items, $name) {
            $code = $items->first()->apiDataUser['code'] ?? 'Other';
            return [
                'code' => $code,
                'name_th' => $name,
            ];
        })->values();

        $sortedUserSums = $userSums->sortBy('name_th')->values();

        return response()->json(['data' => $sortedUserSums], 200);
    }

    public function getBarChartbyDep(Request $request)
    {


        $query = LogPrinter::where('jobstatus', 'Done')->whereIn('jobtype', ['Print', 'Copy']);

        if ($request->has('startDate') && $request->has('endDate')) {
            $startDate = date('Y-m-d', strtotime($request->startDate));
            $endDate = date('Y-m-d', strtotime($request->endDate));
            $query->whereBetween('date', [$startDate, $endDate]);
        }

        if ($request->has('printers')) {
            $printers = $request->printers;
            $query->whereIn('printername', $printers);
        }

        if ($request->has('code')) {
            // dd($request->has('depId'));
            $code = $request->code;
            //dd($code);
            $query->whereIn('code_user', is_array($code) ? $code : [$code]);
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

    public function getPieChartbyPrinter(Request $request)
    {
        $query = LogPrinter::where('jobstatus', 'Done')->whereIn('jobtype', ['Print', 'Copy']);

        if ($request->has('startDate') && $request->has('endDate')) {
            $startDate = date('Y-m-d', strtotime($request->startDate));
            $endDate = date('Y-m-d', strtotime($request->endDate));
            $query->whereBetween('date', [$startDate, $endDate]);
        }
        if ($request->has('code')) {
            // dd($request->has('depId'));
            $code = $request->code;
            //dd($code);
            $query->whereIn('code_user', is_array($code) ? $code : [$code]);
        }
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

    public function getBarChartbyUser(Request $request)
    {
        $query = LogPrinter::select('id', 'jobtype', 'date', 'username', 'time', 'jobstatus', 'code_user', 'printername', 'jobnumber', 'total_color', 'total_bw')
            ->where('jobstatus', 'Done')
            ->whereIn('jobtype', ['Print', 'Copy']);

        if ($request->has('startDate') && $request->has('endDate')) {
            $startDate = date('Y-m-d', strtotime($request->startDate));
            $endDate = date('Y-m-d', strtotime($request->endDate));
            $query->whereBetween('date', [$startDate, $endDate]);
        }

        if ($request->has('printers')) {
            $printers = $request->printers;
            $query->whereIn('printername', $printers);
        }
        if ($request->has('code')) {
            // dd($request->has('depId'));
            $code = $request->code;
            //dd($code);
            $query->whereIn('code_user', is_array($code) ? $code : [$code]);
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
        $sortedUserSums = $userSums->sortByDesc('total')->values();

        return response()->json(['data' => $sortedUserSums], 200);
    }

    public function getDataPrinter(Request $request)
    {

        $printerNames = LogPrinter::distinct()->pluck('printername');

        $query = LogPrinter::where('jobstatus', 'Done')
            ->whereIn('jobtype', ['Print', 'Copy'])
            ->select(
                'printername',
                DB::raw('SUM(total_color) as total_color'),
                DB::raw('SUM(total_bw) as total_bw'),
                DB::raw('SUM(total_color + total_bw) as total')
            )
            ->groupBy('printername');

        if ($request->has('startDate') && $request->has('endDate')) {
            $startDate = date('Y-m-d', strtotime($request->startDate));
            $endDate = date('Y-m-d', strtotime($request->endDate));
            $query->whereBetween('date', [$startDate, $endDate]);
        }

        if ($request->has('code')) {
            $code = $request->code;
            $query->whereIn('code_user', is_array($code) ? $code : [$code]);
        }

        $data = $query->get();


        $data = $printerNames->map(function ($printerName) use ($data) {
            $found = $data->firstWhere('printername', $printerName);
            return [
                'printername' => $printerName,
                'total_color' => $found ? $found->total_color : 0,
                'total_bw' => $found ? $found->total_bw : 0,
                'total' => $found ? $found->total : 0
            ];
        });

        return response()->json(['data' => $data], 200);
    }

    public function searchData(Request $request)
    {
    }
}
