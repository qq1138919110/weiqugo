<?php
namespace Home\Controller; //命名空间 定义这些文件在哪
use Think\Controller;  //加载核心控制库
class LoginController extends Controller {
     public function login(){

      if(IS_POST){
       $username =trim(I('post.username'));
       $user_name =M('Home_user')->where(array('username'=>$username,'recycle'=>0))->find();
       if(!$user_name){
        $this->ajaxReturn(1);//账号不存在
       }
       if($user_name['stuats']==1){
        $this->ajaxReturn(2);//已注销
       }
       $password =trim(I('post.password'));
       $qpassword =md5_pwd($password);
       $res =M('Home_user')->where(array('username'=>$username,'password'=>$qpassword,'stuats'=>0,'recycle'=>0))->find();
       if($res['id']){
        session('user_id',$res['id']);
        session('home_username',$username);
        $this->ajaxReturn(3);
       }else{
        $this->ajaxReturn(4);
       }
      }else{
        //查找数据，输出视图
      $set=M('Basic_set')->field('statistical_code')->where(array('id'=>1))->find();
      $this->assign('set',$set);
       $this->display();
      }

        }
      
      public function registered(){
        $set=M('Basic_set')->field('share_title,share_images')->where(array('id'=>1))->find();
        $this->assign('set',$set);
        Vendor('Jssdk.jssdk');
        $jssdk = new \JSSDK('wxb5e50fea3e72762d','e972bb6bdfd8197839029d506279c284');
        $signPackage =$jssdk->getSignPackage();
        $this->assign('signPackage',$signPackage);
        $this->display();
      }
      public function send_messages(){
        if(IS_AJAX){
          $phone =trim(I('post.phone'));
          if(empty($phone)){
            $this->ajaxReturn(1);
          }
          $user =M('Home_user')->where(array('phone'=>$phone,'recycle'=>0))->find();
          if($user){
            $this->ajaxReturn(2);
          }
        $duanxin =M('Duanxin')->where(array('phone'=>$phone))->find();
        if(time()<$duanxin['create_time']+60){
          $this->ajaxReturn(3);//小于一分钟的时候不能发送
        }
        //初始化必填
        $options['accountsid']='79fa050e10b3af28076a182f1fcae761'; //填写自己的
        $options['token']='c96dfcf22e3769afcfef39d7d788c958'; //填写自己的
        //初始化 $options必填
        import("Org.Util.Ucpaas");
        $ucpass = new \Ucpaas($options);
                
                //随机生成6位验证码
        srand((double)microtime()*1000000);//create a random number feed.
        $ychar="0,1,2,3,4,5,6,7,8,9";
        $list=explode(",",$ychar);
        for($i=0;$i<6;$i++){
        $randnum=rand(0,9); // 10+26;
        $authnum.=$list[$randnum];
        }
        //短信验证码（模板短信）,默认以65个汉字（同65个英文）为一条（可容纳字数受您应用名称占用字符影响），超过长度短信平台将会自动分割为多条发送。分割后的多条短信将按照具体占用条数计费。
        $appId = "5b1ec5587b034786aa80640782ac0b10";  //填写自己的
        $to = $phone;

        $templateId = "30897";
        $param=$authnum;
        

        $arr=$ucpass->templateSMS($appId,$to,$templateId,$param);

        if (substr($arr,21,6) == 000000) {
            //如果成功就，这里只是测试样式，可根据自己的需求进行调节
            $duanxin =M('Duanxin')->where(array('phone'=>$to))->find();
            if($duanxin){
              $data['id'] =$duanxin['id'];
              $data['param'] =$param;
              $data['create_time'] =time();
              $res =M('Duanxin')->data($data)->save();
              if($res!==false){
                $this->ajaxReturn(4);//发送成功
              }else{
                $this->ajaxReturn(5);//延迟
              }
            
            }else{
                $data['phone']          = $to;
                $data['param']         =$param;
                $data['create_time']   =time();
                $res2 =M('Duanxin')->data($data)->add();
                if($res2){
                  $this->ajaxReturn(4);
                }else{
                  $this->ajaxReturn(5);
                }
            }
        }else{
            //如果不成功
            $this->ajaxReturn(6);//发送失败
            
        }
        }else{
          $this->redirect('Login/login');
        }
      }
      public function registered_ajax(){
        if(IS_AJAX){
          $username =trim(I('post.username'));
          if(empty($username)){
            $this->ajaxReturn(1);
          }
          $u_name =M('Home_user')->field('id')->where(array('username'=>$username,'recycle'=>0))->find();
          if($u_name){
            $this->ajaxReturn(2);//账号存在
          }
          $phone =trim(I('post.phone'));
          if(empty($phone)){
            $this->ajaxReturn(1);
          }
          $p_user=M('Home_user')->field('id')->where(array('phone'=>$phone,'recycle'=>0))->find();
          if($p_user){
            $this->ajaxReturn(3);//手机号
          }
          $identity =trim(I('post.identity'));
          if(empty($identity)){
            $this->ajaxReturn(1);
          }
          $i_user =M('Home_user')->field('id')->where(array('identity'=>$identity,'recycle'=>0))->find();
          if($i_user){
            $this->ajaxReturn(4);//身份证
          }
          $param =trim(I('post.code'));
          if(empty($param)){
            $this->ajaxReturn(1);
          }
          $duanxin =M('Duanxin')->where(array('phone'=>$phone))->find();
          if(!$duanxin){
            $this->ajaxReturn(5);//请点击发送验证码
          }
          if($duanxin['create_time']+1800<=time()){
            $this->ajaxReturn(6);//验证时间过期
          }
          if($duanxin['param'] !=$param){
            $this->ajaxReturn(7);//验证码不正确
          }
          $data['username'] =$username;
          $data['password'] =md5_pwd(trim(I('post.password')));
          $data['name']     =trim(I('post.name'));
          $data['sex']      =trim(I('post.sex'));
          $data['phone']    =$phone;
          $data['weixin']   =trim(I('post.weixin'));
          $data['identity'] =$identity;
          $data['create_time'] =time();
          $res =M('Home_user')->data($data)->add();
          if($res){
            $this->ajaxReturn(8);
          }else{
            $this->ajaxReturn(9);
          }

        }else{
          $this->redirect('Login/login');
        }
      }
    public function retrieve_ajax(){
      if(IS_AJAX){
        $username =trim(I('post.username'));
        $user =M('Home_user')->where(array('username'=>$username,'recycle'=>0))->find();
        if(!$user){
          $this->ajaxReturn(1);//账号不存在
        }
        if($user['stuats']==1){
          $this->ajaxReturn(2);//账号已被注销
        }
        $dd['adminid'] =$user['id'];
        $this->ajaxReturn($dd);
      }else{
        $this->redirect('Login/login');
      }
    }
    public function find_password(){
   
      $adminid =trim(I('get.adminid'));
      if(empty($adminid)){
        $this->redirect('Login/404');
      }
      $user =M('Home_user')->field('id,phone')->where(array('id'=>$adminid,'stuats'=>0,'recycle'=>0))->find();
      if(!$user){
        $this->redirect('Login/404');
      }
      $this->assign('user',$user);
      $this->display();
    }
    public function send_param(){
      if(IS_AJAX){
        $id =trim(I('post.id'));
        if(empty($id)){
          $this->ajaxReturn(1);
        }
        $phone =trim(I('post.phone'));
        if(empty($phone)){
          $this->ajaxReturn(1);
        }
        $user =M('Home_user')->where(array('id'=>$id,'phone'=>$phone,'stuats'=>0,'recycle'=>0))->find();
        if(!$user){
          $this->ajaxReturn(1);
        }
        $duanxin =M('Duanxin')->where(array('phone'=>$phone))->find();
        if(time()<$duanxin['create_time']+60){
          $this->ajaxReturn(2);//小于一分钟的时候不能发送
        }
        //初始化必填
        $options['accountsid']='79fa050e10b3af28076a182f1fcae761'; //填写自己的
        $options['token']='c96dfcf22e3769afcfef39d7d788c958'; //填写自己的
        //初始化 $options必填
        import("Org.Util.Ucpaas");
        $ucpass = new \Ucpaas($options);
                
                //随机生成6位验证码
        srand((double)microtime()*1000000);//create a random number feed.
        $ychar="0,1,2,3,4,5,6,7,8,9";
        $list=explode(",",$ychar);
        for($i=0;$i<6;$i++){
        $randnum=rand(0,9); // 10+26;
        $authnum.=$list[$randnum];
        }
        //短信验证码（模板短信）,默认以65个汉字（同65个英文）为一条（可容纳字数受您应用名称占用字符影响），超过长度短信平台将会自动分割为多条发送。分割后的多条短信将按照具体占用条数计费。
        $appId = "5b1ec5587b034786aa80640782ac0b10";  //填写自己的
        $to = $phone;

        $templateId = "30897";
        $param=$authnum;
        

        $arr=$ucpass->templateSMS($appId,$to,$templateId,$param);

        if (substr($arr,21,6) == 000000) {
            //如果成功就，这里只是测试样式，可根据自己的需求进行调节
            $duanxin =M('Duanxin')->where(array('phone'=>$to))->find();
            if($duanxin){
              $data['id'] =$duanxin['id'];
              $data['param'] =$param;
              $data['create_time'] =time();
              $res =M('Duanxin')->data($data)->save();
              if($res!==false){
                $this->ajaxReturn(3);//发送成功
              }else{
                $this->ajaxReturn(4);//延迟
              }
            
            }else{
                $data['phone']          = $to;
                $data['param']         =$param;
                $data['create_time']   =time();
                $res2 =M('Duanxin')->data($data)->add();
                if($res2){
                  $this->ajaxReturn(3);
                }else{
                  $this->ajaxReturn(4);
                }
            }
        }else{
            //如果不成功
            $this->ajaxReturn(4);//发送失败
            
        }
      }else{
        $this->redirect('Login/login');
      }
    }
    public function retrieve_password_ajax(){
      if(IS_AJAX){
        $param =trim(I('post.param'));
        if(empty($param)){
          $this->ajaxReturn(1);
        }
        $phone=trim(I('post.phone'));
        if(empty($phone)){
          $this->ajaxReturn(1);
        }
        $id =trim(I('post.id'));
        if(empty($id)){
          $this->ajaxReturn(1);
        }
        $user =M('Home_user')->where(array('id'=>$id,'phone'=>$phone,'stuats'=>0,'recycle'=>0))->find();
        if(!$user){
          $this->ajaxReturn(1);
        }
        $duanxin =M('Duanxin')->where(array('phone'=>$phone))->find();
        if(!$duanxin){
          $this->ajaxReturn(2);//请点击发送验证码
        }
        if($duanxin['create_time']+1800<=time()){
          $this->ajaxReturn(3);//验证时间过期
        }
        if($duanxin['param'] !=$param){
          $this->ajaxReturn(4);//验证码不正确
        }
        $data['id']         =$user['id'];
        $data['password']   =md5_pwd(trim(I('post.xmima')));
        $res=M('Home_user')->data($data)->save();
        if($res!==false){
          $this->ajaxReturn(5);
        }else{
          $this->ajaxReturn(6);
        }
      }else{
       $this->redirect('Login/login');
      }
    }
   
}