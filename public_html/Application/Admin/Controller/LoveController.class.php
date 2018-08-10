<?php
// header("Content-type:text/html;charset=utf-8");
namespace Admin\Controller; //命名空间 定义这些文件在哪
use Think\Controller;  //加载核心控制库
class LoveController extends AdminController {

           public function love_donation(){
            
                $count = M('Love_donation')->count();// 查询满足要求的总记录数
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
                $list = M('Love_donation')->order('number')->page($p.',10')->select();
                $this->assign('shop_id',$shop_id);
                $this->assign('list',$list);
                $this->assign('page',$show); //分页代码
            $this->display();
           }
    
    public function love_donation_add(){
      if(IS_AJAX){
        $data['number'] =trim(I('post.number'));
        if(empty($data['number'])){
          $data['number'] =0;
        }
        if(is_numeric($data['number'])=== false){
          $this->ajaxReturn(4);
        }
        $data['title'] =trim(I('post.title'));
        $data['state'] =trim(I('post.state'));
        $data['details'] =trim($_POST['details']);
        $more_img = I('post.more_img');
        $data['photo'] =implode(',', $more_img);
        $data['create_time'] =time();
        $res =M('Love_donation')->data($data)->add();
        if($res){
          $this->ajaxReturn(1);
        }else{
          $this->ajaxReturn(2);
        }
      }
      $this->display();
    } 
    public function love_donation_update(){
      if(IS_AJAX){
        $id =trim(I('post.id'));
        $list =M('Love_donation')->where(array('id'=>$id))->find();
        if(!$list){
          $this->ajaxReturn(1);
        }
        $data['id']     =$id;
        $data['number'] =trim(I('post.number'));
        if(empty($data['number'])){
          $data['number'] =0;
        }
        if(is_numeric($data['number'])=== false){
          $this->ajaxReturn(2);
        }
        $data['title'] =trim(I('post.title'));
        $data['state'] =trim(I('post.state'));
        $data['details'] =trim($_POST['details']);
        $more_img = I('post.more_img');
        $data['photo'] =implode(',', $more_img);
        $data['create_time'] =time();
        $res =M('Love_donation')->data($data)->save();
        if($res!==false){
          $this->ajaxReturn(3);
        }else{
          $this->ajaxReturn(4);
        }
      }
      $id =trim(I('get.id'));
      $list =M('Love_donation')->where(array('id'=>$id))->find();
      if(!$list){
        $this->redirect('Love/love_donation');
      }
      $this->assign('list',$list);
      $this->display();
    }
      public function show_love(){
          $count = M('Show_love')->count();// 查询满足要求的总记录数
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
          $list = M('Show_love')->order('create_time desc')->page($p.',10')->select();
          $this->assign('list',$list);
          $this->assign('page',$show); //分页代码
      $this->display();
           }
    public function show_love_add(){
      if(IS_AJAX){
        $data['title'] =trim(I('post.title'));
        $data['content'] =trim($_POST['content']);
        $more_img = I('post.more_img');
        $data['picture'] =implode(',', $more_img);
        $data['create_time'] =time();
        $res =M('Show_love')->data($data)->add();
        if($res){
          $this->ajaxReturn(1);
        }else{
          $this->ajaxReturn(2);
        }

      }
      $this->display();
    }
    public function  show_love_update(){
      if(IS_AJAX){
        $id =trim(I('post.id'));
        $list =M('Show_love')->where(array('id'=>$id))->find();
        if(!$list){
          $this->ajaxReturn(1);
        }
        $data['id']  =$id;
        $data['title'] =trim(I('post.title'));
        $data['content'] =trim($_POST['content']);
        $more_img = I('post.more_img');
        $data['picture'] =implode(',', $more_img);
        $res =M('Show_love')->data($data)->save();
        if($res!==false){
          $this->ajaxReturn(2);
        }else{
          $this->ajaxReturn(3);
        }
      }
      $id =trim(I('get.id'));
      $list =M('Show_love')->where(array('id'=>$id))->find();
      if(!$list){
        $this->redirect('Love/show_love');
      }
      $this->assign('list',$list);
      $this->display();
    }
    public function show_love_delete(){
      if(IS_AJAX){
        $id =trim(I('post.good_id'));
        $list =M('Show_love')->where(array('id'=>$id))->find();
        if(!$list){
          $this->ajaxReturn(1);
        }
        $delete =M('Show_love')->where(array('id'=>$id))->delete();
        if($delete!==false){
          $this->ajaxReturn(2);
        }else{
          $this->ajaxReturn(3);
        }
      }else{
       $this->redirect('Love/show_love'); 
      }
    }
     public function adoption_center(){
          $count = M('Pet')->count();// 查询满足要求的总记录数
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
          $list = M('Pet')->order('number')->page($p.',10')->select();
          $this->assign('list',$list);
          $this->assign('page',$show); //分页代码
      $this->display();
           }
    public function adoption_center_add(){
      if(IS_AJAX){
        $data['number'] =trim(I('post.number'));
        if(empty($data['number'])){
          $data['number'] =0;
        }
        if(is_numeric($data['number'])=== false){
          $this->ajaxReturn(4);
        }
        $data['pet_name'] =trim(I('post.pet_name'));
        $data['integral'] =trim(I('post.integral'));
        $data['pet_age']  =trim(I('post.pet_age'));
        $data['master_name'] =trim(I('post.pet_name'));
        $data['master_phone'] =trim(I('post.master_phone'));
        $data['master_address'] =trim(I('post.master_address'));
        $data['state'] =1;
        $data['pet_profile'] =trim($_POST['pet_profile']);
        $more_img = I('post.more_img');
        $data['pet_map'] =implode(',', $more_img);
        $data['create_time'] =time();
        $res =M('Pet')->data($data)->add();
        if($res){
          $this->ajaxReturn(1);
        }else{
          $this->ajaxReturn(2);
        }

      }
      $this->display();
    }
    public function adoption_center_update(){
      if(IS_AJAX){
      $id =trim(I('post.id'));
      $list =M('Pet')->where(array('id'=>$id))->find();
      if(!$list){
        $this->ajaxReturn(3);
      }
      $data['number'] =trim(I('post.number'));
        if(empty($data['number'])){
          $data['number'] =0;
        }
        if(is_numeric($data['number'])=== false){
          $this->ajaxReturn(4);
        }
      $data['id'] =$id;
      $data['pet_name'] =trim(I('post.pet_name'));
      $data['integral'] =trim(I('post.integral'));
      $data['pet_age']  =trim(I('post.pet_age'));
      $data['master_name'] =trim(I('post.pet_name'));
      $data['master_phone'] =trim(I('post.master_phone'));
      $data['master_address'] =trim(I('post.master_address'));
      $data['state'] =trim(I('post.state'));
      $data['pet_profile'] =trim($_POST['pet_profile']);
      $more_img = I('post.more_img');
      $data['pet_map'] =implode(',', $more_img);
      $data['create_time'] =time();
      $res =M('Pet')->data($data)->save();
      if($res){
        $this->ajaxReturn(1);
      }else{
        $this->ajaxReturn(2);
      }
      }
      $id =trim(I('get.id'));
      $list =M('Pet')->where(array('id'=>$id))->find();
      if(!$list){
        $this->redirect('Love/adoption_center');
      }
      $this->assign('list',$list);
      $this->display();
    }
}