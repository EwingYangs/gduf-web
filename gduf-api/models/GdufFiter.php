<?php
namespace app\models;
use Yii;
use app\models\Common;

/**
 * 公共过滤类
 * @Author: Ewing
 * @Date:   2017-08-23 16:14:39
 * @Last Modified by:   Marte
 * @Last Modified time: 2018-03-15 12:34:44
 */
class GdufFiter
{

    private static $week = ['Monday' ,'Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday'];

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



        if($title_results && !$title_results[0] || !isset($title_results[0])){
            Common::ajaxResult(State::$SYS_LOSS_ERROR_CODE , State::$SYS_LOSS_ERROR_MSG ,'请重新登录');
        }

        $title_res = self::unsetFirstEle($title_results);


        $td_pattern = "/<tr><td>([^<>]+)<\/td><td>([^<>]+)<\/td><td align=\"left\">([^<>]+)<\/td><td align=\"left\">([^<>]+)<\/td><!-- 控制成绩显示 --><td style=\" \"><a href=\"([^<>]+)\">([^<>]+)<\/a><\/td><\/td><td>([^<>]+)<\/td><td>[0-9]+<\/td><!-- 控制绩点显示 -->           <td>([^<>]*)<\/td>      <td>([^<>]+)<\/td><td>([^<>]+)<\/td><td>([^<>]+)<\/td><\/tr>/";
        preg_match_all($td_pattern, $scoreInfo, $td_results);

        $td_res = self::unsetFirstEles($td_results);
        $td_arr = self::divArray($td_res);


        $td_arr = Common::Pagination($td_arr);//分页
        $result = ['all' => $title_res , 'data' => $td_arr];
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
                if(!isset($v[$i]) || !$v[$i]){
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
        $book_pattern = "/<span class=\"author\">作者：<strong>([^<>]*)<\/strong><\/span><span class=\"publisher\">出版社：<strong>([^<>]*)<\/strong><\/span><span class=\"dates\">出版时间：<strong>([^<>]*)<\/strong><\/span><span class=\"dates\">ISBN：<strong>([^<>]*)<\/strong><\/span>/";
        preg_match_all($book_pattern, $bookInfo, $result);

        $book_pattern_1 = "/<h3 class=\"title\"><a href=\"([^<>]*)\" target=‘_blank’> ([^<>]*)<\/a>([^<>]*)<\/h3>/";
        preg_match_all($book_pattern_1, $bookInfo, $result1);

        $book_pattern_2 = "/<span class=\"dates\">索书号：<strong>([^<>]+)<\/strong><\/span>/";
        preg_match_all($book_pattern_2, $bookInfo, $result2);




        $book_pattern_2 = "/<span class=\"dates\">分类号：<strong>([^<>]*)<\/strong><\/span>/";
        preg_match_all($book_pattern_2, $bookInfo, $result6);

        $book_pattern_3 = "/<span class=\"dates\">页数：<strong>([^<>]*)<\/strong><\/span>/";
        preg_match_all($book_pattern_3, $bookInfo, $result3);

        $book_pattern_3 = "/<span class=\"dates\">价格：<strong>([^<>]*)<\/strong><\/span>/";
        preg_match_all($book_pattern_3, $bookInfo, $result7);


        $book_pattern_4 = "/<input id=\"StrTmpRecno\" class=\"inputISBN\" name=\"StrTmpRecno\" type=\"text\"  value=([^<>]*)>/";
        preg_match_all($book_pattern_4, $bookInfo, $result4);


        $book_pattern_5 = "/<div class=\"text\">([^<>]*)<\/div>/";
        preg_match_all($book_pattern_5, $bookInfo, $result5);

        array_push($result , $result2[1]);
        array_push($result , $result6[1]);
        array_push($result , $result3[1]);
        array_push($result , $result7[1]);
        array_push($result , $result5[1]);
        $result = self::unsetFirstEles($result);
        array_unshift($result , $result1[2]);
        array_unshift($result , $result4[1]);


        //查询出书本的剩余数
        $bNumberList = implode(';',$result4[1]);
        $header = array(
            "Content-Type: application/html",
        );
        $gdufbooklocalUrl = Yii::$app->params['gdufbooklocalUrl'] . "?ListRecno=".$bNumberList.';';
        $bookLocal = Common::curlGetContents($gdufbooklocalUrl, 1800 , $header ,1) ;
        $bookLocal = simplexml_load_string($bookLocal);
        $bookLocal = json_decode(json_encode($bookLocal),TRUE);
        $bookLocalCount = array();
        foreach($bookLocal['books'] as $key=>$value){
            if(isset($value['book']['bookid'])){
                $bookLocalCount[] = '1';
            }else{
                $bookLocalCount[] = strval(count($value['book']));
            }
        }
        array_push($result , $bookLocalCount);

        $result = self::divArray($result);
        Common::ajaxResult(State::$SUSSION_CODE , State::$SUSSION_MSG , $result);

        return $result;
    }

    public static function fiterBookDetail(){}

    public static function fiterRoom($roomInfo){
        $roomInfo = preg_replace("/(<br>)+/","",$roomInfo);//去掉换行、制表等特殊字符
        $roomInfo = preg_replace("/( <div id='' class=\"kbcontent1\">([^<>]*)<\/div>)+/",1,$roomInfo);//去掉换行、制表等特殊字符
        $roomInfo = preg_replace("/(<div id='' class=\"kbcontent1\">([^<>]*)<\/div>)+/",1,$roomInfo);//去掉换行、制表等特殊字符
        $roomInfo = preg_replace("/( &nbsp;)+/",0,$roomInfo);//去掉换行、制表等特殊字符

        $f_pattern = "/<td height=\"28\" align=\"center\"><nobr>([^<>]*)<\/nobr><\/td>/";
        preg_match_all($f_pattern, $roomInfo, $floorResult);

        if($floorResult && !$floorResult[0] || !isset($floorResult[0])){
            Common::ajaxResult(State::$SYS_LOSS_ERROR_CODE , State::$SYS_LOSS_ERROR_MSG ,'请重新登录');
        }



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

        return $arr;
    }

    public static function  fiterLesson($lessonInfo){
        $lessonInfo = preg_replace("/(<br>)+/","",$lessonInfo);//去掉换行、制表等特殊字符
        $lessonInfo = preg_replace("/(<br>)+/","",$lessonInfo);//去掉换行、制表等特殊字符
        $lessonInfo = preg_replace("/(<br\/>)+/","",$lessonInfo);//去掉换行、制表等特殊字符
        $lessonInfo = preg_replace("/(&nbsp;)+/","null",$lessonInfo);//去掉换行、制表等特殊字符
        $lessonInfo = preg_replace("/(\" >)+/","\">",$lessonInfo);//去掉换行、制表等特殊字符
        $lessonInfo = preg_replace("/(<font title='老师'>)+/",'|',$lessonInfo);//去掉换行、制表等特殊字符
        $lessonInfo = preg_replace("/(<\/font><font title='周次\(节次\)'>)+/",'|',$lessonInfo);//去掉换行、制表等特殊字符
        $lessonInfo = preg_replace("/(<\/font><font title='教室'>)+/",'|',$lessonInfo);//去掉换行、制表等特殊字符
        $lessonInfo = preg_replace("/(<\/font><\/div><\/td><td width=\"123\" height=\"28\" align=\"center\" valign=\"top\">)+/",'</div>',$lessonInfo);//去掉换行、制表等特殊字符

        $f_pattern = "/style=\"display: none;\" class=\"kbcontent\">([^<>]*)<\/div>/";
        preg_match_all($f_pattern, $lessonInfo, $lessonResult);

        if($lessonResult && !$lessonResult[0] || !isset($lessonResult[0])){
            Common::ajaxResult(State::$SYS_LOSS_ERROR_CODE , State::$SYS_LOSS_ERROR_MSG ,'请重新登录');
        }

        $lessonResult = $lessonResult[1];

        $lessonResult = self::dispatchLesson($lessonResult);
        return $lessonResult;
    }

    public static function dispatchLesson($lessonResult){
        $result = array();
        foreach($lessonResult as $index => $lesson){
            $ca = ceil(($index+1)/7);
            switch ($ca) {
                case 2:
                    $ca = 3;
                    break;
                case 3:
                    $ca = 5;
                    break;
                case 4:
                    $ca = 6;
                    break;
                case 5:
                    $ca = 7;
                    break;
            }
            $lesson = self::explodeLession($lesson, $ca);
            if(!$lesson){
                continue;
            }
            switch ($index%7) {
                case 0:
                    //星期一
                    $result[0][] = $lesson;
                    break;
                case 1:
                    //星期二
                    $result[1][] = $lesson;
                    break;
                case 2:
                    //星期三
                    $result[2][] = $lesson;
                    break;
                case 3:
                    //星期四
                    $result[3][] = $lesson;
                    break;
                case 4:
                    //星期五
                    $result[4][] = $lesson;
                    break;
                case 5:
                    //星期六
                    $result[5][] = $lesson;
                    break;
                case 6:
                    //星期日
                    $result[6][] = $lesson;
                    break;
            }
        }
        ksort($result);
        return array_values($result);
    }

    public static function explodeLession($lesson, $ca){
        if($lesson == 'null'){
            $lesson = null;
        }else{
            $lesson = explode('|', $lesson);
            unset($lesson[2]);
            array_push($lesson, $ca);
            $lesson = array_combine(array('site','teacher','subject','class'),array_values($lesson));
        }
        return $lesson;
    }

    public function fiterCurrentFee($FeeInfo){
        $pattern = "/<div class=\"layout-wrapper\">(.*)<\/div>/";
        preg_match($pattern, $FeeInfo, $result);


        $td_pattern = "/<td class=\"warning\">[^<>]*<\/td>                                <td>[^<>]*<\/td>                                <td class=\"info\">(?<fee>[^<>]*?)<\/td>/";
        preg_match_all($td_pattern, $result[0], $result);

        if(!$result['fee']){
            Common::ajaxResult(State::$SYS_ERROR_CODE , '您的宿舍暂时没有录入系统!');
        }

        return isset($result['fee'][0]) ? $result['fee'][0] : '0.00';
    }


    public function fiterFeeList($FeeInfo){
        $pattern = "/<div class=\"layout-wrapper\">(.*)<\/div>/";
        preg_match($pattern, $FeeInfo, $result);

        $fee_pattern = "/<tr>                                <td class=\"warning\">(?<date>[^<>]*?)<\/td>                                <td>(?<today>[^<>]*?)<\/td>                                <td class=\"info\">(?<remain>[^<>]*?)<\/td>                            <\/tr>/";
        preg_match_all($fee_pattern, $result[0], $feeList);
        if(!$feeList['remain']){
            Common::ajaxResult(State::$SYS_ERROR_CODE , '您的宿舍暂时没有录入系统!');
        }

        $feeListArr = [];
        foreach($feeList['remain'] as $k=>$v){
            $feeListArr[] = [
                "date"=>$feeList['date'][$k],
                "today"=>$feeList['today'][$k],
                "remain"=>$feeList['remain'][$k]
            ];
        }



        $buy_pattern = "/<td class=\"warning\">(?<date>[^<>]*?)<\/td>                                <td>[^<>]*<\/td>                                <td class=\"info\">(?<fee>[^<>]*?)<\/td>                                <td>(?<amount>[^<>]*?)<\/td>/";
        preg_match_all($buy_pattern, $result[0], $buyList);

        $buyListArr = [];
        if($buyList['fee']){
            foreach($buyList['fee'] as $k=>$v){
                $buyListArr[] = [
                    "date"=>$buyList['date'][$k],
                    "fee"=>$buyList['fee'][$k],
                    "amount"=>$buyList['amount'][$k]
                ];
            }
        }
        return [
            'feeListArr' => $feeListArr,
            'buyListArr' => $buyListArr,
        ];
    }





}