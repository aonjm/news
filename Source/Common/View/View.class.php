<?php
/**
 * Created by xiaxiaowei
 * User:  *夏晓伟*
 * Email: <54879490@qq.com>
 * Date: 2017/8/28
 * Time: 10:46
 * 视图基类
 */

namespace Common;


class View
{
    private $data;
    private $viewPathDefault;

    /**
     * 显示视图文件
     *
     * doc
     * @param null $path
     */
    public function display($path = null)
    {
        if ($path == null) {
            $path = $this->getViewPath();
        }
        if (file_exists($path)) {
            include "{$path}";
        } else {
            exit("Template does not exist");
        }

    }

    /**
     * 获取视图文件路径
     *
     * doc
     * @return string
     */
    private function getViewPath()
    {
        if (!$this->viewPathDefault) {
            $this->viewPathDefault = PATH_VIEW_SKIN . '/' . Url::getC() . '/' . Url::getM() . '.view.php';
        }
        return $this->viewPathDefault;
    }

    /**
     * 设置数据变量
     *
     * @param $name
     * @param $value
     */
    public function setData($name, $value)
    {
        $this->data[$name] = $value;
    }

    /**
     * 获取变量
     *
     * @param null $name
     * @return mixed
     */
    public function getData($name = null)
    {
        if ($name == null){
            return $this->data;
        }
        return $this->data[$name];

    }

    /**
     * 包含文件
     *
     * doc
     * @param $path
     */
    public function inc($path){
        $pathArr=array(
            $path,
            \PATH_VIEW_SKIN.'/'.Url::getC().'/'.$path.'.view.php',
            \PATH_VIEW_SKIN.'/Common/'.$path.'.view.php',
            null
        );
        foreach ($pathArr as $val){
            if(file_exists($val)){
                include "{$val}";
                break;
            }
            if($val==null){
                echo "{$path} 不存在.";
            }
        }
    }

}