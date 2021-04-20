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
       // echo "test......";
    }
}