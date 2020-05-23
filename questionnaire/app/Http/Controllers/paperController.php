<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
class paperController extends Controller
{
    //

    public function papercreatedlist(Request $request){
        $username=$request->session()->get('username');
        $faculty=$request->session()->get("faculty");
        $profession=$request->session()->get("profession");
        if(!$faculty||!$profession){
            $result['code']=0;
            $result['message']="您的登录信息已失效，请重新登录";
            return json_encode($result,JSON_UNESCAPED_UNICODE);
        }
        $paperquery2= DB::table("paper")
        ->whereIn('faculty', ['-1',$faculty])
        ->whereIn('profession', ['-1',$profession])
        ->where('creator',$username)
        ->get();
        $paperquery1=json_encode($paperquery2);
        $paperquery=json_decode($paperquery1, true);

        if(!$paperquery2->isEmpty()){
            $count=0;

            foreach ($paperquery as $v){
                $count=$count+1;
                $data1=$count;
                $paperarray[$data1]=$v;
            }
        }
        else{
            $result['code']=0;
            $result['message']="查找不到您创建的问卷";
            return json_encode($result,JSON_UNESCAPED_UNICODE);
        }

        $result['code']=1;
        $result['paperinfo']=$paperarray;
        return json_encode($result,JSON_UNESCAPED_UNICODE);
    }



    public function createpaper(Request $request){
        //$paperinfo=$request->get("paperinfo");
        //$questioninfo=$request->get("questioninfo");
        //$optioninfo=$request->get("optioninfo");
        //$lasttime=$request->get($paperinfo->lasttime);
        $paperinfo = DB::table("paper")->get();
        $paperinfo1=json_encode($paperinfo,JSON_UNESCAPED_UNICODE);
        $paperinfo=json_decode($paperinfo1,true);

        $questionquery = DB::table("paper")->where('papername',$paperinfo[0]['papername'])->get();
        $resultcount=$questionquery->count();
        if($resultcount){
            $result['code']=0;
            $result['message']='问卷名已存在';
            return json_encode($result,JSON_UNESCAPED_UNICODE);
        }

        $questioninfo = DB::table("question")->get();
        $questioninfo1=json_encode($questioninfo,JSON_UNESCAPED_UNICODE);
        $questioninfo=json_decode($questioninfo1,true);

        $optioninfo = DB::table("options")->get();
        $optioninfo1=json_encode($optioninfo,JSON_UNESCAPED_UNICODE);
        $optioninfo=json_decode($optioninfo1,true);

        $endtime=Carbon::now()->addDays(20);
        echo $endtime;
        foreach ($paperinfo as $u){
            $paperquery =DB::table("paper")->insert([
                'paperid'=>$u['paperid'],
                'papername'=>$u['papername'],
                'faculty'=>$u['faculty'],
                'profession'=>$u['profession'],
                'creator'=>$u['creator'],
                'power'=>$u['power'],
                'papertype'=>$u['papertype'],
                'paperexplain'=>$u['paperexplain'],
                'questionnumber'=>10,
                'endtime'=>$endtime
                ]);
            if(!$paperquery) {
                $result['code']=0;
                $result['message']='问卷信息插入失败';
                return json_encode($result,JSON_UNESCAPED_UNICODE);
            }
            }
        $order=1;
        foreach($questioninfo as $v){
            $questionquery =DB::table("question")->insert([
                'questionid'=>$order,
                'paperid'=>$u['paperid'],
                'stem'=>$v['stem'],
                'questiontype'=>$v['questiontype'],
                'correctanswer'=>$v['correctanswer']
            ]);
            if(!$questionquery) {
                $result['code']=0;
                $result['message']='题目信息插入失败';
                return json_encode($result,JSON_UNESCAPED_UNICODE);
            }
            $order=$order+1;
        }
        foreach($optioninfo as $w){//查找选项时要同时查找题号和问卷号
            $optionquery =DB::table("options")->insert([
                'optionname'=>$w['optionname'],
                'questionid'=>$w['questionid'],
                'paperid'=>$u['paperid'],
                'content'=>$w['content']
            ]);
            if(!$optionquery) {
                $result['code']=0;
                $result['message']='选项信息插入失败';
                return json_encode($result,JSON_UNESCAPED_UNICODE);
            }
        }
        $result['code']=1;
        $result['message']='问卷创建成功';
        return json_encode($result,JSON_UNESCAPED_UNICODE);

    }

    public function updatepaper(Request $request){
        //$paperinfo=$request->get("paperinfo");
        //$questioninfo=$request->get("questioninfo");
        //$optioninfo=$request->get("optioninfo");
        //$lasttime=$request->get($paperinfo->lasttime);

        $paperinfo = DB::table("paper")->get();
        $paperinfo1=json_encode($paperinfo,JSON_UNESCAPED_UNICODE);
        $paperinfo=json_decode($paperinfo1,true);

        $questionquery = DB::table("paper")->where('papername',$paperinfo[0]['papername'])->get();
        $resultcount=$questionquery->count();
        if(!$resultcount){
            $result['code']=0;
            $result['message']='问卷名不存在';
            return $result;
        }

        $questioninfo = DB::table("question")->get();
        $questioninfo1=json_encode($questioninfo,JSON_UNESCAPED_UNICODE);
        $questioninfo=json_decode($questioninfo1,true);

        $optioninfo = DB::table("options")->get();
        $optioninfo1=json_encode($optioninfo,JSON_UNESCAPED_UNICODE);
        $optioninfo=json_decode($optioninfo1,true);

        $endtime=Carbon::now()->addDays(20);
        echo $endtime;
        foreach ($paperinfo as $u){
            $paperquery =DB::table("paper")->insert([
                'paperid'=>$u['paperid'],
                'papername'=>$u['papername'],
                'faculty'=>$u['faculty'],
                'profession'=>$u['profession'],
                'creator'=>$u['creator'],
                'power'=>$u['power'],
                'papertype'=>$u['papertype'],
                'paperexplain'=>$u['paperexplain'],
                'questionnumber'=>10,
                'endtime'=>$endtime
                ]);
            if(!$paperquery) {
                $result['code']=0;
                $result['message']='问卷信息修改失败';
                return json_encode($result,JSON_UNESCAPED_UNICODE);
            }
            }
        $order=1;
        foreach($questioninfo as $v){
            $questionquery =DB::table("question")->insert([
                'questionid'=>$order,
                'paperid'=>$u['paperid'],
                'stem'=>$v['stem'],
                'questiontype'=>$v['questiontype'],
                'correctanswer'=>$v['correctanswer']
            ]);
            if(!$questionquery) {
                $result['code']=0;
                $result['message']='题目信息修改失败';
                return json_encode($result,JSON_UNESCAPED_UNICODE);
            }
            $order=$order+1;
        }
        foreach($optioninfo as $w){//查找选项时要同时查找题号和问卷号
            $optionquery =DB::table("options")->insert([
                'optionname'=>$w['optionname'],
                'questionid'=>$w['questionid'],
                'paperid'=>$u['paperid'],
                'content'=>$w['content']
            ]);
            if(!$optionquery) {
                $result['code']=0;
                $result['message']='选项信息修改失败';
                return json_encode($result,JSON_UNESCAPED_UNICODE);
            }
        }
        $result['code']=1;
        $result['message']='问卷修改成功';
        return json_encode($result,JSON_UNESCAPED_UNICODE);

    }
    public function deletepaper(Request $request){//删除问卷
        $papername=$request->get("papername");
        $paperquery = DB::table("paper")->where('papername',$papername)->get();
        echo $resultcount=$paperquery->count();
        if(!$resultcount){
            $result['code']=0;
            $result['message']='问卷名不存在';
            return json_encode($result,JSON_UNESCAPED_UNICODE);
        }
        $deleteresult = DB::table("paper")->where('papername',$papername)->delete();
        if(!$deleteresult){
            $result['code']=0;
            $result['message']='删除操作执行失败';
            return json_encode($result,JSON_UNESCAPED_UNICODE);
        }
        else{
            $result['code']=1;
            $result['message']='问卷删除成功';
            return json_encode($result,JSON_UNESCAPED_UNICODE);
        }
    }





    public function getpaper(Request $request){//从数据库中读取数据并生成一份问卷
        $papername=$request->get("papername");//获取问卷名
        $paperquery = DB::table("paper")->where('papername',$papername)->first();//获取问卷信息
        $paperset = DB::table("paper")->where('papername',$papername)->first();//获取问卷设置

        if(!$paperquery||!$paperset){
            $result['code']=0;
            $result['message']='问卷名不存在';
            return json_encode($result,JSON_UNESCAPED_UNICODE);
        }//验证问卷是否存在

        $questionquery= DB::table("question")->where('paperid',$paperquery->paperid)->get();//获取题库

        //导出设置信息
        $num[0]=$paperset->scnum;
        $num[1]=$paperset->mcnum+$num[0];
        $num[2]=$paperset->sfnum+$num[1];
        $num[3]=$paperset->mfnum+$num[2];
        $num[4]=$paperset->sanum+$num[3];
        //num[0-4]分别为单选、多选、单项填空、多项填空的起始题号

        if($paperset->quesrand==1){//若随机生成题目
            $but[0]=!!$paperset->randsc;
            $but[1]=!!$paperset->randmc;
            $but[2]=!!$paperset->randsf;
            $but[3]=!!$paperset->randmf;
            $but[4]=!!$paperset->randsa;//button[0-4]用于判断是否要生成某个题型的题目
            if($but[0]){
                $rand0=Controller::getrandlist(1,$num[0],$paperset->randsc);
                $scquery=DB::table('question')->where('papername',$papername)->whereIn('questionid',$rand0)->get();

                $scquery1=json_encode($scquery,JSON_UNESCAPED_UNICODE);
                $result['scinfo']=json_decode($scquery1,true);
            }

            if($but[1]){
                $rand1=Controller::getrandlist($num[0]+1,$num[1],$paperset->randmc);
                $mcquery=DB::table('question')->where('papername',$papername)->whereIn('questionid',$rand1)->get();
                $mcquery1=json_encode($mcquery,JSON_UNESCAPED_UNICODE);
                $result['mcinfo']=json_decode($mcquery1,true);
            }

            if($but[2]){
                $rand2=Controller::getrandlist($num[1]+1,$num[2],$paperset->randsf);
                $sfquery=DB::table('question')->where('papername',$papername)->whereIn('questionid',$rand2)->get();
                $sfquery1=json_encode($sfquery,JSON_UNESCAPED_UNICODE);
                $result['sfinfo']=json_decode($sfquery1,true);
            }

            if($but[3]){
                $rand3=Controller::getrandlist($num[2]+1,$num[3],$paperset->randmf);
                $mfquery=DB::table('question')->where('papername',$papername)->whereIn('questionid',$rand3)->get();
                $mfquery1=json_encode($mfquery,JSON_UNESCAPED_UNICODE);
                $result['mfinfo']=json_decode($mfquery1,true);
            }

            if($but[4]){
                $rand4=Controller::getrandlist($num[3]+1,$num[4],$paperset->randsa);
                $saquery=DB::table('question')->where('papername',$papername)->whereIn('questionid',$rand4)->get();
                $saquery1=json_encode($saquery,JSON_UNESCAPED_UNICODE);
                $result['sainfo']=json_decode($saquery1,true);
            }
            //生成查询索引，并执行查询操作

        }
        else{//若不随机生成题目，则读取数据库中所有题目
            $but[0]=!!$paperset->scnum;
            $but[1]=!!$paperset->mcnum;
            $but[2]=!!$paperset->sfnum;
            $but[3]=!!$paperset->mfnum;
            $but[4]=!!$paperset->sanum;//button[0-4]用于判断是否要生成某个题型的题目
        if($but[0]){
            $scquery=DB::table('question')->where('papername',$papername)->where('questiontype',1)->get();

            $scquery1=json_encode($scquery,JSON_UNESCAPED_UNICODE);
            $scarray=json_decode($scquery1,true);

            if($paperset->opdisorder){//读取选项信息并打乱
                for($i=0;$i<$scquery->count();$i++){
                    $result['scinfo'][$i]['questioninfo']=$scarray[$i];

                    $optionsquery= DB::table("options")
                    ->where('paperid',$paperquery->paperid)
                    ->where('questionid',$scarray[$i]['questionid'])
                    ->get();
                    $optionsquery1=json_encode($optionsquery,JSON_UNESCAPED_UNICODE);
                    $optionsarray=json_decode($optionsquery1, true);
                    $opnum=$optionsquery->count();
                    $randarr=array();
                    $randarr=Controller::getrandlist(1,$opnum,$opnum);//获取随机数组

                    $str='A';
                    //echo json_encode($randarr);//检查点
                    for($j=0;$j<$opnum;$j++){//打乱选项
                        $result['scinfo'][$i]['optionsinfo'][$j]=$optionsarray[$randarr[$j]];
                        $result['scinfo'][$i]['optionsinfo'][$j]['optionname']=$str;
                        $str++;
                    }
                }
            }
            else{
                    for($i=0;$i<$scquery->count();$i++){
                        $result['scinfo'][$i]['questioninfo']=$scarray[$i];

                        $optionsquery= DB::table("options")
                        ->where('paperid',$paperquery->paperid)
                        ->where('questionid',$scarray[$i]['questionid'])
                        ->get();
                        $optionsquery1=json_encode($optionsquery,JSON_UNESCAPED_UNICODE);
                        $optionsarray=json_decode($optionsquery1, true);
                        $opnum=$optionsquery->count();
                        $result['scinfo'][$i]['optionsinfo']=$optionsarray;//输出选项信息
                        /*for($j=0;$j<$opnum;$j++){
                            $result['scinfo'][$i]['optionsinfo'][$j]=$optionsarray[$randarr[$j]];
                            $result['scinfo'][$i]['optionsinfo'][$j]['optionname']=$str;
                            $str++;
                        }*/
                    }
                }

            }

            if($but[1]){
                $mcquery=DB::table('question')->where('papername',$papername)->where('questiontype',2)->get();

                $mcquery1=json_encode($mcquery,JSON_UNESCAPED_UNICODE);
                $mcarray=json_decode($mcquery1,true);

                if($paperset->opdisorder){//读取选项信息并打乱
                    for($i=0;$i<$mcquery->count();$i++){
                        $result['mcinfo'][$i]['questioninfo']=$mcarray[$i];

                        $optionsquery= DB::table("options")
                        ->where('paperid',$paperquery->paperid)
                        ->where('questionid',$mcarray[$i]['questionid'])
                        ->get();
                        $optionsquery1=json_encode($optionsquery,JSON_UNESCAPED_UNICODE);
                        $optionsarray=json_decode($optionsquery1, true);
                        $opnum=$optionsquery->count();
                        $randarr=array();
                        $randarr=Controller::getrandlist(1,$opnum,$opnum);//获取随机数组

                        $str='A';
                        //echo json_encode($randarr);//检查点
                        for($j=0;$j<$opnum;$j++){//打乱选项
                            $result['mcinfo'][$i]['optionsinfo'][$j]=$optionsarray[$randarr[$j]];
                            $result['mcinfo'][$i]['optionsinfo'][$j]['optionname']=$str;
                            $str++;
                        }
                    }
                }
                else{
                        for($i=0;$i<$mcquery->count();$i++){
                            $result['mcinfo'][$i]['questioninfo']=$mcarray[$i];

                            $optionsquery= DB::table("options")
                            ->where('paperid',$paperquery->paperid)
                            ->where('questionid',$scarray[$i]['questionid'])
                            ->get();
                            $optionsquery1=json_encode($optionsquery,JSON_UNESCAPED_UNICODE);
                            $optionsarray=json_decode($optionsquery1, true);
                            $opnum=$optionsquery->count();
                            $result['mcinfo'][$i]['optionsinfo']=$optionsarray;//输出选项信息
                            /*for($j=0;$j<$opnum;$j++){
                                $result['scinfo'][$i]['optionsinfo'][$j]=$optionsarray[$randarr[$j]];
                                $result['scinfo'][$i]['optionsinfo'][$j]['optionname']=$str;
                                $str++;
                            }*/
                        }
                    }
            }

            if($but[2]){
                $sfquery=DB::table('question')->where('papername',$papername)->where('questiontype',3)->get();

                $sfquery1=json_encode($sfquery,JSON_UNESCAPED_UNICODE);
                $sfarray=json_decode($sfquery1,true);
                $result['sfinfo']=json_decode($sfquery1,true);
            }

            if($but[3]){
                $mfquery=DB::table('question')->where('papername',$papername)->where('questiontype',4)->get();

                $mfquery1=json_encode($mfquery,JSON_UNESCAPED_UNICODE);
                $mfarray=json_decode($mfquery1,true);
                $result['mfinfo']=json_decode($mfquery1,true);
            }

            if($but[4]){
                $saquery=DB::table('question')->where('papername',$papername)->where('questiontype',5)->get();
                $saquery1=json_encode($saquery,JSON_UNESCAPED_UNICODE);
                $saarray=json_decode($mfquery1,true);
                $result['sainfo']=json_decode($saquery1,true);
            }
            //生成查询索引，并执行查询操作
        }

        if($scquery->empty()||$mcquery->empty()||$sfquery->empty()||$mfquery->empty()||$saquery->empty()){
            $result1['code']=0;
            $result1['message']='题目获取失败';
            return json_encode($result1,JSON_UNESCAPED_UNICODE);//检查题目检索结果
        }

        $paperquery1=json_encode($paperquery,JSON_UNESCAPED_UNICODE);

        $result['paperinfo']=json_decode($paperquery1,true);
        $result['paperinfo']['lasttime']=$paperset->lasttime;//输出问卷信息

        $cacheid=DB::table('cacheinfo')->insertGetId([
            'userid'=>$request->session()->get("studentnumber"),
            'paperid'=>$paperquery->paperid
        ]);

        if(!$cacheid){
            $result1['code']=0;
            $result1['message']='缓存生成失败';
            return json_encode($result1,JSON_UNESCAPED_UNICODE);//检查缓存id信息
        }

        $count=1;
        if($but[0]){
            for($i=0;$i<$scquery->count();$i++){
                DB::table('papercache')->insert([
                    'cacheid'=>$cacheid,
                    'questionorder'=>$count,
                    'questionid'=>$scarray[$i]['questionid'],
                    'correctanswer'=>$scarray[$i]['correctanswer']
                ]);

                $count++;
            }
        }

        if($but[1]){
            for($i=0;$i<$mcquery->count();$i++){
                DB::table('papercache')->insert([
                    'cacheid'=>$cacheid,
                    'questionorder'=>$count,
                    'questionid'=>$mcarray[$i]['questionid'],
                    'correctanswer'=>$mcarray[$i]['correctanswer']
                ]);

                $count++;
            }
        }

        if($but[2]){
            for($i=0;$i<$sfquery->count();$i++){
                DB::table('papercache')->insert([
                    'cacheid'=>$cacheid,
                    'questionorder'=>$count,
                    'questionid'=>$mcarray[$i]['questionid']
                ]);

                $count++;
            }
        }

        if($but[3]){
            for($i=0;$i<$mfquery->count();$i++){
                DB::table('papercache')->insert([
                    'cacheid'=>$cacheid,
                    'questionorder'=>$count,
                    'questionid'=>$mfarray[$i]['questionid']
                ]);

                $count++;
            }
        }

        if($but[4]){
            for($i=0;$i<$saquery->count();$i++){
                DB::table('papercache')->insert([
                    'cacheid'=>$cacheid,
                    'questionorder'=>$count,
                    'questionid'=>$saarray[$i]['questionid']
                ]);

                $count++;
            }
        }

        $result['code']=1;
        $result['message']='问卷生成成功';
        return json_encode($result,JSON_UNESCAPED_UNICODE);



        $quesnum=$questionquery->count();
        $ran=1;
        for($j=0;$j<$quesnum;$j++){
            $getrand[$j]=$j;
            }

            for($j=0;$j<$ran;$j++){
                $rand=mt_rand($j,$quesnum-1);
                if($getrand[$j]==$j){
                    $getrand[$j]=$getrand[$rand];
                    $getrand[$rand]=$j;
                }
                $quesrandarr[$j]=$getrand[$j];
            }
        for($i=0;$i<$ran;$i++){
            $quesrandarr[$i]++;
        }
        //return $quesrandarr;
        $questionquery= DB::table("question")
        ->where('paperid',$paperquery->paperid)
        ->whereIn('questionid',$quesrandarr)
        ->get();

        $questionquery1=json_encode($questionquery,JSON_UNESCAPED_UNICODE);
        $questionarray=json_decode($questionquery1, true);
        //return $questionarray;

        for($i=0;$i<$questionquery->count();$i++){
            $result[$i]['questioninfo']=$questionarray[$i];

            $optionsquery= DB::table("options")
            ->where('paperid',$paperquery->paperid)
            ->where('questionid',$questionarray[$i]['questionid'])
            ->get();
            $optionsquery1=json_encode($optionsquery,JSON_UNESCAPED_UNICODE);
            $optionsarray=json_decode($optionsquery1, true);
            $opnum=$optionsquery->count();

            $result[$i]['optionsinfo']=$optionsarray;

            $randarr=array();
            for($j=0;$j<$opnum;$j++){
            $randarr[$j]=$j;
            }

            for($j=0;$j<$opnum;$j++){
                $rand=mt_rand($j,$opnum-1);
                if($randarr[$j]==$j){
                    $randarr[$j]=$randarr[$rand];
                    $randarr[$rand]=$j;
                }
            }

            $str='A';
            echo json_encode($randarr);
            for($j=0;$j<$opnum;$j++){
                $result[$i]['optionsinfo'][$j]=$optionsarray[$randarr[$j]];
                $result[$i]['optionsinfo'][$j]['optionname']=$str;
                $str++;
            }

        }




        /*foreach($questionquery as $u){
            $correctanswer[$u->questionid]=$u->correctanswer;
        }
        $result['code']=1;
        $result['questioninfo']=$questionquery;
        $result['optioninfo']=$optionquery;*/
        return json_encode($result,JSON_UNESCAPED_UNICODE);

    }



    public function fillinpaper(Request $request){//批改用户提交的问卷并保存作答信息
        $answer=1;//答案数组，测试用
        $papername=$request->get("papername");//问卷名
        $username=$request->session()->get("username");//用户名
        if(!$username){
            $result['code']=0;
            $result['message']='登录信息失效，请重新登录';
            return json_encode($result,JSON_UNESCAPED_UNICODE);
        }//检查登录状态



        $paperquery= DB::table("paper")
        ->where('papername',$papername)
        ->first();
        $paperid=$paperquery->paperid;//获取问卷信息

        $questionquery= DB::table("papercache")//papercache表用于缓存题目信息
        ->where('paperid', $paperid)
        ->where('userid',$request->session()->get('userid'))
        ->get();
        $questionquery1=json_encode($questionquery,JSON_UNESCAPED_UNICODE);
        $questioninfo=json_decode($questionquery1,true);//读取问卷缓存信息


        $point=0;//开始评分，point为试卷总分，score为某题得分
        $score=0;//若答案不正确，则本题得分为0
        for($i=0;$i<$questionquery->count();$i++){   //这里要求用户答案对应的题目顺序必须与papercache表中存储顺序相同
            $type=$questioninfo[$i]['questiontype'];

            if($type==1||$type==2){//客观题评分
                if($questioninfo[$i]['correctanswer']==$answer[$i]){
                    $score=$questioninfo[$i]['score'];
                }
                //else $score=0;//重复操作
                $point+=$score;
            }

            else if($type==3||$type==4){//主观题评分
                $answerquery= DB::table("answers")//从answers表单中读取当前题目所有的正确答案
                ->where('paperid', $paperid)
                ->where('questionid',$questioninfo[$i]['questionid'])
                ->get();

                foreach($answerquery as $v){//逐个比对，判断用户答案是否正确
                    if($answer[$i]==$v->answer){
                        $score=$questioninfo[$i]['score'];
                        break;
                    }
                }
                $point+=$score;

            }

            $questionrecord[$i]['papername']=$paperquery->$paperid;
            $questionrecord[$i]['answer']=$answer[$i];
            $questionrecord[$i]['point']=$score;//记录每一道题的作答情况，在获取答题记录id后统一存储到questionrecord表中

        }
        $recordid= DB::table("answerrecord")->insertGetId([//在answerrecord表单中记录问卷整体作答情况，并自动生成recordid，作为题目作答记录的索引
            'paperid'=>$paperid,
            'papername'=>$papername,
            'point'=>$point,
            'username'=>$username,
            'papertype'=>$paperquery->papertype
        ]);
        if(!$recordid){//若recordid=0或不存在，则问卷记录生成失败
            $result['code']=0;
            $result['message']='问卷记录生成失败';
            return json_encode($result,JSON_UNESCAPED_UNICODE);
        }

        for($j=0;$j<$questionquery->count();$j++){//保存每一道题的作答情况
            $questionrecord= DB::table("questionrecord")->insert([
                'recordid'=>$recordid,
                'papername'=>$questionrecord[$j]['papername'],
                'answer'=>$questionrecord[$i]['answer'],
                //'correctanswer'=>$w->correctanswer,//准备删掉
                'point'=>$questionrecord[$i]['point']=$score
            ]);
            /*if(!$questionrecord){
                $result['code']=0;
                $result['message']='问卷题目信息存储失败';
                return json_encode($result,JSON_UNESCAPED_UNICODE);
            }*/ //检测各题目作答情况的代码
        }




            $result['code']=1;
            $result['message']='问卷提交成功';
            $result['point']=$point;
            return json_encode($result,JSON_UNESCAPED_UNICODE);//返回分数及操作成功提示信息
    }








    public function mypaperlist(Request $request){
        $username=$request->session()->get("username");
        $faculty=$request->session()->get("faculty");
        $profession=$request->session()->get("profession");
        if(!$username){
            $result['code']=0;
            $result['message']='登录信息失效，请重新登录';
            return json_encode($result,JSON_UNESCAPED_UNICODE);
        }
        $paperquery= DB::table("paper")
        ->whereIn('faculty', ['-1',$faculty])
        ->whereIn('profession', ['-1',$profession])->get();

        if(!$paperquery->count()){
            $result['code']=0;
            $result['message']='问卷信息获取失败';
            return json_encode($result,JSON_UNESCAPED_UNICODE);
        }
        $answerquery= DB::table("answerrecord")
        ->where('username', $username)->get();

        foreach($answerquery as $u){
            $papername[$u->papername]=$u->papername;
            $paperpoint[$u->papername]=$u->point;
        }

        //$answerquery1=json_encode($answerquery,JSON_UNESCAPED_UNICODE);
        //$answerquery2=json_decode($answerquery1, true);

        $paperquery1=json_encode($paperquery,JSON_UNESCAPED_UNICODE);
        $paperquery2=json_decode($paperquery1, true);
        $i=0;
        foreach($paperquery2 as $v){

            if(in_array($v['papername'],$papername)){
                $paperquery2[$i]['point']=$paperpoint[$v['papername']];
            }
            else{
                $paperquery2[$i]['point']=-1;
            }
            $i+=1;
        }

        $result['code']=1;
        $result['paperinfo']=$paperquery2;

        return json_encode($result,JSON_UNESCAPED_UNICODE);
    }


    public function viewonespaper(Request $request){
        $papername=$request->get("papername");
        $username=$request->get("username");

        $userquery= DB::table("user")->where('username', $username)->first();

        if(!$userquery){
            $result['code']=0;
            $result['message']='用户信息获取失败';
            return json_encode($result,JSON_UNESCAPED_UNICODE);
        }

        $paperquery= DB::table("answerrecord")->where('papername', $papername)->where('username', $username)->first();

        if(!$paperquery){
            $result['code']=0;
            $result['message']='问卷信息获取失败';
            return json_encode($result,JSON_UNESCAPED_UNICODE);
        }
        $questionquery= DB::table("questionrecord")->where('recordid', $paperquery->increasingid)->get();//测试数据：recordid=12，13

        $result['code']=1;
        $result['paperinfo']=$paperquery;
        $result['questioninfo']=$questionquery;
        return json_encode($result,JSON_UNESCAPED_UNICODE);
    }




}
//UPDATE `questionnaire` SET `faculty`='cs',`profession`='cs' WHERE name='orangecat'查询代码
//UPDATE `options` SET `content`='蓝猫' WHERE id=2


