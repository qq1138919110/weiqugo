<?php
// header("Content-type:text/html;charset=utf-8");
namespace Admin\Controller; //命名空间 定义这些文件在哪
use Think\Controller;  //加载核心控制库
class IndexController extends AdminController {
    public function index(){
     
    		//查找数据，输出视图
           
    		$this->display();
    	
        }
     public function basic_set(){
        if(IS_POST){
            $data['id']                 =1;
            $more_img                   = I('post.more_img');
            $data['qq']       =implode(',', $more_img);
            $set =M('Seting')->where(array('id'=>1))->find();
            if($set){
              $ress =M('Seting')->data($data)->save();
              if($ress!==false){
                $this->ajaxReturn(1);
              }else{
                $this->ajaxReturn(2);
              }
            }else{
                $res =M('Seting')->data($data)->add();
                if($res){
                    $this->ajaxReturn(1);
                }else{
                    $this->ajaxReturn(2);
                }
            }

        }
        $set =M('Seting')->where(array('id'=>1))->find();
        $this->assign('set',$set);
        $this->display();
     }
     public function binner_set(){
        if(IS_AJAX){
          $old =I('post.');
          foreach ($old['more_img'] as $key => $value) {
            $data['id'] =$old['id'][$key];
            $data['home_banner'] =$value;
            $data['page'] ='home';
            $data['link'] =$old['link'][$key];
            $data['number'] =$old['number'][$key];
            if($data['number']==''){
              $data['number']=0;
            }
            if(is_numeric($data['number'])=== false){
              $data['number']=0;
            }
            if(!empty($data['id'])){
             $res =M('Basic_set')->data($data)->save();
            }else{
              unset($data['id']);
              $res =M('Basic_set')->data($data)->add();
            }
          }
          
          // $old_img =M('Basic_set')->order('id')->field('home_banner')->where(array('page'=>'home'))->select();
          // $img =array();
          // foreach ($old_img as $key => $value) {
          //   $img[$key] =$value['home_banner'];
          // }
          // $more_img = I('post.more_img');
          // $diff = array_diff($more_img,$img);
          //   if(!empty($diff)){
          //     foreach ($diff as $key => $value) {
          //       $data['home_banner'] =$value;
          //       $data['page'] ='home';
          //       $res =M('Basic_set')->data($data)->add();
          //     }
          //   }
          $this->ajaxReturn(1);
        }
        $set =M('Basic_set')->order('id')->where(array('page'=>'home'))->select();
        $this->assign('set',$set);
        $this->display();
     }
     public function delete_img(){
      if(IS_AJAX){
        $id =trim(I('post.id'));
        $delete_image =M('Basic_set')->order('id')->where(array('id'=>$id))->delete();
        if($delete_image!==false){
          $this->ajaxReturn(2);
        }else{
          $this->ajaxReturn(3);
        }
      }
     }
     public function administrators(){
        $uid =session('uid');
        $user =M('User')->select();
        $this->assign('user',$user);
        $this->assign('uid',$uid);
        $this->display();
     }
     public function judge(){
        if(IS_AJAX){
        if(session('uid')==1){
            $this->ajaxReturn(1);
        }else{
            $this->ajaxReturn(2);
        }

        }else{
            $this->redirect('Index/index');
        }
     }
     public function administrators_add(){
        if(IS_AJAX){
            $data['username'] =trim(I('post.username'));
            $user =M('User')->where(array('username'=>$data['username']))->find();
            if($user){
                $this->ajaxReturn(1);//账号已存在
            }
            $data['phone'] =trim(I('post.phone'));
            $user_phone =M('User')->where(array('phone'=>$data['phone']))->find();
            if($user_phone){
                $this->ajaxReturn(2);//手机号已存在
            }
            $data['password'] =md5_pwd(trim(I('post.password')));
            $data['create_time'] =time();
            $res =M('User')->data($data)->add();
            if($res){
                $this->ajaxReturn(3);
            }else{
                $this->ajaxReturn(4);
            }
        }
        if(session('uid')!=1){
            $this->error('非法操作');
        }
        $this->display();
     }
     public function administrators_update(){
        if(IS_AJAX){
            $id =trim(I('post.uid'));
            if(empty($id)){
                $this->ajaxReturn(1);
            }
            $oldpassword =trim(I('post.oldpassword'));
            if(empty($oldpassword)){
                $this->ajaxReturn(1);
            }
            $password =trim(I('post.password'));
            if(empty($password)){
                $this->ajaxReturn(1);
            }
            $uid =M('User')->where(array('id'=>$id))->find();
            if(!$uid){
                $this->ajaxReturn(1);
            }
            $phone =trim(I('post.phone'));
            if(empty($phone)){
                $this->ajaxReturn(1);
            }
            $uidphone =M('User')->where(array('phone'=>$phone))->find();
            if($uidphone){
                 if($uidphone['id']!=$id){
                   $this->ajaxReturn(2);//账号已存在
                }
            }
            if(session('uid')==1){
            $data['id']     =$id;
            $data['phone']  =$phone;
            $data['password'] =md5_pwd(trim(I('post.password')));
            $res =M('User')->data($data)->save();
            if($res!==false){
                $this->ajaxReturn(4);//修改成功
            }else{
                $this->ajaxReturn(5);
            }
            }else{
             $password =md5_pwd(trim(I('post.oldpassword')));
             $user=M('User')->where(array('id'=>$id,'password'=>$password))->find();
             if(!$user){
                $this->ajaxReturn(3);//密码错误
             }
             $data['id']        =$id;
             $data['password']  =md5_pwd(trim(I('post.password')));
             $data['phone']  =$phone;
             $res =M('User')->data($data)->save();
             if($res!==false){
                $this->ajaxReturn(4);//修改成功
            }else{
                $this->ajaxReturn(5);
            }
            }
        }
        $uid =trim(I('get.uid'));
        if(empty($uid)){
            $this->error('非法操作');
        }
        $user =M('User')->where(array('id'=>$uid))->find();
        if(!$user){
            $this->error('非法操作');
        }
        if($uid!=session('uid')){
            if(session('uid')!=1){
                $this->error('非法操作');
            }
        }
        $this->assign('user',$user);
        $this->display();
     }
     public function delete_administrators(){
        if(IS_AJAX){
            $uid =session('uid');
            if($uid!=1){
                $this->ajaxReturn(1);
            }
            $id =trim(I('post.id'));
            if(empty($id)){
                $this->ajaxReturn(2);
            }
            $user =M('User')->where(array('id'=>$id))->find();
            if(!$user){
                $this->ajaxReturn(2);
            }
            $delete_user =M('User')->where(array('id'=>$id))->delete();
            if($delete_user!==false){
                $this->ajaxReturn(3);
            }else{
                $this->ajaxReturn(4);
            }
        }
     }
     public function chartrevenue(){
        $user =M('Home_user')->order('create_time desc')->select();
        foreach ($user as $key => $value) {
           $count1[$value['id']] =M('Order')->where(array('user_id'=>$value['id'],'order_status'=>1))->count();
           $count2[$value['id']] =M('Order')->where(array('user_id'=>$value['id'],'order_status'=>2))->count();
           $count3[$value['id']] =M('Order')->where(array('user_id'=>$value['id'],'order_status'=>3))->count();
           $count4[$value['id']] =M('Order')->where(array('user_id'=>$value['id'],'order_status'=>4))->count();
           $moneycount[$value['id']]=M('Chongzhi')->where(array('user_id'=>$value['id'],'status'=>1))->sum('price');

        }
        $this->assign('user',$user);
        $this->assign('count1',$count1);
        $this->assign('count2',$count2);
        $this->assign('count3',$count3);
        $this->assign('count4',$count4);
        $this->assign('moneycount',$moneycount);
        $this->display();
     }
     //搜索下单客户
     public function single_customer(){
        if(IS_AJAX){
        $user_id =trim(I('get.user_id'));
        $phone =trim(I('get.phone'));
        if($user_id==''&&$phone==''){
          $user =M('Home_user')->order('create_time desc')->select();
           foreach ($user as $key => $value) {
           $count1[$value['id']] =M('Order')->where(array('user_id'=>$value['id'],'order_status'=>1))->count();
           $count2[$value['id']] =M('Order')->where(array('user_id'=>$value['id'],'order_status'=>2))->count();
           $count3[$value['id']] =M('Order')->where(array('user_id'=>$value['id'],'order_status'=>3))->count();
           $count4[$value['id']] =M('Order')->where(array('user_id'=>$value['id'],'order_status'=>4))->count();
           $moneycount[$value['id']]=M('Chongzhi')->where(array('user_id'=>$value['id'],'status'=>1))->sum('price');

        }
        }else{
        if($user_id!=''){
            $where['id'] =$user_id;
        }
        if($phone!=''){
            $where['phone'] =array('like', '%' . $phone . '%'); 
        }
        $user =M('Home_user')->where($where)->order('create_time desc')->select();
        if($user){
            foreach ($user as $key => $value) {
           $count1[$value['id']] =M('Order')->where(array('user_id'=>$value['id'],'order_status'=>1))->count();
           $count2[$value['id']] =M('Order')->where(array('user_id'=>$value['id'],'order_status'=>2))->count();
           $count3[$value['id']] =M('Order')->where(array('user_id'=>$value['id'],'order_status'=>3))->count();
           $count4[$value['id']] =M('Order')->where(array('user_id'=>$value['id'],'order_status'=>4))->count();
           $moneycount[$value['id']]=M('Chongzhi')->where(array('user_id'=>$value['id'],'status'=>1))->sum('price');
            }
        }else{
          $user =M('Home_user')->order('create_time desc')->select();
           foreach ($user as $key => $value) {
           $count1[$value['id']] =M('Order')->where(array('user_id'=>$value['id'],'order_status'=>1))->count();
           $count2[$value['id']] =M('Order')->where(array('user_id'=>$value['id'],'order_status'=>2))->count();
           $count3[$value['id']] =M('Order')->where(array('user_id'=>$value['id'],'order_status'=>3))->count();
           $count4[$value['id']] =M('Order')->where(array('user_id'=>$value['id'],'order_status'=>4))->count();
           $moneycount[$value['id']]=M('Chongzhi')->where(array('user_id'=>$value['id'],'status'=>1))->sum('price');

        }
        }
        }
        $dd['user']   =$user;
        $dd['count1'] =$count1;
        $dd['count2'] =$count2;
        $dd['count3'] =$count3;
        $dd['count4'] =$count4;
        $dd['moneycount'] =$moneycount;
        $this->ajaxReturn($dd);     
        }else{
            $this->redirect('Index/index');
        }
     }
        //导出数据方法
    public function users_export()
    {   

        $id  = I('post.id');
        $where['id'] =array('in',$id);
        $goods_list = M('Home_user')->where($where)->select();
        foreach ($goods_list as $key => $value) {
           $count1[$value['id']] =M('Order')->where(array('user_id'=>$value['id'],'order_status'=>1))->count();
           $count2[$value['id']] =M('Order')->where(array('user_id'=>$value['id'],'order_status'=>2))->count();
           $count3[$value['id']] =M('Order')->where(array('user_id'=>$value['id'],'order_status'=>3))->count();
           $count4[$value['id']] =M('Order')->where(array('user_id'=>$value['id'],'order_status'=>4))->count();
           $moneycount[$value['id']]=M('Chongzhi')->where(array('user_id'=>$value['id'],'status'=>1))->sum('price');
            }
        $data = array();
            foreach ($goods_list as $k=>$goods_info){
            $data[$k][id] = $goods_info['id'];
            $data[$k][name] = $goods_info['name'];
            $data[$k][phone] = $goods_info['phone'];
            $count11 =$count1[$goods_info['id']];
            $count22 =$count2[$goods_info['id']];
            $count33 =$count3[$goods_info['id']];
            $count44 =$count4[$goods_info['id']];
            $money =$moneycount[$goods_info['id']];
            $data[$k][order_status1] = $count11;
            $data[$k][order_status2] = $count22;
            $data[$k][order_status3] = $count33;
            $data[$k][order_status4] = $count44;
            if($money==''){
            $data[$k][amount]        = 0;
            }else{
            $data[$k][amount]        = $money;    
            }
            
        }
        //print_r($goods_list);
        

        foreach ($data as $field=>$v){

            if($field == 'id'){
                $headArr[]='顾客ID';
            }

            if($field == 'name'){
                $headArr[]='顾客姓名';
            }
            if($field == 'phone'){
                $headArr[]='顾客手机';
            }
            if($field == 'order_status1'){
                $headArr[]='末处理订单总数量';
            }
            if($field == 'order_status2'){
                $headArr[]='已处理订单总数量';
            }
            if($field == 'order_status3'){
                $headArr[]='成功订单总数量';
            }
            if($field == 'order_status4'){
                $headArr[]='失败订单总数量';
            }
            if($field == 'amount'){
                $headArr[]='总成交额';
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
    public function order_statistics(){
         $month = date('m');
         $year =  date('Y');
         $day =   date('d');
         $last_month = date('m') - 1;
         if($month == 1){
           $last_month = 12;
           $year = $year - 1;
         }
         $last_time =mktime(0, 0, 0, $last_month, $day, $year);
        //for($i = strtotime(date("Y-m-d",$last_time)); $i <= strtotime(date("Y-m-d",time())); $i += 86400) {
        $countcoun111 =0;
        $countcoun222 =0;
        for($i = $last_time; $i <= time(); $i += 86400) {
        $last =$i+86399;
        $count1[$i] =M('Order')->where(array('booking_time'=>array(array('EGT',$i),array('ELT',$last),'AND'),'order_status'=>3))->count();
        $countcoun111 +=$count1[$i];
        $count2[$i] =M('Order')->where(array('booking_time'=>array(array('EGT',$i),array('ELT',$last),'AND'),'order_status'=>4))->count();
        $countcoun222 +=$count2[$i];
        }
        $this->assign('countcoun111',$countcoun111);
        $this->assign('countcoun222',$countcoun222);
        $this->assign('count1',$count1);
        $this->assign('count2',$count2);
        $this->assign('last_time',$last_time);
        $this->display();
    }
    public function order_statistics_ajax(){
        if(IS_AJAX){
        $start =strtotime(trim(I('get.start')));
        if(empty($start)){
         $month = date('m');
         $year =  date('Y');
         $day =   date('d');
         $last_month = date('m') - 1;
         if($month == 1){
           $last_month = 12;
           $year = $year - 1;
         }
         $start =mktime(0, 0, 0, $last_month, $day, $year);
        }
        $end =strtotime(trim(I('get.end')));
        if(empty($end)){
            $end =time();
        }
        $countcoun111 =0;
        $countcoun222 =0;
        for($i = $start; $i <= $end; $i += 86400) {
        $last =$i+86399;
        $count1[$i] =M('Order')->where(array('booking_time'=>array(array('EGT',$i),array('ELT',$last),'AND'),'order_status'=>3))->count();
        $countcoun111 +=$count1[$i];
        $count2[$i] =M('Order')->where(array('booking_time'=>array(array('EGT',$i),array('ELT',$last),'AND'),'order_status'=>4))->count();
        $countcoun222 +=$count2[$i];
        }
        $dd['start']    =$start;
        $dd['end']      =$end;
        $dd['count1']   =$count1;
        $dd['count2']   =$count2;
        $dd['countcoun111']   =$countcoun111;
        $dd['countcoun222']   =$countcoun222;
        $this->ajaxReturn($dd);
        }else{
            $this->redirect('Index/index');
        }
    }
    //导出订单统计数据方法
    public function order_export()
    {   
        $start =strtotime(trim(I('get.start_date')));
        if(empty($start)){
         $month = date('m');
         $year =  date('Y');
         $day =   date('d');
         $last_month = date('m') - 1;
         if($month == 1){
           $last_month = 12;
           $year = $year - 1;
         }
         $start =mktime(0, 0, 0, $last_month, $day, $year);
        }
        $end =strtotime(trim(I('get.end_date')));
        if(empty($end)){
            $end =time();
        }
        $countcoun111 =0;
        $countcoun222 =0;
        for($i = $start; $i <= $end; $i += 86400) {
        $last =$i+86399;
        $count1[$i] =M('Order')->where(array('booking_time'=>array(array('EGT',$i),array('ELT',$last),'AND'),'order_status'=>3))->count();
        $countcoun111 +=$count1[$i];
        $count2[$i] =M('Order')->where(array('booking_time'=>array(array('EGT',$i),array('ELT',$last),'AND'),'order_status'=>4))->count();
        $countcoun222 +=$count2[$i];
        }

        $data = array();
        $k=0;
            for($i = $start; $i <= $end; $i += 86400) {
            $data[$k][day] = date("Y-m-d",$i);
            $count11 =$count1[$i];
            $count22 =$count2[$i];
            $count33 =$count3[$i];
            $data[$k][order_status1] = $count11;
            $data[$k][order_status2] = $count22;
            $k =$k+1;
        }
            $data[$k][day] = '总计';
            $data[$k][order_status1] = $countcoun111;
            $data[$k][order_status2] = $countcoun222;
        foreach ($data as $field=>$v){
            if($field == 'day'){
                $headArr[]='日期';
            }

            if($field == 'order_status1'){
                $headArr[]='成功订单总数量';
            }
            if($field == 'order_status2'){
                $headArr[]='失败订单总数量';
            }
       
        }

        $filename="goods_list";


        $this->getExcel($filename,$headArr,$data);
    }
     public function fund_statistics(){
         $month = date('m');
         $year =  date('Y');
         $day =   date('d');
         $last_month = date('m') - 1;
         if($month == 1){
           $last_month = 12;
           $year = $year - 1;
         }
         $last_time =mktime(0, 0, 0, $last_month, $day, $year);
        //for($i = strtotime(date("Y-m-d",$last_time)); $i <= strtotime(date("Y-m-d",time())); $i += 86400) {
        $countcoun111 =0;
        for($i = $last_time; $i <= time(); $i += 86400) {
        $last =$i+86399;
        $count1[$i] =M('Chongzhi')->where(array('recharge_time'=>array(array('EGT',$i),array('ELT',$last),'AND'),'status'=>1))->sum('price');
        $countcoun111 +=$count1[$i];
        }
        $this->assign('countcoun111',$countcoun111);
        $this->assign('count1',$count1);
        $this->assign('last_time',$last_time);
        $this->display();
    }
    public function fund_statistics_ajax(){
        if(IS_AJAX){
        $start =strtotime(trim(I('get.start')));
        if(empty($start)){
         $month = date('m');
         $year =  date('Y');
         $day =   date('d');
         $last_month = date('m') - 1;
         if($month == 1){
           $last_month = 12;
           $year = $year - 1;
         }
         $start =mktime(0, 0, 0, $last_month, $day, $year);
        }
        $end =strtotime(trim(I('get.end')));
        if(empty($end)){
            $end =time();
        }
        $countcoun111 =0;
        for($i = $start; $i <= $end; $i += 86400) {
        $last =$i+86399;
        $count1[$i] =M('Chongzhi')->where(array('recharge_time'=>array(array('EGT',$i),array('ELT',$last),'AND'),'status'=>1))->sum('price');
        $countcoun111 +=$count1[$i];
        }
        $dd['start']    =$start;
        $dd['end']      =$end;
        $dd['count1']   =$count1;
        $dd['countcoun111']   =$countcoun111;
        $this->ajaxReturn($dd);
        }else{
            $this->redirect('Index/index');
        }
    }
    //导出资金统计数据方法
    public function fund_export()
    {   
        $start =strtotime(trim(I('get.start_date')));
        if(empty($start)){
         $month = date('m');
         $year =  date('Y');
         $day =   date('d');
         $last_month = date('m') - 1;
         if($month == 1){
           $last_month = 12;
           $year = $year - 1;
         }
         $start =mktime(0, 0, 0, $last_month, $day, $year);
        }
        $end =strtotime(trim(I('get.end_date')));
        if(empty($end)){
            $end =time();
        }
        $countcoun111 =0;
        for($i = $start; $i <= $end; $i += 86400) {
        $last =$i+86399;
        $count1[$i] =M('Chongzhi')->where(array('recharge_time'=>array(array('EGT',$i),array('ELT',$last),'AND'),'status'=>1))->sum('price');
        $countcoun111 +=$count1[$i];
        }

        $data = array();
        $k=0;
            for($i = $start; $i <= $end; $i += 86400) {
            $data[$k][day] = date("Y-m-d",$i);
            $count11 =$count1[$i];
            if($count11!=''){
              $data[$k][order_status1] = $count11;  
              }else{
                $data[$k][order_status1] = 0;
              }
            $k =$k+1;
        }
            $data[$k][day] = '总计';
            $data[$k][order_status1] = $countcoun111;
        foreach ($data as $field=>$v){
            if($field == 'day'){
                $headArr[]='日期';
            }

            if($field == 'order_status1'){
                $headArr[]='总成金额';
            }
           
       
        }

        $filename="goods_list";


        $this->getExcel($filename,$headArr,$data);
    }
    public function loginout(){
    //session(null);
    unset($_SESSION['username']);
    unset($_SESSION['uid']);
    // var_dump(session('?username'));exit;
    if (session('?uid') ==false){
    $this->success('退出成功',U('Admin/Login/index'),3);
     }
  }

}