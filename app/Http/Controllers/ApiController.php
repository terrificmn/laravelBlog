<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Api;
use Psy\Readline\Hoa\IStream;

use function PHPUnit\Framework\isEmpty;

class ApiController extends Controller {
    //
    public function index(Request $request) {
        return view('api.index');
    }


    public function getToken(Request $request) {
        $pwd = $request->pwd_to_check;
        if($pwd == "") {
            return response()->json( ['res_type' => 'get_token', 'msg' => 'faliure']);
        }

        $result = Api::where('git_pwd', '=', $pwd)->get('git_token');
        if(count($result) > 0) {
            // dd($result[0]->git_token); // password confirmed
            return response()->json( ['res_type' => 'get_token', 'msg' => $result[0]->git_token ]);
        } else {
            // dd('wrong');
            return response()->json( ['res_type' => 'get_token', 'msg' => 'wrong password']);
        }
    }


    public function setToken(Request $request) {
        // validate ajax 때문에 결과를 받아야 하는데 아직 잘 모르겟음 그냥 심플하게 "" 로 체크, null과 empty로는 상태 확인이 잘 안됨
        // $result = $request->validate([
        //     'git_token' => 'required',
        //     'git_pwd' => 'required',
        //     'git_pwd_check' => 'required',
        // ]);
        $token = $request->git_token;
        $pwd = $request->git_pwd;
        $pwd_check = $request->git_pwd_check;
        $result = 'failure';

        /// radio check
        $radio_selected = $request->git_radio;
        $is_full_update_only = false;
        $is_pwd_update_only = false;
        $is_token_update_only = false;

        if($radio_selected == "r_full") { //"r_full" default
            if($token == "" || $pwd == "" || $pwd_check == "") { 
                $result = "missing one of inputs";
                return response()->json( ['res_type' =>'set_token', 'msg' => $result]);
            } else if($pwd != $pwd_check) { 
                $result = "password difference";
                return response()->json( ['res_type' =>'set_token', 'msg' => $result]);
            }
            $is_full_update_only = true;
        
        } else if($radio_selected == "r_pwd") {
            if($pwd != $pwd_check) { 
                $result = "password difference";
                return response()->json( ['res_type' =>'set_token', 'msg' => $result]);
            } else if($pwd == "" || $pwd_check == "") {
                $result = "password empty";
                return response()->json( ['res_type' =>'set_token', 'msg' => $result]);
            }
            $is_pwd_update_only = true;

        } else if($radio_selected == "r_token") {
            if($token == "" ) {
                $result = "token input needs";
                return response()->json( ['res_type' =>'set_token', 'msg' => $result]);
            }
            $is_token_update_only = true;
        }

        ///TODO: user_id 를 1로 고정, foreign key로 참고할 아이디가 자기자신 밖에 없음. (현재 Aug15. 2024)
        $result = Api::where('user_id', '=', 1)->get();
        $msg = 'success';
        
        if(count($result) == 0 && $is_full_update_only == false) {
            return response()->json( ['res_type' =>'set_token', 'msg' => 'choose Full button, all inputs are needed for the first time.']);
        }
        /// create mode // no row from DB found
        if(count($result) == 0) {
            Api::create([
                'git_token' => $token,
                'git_pwd' => $pwd,
                'user_id' => auth()->user()->id
            ]);
            return response()->json( ['res_type' =>'set_token', 'msg' => $msg]);
        }

        /// update mode 
        if($is_pwd_update_only) {
            Api::where('user_id', '=', 1)->update( [ 
                'git_pwd' => $request->input('git_pwd')
            ]);
        } else if($is_token_update_only) {
            Api::where('user_id', '=', 1)->update( [ 
                'git_token' => $request->input('git_token'),
            ]);
        } else if($is_full_update_only) {
            Api::where('user_id', '=', 1)->update( [ 
                'git_pwd' => $request->input('git_pwd'),
                'git_token' => $request->input('git_token'),
            ]);
        } else {
            return response()->json( ['res_type' =>'set_token', 'msg' => 'db failure']);
        }

        return response()->json( ['res_type' =>'set_token', 'msg' => $msg]);
    }


} // the end of class
