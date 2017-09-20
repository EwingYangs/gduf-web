<?php

namespace app\controllers;

use Yii;
use app\controllers\BaseController;
use app\models\Common;
use app\models\State;
use app\models\GdufFiter;

class SiteController extends BaseController
{
    //初始化方法
    public function init()
    {
        parent::init();
    }

    /**
     * 接受到小程序发送的账号密码，请求广金教务系统，获取cookie
     *
     * @return Response|JSON
     */
    public function actionLogin()
    {
        $encode = Yii::$app->request->post('encoded');
        $gdufLoginUrl = Yii::$app->params['gdufLoginUrl'];
        $header = array(
            "Content-Type: application/x-www-form-urlencoded",
        );
        $content = array('encoded' => $encode);
        $result = Common::curlPostAppMsg($gdufLoginUrl, $content, $header, 1800, 1);
        if($result['header'] == 404){
            Common::ajaxResult(State::$SYS_LOGIN_ERROR_CODE , State::$SYS_LOGIN_ERROR_MSG ,'账号或密码错误，登陆失败');
        }else{
            Common::ajaxResult(State::$SUSSION_CODE , State::$SUSSION_MSG ,'登陆成功');
        }
    }

    /**
     * Logout action.
     *
     * @return Response
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }


    /**
     * [查询成绩接口]
     * @Ewing
     * @DateTime 2017-08-28T09:37:25+0800
     * @return   [json]                   [description]
     */
    public function actionScore(){
        ob_start(); //打开缓冲区
        $kksj = Yii::$app->request->post('kksj');//开课时间
        $kcxz = Yii::$app->request->post('kcxz');//课程性质
        $kcmc = Yii::$app->request->post('kcmc');//课程名称
        $xsfs = Yii::$app->request->post('xsfs');//显示方式

        $xsfs = $xsfs ? $xsfs : 'all';

        $header = array(
            "Content-Type: application/x-www-form-urlencoded",
        );
        $gdufScoreUrl = Yii::$app->params['gdufScoreUrl'];
        $content = array(
            'kksj' => $kksj,
            'kcxz' => $kcxz,
            'kcmc' => $kcmc,
            'xsfs' => $xsfs,
        );

        Common::curlPostAppMsg($gdufScoreUrl, $content, $header, 1800 ,0);
        $scoreInfo=ob_get_contents();//获取输出的内容
        $scoreInfo = preg_replace("/[\t\n\r]+/","",$scoreInfo);//去掉换行、制表等特殊字符
        // $pattern = "/<div class=\"Nsb_pw\">([\S\s]*)<\/div>/";

        $scoreInfo = GdufFiter::fiterScore($scoreInfo); //过滤成绩
        Common::ajaxResult(State::$SUSSION_CODE , State::$SUSSION_MSG ,$scoreInfo);
    }

    /**
     * [actionGetScoreFrom description]
     * @Ewing
     * @DateTime 2017-08-30T11:58:28+0800
     * @return   [type]                   [description]
     */
    public function actionGetScoreFrom(){

    }
}
