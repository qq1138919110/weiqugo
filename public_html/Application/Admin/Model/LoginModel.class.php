<?php
namespace Admin\Model; //命名空间 定义这些文件在哪
use Think\Model\RelationModel;  //加载核心控制库
class LoginModel extends RelationModel{

		protected $_link = array(
			'Daili' => array( //关联模型名
			'mapping_type' => self::HAS_MANY, //关联关系
			'class_name' => 'Daili',//相当于表名                         //关联模型的配置
			'foreign_key' => 'pid',//关联的外键
			// 'mapping_name' =>'info', //把数据查出来封装到什么键名
			'mapping_fields'=>'name',//查询什么字段 把查询到的数据 封装到关联模型名
			'as_fields' =>'name:name'//把查到的字段重命名

      ),
    );
    public function find_one($where=''){
    	$news =$this->where($where)->find();
    	return $news;
     }

      public function find_one_r($relation=true,$where='',$order='',$page='1,1'){
    	$news =$this->relation($relation)->where($where)->order($order)->page($page)->select();
    	return $news;
     }
      public function find_one_rr($where='',$order='',$page='1,1'){
    	$news =$this->where($where)->order($order)->page($page)->select();
    	return $news;
     }

      public function delete_r($relation=true,$where=''){
    	$news =$this->relation($relation)->where($where)->delete();
    	return $news;
     }
       public function add_r($relation=true,$data=''){
    	$news =$this->relation($relation)->data($data)->add();
    	return $news;
     }
      public function update_r($relation=true,$data=''){
      $news =$this->relation($relation)->save($data);
      return $news;
     }
    }