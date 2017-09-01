<?php
namespace app\models;
/**
 * @desc ajax 返回状态码
 * 1000 正常       1001-1999 系统错误
 */
class State
{

    public static $SUSSION_CODE = 1000;
    public static $SUSSION_MSG = 'success';

    public static $SYS_ERROR_CODE = 1001;
    public static $SYS_ERROR_MSG = 'busy';

    public static $SYS_PERMISSION_ERROR_CODE = 1002;
    public static $SYS_PERMISSION_ERROR_MSG = 'forbidden';

    public static $SYS_PARAM_ERROR_CODE = 1003;
    public static $SYS_PARAM_ERROR_MSG = 'parameter error';

    public static $SYS_LOGIN_ERROR_CODE = 1004;
    public static $SYS_LOGIN_ERROR_MSG = 'login error';
}