<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;




class UserController extends Controller
{
    //



    public function justtest(Request $request){
        return controller::getrandlist(6,10,5);
    }
    public function index(){
        return time();
    }
    public function insertUser($id,$name,$email)
    {
            $result=DB::table("users")->insertGetId(['name'=>$name,'id'=>$id,'email'=>$email]);
            return $result;
    }

    public function updateUser($oldId,$id,$name,$email)
    {
            $result=DB::table("users")->where('id',$oldId)->update(['id'=>$id,"name"=>$name,'email'=>$email]);
            return $result;
    }
    public function searchUser($id)
    {
            $result=DB::table("users")->where("id",$id)->get();
            var_dump($result);
    }
    public function searchUser1($id)
    {
        if(!empty($_POST['dl']))
{
    echo "<script> location.href='testa.php';</script>"; //跳转至测试页面
}
    }

    public function deleteUser($id)
    {
            $bool=DB::table("users")->where("id",$id)->delete();
            return $bool;
    }//以上为练习内容




    public function login(Request $request){

        $username=$request->get('username');
        $password=$request->get('password');
        $users = DB::table("user")->where("username",$username)->get();
        if(!$users->isEmpty()){
            foreach ($users as $v){

            }
                if($v->password==$password){
                    $request->session()->put("username",$v->username);
                    $request->session()->put("faculty",$v->faculty);
                    $request->session()->put("profession",$v->profession);
                    $request->session()->put("studentnumber",$v->studentnumber);
                    $request->session()->put("power",$v->power);
                    //$request->session()->put("loginresult",1);
                    $result=array(
                        'code'=>1,
                        'message'=>"登录成功",
                        'username'=>$request->session()->get("username"),
                        'power'=>$request->session()->get("power"),
                        'studentnumber'=>$request->session()->get("studentnumber"),
                        'faculty'=>$request->session()->get("faculty"),
                        'profession'=>$request->session()->get("profession")
                    );
                    return json_encode($result,JSON_UNESCAPED_UNICODE);
                }

            else{
                $result['code']=0;
                $result['message']='密码错误';
                return json_encode($result,JSON_UNESCAPED_UNICODE);
            }
        }

        else{
            $result['code']=0;
            $result['message']='用户名不存在';
            return json_encode($result,JSON_UNESCAPED_UNICODE);
        }

    }

    public function register(Request $request){
        $username=$request->get('username');
        $password=$request->get('password');
        $faculty=$request->get('faculty');
        $profession=$request->get('profession');
        $studentnumber=$request->get('studentnumber');
        $userquery = DB::table("user")->where("username",$username)->get();
        if(!$userquery->isEmpty()){
            $result['code']=0;
            $result['message']='用户名已被注册';
            return json_encode($result,JSON_UNESCAPED_UNICODE);
            }
        $userquery = DB::table("user")->where("studentnumber",$studentnumber)->get();
        if(!$userquery->isEmpty()){
            $result['code']=0;
            $result['message']='学号已被注册';
            return json_encode($result,JSON_UNESCAPED_UNICODE);
            }
            $registerquery =DB::table("user")->insert([
                'id'=>1,
                'username'=>$username,
                'password'=>$password,
                'studentnumber'=>$studentnumber,
                'power'=>1,
                'faculty'=>$faculty,
                'profession'=>$profession
                ]);

            if($registerquery){
                $result['code']=1;
                $result['message']='注册成功';
                return json_encode($result,JSON_UNESCAPED_UNICODE);
            }

            else{
                $result['code']=0;
                $result['message']='注册操作失败';
                return json_encode($result,JSON_UNESCAPED_UNICODE);
            }


    }




    public function changepower(Request $request){
        $username=$request->get("username");
        $newpower=$request->get("newpower");
        $userquery= DB::table("user")->where('username',$username)->get();
        $judgeuserexist=$userquery->count();
        if(!$judgeuserexist){
            $result['code']=0;
            $result['message']='用户名不存在';
            return json_encode($result,JSON_UNESCAPED_UNICODE);
        }

        $changeuserpower=DB::table("user")->where('username',$username)->update(['power'=>$newpower]);
        if(!$changeuserpower){
            $result['code']=0;
            $result['message']='修改操作执行失败';
            return json_encode($result,JSON_UNESCAPED_UNICODE);
        }

        $result['code']=1;
        $result['message']='权限修改成功';
        return json_encode($result,JSON_UNESCAPED_UNICODE);
    }

public function addfavourite(Request $request){
    $code=0;
    $studentnumber=$request->get('studentnumber');
    $paperid=$request->get('paperid');
    $query= DB::table("favourites")
    ->where('studentnumber',$studentnumber)
    ->where('paperid',$paperid)
    ->get();

    if($query->count()){
        $result['code']=0;
        $result['message']='您已收藏此问卷';
        return json_encode($result,JSON_UNESCAPED_UNICODE);
    }
    else{
        $insert=DB::table("favourites")->insert([
            'studentnumber'=>$studentnumber,
            'paperid'=>$paperid
        ]);
        if($insert){
            $result['code']=1;
            $result['message']='收藏成功';
            return json_encode($result,JSON_UNESCAPED_UNICODE);
        }
        else{
            $result['code']=0;
            $result['message']='数据库收藏操作执行失败';
            return json_encode($result,JSON_UNESCAPED_UNICODE);
        }

    }
}


public function deletefavourite(Request $request){
    $code=0;
    $studentnumber=$request->get('studentnumber');
    $paperid=$request->get('paperid');
    $query= DB::table("favourites")
    ->where('studentnumber',$studentnumber)
    ->where('paperid',$paperid)
    ->get();

    if($query->count()){
        $delete=DB::table("favourites")
        ->where('studentnumber',$studentnumber)
        ->where('paperid',$paperid)
        ->delete();
        if($delete){
            $result['code']=1;
            $result['message']='删除成功';
            return json_encode($result,JSON_UNESCAPED_UNICODE);
        }
        $result['code']=0;
        $result['message']='数据库删除操作执行失败';
        return json_encode($result,JSON_UNESCAPED_UNICODE);
    }
    else{
        $result['code']=0;
        $result['message']='您未收藏此问卷';
        return json_encode($result,JSON_UNESCAPED_UNICODE);

        }

    }




}
