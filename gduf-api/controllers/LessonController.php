<?php

namespace app\controllers;

use Yii;
use app\controllers\BaseController;
use app\models\Common;
use app\models\State;
use app\models\GdufFiter;

class LessonController extends BaseController
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
    public function actionGetLesson()
    {
        $xnxqh = Yii::$app->params['trem'];
        $zc = Yii::$app->request->post('zc');//周次
        $encoded = Yii::$app->request->post('encoded');//周次
        $gduflessonUrl = Yii::$app->params['gduflessonUrl'];
        $header = array(
            "Content-Type: application/x-www-form-urlencoded",
        );
        $content = array(
            'xnxq01id' => $xnxqh,
            'zc' => $zc,
            'encoded' => $encoded,
        );
        $LessonInfo = Common::curlPostAppMsg($gduflessonUrl, $content, $header, 1800 ,1);
        // $scoreInfo=ob_get_contents();//获取输出的内容
        $LessonInfo = preg_replace("/[\t\n\r]+/","",$LessonInfo['query']);//去掉换行、制表等特殊字符

        $LessonInfo = GdufFiter::fiterLesson($LessonInfo); //过滤图书内容
        Common::ajaxResult(State::$SUSSION_CODE , State::$SUSSION_MSG ,$LessonInfo);
    }

}
