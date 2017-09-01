<?php
namespace app\models;
use Yii;

/**
 * 公共函数
 * @Author: Ewing
 * @Date:   2017-08-23 16:14:39
 * @Last Modified by:   Marte
 * @Last Modified time: 2017-08-30 11:53:32
 */
class Common
{
    /**
     * @desc 将$data写到日志文件 runtime/xhlearn.log
     * @param unknown $data
     */
    public static function wwwLogger($data)
    {
        $log = 'www' . date('Y-m-d') . '.log';
        $logPath = dirname(__FILE__) . '/../runtime/' . $log;
        file_put_contents($logPath, "\n" . date('Y-m-d H:i:s') . "\n" . var_export($data, true), FILE_APPEND);
    }

    public static function get_client_ip()
    {
        $client = @$_SERVER['HTTP_CLIENT_IP'];
        $forward = @$_SERVER['HTTP_X_FORWARDED_FOR'];
        $remote = $_SERVER['REMOTE_ADDR'];

        if (filter_var($client, FILTER_VALIDATE_IP)) {
            $ip = $client;
        } elseif (filter_var($forward, FILTER_VALIDATE_IP)) {
            $ip = $forward;
        } else {
            $ip = $remote;
        }

        return $ip;
    }

    /**
     * 截取指定长度汉字而不会出现以"?>"结尾,超出部分以"..."代替
     * @param <type> $str
     * @param <type> $start
     * @param <type> $len
     * @return <type>
     */
    public static function showShort($str, $len)
    {
        $tempstr = CommonFunction::csubstr($str, 0, $len);
        if ($str <> $tempstr)
            $tempstr .= "..."; //要以什么结尾,修改这里就可以.
        return $tempstr;
    }



    /**
     * 封装curl,可GET和POST PUT DELETE
     * @param string $url
     * @param array $data
     * @param string $method GET POST PUT DELETE
     * @param int $timeout 单位为秒
     */
    public static function curlGetContents($url, $data = '', $method = "GET", $timeout = 5, $header = array())
    {
        if (is_array($data)) {
            $data = http_build_query($data);
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Bmob Web 1.0');
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);

        if ($header) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $header);  //设置头信息的地方
        }

        if ("get" != strtolower($method) && $data) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        }

        $query = curl_exec($ch);
        curl_close($ch);
        return $query;
    }

    /**
     * @desc 封装curl，可Post
     * @param string $url
     * @param array $data
     * @param int $timeout
     * @param int $return_html  0输出html 1不输出html
     */

    public static function curlPostAppMsg($url, $data = '', $header, $timeout = 5 , $html=0)
    {
        $cookie_file = $_SERVER['DOCUMENT_ROOT'].'/../cookie.txt';

        if (is_array($data)) {
            $data = http_build_query($data);
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, $html);
        curl_setopt($ch, CURLOPT_HEADER, 1);//定义是否显示状态头 1：显示 ； 0：不显示
        //函数中加入下面这条语句
        // curl_setopt($ch, CURLOPT_USERAGENT, 'Bmob Web 1.0');
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);  //设置头信息的地方
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
//        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_COOKIEJAR,  $cookie_file); //存储cookies


        curl_setopt($ch, CURLOPT_COOKIEFILE,  $cookie_file); //设置cookies

        $query = curl_exec($ch);

        $header = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        $err = curl_error($ch);

        if($err){
            self::wwwLogger($err);
        }
        if($query === FALSE) {
            $version = curl_version();
            extract(curl_getinfo($ch));
            $err = curl_error($ch);
            $metrics = <<<EOD
        URL....: $url
        Code...: $http_code ($redirect_count redirect(s) in $redirect_time secs)
        Content: $content_type Size: $download_content_length (Own: $size_download) Filetime: $filetime
        Time...: $total_time Start @ $starttransfer_time (DNS: $namelookup_time Connect: $connect_time Request: $pretransfer_time)
        Speed..: Down: $speed_download (avg.) Up: $speed_upload (avg.)
        Error..: $err
        Curl...: v{$version['version']}
EOD;

            self::wwwLogger($metrics);
        }

        curl_close($ch);
        $result = ['query' => $query , 'header' => $header];
        return $result;

    }


    // 将数组转换成Json格式，中文需要进行URL编码处理,将Unicode编码转为中文
    public static function Array2Json($array, $apply_to_keys_also = true)
    {
        CommonFunction::arrayRecursive($array, 'urlencode', $apply_to_keys_also);
        $json = json_encode($array);
        $json = urldecode($json);
        // ext需要不带引号的bool类型
        $json = str_replace("\"false\"", "false", $json);
        $json = str_replace("\"true\"", "true", $json);
        return $json;
    }


    /**
     * 检查手机号格式是否正确
     * @param $creditNum 身份证号
     * @return string
     */
    public static function validateMobile($mobile) {
        if(trim($mobile)==""){
            return false;
        }
        if (!preg_match("/^(0|\\+86|86|17951)?(13[0-9]|15[012356789]|17[0-9]|18[0-9]|14[57])[0-9]{8}$/", $mobile)) {
           return false;
        }
        return true;
    }




    /**
     * @desc ajax 返回值
     */
    public static function ajaxResult($code = 1000, $msg = '成功', $data = null, $return = false)
    {

        $r = array(
            'status' => array('code' => $code, 'msg' => $msg)
        );
        if ($data) {
            $r['data'] = $data;
        }

        if ($return) {
            return json_encode($r);
        } else {
            echo json_encode($r);
            Yii::$app->end();
        }

    }


    /**
     *
     *从一段长文本中,截取包含关键字的一段
     * @param unknown_type $content 原文本
     * @param unknown_type $str 关键字
     * @param unknown_type $len  截取长度
     */
    public static  function getSummery( $content, $str, $len = 250 )
    {

        $startOffset = mb_stripos($content, $str, 0, "utf8");
        if ( $startOffset === false ) { //该段不包含这个关键字, 则返回全部
            return mb_substr($content, 0, $len, "utf8");
        }

        $contentLen =  mb_strlen($content, "utf8");
        if( $startOffset > floor($len/2) ) { //如果截取一半还不达到段头,则从一定的地点开始截取
            $contentOffset = $startOffset -  floor($len/2) ;
        } else {
            $contentOffset = 0;
        }

        return mb_substr($content, $contentOffset, $len, "utf8");

    }



}