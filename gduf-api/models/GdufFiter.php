<?php
namespace app\models;
use Yii;
use app\models\Common;

/**
 * 公共过滤类
 * @Author: Ewing
 * @Date:   2017-08-23 16:14:39
 * @Last Modified by:   Marte
 * @Last Modified time: 2017-09-20 15:04:23
 */
class GdufFiter
{

    private static $week = ['Mon' ,'Tues','Wed','Thur','Fri','Sat','Sun'];

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

        $td_res = self::unsetFirstEles($td_results);
        $td_arr = self::divArray($td_res);
        $th_res = self::getScoreth($scoreInfo);

        $result = [$title_res , $th_res , $td_arr];
        return $result;
    }

    //数组转换
    public static function divArray($arr){
        if(!is_array($arr)){
            return;
        }
        $len = count($arr[0]);
        $res = array();
        foreach($arr as $k=>$v){
            for($i = 0 ; $i < $len ; $i++){
                if(!isset($v[$i])){
                    $v[$i] = '未获取';
                }
                $res[$i][] = $v[$i];
            }
        }
        return $res;
    }


    //去掉过滤后的首元素
    public static function unsetFirstEle($arr){
        if(!is_array($arr)){
            return;
        }
        foreach($arr as $k=>$v){
            if($k == 0){
                continue;
            }
            $res[] = $v[0];
        }
        return $res;
    }

    //去掉过滤后的首元素
    public static function unsetFirstEles($arr){
        if(!is_array($arr)){
            return;
        }
        foreach($arr as $k=>$v){
            if($k == 0){
                continue;
            }
            $res[] = $v;
        }
        return $res;
    }



    //图书html过滤成数组
    public static function fiterBookList($bookInfo){
        $book_pattern = "/<span class=\"author\">作者：<strong>([^<>]+)<\/strong><\/span><span class=\"publisher\">出版社：<strong>([^<>]+)<\/strong><\/span><span class=\"dates\">出版时间：<strong>([^<>]+)<\/strong><\/span><span class=\"dates\">ISBN：<strong>([^<>]+)<\/strong><\/span>/";
        preg_match_all($book_pattern, $bookInfo, $result);

        $book_pattern_1 = "/<h3 class=\"title\"><a href=\"([^<>]+)\" target=‘_blank’> ([^<>]+)<\/a>([^<>]*)<\/h3>/";
        preg_match_all($book_pattern_1, $bookInfo, $result1);


        $book_pattern_2 = "/<span class=\"dates\">索书号：<strong>([^<>]+)<\/strong><\/span>/";
        preg_match_all($book_pattern_2, $bookInfo, $result2);



        $book_pattern_2 = "/<span class=\"dates\">分类号：<strong>([^<>]+)<\/strong><\/span>/";
        preg_match_all($book_pattern_2, $bookInfo, $result6);

        $book_pattern_3 = "/<span class=\"dates\">页数：<strong>([^<>]+)<\/strong><\/span>/";
        preg_match_all($book_pattern_3, $bookInfo, $result3);

        $book_pattern_3 = "/<span class=\"dates\">价格：<strong>([^<>]+)<\/strong><\/span>/";
        preg_match_all($book_pattern_3, $bookInfo, $result7);


        $book_pattern_4 = "/<input id=\"StrTmpRecno\" class=\"inputISBN\" name=\"StrTmpRecno\" type=\"text\"  value=([^<>]+)>/";
        preg_match_all($book_pattern_4, $bookInfo, $result4);


        $book_pattern_5 = "/<div class=\"text\">([^<>]+)<\/div>/";
        preg_match_all($book_pattern_5, $bookInfo, $result5);


        array_push($result , $result2[1]);
        array_push($result , $result6[1]);
        array_push($result , $result3[1]);
        array_push($result , $result7[1]);
        array_push($result , $result5[1]);
        $result = self::unsetFirstEles($result);
        array_unshift($result , $result1[2]);
        array_unshift($result , $result4[1]);

        $result = self::divArray($result);
        return $result;
    }

    public static function fiterRoom($roomInfo){
        $roomInfo = preg_replace("/(<br>)+/","",$roomInfo);//去掉换行、制表等特殊字符

        $roomInfo = preg_replace("/( <div id='' class=\"kbcontent1\">([^<>]*)<\/div>)+/",1,$roomInfo);//去掉换行、制表等特殊字符
        $roomInfo = preg_replace("/(<div id='' class=\"kbcontent1\">([^<>]*)<\/div>)+/",1,$roomInfo);//去掉换行、制表等特殊字符
        $roomInfo = preg_replace("/( &nbsp;)+/",0,$roomInfo);//去掉换行、制表等特殊字符
        $f_pattern = "/<td height=\"28\" align=\"center\"><nobr>([^<>]*)<\/nobr><\/td>/";
        preg_match_all($f_pattern, $roomInfo, $floorResult);

        $r_pattern = "/<td  height=\"28\" align=\"center\" valign=\"top\"><nobr>([^<>]*)<\/nobr><\/td>/";
        preg_match_all($r_pattern, $roomInfo, $roomResult);


        $roomResult = $roomResult[1];
        $floorResult = $floorResult[1];

        $div = count($floorResult);


        $roomResult = Common::partition($roomResult , $div);


        foreach ($roomResult as $key => &$value) {
            $value = Common::partition($value , 7);
        }

        $arr = array();
        foreach($roomResult as $k => $v){
            foreach ($v as $k1 => $v1) {
                $arr[self::$week[$k1]][$floorResult[$k]] = $v1;
            }
        }

        Common::ajaxResult(State::$SUSSION_CODE , State::$SUSSION_MSG ,$arr);


        Common::ajaxResult(State::$SUSSION_CODE , State::$SUSSION_MSG ,$roomResult);

    }



}