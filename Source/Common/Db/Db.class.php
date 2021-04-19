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
        'user' => 'root',
        'password' => '',
        'database' => '',
        'port' => 3306,
        'charset' => 'utf8'
    );
    private $lastInsId;

    public function __construct($config = array())
    {
        $config = $this->config = array_merge($this->config, $config);
        @parent::mysqli($config['host'], $config['user'], $config['password'], $config['database'], $config['port']);
        if ($this->connect_errno) {
            $this->dError($this->connect_error);
        }
        $this->set_charset($config['charset']);
    }

    /**
     * 执行sql语句的方法
     *
     * @param $params  array(
                            'sql'=>'insert into test(id,name,pic) values(?,?,?)',
                            'bind'=>array('iss',array(100,'孙胜利的私房库','dqwdqwdwq')),
                            或者'bind'=>array('iss',array('id'=>100,'name'=>'孙胜利的私房库','pic'=>'dqwdqwdwq'))
                        )
     * @return bool|int|\mysqli_result
     */
    public function execute($params)
    {
        $stmt = $this->stmt_init();
        if ($stmt->prepare($params['sql'])) {
            //如果传递了绑定的参数就需要绑定参数
            if (isset($params['bind'])) {
                foreach ($params['bind'][1] as $key => $val) {
                    $tmp[] = &$params['bind'][1][$key];
                }
                array_unshift($tmp, $params['bind'][0]);
                if (!@call_user_func_array([$stmt, 'bind_param'], $tmp)) {
                    $this->dError('参数绑定失败');
                }
            }
            $stmt->execute();
            if ($stmt->result_metadata()) {
                return $stmt->get_result()->fetch_all(MYSQL_ASSOC);
            }
            $this->lastInsId = $stmt->insert_id;
            return $stmt->affected_rows;
        } else {
            $this->dError($stmt->error);
        }
    }

    /**
     * 获取最后执行的insert语句返回的id
     *
     * @return mixed
     */
    public function getLastInsId()
    {
        return $this->lastInsId;
    }

    private function dError($error)
    {
        throw new \Exception($error);
    }
}