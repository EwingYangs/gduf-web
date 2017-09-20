<?php

namespace app\controllers;

use Yii;
use app\controllers\BaseController;
use app\models\Common;
use app\models\State;
use app\models\GdufFiter;

class BookController extends BaseController
{

    //初始化方法
    public function init()
    {
        parent::init();
    }

    /**
     * 获取图书列表
     *
     * @return Response|JSON
     */
    public function actionGetBookList()
    {
        $strType = Yii::$app->request->post('strType');
        $strKeyValue = Yii::$app->request->post('strKeyValue');
        $page = Yii::$app->request->post('page');
        $gdufbooksearchUrl = Yii::$app->params['gdufbooksearchUrl'];
        $header = array(
            "Content-Type: application/html",
        );
        $gdufbooksearchUrl = $gdufbooksearchUrl . "?strType=".$strType."&strKeyValue=".$strKeyValue;
        if($page){
            $gdufbooksearchUrl = $gdufbooksearchUrl . "&page=" . $page;
        }
        $bookInfo = Common::curlGetContents($gdufbooksearchUrl, 1800 , $header ,1) ;
        $bookInfo = preg_replace("/[\t\n\r]+/","",$bookInfo);//去掉换行、制表等特殊字符


        $bookInfo = GdufFiter::fiterBookList($bookInfo); //过滤图书内容
        Common::ajaxResult(State::$SUSSION_CODE , State::$SUSSION_MSG ,$bookInfo);
    }


    /**
     * 获取图书图片
     *
     * @return Response|JSON
     */
    public function actionGetBookImage()
    {
        $ListISBN = Yii::$app->request->post('ListISBN');
        $gdufbookImageUrl = Yii::$app->params['gdufbookImageUrl'];
        $header = array(
            "Content-Type: application/html",
        );
        $gdufbookImageUrl = $gdufbookImageUrl . "?ListISBN=".$ListISBN;

        $bookDetail = Common::curlGetContents($gdufbookImageUrl, 1800 , $header ,1) ;
        $bookDetail = simplexml_load_string($bookDetail);
        Common::ajaxResult(State::$SUSSION_CODE , State::$SUSSION_MSG , $bookDetail);
    }
}
