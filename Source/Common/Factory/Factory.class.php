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
        //使用class_exists  是为了能触发自动加载
        if (class_exists($type)){
            return new $type;
        }else{
            throw new \Exception("class {$type} doesn't exist");
        }
    }
}