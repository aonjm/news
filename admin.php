<?php
/**
 * Created by xia wei.
 * User: 54879490@qq.com
 * Date: 2017/8/15
 * Time: 0:56
 */
//定义令牌文件 防止直接执行
header("content-type:text/html;charset=utf-8");
define('SITE','site');
define('MODULE','Admin');
define('URL_MODE',1);
require './Source/Conf/Action.inc.php';