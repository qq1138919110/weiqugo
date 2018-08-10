<?php
// header("Content-type:text/html;charset=utf-8");
namespace Admin\Controller; //命名空间 定义这些文件在哪
use Think\Controller;  //加载核心控制库
class CustomerController extends AdminController {

       
        	
        	public function member(){
        		$count = M('Home_user')->where(array('is_stop'=>1))->count();// 查询满足要求的总记录数
		        $Page = new \Think\Page($count,10);// 实例化分页类 传入总记录数和每页显示的记录数
		        $Page->setConfig('header','');
		        $Page->setConfig('prev','上一页');
		        $Page->setConfig('next','下一页');
		        $Page->setConfig('first','第一页');
		        $Page->setConfig('last','最后一页');
		        // $Page->setConfig('end','最后一页');
		        $Page->lastSuffix = false;
		        $Page->setConfig('theme','%HEADER% %FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END%');
		        $show = $Page->show();// 分页显示输出
		        $p =$_GET['p']?$_GET['p']:1;
		        $list = M('Home_user')->where(array('is_stop'=>1))->order('id desc')->page($p.',10')->select();
				$this->assign('list',$list);
		        $this->assign('page',$show); //分页代码
         		$this->display();
        	}

            public function member_update(){
                if(IS_AJAX){
                    $uid =trim(I('post.uid'));
                    if(empty($uid)){
                        $this->ajaxReturn(1);
                    }
                    $user =M('Home_user')->where(array('id'=>$uid,'is_stop'=>1))->find();
                    if(!$user){
                        $this->ajaxReturn(1);
                    }
                    $data['user_phone'] =trim(I('post.phone'));
                    $phone =M('Home_user')->where(array('user_phone'=>$data['user_phone'],'is_stop'=>1))->find();
                    if($phone){
                      if($phone['id']!=$uid){
                        $this->ajaxReturn(2);
                        }  
                    }
                    $data['id']    =$uid;
                    $data['user_name'] =trim(I('post.name'));
                    $data['user_weixin'] =trim(I('post.weixin'));
                    $res =M('Home_user')->data($data)->save();
                    if($res!==false){
                        $this->ajaxReturn(4);
                    }else{
                        $this->ajaxReturn(5);
                    }
                    
                }
                $uid =trim(I('get.uid'));
                if(empty($uid)){
                    $this->error('非法操作');
                }
                $user =M('Home_user')->where(array('id'=>$uid))->find();
                if(!$user){
                    $this->error('非法操作');
                }
                $this->assign('user',$user);
                $this->display();
            }
            public function member_delete(){
                if(IS_AJAX){
                    $uid =trim(I('post.uid'));
                    $user =M('Home_user')->where(array('id'=>$uid,'is_stop'=>1))->find();
                    if(!$user){
                        $this->ajaxReturn(1);
                    }
                    $res =M('Home_user')->where(array('id'=>$uid))->save(array('is_stop'=>0));
                    if($res!==false){
                        $this->ajaxReturn(2);
                    }else{
                        $this->ajaxReturn(3);
                    }
                }else{
                    $this->redirect('Index/index');
                }
            }
            public function search(){
                if(IS_AJAX){
                 $name      =trim(I('get.name'));
                 $phone     =trim(I('get.phone'));
                 if($name==''&&$phone==''){
                    $list = M('Home_user')->where(array('is_stop'=>1))->order('id desc')->select();
                    foreach ($list as $key => $value) {
                        $list[$key]['register_time'] =date('Y-m-d H:i:s',$value['register_time']);
                    }
                    $dd['list']         =$list;
                    $this->ajaxReturn($dd);
                 }
                 if($name!=''){
                   $where['user_name'] =array('like', '%' . $name . '%'); 
                 }
                 if($phone!=''){
                   $where['user_phone'] =array('like', '%' . $phone . '%'); 
                 }
                 
                 $where['is_stop'] =1;
                 $list = M('Home_user')->where($where)->order('id desc')->select();
                 foreach ($list as $key => $value) {
                        $list[$key]['register_time'] =date('Y-m-d H:i:s',$value['register_time']);
                    }
                 $dd['list']         =$list;
                 $this->ajaxReturn($dd);
                }
                
                $this->display();
            }

                   //导出数据方法
    public function goods_export()
    {   

        $id  = I('post.id');
        if(empty($id)){
        $goods_list = M('Home_user')->order('id desc')->where(array('is_stop'=>1))->select();
        }else{
        $where['id'] =array('in',$id);
        $where['is_stop'] =1;
        $goods_list = M('Home_user')->order('id desc')->where($where)->select();  
        }
        //print_r($goods_list);exit;
        $data = array();
            foreach ($goods_list as $k=>$goods_info){
            $data[$k][name] = $goods_info['user_name'];
            $data[$k][phone] = $goods_info['user_phone'];
            $data[$k][weixn] = $goods_info['user_weixin'];
            $data[$k][balance] = $goods_info['score'];
            $data[$k][register_time] = date('Y-m-d H:i:s',$goods_info['register_time']);
        }
        
        

        foreach ($data as $field=>$v){

            if($field == 'name'){
                $headArr[]='姓名';
            }
           
            if($field == 'phone'){
                $headArr[]='手机号';
            }
            if($field == 'weixin'){
                $headArr[]='微信号';
            }
           
            if($field == 'balance'){
                $headArr[]='积分';
            }

            if($field == 'register_time'){
                $headArr[]='注册时间';
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
               //修改余额
            public function modified_amount(){
                $uid =trim(I('get.uid'));
                if(empty($uid)){
                    $this->error('非法操作');
                }
                $user =M('Home_user')->where(array('id'=>$uid,'is_stop'=>1))->find();
                if(!$user){
                    $this->error('非法操作');
                }
                $this->assign('user',$user);
                $this->display();
            }
            public function modified_amount_ajax(){
                if(IS_AJAX){
                    $uid =trim(I('post.uid'));
                    if(empty($uid)){
                        $this->ajaxReturn(1);
                    }
                    $user =M('Home_user')->where(array('id'=>$uid,'is_stop'=>1))->find();
                    if(!$user){
                        $this->ajaxReturn(1);
                    }
                    $balance =trim(I('post.balance'));
                    if(empty($balance)){
                        $balance =0;
                    }
                     if($user['score'] >$balance){
                        $jiadata['user_id'] =$user['id'];
                        $userbalance =$user['score']-$balance;
                        $jiadata['score'] =$userbalance;
                        $jiadata['is_add'] =0;
                        $jiadata['remark'] ='后台消费';
                        $jiadata['add_time'] =time(); 
                    }
                    if($balance>$user['score']){
                        $jiadata['user_id'] =$user['id'];
                        $userbalance =$balance-$user['score'];
                        $jiadata['score'] =$userbalance;
                        $jiadata['is_add'] =1;
                        $jiadata['remark'] ='后台充值';
                        $jiadata['add_time'] =time();
                    }
                    $data['id']         =$uid;
                    $data['score']    =$balance;
                    $res =M('Home_user')->data($data)->save();
                    if($res!==false){
                        $ress =M('Score')->data($jiadata)->add();
                        $this->ajaxReturn(2);
                    }else{
                        $this->ajaxReturn(3);
                    }
                }else{
                    $this->redirect('Index/index');
                }
            }
            //评论
          public function comment(){
            $count = M('Comment')->where(array('status'=>0))->count();// 查询满足要求的总记录数
            $Page = new \Think\Page($count,10);// 实例化分页类 传入总记录数和每页显示的记录数
            $Page->setConfig('header','');
            $Page->setConfig('prev','上一页');
            $Page->setConfig('next','下一页');
            $Page->setConfig('first','第一页');
            $Page->setConfig('last','最后一页');
            // $Page->setConfig('end','最后一页');
            $Page->lastSuffix = false;
            $Page->setConfig('theme','%HEADER% %FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END%');
            $show = $Page->show();// 分页显示输出
            $p =$_GET['p']?$_GET['p']:1;
            $list = M('Comment')->where(array('status'=>0))->order('id desc')->page($p.',10')->select();
            foreach ($list as $key => $value) {
                $list[$key]['comment'] =base64_decode($value['comment']);
                $list[$key]['uid'] =M('Home_user')->where(array('id'=>$value['user_id']))->field('user_name')->find();
                $list[$key]['goods'] =M('Goods')->where(array('good_id'=>$value['goods_id']))->field('good_name')->find();
            }

            $this->assign('list',$list);
            $this->assign('page',$show); //分页代码
            $this->display();
          }
          //评论详情
          public function reply_comment(){
            $comment_id =trim(I('get.comment_id'));
            $comment =M('Comment')->where(array('id'=>$comment_id))->find();
            $comment['comment'] =base64_decode($comment['comment']);
            $comment['uid']=M('Home_user')->where(array('id'=>$comment['user_id']))->field('user_name')->find();
            $comment['goods'] =M('Goods')->where(array('good_id'=>$comment['goods_id']))->field('good_name')->find();
            $this->assign('comment',$comment);
            $this->display();
          }
        //通过精选
          public function adopt_comment_ajax(){
            if(IS_AJAX){
                $comment_id =trim(I('post.comment_id'));
                $comment =M('Comment')->where(array('id'=>$comment_id))->find();
                if(!$comment){
                    $this->ajaxReturn(1);
                }
                $res =M('Comment')->where(array('id'=>$comment_id))->save(array('status'=>'1','is_display'=>1,'review_time'=>time()));
                if($res!==false){
                    $this->ajaxReturn(2);
                }else{
                    $this->ajaxReturn(3);
                }
            }
          }
            //不通过
            public function not_through_comment_ajax(){
            if(IS_AJAX){
                $comment_id =trim(I('post.comment_id'));
                $comment =M('Comment')->where(array('id'=>$comment_id))->find();
                if(!$comment){
                    $this->ajaxReturn(1);
                }
                $res =M('Comment')->where(array('id'=>$comment_id))->save(array('status'=>'1','is_display'=>0,'review_time'=>time()));
                if($res!==false){
                    $this->ajaxReturn(2);
                }else{
                    $this->ajaxReturn(3);
                }
            }
          }
          //批量通过
        public function batch_adopt_comment(){
            if(IS_AJAX){
                $id  = I('post.comment_id');
                $nid = explode(',',$id);
                $newid = array_filter($nid);
                $id  = implode(',', $newid);
                $where['id'] =array('in',$id);
                $data['status'] =1;
                $data['is_display'] =1;
                $data['review_time'] =time();
                $res =M('Comment')->where($where)->save($data);
                if($res!==false){
                    $this->ajaxReturn(1);
                }else{
                    $this->ajaxReturn(2);
                }
            }else{
                $this->redirect('Index/index');
            }
        }

           //批量不通过
        public function batch_not_through_comment(){
            if(IS_AJAX){
                $id  = I('post.comment_id');
                $nid = explode(',',$id);
                $newid = array_filter($nid);
                $id  = implode(',', $newid);
                $where['id'] =array('in',$id);
                $data['status'] =1;
                $data['is_display'] =0;
                $data['review_time'] =time();
                $res =M('Comment')->where($where)->save($data);
                if($res!==false){
                    $this->ajaxReturn(1);
                }else{
                    $this->ajaxReturn(2);
                }
            }else{
                $this->redirect('Index/index');
            }
        }
        //通过
        public function adopt_comment(){
            $count = M('Comment')->where(array('status'=>1,'is_display'=>1))->count();// 查询满足要求的总记录数
            $Page = new \Think\Page($count,10);// 实例化分页类 传入总记录数和每页显示的记录数
            $Page->setConfig('header','');
            $Page->setConfig('prev','上一页');
            $Page->setConfig('next','下一页');
            $Page->setConfig('first','第一页');
            $Page->setConfig('last','最后一页');
            // $Page->setConfig('end','最后一页');
            $Page->lastSuffix = false;
            $Page->setConfig('theme','%HEADER% %FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END%');
            $show = $Page->show();// 分页显示输出
            $p =$_GET['p']?$_GET['p']:1;
            $list = M('Comment')->where(array('status'=>1,'is_display'=>1))->order('review_time desc')->page($p.',10')->select();
            foreach ($list as $key => $value) {
                $list[$key]['comment'] =base64_decode($value['comment']);
                $list[$key]['uid'] =M('Home_user')->where(array('id'=>$value['user_id']))->field('user_name')->find();
                $list[$key]['goods'] =M('Goods')->where(array('good_id'=>$value['goods_id']))->field('good_name')->find();
            }

            $this->assign('list',$list);
            $this->assign('page',$show); //分页代码
            $this->display();
        }
        //不通过
        public function not_through_comment(){
            $count = M('Comment')->where(array('status'=>1,'is_display'=>0))->count();// 查询满足要求的总记录数
            $Page = new \Think\Page($count,10);// 实例化分页类 传入总记录数和每页显示的记录数
            $Page->setConfig('header','');
            $Page->setConfig('prev','上一页');
            $Page->setConfig('next','下一页');
            $Page->setConfig('first','第一页');
            $Page->setConfig('last','最后一页');
            // $Page->setConfig('end','最后一页');
            $Page->lastSuffix = false;
            $Page->setConfig('theme','%HEADER% %FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END%');
            $show = $Page->show();// 分页显示输出
            $p =$_GET['p']?$_GET['p']:1;
            $list = M('Comment')->where(array('status'=>1,'is_display'=>0))->order('review_time desc')->page($p.',10')->select();
            foreach ($list as $key => $value) {
                $list[$key]['comment'] =base64_decode($value['comment']);
                $list[$key]['uid'] =M('Home_user')->where(array('id'=>$value['user_id']))->field('user_name')->find();
                $list[$key]['goods'] =M('Goods')->where(array('good_id'=>$value['goods_id']))->field('good_name')->find();
            }

            $this->assign('list',$list);
            $this->assign('page',$show); //分页代码
            $this->display();
        }
        public function consumption_record(){
            $uid =trim(I('get.uid'));
            if($uid){
                $count = M('Score')->where(array('user_id'=>$uid))->count();// 查询满足要求的总记录数    
            }else{
                $count = M('Score')->count();// 查询满足要求的总记录数
            }
            
            $Page = new \Think\Page($count,10);// 实例化分页类 传入总记录数和每页显示的记录数
            $Page->setConfig('header','');
            $Page->setConfig('prev','上一页');
            $Page->setConfig('next','下一页');
            $Page->setConfig('first','第一页');
            $Page->setConfig('last','最后一页');
            // $Page->setConfig('end','最后一页');
            $Page->lastSuffix = false;
            $Page->setConfig('theme','%HEADER% %FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END%');
            $show = $Page->show();// 分页显示输出
            $p =$_GET['p']?$_GET['p']:1;
            if($uid){
                $list = M('Score')->where(array('user_id'=>$uid))->order('id desc')->page($p.',10')->select();
            }else{
                $list = M('Score')->order('id desc')->page($p.',10')->select();
            }
            
            foreach ($list as $key => $value) {
                $list[$key]['uid'] =M('Home_user')->field('user_phone')->where(array('id'=>$value['user_id']))->find();
            }
            $this->assign('list',$list);
            $this->assign('page',$show); //分页代码
            $this->display();
        }
            public function record_export()
        {   

        $id  = I('post.id');
        if(empty($id)){
        $goods_list = M('Score')->order('id desc')->select();
        }else{
        $where['id'] =array('in',$id);
        $goods_list = M('Score')->order('id desc')->where($where)->select();  
        }
        foreach ($goods_list as $key => $value) {
            $goods_list[$key]['uid'] = M('Home_user')->where(array('id'=>$value['user_id']))->find();
        }
        $data = array();
            foreach ($goods_list as $k=>$goods_info){
            $data[$k][name] = $goods_info['uid']['user_phone'];
            $data[$k][remark] = $goods_info['remark'];
            if($goods_info['is_add']==1){
                $data[$k][score] ='+'.$goods_info['score'];
            }else{
                $data[$k][score] ='-'.$goods_info['score'];
            }
            $data[$k][add_time] = date('Y-m-d H:i:s',$goods_info['add_time']);
            if($goods_info['status']==1){
                $data[$k][status] ='正常';
            }else{
                $data[$k][status] ='已撤销';
            }
        }
        
        foreach ($data as $field=>$v){

            if($field == 'name'){
                $headArr[]='消费用户';
            }
           
            if($field == 'remark'){
                $headArr[]='消费备注';
            }
            if($field == 'score'){
                $headArr[]='积分';
            }
           
            if($field == 'add_time'){
                $headArr[]='消费时间';
            }

            if($field == 'status'){
                $headArr[]='消费状态';
            }
            
       
        }

        $filename="goods_list";


        $this->getExcel($filename,$headArr,$data);
    }
} 