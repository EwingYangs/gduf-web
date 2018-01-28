<?php

namespace app\controllers;

use Yii;
use app\controllers\BaseController;
use app\models\Common;
use app\models\State;
use app\models\GdufFiter;

class RoomController extends BaseController
{

    private $roomInfo = null;

    //初始化方法
    public function init()
    {
        parent::init();
    }

    /**
     * 获取教室课表列表
     *
     * @return Response|JSON
     */
    public function actionGetRoom()
    {
        $xnxqh = Yii::$app->params['trem'];
        $xqid = Yii::$app->request->post('xqid');//校区
        $jzwid = Yii::$app->request->post('jzwid');//教学楼
        $encoded = Yii::$app->request->post('encoded');//教学楼
        $jzwidtem = intval($jzwid);
        if(in_array($jzwid, array(15,16,20,18,19))){
            //北教
            $jzwid = 13;
        }

        $gdufclassroomUrl = Yii::$app->params['gdufclassroomUrl'];
        $header = array(
            "Content-Type: application/x-www-form-urlencoded",
        );
        $content = array(
            'xnxqh' => $xnxqh,
            'xqid' => $xqid,
            'jzwid' => $jzwid,
            'encoded' => $encoded,
        );

        $cacheKey = 'room' + $jzwidtem;
        $room = Yii::$app->cache->get($cacheKey);//缓存

        if($room){
            $this->roomInfo = $room;
        }else{
            $this->roomInfo = Common::curlPostAppMsg($gdufclassroomUrl, $content, $header, 1800 ,1);
            $this->roomInfo = preg_replace("/[\t\n\r]+/","",$this->roomInfo['query']);//去掉换行、制表等特殊字符
            $this->roomInfo = GdufFiter::fiterRoom($this->roomInfo); //过滤教室内容
            Yii::$app->cache->set($cacheKey,$this->roomInfo,24*3600*30*5);
        }
        $this->roomInfo = $this->selectRoomByDay();
        $this->roomInfo = $this->fiterFloor($jzwidtem);//楼层过滤

        Common::ajaxResult(State::$SUSSION_CODE , State::$SUSSION_MSG ,$this->roomInfo);
    }

    public function selectRoomByDay(){
        $day = date("l");
        return $this->roomInfo[$day];
    }

    public function fiterFloor($jzwid){
        switch ($jzwid) {
            case 17:
                //实验楼
                $reg = "/^实验楼(.*)/";
                return $this->sortsy($reg);
            case 1:
                //2栋
                $reg = "/^2-(.*)/";
                return $this->sortsy($reg);
            case 4:
                //1栋
                $reg = "/^1-(.*)/";
                return $this->sortsy($reg);
            case 5:
                //7栋
                $reg = "/^7－(.*)/";
                return $this->sortsy($reg);
            case 10:
                //7栋
                $reg = "/^3-(.*)/";
                return $this->sortsy($reg);
            case 14:
                //6栋
                $reg = "/^6-(.*)/";
                return $this->sortsy($reg);
            case 15:
                //北教A
                $reg = "/^北教A(.*)/";
                return $this->sortsy($reg);
            case 16:
                //北教B
                $reg = "/^北教B(.*)/";
                return $this->sortsy($reg);
            case 20:
                //北教C
                $reg = "/^北教C(.*)/";
                return $this->sortsy($reg);
            case 18:
                //北教D
                $reg = "/^北教D(.*)/";
                return $this->sortsy($reg);
            case 19:
                //北教D
                $reg = "/^北阶(.*)/";
                return $this->sortsy($reg);
            default:
                # code...
                break;
        }
    }

    //实验楼楼层分类
    public function sortsy($reg){
        $res = array();
        $r = array();
        foreach($this->roomInfo as $floor => $value){
            preg_match($reg, $floor, $result);
            $s = array();
            foreach($value as $k1 => $v1){
                if($k1 == 1 || $k1 == 2){
                    array_push($s, $v1);
                    array_push($s, $v1);
                    array_push($s, $v1);
                }else{
                    array_push($s, $v1);
                    array_push($s, $v1);
                }
            }
            if($result){
                if(isset($result[1]) && strlen($result[1]) > 3){
                    //获取大于10楼的楼层
                    $floor = substr($result[1] , 0 , 2);
                }else if(isset($result[1]) && strlen($result[1]) == 3){
                    $floor = substr($result[1] , 0 , 1);
                }

                $res[$floor][$result[1]] = $s;
            }
        }
        foreach($res as $key => $value){
            $rr = array();
            $rr['index'] = $key;
            $a = array();
            $aa = array();
            foreach($value as $k => $v){
                $aa['ceil'] = $k;
                $aa['data'] = $v;
                array_push($a, $aa);
            }
            $rr['floor'] = $a;
            array_push($r, $rr);
        }
        array_multisort(array_column($r,'index'),SORT_ASC,$r);
        return $r;
    }

}