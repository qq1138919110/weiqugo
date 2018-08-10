<?php
namespace Home\Controller; //命名空间 定义这些文件在哪
use Think\Controller;  //加载核心控制库
class IndexController extends HomeController {
  public function _initialize()
  {
}
    public function index(){
       header("Location:index.php?s=/Home/Selected".$GLOBALS["puid_url"]);exit;
    	
    	$this->display();
    }
    
    //授权登录
    public function wechat_login(){
      dump(123456);exit;
      
    }
    
    //***************************************************************************************//
      //不需要授权
    public function index2(){
      {

        $adminid =trim(I('get.adminid'));
        if(empty($adminid)){
          $this->redirect('Index/page_error');
        }
        $shop_id =trim(I('get.shop_id'));
        if(empty($shop_id)){
          $this->redirect('Index/page_error');
        }
        $store =M('Store')->where(array('user_id'=>$adminid,'shop_id'=>$shop_id))->find();
        if(!$store){
          $this->redirect('Index/page_error');
        }
        $APPID='wxc1c8091cedafb8a7';
        $REDIRECT_URI='http://wx.guanchibao.com/index.php/Home/Index/verification/adminid/'.$adminid.'/shop_id/'.$shop_id.'.html';
        $scope='snsapi_base';
        //$scope='snsapi_userinfo';//需要授权
        $url='https://open.weixin.qq.com/connect/oauth2/authorize?appid='.$APPID.'&redirect_uri='.urlencode($REDIRECT_URI).'&response_type=code&scope='.$scope.'&state='.$state.'#wechat_redirect';
        header("Location:".$url);
        //$this->display();
      }

        }
        //需要授权
       public function indexauthorization(){
    {
      $shop_id =trim(I('get.shop_id'));
      $adminid =trim(I('get.adminid'));
      $APPID='wxc1c8091cedafb8a7';
      $REDIRECT_URI='http://wx.guanchibao.com/index.php/Home/Index/verification1/adminid/'.$adminid.'/shop_id/'.$shop_id.'.html';
      //$scope='snsapi_base';
      $scope='snsapi_userinfo';//需要授权
      $url='https://open.weixin.qq.com/connect/oauth2/authorize?appid='.$APPID.'&redirect_uri='.urlencode($REDIRECT_URI).'&response_type=code&scope='.$scope.'&state='.$state.'#wechat_redirect';
      header("Location:".$url);
      //$this->display();
    }

      }
      //获取信息
      public function verification(){
      {
        $appid = "wxc1c8091cedafb8a7";
        $secret = "d4c36712b780b3ae1fcb7d949bd3c55b";
        $code = $_GET["code"];
        $url='https://api.weixin.qq.com/sns/oauth2/access_token?appid='.$appid.'&secret='.$secret.'&code='.$code.'&grant_type=authorization_code';//通过code换取网页授权access_token
        $str=$this->https_requestc_other($url);
        //$str=file_get_contents($url);
        $arr=json_decode($str,true);
        $openid=$arr['openid'];
        $access_token=$arr['access_token'];
        $shop_id =$_GET["shop_id"];
        $adminid =$_GET["adminid"];
        $user =M('Customer_register')->where(array('openid'=>$openid))->find();
        if(!$user){
          $this->redirect('Index/indexauthorization',array('adminid'=>$adminid,'shop_id'=>$shop_id),0);
        }

        $customer_id=M('Customer')->where(array('customer_id'=>$user['customer_id'],'user_id'=>$adminid))->find();
        if(!$customer_id){
          $data['customer_id']    =$user['customer_id'];
          $data['user_id']        =$adminid;
          $data['attention_time'] =time();
          $res= M('Customer')->data($data)->add();
        }
        $this->redirect('Index/storage', array('openid' => $openid,'access_token'=>$access_token,'adminid'=>$adminid,'shop_id'=>$shop_id), 0);
      }

        }
        //授权获取信息
        public function verification1(){
      {
        $appid = "wxc1c8091cedafb8a7";
        $secret = "d4c36712b780b3ae1fcb7d949bd3c55b";
        $code = $_GET["code"];
        $url='https://api.weixin.qq.com/sns/oauth2/access_token?appid='.$appid.'&secret='.$secret.'&code='.$code.'&grant_type=authorization_code';//通过code换取网页授权access_token
        $str=$this->https_requestc_other($url);
        //$str=file_get_contents($url);
        $arr=json_decode($str,true);
        $user=file_get_contents('https://api.weixin.qq.com/sns/userinfo?access_token='.$arr['access_token'].'&openid='.$arr['openid'].'&lang=zh_CN');//获取用户信息
        $user_info=json_decode($user,true);
        $openid=$arr['openid'];
        $access_token=$arr['access_token'];
        $shop_id =$_GET["shop_id"];
        $adminid =$_GET["adminid"];
        $data['openid']       =$arr['openid'];
        $data['wx_name']      =$user_info['nickname'];
        $data['sex']          =$user_info['sex'];
        $data['province']     =$user_info['province'];
        $data['city']         =$user_info['city'];
        $data['country']      =$user_info['country'];
        $data['c_picture']    =$user_info['headimgurl'];
        $data['register_time']=time();
        $res =M('Customer_register')->data($data)->add();
        if($res){
          $dada['customer_id']    =$res['customer_id'];
          $dada['user_id']        =$adminid;
          $dada['attention_time'] =time();
          $ress=M('Customer')->data($dada)->add();
         }
        $this->redirect('Index/storage', array('openid' => $openid,'access_token'=>$access_token,'adminid'=>$adminid,'shop_id'=>$shop_id), 0);

      }

        }

        //存储
    public function storage(){
        {
        $shop_id =trim(I('get.shop_id'));
        $adminid =trim(I('get.adminid'));
        $openid =trim(I('get.openid'));
        session('openid',$openid);
        $access_token=trim(I('get.access_token'));
        session('access_token',$access_token);
        $this->redirect('Index/indexx',array('adminid'=>$adminid,'shop_id'=>$shop_id),0);
          }
        }

        public function indexx(){
          $shop_id =trim(I('get.shop_id'));
          if(empty($shop_id)){
             $this->redirect('Index/page_error');
          }
          $adminid =trim(I('get.adminid'));
          if(empty($adminid)){
             $this->redirect('Index/page_error');
          }
          $store =M('Store')->where(array('shop_id'=>$shop_id,'user_id'=>$adminid))->find();
          if(!$store){
            $this->redirect('Index/page_error');
          }
          $openid ='o1E90wraJ4UW08X0lEYPj7FMlNUo';
          //$customer_register =M('Customer_register')->where(array('openid'=>session('openid')))->find();
          $customer_register =M('Customer_register')->where(array('openid'=>$openid))->find();
          if(!$customer_register){
              $this->redirect('Index/index',array('adminid'=>$adminid,'shop_id'=>$shop_id),0);
          }

          $price =M('Store_send_price')->where(array('shop_id'=>$shop_id))->find();
          $preselection =M('Store_preselection')->where(array('shop_id'=>$shop_id))->find();
          //收藏
          $collect =M('Store_collect')->where(array('shop_id'=>$shop_id,'customer_id'=>$customer_register['customer_id']))->find();
          //评论
          $comments  =M('Comments')->where(array('user_id'=>$adminid,'shop_id'=>$shop_id))->order('comment_time desc')->limit(2)->select();
          foreach ($comments as $key => $value) {
            $customer_id =$value['customer_id'];
            $user_register[$value['comment_id']] =M('Customer_register')->where(array('customer_id'=>$customer_id))->find();
          }
          //优惠券更新
          $coupon_grant =M('Coupon_grant')->where(array('user_id'=>$adminid,'customer_id'=>$customer_register['customer_id'],'coupon_status'=>0))->select();
          if($coupon_grant){
            foreach ($coupon_grant as $key => $value) {
             $coupon_id =$value['coupon_id'];
             $coupon[$value['id']] =M('Coupon')->where(array('coupon_id'=>$coupon_id))->find();
             if($coupon[$value['id']]){
              foreach ($coupon as $k => $v) {
              if($v['end_date'] <time()){
                M('Coupon_grant')->where(array('id'=>$value['id']))->save(array(
                    'coupon_status'  => '2',
                ));
              }
             }
             }

            }
          }
          $this->assign('collect',$collect);
          $this->assign('user_register',$user_register);
          $this->assign('comments',$comments);
          $this->assign('preselection',$preselection);
          $this->assign('price',$price);
          $this->assign('store',$store);
          $this->display();
        }
      public function dituindex(){
        $shop_id =trim(I('get.shop_id'));
          if(empty($shop_id)){
             $this->redirect('Index/page_error');
          }
          $adminid =trim(I('get.adminid'));
          if(empty($adminid)){
             $this->redirect('Index/page_error');
          }
          $store =M('Store')->where(array('shop_id'=>$shop_id,'user_id'=>$adminid))->find();
          if(!$store){
            $this->redirect('Index/page_error');
          }
          $this->assign('store',$store);
          $this->display();
      }
      public function collect_url(){
        if(IS_AJAX){
          $shop_id =trim(I('get.shop_id'));
          if(empty($shop_id)){
             $this->ajaxReturn(1);
          }
          $openid ='o1E90wraJ4UW08X0lEYPj7FMlNUo';
          //$customer_register =M('Customer_register')->where(array('openid'=>session('openid')))->find();
          $customer_register =M('Customer_register')->where(array('openid'=>$openid))->find();
          if(!$customer_register){
             $this->ajaxReturn(1);
          }
          $data['shop_id']      =$shop_id;
          $data['customer_id']  =$customer_register['customer_id'];
          $data['collect_time'] =time();
          $res =M('Store_collect')->data($data)->add();
          if($res){
            $this->ajaxReturn(2);
          }else{
            $this->ajaxReturn(1);
          }
        }else{
          $this->redirect('Index/indexx');
        }
      }
      public function collect_url_delete(){
        if(IS_AJAX){
          $shop_id =trim(I('get.shop_id'));
          if(empty($shop_id)){
             $this->ajaxReturn(1);
          }
          $openid ='o1E90wraJ4UW08X0lEYPj7FMlNUo';
          //$customer_register =M('Customer_register')->where(array('openid'=>session('openid')))->find();
          $customer_register =M('Customer_register')->where(array('openid'=>$openid))->find();
          if(!$customer_register){
             $this->ajaxReturn(1);
          }
          $collect =M('Store_collect')->where(array('shop_id'=>$shop_id,'customer_id'=>$customer_register['customer_id']))->find();
          if(!$collect){
            $this->ajaxReturn(1);
          }
          $res =M('Store_collect')->where(array('id'=>$collect['id']))->delete();
          if($res!==false){
            $this->ajaxReturn(2);
          }else{
            $this->ajaxReturn(1);
          }
        }else{
          $this->redirect('Index/indexx');
        }
      }
      public function comment(){
          $shop_id =trim(I('get.shop_id'));
          if(empty($shop_id)){
             $this->redirect('Index/page_error');
          }
          $adminid =trim(I('get.adminid'));
          if(empty($adminid)){
             $this->redirect('Index/page_error');
          }
          $store =M('Store')->where(array('shop_id'=>$shop_id,'user_id'=>$adminid))->find();
          if(!$store){
            $this->redirect('Index/page_error');
          }
          $openid ='o1E90wraJ4UW08X0lEYPj7FMlNUo';
          //$customer_register =M('Customer_register')->where(array('openid'=>session('openid')))->find();
          $customer_register =M('Customer_register')->where(array('openid'=>$openid))->find();
          if(!$customer_register){
              $this->redirect('Index/index',array('adminid'=>$adminid,'shop_id'=>$shop_id),0);
          }
          //评论
          $comments  =M('Comments')->where(array('user_id'=>$adminid,'shop_id'=>$shop_id))->order('comment_time desc')->select();
          foreach ($comments as $key => $value) {
            $customer_id =$value['customer_id'];
            $user_register[$value['comment_id']] =M('Customer_register')->where(array('customer_id'=>$customer_id))->find();
          }
          $count1 =M('Comments')->where(array('user_id'=>$adminid,'shop_id'=>$shop_id,'comment_rate'=>1))->count();
          $count2 =M('Comments')->where(array('user_id'=>$adminid,'shop_id'=>$shop_id,'comment_rate'=>2))->count();
          $count3 =M('Comments')->where(array('user_id'=>$adminid,'shop_id'=>$shop_id,'comment_rate'=>3))->count();
          $count4 =M('Comments')->where(array('user_id'=>$adminid,'shop_id'=>$shop_id,'comment_rate'=>4))->count();
          $count5 =M('Comments')->where(array('user_id'=>$adminid,'shop_id'=>$shop_id,'comment_rate'=>5))->count();
          $count6 =($count1+($count2*2)+($count3*3)+($count4*4)+($count5*5));
          $count7 =$count1+$count2+$count3+$count4+$count5;
          $count8 =round($count6/$count7,1);
          $numbersmall =substr($count8,2);
          $numberlarge =substr($count8,0,1);
          if($numbersmall>=5){
            $count8 =round($count8,0);
            $count9 =$count8.'.0';
            if($count8!=5){
              $large =1;
            }
          }else{
            if($numbersmall!=0){
                $count8 =round($count8,0);
                if($count8==1){
                $count9 =$count8.'.5';
                }
                if($count8==2){
                $count9 =$count8.'.5';
                }
                if($count8==3){
                $count9 =$count8.'.5';
                }
                if($count8==4){
                $count9 =$count8.'.5';
                }
                if($count8==5){
                $count9 =$count8.'.5';
                }
                if($count8!=5){
                  $small =1;
                }
              }
            }
          //百分比
          $percentage1 =(round($count1/$count7,1))*100;
          $percentage2 =(round($count2/$count7,1))*100;
          $percentage3 =(round($count3/$count7,1))*100;
          $percentage4 =(round($count4/$count7,1))*100;
          $percentage5 =(round($count5/$count7,1))*100;
          $this->assign('percentage1',$percentage1);
          $this->assign('percentage2',$percentage2);
          $this->assign('percentage3',$percentage3);
          $this->assign('percentage4',$percentage4);
          $this->assign('percentage5',$percentage5);
          $this->assign('numberlarge',$numberlarge);
          $this->assign('count9',$count9);
          $this->assign('large',$large);
          $this->assign('small',$small);
          $this->assign('count1',$count1);
          $this->assign('count2',$count2);
          $this->assign('count3',$count3);
          $this->assign('count4',$count4);
          $this->assign('count5',$count5);
          $this->assign('comments',$comments);
          $this->assign('user_register',$user_register);
          $this->display();
      }
    public function getWxAccessToken(){
    //1.请求url地址
    $appid = 'wxc1c8091cedafb8a7';
    $appsecret =  'd4c36712b780b3ae1fcb7d949bd3c55b';
    $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=".$appid."&secret=".$appsecret;

    //2初始化
    $ch = curl_init();
    //3.设置参数
    curl_setopt($ch , CURLOPT_URL, $url);
    curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,FALSE);
    curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,FALSE);
    curl_setopt($ch , CURLOPT_RETURNTRANSFER, 1);
    //4.调用接口
    $res = curl_exec($ch);

    //5.关闭curl
    curl_close( $ch );
    if( curl_errno($ch) ){
      var_dump( curl_error($ch) );
    }
    $arr = json_decode($res, true);
    $access_token=$arr['access_token'];
    return $access_token;

  }
    function https_requestc_other($url)
    {
      $curl = curl_init();
      curl_setopt($curl, CURLOPT_URL, $url);
      curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
      curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
      curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
      $data = curl_exec($curl);
      if (curl_errno($curl)) {return 'ERROR '.curl_error($curl);}
      curl_close($curl);
      return $data;
    }
    
    
    
    
    
    
    
    
    //*************************************************************************************//
   
    
}