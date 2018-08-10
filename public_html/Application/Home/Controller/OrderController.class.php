<?php
namespace Home\Controller; //命名空间 定义这些文件在哪
use Think\Controller;  //加载核心控制库
class OrderController extends Controller {
  public $user_id="";
  public function _initialize(){
    //dump($GLOBALS['a']);
    //假如用户还未登陆，怎设置分享人的id，如果有用户已经登陆了，则把父puid清除掉
    if(!isset($_COOKIE['login_user_id']) or $_COOKIE['login_user_id']==NULL){
      if(!isset($_COOKIE['puid'])){
        if(isset($_GET['puid'])){
          cookie('puid',i('get.puid'),array('expire'=>time()+3600*24*365,'prefix'=>'login_'));
        }

      }

      if(IS_AJAX){

        $re['status']=999;
        $re['data']="";
        $re['msg']='';
        echo json_encode($re);exit;
      }else{
        header('Location:index.php?s=/Home/User/login'.$GLOBALS['puid_url']);exit;
        echo "<script>top.location='index.php?s=/Home/User/login".$GLOBALS['puid_url']."';return false;</script>";exit;
        //header('Location:index.php?s=/Home/User/login');exit;
      }

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
      //判断库存是否足够start
      if($re_data['item_sku_array']['number']<$data_stem['number']){
        d('HomeUser')->work_error('库存不足');
      }

      //判断库存是否足够end
      //dump(d('g'));
      //dump($re_data);exit;

      //dump($data);
      //查找之前有没有添加过此产品and此产品的参数，如果有相同的话则加上相应的数量，否则新增一条记录 start
      $where_find['user_id']=$_COOKIE['login_user_id'];
      $where_find['goods_id']=$data_stem['goods_id'];
      $where_find['sku_id']=$re_data['item_sku_array']['sku_id'];
      $where_find['not_cart']=0;
      $re_find_data=m('shopping_cart')->where($where_find)->order('id DESC')->find();
      if($re_find_data){
        $data_update['amount']=$data_stem['number']+$re_find_data['amount'];
        $where_update['id']=$re_find_data['id'];
        $data_update['update_time']=$time;
        //$where_update['not_cart']=0;
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
        //判断库存是否足够start

        //判断库存是否足够end
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
      //$where['tp_goods.is_good_attr']=1;
      $where['tp_shopping_cart.user_id']=$user_id;
      $where['tp_shopping_cart.not_cart']=0;
      $re_data=m('shopping_cart')->field('tp_shopping_cart.id,tp_shopping_cart.goods_id,tp_shopping_cart.amount,tp_shopping_cart.sku_id,tp_shopping_cart.update_time,tp_goods.good_name,tp_goods.goods_producer,tp_goods.good_cover')->join('tp_goods ON tp_shopping_cart.goods_id=tp_goods.good_id')->where($where)->order('tp_shopping_cart.id DESC')->select();
      //echo json_encode($re_data);exit;
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
      $you_like=d('HomeUser')->you_like();
      $this->assign('you_like',$you_like);
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
      //$data_stem=i('post.');
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
      $re_data=m('shopping_cart')->field('tp_goods.stock_amount,tp_goods.good_value,tp_goods.is_good_attr,tp_shopping_cart.goods_id,tp_shopping_cart.amount,tp_shopping_cart.sku_id,tp_shopping_cart.update_time,tp_goods.good_name,tp_goods.goods_producer,tp_goods.good_cover')->join('tp_goods ON tp_shopping_cart.goods_id=tp_goods.good_id')->where($where)->order('tp_shopping_cart.id DESC')->select();
      $where_user['id']=$this->user_id;
      $defaul_address=m('home_user')->field('defaul_address')->where($where_user)->find();
      $defaul_address=$defaul_address['defaul_address'];
      //dump($defaul_address);
      $this->assign('defaul_address',$defaul_address);

      //echo json_encode($re_data);exit;
      foreach ($re_data as $key=>$value) {
        //判断是否开启属性
        if($value['is_good_attr']!=1){
          //如果没有开启属性在这里处理

          $where_find_attr['tp_item_sku.sku_id']=$value['sku_id'];
          $attr_data=m('item_sku')->where($where_find_attr)->find();
          $re_data[$key]['sum_money']=$attr_data['money']*$value['amount'];

          //echo json_encode($re_data);exit;
          $sum_number=$sum_number+$value['amount'];
          $sum_money_temp=$attr_data['money']*$value['amount'];
          $sum_money=$sum_money+$sum_money_temp;
          //echo json_encode($sum_money_temp);
          //$re_data[$key]['sum_money']=$sum_money_temp;

        }else{
          //如果开启属性，在这里处理
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

      }
      $where_user['id']=$this->user_id;
      $user_info=m('home_user')->where($where_user)->find();
      //dump($user_info);
      if($user_info['defaul_address']!=0){
        //echo 123;exit;
        $where_address['status']=1;
        $where_address['user_id']=$this->user_id;
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
          $where_address_all_two['status']=1;
          //$where_address['user_id']=$this->user_id;
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
    $user_id=$this->user_id;
    $time=time();

    $order_no=$user_id.$time;

    //echo json_encode($order_no);exit;
    $sum_number=0;
    $sum_money=0.00;
    //$time=time();
   $data_stem=i('post.');
   //echo json_encode($data_stem);exit;
   $data['user_id']=$this->user_id;

   //获取产品，判断是否开启商品属性
   //获取收货地址
   $where_address['address_id']=$data_stem['address'];
   $where_address['user_id']=$this->user_id;
   $address_info=m('address')->where($where_address)->find();
   //获取购买的产品
   if(strpos($data_stem['cart_id'],',')!==false){
     $where['id']=array('in',$data_stem['cart_id']);
   }else{
     $where['id']=$data_stem['cart_id'];
   }

   $where['tp_shopping_cart.user_id']=$this->user_id;
   //echo json_encode($where_cart);exit;
      //$re_data=m('shopping_cart')->where($where)->order('id DESC')->select();
      $re_data=m('shopping_cart')->field('tp_goods.stock_amount,tp_goods.good_value,tp_goods.is_good_attr,tp_shopping_cart.goods_id,tp_shopping_cart.amount,tp_shopping_cart.sku_id,tp_shopping_cart.update_time,tp_goods.good_name,tp_goods.goods_producer,tp_goods.good_cover')->join('tp_goods ON tp_shopping_cart.goods_id=tp_goods.good_id')->where($where)->order('tp_shopping_cart.id DESC')->select();
      //echo json_encode($re_data);exit;
   //判断是否已经开启商品属性
   //已经开启属性的时候在这里显示
     $order_detail=array();

      foreach ($re_data as $key=>$value) {
        //判断是否开启属性
        if($value['is_good_attr']!=1){
          //如果没有开启属性在这里处理

          $where_find_attr['tp_item_sku.sku_id']=$value['sku_id'];
          $attr_data=m('item_sku')->where($where_find_attr)->find();
          $re_data[$key]['sum_money']=$attr_data['money']*$value['amount'];

          //echo json_encode($re_data);exit;
          $sum_number=$sum_number+$value['amount'];
          $sum_money_temp=$re_data[$key]['sum_money'];
          $sum_money=$sum_money+$sum_money_temp;
          //echo json_encode($sum_money_temp);
          //$re_data[$key]['sum_money']=$sum_money_temp;
          $order_detail[$key]['goods_name']=$re_data[$key]['good_name'];
          $order_detail[$key]['goods_attr']=$re_data[$key]['good_attr']?$re_data[$key]['good_attr']:"";
          $order_detail[$key]['goods_id']=$re_data[$key]['goods_id'];
          $order_detail[$key]['sales_amount']=$re_data[$key]['amount'];
          $order_detail[$key]['sales_time']=$time;
          $order_detail[$key]['values']=$attr_data['money']*$value['amount'];
          $order_detail[$key]['order_no']=$order_no;
          $order_detail[$key]['sku_id']=$value['sku_id'];
          $order_detail[$key]['img']=$value['good_cover'];
          //echo json_encode($re_data);exit;
          //判断库存是否足够start
          if($attr_data['number'] < $re_data[$key]['amount']){
            d('HomeUser')->work_error($re_data[$key]['good_name'].'库存不足');
          }
        }else{
          //如果开启属性，在这里处理
          $where_find_attr['tp_item_sku.sku_id']=$value['sku_id'];
          $attr_data=m('item_sku')->where($where_find_attr)->find();
          //dump($attr_data);exit;
          $sum_money_temp=$attr_data['money']*$value['amount'];
          $sum_number=$sum_number+$value['amount'];
          $re_data[$key]['sum_money']=$sum_money_temp;
          $sum_money=$sum_money+$sum_money_temp;
          $new_attr_val=explode(',',$attr_data['attr_symbol_path']);
          $order_detail[$key]['good_attr']='';
         foreach ($new_attr_val as $k=> $val) {
           $where_attr_val['symbol']=$val;
           $attr_val=m('item_attr_val')->where($where_attr_val)->find();
           //echo json_encode($attr_val);
           $re_data[$key]['attr_val'][]=$attr_val['attr_value'];
           if($re_data[$key]['good_attr']==""){
             $re_data[$key]['good_attr']=$attr_val['attr_value'];
           }else{
             $re_data[$key]['good_attr']=$re_data[$key]['good_attr'].','.$attr_val['attr_value'];
           }
         }
         //判断库存是否足够start
         //echo json_encode($re_data);exit;
         if($attr_data['number'] < $re_data[$key]['amount']){
           //准备去把过期的未付款订单取消掉补回库存---待修改
           d('HomeUser')->work_error($re_data[$key]['good_name'].'库存不足');
         }

         //判断库存是够足够end
         $order_detail[$key]['goods_name']=$re_data[$key]['good_name'];
         $order_detail[$key]['goods_attr']=$re_data[$key]['good_attr']?$re_data[$key]['good_attr']:"";
         $order_detail[$key]['goods_id']=$re_data[$key]['goods_id'];
         $order_detail[$key]['sales_amount']=$re_data[$key]['amount'];
         $order_detail[$key]['sales_time']=$time;
         $order_detail[$key]['values']=$re_data[$key]['sum_money'];
         $order_detail[$key]['order_no']=$order_no;
         $order_detail[$key]['sku_id']=$value['sku_id'];
         //$order_detail[key]['sku_id']=$value['sku_id'];

        }

      }
      //echo json_encode($order_detail);exit;
      //echo json_encode($order_detail);exit;
      $re_temp['total_number']=$sum_number;
      $re_temp['total_price']=$sum_money;
   //echo json_encode($re_temp);exit;
   //赋值订单数据start
   $data_order['amount']=$sum_money;
   $data_order['address']=$address_info['provinces'].$address_info['address'];
   $data_order['consignee_name']=$address_info['consignee_name'];
   $data_order['consignee_phone']=$address_info['consignee_phone'];
   $data_order['user_id']=$this->user_id;
   $data_order['order_no']=$order_no;
   $data_order['booking_time']=$time;
   $data_order['order_status']=0;
   //echo json_encode($data_order);exit;
   $re_order_add=m('order')->add($data_order);
   //echo json_encode($re_order_add);exit;
   $re_sales_status=1;
   if($re_order_add){
     //echo (is_array($re_order_add));exit;
     foreach ($order_detail as $key=> $value) {
       //echo 123;exit;
       //echo json_encode($value);exit;
       $data_detail_new=array();
       $data_detail_new=$value;
       //echo json_encode($value);exit;
       //$data_detail_new['order_no']=$data_order['order_no'];
       //echo json_encode($data_detail_new);exit;
       $re_sales=m('sales')->add($data_detail_new);
       if(!$re_sales){
         $re_sales_status=0;
       }else{
         //这里减去库存start
         $where_del_stock['sku_id']=$data_detail_new['sku_id'];
         file_put_contents('jiankucun.txt',json_encode($where_del_stock));
         $stock_data=m('item_sku')->field('number')->where($where_del_stock)->find();
         $stock_data['number']=$stock_data['number']-$data_detail_new['sales_amount'];
         m('item_sku')->where($where_del_stock)->save($stock_data);
         $where_goods_stock['good_id']=$data_detail_new['goods_id'];
         $goods_stock=m('goods')->field('stock_amount')->where($where_goods_stock)->find();
         $goods_stock['stock_amount']=$goods_stock['stock_amount']-$data_detail_new['sales_amount'];
         m('goods')->where($where_goods_stock)->save($goods_stock);
        //这里减去库存end
       }
     }
   }
   if($re_sales_status==0){
     $this->error('提交订单不成功，请联系客服');
   }else{
     if(strpos($data_stem['cart_id'],',')!==false){
       $where_del['id']=array('in',$data_stem['cart_id']);
     }else{
       $where_del['id']=$data_stem['cart_id'];
     }
     $where_del['user_id']=$this->user_id;
     m('shopping_cart')->where($where_del)->delete();
     //header("Location:index.php?s=/Home/Order/payment");exit;
     //$this->success('提交订单成功');
     d('HomeUser')->work_success($re_order_add);
   }
   //header("Location:index.php?s=/Home/Order/payment");exit;
   //header("Location:index.php?s=/Home/Selected");exit;


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

    /*$re_data=m('shopping_cart')->field('tp_shopping_cart.goods_id,tp_shopping_cart.amount,tp_shopping_cart.sku_id,tp_shopping_cart.update_time,tp_goods.good_name,tp_goods.goods_producer,tp_goods.good_cover')->join('tp_goods ON tp_shopping_cart.goods_id=tp_goods.good_id')->where($where)->order('tp_shopping_cart.id DESC')->select();*/

    if($goods_info['is_good_attr']!=1){
      //当该商品没有开启属性的时候执行这里，直接使用这边的价格
      $where_stem1['good_id']=$data_stem['goods_id'];
      $where_stem1['attr_symbol_path']="";
      $attr_data=m('item_sku')->where($where_stem1)->find();
      if($attr_data['number']<$data_stem['number']){
        d('HomeUser')->work_error('库存不足');
      }
      $data['sku_id']=$attr_data['sku_id'];
      $data['add_time']=$time;
      $data['user_id']=$_COOKIE['login_user_id'];
      $data['goods_id']=$data_stem['goods_id'];
      $data['amount']=$data_stem['number'];
      $data['update_time']=$time;
      $data['not_cart']=1;
      $re_add=m('shopping_cart')->add($data);
      if($re_add){
          //header("Location:index.php?s=/Home/Order/submit_cart&id=".$re_add);
          $re['id']=$re_add;
          d('HomeUser')->work_success($re,'添加购物车成功');
        }else{
          d('HomeUser')->work_error('添加购物车失败');
        }



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

  //获取未支付列表
  public function payment(){
    //echo 123;exit;
    $user_id=$this->user_id;
    $where['user_id']=$user_id;
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

        /*if($false_order==""){
          $false_order[$key]=$value['order_id'];
        }else{
          $false_order=$false_order.','.$value['order_id'];
        }*/
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
    //dump($false_order);exit;
    /*if($false_order!=""){
      if(strpos($false_order,',')!==false){
         $where_false['order_id']=array('in',$false_order);
       }else{
         $where_false['order_id']=$false_order;
       }
       $data_false['order_status']=4;
       //dump($where_false);exit;
       m('order')->where($where_false)->save($data_false);
       //dump(m('order'));exit;
    }*/
    //dump($re_order);
    $this->assign('data',$re_order);
    $this->display();

  }

  //支付页面
  public function pay(){
    $data_stem=i('get.');
    $user_id=$this->user_id;
    $where['order_id']=$data_stem['id'];
    $where['user_id']=$user_id;
    $re_data=m('order')->field('order_id,order_no,amount,address,consignee_name,consignee_phone,booking_time')->where($where)->order('order_id DESC')->find();
    //dump(m('order'));

    $re_data['term']=$re_data['booking_time']+1800;
    $re_data['term']=date('Y-m-d H:i:s',$re_data['term']);
    $this->assign('data',$re_data);
    //dump($re_data);
    //$this->assiign('time',);
    $this->display();
  }

  //确认支付
  public function pay_two(){
    //echo '<meta charset="utf-8">';
    $time=time();
    $user_id=$this->user_id;
    $data_stem=i('get.');
    $where['order_id']=$data_stem['id'];
    $order_info=m('order')->field('amount,order_status,order_no')->where($where)->find();
    $where_user['id']=$user_id;
    if($where_user['id']=="" or $where_user['id']==NULL){
      header('Location:index.php?s=/Home/User/login');exit;
    }
    $user_info=m('home_user')->field('score')->where($where_user)->find();

    //dump($user_info);
    //dump($order_info);
    if($order_info['order_status']==4 ){
      $this->error('订单已经失效');exit;
    }
    if($order_info['order_status']==1 ){
      $this->error('不能重复支付');exit;
    }
    if($order_info['amount']>$user_info['score'] ){
      $this->error('积分不足，无法兑换');exit;
    }
    $user_info['score']=$user_info['score']-$order_info['amount'];
    $re_pay=m('home_user')->where($where_user)->save($user_info);
    if($re_pay){
      /***********************支付成功******************/
      //更改订单状态
      $change_order['order_status']=1;
      $change_order['pay_time']=$time;
      $re_order_change=m('order')->where($where)->save($change_order);
      //更新总销量
      $where_detail['order_no']=$order_info['order_no'];
      $re_detail=m('sales')->where($where_detail)->order('id DESC')->select();
      file_put_contents('sales.txt',json_encode($re_detail));
      foreach($re_detail as $key => $value){
        $goods_info=array();
        $where_goods['good_id']=$value['goods_id'];
        $goods_info=m('goods')->field('sales_amount')->where($where_goods)->find();
        $goods_info['sales_amount']=$goods_info['sales_amount']+$value['sales_amount'];
        m('goods')->where($where_goods)->save($goods_info);
      }
      //记录积分动态
      $data_score['user_id']=$user_id;
      $data_score['update_time']=$time;
      $data_score['add_time']=$time;
      $data_score['order_id']=$data_stem['id'];
      $data_score['score']=$order_info['amount'];
      $data_score['remark']='兑换商品';
      m('score')->add($data_score);
      header("Location:index.php?s=/Home/Order/get_order&page=1&status=1".$GLOBALS['puid_url']);exit;

    }else{
      //支付失败

    }
    //$data_score['user_id']=$user_id;
    //更新总销量

    //$this->display();

  }

  //获取未发货订单
  public function get_order(){
    //dump($GLOBALS['puid_url']);
    //根据不同的状态获取不同的数据，status订单状态：0待支付，1已支付，待发货，2已发货，3已完成，4已取消，5正在退货，售后
    $user_id=$this->user_id;
    //dump($user_id);exit;
    $data_stem=i('get.');
    if($data_stem['status']==0){
      $title="待支付订单";

    }elseif($data_stem['status']==1){
      $title="待发货订单";
    }elseif($data_stem['status']==2){
      $title="待收货订单";
    }elseif($data_stem['status']==3){
      $title="已完成订单";
    }elseif($data_stem['status']==998){
      $title="待评价订单";
    }
    $this->assign('title',$title);
    //file_put_contents('text.txt',json_encode($_GET));
    $page=$data_stem['page'];
    $data=d('Goods')->get_order($user_id,$page,$data_stem['status']);
    //猜你喜欢start
    $you_like=d('HomeUser')->you_like();
    $this->assign('you_like',$you_like);
    //dump($you_like);
    //猜你喜欢end

    //dump($data);
    if(IS_AJAX){
      d('HomeUser')->work_success($data);
    }else{
      $this->assign('data',$data);
      $this->display();
    }
  }

  //增加或减少购物车数量
    public function work_cart(){
      $data_stem=i('post.');
      $where['id']=$data_stem['id'];

      if($data_stem['status']==1){
        //这里是增加库存
        $cart_info=m('shopping_cart')->field('amount')->where($where)->find();
        $cart_info['amount']=$cart_info['amount']+1;
        $re=m('shopping_cart')->where($where)->save($cart_info);
      }else if($data_stem['status']==0){
        //在这里减少库存
        $cart_info=m('shopping_cart')->field('amount')->where($where)->find();
        if($cart_info['amount']>1){
          $cart_info['amount']=$cart_info['amount']-1;
          $re=m('shopping_cart')->where($where)->save($cart_info);
        }

      }elseif ($data_stem['status']==2) {
        if($where['id']!=""){
          $re=m('shopping_cart')->where($where)->delete();
        }

      }
      if($re){
        d('HomeUser')->work_success("","操作成功");
      }else{
        d('HomeUser')->work_error("操作不成功");
      }
    }

    //确认收货
    public function complete_order(){
      $data_stem=i('get.');
      $user_id=$_COOKIE['login_user_id'];
      $where['user_id']=$user_id;
      $where['order_id']=$data_stem['order_id'];
      $data['order_status']=3;
      $data['complete_time']=time();
      $re=m('order')->where($where)->save($data);
      if($re){
        $this->success();
      }else{
        $this->error();
      }
    }

    //评论订单
    public function comment_order(){
      $time=time();
      $user_id=$_COOKIE['login_user_id'];
      $data_stem=i('get.');
      $data_stem_post=i('post.');
      if(IS_POST){
        //dump($data_stem_post);exit;
        if(isset($data_stem_post['checkbox1'])){
          $data_comment['is_anonymity']=0;
        }
        $where['tp_order.order_id']=$data_stem['order_id'];
        $where['tp_order.user_id']=$user_id;
        $order_info=m('order')->where($where)->find();
        if($order_info['order_status']==3 and $order_info['is_comment']==0){
          //获取图片地址
          $img="";
          for($i=0;$i<20;$i++){
            if(isset($data_stem_post['uploadImage_'.$i])){
              //创建文件夹start
              $path="/Uploads/Order/";
              if(!is_dir(".".$path)){
                    $re=mkdir(".".$path,0777,true);
                }
              $path="/Uploads/Order/".$order_info['order_no']."/";
              if(!is_dir(".".$path)){
                    $re=mkdir(".".$path,0777,true);
                }
              //创建文件夹end
              $img_temp=explode(';base64,',$data_stem_post['uploadImage_'.$i]);
              $suffix_array=explode('/',$img_temp[0]);
              if(end($suffix_array)=="jpeg"){
                $suffix="jpg";
              }else{
                $suffix=end($suffix_array);
              }
              if($img==""){
                file_put_contents('.'.$path.$order_info['order_no'].$i.'.'.$suffix,base64_decode(end($img_temp)));
                $img=$path.$order_info['order_no'].$i.'.'.$suffix;
              }else{
                file_put_contents('.'.$path.$order_info['order_no'].$i.'.'.$suffix,base64_decode(end($img_temp)));
                $img=$img.','.$path.$order_info['order_no'].$i.'.'.$suffix;
              }
            }
          }
          //dump($order_info);exit;
          //->join('tp_sales ON tp_order.order_no=tp_sales.order_no')
          $where_sales['order_no']=$order_info['order_no'];
          $sales_info=m('sales')->where($where_sales)->select();
          foreach ($sales_info as $key=>$value) {

            $data_comment['goods_id']=$value['goods_id'];
            $data_comment['order_id']=$order_info['order_id'];
            $data_comment['img']=$img?$img:"";
            $data_comment['add_time']=$time;
            $data_comment['user_id']=$user_id;
            $data_comment['comment']=base64_encode($data_stem_post['comment']);
            d('comment')->add($data_comment);

          }
          //处理订单表，更新为已评论状态
          $data_order_update['is_comment']=1;
          m('order')->where($where)->save($data_order_update);
          header("Location:index.php?s=/Home/Order/get_order&page=1&status=998".$GLOBALS['puid_url']);exit;

        }else{
          $this->error('请确认是否已经评论过');
        }



      }


      $this->display();
    }


     //登陆后先跳转到这里做授权然后返回
        public function mandate(){
                // $re_http='http://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'].'?'.$_SERVER['QUERY_STRING'];
                // echo $re_http;exit;
                //dump($_SERVER['QUERY_STRING']);
                $appid="wxb5e50fea3e72762d";
                $appsecret="2bbf4931160906fc5cc51bf288f55125";
                if(!isset($_GET['code'])){
                        $re_http='http://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'].'?'.$_SERVER['QUERY_STRING'];
            			 $re_http_encode=urlencode($re_http);
            		        $http="https://open.weixin.qq.com/connect/oauth2/authorize?appid=".$appid."&redirect_uri=".$re_http_encode."&response_type=code&scope=snsapi_base&state=123#wechat_redirect";
            			 header("Location:".$http);exit;
                }else{
                  $code=i('get.code');
                  $url='https://api.weixin.qq.com/sns/oauth2/access_token?appid='.$appid.'&secret='.$appsecret.'&code='.$code.'&grant_type=authorization_code';
                  $data=curl_get($url);
                  $data=json_decode($data,true);
                  //dump($data);
                  $user_id=$_COOKIE['login_user_id'];
                  //dump($user_id);exit;
                  $where['id']=$user_id;
                  $user_info=m('home_user')->where($where)->find();
                  if($user_info['openid']!=""){
                    $open_id_json=$user_info['openid'];
                    $openid_array=json_decode($user_info['openid']);
                    if(!in_array($openid_array,$data['openid'])){
                      $openid_array[]=$data['openid'];
                      $save_data['openid']=json_encode($openid_array);

                      m('home_user')->where($where)->save($save_data);
                    }
                  }else{
                    $openid_array[]=$data['openid'];
                    $save_data['openid']=json_encode($openid_array);
                    m('home_user')->where($where)->save($save_data);
                  }

                  header("Location:index.php?s=/Home/Selected/index".$GLOBALS['puid_url']);
                  exit;
                }
                //code($appid,$appsecret);
                // dump(i('get.code'));exit;
                // $user_id=$_COOKIE['login_user_id'];
                // //dump($user_id);exit;
                // $where['id']=$user_id;
                // $user_info=m('home_user')->where($where)->find();exit;
                //header("Location:index.php?s=/Home/Selected/index".$GLOBALS['puid_url']);
        }







}
