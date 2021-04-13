<?php
/**
 * Created by xia&wei.
 * User: 54879490@qq.com
 * Date: 2017/8/16
 * Time: 1:03
 */

namespace Common\Controller;


class Controller
{
    public function __call($name, $arguments)
    {
        var_dump('不存在'.$name);
    }
}