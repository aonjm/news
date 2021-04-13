<?php
/**
 * Created by xia&wei.
 * User: 54879490@qq.com
 * Date: 2017/8/15
 * Time: 1:21
 */
if (!defined('SITE')) exit('Access Denied');
//定义整个应用在操作系统中的目录
define('PATH_APP',dirname(dirname(dirname(__FILE__))));
//定义核心文件目录
define('PATH_SOURCE',PATH_APP.DIRECTORY_SEPARATOR.'Source');
//定义模块所在目录
define('PATH_MODULE',PATH_SOURCE.DIRECTORY_SEPARATOR.MODULE);
var_dump(PATH_MODULE);

