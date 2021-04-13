<?php
/**
 * Created by xia&wei.
 * User: 54879490@qq.com
 * Date: 2017/8/27
 * Time: 18:00
 * 方法库
 */
if (!defined('SITE')) exit('Access Denied');
//按命名空间自动加载
function loadAbs($className){
    $path = PATH_SOURCE.'/'.str_replace('\\','/',$className).'.class.php';
    if (file_exists($path)){
        require $path;
    }
}
//加载common命名空间下的class
function loadCommon($className){

    foreach (scandir(PATH_COMMON) as $val){
        if ($val == '.' || $val == '..' || is_file(PATH_COMMON.'/'.$val)){
            continue;
        }
        $class = explode('\\',$className);
        $class = $class[count($class)-1];
        $path = PATH_COMMON.'/'.$val.'/'.$class.'.class.php';
        if (file_exists($path)){
            require $path;
            return true;
        }

    }
}