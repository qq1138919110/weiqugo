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
    //我的测试公众号信息

    //公司的测试公众号信息
     public $appid="wxb5e50fea3e72762d";
     public $appsecret="f44897ebbfb7349358aa7b307aa021f3";
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
        //dump ($GLOBALS['foot_active']);
        //dump($_SERVER["PATH_INFO"]);
        //判断是哪个页面的页面，用作页脚显示end
        
        //授权登录start
        if(i('get.code')){
            $this->openid();
            //file_put_contents('code1.txt',i('get.code'));
            //dump(i('get.'));
            
            return ;
        }
        if(strpos($url_info_new,"Order")!==false){
            if(!isset($_COOKIE['login_user_id'])){
                //登陆了以后操作这里，判断是否已经关注公众号
            }else{
                //没有登录操作这里，提示关注公众号
            }
            $this->code();
        }
        
        
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
            dump($data['openid']);
            $this->is_concerns($data['openid']);
            $openid=$data['openid'];
           S($code,$openid,7000); 
           s($code.'_web_ak',$data['access_token'],7000);
        }
        return;
       
    }
    
    
    //判断该用户已经关注了公众号，如果没有则弹出关注公众号的二维码
    public function is_concerns($openid=""){
        if(!isset($_COOKIE['login_user_id'])){
            header("Location:index.php?s=/Home/User/login".$GLOBALS['puid_url']);exit;
        }else{
            //查询该用户是否已经关注过公众号，如果没有关注则弹出让其关注的弹框
            $where['id']=$_COOKIE['login_user_id'];
            if($_COOKIE['login_user_id']!=""){
                   $user_info=m('home_user')->where($where)->find();
                    if($user_info['openid']!=""){
                        //如果不为空则是已经关注过
                        $GLOBALS['is_concerns']=0;
                        
                    }else{
                        $GLOBALS['is_concerns']=1;
                        //如果为空则是没有关注过,添加openid进home_user数据库表
                        $data['openid']=$openid;
                        m('home_user')->where($where)->save($data);
                        
                    } 
                }else{
                    header("Location:index.php?s=/Home/User/login".$GLOBALS['puid_url']);exit;
                }
            
        }
        return true;
    }
    
}