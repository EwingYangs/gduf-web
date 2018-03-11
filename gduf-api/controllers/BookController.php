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

    public function actionGetBookDetail(){
        $ListISBN = Yii::$app->request->post('ListISBN');
        $header = array(
            "Content-Type: application/html",
        );

        $arr = array();
        $bookImage = '';

        if($ListISBN != '未获取'){
            $gdufbookdetailUrl = Yii::$app->params['gdufbookdetailUrl'];
            $gdufbookdetailUrl = $gdufbookdetailUrl . $ListISBN;

            $bookInfo = Common::curlGetContents($gdufbookdetailUrl, 1800 , $header ,1) ;
            if($bookInfo == 'bad isbn'){
                Common::ajaxResult(State::$SYS_ERROR_CODE , 'bad isbn');
            }
            $bookInfo = preg_replace("/db:attribute/","attribute",$bookInfo);

            $bookInfo = simplexml_load_string($bookInfo);

            $arr = array();
            foreach($bookInfo->children()->attribute as $k=>$v){
                $arr[((array)($v->attributes()->name))[0]] = ((array)($v))[0];
            }


            if(!$bookInfo->summary || !is_string(((array)($bookInfo->children()->summary))[0])){
                $arr['summary'] = '';
            }else{
                $arr['summary'] = ((array)($bookInfo->children()->summary))[0];
            }

            $bookImage = ((array)($bookInfo->children()->link[2]->attributes()->href))[0];
        }

        $bookLocal = $this->getBookLocal();

        $bookDeatil = [
            'bookImage' => $bookImage,
            'bookLocal' => $bookLocal,
            'baseInfo' => $arr,
        ];

        Common::ajaxResult(State::$SUSSION_CODE , State::$SUSSION_MSG , $bookDeatil);
    }


    /**
     * 获取图书图片
     *
     * @return Response|JSON
     */
    // public function getBookImage()
    // {
    //     $ListISBN = Yii::$app->request->post('ListISBN');
    //     $gdufbookImageUrl = Yii::$app->params['gdufbookImageUrl'];
    //     $header = array(
    //         "Content-Type: application/html",
    //     );
    //     $gdufbookImageUrl = $gdufbookImageUrl . "?ListISBN=".$ListISBN.';';

    //     $bookImage = Common::curlGetContents($gdufbookImageUrl, 1800 , $header ,1) ;
    //     $bookImage = simplexml_load_string($bookImage);
    //     return $bookImage;
    // }

    /**
     * 获取图书馆藏信息
     *
     * @return Response|JSON
     */
    public function getBookLocal()
    {
        $ListISBN = Yii::$app->request->post('ListRecno');
        $gdufbookLocalUrl = Yii::$app->params['gdufbooklocalUrl'];
        $header = array(
            "Content-Type: application/html",
        );
        $gdufbookLocalUrl = $gdufbookLocalUrl . "?ListRecno=".$ListISBN.';';

        $bookLocal = Common::curlGetContents($gdufbookLocalUrl, 1800 , $header ,1) ;
        $bookLocal = simplexml_load_string($bookLocal);
        return $bookLocal->books;
    }
}
