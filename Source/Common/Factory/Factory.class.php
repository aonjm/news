<?php
/**
 * Created by xia&wei.
 * User: 54879490@qq.com
 * Date: 2017/8/27
 * Time: 20:16
 * 工厂类基类
 */
namespace Common;
class Factory
{
    /**
     * 创建类实例
     *
     * @param null $type
     * @return mixed
     * @throws \Exception
     */
    protected static function create($type = null)
    {
        if (class_exists($type)){
            return new $type;
        }else{
            throw new \Exception("class {$type} doesn't exist");
        }
    }
}