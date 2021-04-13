<?php
/**
 * Created by xia&wei.
 * User: 54879490@qq.com
 * Date: 2017/8/15
 * Time: 1:36
 */
if (!defined('SITE')) exit('Access Denied');
require 'Conf.inc.php';
require 'Functions.php';
spl_autoload_register('loadAbs');
echo \Common\Url::getM();