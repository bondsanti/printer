<?php

namespace App\Http\Controllers;

use App\Models\Log;
use App\Models\Role_user;
use App\Models\User;
use Illuminate\Http\Request;
use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Support\Facades\Hash;
use DB;
use Session;

class CustomAuthController extends Controller
{
    public function login()
    {
        //return view('login');
        return redirect('https://vbnext.vbeyond.co.th/main');
        //return redirect('http://127.0.0.1:8000/main');
    }

    public function loginUser(Request $request)
    {

        $request->validate([
            'code' => 'required',
            'password' => 'required'
        ], [
            'code.required' => 'ป้อนรหัสพนักงาน',
            'password.required' => 'ป้อนรหัสผ่าน'
        ]);


        $user_hr = User::where('code', $request->code)->first();
// dd($user_hr);

        if (!$user_hr) {
            Alert::error('ไม่พบผู้ใช้งาน', 'กรุณากรอกข้อมูลใหม่อีกครั้ง');
            return back();
        } else {


            if ($user_hr->active != 0) {

                $role_user = Role_user::where('user_id', $user_hr->user_id)->orwhere('active',1)->first();

                if (!$role_user) {

                    Alert::warning('คุณไม่มีสิทธิ์เข้าระบบ', 'กรุณาติดต่อ Admin!!');
                    return back();

                } else {

                    if (Hash::check($request->password, $user_hr->password)) {

                        $request->session()->put('loginId', $user_hr->user_id);

                        DB::table('vbeyond_report.log_login')->insert([
                            'username' => $user_hr->code,
                            'dates' => date('Y-m-d'),
                            'timeStm' => date('Y-m-d H:i:s'),
                            'page' => 'ReportPrinter'
                        ]);

                        Log::addLog($request->session()->get('loginId'), 'Login', 'Login');


                            Alert::success('เข้าสู่ระบบสำเร็จ');
                            return redirect('/');




                    } else {

                        Alert::warning('รหัสผ่านไม่ถูกต้อง', 'กรุณากรอกข้อมูลใหม่อีกครั้ง');
                        return back();
                    }


                    Alert::warning('รหัสผ่านไม่ถูกต้อง', 'กรุณากรอกข้อมูลใหม่อีกครั้ง');
                    return back();
                }

            } else {
                Alert::error('ไม่พบผู้ใช้งาน', 'กรุณากรอกข้อมูลใหม่อีกครั้ง');
                return back();
            }
        }
    }

    public function logoutUser(Request $request)
    {

        if ($request->session()->has('loginId')) {

            Log::addLog($request->session()->get('loginId'), 'Logout', 'Logout');

            $request->session()->pull('loginId');
            Alert::success('ออกจากระบบเรียบร้อย', 'ไว้พบกันใหม่ :)');

            return redirect('/');
        }
    }

    public function AllowLoginConnect(Request $request,$id,$token)
    {

        $user = User::where('user_id', '=', $id)->first();
        //dd($user);
        if($user){
            $request->session()->put('loginId',$user->user_id);
            // Auth::login($user);
            // $user->last_login_at = date('Y-m-d H:i:s');
            $user->save();
            $checkToken = User::where('token', '=', $token)->first();

            if ($checkToken) {
                DB::table('vbeyond_report.log_login')->insert([
                    'username' => $user->code,
                    'dates' => date('Y-m-d'),
                    'timeStm' => date('Y-m-d H:i:s'),
                    'page' => 'ReportPrinter'
                ]);

                Log::addLog($request->session()->get('loginId'), '', 'Login AllowLoginConnect By vBisConnect');
                return redirect('/');
            }else{
                $request->session()->pull('loginId');
                return redirect('/');
            }


            }else if($user->active==0){
                $request->session()->pull('loginId');
                return redirect('/');
            }else{
                return redirect('/');
            }

    }



}
