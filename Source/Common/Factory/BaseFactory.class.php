<?php
/**
 * Created by xia&wei.
 * User: 54879490@qq.com
 * Date: 2017/8/16
 * Time: 0:12
 */

namespace Common\Factory;

class BaseFactory
{
    static function create($type){
        if(class_exists($type)){
            return new $type();
        }else{
            throw new \Exception("{$type} 创建失败");
        }
    }
}