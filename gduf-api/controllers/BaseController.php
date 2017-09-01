<?php
namespace app\controllers;
use Yii;
use yii\rest\Controller;
use app\models\Common;
use app\models\State;

class BaseController extends Controller
{
    public function init()
    {
        $app_access_token = md5('gduf-token-key');
        //验证access-token
        if(!Yii::$app->request->isPost || !isset($_SERVER['HTTP_X_GDUF_ACCESS_TOKEN']) || $_SERVER['HTTP_X_GDUF_ACCESS_TOKEN'] != $app_access_token ){
            Common::ajaxResult(State::$SYS_PERMISSION_ERROR_CODE , State::$SYS_PERMISSION_ERROR_MSG);
        }
    }
}
