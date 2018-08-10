<?php
// header("Content-type:text/html;charset=utf-8");
namespace Admin\Controller; //命名空间 定义这些文件在哪
use Think\Controller;  //加载核心控制库
class OrderController extends AdminController {
    //未处理页面
    public function index(){
            $count = M('Order')->where(array('order_status'=>1))->count();// 查询满足要求的总记录数
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
            $list = M('Order')->where(array('order_status'=>1))->field('order_id,order_no,user_id,shop_detail,c_remark,amount,booking_time,address,consignee_name,consignee_phone,pay_time')->order('pay_time desc')->page($p.',10')->select();
            foreach ($list as $key => $value) {
                $list[$key]['uid'] =M('Home_user')->field('user_name,user_phone')->where(array('id'=>$value['user_id']))->find();
                $list[$key]['order_detail'] =M('Sales')->where(array('order_no'=>$value['order_no']))->select();
            }
            
            $this->assign('list',$list);
            $this->assign('page',$show); //分页代码
    	    $this->display();
        }
        //已发货订单操作
        public function retermine_order(){
            if(IS_AJAX){
                $id  = I('get.order_id');
                $nid = explode(',',$id);
                $newid = array_filter($nid);
                $id  = implode(',', $newid);
                $where['order_id'] =array('in',$id);
                $data['send_time'] =time();
                $data['order_status'] =2;
                $res =M('Order')->where($where)->save($data);
                if($res!==false){
                    $this->ajaxReturn(1);
                }else{
                    $this->ajaxReturn(2);
                }
            }else{
                $this->redirect('Index/index');
            }
        }
        //取消订单操作
         public function fail_order(){
            if(IS_AJAX){
                $id  = I('get.order_id');
                $nid = explode(',',$id);
                $newid = array_filter($nid);
                $id  = implode(',', $newid);
                $where['order_id'] =array('in',$id);
                $data['order_status'] =4;
                $order =M('Order')->field('user_id,amount,order_no,order_id')->where($where)->select();
                $res =M('Order')->where($where)->save($data);
                if($res!==false){
                    foreach ($order as $k => $v) {
                        //加上相应得积分
                        $user =M('Home_user')->where(array('id'=>$v['user_id']))->find();
                        $userdata['id']      =$user['id'];
                        $userdata['score'] =$user['score'] +$v['amount'];
                        $resuser =M('Home_user')->data($userdata)->save();
                        //消费表改状态
                        $score =M('Score')->where(array('order_id'=>$v['order_id']))->find();
                        $scoredata['id']          =$score['id'];
                        $scoredata['status']      =0;
                        $scoredata['update_time'] =time();
                        $resscore =M('Score')->data($scoredata)->save();
                        $sales =M('Sales')->where(array('order_no'=>$v['order_no']))->select();
                        foreach ($sales as $key => $value) {
                            //减去相应销售量
                            $goods =M('Goods')->where(array('good_id'=>$value['goods_id']))->field('sales_amount')->find();
                            $goodsdata['good_id']=$value['goods_id'];
                            $goodsdata['sales_amount']=$goods['sales_amount'] -$value['sales_amount'];
                            $resgoods =M('Goods')->data($goodsdata)->save();
                            //加去库存
                            $goods_attr =M('Item_sku')->where(array('sku_id'=>$value['sku_id']))->find();
                            $goods_attrdata['sku_id'] =$goods_attr['sku_id'];
                            $goods_attrdata['number'] =$goods_attr['number'] +$value['sales_amount'];
                            $resgoods_attr =M('Item_sku')->data($goods_attrdata)->save();
                        }
                    }
                    $this->ajaxReturn(1);
                }else{
                    $this->ajaxReturn(2);
                }
            }else{
                $this->redirect('Index/index');
            }
        }
        //成功订单
        public function success_order(){
            if(IS_AJAX){
                $id  = I('get.order_id');
                $nid = explode(',',$id);
                $newid = array_filter($nid);
                $id  = implode(',', $newid);
                $where['order_id'] =array('in',$id);
                $data['order_status'] =3;
                $data['complete_time'] =time();
                $res =M('Order')->where($where)->save($data);
                if($res!==false){
                    $this->ajaxReturn(1);
                }else{
                    $this->ajaxReturn(2);
                }
            }else{
                $this->redirect('Index/index');
            }
        }
          //进行中页面
         public function confirmed(){
            $count = M('Order')->where(array('order_status'=>2))->count();// 查询满足要求的总记录数
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
            $list = M('Order')->where(array('order_status'=>2))->field('order_id,order_no,user_id,shop_detail,c_remark,amount,booking_time,address,consignee_name,consignee_phone,pay_time')->order('send_time desc')->page($p.',10')->select();
           foreach ($list as $key => $value) {
                $list[$key]['uid'] =M('Home_user')->field('user_name,user_phone')->where(array('id'=>$value['user_id']))->find();
                $list[$key]['order_detail'] =M('Sales')->where(array('order_no'=>$value['order_no']))->select();
            }
            $this->assign('uuuser',$uuuser);
            $this->assign('list',$list);
            $this->assign('page',$show); //分页代码
            $this->display();
        }
           //成功订单页面
    public function successful(){
            $count = M('Order')->where(array('order_status'=>3))->count();// 查询满足要求的总记录数
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
            $list = M('Order')->where(array('order_status'=>3))->field('order_id,order_no,user_id,shop_detail,c_remark,amount,booking_time,address,consignee_name,consignee_phone,pay_time')->order('complete_time desc')->page($p.',10')->select();
            foreach ($list as $key => $value) {
                $list[$key]['uid'] =M('Home_user')->field('user_name,user_phone')->where(array('id'=>$value['user_id']))->find();
                $list[$key]['order_detail'] =M('Sales')->where(array('order_no'=>$value['order_no']))->select();
            }
            $this->assign('list',$list);
            $this->assign('page',$show); //分页代码
            $this->display();
        }
               //失败订单页面
            public function fail(){
            $count = M('Order')->where(array('order_status'=>4))->count();// 查询满足要求的总记录数
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
            $list = M('Order')->where(array('order_status'=>4))->field('order_id,order_no,user_id,shop_detail,c_remark,amount,booking_time,address,consignee_name,consignee_phone,pay_time')->order('booking_time desc')->page($p.',10')->select();
            foreach ($list as $key => $value) {
                $list[$key]['uid'] =M('Home_user')->field('user_name,user_phone')->where(array('id'=>$value['user_id']))->find();
                $list[$key]['order_detail'] =M('Sales')->where(array('order_no'=>$value['order_no']))->select();
            }
            $this->assign('list',$list);
            $this->assign('page',$show); //分页代码
            $this->display();
        }
        public function search_order(){
            if(IS_AJAX){
               $order_no =trim(I('get.order_no'));
               $order_status =trim(I('get.order_status'));
               if($order_status==0){
                $order_status='';
               }
               $start_date =trim(I('get.booking_time'));
               if(!empty($start_date)){
                $start_date =strtotime($start_date);
               }
               $end_date   =trim(I('get.booking_time2'));
               if(!empty($end_date)){
                $end_date =strtotime($end_date)+86399;
               }
               $user_id =trim(I('get.user_id'));
               if($order_no==''&&$order_status==''&&$start_date==''&&$end_date==''&&$user_id==''){
                 $order =M('Order')->order('booking_time desc')->select();
                 foreach ($order as $key => $value) {
                     $order[$key]['uid'] =M('Home_user')->field('user_name,user_phone')->where(array('id'=>$value['user_id']))->find();
                     $order_detail =M('Sales')->where(array('order_no'=>$value['order_no']))->select();
                     foreach ($order_detail as $k => $val) {
                        if($val['goods_attr']){
                            $goods_attr= $val['goods_name'].'('.$val['goods_attr'].')'.'x'.$val['sales_amount'].',';
                          }else{
                            $goods_attr= $val['goods_name'].'x'.$val['sales_amount'].','; 
                          }
                     }
                     unset($order_detail);
                     $order[$key]['order_detail'] =$goods_attr;
                     $order[$key]['booking_time'] =date('Y-m-d H:i:s',$value['booking_time']);
                     $order[$key]['pay_time']     =date('Y-m-d H:i:s',$value['pay_time']);
                 }
                
                 $dd['order'] =$order;
                 $this->ajaxReturn($dd);
               }
               if($order_no!=''){
                   $where['order_no'] =array('like', '%' . $order_no . '%'); 
               }
               if($order_status!=''){
                    $where['order_status'] =$order_status;
                }
                if($start_date!=''&&$end_date!=''){
                $where['booking_time'] = array(array('EGT',$start_date),array('ELT',$end_date),'AND');
                }
                if($start_date!=''&&$end_date==''){
                $t = time();
                $end = mktime(23,59,59,date("m",$t),date("d",$t),date("Y",$t));
                $where['booking_time'] = array(array('EGT',$start_date),array('ELT',$end),'AND');   
                }
                if($start_date==''&&$end_date!=''){
                    $start_date =0;
                    $where['booking_time'] = array(array('EGT',$start_date),array('ELT',$end_date),'AND');
                }
                if($user_id!=''){
                    $where['user_id'] =$user_id;
                }
                $order =M('Order')->order('booking_time desc')->where($where)->select();
                 foreach ($order as $key => $value) {
                     $order[$key]['uid'] =M('Home_user')->field('user_name,user_phone')->where(array('id'=>$value['user_id']))->find();
                     $order_detail =M('Sales')->where(array('order_no'=>$value['order_no']))->select();
                     foreach ($order_detail as $k => $val) {
                        if($val['goods_attr']){
                            $goods_attr= $val['goods_name'].'('.$val['goods_attr'].')'.'x'.$val['sales_amount'].',';
                          }else{
                            $goods_attr= $val['goods_name'].'x'.$val['sales_amount'].','; 
                          }
                     }
                     unset($order_detail);
                     $order[$key]['order_detail'] =$goods_attr;
                     $order[$key]['booking_time'] =date('Y-m-d H:i:s',$value['booking_time']);
                     $order[$key]['pay_time']     =date('Y-m-d H:i:s',$value['pay_time']);
                 }
                $dd['order'] =$order;
                $this->ajaxReturn($dd);
            }else{
                $this->redirect('Index/index');
            }
        }

        //导出数据方法
    public function goods_export()
    {   
        $order_id  = I('post.order_id');
        $where['order_id'] =array('in',$order_id);
        $goods_list = M('Order')->where($where)->select();
        foreach ($goods_list as $key => $value) {
            $goods_list[$key]['uid'] =M('Home_user')->field('user_name,user_phone')->where(array('id'=>$value['user_id']))->find();
            $goods_list[$key]['order_detail'] =M('Sales')->where(array('order_no'=>$value['order_no']))->select();
        }
        $data = array();
            foreach ($goods_list as $k=>$goods_info){
            $data[$k][name] = $goods_info['uid']['user_name'];    
            $data[$k][order_no] = $goods_info['order_no'];
            $data[$k][booking_time] = date("Y-m-d H:i:s",$goods_info['booking_time']);
            $data[$k][pay_time] = date("Y-m-d H:i:s",$goods_info['pay_time']);
            foreach ($goods_info['order_detail'] as $key => $value) {
              if($value['goods_attr']){
                $data[$k][shop_detail]= $value['goods_name'].'('.$value['goods_attr'].')'.'x'.$value['sales_amount'].',';
                }else{
                $data[$k][shop_detail]= $value['goods_name'].'x'.$value['sales_amount'].','; 
              }
            }
            $data[$k][c_remark] = $goods_info['c_remark'];
            $data[$k][amount] = $goods_info['amount'];
            $data[$k][consignee_name] = $goods_info['consignee_name'];
            $data[$k][consignee_phone] = $goods_info['consignee_phone'];
            $data[$k][address] = $goods_info['address'];
            
            
            if($goods_info['order_status']==1){
            $data[$k][order_status] ='待发货订单';
            }else if($goods_info['order_status']==2){
            $data[$k][order_status] ='已发货订单';
            }else if($goods_info['order_status']==3){
            $data[$k][order_status] ='成功订单';  
            }else if($goods_info['order_status']==4){
            $data[$k][order_status] ='取消订单';   
            }
        }
        
        

        foreach ($data as $field=>$v){

            if($field == 'name'){
                $headArr[]='下单顾客';
            }

            if($field == 'order_no'){
                $headArr[]='订单编号';
            }

            if($field == 'booking_time'){
                $headArr[]='下单时间';
            }

            if($field == 'pay_time'){
                $headArr[]='支付时间';
            }
            if($field == 'shop_detail'){
                $headArr[]='订单详情';
            }

            if($field == 'c_remark'){
                $headArr[]='备注';
            }
            if($field == 'amount'){
                $headArr[]='总成交额';
            }
            if($field == 'consignee_name'){
                $headArr[]='收货人姓名';
            }
            if($field == 'consignee_phone'){
                $headArr[]='收货人电话';
            }
            if($field == 'address'){
                $headArr[]='收货地址';
            }
            if($field == 'order_status'){
                $headArr[]='订单状态';
            }
       
        }

        $filename="goods_list";


        $this->getExcel($filename,$headArr,$data);
    }

    private  function getExcel($fileName,$headArr,$data){
        //导入PHPExcel类库，因为PHPExcel没有用命名空间，只能inport导入
        import("Org.Util.PHPExcel");
        import("Org.Util.PHPExcel.Writer.Excel5");
        import("Org.Util.PHPExcel.IOFactory.php");

        $date = date("Y_m_d",time());
        $fileName .= "_{$date}.xls";

        //创建PHPExcel对象，注意，不能少了\
        $objPHPExcel = new \PHPExcel();
        $objProps = $objPHPExcel->getProperties();

        //设置表头
        $key = ord("A");
        //print_r($headArr);exit;
        foreach($headArr as $v){
            $colum = chr($key);
            $objPHPExcel->setActiveSheetIndex(0) ->setCellValue($colum.'1', $v);
            $objPHPExcel->setActiveSheetIndex(0) ->setCellValue($colum.'1', $v);
            $key += 1;
        }

        $column = 2;
        $objActSheet = $objPHPExcel->getActiveSheet();

        //print_r($data);exit;
        foreach($data as $key => $rows){ //行写入
            $span = ord("A");
            foreach($rows as $keyName=>$value){// 列写入
                $j = chr($span);
                $objActSheet->setCellValue($j.$column, $value);
                $span++;
            }
            $column++;
        }

        $fileName = iconv("utf-8", "gb2312", $fileName);
        //重命名表
        //$objPHPExcel->getActiveSheet()->setTitle('test');
        //设置活动单指数到第一个表,所以Excel打开这是第一个表
        $objPHPExcel->setActiveSheetIndex(0);
        header('Content-Type: application/vnd.ms-excel');
        header("Content-Disposition: attachment;filename=\"$fileName\"");
        header('Cache-Control: max-age=0');

        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save('php://output'); //文件通过浏览器下载
        exit;
    }
    


}