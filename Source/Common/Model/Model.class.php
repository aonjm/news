<?php
/**
 * Created by xia&wei.
 * User: 54879490@qq.com
 * Date: 2017/9/2
 * Time: 18:08
 * model层基类
 */

namespace Common;


class Model
{
    protected $config;
    protected $db;
    protected $tableName;
    protected $trueTableName;
    protected $tablePrefix;
    protected $fields;
    protected $fieldsType;
    protected $pk;
    public $error;

    //规则的验证时机
    protected $vTime;
    //默认的验证时机
    protected $vTimeD = [self::INSERT, self::DELETE, self::UPDATE, self::SELECT];
    //验证不通过的自定义提示语句
    protected $vMessage;
    //验证不通过的默认提示语句
    protected $vMessageD = [
        'null' => '{field}不能为空',
        'type' => '{field}数据类型不对',
        'between' => '{filed}的值必须介于{rule}',
        'not between' => '{filed}不得介于{rule}',
        'in' => '{filed}必须属于{rule}',
        'not in' => '{filed}不得属于{rule}',
        'length' => '{filed}的长度必须介于{rule}',
        'unique' => '{filed}中已经存在{value}',
        'regex' => '{filed}包含非法字符',
        'function' => '{field}没有通过函数{function}的验证',
        'method' => '{field}没有通过方法{method}的验证',
        'equal' => '{field}必须等于{rule}',
        'not equal' => '{field}不得等于{rule}',
        'confirm' => '{field}与{rule}输入不一致',
    ];
    //字段的别名
    protected $vFieldAlias;
    //自定义规则
    protected $validate;
    //默认规则
    protected $validateD;


    //合并后的验证规则
    protected $trueValidate;

    //定义验证时机常量
    const INSERT = 1;
    const DELETE = 2;
    const UPDATE = 3;
    const SELECT = 4;

    public function __construct()
    {
        $this->db = new Db($this->parseConfig());
        $data = ['id' => 100, 'name' => '', 'pic' => 'dfadsafdasfsaf'];
        var_dump($this->validate($data));
        var_dump($this->getError());

    }

    /**
     * 解析数据库配置文件
     *
     * @return array
     */
    protected function parseConfig()
    {
        if (!isset($this->config)) {
            $this->config = [];
            if (defined('DB_HOST')) {
                $this->config['host'] = DB_HOST;
            }
            if (defined('DB_USER')) {
                $this->config['user'] = DB_USER;
            }
            if (defined('DB_PASSWORD')) {
                $this->config['password'] = DB_PASSWORD;
            }
            if (defined('DB_DATABASE')) {
                $this->config['database'] = DB_DATABASE;
            }
            if (defined('DB_PORT')) {
                $this->config['port'] = DB_PORT;
            }
            if (defined('DB_CHARSET')) {
                $this->config['charset'] = DB_CHARSET;
            }
        }
        return $this->config;

    }

    /**
     * 获取数据表前缀
     *
     * @return string
     */
    protected function getTablePrefix()
    {
        if (!isset($this->tablePrefix)) {
            if (defined('DB_PREFIX')) {
                $this->tablePrefix = DB_PREFIX;
            } else {
                $this->tablePrefix = '';
            }
        }
        return $this->tablePrefix;
    }

    /**
     * 获取真实的表名称
     *
     * @return string
     */
    protected function getTableName()
    {
        if (!isset($this->trueTableName)) {
            $this->trueTableName = $this->getTablePrefix() . $this->tableName;
        }
        return $this->trueTableName;
    }

    /**
     * 解析数据表的字段信息
     *
     */
    protected function parseFields()
    {
        $params['sql'] = "SHOW COLUMNS FROM {$this->getTableName()}";
        $fields = $this->db->execute($params);
        foreach ($fields as $val) {
            //获得主键
            if (isset($val['Key']) && $val['Key'] == 'PRI') {
                $this->pk = $val['Field'];
            }
            //获得字段名数组
            $this->fields[] = $val['Field'];
            //获取字段类型
            if (strpos($val['Type'], 'int') !== false) {
                $this->fieldsType[$val['Field']] = 'i';
                $this->validateD[$val['Field']]['type'] = 'i';
            } else {
                $this->fieldsType[$val['Field']] = 's';
                $this->validateD[$val['Field']]['type'] = 's';
            }
            //验证字段是否可以为空不传
            if ($val['Null'] == 'YES' || $val['Extra'] == 'auto_increment') {
                $this->validateD[$val['Field']]['null'] = true;
            } else {
                $this->validateD[$val['Field']]['null'] = false;
            }
        }
    }

    /**
     * 对数据进行验证
     *
     * @param $data
     * @return bool
     */
    public function validate($data)
    {
        //这里必须要循环实际的真实规则，即使没有传递需要的数据 也能使验证起作用
        foreach ($this->getValidate() as $key => $val) {
            if (array_key_exists($key, $data)) {
                //如果数据中存在某个字段
                if ($data[$key] == '') {
                    $this->error = $this->getValidateMessage($key, 'null');
                    return false;
                }
            } else {
                //默认字段验证规则
                //1、先验证是否可以为空
                if (!$val['null']) {
                    $this->error = $this->getValidateMessage($key, 'null');
                    return false;
                }
            }
        }
    }

    /**
     * 获取验证提示信息
     *
     * @param $field  字段的名称
     * @param $ruleType   字段的验证规则值
     * @param null $rule 字段验证规则键
     * @param null $val 字段符合的值
     * @return mixed
     */
    protected function getValidateMessage($field, $ruleType, $rule = null, $val = null)
    {
        //如果某个字段的某条验证规则的自定义提示信息存在，就使用自定义 ，否则 使用默认
        if (isset($this->vMessage[$field][$ruleType])) {
            $message = $this->vMessage[$field][$ruleType];
        } else {
            $message = $this->vMessageD[$ruleType];
        }
        //执行替换
        if (strpos($message, '{field}') !== false) {
            $message = str_replace('{field}', $this->getValidateAlias($field), $message);
        }
        if (strpos($message, '{rule}') !== false) {
            $message = str_replace('{rule}', $ruleType, $message);
        }
        if (strpos($message, '{value}') !== false) {
            $message = str_replace('{value}', $val, $message);
        }
        //function以及method等等需要补充完善.

        return $message;
    }


    /**
     * 获取验证的别名
     *
     * @param $field
     * @return mixed
     */
    protected function getValidateAlias($field)
    {
        if (isset($this->vFieldAlias[$field])) {
            return $this->vFieldAlias[$field];
        } else {
            return $field;
        }
    }

    /**
     * 获取最终的真实验证规则
     *
     * @return mixed
     */
    protected function getValidate()
    {
        if (!isset($this->trueValidate)) {
            //pr($this->getValidateD());
            foreach ($this->getValidateD() as $key => $val) {
                //当提供了自定义验证规则的情况下，以自定义的覆盖默认  否则直接使用默认的
                if (isset($this->validate[$key])) {
                    $this->trueValidate[$key] = array_merge($val, $this->validate[$key]);
                } else {
                    $this->trueValidate[$key] = $val;
                }
            }
        }
        return $this->trueValidate;
    }

    /**
     * 获取默认验证规则
     *
     * @return mixed
     */
    protected function getValidateD()
    {
        if (!isset($this->validateD)) {
            $this->parseFields();
        }
        return $this->validateD;
    }

    /**
     * 获取数据表的字段集合
     *
     * @return mixed
     */
    protected function getFields()
    {
        if (!isset($this->fields)) {
            $this->parseFields();
        }
        return $this->fields;
    }

    /**
     * 获取数据表字段类型的集合或单个字段的类型
     *
     * @param null $fieldName
     * @return mixed
     * @throws \Exception
     */
    protected function getFieldType($fieldName = null)
    {
        if (!isset($this->fieldsType)) {
            $this->parseFields();
        }
        if ($fieldName) {
            return $this->fieldsType[$fieldName];
        } else {
            return $this->fieldsType;
        }
    }

    /**
     * 获取数据表的主键
     *
     * @return mixed
     */
    protected function getPk()
    {
        if (!isset($this->pk)) {
            $this->parseFields();
        }
        return $this->pk;
    }

    /**
     * 获取错误信息
     *
     * @return mixed
     */
    public function getError()
    {
        return $this->error;
    }

}