<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class Helper extends Controller
{
    //
    public static function IDGenerator($model, $trow, $length = 3, $prefix){
        $data = $model::orderBy('id', 'desc')->first();
        if(!$data){
            $og_length = $length;
            $last_number = "";

        }else{
            $code = substr($data->$trow, strlen($prefix)+1);
            $actial_last_number = ($code/1)*1;
            $increament_last_number = $actial_last_number+1;
            $last_number_length = strlen($increament_last_number);
            $og_length = $length - $last_number_length;
            $last_number = $increament_last_number;
        }
        $zeros = ""; 
        for($i=0;$i<$og_length;$i++){
            $zeros.="0";
        }
        return $prefix.'-'.$zeros.$last_number;
    }
}
