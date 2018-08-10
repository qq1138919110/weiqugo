<?php
namespace Admin\Controller; //命名空间 定义这些文件在哪
use Think\Controller;  //加载核心控制库
class AdminController extends Controller {
  public function __construct(){ //继承父级的构造函数 这里的父级 指核心构造函数
    parent::__construct();
   if(session('uid')<1){
    $this->redirect('Admin/Login/index');

   }
  	$username =M('User')->where(array('user_id'=>session('uid')))->find();
  	$this->assign('username',$username);
   }  

 }
