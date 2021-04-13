<?php
/**
 * Created by xia&wei.
 * User: 54879490@qq.com
 * Date: 2017/8/27
 * Time: 20:22
 * 创建控制器类
 */
namespace Common;
class ControllerFactory extends Factory
{
    /**
     * 创建控制器类
     *
     * @param null $type
     * @return mixed
     */
    public static function create($type = null)
    {
        $controller = '\\'.MODULE.'\\Controller\\'.Url::getC(true);
        return parent::create($controller);
    }
}