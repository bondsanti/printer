<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Session;
use RealRashid\SweetAlert\Facades\Alert;
use App\Models\Log;
use App\Models\Role_user;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\Translation\Provider\Dsn;

class UserController extends Controller
{



    public function getRole()
    {
        $isRole = Role_user::where('user_id', Session::get('loginId'))->first();
        return response()->json(['data' => $isRole],200);
    }

    public function getData()
    {
        // $User = Role_user::with('user_ref:id,code,name_eng,position_id', 'user_ref.position_ref:id,name')->get();
        $User = Role_user::with('user_ref:id,code,email')->get();
        return response()->json(['data' => $User],200);
    }

    public function storeData(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'code' => 'required',
            'role_type' => 'required|in:SuperAdmin,Admin,User', // ตรวจสอบว่า role_type เป็นค่าที่ถูกต้อง
        ], [
            'code.required' => 'กรุณากรอกรหัสพนักงาน',
            'role_type.required' => 'กรุณาเลือกสิทธิ์การใช้งาน',
            'role_type.in' => 'สิทธิ์การใช้งานไม่ถูกต้อง',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $user = User::where('code', $request->code)->where('active', 1)->first();

        if (!$user) {
            return response()->json(['message' => 'ไม่พบผู้ใช้งาน'], 404);
        }

        $existingRole = Role_user::where('user_id', $user->id)->first();

        if ($existingRole) {
            return response()->json(['message' => 'มีผู้ใช้ท่านนี้อยู่ในระบบแล้ว'], 404);
        }

        $roleUser = new Role_user();
        $roleUser->user_id = $user->id;
        $roleUser->role_type = $request->role_type;
        $roleUser->active = 1;
        $roleUser->save();

        Log::addLog(Session::get('loginId'), 'Create User', $roleUser);

        return response()->json(['message' => 'เพิ่มผู้ใช้งานเรียบร้อยแล้ว'], 201);
    }


    public function updateData(Request $request,$id)
    {
        $validator = Validator::make($request->all(), [
            'codee' => 'required',
            'role_typee' => 'required|in:SuperAdmin,Admin,User', // ตรวจสอบว่า role_type เป็นค่าที่ถูกต้อง
        ], [
            'codee.required' => 'กรุณากรอกรหัสพนักงาน',
            'role_typee.required' => 'กรุณาเลือกสิทธิ์การใช้งาน',
            'role_typee.in' => 'สิทธิ์การใช้งานไม่ถูกต้อง',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        // พบข้อมูลที่ต้องการอัปเดตโดยใช้ ID
        $roleUser = Role_user::find($id);

        if (!$roleUser) {
            return response()->json(['message' => 'ไม่พบข้อมูลที่ต้องการอัปเดต'], 404);
        }

        $user = User::where('code', $request->codee)->where('active', 1)->first();

        if (!$user) {
            return response()->json(['message' => 'ไม่พบผู้ใช้งาน'], 404);
        }

        // $existingRole = Role_user::where('user_id', $user->id)->where('role_type',$request->role_typee)->first();

        // if ($existingRole) {

        //     return response()->json(['message' => 'มีผู้ใช้ท่านนี้อยู่ในระบบแล้ว'], 404);
        // }

        // อัปเดตข้อมูล
        $roleUser->user_id = $user->id;
        $roleUser->role_type = $request->role_typee;
        $roleUser->active = $request->active;
        $roleUser->save();

        Log::addLog(Session::get('loginId'), 'Update User', $roleUser);

        return response()->json(['message' => 'อัปเดตข้อมูลเรียบร้อยแล้ว'], 200);
    }


    public function deleteData($id)
    {
        $roleUser = Role_user::findOrFail($id); // หา RoleUser จาก ID ที่ระบุ
        // $roleUser_old = $roleUser->toArray();

        Log::addLog(Session::get('loginId'), 'Delete RoleUser',$roleUser);
        $roleUser->delete($id);

        return response()->json([
            'message' => 'ลบข้อมูลสำเร็จ'
        ], 200);

    }

}
