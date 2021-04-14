<?php
/**
 * Created by xiaxiaowei
 * User:  *夏晓伟*
 * Email: <54879490@qq.com>
 * Date: 2017/8/28
 * Time: 13:06
 */

namespace Common;
class Db extends \mysqli
{
    private $config = array(
        'host' => 'localhost',
        'username' => 'root',
        'password' => '',
        'database' => '',
        'port' => 3306,
        'charset' => 'utf8'
    );

    public function __construct($config = array())
    {
        $config = $this->config = array_merge($this->config, $config);
        @parent::mysqli($config['host'], $config['username'], $config['password'], $config['database'], $config['port']);
        if ($this->connect_errno){
            $this->dbError($this->connect_error);
        }
    }

    private function dbError($error)
    {
        throw new \Exception($error);
    }
}