<?php

namespace App\Http\Controllers;

use App\Models\LogPrinter;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    // public function getData(Request $request){

    //     $query = LogPrinter::with(['user_ref:code,name_th,name_eng,department_id', 'user_ref.dep_ref:id,name'])
    //     ->where('jobstatus', 'Done')->wherein('jobtype', ['Print', 'Copy']);
    //     if ($request->has('startDate') && $request->has('endDate')) {
    //         $startDate = date('Y-m-d', strtotime($request->startDate));
    //         $endDate = date('Y-m-d', strtotime($request->endDate));
    //         $query->whereBetween('date', [$startDate, $endDate]);
    //     }
    //    // $sql = $query->toSql();
    //     $data = $query->get();
    //     $data->each(function ($item) {
    //         $item->total = $item->total_color + $item->total_bw;
    //     });
    //     $departmentSums = $data->groupBy('user_ref.department_id')->map(function ($items, $departmentId) {
    //         $departmentName = $items->first()['user_ref']['dep_ref']['name'] ?? 'Other'; // ให้ค่าเป็น "-" ถ้า dep_ref มีค่าเป็น null
    //         return [
    //             'department_id' => $departmentId,
    //             'department_name' => $departmentName,
    //             'total_color' => $items->sum('total_color'),
    //             'total_bw' => $items->sum('total_bw'),
    //             'total' => $items->sum('total')
    //         ];
    //     })->sortByDesc('total')->values();
    //     return response()->json(['data' => $departmentSums],200);






    // }
    // public function getData(Request $request){
    //     $query = LogPrinter::with(['user_ref:code,name_th,name_eng,department_id', 'user_ref.dep_ref:id,name'])
    //                         ->where('jobstatus', 'Done')
    //                         ->whereIn('jobtype', ['Print', 'Copy']);

    //     if ($request->has('startDate') && $request->has('endDate')) {
    //         $startDate = date('Y-m-d', strtotime($request->startDate));
    //         $endDate = date('Y-m-d', strtotime($request->endDate));
    //         $query->whereBetween('date', [$startDate, $endDate]);
    //     }

    //     $data = $query->get();

    //     // สร้าง associative array เพื่อเก็บผลลัพธ์แยกตาม department_id
    //     $departmentSums = [];
    //     $UserSums = [];

    //     // วนลูปผลลัพธ์ที่ได้จากการ query
    //     foreach ($data as $item) {
    //         // ตรวจสอบว่าผู้ใช้มีความสัมพันธ์กับแผนกหรือไม่
    //         if ($item->user_ref && $item->user_ref->dep_ref) {
    //             $departmentId = $item->user_ref->dep_ref->id;
    //             $departmentName = $item->user_ref->dep_ref->name;
    //             // สร้างหรือเพิ่มข้อมูลใน associative array
    //             if (!isset($departmentSums[$departmentId])) {
    //                 $departmentSums[$departmentId] = [
    //                     'department_id' => $departmentId,
    //                     'department_name' => $departmentName,
    //                     'total_color' => 0,
    //                     'total_bw' => 0,
    //                     'total' => 0
    //                 ];
    //             }

    //             // เพิ่มค่า total_color และ total_bw
    //             $departmentSums[$departmentId]['total_color'] += $item->total_color;
    //             $departmentSums[$departmentId]['total_bw'] += $item->total_bw;

    //             // คำนวณผลรวม total
    //             $departmentSums[$departmentId]['total'] += $item->total_color + $item->total_bw;
    //         }
    //     }

    //     // แปลง associative array เป็น indexed array
    //     $departmentSums = array_values($departmentSums);

    //     foreach ($data as $item) {
    //         // ตรวจสอบว่าผู้ใช้มีความสัมพันธ์กับแผนกหรือไม่
    //         if ($item->user_ref && $item->user_ref->name_eng) {
    //             $depId = $item->user_ref->department_id;
    //             $userCode = $item->user_ref->code;
    //             $userName = $item->user_ref->name_eng;
    //             // สร้างหรือเพิ่มข้อมูลใน associative array
    //             if (!isset($UserSums[$userCode])) {
    //                 $UserSums[$userCode] = [
    //                     'depId' => $depId,
    //                     'code' => $userCode,
    //                     'users' => $userName,
    //                     'total_color' => 0,
    //                     'total_bw' => 0,
    //                     'total' => 0
    //                 ];
    //             }

    //             // เพิ่มค่า total_color และ total_bw
    //             $UserSums[$userCode]['total_color'] += $item->total_color;
    //             $UserSums[$userCode]['total_bw'] += $item->total_bw;

    //             // คำนวณผลรวม total
    //             $UserSums[$userCode]['total'] += $item->total_color + $item->total_bw;
    //         }
    //     }
    //     $UserSums = array_values($UserSums);

    //     return response()->json(['data' => $UserSums], 200);
    // }
    // public function getData(Request $request){
    //     $query = LogPrinter::with(['user_ref:code,name_th,name_eng,department_id', 'user_ref.dep_ref:id,name'])
    //                         ->where('jobstatus', 'Done')
    //                         ->whereIn('jobtype', ['Print', 'Copy']);

    //     if ($request->has('startDate') && $request->has('endDate')) {
    //         $startDate = date('Y-m-d', strtotime($request->startDate));
    //         $endDate = date('Y-m-d', strtotime($request->endDate));
    //         $query->whereBetween('date', [$startDate, $endDate]);
    //     }

    //     $data = $query->get();

    //     // สร้างโครงสร้างข้อมูลเริ่มต้นเพื่อเก็บข้อมูลแผนกและผู้ใช้งานในแต่ละแผนก
    //     $departments = [];

    //     // วนลูปผลลัพธ์ที่ได้จากการ query เพื่อนำข้อมูลเข้าสู่โครงสร้างข้อมูล
    //     foreach ($data as $item) {
    //         if ($item->user_ref && $item->user_ref->dep_ref) {
    //             $departmentId = $item->user_ref->dep_ref->id;
    //             $departmentName = $item->user_ref->dep_ref->name;

    //             // ตรวจสอบว่าแผนกมีอยู่ในโครงสร้างข้อมูลหรือไม่
    //             if (!isset($departments[$departmentId])) {
    //                 $departments[$departmentId] = [
    //                     'department_id' => $departmentId,
    //                     'department_name' => $departmentName,
    //                     'total_bw' => 0,
    //                     'total_color' => 0,
    //                     'total' => 0,
    //                     'users' => []
    //                 ];
    //             }

    //             // เพิ่มข้อมูลผู้ใช้งานในแผนก
    //             $userCode = $item->user_ref->code;
    //             $userName = $item->user_ref->name_eng;
    //             if (!isset($departments[$departmentId]['users'][$userCode])) {
    //                 $departments[$departmentId]['users'][$userCode] = [
    //                     'code' => $userCode,
    //                     'name' => $userName,
    //                     'total_bw' => 0,
    //                     'total_color' => 0,
    //                     'total' => 0
    //                 ];
    //             }

    //             // นับผลรวมการใช้งานของผู้ใช้งานในแผนก
    //             $departments[$departmentId]['users'][$userCode]['total_bw'] += $item->total_bw;
    //             $departments[$departmentId]['users'][$userCode]['total_color'] += $item->total_color;
    //             $departments[$departmentId]['users'][$userCode]['total'] += $item->total_bw + $item->total_color;
    //         }
    //     }

    //     // คำนวณผลรวมของแผนก
    //     foreach ($departments as &$department) {
    //         $department['total_bw'] = array_sum(array_column($department['users'], 'total_bw'));
    //         $department['total_color'] = array_sum(array_column($department['users'], 'total_color'));
    //         $department['total'] = array_sum(array_column($department['users'], 'total'));
    //     }

    //     return response()->json(['data' => array_values($departments)], 200);
    // }
    // public function getData(Request $request){
    //     $query = LogPrinter::with(['user_ref:code,name_th,name_eng,department_id', 'user_ref.dep_ref:id,name'])
    //                         ->where('jobstatus', 'Done')
    //                         ->whereIn('jobtype', ['Print', 'Copy']);

    //     if ($request->has('startDate') && $request->has('endDate')) {
    //         $startDate = date('Y-m-d', strtotime($request->startDate));
    //         $endDate = date('Y-m-d', strtotime($request->endDate));
    //         $query->whereBetween('date', [$startDate, $endDate]);
    //     }

    //     $data = $query->get();

    //     // สร้างโครงสร้างข้อมูลเริ่มต้นเพื่อเก็บข้อมูลแผนกและผู้ใช้งานในแต่ละแผนก
    //     $departments = [];

    //     // วนลูปผลลัพธ์ที่ได้จากการ query เพื่อนำข้อมูลเข้าสู่โครงสร้างข้อมูล
    //     foreach ($data as $item) {
    //         if ($item->user_ref && $item->user_ref->dep_ref) {
    //             $departmentId = $item->user_ref->dep_ref->id ?? '000';
    //             $departmentName = $item->user_ref->dep_ref->name ?? 'Other';

    //             // ตรวจสอบว่าแผนกมีอยู่ในโครงสร้างข้อมูลหรือไม่
    //             if (!isset($departments[$departmentId])) {
    //                 $departments[$departmentId] = [
    //                     'department_id' => $departmentId,
    //                     'department_name' => $departmentName,
    //                     'total_bw' => 0,
    //                     'total_color' => 0,
    //                     'total' => 0,
    //                     'users' => []
    //                 ];
    //             }

    //             // เพิ่มข้อมูลผู้ใช้งานในแผนก
    //             $userCode = $item->user_ref->code;
    //             $userName = $item->user_ref->name_eng;
    //             if (!isset($departments[$departmentId]['users'][$userCode])) {
    //                 $departments[$departmentId]['users'][$userCode] = [
    //                     'code' => $userCode,
    //                     'name' => $userName,
    //                     'total_bw' => 0,
    //                     'total_color' => 0,
    //                     'total' => 0
    //                 ];
    //             }

    //             // นับผลรวมการใช้งานของผู้ใช้งานในแผนก
    //             $departments[$departmentId]['users'][$userCode]['total_bw'] += $item->total_bw;
    //             $departments[$departmentId]['users'][$userCode]['total_color'] += $item->total_color;
    //             $departments[$departmentId]['users'][$userCode]['total'] += $item->total_bw + $item->total_color;
    //         }
    //     }

    //     // แปลงโครงสร้างข้อมูลใหม่เป็นรูปแบบที่ต้องการ
    //     $formattedData = [];
    //     foreach ($departments as $department) {

    //         $formattedUsers = array_values($department['users']);
    //         $formattedData[] = [
    //             'department_id' => $department['department_id'],
    //             'department_name' => $department['department_name'],
    //             'users' => $formattedUsers
    //         ];
    //     }

    //     return response()->json(['data' => $formattedData], 200);
    // }

    public function getData(Request $request)
    {
        $query = LogPrinter::with(['user_ref:code,name_th,name_eng,department_id', 'user_ref.dep_ref:id,name'])
            ->where('jobstatus', 'Done')
            ->whereIn('jobtype', ['Print', 'Copy']);

        if ($request->has('startDate') && $request->has('endDate')) {
            $startDate = date('Y-m-d', strtotime($request->startDate));
            $endDate = date('Y-m-d', strtotime($request->endDate));
            $query->whereBetween('date', [$startDate, $endDate]);
        }

        // if ($request->has('depId')) {
        //    // dd($request->has('depId'));
        //     $departments = $request->depId;
        //     //dd($departments);
        //     $query->whereIn('user_ref.department_id', is_array($departments) ? $departments : [$departments]);

        // }


        if ($request->has('code')) {
            // dd($request->has('depId'));
             $code = $request->code;
             //dd($code);
             $query->whereIn('code_user', is_array($code) ? $code : [$code]);

         }
        //  $sql = $query->toSql();
        $data = $query->get();

        // dd($data);



        // สร้างโครงสร้างข้อมูลเริ่มต้นเพื่อเก็บข้อมูลแผนกและผู้ใช้งานในแต่ละแผนก
        $departments = [];

        // วนลูปผลลัพธ์ที่ได้จากการ query เพื่อนำข้อมูลเข้าสู่โครงสร้างข้อมูล
        foreach ($data as $item) {
            if ($item->user_ref && $item->user_ref->dep_ref) {
                $departmentId = $item->user_ref->dep_ref->id ?? '999';
                $departmentName = $item->user_ref->dep_ref->name ?? 'Other';

                // ตรวจสอบว่าแผนกมีอยู่ในโครงสร้างข้อมูลหรือไม่
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

                // เพิ่มข้อมูลผู้ใช้งานในแผนก
                $userCode = $item->user_ref->code;
                $userName = $item->user_ref->name_eng;
                if (!isset($departments[$departmentId]['users'][$userCode])) {
                    $departments[$departmentId]['users'][$userCode] = [
                        'code' => $userCode,
                        'name' => $userName,
                        'total_bw' => 0,
                        'total_color' => 0,
                        'total' => 0
                    ];
                }

                // นับผลรวมการใช้งานของผู้ใช้งานในแผนก
                $departments[$departmentId]['total_bw'] += $item->total_bw;
                $departments[$departmentId]['total_color'] += $item->total_color;
                $departments[$departmentId]['total'] += $item->total_bw + $item->total_color;
                $departments[$departmentId]['users'][$userCode]['total_bw'] += $item->total_bw;
                $departments[$departmentId]['users'][$userCode]['total_color'] += $item->total_color;
                $departments[$departmentId]['users'][$userCode]['total'] += $item->total_bw + $item->total_color;
            }
        }

        // แปลงโครงสร้างข้อมูลใหม่เป็นรูปแบบที่ต้องการ
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
        $query = LogPrinter::with(['user_ref:code,name_th,name_eng,department_id', 'user_ref.dep_ref:id,name'])->where('jobstatus', 'Done')->wherein('jobtype', ['Print', 'Copy']);

        $data = $query->get();

        $departmentSums = $data->groupBy('user_ref.department_id')->map(function ($items, $departmentId) {
            $departmentName = $items->first()['user_ref']['dep_ref']['name'] ?? 'Other'; // ให้ค่าเป็น "-" ถ้า dep_ref มีค่าเป็น null
            return [
                'department_id' => $departmentId ?? '999',
                'department_name' => $departmentName,
            ];
        })->values();

        return response()->json(['data' => $departmentSums], 200);
    }

    public function getUser(Request $request)
    {
        $query = LogPrinter::with(['user_ref:code,name_th,name_eng,department_id', 'user_ref.dep_ref:id,name'])->where('jobstatus', 'Done')->wherein('jobtype', ['Print', 'Copy']);
        $data = $query->get();
        // $Users = $data->groupBy('user_ref.code')->map(function ($items, $code) {
        //     $UserName = $items->first()['user_ref']['name_eng'] ?? $items->first()['username']; // ให้ค่าเป็น "-" ถ้า user_ref มีค่าเป็น null
        //     return [
        //         'code' => $code,
        //         'user' => $UserName,
        //     ];
        $Users = $data->groupBy('code_user')->map(function ($items, $code) {
            $UserName = $items->first()['user_ref']['name_eng'] ?? $items->first()['username']; // ให้ค่าเป็น "-" ถ้า user_ref มีค่าเป็น null
            return [
                'code' => $code,
                'user' => $UserName,
            ];
        })->values();

        return response()->json(['data' => $Users], 200);
    }

    public function getBarChartbyDep(Request $request)
    {

        $query = LogPrinter::with(['user_ref:code,name_th,name_eng,department_id', 'user_ref.dep_ref:id,name'])
            ->where('jobstatus', 'Done')->wherein('jobtype', ['Print', 'Copy']);


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
        $data->each(function ($item) {
            $item->total = $item->total_color + $item->total_bw;
        });

        $departmentSums = $data->groupBy('user_ref.department_id')->map(function ($items, $departmentId) {
            $departmentName = $items->first()['user_ref']['dep_ref']['name'] ?? 'Other'; // ให้ค่าเป็น "-" ถ้า dep_ref มีค่าเป็น null
            return [
                'department_id' => $departmentId,
                'department_name' => $departmentName,
                'total_color' => $items->sum('total_color'),
                'total_bw' => $items->sum('total_bw'),
                'total' => $items->sum('total')
            ];
        })->values();

        return response()->json(['data' => $departmentSums], 200);
    }

    public function getPieChartbyPrinter(Request $request)
    {
        $query = LogPrinter::with(['user_ref:code,name_th,name_eng,department_id', 'user_ref.dep_ref:id,name'])
        ->where('jobstatus', 'Done')->whereIn('jobtype', ['Print', 'Copy']);

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
        $query = LogPrinter::with(['user_ref:code,name_th,name_eng'])
        ->where('jobstatus', 'Done')->wherein('jobtype', ['Print', 'Copy']);

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

        $data->each(function ($item) {
            $item->total = $item->total_color + $item->total_bw;
        });

        $UserSums = $data->groupBy('user_ref.code')->map(function ($items, $code) {
            $UserName = $items->first()['user_ref']['name_eng'] ?? $items->first()['username']; // ให้ค่าเป็น "-" ถ้า user_ref มีค่าเป็น null
            return [
                'code' => $code,
                'user' => $UserName,
                'total_color' => $items->sum('total_color'),
                'total_bw' => $items->sum('total_bw'),
                'total' => $items->sum('total')
            ];
        })->sortByDesc('total')->take(30)->values();

        return response()->json(['data' => $UserSums], 200);
    }

    public function getDataPrinter(Request $request)
    {
        $query = LogPrinter::with(['user_ref:code,name_th,name_eng,department_id', 'user_ref.dep_ref:id,name'])->where('jobstatus', 'Done')->whereIn('jobtype', ['Print', 'Copy']);

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

        $data = $query->select(
            'printername',
            DB::raw('SUM(total_color) as total_color'),
            DB::raw('SUM(total_bw) as total_bw'),
            DB::raw('SUM(total_color+total_bw) as total')
        )->groupBy('printername')->get();

        return response()->json(['data' => $data], 200);
    }

    public function searchData(Request $request)
    {

    }

}
