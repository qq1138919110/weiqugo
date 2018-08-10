<?php
namespace Home\Model; //命名空间 定义这些文件在哪
use Think\Model\RelationModel;  //加载核心控制库
class HomeUserModel extends RelationModel{

		protected $_link = array(
			'HomeUserInfo' => array( //关联模型名
			'mapping_type' => self::HAS_ONE, //关联关系
			'class_name' => 'HomeUserInfo',//相当于表名                         //关联模型的配置
			'foreign_key' => 'home_user_id',//关联的外键
			'mapping_name' =>'info', //把数据查出来封装到什么键名
			// 'mapping_fields'=>'content',//查询什么字段 把查询到的数据 封装到关联模型名
			// 'as_fields' =>'content:content'//把查到的字段重命名

      ),
    );
     //操作成功返回的数据
	 public function work_success($data="",$msg=""){
		 $re['status']="1";
		 if($msg==""){
			 $re['msg']="ok";
		 }else{
			 $re['msg']=$msg;
		 }
		 $re['data']=$data;
		 echo json_encode($re);exit;
	 }

  	 //操作失败返回的数据
  	 public function work_error($msg="",$status=""){
  		 if($status==""){
  			 $re['status']="0";
  		 }else{
  			 $re['status']=$status;
  		 }
  		 if($msg==""){
  			 $re['msg']="error";
  		 }else{
  			 $re['msg']=$msg;
  		 }
  		 $re['data']="";
  		 echo json_encode($re);exit;

  	 }
  	 //时间转换成刚刚或者是几个小时之前又或者是几天前
  	 public function conduct_time($time){
  		 if(floor((time()-$time)/3600)<24){
   			$re_time=floor((time()-$time)/3600)."小时前";
   			if((int)$re_time < 1 ){
   				$re_time="刚刚";
   			}
   		}else{
   			$re_time=floor((time()-$time)/86400)."天前";
   		}
  		//dump($re_time);
  		return $re_time;
  	 }

  	 //获取导航栏
  	 public function get_navigation(){
  	  $navigation =M('Navigation')->field('navigation_name,navigation_id')->order('navigation_no')->select();
        //dump($navigation);
       return $navigation;
  	 }
  	 //文件上传
  	 public function upload_file($file_name){

  	 	if(isset($_FILES[$file_name])){
          $upload = new \Think\Upload();// 实例化上传类
          $upload->maxSize   =     3145728 ;// 设置附件上传大小
          $upload->exts      =     array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
          $upload->rootPath  =    "./Uploads/"; // 设置附件上传根目录
          //$upload->savePath  ="page/".$page."/"; // 设置附件上传（子）目录
          //$upload->savePath  = $user_id."/"; // 设置附件上传（子）目录
          // 上传文件
          $info   =   $upload->uploadOne($_FILES[$file_name]);
          $upload->rootPath  =    "/Uploads/"; // 设置附件上传根目录
          //dump($upload);exit;
          if($info) {
            // 上传错误提示错误信息
            $data_stem['head_img']=$upload->rootPath.$info['savepath'].$info['savename'];
          }

        }
        return $data_stem['head_img'];
  	 }

		 //猜你喜欢
		 public function you_like(){
			 //dump($_COOKIE);
			 if(isset($_COOKIE['like_cid'])){
				 $where['navigation_id']=i('cookie.like_cid');
				 $where['good_status']=1;
				 $where['recycle']=0;
				 $data=m('goods')->where($where)->order('good_id DESC')->limit('10')->select();
				 //dump($data);
				 //dump(m('goods'));
				 $number=count($data)>3?4:count($data);
				 $key_temp=array_rand($data,$number);
				 //dump($key);
				 $re_data=array();
				 foreach ($key_temp as $key => $value) {
				 	$re_data[]=$data[$value];
				 }
				 return $re_data;
			 }else{

				 $where['good_status']=1;
				 $where['recycle']=0;
				 $data=m('goods')->where($where)->order('good_id DESC')->limit('10')->select();
				 //dump($data);
				 //dump(m('goods'));
				 $number=count($data)>3?4:count($data);
				 $key_temp=array_rand($data,$number);
				 //dump($key);
				 $re_data=array();
				 foreach ($key_temp as $key => $value) {
				 	$re_data[]=$data[$value];
				 }
				 return $re_data;
			 }
		 }


    }
