<?php
namespace Home\Controller; //命名空间 定义这些文件在哪
use Think\Controller;  //加载核心控制库
class IndexController extends HomeController {
  public function _initialize()
  {
  }
     //公共头，带导航栏
  public function header(){
    $navigation =M('Navigation')->field('navigation_name,navigation_id')->order('navigation_no')->select();
    $this->assign('navigation',$navigation);
    $this->display();
  }  
}