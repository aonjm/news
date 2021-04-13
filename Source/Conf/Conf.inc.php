<?php
/**
 * Created by xia&wei.
 * User: 54879490@qq.com
 * Date: 2017/8/15
 * Time: 1:21
 */
if (!defined('SITE')) exit('Access Denied');
//定义整个应用在操作系统中的目录
define('PATH_APP', dirname(dirname(dirname(__FILE__))));
//定义核心文件目录
define('PATH_SOURCE', PATH_APP . '/' . 'Source');
//定义当前模块所在目录
define('PATH_MODULE', PATH_SOURCE . '/' . MODULE);
//定义公共路径
define('PATH_COMMON', PATH_SOURCE . '/' . 'Common');
//定义默认控制器参数名
define('INDEX_CONTROLLER', 'c');
//定义默认操作参数名
define('INDEX_METHOD', 'm');
//url模式
defined('URL_MODE') ? null : define(URL_MODE, 0);

