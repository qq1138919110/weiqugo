<?php
namespace Home\Controller; //命名空间 定义这些文件在哪
use Think\Controller;  //加载核心控制库
class OrderController extends Controller {
  public $user_id="";
  public function _initialize(){
    //假如用户还未登陆，怎设置分享人的id，如果有用户已经登陆了，则把父puid清除掉
    if(!isset($_COOKIE['login_user_id'])){
      if(!isset($_COOKIE['puid'])){
        if(isset($_GET['puid'])){
          cookie('puid',i('get.puid'),array('expire'=>time()+3600*24*365,'prefix'=>'login_'));
        }
        
      }
      $re['status']=999;
      $re['data']="";
      $re['msg']='';
      echo json_encode($re);exit;
       header('Location:index.php?s=/Home/User/login');
    }else{
      cookie('puid',NULL,array('expire'=>time()-3600*24*365,'prefix'=>'login_'));
      $this->user_id=$_COOKIE['login_user_id'];
    }
  }
    //添加产品到购物车
    public function add_cart(){
      //dump($_GET);exit;
      //file_put_contents('puid.txt',json_encode($_GET));
      $time=time();
      $data_stem=i('post.');
      $re_data=d('Goods')->get_attr_sku($data_stem['goods_id'],$data_stem['attr']);
      //dump(d('g'));
      //dump($re_data);exit;
      
      //dump($data);
      //查找之前有没有添加过此产品and此产品的参数，如果有相同的话则加上相应的数量，否则新增一条记录 start
      $where_find['user_id']=$_COOKIE['login_user_id'];
      $where_find['goods_id']=$data_stem['goods_id'];
      $where_find['sku_id']=$re_data['item_sku_array']['sku_id'];
      $re_find_data=m('shopping_cart')->where($where_find)->order('id DESC')->find();
      if($re_find_data){
        $data_update['amount']=$data_stem['number']+$re_find_data['amount'];
        $where_update['id']=$re_find_data['id'];
        $data_update['update_time']=$time;
        $re_update=m('shopping_cart')->where($where_update)->save($data_update);
        if($re_update){
          d('HomeUser')->work_success('','添加购物车成功');
        }else{
          d('HomeUser')->work_error('添加购物车失败');
        }
      }else{
        $data['add_time']=$time;
        $data['user_id']=$_COOKIE['login_user_id'];
        $data['goods_id']=$data_stem['goods_id'];
        $data['amount']=$data_stem['number'];
        $data['sku_id']=$re_data['item_sku_array']['sku_id'];
        $data['update_time']=$time;
        $re_add=m('shopping_cart')->add($data);
        if($re_add){
          d('HomeUser')->work_success('','添加购物车成功');
        }else{
          d('HomeUser')->work_error('添加购物车失败');
        }
        
      }
      //dump($re_find_data);
      //查找之前有没有添加过此产品and此产品的参数，如果有相同的话则加上相应的数量，否则新增一条记录 end
      //$re=m('shopping_cart')->add($data);
      //dump($re);exit;
      
    }
    
    //获取购物车列表
    public function cart(){
      //dump($this->user_id);
      //dump($_COOKIE);exit;
      $user_id=$this->user_id;
      $sum_money=0;
      //dump($sum_money);
      //dump($user_id);exit;
      $where['tp_goods.is_good_attr']=1;
      $where['tp_shopping_cart.user_id']=$user_id;
      $re_data=m('shopping_cart')->field('tp_shopping_cart.goods_id,tp_shopping_cart.amount,tp_shopping_cart.sku_id,tp_shopping_cart.update_time,tp_goods.good_name,tp_goods.goods_producer,tp_goods.good_cover')->join('tp_goods ON tp_shopping_cart.goods_id=tp_goods.good_id')->where($where)->order('tp_shopping_cart.id DESC')->select();
      foreach ($re_data as $key => $value) {
        $sum_money_temp=0;
       $where_find_attr['tp_item_sku.sku_id']=$value['sku_id'];
       $attr_data=m('item_sku')->where($where_find_attr)->find();
       //dump($attr_data);
       $sum_money_temp=$attr_data['money']*$value['amount'];
       $re_data[$key]['sum_money']=$sum_money_temp;
      // dump($re_data[$key]);
       $sum_money+=$sum_money_temp;
       $re_data[$key]['money']=$attr_data['money'];
       $new_attr_val=explode(',',$attr_data['attr_symbol_path']);
       foreach ($new_attr_val as $k=> $val) {
         //dump($val);
         $where_attr_val['symbol']=$val;
         $attr_val=m('item_attr_val')->where($where_attr_val)->find();
         $re_data[$key]['attr_val'][]=$attr_val['attr_value'];
         
       }
       //dump($new_attr_key);
      }//exit;
      //dump($sum_money);
      //dump($sum_money);
      $this->assign('sum_money',$sum_money);
      $this->assign('re_data',$re_data);
      //dump($re_data);
      $this->display();
    }
    
    //删除订单
    public function del_cart(){
      $data_stem=i('get.id');
      $user_id=$_COOKIE['login_user_id'];
      $where['user_id']=$user_id;
      $where['id']=$data_stem['id'];
      if($user_id!="" and $where['id']!=""){
        $re=m('shopping_cart')->where($where)->delete();
        if($re){
          d('HomeUser')->work_success('','删除成功');
        }else{
          d('HomeUser')->error('删除失败');
        }
      }else{
         d('HomeUser')->error('删除失败');
      }
    }
    
    //提交购物车数据
    public function submit_cart(){
      //$_POST['id']="2,3,5,4";
      $data_stem=i('post.');
      $data_stem=i('get.');
      $sum_money=0;
      $sum_number=0;
      if(strpos($data_stem['id'],',')!==false){
        $where['tp_shopping_cart.id']=array('in',$data_stem['id']);
      }else{
        $where['tp_shopping_cart.id']=$data_stem['id'];
      }
      $where['tp_shopping_cart.user_id']=$this->user_id;
      //$re_data=m('shopping_cart')->where($where)->order('id DESC')->select();
      $re_data=m('shopping_cart')->field('tp_shopping_cart.goods_id,tp_shopping_cart.amount,tp_shopping_cart.sku_id,tp_shopping_cart.update_time,tp_goods.good_name,tp_goods.goods_producer,tp_goods.good_cover')->join('tp_goods ON tp_shopping_cart.goods_id=tp_goods.good_id')->where($where)->order('tp_shopping_cart.id DESC')->select();
      foreach ($re_data as $key=>$value) {
        $where_find_attr['tp_item_sku.sku_id']=$value['sku_id'];
        $attr_data=m('item_sku')->where($where_find_attr)->find();
        //dump($attr_data);exit;
        $sum_money_temp=$attr_data['money']*$value['amount'];
        $sum_number=$sum_number+$value['amount'];
        $re_data[$key]['sum_money']=$sum_money_temp;
        $sum_money=$sum_money+$sum_money_temp;
        $new_attr_val=explode(',',$attr_data['attr_symbol_path']);
       foreach ($new_attr_val as $k=> $val) {
         //dump($val);
         $where_attr_val['symbol']=$val;
         $attr_val=m('item_attr_val')->where($where_attr_val)->find();
         $re_data[$key]['attr_val'][]=$attr_val['attr_value'];
         
       }
      }
      $where_user['id']=$this->user_id;
      $user_info=m('home_user')->where($where_user)->find();
      //dump($user_info);
      if($user_info['defaul_address']!=0){
        //echo 123;exit;
        $where_address['address_id']=$user_info['defaul_address'];
        $address_info=m('address')->where($where_address)->find();
        //dump($address_info);
      }
      
      //获取地址列表start
      $where_address['user_id']=$user_info['id'];
      $address_lists=m('address')->where($where_address)->order()->select();
      
      //获取地址列表end
      //获取全部地址列表start
      $where_address_all['user_id']=$_COOKIE['login_user_id'];
          $where_address_all['user_phone']=$_COOKIE['login_user_phone'];
          $user_info=m('home_user')->where($where_address_all)->order('id DESC')->find();
          //dump($user_info);
          $where_address_all_two['user_id']=$user_info['id'];
          $address_lists=m('address')->where($where_address_all_two)->order()->select();
          $this->assign('address_lists',$address_lists);
      
      //获取全部地址列表end
      
      
      
      
      
      //dump($user_info);
      $this->assign('cart_id',$data_stem['id']);
      $this->assign('address',$address_info);
      $this->assign("sum_number",$sum_number);
      $this->assign('sum_money',$sum_money);
      $this->assign('data',$re_data);
      $this->display();
    }
    //生成订单
  public function add_order(){
    $time=time();
    $order_no=$this->user_id.$time;
    $sum_number=0;
    $sum_money=0.00;
    $time=time();
   $data_stem=i('post.');
   $data['user_id']=$this->user_id;
   
   //获取产品，判断是否开启商品属性
   //获取收货地址
   $where_address['address_id']=$data_stem['address'];
   $where_address['user_id']=$this->user_id;
   $address_info=m('address')->where($where_address)->find();
   //获取购买的产品
   if(strpos($data_stem['cart_id'],',')!==false){
     $where_cart['id']=array('in',$data_stem['cart_id']);
   }else{
     $where_cart['id']=$data_stem['cart_id'];
   }
   $where_cart['user_id']=$this->user_id;
   //$order_data=m('shopping_cart')->where($where_cart)->select();
   $re_data_cart=m('shopping_cart')->field('tp_shopping_cart.goods_id,tp_shopping_cart.amount,tp_shopping_cart.sku_id,tp_shopping_cart.update_time,tp_goods.good_name,tp_goods.goods_producer,tp_goods.good_cover')->join('tp_goods ON tp_shopping_cart.goods_id=tp_goods.good_id')->where($where_cart)->order('tp_shopping_cart.id DESC')->select();
   
    $order_detail=array();
      foreach ($re_data_cart as $key=>$value) {
        $where_find_attr['tp_item_sku.sku_id']=$value['sku_id'];
        $attr_data=m('item_sku')->where($where_find_attr)->find();
        //dump($attr_data);exit;
        $sum_money_temp=$attr_data['money']*$value['amount'];
        $sum_number=$sum_number+$value['amount'];
        $re_data_cart[$key]['sum_money']=$sum_money_temp;
        $sum_money=$sum_money+$sum_money_temp;
        $new_attr_val=explode(',',$attr_data['attr_symbol_path']);
        $order_detail[$key]['goods_attr']='';
       foreach ($new_attr_val as $k=> $val) {
         $where_attr_val['symbol']=$val;
         $attr_val=m('item_attr_val')->where($where_attr_val)->find();
         $re_data_cart[$key]['attr_val'][]=$attr_val['attr_value'];
         if($re_data_cart[$key]['goods_attr']==""){
           $re_data_cart[$key]['goods_attr']=$attr_val['attr_value'];
         }else{
           $re_data_cart[$key]['goods_attr']=$re_data_cart[$key]['goods_attr'].','.$attr_val['attr_value'];
         }
       }
       $order_detail[$key]['goods_name']=$re_data_cart[$key]['good_name'];
       $order_detail[$key]['goods_attr']=$re_data_cart[$key]['goods_attr'];
       $order_detail[$key]['goods_id']=$re_data_cart[$key]['goods_id'];
       $order_detail[$key]['sales_amount']=$re_data_cart[$key]['amount'];
       $order_detail[$key]['sales_time']=$time;
       $order_detail[$key]['sum_money']=$re_data_cart[$key]['sum_money'];
       $order_detail[$key]['order_no']=$order_no;
      }
      //echo json_encode($order_detail);exit;
      $re_temp['total_number']=$sum_number;
      $re_temp['total_price']=$sum_money;
   //echo json_encode($re_temp);exit;
   //赋值订单数据start
   $data_order['amount']=$sum_money;
   $data_order['address']=$address_info['provinces'].$address_info['address'];
   $data_order['user_id']=$this->user_id;
   $data_order['order_no']=$order_no;
   $data_order['booking_time']=$time;
   $data_order['order_status']=0;
   $re_order_add=m('order')->add($data_order);
   $re_sales_status=1;
   if($re_order_add){
     foreach ($order_detail as $key=> $value) {
       $data_detail_new=array();
       $data_detail_new=$value;
       $re_sales=m('sales')->add($data_detail_new);
       if(!$re_sales){
         $re_sales_status=0;
       }
     }
   }
   if($re_sales_status==0){
     $this->error('提交订单不成功，请联系客服');
   }else{
     $this->error('提交订单成功');
   }
   
  }
  
  //不加入购物车，直接购买产品 //先加入购物车
  public function add_order_two(){
    /*  */
    $time=time();
    $user_id=$this->user_id;
    $data['user_id']=$user_id;
    $data_stem=i('post.');
    $where_goods_find['good_id']=$data_stem['goods_id'];
    $goods_info=m('goods')->field('good_name,good_value,is_good_attr,stock_amount')->where($where_goods_find)->find();
    
    
    if($goods_info['is_good_attr']!=1){
      //当该商品没有开启属性的时候执行这里，直接使用这边的价格
      
      
    }else{
      //echo 123;exit;
      //如果商品开启了属性，则执行这里
      //先把产品添加到购物车，然后合并后面的流程start
      //$data_stem=i('post.');
      $re_data=d('Goods')->get_attr_sku($data_stem['goods_id'],$data_stem['attr']);
      //查找之前有没有添加过此产品and此产品的参数，如果有相同的话则加上相应的数量，否则新增一条记录 start
      
        $data['add_time']=$time;
        $data['user_id']=$_COOKIE['login_user_id'];
        $data['goods_id']=$data_stem['goods_id'];
        $data['amount']=$data_stem['number'];
        $data['sku_id']=$re_data['item_sku_array']['sku_id'];
        $data['update_time']=$time;
        $data['not_cart']=1;
        $re_add=m('shopping_cart')->add($data);
        //echo json_encode($re_add);exit;
        if($re_add){
          //header("Location:index.php?s=/Home/Order/submit_cart&id=".$re_add);
          $re['id']=$re_add;
          d('HomeUser')->work_success($re,'添加购物车成功');
        }else{
          d('HomeUser')->work_error('添加购物车失败');
        }
        
      
    
    //先把产品添加到购物车，然后合并后面的流程end
      
    }
    
    //echo json_encode($goods_info);exit;
      
  }
    
    
    
    
    
    
    
    
    
    
    
    
    
}