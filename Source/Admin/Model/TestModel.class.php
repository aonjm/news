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
        /**
         * 数据是否可以不传   null
         * 数据类型         type
         * 数值所在的区间     between
         * 数值不得在的区间   not between
         * 数据是否属于某个范围  in
         * 数据不得属于某个范围  not in
         * 数据的长度的验证     length
         * 数据是否可以重复     unique
         * 数据是否符合我们所描述的正则表达式 regex
         * 数据是否符合某个自定义函数   function
         * 数据是否符合某个方法    method
         * 数据是否等于某个值      equal
         * 数据是否不得等于某个值   not equal
         * 两条数据是否一致	        confirm
         */
        $this->validate = [
            'id'=>[
                'null'=>true,
                'type'=>'i',
                'between'=>'0,1000',
                'not between' =>'20,30',
            ],
            'name'=>[
                'null'=>false,
                'type'=>'s',
                'in'=>'值1,值二,...',
                'length'=>'5,10',
                'unique'=>false,
                'function'=>['test',[1,2,3]]
            ],
            'pic'=>[
                'length'=>'60',
                'length'=>'60'
            ]
        ];
        $this->vTime = [
            'id'=>[
                'not between'=> [self::INSERT, self::DELETE, self::UPDATE, self::SELECT],
            ]
        ];

        $this->vMessage = [
            'id'=>[
                'type'=>'id必须是一个数字',
                'between'=>'id值必须在某个范围'
            ],
            'name'=>[
                'in'=>'name必须在某个范围内'
            ],
            'pic'=>[
                'null'=>'图片不能为空',
            ]
        ];
        parent::__construct();
    }
}