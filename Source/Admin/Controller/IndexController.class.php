<?php
/**
 * Created by xia&wei.
 * User: 54879490@qq.com
 * Date: 2017/8/16
 * Time: 0:08
 */

namespace Admin\Controller;

use Common\Controller;

class IndexController extends Controller
{
    public function index()
    {
        $this->view->setData('name','xiawei');
        $this->view->setData('sex','男');
        $this->view->setData('from','湖北');
        $this->view->display();
    }
}