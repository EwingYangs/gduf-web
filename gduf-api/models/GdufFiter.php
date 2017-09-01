<?php
namespace app\models;
use Yii;

/**
 * 公共过滤类
 * @Author: Ewing
 * @Date:   2017-08-23 16:14:39
 * @Last Modified by:   Marte
 * @Last Modified time: 2017-08-30 11:10:03
 */
class GdufFiter
{

    //获取成绩的th头
    public static function getScoreth($scoreInfo){
        $th_res = Yii::$app->cache->get('th_res');//缓存
        if($th_res){
            return $th_res;
        }else{
            $th_pattern = "/<th class=\"Nsb_r_list_thb\" style=\"width: 35px;\">([^<>]+)<\/th><th class=\"Nsb_r_list_thb\" style=\"width: 140px;\">([^<>]+)<\/th><th class=\"Nsb_r_list_thb\" style=\"width: 110px;\">([^<>]+)<\/th><th class=\"Nsb_r_list_thb\">([^<>]+)<\/th><th class=\"Nsb_r_list_thb\" style=\"width: 60px;\">([^<>]+)<\/th><th class=\"Nsb_r_list_thb\" style=\"width: 50px;\">([^<>]+)<\/th><th class=\"Nsb_r_list_thb\" style=\"width: 50px;\">([^<>]+)<\/th><th class=\"Nsb_r_list_thb\" style=\"width: 50px;\">([^<>]+)<\/th><th class=\"Nsb_r_list_thb\" style=\"width: 60px;\">([^<>]+)<\/th><th class=\"Nsb_r_list_thb\" style=\"width: 60px;\">([^<>]+)<\/th><th class=\"Nsb_r_list_thb\" style=\"width: 120px;\">([^<>]+)<\/th>/";
            preg_match_all($th_pattern, $scoreInfo, $th_results);
            $th_res = self::unsetFirstEle($th_results);

            Yii::$app->cache->set('th_res',$th_res,24*3600*30);
            return $th_res;
        }
    }


    //成绩html过滤成数组
    public static function fiterScore($scoreInfo){
        $title_pattern = "/查询条件：([^<>]+) <br \/> 一共需要修读<span>([^<>]+)<\/span>学分， 已修读<span>([^<>]+)<\/span>学分， 还需修读<span>([^<>]+)<\/span>学分， 主修课程平均学分绩点<span>([^<>]+)<\/span>， 辅修课程平均学分绩点<span>([^<>]+)。<\/span>/";
        preg_match_all($title_pattern, $scoreInfo, $title_results);
        $title_res = self::unsetFirstEle($title_results);


        $td_pattern = "/<tr><td>([^<>]+)<\/td><td>([^<>]+)<\/td><td align=\"left\">([^<>]+)<\/td><td align=\"left\">([^<>]+)<\/td><!-- 控制成绩显示 --><td style=\" \"><a href=\"([^<>]+)\">([^<>]+)<\/a><\/td><\/td><td>([^<>]+)<\/td><td>0<\/td><!-- 控制绩点显示 -->           <td>([^<>]*)<\/td>      <td>([^<>]+)<\/td><td>([^<>]+)<\/td><td>([^<>]+)<\/td><\/tr>/";
        preg_match_all($td_pattern, $scoreInfo, $td_results);
        $td_res = array();
        foreach($td_results as $k=>$v){
            if($k == 0){
                continue;
            }
            $td_res[] = $v;
        }
        $td_arr = array();
        foreach($td_res as $k=>$v){
            foreach($v as $k1 => $v1){
                $td_arr[$k1][] = $v1;
            }
        }

        $th_res = self::getScoreth($scoreInfo);

        $result = [$title_res , $th_res , $td_arr];
        return $result;
    }


    //去掉过滤后的首元素
    public static function unsetFirstEle($arr){
        foreach($arr as $k=>$v){
            if($k == 0){
                continue;
            }
            $res[] = $v[0];
        }
        return $res;
    }


}