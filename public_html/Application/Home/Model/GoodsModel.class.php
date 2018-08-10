<?php
namespace Home\Model; //命名空间 定义这些文件在哪
use Think\Model\RelationModel;  //加载核心控制库
class GoodsModel extends RelationModel{

		protected $_link = array(
			'Brand' => array( //关联模型名
			'mapping_type' => self::BELONGS_TO, //关联关系
			'class_name' => 'Brand',//相当于表名                         //关联模型的配置
			'foreign_key' => 'brand_id',//关联的外键
			// 'mapping_name' =>'info', //把数据查出来封装到什么键名
			'mapping_fields'=>'name',//查询什么字段 把查询到的数据 封装到关联模型名
			'as_fields' =>'name:brand_name'//把查到的字段重命名

      ),
      'Category' => array( //关联模型名
      'mapping_type' => self::BELONGS_TO, //关联关系
      'class_name' => 'Category',//相当于表名                         //关联模型的配置
      'foreign_key' => 'category_id',//关联的外键
      // 'mapping_name' =>'info', //把数据查出来封装到什么键名
      'mapping_fields'=>'name',//查询什么字段 把查询到的数据 封装到关联模型名
      'as_fields' =>'name:cate_name'//把查到的字段重命名

      ),
      'GoodsInfo' => array( //关联模型名
      'mapping_type' => self::HAS_ONE, //关联关系
      'class_name' => 'GoodsInfo',//相当于表名                         //关联模型的配置
      'foreign_key' => 'goods_id',//关联的外键
      // 'mapping_name' =>'info', //把数据查出来封装到什么键名
      'mapping_fields'=>'content,more_img',//查询什么字段 把查询到的数据 封装到关联模型名
      'as_fields' =>'content,more_img',//把查到的字段重命名

      ),
      'GoodsAttrInfo' => array( //关联模型名
      'mapping_type' => self::HAS_MANY, //关联关系   //HAS_MANY 一对多
      'class_name' => 'GoodsAttrInfo',//相当于表名                         //关联模型的配置
      'foreign_key' => 'goods_id',//关联的外键
      // 'mapping_name' =>'info', //把数据查出来封装到什么键名
      // 'mapping_fields'=>'id,attr_id,attr_value_num,money',//查询什么字段 把查询到的数据 封装到关联模型名
      // 'as_fields' =>'id:goods_arrt_id,attr_id,attr_value_num,money'//把查到的字段重命名

      ),
    );
    public function find_one($where=''){
    	$news =$this->where($where)->find();
    	return $news;
     }
      public function find_r($relation=true,$where=''){
      $news =$this->relation($relation)->where($where)->find();
      //查询一条
      return $news;
     }
      public function select_r($relation=true,$where='',$order='',$page='1,1'){
    	$news =$this->relation($relation)->where($where)->order($order)->page($page)->select();
      //分页
    	return $news;
     }
      public function find_one_rr($where='',$order='',$page='1,1'){
    	$news =$this->where($where)->order($order)->page($page)->select();
    	return $news;
     }

      public function delete_r($relation=true,$where=''){
    	$news =$this->relation($relation)->where($where)->delete();
      //删除
    	return $news;
     }
       public function add_r($relation=true,$data=''){
    	$news =$this->relation($relation)->data($data)->add();
      //添加
    	return $news;
     }
      public function update_r($relation=true,$data=''){
      $news =$this->relation($relation)->save($data);
      //修改
      return $news;
     }


     //获取属性数据库表的相应id
     public function get_attr_sku($goods_id,$new_attr1){
       $item_sku=0;
       $item_sku_array=array();
       if($item_sku!=0){
         continue;
       }
       $new_attr=explode(',',$new_attr1);
       //dump($new_attr);
       $new_attr_number=count($new_attr);
       //dump($new_attr_number);
       $data['user_id']=$_COOKIE['login_user_id'];
       $data['goods_id']=$data_stem['goods_id'];
       //查找属性数据库表的id start
       $where_find_goods_attr['good_id']=$goods_id;
       $where_find_goods_attr['status']=1;
       //dump($where_find_goods_attr);
			//  if(IS_AJAX){
      //   d('HomeUser')->work_success($where_find_goods_attr);
      //  }
       $new_attr_data=m('item_sku')->where($where_find_goods_attr)->select();
       foreach ($new_attr_data as $key=> $value) {
         $is_in=1;
         $find_attr=explode(',',$value['attr_symbol_path']);
         if($new_attr_number==count($find_attr)){
           foreach ($new_attr as $k=> $val) {
             //作比较
             if( !in_array($val,$find_attr) ){
               $is_in=0;
             }
           }
           //得出最终的属性id结果
           if($is_in==1){
             $item_sku=$value['sku_id'];
             $item_sku_array=$value;
           }

         }else{
           //跳出此次循环
           continue;
         }
       }
       if($item_sku!=0){
        $re['item_sku']=$item_sku;
        $re['item_sku_array']=$item_sku_array;
        return $re;
       }else{
        return false;
       }
     }

     //获取不同状态的订单
     public function get_order($user_id,$page,$status=""){
      //$user_id=$this->user_id;
      $where['user_id']=$user_id;
      if($status!="" and $status!=999){
       $where['order_status']=$status;
      }
      if($status!="" and $status==998){
       //获取已完成，待评价的订单
       $where['order_status']=3;
       $where['is_comment']=0;
      }

      //$where['order_status']=0;
      //$time_valid =time()-1800;
      //$where['booking_time']=array('');
      $false_order=array();
      if($status==0){
      $order_stor="booking_time DESC";
    }elseif($status==1){
      $order_stor="pay_time DESC";
    }elseif($status==2){
      $order_stor="send_time DESC";
    }elseif($status==3){
      $order_stor="complete_time DESC";
    }elseif($status==998){
      $order_stor="booking_time DESC";
    }
      $re_order=m('order')->where($where)->order($order_stor)->page($page.',3')->select();
      //file_put_contents('order.txt',json_encode($where));
      //dump(m('order'));
      //dump($re_order);
      foreach ($re_order as $key => $value) {
        if($value['order_status']==0){
            $re_order[$key]['order_status_title']="待付款订单";
        }elseif ($value['order_status']==1) {
            $re_order[$key]['order_status_title']="待发货订单";
        }elseif($value['order_status']==2){
            $re_order[$key]['order_status_title']="待收货订单";
        }elseif($value['order_status']==3){
            $re_order[$key]['order_status_title']="已完成订单";
        }elseif($value['order_status']==4){
            $re_order[$key]['order_status_title']="已取消订单";
        }elseif($value['order_status']==5){
            $re_order[$key]['order_status_title']="售后订单";
        }
        $re_order[$key]['is_false']=1;
        $re_order[$key]['sum_number']=0;
        $where_detail=array();
        $re_detail="";
        $where_detail['order_no']=$value['order_no'];
        $re_detail=m('sales')->where($where_detail)->order('id DESC')->select();

        //$re_detail=m('sales')->where($where_detail)->order('id DESC')->select();

        $re_detail=$re_detail?$re_detail:array();
        $re_order[$key]['goods_list']=$re_detail;
        //dump($value);
        //dump(time()-$value['booking_time']);
        if(time()-$value['booking_time']>1800 and ($value['order_status']==0 or $value['order_status']==4)){
          //dump($value);
          $re_order[$key]['is_false']=0;
          //如果是过时的订单，则把相应的库存加回去
          $data_false['order_status']=4;
          $where_false['order_id']=$value['order_id'];
          //dump($where_false);exit;
          m('order')->where($where_false)->save($data_false);
          //unset($re_order[$key]);continue;
          /*if($false_order==""){
            $false_order[$key]=$value['order_id'];
          }else{
            $false_order=$false_order.','.$value['order_id'];
          }*/
        }
        foreach($re_detail as $k=>$val){
          $re_order[$key]['sum_number']+=$val['sales_amount'];
          $where_goods_temp['good_id']=$val['goods_id'];
          $temp_goods=m('goods')->field('stock_amount,good_cover')->where($where_goods_temp)->find();
          //dump($temp_goods);
          $re_order[$key]['goods_list'][$k]['img']=$temp_goods['good_cover'];
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


            $temp_goods['stock_amount']=$temp_goods['stock_amount']+$val['sales_amount'];
            m('goods')->where($where_goods_temp)->save($temp_goods);



          }


        }


      }
      return $re_order;

     }


    }
