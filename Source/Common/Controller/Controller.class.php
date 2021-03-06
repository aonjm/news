<?php
/**
 * Created by xia&wei.
 * User: 54879490@qq.com
 * Date: 2017/8/27
 * Time: 20:48
 * 控制器基类
 */

namespace Common;
class Controller
{
    protected $view;
    protected $model;

    public function __construct()
    {
        $this->view = new View();
    }

    /**
     * 当访问一个不存在的方法的时候
     *
     * @param $name
     * @param $arguments
     */
    public function __call($name, $arguments)
    {
        exit("<span style='font-size: 100px;'>:((</span><br>method <span style='color: red'>'{$name}'</span> doesn't exist");
    }
    

}