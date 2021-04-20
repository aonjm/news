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


    //验证不通过的自定义提示语句
    protected $vMessage;
    //验证不通过的默认提示语句
    protected $vMessageD = [
        'null' => '{field}不能为空',
        'type' => '{field}数据类型不对',
        'between' => '{field}的值必须介于{rule}',
        'not between' => '{field}不得介于{rule}',
        'in' => '{field}必须属于{rule}',
        'not in' => '{field}不得属于{rule}',
        'length' => '{field}的长度必须介于{rule}',
        'unique' => '{field}中已经存在{value}',
        'regex' => '{field}不符合正则',
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
    //规则的验证时机
    protected $vTime;
    //默认的验证时机
    protected $vTimeD = [self::INSERT, self::DELETE, self::UPDATE, self::SELECT];
    //当前的验证时机
    protected $cTime;
    //定义验证时机常量
    const INSERT = 1;
    const DELETE = 2;
    const UPDATE = 3;
    const SELECT = 4;

    public function __construct()
    {
        $this->db = new Db($this->parseConfig());
        //$data = ['id' => 10, 'name' => '测试插入数据dafdafadsfdasfasas', 'pic' => 'dfadsafdasfsaf'];
        /* $this->setCTime(self::SELECT);
         var_dump($this->validate($data));
         var_dump($this->getError());*/

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
                foreach ($val as $ruleType => $rule) {
                    //如果不存在验证规则
                    if ($ruleType == 'null') continue;
                    /*****************************************/
                    //判断当前字段的验证时机是否包含了当前设置的验证时机
                    /*****************************************/
                    if (in_array($this->cTime, $this->getVTime($key, $ruleType))) {
                        $tmp = $data[$key];
                        if ($ruleType == 'unique') {
                            $tmp = [
                                'fieldName' => $key,
                                'fieldValue' => $data[$key]
                            ];
                        }
                        if ($ruleType == 'confirm') {
                            $tmp = [$data[$rule], $data[$key]];
                        }
                        //对具体的某条规则进行检查
                        if (!$this->check($tmp, $ruleType, $rule)) {
                            $this->error = $this->getValidateMessage($key, $ruleType, $rule, $data[$key]);
                            return false;
                        }
                    }

                }
            } else {
                //默认字段验证规则
                //1、先验证是否可以为空
                if (in_array($this->cTime, $this->getVTime($key, 'null'))) {
                    if (!$val['null']) {
                        $this->error = $this->getValidateMessage($key, 'null');
                        return false;
                    }
                }
            }
        }
        return true;
    }

    /**
     * 对传入的值进行具体的某项规则的验证
     *
     * @param $value  实际传入的值
     * @param $ruleType  验证规则的名称
     * @param $rule    实际写的验证规则
     * @return bool
     */
    protected function check($value, $ruleType, $rule)
    {
        switch ($ruleType) {
            case 'type':
                if ($rule == 'i') {
                    return is_numeric($value);
                } else if ($rule == 's') {
                    return is_string($value);
                } else {
                    return true;
                }
                break;
            case 'between':
                $between = explode(',', $rule);
                return $value >= $between[0] && $value <= $between[1];
                break;
            case 'not between':
                $not_between = explode(',', $rule);
                return $value < $not_between[0] || $value > $not_between[1];
                break;
            case 'in':
                $in = explode(',', $rule);
                return in_array($value, $in);
                break;
            case 'not in':
                $not_in = explode(',', $rule);
                return !in_array($value, $not_in);
                break;
            case 'length':
                $length = explode(',', $rule);
                $valueLen = mb_strlen($value, 'utf-8');
                if (count($length) == 1) {
                    return $valueLen <= $length[0];
                } elseif (count($length) == 2) {
                    return $valueLen >= $length[0] && $valueLen <= $length[1];
                }
                break;
            case 'unique':
                if ($rule) {
                    //如果设定了true  即该字段必须唯一  那么就需要从数据库查询是否存在该值
                    $field = $value['fieldName'];
                    $fieldValue = $value['fieldValue'];
                    $params = [
                        'sql' => "select {$field} from {$this->getTableName()} where {$field}=?",
                        'bind' => [$this->getFieldType($field), [$fieldValue]]
                    ];
                    if (count($this->db->execute($params))) {
                        return false;
                    } else {
                        return true;
                    }

                } else {
                    return true;
                }
                break;
            case 'equal':
                return $value == $rule;
                break;
            case 'not equal':
                return $value != $rule;
                break;
            case 'confirm':
                return $value[0] == $value[1];
                break;
            case 'regex':
                return preg_match($rule, $value);
                break;
            case 'function':
                if (isset($rule[1])) {
                    $params = $rule[1];
                    array_unshift($params, $value);
                } else {
                    $params = [$value];
                }
                return call_user_func_array($rule[0], $params);
                break;
            case 'method':
                if (isset($rule[1])) {
                    $params = $rule[1];
                    array_unshift($params, $value);
                } else {
                    $params = [$value];
                }
                return call_user_func_array([$this, $rule[0]], $params);

                break;

        }
    }

    /**
     * 获取验证提示信息
     *
     * @param $field  字段的名称
     * @param $ruleType   字段的验证规则的类型值
     * @param null $rule 字段验证规则你填的值
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
        if (strpos($message, '{field}') !== false) {
            $message = str_replace('{field}', $this->getValidateAlias($field), $message);
        }
        if (strpos($message, '{rule}') !== false) {
            $message = str_replace('{rule}', $rule, $message);
        }
        if (strpos($message, '{value}') !== false) {
            $message = str_replace('{value}', $val, $message);
        }
        if (strpos($message, '{function}') !== false) {
            $message = str_replace('{function}', $rule[0], $message);
        }
        if (strpos($message, '{method}') !== false) {
            $message = str_replace('{method}', $rule[0], $message);
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

    /**
     * 获取当前字段的当前验证规则类型的验证时机
     *
     * @param $field
     * @param $ruleType
     * @return array
     */
    protected function getVTime($field, $ruleType)
    {
        //如果存在设置的验证时机
        if (isset($this->vTime[$field][$ruleType])) {
            if (is_array($this->vTime[$field][$ruleType])) {
                //如果当前字段的验证时机是个数组的话
                return $this->vTime[$field][$ruleType];
            } else {
                return [$this->vTime[$field][$ruleType]];
            }
        } else {
            return $this->vTimeD;
        }
    }

    /**
     * 设置当前的验证时机
     *
     * @param $type
     */
    public function setCTime($type)
    {
        $this->cTime = $type;
    }

    public function add($data,$autoValidate = true)
    {
        if ($autoValidate){
            $this->setCTime(self::INSERT);
            if (!$this->validate($data)){
                return false;
            }
        }
        if (isset($data[$this->getPk()])){
            unset($data[$this->getPk()]);
        }
        $fieldsType = '';
        foreach ($data as $key=>$val){
            if (!in_array($key,$this->getFields())){
                unset($data[$key]);
                continue;
            }

            $fieldsType .= $this->getFieldType($key);
        }
        if (!count($data)){
            $this->error = '无合法数据.';
            return false;
        }
        $fieldsNameList = implode(',',array_keys($data));
        $fieldsValuesList = implode(',',array_fill(0,count($data),'?'));
        $params = [
            'sql'=>"INSERT INTO {$this->getTableName()}({$fieldsNameList}) VALUES({$fieldsValuesList})",
            'bind'=>[$fieldsType,$data]
        ];
        return $this->db->execute($params);
    }


}