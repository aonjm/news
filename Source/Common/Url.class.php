<?php
/**
 * URL
 * Created by xia&wei.
 * User: 54879490@qq.com
 * Date: 2017/8/15
 * Time: 1:40
 */

namespace Common;


class Url
{
    static private $controller;
    static private $method;

    private static function init()
    {
        if (isset($_SERVER['PATH_INFO'])) {
            self::parsePathInfoUrl($_SERVER['PATH_INFO']);
        }
        self::parseUrl();
    }

    /**
     * 解析path_info url
     */
    public static function parsePathInfoUrl($params)
    {
        var_dump($params);
        preg_match_all('/([^\/]+)\/([^\/]+)/',$params,$data);
        if (count($data[0])){
            //匹配到了一组
        }else{
            //
            $_GET[INDEX_CONTROLLER] = trim($params,'\/');
        }
    }

    /**
     * 解析url
     */
    private static function parseUrl()
    {
        //设置默认的控制器
        if (!isset($_GET[INDEX_CONTROLLER]) || $_GET[INDEX_CONTROLLER] == '') {
            $_GET[INDEX_CONTROLLER] = 'index';
        }
        //设置默认方法
        if (!isset($_GET[INDEX_METHOD]) || $_GET[INDEX_METHOD] == '') {
            $_GET[INDEX_METHOD] = 'index';
        }
        self::$controller = ucfirst($_GET[INDEX_CONTROLLER]);
        self::$method = $_GET[INDEX_METHOD];
    }

    /**
     * 获取控制器
     *
     * @param bool $complete true  获取全称
     * @return string
     */
    public static function getC($complete = false)
    {
        if (!self::$controller) {
            self::init();
        }
        if ($complete) {
            //获取包含后缀的名称
            return self::$controller . 'Controller';
        }
        return self::$controller;
    }

    /**
     * 获取方法
     *
     * @return mixed
     */
    public static function getM()
    {
        if (!self::$method) {
            self::init();
        }
        return self::$method;
    }
}