<?php
namespace Home\Controller; //命名空间 定义这些文件在哪
use Think\Controller;  //加载核心控制库
class PersonalController extends HomeController {
  public function _initialize()
  {
}
  public function vip(){
    $user =M('Home_user')->where(array('id'=>session('user_id')))->find();
    $collect =M('Collect')->where(array('user_id'=>session('user_id')))->count();
    $cart    =M('Shopping_cart')->where(array('user_id'=>session('user_id'),'order_no'=>0))->count();
    $order_nuber =M('Order')->where(array('user_id'=>session('user_id'),'pay_status'=>1))->count();
    $this->assign('order_nuber',$order_nuber);
    $this->assign('cart',$cart);
    $this->assign('collect',$collect);
    $this->assign('user',$user);
    $this->display();
  }
  //名字 
  public function change_name(){
    $user =M('Home_user')->field('name')->where(array('id'=>session('user_id')))->find();
    $this->assign('user',$user);
    $this->display();
  }
  public function change_name_ajax(){
    if(IS_AJAX){
      $uid =session('user_id');
      if(empty($uid)){
        $this->ajaxReturn(1);
      }
      $user=M('Home_user')->where(array('id'=>session('user_id')))->find();
      if(!$user){
        $this->ajaxReturn(1);
      }
      $name=trim(I('post.name'));
      if(empty($name)){
        $this->ajaxReturn(1);
      }
      $data['name'] =$name;
      $data['id']   =$user['id'];
      $res =M('Home_user')->data($data)->save();
      if($res!==false){
        $this->ajaxReturn(2);
      }else{
        $this->ajaxReturn(3);
      }
    }else{
      $this->redirect('Index/index');
    }
  }
  //性别
  public function change_sex(){
    $user =M('Home_user')->field('sex')->where(array('id'=>session('user_id')))->find();
    $this->assign('user',$user);
    $this->display();
  }
  public function change_sex_ajax(){
    if(IS_AJAX){
      $uid =session('user_id');
      if(empty($uid)){
        $this->ajaxReturn(1);
      }
      $user=M('Home_user')->where(array('id'=>session('user_id')))->find();
      if(!$user){
        $this->ajaxReturn(1);
      }
      $sex=trim(I('post.sex'));
      if(empty($sex)){
        $this->ajaxReturn(1);
      }
      $data['sex'] =$sex;
      $data['id']   =$user['id'];
      $res =M('Home_user')->data($data)->save();
      if($res!==false){
        $this->ajaxReturn(2);
      }else{
        $this->ajaxReturn(3);
      }
    }else{
      $this->redirect('Index/index');
    }
  }
 //修改手机号
  public function change_phone(){
    $user =M('Home_user')->where(array('id'=>session('user_id')))->find();
    $this->assign('user',$user);
    $this->display();
  }
   public function change_phone_ajax(){
    if(IS_AJAX){
          $param =trim(I('post.param'));
        if(empty($param)){
          $this->ajaxReturn(1);
        }
        $user =M('Home_user')->where(array('id'=>session('user_id')))->find();
        if(!$user){
          $this->ajaxReturn(1);
        }
        $duanxin =M('Duanxin')->where(array('phone'=>$user['phone']))->find();
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
        $data['phone']      =trim(I('post.phone'));
        $phone =M('Home_user')->where(array('phone'=>$data['phone']))->find();
        if($phone){
          $this->ajaxReturn(7);
        }
        $res=M('Home_user')->data($data)->save();
        if($res!==false){
          $this->ajaxReturn(5);
        }else{
          $this->ajaxReturn(6);
        }
      }else{
        $this->redirect('Index/index');
      }
    }
  //个性签名
  public function signature(){
    $user =M('Home_user')->field('signature')->where(array('id'=>session('user_id')))->find();
    $this->assign('user',$user);
    $this->display();
  }
   public function change_signature_ajax(){
    if(IS_AJAX){
      $uid =session('user_id');
      if(empty($uid)){
        $this->ajaxReturn(1);
      }
      $user=M('Home_user')->where(array('id'=>session('user_id')))->find();
      if(!$user){
        $this->ajaxReturn(1);
      }
      $signature=trim(I('post.signature'));
      
      $data['signature'] =$signature;
      $data['id']   =$user['id'];
      $res =M('Home_user')->data($data)->save();
      if($res!==false){
        $this->ajaxReturn(2);
      }else{
        $this->ajaxReturn(3);
      }
    }else{
      $this->redirect('Index/index');
    }
  }
    //修改密码
    public function modify_password_ajax(){
      if(IS_AJAX){
        $id =session('user_id');
        if(empty($id)){
          $this->ajaxReturn(1);
        }
        $user =M('Home_user')->where(array('id'=>$id))->find();
        if(!$user){
          $this->ajaxReturn(1);
        }
          $password =md5_pwd(trim(I('post.jiumima')));
          $uuser =M('Home_user')->where(array('id'=>$id,'password'=>$password))->find();
          if(!$uuser){
            $this->ajaxReturn(2);//旧密码错误
          }
          $data['password'] =md5_pwd(trim(I('post.xinmima')));
          $data['id']       =$id;
          $res =M('Home_user')->data($data)->save();
          if($res!==false){
            $this->ajaxReturn(3);
          }else{
            $this->ajaxReturn(4);
          }

      }else{
        $this->redirect('Index/index');
      }
    }
     //修改头像
    Public function change_photo_ajax(){
      if(IS_AJAX){
        $id =session('user_id');
        if(empty($id)){
          $this->ajaxReturn(1);
        }
        $data['id']     =$id;
        $photo =I('post.tttpian');
        if($photo!=''){
        $photo_url =explode(',', $photo[0]);
        $suffix      =explode('/', $photo_url[0]);
        $imgsuffix   =explode(';', $suffix[1]);
        $data['photo'] .='img/'.time().'.'.$imgsuffix[0];
        $dataphoto =time().'.'.$imgsuffix[0];
        file_put_contents('./Uploads/img/'.$dataphoto,base64_decode($photo_url[1]));
        }
        $res =M('Home_user')->data($data)->save();
        if($res!==false){
          $this->ajaxReturn(2);
        }else{
          $this->ajaxReturn(3);
        }
      }else{
        $this->redirect('Index/index');
      }
       
      }
  //充值
   public function recharge_ajax(){
      if(IS_AJAX){
        $id =session('user_id');
        if(empty($id)){
          $this->ajaxReturn(1);
        }
        $price =I('post.price');
        if(empty($price)){
          $this->ajaxReturn(1);
        }
         //订单号
        $str='1234567890';
        for ($i=0; $i<8 ; $i++) { 
          $str1=$str[rand(0,strlen($str)-1)];
          $char.=$str1;
          }
        $data['order_id'] =time().$char;
        $data['user_id'] =session('user_id');
        $data['price']   =$price *10;
        $data['recharge_time'] =time();
        $res =M('Chongzhi')->data($data)->add();
        if($res){
          $dd['order_id'] =$res;
          $this->ajaxReturn($dd);
        }else{
          $this->ajaxReturn(3);
        }
      }else{
        $this->redirect('Index/index');
      }
    }
  public function collection_service(){
    $set=M('Basic_set')->field('copyright,name,qq')->where(array('id'=>1))->find();
    //分类 
    //第一种
    // $good_type =M('Goods_type')->order('type_no')->select();
    // foreach ($good_type as $key => $value) {
    //   //商品
    //   $goods[$value['type_id']] =M('Goods')->field('good_name,good_id,good_value')->where(array('good_type'=>$value['type_id'],'recycle'=>0))->select();
    //   foreach ($goods[$value['type_id']] as $k => $val) {
    //     //收藏
    //     $collect[$value['type_id']][$val['good_id']] =M('Collect')->where(array('service_id'=>$val['good_id'],'user_id'=>session('user_id')))->find();
    //     if($collect[$value['type_id']][$val['good_id']]){
    //       $ggggoods[$key] =$value;
    //     }
    //   }
    // }
    // $this->assign('collect',$collect);
    // $this->assign('goods',$goods);
    // $this->assign('good_type',$ggggoods);
    //第二种
    $str_type="";
    $type1=array();
    $new_type=array();
    $collect =M('Collect')->where(array('user_id'=>session('user_id')))->select();
    foreach ($collect as $key => $value) {
     $goods[$value['id']] =M('Goods')->where(array('good_id'=>$value['service_id']))->find();
     $type1[]=$goods[$value['id']]['good_type'];
     $goods[$value['id']]['collect_id']= $value['id'];
    }
    $new_type=array_flip(array_flip($type1));//去掉重复值
    foreach ($new_type as $key => $value) {
      if($str_type==""){
        $str_type=$value;
      }else{
        $str_type=$str_type.','.$value;
      }
    }
    //判断是否有逗号
    if(strpos($str_type,",")!==false){
      $where_type['type_id']=array('in',$str_type);
    }else{
      $where_type['type_id']=$str_type;
    }
    $type_data=M('Goods_type')->where($where_type)->select();
    //dump($type_data);exit;
    foreach ($type_data as $key => $value) {
      foreach ($goods as $k => $val) {
        if($value['type_id']  ==$val['good_type']){ //判断商品分类得分类id 是否等于商品表的分类id
          $type_data[$key]['list'][]=$goods[$k];
        }
      }
    }
    
    $this->assign('type_data',$type_data);

    $this->display();
  }
   public function delete_collect(){
    if(IS_AJAX){
      $uid =session('user_id');
      if(empty($uid)){
        $this->ajaxReturn(1);
      }
      $id =trim(I('post.id'));
      if(empty($id)){
        $this->ajaxReturn(1);
      }
      $collect =M('Collect')->where(array('user_id'=>$uid,'id'=>$id))->find();
      if(!$collect){
        $this->ajaxReturn(1);
      }
      $collect_delete=M('Collect')->where(array('id'=>$id))->delete();
      if($collect_delete!==false){
        $this->ajaxReturn(2);
      }else{
        $this->ajaxReturn(3);
      }
    }else{
     $this->redirect('Index/index');
    }
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
}