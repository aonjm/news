<?php
/**
 * Created by xia&wei.
 * User: 54879490@qq.com
 * Date: 2017/8/16
 * Time: 0:08
 */

namespace Admin\Controller;

use Common\Controller;
use Common\Db;

class IndexController extends Controller
{
    public function index()
    {

        $this->view->display();
    }

    public function test(){
        $config = [
            'password'=>'root',
            'database'=>'news'
        ];
        $db = new Db($config);
        $sql = "insert test(name,pic) values(?,?)";
        $data = [
            'name'=>'测试插入数据',
            'pic'=>'测试插入数据',
        ];
        $params = [
            'sql'=>$sql,
            'bind' =>['ss',$data]
        ];
        $db->execute($params);
        pr($db->getLastInsId());

    }
}