<?php
// header("Content-type:text/html;charset=utf-8");
namespace Admin\Controller; //命名空间 定义这些文件在哪
use Think\Controller;  //加载核心控制库
class DailyController extends AdminController {
    //每日签到
  public function Sign(){
    if(IS_AJAX){
    $data['id']                 =1;        
    $data['is_day']             =trim(I('post.is_day'));
    $data['day_integral']       =trim(I('post.day_integral'));
    $data['is_first']           =trim(I('post.is_first'));
    $data['first_integral']     =trim(I('post.first_integral'));
    $data['is_continuity']      =trim(I('post.is_continuity'));
    $data['several_days']       =trim(I('post.several_days'));
    $data['rule']               =trim(I('post.rule'));

    $one =M('Sign_setting')->where(array('id'=>1))->find();
    if($one){
        $res =M('Sign_setting')->data($data)->save();
        if($res!==false){
            $this->ajaxReturn(1);
        }else{
            $this->ajaxReturn(2);
        }
    }
    $res =M('Sign_setting')->data($data)->add();
    if($res){
        $this->ajaxReturn(1);
    }else{
        $this->ajaxReturn(2);
    }
    }else{
        $one =M('Sign_setting')->where(array('id'=>1))->find();
        $this->assign('one',$one);
        $this->display();
    }
  }
  //完善资料
  public function perfect_information(){
    if(IS_AJAX){
    $data['id']             =2;        
    $data['active_opening'] =trim(I('post.active_opening'));
    $data['integral']       =trim(I('post.integral'));
    $data['remark']         ='完善资料';
    $two =M('Activity')->where(array('id'=>2))->find();
    if($two){
        $res =M('Activity')->data($data)->save();
        if($res!==false){
            $this->ajaxReturn(1);
        }else{
            $this->ajaxReturn(2);
        }
    }
    $res =M('Activity')->data($data)->add();
    if($res){
        $this->ajaxReturn(1);
    }else{
        $this->ajaxReturn(2);
    }
    }else{
        $two =M('Activity')->where(array('id'=>2))->find();
        $this->assign('two',$two);
        $this->display();
    }
  }
    //产品评价
  public function product_evaluation(){
    if(IS_AJAX){
    $data['id']             =6;        
    $data['active_opening'] =trim(I('post.active_opening'));
    $data['integral']       =trim(I('post.integral'));
    $data['remark']         ='产品评价';
    $six =M('Activity')->where(array('id'=>6))->find();
    if($six){
        $res =M('Activity')->data($data)->save();
        if($res!==false){
            $this->ajaxReturn(1);
        }else{
            $this->ajaxReturn(2);
        }
    }
    $res =M('Activity')->data($data)->add();
    if($res){
        $this->ajaxReturn(1);
    }else{
        $this->ajaxReturn(2);
    }
    }else{
        $six =M('Activity')->where(array('id'=>6))->find();
        $this->assign('six',$six);
        $this->display();
    }
  }

      //邀约任务
  public function invitation_task(){
    if(IS_AJAX){
    $data['id']             =7;        
    $data['active_opening'] =trim(I('post.active_opening'));
    $data['integral']       =trim(I('post.integral'));
    $data['remark']         ='邀约任务';
    $seven =M('Activity')->where(array('id'=>7))->find();
    if($seven){
        $res =M('Activity')->data($data)->save();
        if($res!==false){
            $this->ajaxReturn(1);
        }else{
            $this->ajaxReturn(2);
        }
    }
    $res =M('Activity')->data($data)->add();
    if($res){
        $this->ajaxReturn(1);
    }else{
        $this->ajaxReturn(2);
    }
    }else{
        $seven =M('Activity')->where(array('id'=>7))->find();
        $this->assign('seven',$seven);
        $this->display();
    }
  }

        //在微取GO(待定)停留30分钟
  public function stop(){
    if(IS_AJAX){
    $data['id']             =8;        
    $data['active_opening'] =trim(I('post.active_opening'));
    $data['integral']       =trim(I('post.integral'));
    $eight =M('Activity')->where(array('id'=>8))->find();
    if($eight){
        $res =M('Activity')->data($data)->save();
        if($res!==false){
            $this->ajaxReturn(1);
        }else{
            $this->ajaxReturn(2);
        }
    }
    $res =M('Activity')->data($data)->add();
    if($res){
        $this->ajaxReturn(1);
    }else{
        $this->ajaxReturn(2);
    }
    }else{
        $eight =M('Activity')->where(array('id'=>8))->find();
        $this->assign('eight',$eight);
        $this->display();
    }
  }

  //成功兑换一款试用装
  public function successful_trial(){
    if(IS_AJAX){
    $data['id']             =9;        
    $data['active_opening'] =trim(I('post.active_opening'));
    $data['integral']       =trim(I('post.integral'));
    $data['remark']         ='成功兑换一款试用装';
    $nine =M('Activity')->where(array('id'=>9))->find();
    if($nine){
        $res =M('Activity')->data($data)->save();
        if($res!==false){
            $this->ajaxReturn(1);
        }else{
            $this->ajaxReturn(2);
        }
    }
    $res =M('Activity')->data($data)->add();
    if($res){
        $this->ajaxReturn(1);
    }else{
        $this->ajaxReturn(2);
    }
    }else{
        $nine =M('Activity')->where(array('id'=>9))->find();
        $this->assign('nine',$nine);
        $this->display();
    }
  }

    //成功兑换一款正品
    public function success_genuine(){
    if(IS_AJAX){
    $data['id']             =10;        
    $data['active_opening'] =trim(I('post.active_opening'));
    $data['integral']       =trim(I('post.integral'));
    $data['remark']         ='成功兑换一款正品';
    $ten =M('Activity')->where(array('id'=>10))->find();
    if($ten){
        $res =M('Activity')->data($data)->save();
        if($res!==false){
            $this->ajaxReturn(1);
        }else{
            $this->ajaxReturn(2);
        }
    }
    $res =M('Activity')->data($data)->add();
    if($res){
        $this->ajaxReturn(1);
    }else{
        $this->ajaxReturn(2);
    }
    }else{
        $ten =M('Activity')->where(array('id'=>10))->find();
        $this->assign('ten',$ten);
        $this->display();
    }
  }

  //活动banner
  public function activity_banner(){
        if(IS_POST){
          $old_img =M('Basic_set')->order('id')->field('home_banner')->where(array('page'=>'goods'))->select();
          $img =array();
          foreach ($old_img as $key => $value) {
            $img[$key] =$value['home_banner'];
          }
          $more_img = I('post.more_img');
          $diff = array_diff($more_img,$img);
            if(!empty($diff)){
              foreach ($diff as $key => $value) {
                $data['home_banner'] =$value;
                $data['page'] ='goods';
                $res =M('Basic_set')->data($data)->add();
              }
            }
          $this->ajaxReturn(1);
        }
        $set =M('Basic_set')->order('id')->where(array('page'=>'goods'))->select();
        $this->assign('set',$set);
        $this->display();
     }
     //注册送积分
      public function registration_integral(){
    if(IS_AJAX){
    $data['id']             =15;        
    $data['active_opening'] =trim(I('post.active_opening'));
    $data['integral']       =trim(I('post.integral'));
    $data['remark']         ='注册送积分';
    $fifteen =M('Activity')->where(array('id'=>15))->find();
    if($fifteen){
        $res =M('Activity')->data($data)->save();
        if($res!==false){
            $this->ajaxReturn(1);
        }else{
            $this->ajaxReturn(2);
        }
    }
    $res =M('Activity')->data($data)->add();
    if($res){
        $this->ajaxReturn(1);
    }else{
        $this->ajaxReturn(2);
    }
    }else{
        $fifteen =M('Activity')->where(array('id'=>15))->find();
        $this->assign('fifteen',$fifteen);
        $this->display();
    }
  }

  //每日抽奖
  public function daily_draw(){
    if(IS_AJAX){
       $data['is_lucky_draw'] =trim(I('post.is_lucky_draw'));
       $data['lottery_number'] =trim(I('post.lottery_number'));
       if(is_numeric($data['lottery_number'])=== false){
        $data['lottery_number'] =0;      
       }
       $data['grand_prize'] =trim(I('post.grand_prize'));
       $data['grand_probability'] =trim(I('post.grand_probability'));
       if(is_numeric($data['grand_probability'])=== false){
        $data['grand_probability'] =0;      
       }
       $data['large_prize'] =trim(I('post.large_prize'));
       if(is_numeric($data['large_prize'])=== false){
        $data['large_prize'] =0;      
       }
       $data['large_probability'] =trim(I('post.large_probability'));
       if(is_numeric($data['large_probability'])=== false){
        $data['large_probability'] =0;      
       }
       $data['first_prize'] =trim(I('post.first_prize'));
       $data['first_probability'] =trim(I('post.first_probability'));
       if(is_numeric($data['first_probability'])=== false){
        $data['first_probability'] =0;      
       }
       $data['two_prize'] =trim(I('post.two_prize'));
       $data['two_probability'] =trim(I('post.two_probability'));
       if(is_numeric($data['two_probability'])=== false){
        $data['two_probability'] =0;      
       }
       $data['three_prize'] =trim(I('post.three_prize'));
       $data['three_probability'] =trim(I('post.three_probability'));
       if(is_numeric($data['three_probability'])=== false){
        $data['three_probability'] =0;      
       }
       $data['not_prize'] =trim(I('post.not_prize'));
       if(is_numeric($data['not_prize'])=== false){
        $data['not_prize'] =0;      
       }
       $data['not_probability'] =trim(I('post.not_probability'));
       if(is_numeric($data['not_probability'])=== false){
        $data['not_probability'] =0;      
       }
       $data['activity_introduction'] =trim(I('post.activity_introduction'));
       $data['share_title'] =trim(I('post.share_title'));
       $data['share_description'] =trim(I('post.share_description'));
       $more_img = I('post.more_img');
       $data['share_pictures'] =implode(',', $more_img);
       $data['id'] =1;
       $one =M('Draw_set')->where(array('id'=>1))->find();
        if($one){
            $res =M('Draw_set')->data($data)->save();
            if($res!==false){
                $this->ajaxReturn(1);
            }else{
                $this->ajaxReturn(2);
            }
        }
        $res =M('Draw_set')->data($data)->add();
        if($res){
            $this->ajaxReturn(1);
        }else{
            $this->ajaxReturn(2);
        }

    }
    $this->display();
  }
            //抢换商品
             public function commodity(){
            $count = M('Goods')->where(array('activity_goods'=>1,'recycle'=>0))->count();// 查询满足要求的总记录数
            $Page = new \Think\Page($count,10);// 实例化分页类 传入总记录数和每页显示的记录数
            $Page->setConfig('header','');
            $Page->setConfig('prev','上一页');
            $Page->setConfig('next','下一页');
            $Page->setConfig('first','第一页');
            $Page->setConfig('last','最后一页');
            $Page->lastSuffix = false;
            $Page->setConfig('theme','%HEADER% %FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END%');
            $show = $Page->show();// 分页显示输出
            $p =$_GET['p']?$_GET['p']:1;
            $list = M('Goods')->where(array('activity_goods'=>1,'recycle'=>0))->order('good_no')->page($p.',10')->select();
            $this->assign('limited',$limited);
            $this->assign('goods_type',$goods_type);
            $this->assign('list',$list);
            $this->assign('page',$show); //分页代码s
            $this->assign('navigation_cid',$navigation_cid);
            $this->display();
           }
           public function commodity_add(){
            if(IS_AJAX){
              $data['good_no'] =trim(I('post.good_no'));
              if(empty($data['good_no'])){
                $data['good_no'] =0;
              }
              if(is_numeric($data['good_no'])=== false){
                $this->ajaxReturn(4);
              }
              $data['activity_goods'] =1;
              $data['good_name'] =trim(I('post.good_name'));
              $data['good_value'] =trim(I('post.good_value'));
              $data['original_price'] =trim(I('post.original_price'));
              $data['unit'] =trim(I('post.unit'));
              $data['goods_producer'] =trim(I('post.goods_producer'));
              $data['start_time']   =strtotime(trim(I('post.start_time')));
              $data['end_time']     =strtotime(trim(I('post.end_time')));
              if($data['start_time'] >$data['end_time']){
                $this->ajaxReturn(3);
              }
              $data['is_good_attr'] =trim(I('post.is_good_attr'));
              $data['good_status'] =trim(I('post.good_status'));
              $data['parameter'] =trim($_POST['parameter']);
              $data['discribe'] =trim($_POST['discribe']);
              $more_img = I('post.more_img');
              $data['good_photo'] =implode(',', $more_img);
              $data['good_photo']=$data['good_photo']?$data['good_photo']:"";
              $photo =I('post.good_cover');
              if($photo!=''){
              $photo_url =explode(',', $photo[0]);
              $suffix      =explode('/', $photo_url[0]);
              $imgsuffix   =explode(';', $suffix[1]);
              $data['good_cover'] .='/Uploads/img/'.time().'.'.$imgsuffix[0];
              $dataphoto =time().'.'.$imgsuffix[0];
              file_put_contents('./Uploads/img/'.$dataphoto,base64_decode($photo_url[1]));
              }
              $goodsres =M('Goods')->data($data)->add();
              if($goodsres){
                $dada['good_id'] =$goodsres;
                $dada['money']   =$data['good_value'];
                $dada['number']  =0;
                $res =M('Item_sku')->data($dada)->add();
                $this->ajaxReturn(1);
              }else{
                $this->ajaxReturn(2);
              }
            }
              $this->display();
            
          }
          public function commodity_update(){
            if(IS_AJAX){
              $data['good_id'] =trim(I('post.good_id'));
              if(empty($data['good_id'])){
               $this->ajaxReturn(1);
              }
              $goods =M('Goods')->where(array('good_id'=>$data['good_id'],'recycle'=>0))->find();
              if(!$goods){
                $this->ajaxReturn(1);
              }
              $data['good_no'] =trim(I('post.good_no'));
              if(empty($data['good_no'])){
                $data['good_no'] =0;
              }
              if(is_numeric($data['good_no'])=== false){
                $this->ajaxReturn(4);
              }
              $data['activity_goods'] =1;
              $data['good_name'] =trim(I('post.good_name'));
              $data['good_value'] =trim(I('post.good_value'));
              $data['original_price'] =trim(I('post.original_price'));
              $data['unit'] =trim(I('post.unit'));
              $data['goods_producer'] =trim(I('post.goods_producer'));
              $data['start_time']   =strtotime(trim(I('post.start_time')));
              $data['end_time']     =strtotime(trim(I('post.end_time')));
              if($data['start_time'] >$data['end_time']){
                $this->ajaxReturn(5);
              }
              $data['is_good_attr'] =trim(I('post.is_good_attr'));
              $data['good_status'] =trim(I('post.good_status'));
              $data['parameter'] =trim($_POST['parameter']);
              $data['discribe'] =trim($_POST['discribe']);
              $more_img = I('post.more_img');
              $data['good_photo'] =implode(',', $more_img);
              $data['good_photo']=$data['good_photo']?$data['good_photo']:"";
              $photo =I('post.good_cover');
              if($photo!=''){
              $photo_url =explode(',', $photo[0]);
              $suffix      =explode('/', $photo_url[0]);
              $imgsuffix   =explode(';', $suffix[1]);
              $data['good_cover'] .='/Uploads/img/'.time().'.'.$imgsuffix[0];
              $dataphoto =time().'.'.$imgsuffix[0];
              file_put_contents('./Uploads/img/'.$dataphoto,base64_decode($photo_url[1]));
              }
              $goodsres =M('Goods')->data($data)->save();
              if($goodsres!==false){
                $this->ajaxReturn(2);
              }else{
                $this->ajaxReturn(3);
              }
            }
              $good_id =trim(I('get.good_id'));
              if(empty($good_id)){
                $this->error('非法操作');
              }
              $good = M('Goods')->where(array('good_id'=>$good_id,'recycle'=>0))->find();
              if(!$good){
                $this->error('非法操作');
              }
              $this->assign('good',$good);
              $this->display();
           
          }
  public function choujiang_ajax(){
    if(IS_AJAX){
        $prize_arr = array(
            '0' => array('id' => 1, 'min' => 1, 'max' => 29, 'prize' => '一等奖', 'v' => 1),
            '1' => array('id' => 2, 'min' => 302, 'max' => 328, 'prize' => '二等奖', 'v' => 2),
            '2' => array('id' => 3, 'min' => 242, 'max' => 268, 'prize' => '三等奖', 'v' => 5),
            '3' => array('id' => 4, 'min' => 182, 'max' => 208, 'prize' => '四等奖', 'v' => 7),
            '4' => array('id' => 5, 'min' => 122, 'max' => 148, 'prize' => '五等奖', 'v' => 10),
            '5' => array('id' => 6, 'min' => 62, 'max' => 88, 'prize' => '六等奖', 'v' => 25),
            '6' => array('id' => 7, 'min' => array(32, 92, 152, 212, 272, 332),
                'max' => array(58, 118, 178, 238, 298, 358), 'prize' => '七等奖', 'v' => 50)
            //min数组表示每个个奖项对应的最小角度 max表示最大角度
            //prize表示奖项内容，v表示中奖几率(若数组中七个奖项的v的总和为100，如果v的值为1，则代表中奖几率为1%，依此类推)
         );
        foreach ($prize_arr as $v) {
            $arr[$v['id']] = $v['v'];
        }

        $prize_id = $this->getRand($arr); //根据概率获取奖项id 
        $res = $prize_arr[$prize_id - 1]; //中奖项 
        $min = $res['min'];
        $max = $res['max'];
        if ($res['id'] == 7) { //七等奖 
            $i = mt_rand(0, 5);
            $data['angle'] = mt_rand($min[$i], $max[$i]);
        } else {
            $data['angle'] = mt_rand($min, $max); //随机生成一个角度 
        }
        $data['prize'] = $res['prize'];

        echo json_encode($data);

    }
  }

        function getRand($proArr) {

            $data = '';
            $proSum = array_sum($proArr); //概率数组的总概率精度 
            foreach ($proArr as $k => $v) { //概率数组循环
                $randNum = mt_rand(1, $proSum);
                if ($randNum <= $v) {
                    $data = $k;
                    break;
                } else {
                    $proSum -= $v;
                }
            }
            unset($proArr);

            return $data;
        }
}