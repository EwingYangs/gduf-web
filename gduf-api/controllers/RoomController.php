<?php

namespace app\controllers;

use Yii;
use app\controllers\BaseController;
use app\models\Common;
use app\models\State;
use app\models\GdufFiter;

class RoomController extends BaseController
{

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
        $gdufclassroomUrl = Yii::$app->params['gdufclassroomUrl'];
        $header = array(
            "Content-Type: application/x-www-form-urlencoded",
        );
        $content = array(
            'xnxqh' => $xnxqh,
            'xqid' => $xqid,
            'jzwid' => $jzwid,
        );
        $roomInfo = Common::curlPostAppMsg($gdufclassroomUrl, $content, $header, 1800 ,1);
        // $scoreInfo=ob_get_contents();//获取输出的内容
        $roomInfo = preg_replace("/[\t\n\r]+/","",$roomInfo['query']);//去掉换行、制表等特殊字符
        $bookInfo = GdufFiter::fiterRoom($roomInfo); //过滤图书内容
        Common::ajaxResult(State::$SUSSION_CODE , State::$SUSSION_MSG ,$roomInfo);
    }

}
