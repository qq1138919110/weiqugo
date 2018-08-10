<?php
namespace Home\Controller; //命名空间 定义这些文件在哪
use Think\Controller;  //加载核心控制库
class UserController extends Controller {
  public $user_info=array();
  public $user_id="";
  public function _initialize(){
    //dump($_COOKIE);
    /*if(!isset($_COOKIE['login_user_id'])){
        //header('Location:index.php?s=/Home/User/login');
    }else{
      $this->user_id=$_COOKIE['login_user_id'];
    }*/

  }
        public function login(){

          if(IS_POST){
            $time=time();
            //file_put_contents("test1.txt",json_encode($_COOKIE));
              if(trim(I('post.user_phone'))!="" and trim(I('post.user_code'))!=""){
                $where['phone'] =trim(I('post.user_phone'));
                $where['verify']  =trim(I('post.user_code'));
                //$where['status']
                $verify_info=m('verify_sms')->where($where)->order('id DESC')->find();
                m('verify_sms')->where($where)->save(array('status'=>'1'));
                if(!$verify_info){
                  d('HomeUser')->work_error('信息错误请重新输入');
                }else{
                  if($verify_info['status']==1 or intval($verify_info['add_time']-time()) > 900){
                    d('HomeUser')->work_error('验证码已经失效');
                  }
                }
                //判断是否有该用户如果没有则需要新添加一个
                $where_user['user_phone']=$where['phone'];
                $user=m('home_user')->where($where_user)->find();

                if(!$user){
                  $data['user_phone']=$where['phone'];
                  $data['register_time']=$time;
                  $data['is_puid']=i('get.puid')?i('get.puid'):0;
                  $re_user_info=m('home_user')->add($data);

                  if($re_user_info){
                    //判断是否是注册送积分start
                    $where_activity['id']=15;
                    $activity_info=m('activity')->where($where_activity)->find();
                    if($activity_info['active_opening']==1){
                      $where_active_user['id']=$re_user_info;
                      $data_active['reg_score']=1;
                      $data_active['score']=$activity_info['integral'];
                      $user_score=m('home_user')->where($where_active_user)->save($data_active);
                      if($user_score){
                        $score['user_id']=$re_user_info;
                        $score['score']=$activity_info['integral'];
                        $score['remark']="注册送积分";
                        $score['add_time']=$time;
                        $score['update_time']=$time;
                        $score['is_add']=1;
                        $re_score=m('score')->add($score);
                      }
                    }
                    //判断是否是注册送积分end
                    //判断是否是分享送积分start   暂时还不行
                    if(isset($_GET['puid'])){
                      //echo 123;exit;
                      if(trim($_GET['puid'])!=""){
                        $where_share['id']=7;
                        $share_info=m('activity')->where($where_share)->find();
                        if($activity_info['active_opening']==1){
                          $where_pid['id']=trim($_GET['puid']);
                         // $user_pid_info=m('home_user')->field('score')->where($where_pid)->find();
                          $user_pid_info=m('home_user')->where($where_pid)->find();
                          //echo json_encode($user_pid_info);exit;
                          $user_puid_data['score']=$user_pid_info['score']+$share_info['integral'];
                          //echo json_encode($user_puid_data);exit;
                          $where_update_score_one['id']=$user_pid_info['id'];
                         $re_puid_score= m('home_user')->where($where_update_score_one)->save($user_puid_data);
                          $score1['user_id']=trim($_GET['puid']);
                          $score1['score']=$share_info['integral'];
                          $score1['remark']="邀请送积分";
                          $score1['add_time']=$time;
                          $score1['update_time']=$time;
                          $score1['is_add']=1;
                          $re_score2=m('score')->add($score1);
                        }
                      }
                    }

                    //判断是否是分享送积分end
                  }


                }else{
                  $re_user_info=true;
                }
                $user=m('home_user')->where($where_user)->find();
                if($re_user_info){
                  cookie('user_id',$user['id'],array('expire'=>time()+3600*24*365,'prefix'=>'login_'));
                  cookie('user_phone',$user['user_phone'],array('expire'=>time()+3600*24*365,'prefix'=>'login_'));
                  $re['puid_url']="&puid=".$user['id'];
                  d('HomeUser')->work_success($re,'登陆成功');
                }else{
                  d('HomeUser')->work_error('登陆失败');
                }
                /*****************************************************************************************/

            }else{
                d('HomeUser')->work_error('登陆失败');
            }
          }

          $this->display();

        }
        //发送短信验证码
        public function send_verify(){

          $data_stem=i('post.');
          send_sms($data_stem['phone']);
        }
        //获取用户信息
        public function info(){
          $time=time();
          //dump($_COOKIE);
          if(!isset($_COOKIE['login_user_id'])){
            if(isset($_GET['puid'])){
              header('Location:index.php?s=/Home/User/login&puid='.i('get.puid'));
            }else{
              header('Location:index.php?s=/Home/User/login');
            }

          }
          $where['user_id']=$_COOKIE['login_user_id'];
          $where['user_phone']=$_COOKIE['login_user_phone'];
          $user_info=m('home_user')->where($where)->order('id DESC')->find();
          $user_info['head_img']=$user_info['head_img']==""?"images/touxiang.png":$user_info['head_img'];
          $today=date('Ymd',$time);
          $last_sign=date('Ymd',$user_info['last_sign_time']);
          $user_info['is_sign']=$today-$last_sign==0?1:0;
          //dump(m('home_user'));
          $user_info['user_name']=$user_info['user_name']?$user_info['user_name']:$user_info['user_phone'];
          $user_info['sign']=$user_info['sign']?base64_decode($user_info['sign']):'这个人太懒，什么都没有留下';
          //统计各个订单状态的数量status
          $where_not_payment['user_id']=$_COOKIE['login_user_id'];
          $where_not_payment['order_status']=0;
          $count_order['count_order_0']=m('order')->where($where_not_payment)->count();
          $where_not_payment['order_status']=1;
          $count_order['count_order_1']=m('order')->where($where_not_payment)->count();
          $where_not_payment['order_status']=2;
          $count_order['count_order_2']=m('order')->where($where_not_payment)->count();
          $where_not_payment['order_status']=5;
          $count_order['count_order_5']=m('order')->where($where_not_payment)->count();
          $where_not_payment['is_comment']=0;
          $where_not_payment['order_status']=3;
          $count_order['count_order_3']=m('order')->where($where_not_payment)->count();
          //dump($count_order);
          $this->assign('count_order',$count_order);
          //统计各个订单状态的数量end

          //dump($user_info);
          //dump(m('home_user'));
          $this->assign('data',$user_info);
          $where_qq['id']=1;
          $qq=m('seting')->field('qq')->where($where_qq)->find();
          $this->assign('qq',$qq['qq']);
          $this->display();
        }
        //清楚cookie
        public function loginout(){
          cookie('user_id',NULL,array('expire'=>time()-3600*24*365,'prefix'=>'login_'));
          cookie('user_phone',NULL,array('expire'=>time()-3600*24*365,'prefix'=>'login_'));

          $this->success('成功退出账号');

        }
        //收获地址管理
        public function address(){

          if(!isset($_COOKIE['login_user_id'])){
            if(isset($_GET['puid'])){
              header('Location:index.php?s=/Home/User/login&puid='.i('get.puid'));
            }else{
              header('Location:index.php?s=/Home/User/login');
            }
          }

          $where['user_id']=$_COOKIE['login_user_id'];
          $where['user_phone']=$_COOKIE['login_user_phone'];
          $user_info=m('home_user')->where($where)->order('id DESC')->find();
          //dump($user_info);
          $where_address['user_id']=$user_info['id'];
          $where_address['status'] =1;
          $address_lists=m('address')->where($where_address)->order('address_id DESC')->select();
          $address_lists['uid'] =$user_info;

          if(IS_AJAX){
            d('HomeUser')->work_success($address_lists);
          }
          //dump($address_lists);
          //dump(m('address'));
          $this->assign('address_lists',$address_lists);
          $this->display();

        }
        //编辑地址（更新地址信息）
        public function edit_address(){
          $address_id=i('get.id');
          if(IS_POST){
          $data_stem=i('post.');
          $where['user_id']=$_COOKIE['login_user_id'];
          $where['address_id']=$address_id;
          $address =m('address')->where($where)->find();
          $data['address_id']=$address['address_id'];
          if(isset($data_stem['address'])){
            $data['address']=$data_stem['address'];
          }
          if(isset($data_stem['provinces'])){
            $data['provinces']=$data_stem['provinces'];
          }
          if(isset($data_stem['consignee_phone'])){
            $data['consignee_phone']=$data_stem['consignee_phone'];
          }
          if(isset($data_stem['consignee_name'])){
            $data['consignee_name']=$data_stem['consignee_name'];
          }
          $data['update_time']=time();
          $re=m('address')->where($where)->save($data);
          if($re){
            echo '<script>window.location.href="index.php?s=/Home/User/address&edit_address=1'.$GLOBALS['puid_url'].'";</script>';
          }else{
            //$this->error('修改失败');
             echo '<script>window.location.href="index.php?s=/Home/User/address&edit_address=0'.$GLOBALS['puid_url'].'";</script>';
          }
          }
          $where_address['address_id'] =$address_id;
          $address =m('address')->where($where_address)->find();
          $this->assign('address',$address);
          $this->display();
        }

        //添加收货地址
        public function add_address(){
          $this->user_id=$_COOKIE['login_user_id'];
          $user_id=$_COOKIE['login_user_id'];
          if(IS_POST){
            $time=time();
            $data_stem=i('post.');
            $data['consignee_name']=$data_stem['consignee_name'];
            $data['consignee_phone']=$data_stem['consignee_phone'];
            $data['provinces']=$data_stem['provinces'];
            $data['address']=$data_stem['address'];
            $data['user_id']=$user_id;
            $data['add_time']=$time;
            $data['update_time']=$time;
            $re=m('address')->add($data);
            if($re){
              $where['id']=$user_id;
              $user_info=m('home_user')->field('defaul_address')->where($where)->find();
              //dump($user_info);exit;
              if($user_info['defaul_address']==0){
                $data_address['defaul_address']=$re;
                //echo json_encode($where);exit;
                $where['id']=$user_id;
                m('home_user')->where($where)->save($data_address);
              }
              if(i('get.page')=='submit_cart'){
                header('Location:index.php?s=/Home/Order/submit_cart&id='.i('get.cart_id').$GLOBALS['puid_url']);exit;
                //echo '<script type="text/javascript" >  history.go(-2);</script>';exit;
              }
              header("Location:index.php?s=/Home/User/address&add_status=1".$GLOBALS['puid_url']);exit;
              //$this->success('添加地址成功');
            }else{
              header("Location:index.php?s=/Home/User/address&add_status=0".$GLOBALS['puid_url']);exit;
              //$this->error('添加地址失败');
            }
          }


          $this->display();
        }

        //删除地址
        public function delete_address(){
          $address_id=i('get.id');
          $where['user_id']=$_COOKIE['login_user_id'];
          $where['address_id']=$address_id;
          $address =m('address')->where($where)->find();
          $where_user['id'] =$_COOKIE['login_user_id'];
          $user =m('home_user')->where($where_user)->find();
          $data['address_id']=$address['address_id'];
          $data['status'] =0;
          //删除默认地址
          if($user['defaul_address']==$address['address_id']){
            $re=m('address')->where($where)->save($data);
            if($re){
            $where_address['user_id']=$_COOKIE['login_user_id'];
            $where_address['status']=1;
            $last_address =m('address')->where($where_address)->order('address_id desc')->find();
            $user_data['id'] =$_COOKIE['login_user_id'];
            $user_data['defaul_address'] =$last_address['address_id'];
            $user_data['defaul_address']=$user_data['defaul_address']?$user_data['defaul_address']:0;
            $user_re =m('home_user')->data($user_data)->save();
            echo '<script>window.location.href="index.php?s=/Home/User/address";</script>';
            }else{
              $this->error('删除失败');
            }
          }else{
            //删除收货地址
            $re=m('address')->where($where)->save($data);
            if($re){
            echo '<script>window.location.href="index.php?s=/Home/User/address";</script>';
            }else{
              $this->error('删除失败');
            }
          }

        }
        //默认地址
        public function default_address(){
          if(IS_AJAX){
            $where_address['address_id'] =i('get.address_id');
            $where_address['user_id']=$_COOKIE['login_user_id'];
            $where_address['status']=1;
            $address =m('address')->where($where_address)->find();
            $data['id'] =$_COOKIE['login_user_id'];
            $data['defaul_address'] =$address['address_id'];
            $re =m('home_user')->data($data)->save();
            if($re!==false){
              d('HomeUser')->work_success('','修改成功');
            }else{
              d('HomeUser')->work_error('修改失败');
            }
          }
        }

        //修改资料完善资料送积分
        public function edit_info(){
          if(!isset($_COOKIE['login_user_id'])){
              header('Location:index.php?s=/Home/User/login'.$GLOBALS['puid_url']);
          }else{
            $this->user_id=$_COOKIE['login_user_id'];
          }
          $time=time();
          $where['id']=$_COOKIE['login_user_id'];
          $user_info=m('home_user')->where($where)->find();
          $user_info['sign']=base64_decode($user_info['sign']);
          if($user_info['defaul_address']!=0){
            $where_address['address_id']=$user_info['defaul_address'];

            $address_info=m('address')->field('provinces,address')->where($where_address)->find();
            //dump($address_info);
            $address_info['provinces']=$address_info['provinces']?$address_info['provinces']:'';
            $address_info['address']=$address_info['address']?$address_info['address']:'';
          }
          $address_info['provinces']=$address_info['provinces']?$address_info['provinces']:'';
          $address_info['address']=$address_info['address']?$address_info['address']:'';
          $user_info['address']=$address_info['provinces'].$address_info['address'];
          $user_info['address']=$user_info['address']==""?"点击添加地址":$user_info['address'];
          $user_info['head_img']=$user_info['head_img']==""?"images/touxiang.png":$user_info['head_img'];
          if(IS_POST){
            $data_stem=i('post.');
            if($_FILES['head_img']['size']>0){
              $head_img=d('HomeUser')->upload_file('head_img');
              //dump($head_img);
              $data['head_img']=$head_img;
            }
             $data['sign']= base64_encode($data_stem['sign']) ;
             $data['user_weixin']=$data_stem['wechat'];
             $data['user_name']=$data_stem['user_name'];
             if($where['id']!=""){
               $re=m('home_user')->where($where)->save($data);
               //dump(m('home_user'));
               if($re){
                 if($user_info['perfect_score']==0){
                   //完善资料送积分start
                   //判断是否已经完善资料start
                   $user_info_edit=m('home_user')->where($where)->find();
                   if($user_info_edit['sign']!="" and $user_info_edit['user_name']!="" and $user_info_edit['user_weixin']!="" and $user_info_edit['defaul_address']!="" ){
                     $where_score['id']=2;
                     $activity_info=m('activity')->where($where_score)->find();
                     if($activity_info['active_opening']==1){
                       //完善资料送积分end
                       $data_add_score['perfect_score']=1;
                       $data_add_score['score']=$user_info['score']+$activity_info['integral'];
                       m('home_user')->where($where)->save($data_add_score);
                       //接下来还有 积分动态表
                       $data_score['user_id']=$_COOKIE['login_user_id'];
                       $data_score['is_add']=1;
                       $data_score['remark']="完善资料";
                       $data_score['score']=$activity_info['integral'];
                       $data_score['add_time']=$time;
                       $data_score['update_time']=$time;
                       m('score')->add($data_score);
                     }
                   }

                   //判断是否已经完善资料end

                   //

                 }
                 $this->success();
               }else{
                 $this->error();
               }

             }

            // $data['sign']=$data_stem['sign'];
            // dump($_FILES);
            // dump(I('post.'));
          }else{
            $this->assign('user_info',$user_info);
            $this->display();
          }

        }


        //签到送积分
        public function sign(){
          $user_id=$_COOKIE['login_user_id'];
          $time=time();
          $today=date('Ymd',$time);
          $where['id']=1;
          $sign_setting_info=m('sign_setting')->where($where)->find();
          //获取用户信息
          $where_user['id']=$user_id;
          $user_data=m('home_user')->where($where_user)->find();
          $sign_number=$user_data['running_sign'];
          $last_sign=date('Ymd',$user_data['last_sign_time']);
          $today_one=date('Ymd',$time);
          if($last_sign-$today_one==0){
            d('HomeUser')->work_error('您已经签过名');
          }
          //看看是否开启签名送积分，如果已经开启则加上相应的积分
          if($sign_setting_info['is_day']==1){

            if($user_data['first_sign']==0){
              //首次签名
              if($sign_setting_info['is_first']==1){
                //开启首次签名的话执行这里
                $data_sign['score']=$sign_setting_info['first_integral'];

              }else{
                $data_sign['score']=$sign_setting_info['day_integral'];
              }
                $data_sign['is_score']=1;
                $data_sign['add_time']=$time;
                $data_sign['user_id']=$user_id;
            }else{

                //判断是否是连续签到
                $last_time=date('Ymd',$user_data['last_sign_time']);
                $is_running=$today-$last_time;
                $user_data['running_sign']=$is_running==1?$user_data['running_sign']:0;

                if($user_data['running_sign']==0){
                  //未开启首次签名的话执行这里
                  $data_sign['score']=$sign_setting_info['day_integral'];

                }else{
                  //如果开启连续签到则执行这里
                  if($sign_setting_info['is_continuity']==1){
                    $user_data['running_sign']=$user_data['running_sign']<=$sign_setting_info['several_days']?$user_data['running_sign']:$sign_setting_info['several_days'];
                    /*//计分算法start
                    if($user_data['running_sign']==$sign_setting_info['several_days']){
                      $user_data['running_sign']=100;
                    }else{
                      $user_data['running_sign']=$user_data['running_sign']*10;
                    }
                    //计分算法end*/
                    //$data_sign['score']=$sign_setting_info['day_integral']+$user_data['running_sign'];



                    //xgg算法//
                    if($user_data['running_sign']>=$sign_setting_info['several_days']-1){
                      $data_sign['score'] =100;
                    }else{
                      $data_sign['score'] =$sign_setting_info['day_integral']+$user_data['running_sign']*10;
                    }
                    // var_dump($user_data['running_sign']);
                    // var_dump($data_sign['score']);exit;
                    //xgg算法结束//
                  }else{
                    $data_sign['score']=$sign_setting_info['day_integral'];
                  }


                }

                $data_sign['is_score']=1;
                $data_sign['add_time']=$time;
                $data_sign['user_id']=$user_id;
                $data_sign['score']=$data_sign['score']?$data_sign['score']:0;

            }
            $re_sing=m('sign')->add($data_sign);
            //$user_score_update['running_sign']=$sign_number+1;
            //$user_score_update['first_sign']=1;
            $user_score_update['score']=$user_data['score']+$data_sign['score'];
            // $user_score_update['last_sign_time']=$time;
            // $where_add_score['id']=$user_id;
            //$re_add_score=m('home_user')->where($where_add_score)->save($user_score_update);
            $re['is_day']=1;
          }else{
            $data_sign['is_score']=0;
            $data_sign['add_time']=$time;
            $data_sign['user_id']=$user_id;
            $data_sign['score']=$data_sign['score']?$data_sign['score']:0;
            $re_sing=m('sign')->add($data_sign);
            $re['is_day']=0;
          }
          // dump($sign_setting_info);
          // $data['add_time']=$time;
          // $data['user_id']=$user_id;
          // $data['is_score']=0;
          if($today_one-$last_sign>1){
            $sign_number=0;
          }
          $a=$today_one-$last_sign;
          //echo json_encode($a);exit;

          $user_score_update['last_sign_time']=$time;
          $where_add_score['id']=$user_id;
          $user_score_update['first_sign']=1;
          $user_score_update['running_sign']=$sign_number+1;
          $re_add_score=m('home_user')->where($where_add_score)->save($user_score_update);
          if($re_add_score){
            $data_score_tab['add_time']=$time;
            $data_score_tab['user_id']=$user_id;
            $data_score_tab['remark']="签到送积分";
            $data_score_tab['is_add']=1;
            $data_score_tab['update_time']=$time;
            $data_score_tab['score']=$data_sign['score']?$data_sign['score']:0;
            m('score')->add($data_score_tab);
            if($sign_setting_info['is_day']==1){
              if($sign_setting_info['is_first']==1){
                $re['add_score']=$data_sign['score'];
                $re['totai_score']=$user_score_update['score'];
                d('HomeUser')->work_success($re);
              }else{
                $re['add_score']=$data_sign['score'];
                $re['totai_score']=$user_score_update['score'];
                $re['is_day']=1;
                d('HomeUser')->work_success($re);
              }

            }else{
              $re['is_day']=0;
              d('HomeUser')->work_success($re);
            }

          }else{
            d('HomeUser')->work_error();
          }
        }

        //判断是否已经是关注公众号的用户
        public function is_concerns(){
          if(!isset($_COOKIE['login_user_id'])){
            //还未登录
            //header('Location:index.php?s=/Home/User/login');
            d('HomeUser')->work_error();
          }else{
            $user_id=$_COOKIE['login_user_id'];
            $where['is_stop']=1;
            $where['id']=$user_id;
            $user_info=m('home_user')->where($where)->find();
            //echo json_encode($user_info);exit;
            if($user_info){
              if($user_info['openid']==""){
                d('HomeUser')->work_error();
              }else{
                $appid="wxb5e50fea3e72762d";
                $appsecret="a7958a3e66f984f31330171eca956a77";
                $is_concerns=0;
                $openid_array=json_decode($user_info['openid'],true);
                foreach ($openid_array as $key => $value) {
                  if($is_concerns==1){
                    continue;exit;
                  }
                  $openid=$value;
                  $re=openid($appid,$appsecret,$openid);
                  //echo $re;
                  $re_array=json_decode($re,true);
                  if($re_array['subscribe']==1){
                    $is_concerns=1;
                  }
                }
                if($is_concerns==1){
                  d('HomeUser')->work_success();
                }else{
                  d('HomeUser')->work_error();
                }
                //echo $re;exit;
              }
            }else{
              d('HomeUser')->work_error();
            }

          }
          echo $re;exit;
        }

        //我的足迹
        public function history(){
          //cookie('history',NULL,array('expire'=>time()+3600*24*365,'prefix'=>''));
          //dump($_COOKIE);

          if(isset($_COOKIE['history'])){
            if($_COOKIE['history']!=""){
              //dump(i('cookie.history'));
              $history=json_decode($_COOKIE['history'],true);
              $data=array();
              foreach ($history as $key => $value) {
                $where['good_id']=$value['goods_id'];
                $data_temp=m('goods')->where($where)->find();
                $data[]=$data_temp;
              }

            }
          }
          $data=$data?array_reverse($data):array();
          //dump($data);
          $this->assign('data',$data);
          $this->display();
        }
        //删除历史足迹数据
        public function del_history(){

          //echo json_encode(i('get.'));
          $true=0;
          if(i('get.del_id')!=""){
            $del_id=explode(',',i('get.del_id'));
            //dump($del_id);
            $history=json_decode($_COOKIE['history'],true);
            cookie('history',NULL,array('expire'=>time()+3600*24*365,'prefix'=>''));
            $temp_history=array();
            foreach ($history as $key => $value) {

              if(!in_array($value['goods_id'],$del_id)){
                //dump($value);
                $temp_history[]=$value;
              }else{
                $true=1;
              }
            }
            //dump($temp_history);
            cookie('history',json_encode($temp_history),array('expire'=>time()+3600*24*365,'prefix'=>''));
          }
          // echo $_COOKIE['history'];

          if($true==0){
            d('HomeUser')->work_error();
          }else{
            d('HomeUser')->work_success();
          }
        }
        //猜你喜欢
        public function you_like(){
          $data=d('HomeUser')->you_like();
          dump($data);
        }
        //赚积分说明
        public function jifen(){
          $this->display();
        }

}
