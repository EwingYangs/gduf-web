<?php

namespace app\controllers;

use Yii;
use app\controllers\BaseController;
use app\models\Common;
use app\models\State;
use app\models\GdufFiter;

class FeeController extends BaseController
{

    //初始化方法
    public function init()
    {
        parent::init();
    }

    /**
     * 获取现在的电费
     *
     * @return Response|JSON
     */
    public function actionGetCurrentFee()
    {
        $buildingId = Yii::$app->request->post('buildingId');
        $roomName = Yii::$app->request->post('roomName');
        $encoded = Yii::$app->request->post('encoded');//密钥
        $page = Yii::$app->request->post('page');
        $gdufgetcurrentfeeUrl = Yii::$app->params['gdufgetcurrentfeeUrl'];
        $header = array(
            "Content-Type: application/x-www-form-urlencoded",
        );

        $content = array(
            'buildingId' => $buildingId,
            'roomName' => $roomName,
            'encoded' => $encoded,
        );
        $FeeInfo = Common::curlPostAppMsg($gdufgetcurrentfeeUrl, $content, $header, 1800 ,1);

        $FeeInfo = preg_replace("/[\t\n\r]+/","",$FeeInfo['query']);//去掉换行、制表等特殊字符

        $FeeInfo = GdufFiter::fiterCurrentFee($FeeInfo); //过滤图书内容
        Common::ajaxResult(State::$SUSSION_CODE , State::$SUSSION_MSG ,$FeeInfo);
    }
}
