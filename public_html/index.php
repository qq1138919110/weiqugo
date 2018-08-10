<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 麦当苗儿 <zuojiazi@vip.qq.com> <http://www.zjzit.cn>
// +----------------------------------------------------------------------

if(version_compare(PHP_VERSION,'5.3.0','<'))  die('require PHP > 5.3.0 !');

/**
 * 系统调试设置
 * 项目正式部署后请设置为false
 */
define ( 'APP_DEBUG', true );

/**
 * 应用目录设置
 * 安全期间，建议安装调试完成后移动到非WEB目录
 */
if(!isset($_COOKIE['login_user_id'])){
    if(isset($_GET['puid'])){
        if(trim($_GET['puid'])!=""){
            $GLOBALS['puid_url']="&puid=". trim($_GET['puid']);
            $GLOBALS['user_pid']=trim($_GET['puid']);
        }else{
            $GLOBALS['puid_url']="";
        }   
    }else{

    }
}else{
     $GLOBALS['puid_url']="&puid=". $_COOKIE['login_user_id'];
     //$GLOBALS['user_pid']=$_COOKIE['login_user_id'];
}

define ( 'APP_PATH', './Application/' );

if(!is_file(APP_PATH . 'User/Conf/config.php')){
	header('Location: ./install.php');
	exit;
}

/**
 * 缓存目录设置
 * 此目录必须可写，建议移动到非WEB目录
 */
define ( 'RUNTIME_PATH', './Runtime/' );

/**
 * 引入核心入口
 * ThinkPHP亦可移动到WEB以外的目录
 */
require './ThinkPHP/ThinkPHP.php';