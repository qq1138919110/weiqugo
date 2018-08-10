<?php
namespace Home\Controller; //命名空间 定义这些文件在哪
use Think\Controller;  //加载核心控制库
class HomeController extends Controller {
  public function index(){
      header("Location:index.php?s=/Home/Selected");exit;
  }
}
