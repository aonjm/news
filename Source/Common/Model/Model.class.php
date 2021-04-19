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

    public function __construct()
    {
        $this->db = new Db($this->parseConfig());
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
            if (isset($val['Key']) && $val['Key'] == 'PRI') {
                $this->pk = $val['Field'];
            }
            $this->fields[] = $val['Field'];
            if (strpos($val['Type'], 'int') !== false) {
                $this->fieldsType[$val['Field']] = 'i';
            } else {
                $this->fieldsType[$val['Field']] = 's';
            }
        }
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


}