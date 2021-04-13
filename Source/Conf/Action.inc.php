<?php
/**
 * Created by xia&wei.
 * User: 54879490@qq.com
 * Date: 2017/8/15
 * Time: 1:36
 */

use \Common\Factory\Factory;
use \Common\Factory\ControllerFactory;

if (!defined('SITE')) exit('Access Denied');
require 'Conf.inc.php';
require 'autoload.php';
spl_autoload_register('autoloadAbs');
$method = \Common\Url::getM();
ControllerFactory::create()->$method();