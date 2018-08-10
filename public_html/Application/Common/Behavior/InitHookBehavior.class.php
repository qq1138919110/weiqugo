<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2013 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------
namespace Common\Behavior;
use Think\Behavior;
use Think\Hook;
defined('THINK_PATH') or exit();

// 初始化钩子信息
class InitHookBehavior extends Behavior {

    //公司的测试公众号信息
     public $appid="wxb5e50fea3e72762d";
     //public $appsecret="f44897ebbfb7349358aa7b307aa021f3";//2bbf4931160906fc5cc51bf288f55125
     public $appsecret="a7958a3e66f984f31330171eca956a77";
    // 行为扩展的执行入口必须是run
    public function run(&$content){
        //dump($GLOBALS['is_concerns']);
        //$GLOBALS['a']="test";
        //判断是哪个页面的页面，用作页脚显示start
        //dump($_SERVER);
        //dump(CONTROLLER_NAME);
        //$url_info=$_SERVER['QUERY_STRING'];
        $url_info=explode('&',$_SERVER['QUERY_STRING']);
        $url_info_new=end(explode('s=/Home/',$url_info[0]));
        //dump($url_info_new) ;
        if(strpos($url_info_new,"Order/")!==false or strpos($url_info_new,"User/")!==false  ){
            $GLOBALS['foot_active']=4;
        }
        if($url_info_new=="Order/cart"  or $url_info_new=="Order/submit_cart" ){
            $GLOBALS['foot_active']=3;
        }
        if(strpos($url_info_new,"Selected/")!==false){
            $GLOBALS['foot_active']=2;
        }
        /*if($url_info_new=="Order/get_order" or ){
            $GLOBALS['foot_active']=2;
        }*/
        if($url_info_new=="Selected"  ){
            $GLOBALS['foot_active']=1;
        }
        //!!!!!!xggg开始//////
    if(!file_exists("../expiration_time.txt")){
                            file_put_contents("../expiration_time.txt",time());
    }
    $expiration =file_get_contents("../expiration_time.txt");
    $expiration_time =$expiration+1800;
    //file_put_contents('22222222.txt',$expiration_time);
    if($expiration_time>=time()){

    $where['order_status']=0;
    //$time_valid =time()-1800;
    //$where['booking_time']=array('');
    $false_order=array();
    $re_order=m('order')->where($where)->order('booking_time DESC')->select();
    //dump($re_order);
    foreach ($re_order as $key => $value) {
      $re_order[$key]['is_false']=1;
      $re_order[$key]['sum_number']=0;
      $where_detail=array();
      $re_detail="";
      $where_detail['order_no']=$value['order_no'];
      $re_detail=m('sales')->where($where_detail)->order('id DESC')->select();

      $re_detail=$re_detail?$re_detail:array();
      $re_order[$key]['goods_list']=$re_detail;
      if(time()-$value['booking_time']>1800){
        $re_order[$key]['is_false']=0;
        //如果是过时的订单，则把相应的库存加回去
        $data_false['order_status']=4;
        $where_false['order_id']=$value['order_id'];
       //dump($where_false);exit;
       m('order')->where($where_false)->save($data_false);
       //重新保存时间
        file_put_contents("../expiration_time.txt",time());
        if($false_order==""){
          $false_order[$key]=$value['order_id'];
        }else{
          $false_order=$false_order.','.$value['order_id'];
        }
      }
      foreach($re_detail as $k=>$val){
        $re_order[$key]['sum_number']+=$val['sales_amount'];
        //如果是过时的订单，则把相应的库存加回去
        if($re_order[$key]['is_false']==0){
          $temp_stock="";
          $temp_goods=array();
          $temp_number=0;
          $temp_goods_stock=0;
          $where_add_number['sku_id']=$val['sku_id'];
          $temp_stock=m('item_sku')->field('number')->where($where_add_number)->find();
          $temp_number=$temp_stock['number']+$val['sales_amount'];
          $temp_stock['number']=$temp_number;
          m('item_sku')->field('number')->where($where_add_number)->save($temp_stock);
          $where_goods_temp['good_id']=$val['goods_id'];
          $temp_goods=m('goods')->field('stock_amount')->where($where_goods_temp)->find();
          $temp_goods['stock_amount']=$temp_goods['stock_amount']+$val['sales_amount'];
          m('goods')->where($where_goods_temp)->save($temp_goods);


        }


      }


    }
} 
        //!!!!!!xggg结束//////
        //dump ($GLOBALS['foot_active']);
        //dump($_SERVER["PATH_INFO"]);
        //判断是哪个页面的页面，用作页脚显示end
        //授权登录start
        if(i('get.code') and $url_info_new!="User/is_concerns" and strpos($url_info[0],"Home/Order/mandate")===false ){
          //file_put_contents('code1.txt',json_encode(i('get.code')));
            $this->openid();

            //dump(i('get.'));//

            return ;
        }
        //if((strpos($url_info[0],"Home/Order/")!==false and $url_info_new!="Order/submit_cart" and $url_info_new!="Order/add_order"  and $url_info_new!="Order/add_order_two" and strpos($url_info_new,"Order/add_cart/")===false and $url_info_new!="User/is_concerns") or $url_info_new=="Selected" ){
        if(strpos($url_info[0],"Home/Order/")!==false and $url_info_new!="Order/submit_cart" and $url_info_new!="Order/add_order"  and $url_info_new!="Order/add_order_two" and strpos($url_info_new,"Order/add_cart/")===false and $url_info_new!="User/is_concerns" and $url_info_new!="Order/mandate" and $url_info_new!="Order/complete_order" and $url_info_new!="Order/get_order" and strpos($url_info_new,"Order/add_cart")===false ){
            if(!isset($_COOKIE['login_user_id'])){
                //登陆了以后操作这里，判断是否已经关注公众号
            }else{
                //没有登录操作这里，提示关注公众号
            }
            //dump($url_info[0]);exit;
            //$re=
            //echo json_encode($_SERVER['QUERY_STRING']);exit;
            $this->code();
        }
        // if(strpos($url_info[0],"Home/Order/")!==false and $url_info_new!="Order/submit_cart" and $url_info_new!="Order/add_order"  and $url_info_new!="Order/add_order_two" and strpos($url_info_new,"Order/add_cart")===false and $url_info_new!="User/is_concerns" and $url_info_new!="Order/mandate" and $url_info_new!="Order/complete_order" and $url_info_new!="Order/get_order" ){
        //     if(!isset($_COOKIE['login_user_id'])){
        //         //登陆了以后操作这里，判断是否已经关注公众号
        //     }else{
        //         //没有登录操作这里，提示关注公众号
        //     }
        //     $this->code();
        // }


        //授权登陆end




        if(isset($_GET['m']) && $_GET['m'] === 'Install') return;

        $data = S('hooks');
        if(!$data){
            $hooks = M('Hooks')->getField('name,addons');
            foreach ($hooks as $key => $value) {
                if($value){
                    $map['status']  =   1;
                    $names          =   explode(',',$value);
                    $map['name']    =   array('IN',$names);
                    $data = M('Addons')->where($map)->getField('id,name');
                    if($data){
                        $addons = array_intersect($names, $data);
                        Hook::add($key,$addons);
                    }
                }
            }
            S('hooks',Hook::get());
        }else{
            Hook::import($data,false);
        }
    }
    //获取code
    public function code(){
        //$re_http="http://weiqugou.php-study.com/index.php?s=/Home/Selected";
        $re_http='http://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'].'?'.$_SERVER['QUERY_STRING'];;
        $re_http_encode=urlencode($re_http);
        $http="https://open.weixin.qq.com/connect/oauth2/authorize?appid=".$this->appid."&redirect_uri=".$re_http_encode."&response_type=code&scope=snsapi_base&state=123#wechat_redirect";
        //file_put_contents('code.txt',$http);exit;
        header("Location:".$http);
        //echo $http;
        //$re_data=curl_get($http);
        //file_put_contents('code.txt',$re_data);exit;
        //dump($re_data);exit;
    }

    //获取openid
    public function openid(){
       //获取 web ak
       $code=i('get.code');
        $url='https://api.weixin.qq.com/sns/oauth2/access_token?appid='.$this->appid.'&secret='.$this->appsecret.'&code='.$code.'&grant_type=authorization_code';
        $data=curl_get($url);
        //file_put_contents("o.txt",$data);
        $data=json_decode($data,true);

        //dump($data);//exit;
        $openid='';
        if(!isset($data['openid'])){
            //检查缓存
            $temp=s($code);
            if($temp!=""){
                $openid=$temp;
            }else{
                $this->response_error('1','获取失败');
            }

        }else{
            //dump($data['openid']);
            $GLOBALS['token']=$this->get_token();
            $user_info_wechat=curl_get('https://api.weixin.qq.com/cgi-bin/user/info?access_token='.$GLOBALS['token'].'&openid='.$data['openid'].'&lang=zh_CN');
            //dump($data['openid']);
            //dump($user_info_wechat);exit;
            $this->is_concerns($user_info_wechat,$data['openid']);
            $openid=$data['openid'];
           S($code,$openid,7000);
           s($code.'_web_ak',$data['access_token'],7000);
        }
        return;

    }


    //判断该用户已经关注了公众号，如果没有则弹出关注公众号的二维码
    public function is_concerns($user_info_wechat,$openid=""){
        $user_info_wechat_array=json_decode($user_info_wechat,true);
        //dump($user_info_wechat_array);
        if(!isset($_COOKIE['login_user_id'])){
            header("Location:index.php?s=/Home/User/login".$GLOBALS['puid_url']);exit;
        }else{
            //查询该用户是否已经登录，如果没有关注则弹出让其关注的弹框
            $where['id']=$_COOKIE['login_user_id'];
            if($_COOKIE['login_user_id']!=""){
                   $user_info=m('home_user')->where($where)->find();
                    /*if($user_info['openid']!=""){
                        //如果不为空则是已经关注过
                        $GLOBALS['is_concerns']=0;

                    }else{
                        $GLOBALS['is_concerns']=1;
                        //如果为空则是没有关注过,添加openid进home_user数据库表
                        $data['openid']=$openid;
                        m('home_user')->where($where)->save($data);

                    }*/

                    if($user_info['openid']!=""){
                      //file_put_contents('../user_info.txt',json_encode($user_info));
                      $open_id_json=$user_info['openid'];
                      $openid_array=json_decode($user_info['openid']);
                      if(!in_array($openid_array,$data['openid'])){
                        if($data['openid']){
                          $openid_array[]=$data['openid'];
                          $save_data['openid']=json_encode($openid_array);

                          m('home_user')->where($where)->save($save_data);
                        }

                      }
                    }else{
                      //file_put_contents('../user_info1.txt',json_encode($user_info));
                      if($data['openid']){
                        $openid_array[]=$data['openid'];
                        $save_data['openid']=json_encode($openid_array);

                        m('home_user')->where($where)->save($save_data);
                      }

                    }
                    //判断该用户是否已经关注start
                    //file_put_contents('../subscribe.txt',json_encode($user_info_wechat_array));
                    if($user_info_wechat_array['subscribe']==0){
                            //该用户未关注公众号
                            header("Location:./code.jpg");exit;
                    }
                    //判断该用户是否已经关注end
                }else{
                    header("Location:index.php?s=/Home/User/login".$GLOBALS['puid_url']);exit;
                }

        }
        return true;
    }

    //获取基础access_token
    public function get_token(){
         if(!file_exists("../access_token_notweb.txt")){
                            file_put_contents("../access_token_notweb.txt",i('get.'));
                 }
        $shuzu=array('');
        $put_contents="";
        file_put_contents('../11111.txt',json_encode($this->appsecret));
        //$url='https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid='.$this->appid.'&secret='.$this->appsecret.'';
        $url='https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid='.$this->appid.'&secret='.$this->appsecret;
        //file_put_contents('../url.txt',$url);
        $access_token_json=file_get_contents("../access_token_notweb.txt");
        if($access_token_json!=""){
          $json_token=json_decode($access_token_json,true);
        }
        if($access_token_json!="" and (isset($json_token['access_token']))){
                $json_token=json_decode($access_token_json,true);
                if(isset($json_token['time'])){
                        if(time()-$json_token['time']>7100 ){
                                        $jieguo=curl_get($url);
                                        if($jieguo){
                                                $shuzu=json_decode($jieguo,TRUE);
                                                $shuzu['time']=time();
                                                $put_contents=json_encode($shuzu);
                                                file_put_contents("../access_token_notweb.txt",$put_contents);
                                        }
                        }else{
                                $json_token=json_decode($access_token_json,true);
                                $shuzu=$json_token;
                        }
                }else{
                             $jieguo=curl_get($url);
                             if($jieguo){
                                    $shuzu=json_decode($jieguo,TRUE);
                                    $shuzu['time']=time();
                                    $put_contents=json_encode($shuzu);
                                    file_put_contents("../access_token_notweb.txt",$put_contents);
                              }
                }
        }else{
                $jieguo=curl_get($url);
                             if($jieguo){
                                    $shuzu=json_decode($jieguo,TRUE);
                                    $shuzu['time']=time();
                                    $put_contents=json_encode($shuzu);
                                    file_put_contents("../access_token_notweb.txt",$put_contents);
                              }
        }

        //var_dump($jieguo);exit;
        file_put_contents("../ccc.txt",json_encode($shuzu));
        $token=$shuzu['access_token'];
        return $token;
}


}
