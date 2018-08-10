<?php
namespace Home\Controller; //命名空间 定义这些文件在哪
use Think\Controller;  //加载核心控制库
class SelectedController extends HomeController {
  public function _initialize()
  {
}

    //首页获取所有分类及其下面的产品
     public function index(){
      //dump($_COOKIE);exit;
      //$this->display();exit;
      //echo 123;exit;
       $cid =trim(I('get.cid'));
       if(!$cid){
        $where_bannre['page']="home";
         $banner =M('Basic_set')->field('home_banner')->where($where_bannre)->select();
         $main_push =M('Goods')->where(array('recycle'=>0,'activity_goods'=>0,'main_marketing'=>1,'good_status'=>1))->order('good_no')->field('good_name,good_value,good_cover,good_id,goods_producer')->select();
         $goods_type =M('Goods_type')->order('type_no')->select();
         foreach ($goods_type as $key => $value) {
           $goods_type[$key]['goods_list'] =M('Goods')->where(array('good_type'=>$value['type_id'],'recycle'=>0,'activity_goods'=>0,'good_status'=>1))->field('good_name,good_value,good_cover,good_id')->order('good_no ASC')->limit(8)->select();
           if(!$goods_type[$key]['goods_list']){
            //unset($goods_type[$key]);
           }
         }
        $navigation =M('Navigation')->field('navigation_name,navigation_id')->order('navigation_no')->select();
        foreach ($navigation as $k => $val) {
          $navigation[$k]['goods_list'] =M('Goods')->where(array('navigation_id'=>$val['navigation_id'],'good_status'=>1,'recycle'=>0,'activity_goods'=>0))->field('good_name,good_value,good_cover,good_id,goods_producer')->order('good_no ASC')->limit(3)->select();
          if(!$navigation[$k]['goods_list']){
            //unset($navigation[$k]);
           }
       }

       //dump($banner);
       if( isset($_REQUEST['data_type']) ){
         //if($_REQUEST['data_type']=="json"){
          $data['banner']     =$banner?$banner:array();
          $data['main_push']  =$main_push?$main_push:array();
          $data['goods_type'] =$goods_type?$goods_type:array();
          //$data['type_goods'] =$type_goods;
          $data['navigation'] =$navigation?$navigation:array();
          $data['navigation_goods'] =$navigation_goods;
         // dump($data);exit;
          // $re['status']='1';
          // $re['msg']='';
          // $re['data']=$data;
          // echo json_encode($re);exit;
          d('HomeUser')->work_success($data);

         //}
       }else{
          //dump($goods_type);
          //dump($main_push);
          $this->assign('banner',$banner);
          $this->assign('main_push',$main_push);
          $count_goods_type=count($goods_type);
          $this->assign('goods_type',$goods_type);
          $this->assign('navigation',$navigation);
          $this->display();exit;
       }





      }else{
       $goods =M('Goods')->where(array('navigation_id'=>$cid,'recycle'=>0,'activity_goods'=>0))->field('goods_producer,good_name,good_value,good_cover')->order('good_no')->select();
       $re['status']='1';
       $re['msg']='';
       $re['data']=$goods;
      }
       $this->assign('data',$data);
       $this->display();
     }

    //获取产品信息
    public function  goods_info(){
      //cookie('history',NULL,array('expire'=>time()-3600*24*365,'prefix'=>''));

     //dump('123');exit;
      $good_id =trim(I('get.good_id'));
      $goods =M('Goods')->where(array('good_id'=>$good_id,'recycle'=>0,'activity_goods'=>0,'good_status'=>1))->find();

      $goods['banner']=explode(',',$goods['good_photo']);
      //dump($goods);
      //dump(M('Goods'));exit;
      //添加cookie start
      cookie('cid',$goods['navigation_id'],array('expire'=>time()+3600*24*365,'prefix'=>'like_'));
      //setcookie("cookie_name", "abcd", time()+3600);

      if(isset($_COOKIE['history'])){
        $count_goods_data=json_decode($_COOKIE['history'],true);
        $count_goods_number=count($count_goods_data);
        //dump($count_goods_data);
        if($count_goods_number>9){
          $cookie_temp=$_COOKIE['history'];
          $cookie_temp_two=array();
          //cookie('history',NULL,array('expire'=>time()-3600*24*365));
          foreach ($count_goods_data as $key => $value) {

            if($key!=0){
              if($value['goods_id']!=$goods['good_id']){
                $cookie_temp_two[]=$value;
              }
            }
          }
          $key=count($cookie_temp_two);
          $cookie_temp_two1[$key]['goods_id']=$goods['good_id'];
          $cookie_temp_two1[$key]['time']=time();
          $cookie_temp_two1[$key]['cid']=$goods['good_type'];
          $cookie_temp_two[]=$cookie_temp_two1[$key];
          cookie('history',json_encode($cookie_temp_two),array('expire'=>time()+3600*24*365,'prefix'=>''));
        }else{
          $cookie_temp=$_COOKIE['history'];
          $cookie_temp_two=array();
          //cookie('history',NULL,array('expire'=>time()-3600*24*365));
          foreach ($count_goods_data as $key => $value) {

            if($value['goods_id']!=$goods['good_id']){
              $cookie_temp_two[]=$value;
            }
          }
          $key=count($cookie_temp_two);
          $cookie_temp_two1[$key]['goods_id']=$goods['good_id'];
          $cookie_temp_two1[$key]['time']=time();
          $cookie_temp_two1[$key]['cid']=$goods['good_type'];
          $cookie_temp_two[]=$cookie_temp_two1[$key];
          cookie('history',json_encode($cookie_temp_two),array('expire'=>time()+3600*24*365,'prefix'=>''));
          /*$count_goods_data[$count_goods_number]['goods_id']=$goods['good_id'];
          $count_goods_data[$count_goods_number]['time']=time();
          $count_goods_data[$count_goods_number]['cid']=$goods['good_type'];
          cookie('history',json_encode($count_goods_data),array('expire'=>time()+3600*24*365,'prefix'=>''));*/
        }
      }else{
        $temp[0]['goods_id']=$goods['good_id'];
        $temp[0]['time']=time();
        $temp[0]['cid']=$goods['good_type'];
        $data_cookie=json_encode($temp);
        cookie('history',$data_cookie,array('expire'=>time()+3600*24*365,'prefix'=>''));
      }

      // $count_goods_number=$count_goods_number+1;
      //
      // $_COOKIE['history'][$count_goods_number]['goods_id']=$goods['good_id'];
      // $_COOKIE['history'][$count_goods_number]['time']=time();
      //添加cookie end
      if($goods['is_good_attr']==1){
       //开启属性处理这里
        $where_goods_attr['good_id']=$goods['good_id'];
        $where_goods_attr['status']=1;
        $sum_number=m('item_sku')->where($where_goods_attr)->sum(number);
        $goods['stock_amount']=$sum_number;
        $attr_array=m('item_sku')->where($where_goods_attr)->select();
        //
        $new_attr=array();
        $where_attr_val='';
        foreach ($attr_array as $key=> $value) {
         //dump($value);
         $new_explode=array();
         $new_explode=explode(',',$value['attr_symbol_path']);
         foreach($new_explode as $k=>$val){
          if($val!=""){
           $new_attr[]=$val;

          }

         }

        }
        $new_attr=array_flip(array_flip($new_attr));
        foreach($new_attr as $k=>$val){
         if($where_attr_val==""){
            $where_attr_val=$val;
           }else{
            $where_attr_val=$where_attr_val.','.$val;
           }
        }
        $where_attr_key['tp_item_attr_val.symbol']=array('in',$where_attr_val);

        $arrt_val_data=m('item_attr_val')->where($where_attr_key)->join('tp_item_attr_key ON tp_item_attr_val.attr_key_id=tp_item_attr_key.attr_key_id')->select();
        $arrt_key=array();
        $attr_number=m('item_attr_val')->field('distinct attr_key_id')->where($where_attr_key)->select();
        $goods['attr_number']=count($attr_number);

        //$arrt_key_str='';
        //所有属性
       //dump($arrt_val_data);//exit;
        foreach($arrt_val_data as $key=>$val){
         $array_key[]=$val['attr_key_id'];
        }
        //dump($array_key);
        $array_key=array_flip(array_flip($array_key));
        //dump($array_key);exit;
        //dump($arrt_key);


        $new_data_attr=array_flip(array_flip($new_attr));
        $new_attr_data=array();
        //dump($new_data_attr);exit;
        foreach ($array_key as $key=> $value1) {
         //dump($value1);exit;
          foreach ($arrt_val_data as $k=> $val) {
            if($val['attr_key_id']==$value1){
             //$new_attr_data[$val['attr_name']][]=$val;
             $new_attr_data[$key]['attr_name1']=$val['attr_name'];
             $new_attr_data[$key]['list'][]=$val;

            }
          }
        }

        //dump($new_attr_data);
        $this->assign('attr_data',$new_attr_data);//exit;
        $goods['true_price']=$goods['good_value'].'积分&nbsp;&nbsp;&nbsp;库存剩余'.$goods['stock_amount'];
        //dump($new_data_attr);
      }else{
       //没有开启属性处理这里
       $where_goods_attr['good_id']=$goods['good_id'];
        $where_goods_attr['status']=1;
        $sum_number=m('item_sku')->where($where_goods_attr)->sum(number);
        $goods['stock_amount']=$sum_number;
        $attr_array=m('item_sku')->where($where_goods_attr)->order('sku_id DESC')->find();
        $goods['true_price']=$attr_array['money'].'积分&nbsp;&nbsp;&nbsp;库存剩余'.$attr_array['number'];
      }
      //获取QQ客服，信息 stock_amount
        $where_qq['id']=1;
        $qq=m('seting')->field('qq')->where($where_qq)->find();
        //dump($qq);
        //获取第一页评论
        $comment_data=$this->comment($good_id,1);
        $this->assign('qq',$qq['qq']);
        //dump($comment_data);
        $this->assign('comment_data',$comment_data);

      if(IS_AJAX){
       d('HomeUser')->work_success($goods);
      }
      //dump($goods);
      $this->assign('data',$goods);
      //dump($_COOKIE);
      //$this->assign('data');
      $this->display();exit;
      $re['status']='1';
      $re['msg']='';
      $re['data']=$goods;
      echo json_encode($re);exit;
      //$this->display();
    }
    //退出登录
    public function loginout(){
    //session(null);
     unset($_SESSION['home_username']);
     unset($_SESSION['user_id']);
    // var_dump(session('?username'));exit;
    if (session('?user_id') ==false){
    $this->redirect('Login/login');
     }
  }

  //分类页面
  public function type_detail(){
     $navigation=d('HomeUser')->get_navigation();
     //dump($navigation);
     $data_stem=i();
     if(isset($data_stem['navigation_id'])){
      $where['navigation_id']=$data_stem['navigation_id'];
     }


     if(!isset($data_stem['navigation_id'])){
      $where['navigation_id']=$navigation[0]['navigation_id'];
      $_GET['navigation_id']=$navigation[0]['navigation_id'];
     }
     $where['recycle']=0;
     //搜索内容
     if(isset($data_stem['search'])){
       $where['good_name']=array("like","%".$data_stem['search']."%");
       unset($where['navigation_id']);
       //dump($where);exit;
     }
     //dump($where);exit;
     $goods_lists=M('Goods')->field('good_id,good_name,good_value,good_cover,goods_producer')->where($where)->order('good_no ASC')->select();
     //dump($goods_lists);
     //dump(M('Goods'));exit;
     $this->assign('goods_lists',$goods_lists);
     //dump($navigation);
     $this->assign('navigation',$navigation);
     $this->display();
     //echo 123123;exit;
  }
  //获取产品评论
  public function comment($goods_id="",$page=""){
   //dump($page);
   $data_stem=i('get.');
   if($goods_id!=""){
    $where['tp_comment.goods_id']=$goods_id;
   }else{
    $where['tp_comment.goods_id']=$data_stem['goods_id'];
   }
   //dump($page);
   if($page==""){

    $page=1;

   }else{
    $page=$page;
   }
   //$page=$$data_stem['page'];
   $where['tp_comment.is_display']=1;
   //$order_lists=m()->where()->select();
   //dump($page);
   $data=m('comment')->field('tp_comment.is_anonymity,tp_comment.order_id,tp_comment.add_time,tp_home_user.head_img,tp_home_user.user_name,tp_comment.comment,tp_comment.img')->where($where)->join('tp_home_user ON tp_home_user.id=tp_comment.user_id')->page($page.',2')->select();
   //dump(m('comment'));exit;
   foreach ($data as $key=>$value) {
    if($value['is_anonymity']==0){
      $data[$key]['user_name']="匿名";
    }
    $data[$key]['comment']=base64_decode($value['comment']);
     $data[$key]['add_time']=date('Y-m-d',$value['add_time']);
     $data[$key]['img']=explode(',',$value['img']);
     $where_order['order_id']=$value['order_id'];
     $order_add_time=m('order')->field('booking_time')->where($where_order)->find();
     //dump(m('order'));
     //dump($order_add_time);
     $data[$key]['booking_time']=date('Y-m-d',$order_add_time['booking_time']);
     //dump($data[$key]['booking_time']);
     //$data

   }
   //$data=m('comment')->where($where)->page($page.',15')->select();
   //dump($data);
   //dump(m('comment'));
   return $data;
  }
  //获取评论，调用接口
  public function get_comment(){
   $data_stem=i('get.');
   //dump($data_stem);
   $data=$this->comment($data_stem['goods_id'],$data_stem['page']);
   d('HomeUser')->work_success($data);
  }
  //增加或减少购物车列表的数据
  public function operate_cart(){
   $data_stem=i('post.');
   $where['id']=$data_stem['id'];
   $data['amount']=$data_stem['number'];
   $re=m('shopping_cart')->where($where)->save($data);
   if($re){
    d('HomeUser')->work_success();
   }else{
    d('HomeUser')->work_error();
   }

  }
  //根据属性值获取库存与积分
  public function get_stock(){
    $data_stem=i('get.');
    $re_data=d('Goods')->get_attr_sku($data_stem['goods_id'],$data_stem['attr']);
   d('HomeUser')->work_success($re_data);

  }

  //获取不同分类的列表
  public function type_detail_two(){
   $data_stem=i('get.');
   $where['good_type']=$data_stem['type_id'];
   $where['recycle']=0;
   $where['activity_goods']=0;
   $where['good_status']=1;
   //$where['good_no']
   $goods_type =M('Goods_type')->order('type_no')->select();
   $data=m('goods')->where($where)->order('good_no ASC')->select();
   //dump($data);
   //dump($goods_type);
   $this->assign('goods_type',$goods_type);
   $this->assign('data',$data);
   $this->display();
  }



}
