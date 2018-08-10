<?php
namespace Home\Controller; //命名空间 定义这些文件在哪
use Think\Controller;  //加载核心控制库
class HuodongController extends Controller {
    public function index(){
	  Vendor('Jssdk.jssdk');
	  $jssdk = new \JSSDK('wxb5e50fea3e72762d','f44897ebbfb7349358aa7b307aa021f3');
	  $signPackage =$jssdk->getSignPackage();
	  var_dump($signPackage);exit;
	  $this->assign('signPackage',$signPackage);
      $this->display();
    }
   
}