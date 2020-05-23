<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function getrandlist($min, $max, $x){//从min-max中抽取x个数生成随机数组result，数组下标从0开始

        for($i=$min;$i<=$max;$i++){
            $randarr[$i]=$i;
        }

        for($i=$min, $j=0;$i<=$max && $j<$x;$i++, $j++){
            $rand=mt_rand($i,$max);
            if($randarr[$i]==$i){
                $randarr[$i]=$randarr[$rand];
                $randarr[$rand]=$i;
            }

            $result[$j]=$randarr[$i];
        }

        return $result;
    }
}
