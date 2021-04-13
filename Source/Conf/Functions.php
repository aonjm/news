<?php
/**
 * Created by xia&wei.
 * User: 54879490@qq.com
 * Date: 2017/8/27
 * Time: 18:00
 */
if (!defined('SITE')) exit('Access Denied');
function loadAbs($className){
    $path = PATH_SOURCE.'/'.str_replace('\\','/',$className).'.class.php';
    if (file_exists($path)){
        require $path;
    }
}