<?php
/**
 * Created by xia&wei.
 * User: 54879490@qq.com
 * Date: 2017/8/27
 * Time: 19:05
 */

namespace Common;
class Url
{
    private static $controller;
    private static $method;

    private static function init()
    {
        self::parseUrl();
    }

    private static function parseUrl()
    {
        if (!isset($_GET[INDEX_CONTROLLER]) || trim($_GET[INDEX_CONTROLLER]) == '') {
            $_GET[INDEX_CONTROLLER] = 'index';
        }
        if (!isset($_GET[INDEX_METHOD]) || trim($_GET[INDEX_METHOD]) == '') {
            $_GET[INDEX_METHOD] = 'index';
        }
        self::$controller = ucfirst($_GET[INDEX_CONTROLLER]);
        self::$method = $_GET[INDEX_METHOD];

    }

    public static function getC($complete = false)
    {
        if (self::$controller == null) {
            self::init();
        }
        return $complete ? self::$controller . 'Controller' : self::$controller;
    }

    public static function getM()
    {
        if (self::$method == null) {
            self::init();
        }
        return self::$method;
    }
}