<?php
/**
 * Created by xia&wei.
 * User: 54879490@qq.com
 * Date: 2017/9/2
 * Time: 18:25
 */

namespace Admin\Controller;


use Admin\Model\TestModel;
use Common\Controller;

class TestController extends Controller
{
    public function __construct()
    {
        $this->model = new TestModel();
        parent::__construct();
    }

    public function index()
    {

    }

    public function add()
    {
        $data = [
            'id'=>18,
            'name'=>'php真的很牛逼',
            'pic'=>'hello world',
            'dfdsafdsa'=>'dsafdasf'
        ];
        var_dump($this->model->add($data));
        var_dump($this->model->getError());
    }
}