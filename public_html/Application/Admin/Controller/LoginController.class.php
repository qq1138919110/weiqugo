<?php
namespace Admin\Controller; //命名空间 定义这些文件在哪
use Think\Controller;  //加载核心控制库
class LoginController extends Controller {
    public function index(){
      layout(false);
    		$this->display(); //调用模版
    	}
     public function login(){

      if(IS_POST){
        //处理POST的数据
        $code =trim(I('post.code'));
        if(!check_verify($code)){
          $this->ajaxReturn(1);
        }
       $username =trim(I('post.username'));
       $user_name =M('User')->where(array('username'=>$username))->find();
       if(!$user_name){
        $this->ajaxReturn(2);
       }
       $password =trim(I('post.password'));
       $qpassword =md5_pwd($password);
       $res =M('User')->where(array('username'=>$username,'password'=>$qpassword))->find();
       if($res['id']){
        session('uid',$res['id']);
        session('username',$username);
        $this->ajaxReturn(3);
       }else{
        $this->ajaxReturn(4);
       }
      }else{
        //查找数据，输出视图
        $this->display();
      }

        }
        public function code(){
          
        $Verify = new \Think\Verify();
        $Verify->fontSize = 50;
        $Verify->length = 4;
        $Verify->useNoise = false;
        $Verify->entry();
    }
}