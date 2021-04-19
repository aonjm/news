<?php
/**
 * Created by xia&wei.
 * User: 54879490@qq.com
 * Date: 2017/9/2
 * Time: 18:09
 * 测试model
 */

namespace Admin\Model;

use Common\Model;

class TestModel extends Model
{

    public function __construct()
    {
        $this->tableName = 'test';
        parent::__construct();
    }
}