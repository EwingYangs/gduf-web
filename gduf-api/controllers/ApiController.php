<?php

namespace app\controllers;

use Yii;
use app\controllers\BaseController;
use app\models\Common;
use app\models\State;
use app\models\GdufFiter;

class ApiController extends BaseController
{

    //初始化方法
    public function init()
    {
        parent::init();
    }

    /**
     * [智校园登录接口]
     * @Ewing
     * @DateTime 2018-03-15T20:11:57+0800
     * @return   [type]                   [description]
     */
    public function actionLogin(){
        $xh = Yii::$app->request->post('xh');
        $pwd = Yii::$app->request->post('pwd');
        if(!$xh){
            Common::ajaxResult(State::$SYS_LOGIN_ERROR_CODE , State::$SYS_LOGIN_ERROR_MSG ,'登陆失败，请输入学号');
        }
        if(!$pwd){
            Common::ajaxResult(State::$SYS_LOGIN_ERROR_CODE , State::$SYS_LOGIN_ERROR_MSG ,'登陆失败，请输入密码');
        }

        $header = array(
            "Content-Type: application/x-www-form-urlencoded",
        );
        $gdufLoginUrl = Yii::$app->params['authUserUrl'] . "&xh=" . $xh. "&pwd=" . $pwd;

        $result = Common::curlGetContents($gdufLoginUrl, 1800 , $header ,1);

        echo $result;
        Yii::$app->end();
    }

    /**
     * [获取成绩信息]
     * @Ewing
     * @DateTime 2018-03-15T21:26:33+0800
     * @return   [type]                   [description]
     */
    public function actionGetScore(){
        $xh = Yii::$app->request->post('xh');
        $xnxqid = Yii::$app->request->post('xnxqid');
        $token = Yii::$app->request->post('token');

        if(!$xh){
            Common::ajaxResult(State::$SYS_ERROR_CODE , State::$SYS_ERROR_MSG ,'获取失败，请输入学号');
        }

        $header = array(
            "Content-Type: application/x-www-form-urlencoded",
            "Token: " . $token,
        );
        $gdufScoreUrl = Yii::$app->params['getCjcxUrl'] . "&xh=" . $xh;

        if($xnxqid){
            $gdufScoreUrl .=  "&xnxqid=" . $xnxqid;
        }

        $result = Common::curlGetContents($gdufScoreUrl, 1800 , $header ,1);

        $result = json_decode($result);

        if(is_array($result)  && $result[0] == null){
            echo json_encode(array('data' => array()));
            Yii::$app->end();
        }
        if(!is_array($result) && isset($result->token) && $result->token == '-1'){
            Common::ajaxResult(State::$SYS_TOKEN_ERROR_CODE , State::$SYS_TOKEN_ERROR_MSG);
            Yii::$app->end();
        }
        $result = Common::Pagination($result);//分页
        Common::ajaxResult(State::$SUSSION_CODE , State::$SUSSION_MSG ,$result);
    }

    /**
     * [获取课程信息]
     * @Ewing
     * @DateTime 2018-03-16T12:34:05+0800
     * @return   [type]                   [description]
     */
    public function actionGetLesson(){
        $xh = Yii::$app->request->post('xh');
        $xnxqid = Yii::$app->params['trem'];
        $zc = Yii::$app->request->post('zc');
        $token = Yii::$app->request->post('token');

        if(!$xh){
            Common::ajaxResult(State::$SYS_ERROR_CODE , State::$SYS_ERROR_MSG ,'获取失败，请输入学号');
        }

        if(!$zc){
            Common::ajaxResult(State::$SYS_ERROR_CODE , State::$SYS_ERROR_MSG ,'获取失败，请输入周次');
        }

        $header = array(
            "Content-Type: application/x-www-form-urlencoded",
            "Token: " . $token,
        );


        $gduflessonUrl = Yii::$app->params['getKbcxAzcUrl'] . "&xh=" . $xh . "&xnxqid=" . $xnxqid . "&zc=" . $zc;
        $result = Common::curlGetContents($gduflessonUrl, 1800 , $header ,1);

        $result = json_decode($result);
        if(is_array($result)  && $result[0] == null){
            echo json_encode(array('data' => array()));
            Yii::$app->end();
        }

        if(!is_array($result) && isset($result->token) && $result->token == '-1'){
            Common::ajaxResult(State::$SYS_TOKEN_ERROR_CODE , State::$SYS_TOKEN_ERROR_MSG);
            Yii::$app->end();
        }

        $arr = [];
        foreach($result as $k => $v){
            $weekIndex = intval(substr($v->kcsj, 0, 1));
            $kcsj = Common::getLessonTime($v->kcsj);
            $arr[($weekIndex - 1)][] = array(
                'subject' => $v->kcmc,
                'teacher' => $v->jsxm,
                'site' => $v->jsmc,
                'class' => $kcsj,
                'isonetwo' => $v->sjbz,
            );
        }

        for ($i=0; $i <= 6; $i++) {
            if(!isset($arr[$i])){
                $arr[$i] = [];
            }
        }
        ksort($arr);


        Common::ajaxResult(State::$SUSSION_CODE , State::$SUSSION_MSG ,$arr);

    }



}
