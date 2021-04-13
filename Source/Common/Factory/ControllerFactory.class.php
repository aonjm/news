<?php
/**
 * Created by xia&wei.
 * User: 54879490@qq.com
 * Date: 2017/8/16
 * Time: 0:47
 */

namespace Common\Factory;


use Common\Url;

class ControllerFactory extends BaseFactory
{
    public static function create($type = null)
    {
        $controller = '\\'.MODULE.'\\Controller\\'.Url::getC(true);
        return parent::create($controller);
    }
}