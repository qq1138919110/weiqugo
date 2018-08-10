<?php
namespace Home\Controller; //命名空间 定义这些文件在哪
use Think\Controller;  //加载核心控制库
class HomeController extends Controller {
  public function __construct(){ //继承父级的构造函数 这里的父级 指核心构造函数
    parent::__construct();
    if(session('user_id')<1){
    $this->redirect('Login/login');

   }
   $set=M('Basic_set')->field('copyright,name,qq,logo')->where(array('id'=>1))->find();
   $good_type =M('Goods_type')->order('type_no')->select();
   $this->assign('set',$set);
   $this->assign('good_type',$good_type);
    $useragent = addslashes($_SERVER['HTTP_USER_AGENT']);
	  if(strpos($useragent, 'MicroMessenger') === false && strpos($useragent, 'Windows Phone') === false ){
	  $post ='https://open.weixin.qq.com/connect/oauth2/authorize?appid=wx752a937e39fe71c2&redirect_uri=http%3A%2F%2Fwww.fantang.com%2Findex.php%2FHome%2FWxpay%2Fnew_pay.html%3Forder_id%3D82&response_type=code&scope=snsapi_base&state=STATE&connect_redirect=1#wechat_redirect';
	  header ( "location:$post" );
	  }
 
 }
}
