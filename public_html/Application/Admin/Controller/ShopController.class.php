<?php
// header("Content-type:text/html;charset=utf-8");
namespace Admin\Controller; //命名空间 定义这些文件在哪
use Think\Controller;  //加载核心控制库
class ShopController extends AdminController {


        public function more_img(){
          if(!IS_AJAX){
              // asdfgh
          }
          // var_dump($_FILES);exit;
          if (empty($_FILES['file']['name'])) {
              $this->ajaxReturn(array("error"=>"您还未选择图片"));
          }
              $upload = new \Think\Upload();// 实例化上传类
              $upload->maxSize = 3145728 ;// 设置附件上传大小
              $upload->exts = array('jpg', 'gif', 'png', 'jpeg');// 设置附件后缀名
              $upload->autoSub = true; //自动使用子目录保存上传文件 默认为true
              //$upload->subName = array('date','Y/m/d'); //子目录创建方式，采用数组或者字符串方式定义
              $upload->hash = true; //是否生成文件的hash编码 默认为true

              //上传文件
              
              $info = $upload->uploadOne($_FILES['file']);//单图
              if(!$info) {// 上传错误提示错误信息
                  $this->ajaxReturn(array('error'=>$upload->getError()));
              }else{// 上传成功
                //   $image = new \Think\Image();
                //   $image->open('./Uploads/'.$info['savepath'].$info['savename']);

                //   //生成一个居中裁剪为150*150的缩略图并保存为thumb.jpg
                // $image->thumb(C('MORE_IMG_H'), C('MORE_IMG_W'),C('MORE_IMG_T'))->save('./Uploads/'.$info['savepath'].$info['savename'].'_thumb1.'.$image->type());                
                   // exit;
                    $this->ajaxReturn(array("error"=>"0","path"=>'/Uploads/'.$info['savepath'],"name"=>$info['savename']));
                   //$this->ajaxReturn(array("error"=>"0","path"=>$info['savepath'],"name"=>$info['savename'].'_thumb1.'.$image->type()));
              }
      }

  
          //商品分类
          public function f_classification(){
            
            $count = M('Goods_type')->count();// 查询满足要求的总记录数
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
            $list = M('Goods_type')->order('type_no')->page($p.',10')->select();
            $this->assign('type',$list);
            $this->assign('page',$show); //分页代码
            $this->display();
          }
          public function f_classification_add(){
            if(IS_AJAX){
              $data['type_name'] =trim(I('post.type_name'));
              $data['type_no'] =trim(I('post.type_no'));
              if(empty($data['type_no'])){
                $data['type_no'] =0;
              }
              if(is_numeric($data['type_no'])=== false){
                $this->ajaxReturn(3);
              }
              $more_img = I('post.more_img');
              $data['type_images'] =implode(',', $more_img);
              $data['type_images']=$data['type_images']?$data['type_images']:"";
              $data['type_link'] =trim(I('post.type_link'));
              $res =M('Goods_type')->data($data)->add();
              if($res){
               $this->ajaxReturn(1);
              }else{
                $this->ajaxReturn(2);
              }
            }else{
              $navigation =M('Navigation')->order('navigation_no')->select();
              $this->assign('navigation',$navigation);
              $this->display();
            }
          }

          public function f_classification_update(){
            if(IS_AJAX){
              $data['type_id'] =trim(I('post.type_id'));
              if(empty($data['type_id'])){
                $this->ajaxReturn(3);
              }
              $type =M('Goods_type')->where(array('type_id'=>$data['type_id']))->find();
              if(!$type){
                $this->ajaxReturn(3);
              }
              $data['type_name']  =trim(I('post.type_name'));
              $more_img = I('post.more_img');
              $data['type_images'] =implode(',', $more_img);
              $data['type_images']=$data['type_images']?$data['type_images']:"";
              $data['type_no']    =trim(I('post.type_no'));
              if(empty($data['type_no'])){
                $data['type_no'] =0;
              }
              if(is_numeric($data['type_no'])=== false){
                $this->ajaxReturn(4);
              }
              $data['type_link'] =trim(I('post.type_link'));
              $res =M('Goods_type')->data($data)->save();
              if($res !==false){
               $this->ajaxReturn(1);
              }else{
                $this->ajaxReturn(2);
              }
            }
              $type_id =trim(I('get.type_id'));
              if(empty($type_id)){
                $this->redirect('Shop/f_classification');
              }
              $type =M('Goods_type')->where(array('type_id'=>$type_id))->find();
              if(!$type){
                  $this->redirect('Shop/f_classification'); 
              }
              $this->assign('type',$type);
              $this->display();
            
          }

           public function f_classification_delete(){
            if(IS_AJAX){
                $type_id=trim(I('post.type_id'));
               if(empty($type_id)){
                $this->ajaxReturn(1);
               }
               $type =M('Goods_type')->where(array('type_id'=>$type_id))->find();
               if(!$type){
                $this->ajaxReturn(1);
               }
               $goods =M('Goods')->where(array('good_type'=>$type_id,'recycle'=>0))->select();
               if($goods){
                $this->ajaxReturn(2);
               }
               $typedelete =M('Goods_type')->where(array('type_id'=>$type_id))->delete();
               if($typedelete!==false){
                 $this->ajaxReturn(3);
               }else{
                $this->ajaxReturn(4);
               } 
                }else{
                  $this->redirect('Index/index');
                }
              }
              //商品导航
            public function navigation(){
            $count = M('Navigation')->count();// 查询满足要求的总记录数
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
            $list = M('Navigation')->order('navigation_no')->page($p.',10')->select();
            $this->assign('type',$list);
            $this->assign('page',$show); //分页代码
            $this->display();
          }
          public function navigation_add(){
            if(IS_AJAX){
              $data['navigation_name'] =trim(I('post.navigation_name'));
              $data['navigation_no']   =trim(I('post.navigation_no'));
              if(empty($data['navigation_no'])){
                $data['navigation_no'] =0;
              }
              if(is_numeric($data['navigation_no'])=== false){
                $this->ajaxReturn(3);
              }
              $res =M('Navigation')->data($data)->add();
              if($res){
               $this->ajaxReturn(1);
              }else{
                $this->ajaxReturn(2);
              }
            }else{
              $this->display();
            }
          }

          public function navigation_update(){
            if(IS_AJAX){
              $data['navigation_id'] =trim(I('post.navigation_id'));
              if(empty($data['navigation_id'])){
                $this->ajaxReturn(3);
              }
              $type =M('Navigation')->where(array('navigation_id'=>$data['navigation_id']))->find();
              if(!$type){
                $this->ajaxReturn(3);
              }
              $data['navigation_name']  =trim(I('post.navigation_name'));
              $data['navigation_no']    =trim(I('post.navigation_no'));
              if(empty($data['navigation_no'])){
                $data['navigation_no'] =0;
              }
              if(is_numeric($data['navigation_no'])=== false){
                $this->ajaxReturn(4);
              }
              $res =M('Navigation')->data($data)->save();
              if($res !==false){
               $this->ajaxReturn(1);
              }else{
                $this->ajaxReturn(2);
              }
            }
              $navigation_id =trim(I('get.navigation_id'));
              if(empty($navigation_id)){
                $this->redirect('Shop/navigation');
              }
              $type =M('Navigation')->where(array('navigation_id'=>$navigation_id))->find();
              if(!$type){
                  $this->redirect('Shop/navigation'); 
              }
              $this->assign('type',$type);
              $this->display();
            
          }

           public function navigation_delete(){
            if(IS_AJAX){
                $navigation_id=trim(I('post.navigation_id'));
               if(empty($navigation_id)){
                $this->ajaxReturn(1);
               }
               $type =M('Navigation')->where(array('navigation_id'=>$navigation_id))->find();
               if(!$type){
                $this->ajaxReturn(1);
               }
               $goods =M('Goods_type')->where(array('navigation_id'=>$navigation_id))->select();
               if($goods){
                $this->ajaxReturn(2);
               }

               $typedelete =M('Navigation')->where(array('navigation_id'=>$navigation_id))->delete();
               if($typedelete!==false){
                 $this->ajaxReturn(3);
               }else{
                $this->ajaxReturn(4);
               } 
                }else{
                  $this->redirect('Index/index');
                }
              }
              //精品商品
           public function commodity(){
            $good_name =trim(I('post.good_name'));
            if(isset($good_name)){
            $where['good_name']=array("like","%".$good_name."%");
            }
            $where['activity_goods'] =0;
            $where['recycle'] =0;

            $count = M('Goods')->where($where)->count();// 查询满足要求的总记录数
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
            $list = M('Goods')->where($where)->order('good_no')->page($p.',10')->select();
            foreach ($list as $key => $value) {
              $list[$key]['goods_stock_amount'] =0;
              $type_id   =$value['good_type'];
              $goods_type[$value['good_id']] =M('goods_type')->where(array('type_id'=>$type_id))->find();
              $navigation[$value['good_id']] =M('Navigation')->where(array('navigation_id'=>$value['navigation_id']))->find();
              $stock =M('Item_sku')->where(array('good_id'=>$value['good_id'],'status'=>1))->select();
              foreach ($stock as $ke => $val) {
                $list[$key]['goods_stock_amount']+=$val['number'];
              }
              unset($stock);
            }
            $this->assign('p',$p);
            $this->assign('navigation',$navigation);
            $this->assign('goods_type',$goods_type);
            $this->assign('list',$list);
            $this->assign('page',$show); //分页代码s
            //兑换升级为抢购 抢购过期转回兑换
            $overdue =M('Goods')->where(array('activity_goods'=>1,'end_time'=>array('ELT',time()),'navigation_id'=>array('NEQ','0')))->select();
            foreach ($overdue as $key => $value) {
              M('Goods')->where(array('good_id'=>$value['good_id']))->save(array(
                    'activity_goods'  => '0',                
                ));
            }
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
              $data['good_name'] =trim(I('post.good_name'));
              $data['good_value'] =trim(I('post.good_value'));
              $data['navigation_id'] =trim(I('post.navigation_id'));
              $data['good_type'] =trim(I('post.good_type'));
              $data['unit'] =trim(I('post.unit'));
              $data['goods_producer'] =trim(I('post.goods_producer'));
              $data['is_good_attr'] =trim(I('post.is_good_attr'));
              $data['main_marketing'] =trim(I('post.main_marketing'));
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
              
              $good_type =M('Goods_type')->order('type_no')->select();
              $navigation=M('Navigation')->order('navigation_no')->select();
              $this->assign('good_type',$good_type);
              $this->assign('navigation',$navigation);
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
              $data['good_name'] =trim(I('post.good_name'));
              $data['navigation_id'] =trim(I('post.navigation_id'));
              $data['good_value'] =trim(I('post.good_value'));
              $data['good_type'] =trim(I('post.good_type'));
              $data['unit'] =trim(I('post.unit'));
              $data['goods_producer'] =trim(I('post.goods_producer'));
              $data['is_good_attr'] =trim(I('post.is_good_attr'));
              $data['main_marketing'] =trim(I('post.main_marketing'));
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
                $this->redirect('Shop/commodity');
              }
              $good = M('Goods')->where(array('good_id'=>$good_id,'recycle'=>0))->find();
              if(!$good){
                $this->redirect('Shop/commodity');
              }
              $navigation=M('Navigation')->order('navigation_no')->select();
              $this->assign('navigation',$navigation);
              $good_type =M('Goods_type')->order('type_no')->select();
              $this->assign('good_type',$good_type);
              $this->assign('good',$good);
              $this->display();
           
          }
          public function commodity_delete(){
            if(IS_AJAX){
              $good_id =$data['good_id']=trim(I('post.good_id'));
            if(empty($good_id)){
              $this->ajaxReturn(1);
            }
            $good =M('Goods')->where(array('good_id'=>$good_id,'recycle'=>0))->find();
            if(!$good){
              $$this->ajaxReturn(1);
            }
            $data['recycle'] =1;
            $goodtable =M('Goods')->data($data)->save();
            if($goodtable!==false){
             $this->ajaxReturn(2);
            }else{
              $this->ajaxReturn(3);
            }
          }else{
            $this->redirect('Index/index');
          }
            
          }
          public function batchdeletegoods(){
            if(IS_AJAX){
              $id  = I('get.good_id');
              $nid = explode(',',$id);
              $newid = array_filter($nid);
              $id  = implode(',', $newid);
              $where['good_id'] =array('in',$id);
              $data['recycle'] =1;
              $res =M('Goods')->where($where)->save($data);
              if($res!==false){
                $this->ajaxReturn(1);
              }else{
                $this->ajaxReturn(2);
              }
            }else{
              $this->redirect('Index/index');
            }
          }
          //商品属性
          public function attribute(){
            $good_id =trim(I('get.good_id'));
              if(empty($good_id)){
                $this->redirect('Shop/commodity');
              }
            $good = M('Goods')->where(array('good_id'=>$good_id,'recycle'=>0))->find();
            if(!$good){
              $this->redirect('Shop/commodity');
            }
            $count = M('Item_attr_key')->where(array('good_id'=>$good_id))->count();// 查询满足要求的总记录数
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
            $list = M('Item_attr_key')->where(array('good_id'=>$good_id))->order('attr_key_id')->page($p.',10')->select();
            $this->assign('list',$list);
            $this->assign('good_id',$good_id);
            $this->assign('page',$show); //分页代码s
            $this->display();
          }
          public function attribute_name_add(){
            if(IS_AJAX){
               $data['good_id'] =trim(I('post.good_id'));
              if(empty($data['good_id'])){
                $this->ajaxReturn(4);
              }
              $good = M('Goods')->where(array('good_id'=>$data['good_id'],'recycle'=>0))->find();
              if(!$good){
                $this->ajaxReturn(4);
              }
              $data['attr_name']       =trim(I('post.attr_name'));
              $res =M('Item_attr_key')->data($data)->add();
              if($res){
               $this->ajaxReturn(1);
              }else{
                $this->ajaxReturn(2);
              }
            }
            $good_id =trim(I('get.good_id'));
              if(empty($good_id)){
                 $this->redirect('Shop/commodity');
              }
            $good = M('Goods')->where(array('good_id'=>$good_id,'recycle'=>0))->find();
            if(!$good){
               $this->redirect('Shop/commodity');
            }
            $this->assign('good_id',$good_id);
            $this->display();
          }
          public function attribute_name_update(){
            if(IS_AJAX){
              $data['attr_key_id'] =trim(I('post.attr_key_id'));
              if(empty($data['attr_key_id'])){
                $this->ajaxReturn(4);
              }
              $attr =M('Item_attr_key')->where(array('attr_key_id'=>$data['attr_key_id']))->find();
              if(!$attr){
                $this->ajaxReturn(4);
              }
              $data['attr_name']  =trim(I('post.attr_name'));
              $res =M('Item_attr_key')->data($data)->save();
              if($res !==false){
               $this->ajaxReturn(1);
              }else{
                $this->ajaxReturn(2);
              }
            }
            $attr_key_id =trim(I('get.attr_key_id'));
            $attr =M('Item_attr_key')->where(array('attr_key_id'=>$attr_key_id))->find();
            if(!$attr){
              $this->redirect('Shop/commodity');
            }
            $this->assign('attr',$attr);
            $this->display();
          }
          public function attribute_name_delete(){
            if(IS_AJAX){
              $attr_key_id =trim(I('post.good_id'));
              $attr =M('Item_attr_key')->where(array('attr_key_id'=>$attr_key_id))->find();
              if(!$attr){
                $this->ajaxReturn(1);
              }
              $attr_val =M('Item_attr_val')->where(array('attr_key_id'=>$attr_key_id))->select();
              if($attr_val){
                $this->ajaxReturn(4);
              }
              $res =M('Item_attr_key')->where(array('attr_key_id'=>$attr_key_id))->delete();
              if($res!==false){
                $this->ajaxReturn(2);
              }else{
                $this->ajaxReturn(3);
              }
            }else{
              $this->redirect('Shop/commodity');
            }
          }
          public function attribute_value(){
            $attr_key_id =trim(I('get.attr_key_id'));
            $attr =M('Item_attr_key')->where(array('attr_key_id'=>$attr_key_id))->find();
            if(!$attr){
              $this->redirect('Shop/commodity');
            }
            $count = M('Item_attr_val')->where(array('attr_key_id'=>$attr_key_id))->count();// 查询满足要求的总记录数
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
            $list = M('Item_attr_val')->where(array('attr_key_id'=>$attr_key_id))->order('symbol')->page($p.',10')->select();
            $this->assign('list',$list);
            $this->assign('attr_key_id',$attr_key_id);
            $this->assign('page',$show); //分页代码s
            $this->display();
          }

          public function attribute_value_add(){
            if(IS_AJAX){
              $data['attr_key_id'] =trim(I('post.attr_key_id'));
              $attr =M('Item_attr_key')->where(array('attr_key_id'=>$data['attr_key_id']))->find();
              if(!$attr){
                $this->ajaxReturn(4);
              }
              $data['good_id'] =$attr['good_id'];
              $data['attr_value']       =trim(I('post.attr_value'));
              $res =M('Item_attr_val')->data($data)->add();
              if($res){
               $this->ajaxReturn(1);
              }else{
                $this->ajaxReturn(2);
              }
            }
            $attr_key_id =trim(I('get.attr_key_id'));
            $attr =M('Item_attr_key')->where(array('attr_key_id'=>$attr_key_id))->find();
            if(!$attr){
              $this->redirect('Shop/commodity');
            }
            $this->assign('attr_key_id',$attr_key_id);
            $this->display();
          }
          public function attribute_value_update(){
            if(IS_AJAX){
            $data['symbol'] =trim(I('post.symbol'));
            $attr_value =M('Item_attr_val')->where(array('symbol'=>$data['symbol']))->find();
            if(!$attr_value){
              $this->ajaxReturn(4);
            }
             $data['attr_value']  =trim(I('post.attr_value'));
              $res =M('Item_attr_val')->data($data)->save();
              if($res !==false){
               $this->ajaxReturn(1);
              }else{
                $this->ajaxReturn(2);
              } 
            }
            $symbol =trim(I('get.symbol'));
            $attr_value =M('Item_attr_val')->where(array('symbol'=>$symbol))->find();
            if(!$attr_value){
              $this->redirect('Shop/commodity');
            }
            $this->assign('attr_value',$attr_value);
            $this->display();
          }
          public function attribute_value_delete(){
            if(IS_AJAX){
            $symbol =trim(I('post.symbol'));
            $attr_value =M('Item_attr_val')->where(array('symbol'=>$symbol))->find();
            if(!$attr_value){
              $this->ajaxReturn(1);
            }
            $item_sku =M('Item_sku')->where(array('good_id'=>$attr_value['good_id'],'status'=>1))->select();
            foreach ($item_sku as $key => $value) {
              $attr_symbol_path =explode(',',$value['attr_symbol_path']);
              if (in_array($attr_value['symbol'],$attr_symbol_path)) {
                  $this->ajaxReturn(4); 
                    }
            }
            $res =M('Item_attr_val')->where(array('symbol'=>$symbol))->delete();
            if($res!==false){
              $this->ajaxReturn(2);
            }else{
              $this->ajaxReturn(3);
            }
            }else{
              $this->redirect('Shop/commodity');
            }
          }
          public function commodity_attribute_add(){
           
            if(IS_AJAX){
              $data['good_id'] =I('post.good_id');
              $good = M('Goods')->where(array('good_id'=>$data['good_id'],'recycle'=>0))->find();
              if(!$good){
                  $re['status']='0';
                  $re['msg']='找不到该商品';
                  $re['data']="";
                  echo json_encode($re);exit; 
              }
              $data['money'] = I('post.money');
              $data['number'] = I('post.number');
              $data =I('post.');
              $map['good_id'] =$data['good_id'];
              //重新组装
              $attr_key =M('Item_attr_key')->order('attr_key_id')->where(array('good_id'=>$map['good_id']))->select();
              foreach ($attr_key as $k => $v) {
                $attr_val[$v['attr_key_id']] =M('Item_attr_val')->order('symbol')->where(array('attr_key_id'=>$v['attr_key_id']))->select();
                $attr[$v['attr_key_id']] =$v;    //  1 => 
                $attr[$v['attr_key_id']]['value']=$attr_val[$v['attr_key_id']];
              }
              foreach ($data['money'] as $k => $v) {
                $map['money']=$v;
                if(empty($map['money'])){
                  $re['status']='0';
                  $re['msg']='商品积分不能为空';
                  $re['data']="";
                  echo json_encode($re);exit;
                }
                if(is_numeric($map['money'])=== false){
                  $re['status']='0';
                  $re['msg']='商品积分只能为数字';
                  $re['data']="";
                  echo json_encode($re);exit;
              }
              $map['number'] =$data['number'][$k];
              if(empty($map['number'])){
                  $re['status']='0';
                  $re['msg']='商品库存量不得为空';
                  $re['data']="";
                  echo json_encode($re);exit;
               }
              if(is_numeric($map['number'])=== false){
                  $re['status']='0';
                  $re['msg']='商品库存量只能为数字';
                  $re['data']="";
                  echo json_encode($re);exit;
              }
              $map['attr_symbol_path'] ='';
              foreach ($attr as $key => $value) {
                if(empty($data[$value['attr_key_id']][$k])){
                  $re['status']='0';
                  $re['msg']=$value['attr_name'].'属性不得为空';
                  $re['data']="";
                  echo json_encode($re);exit;
              }
              $map['attr_symbol_path'] .=$data[$value['attr_key_id']][$k].',';
              }
              $map['attr_symbol_path'] =rtrim($map['attr_symbol_path'],',');//去掉最右边的空格
              M('Item_sku')->data($map)->add();
              }
              $re['status']='1';
              $re['msg']="添加成功";
              $re['data']="";
              echo json_encode($re);exit;
            }else{
              $good_id =trim(I('get.good_id'));
              $good = M('Goods')->where(array('good_id'=>$good_id,'recycle'=>0))->find();
              if(!$good){
                $this->redirect('Shop/commodity');
              }
            $attr_key =M('Item_attr_key')->order('attr_key_id')->where(array('good_id'=>$good_id))->select();
            foreach ($attr_key as $k => $v) {
              $attr_val[$v['attr_key_id']] =M('Item_attr_val')->order('symbol')->where(array('attr_key_id'=>$v['attr_key_id']))->select();
              $attr[$v['attr_key_id']] =$v;    //  1 => 
              $attr[$v['attr_key_id']]['value']=$attr_val[$v['attr_key_id']];
            }
            $this->assign('attr',$attr);
            $this->display();
            }
          }
          public function commodity_attribute_update(){
            if(IS_AJAX){
              $data['good_id'] =I('post.good_id');
              $good = M('Goods')->where(array('good_id'=>$data['good_id'],'recycle'=>0))->find();
              if(!$good){
                  $re['status']='0';
                  $re['msg']='找不到该商品';
                  $re['data']="";
                  echo json_encode($re);exit; 
              }
              $data['money'] = I('post.money');
              $data['number'] = I('post.number');
              $data =I('post.');
              $map['good_id'] =$data['good_id'];
              //重新组装
              $attr_key =M('Item_attr_key')->order('attr_key_id')->where(array('good_id'=>$map['good_id']))->select();
              foreach ($attr_key as $k => $v) {
                $attr_val[$v['attr_key_id']] =M('Item_attr_val')->order('symbol')->where(array('attr_key_id'=>$v['attr_key_id']))->select();
                $attr[$v['attr_key_id']] =$v;    //  1 => 
                $attr[$v['attr_key_id']]['value']=$attr_val[$v['attr_key_id']];
              }
              foreach ($data['money'] as $k => $v) {
                $map['money']=$v;
                if(empty($map['money'])){
                  $re['status']='0';
                  $re['msg']='商品积分不能为空';
                  $re['data']="";
                  echo json_encode($re);exit;
                }
                if(is_numeric($map['money'])=== false){
                  $re['status']='0';
                  $re['msg']='商品积分只能为数字';
                  $re['data']="";
                  echo json_encode($re);exit;
              }
              $map['number'] =$data['number'][$k];
              if(empty($map['number'])){
                  $re['status']='0';
                  $re['msg']='商品库存量不得为空';
                  $re['data']="";
                  echo json_encode($re);exit;
               }
              if(is_numeric($map['number'])=== false){
                  $re['status']='0';
                  $re['msg']='商品库存量只能为数字';
                  $re['data']="";
                  echo json_encode($re);exit;
              }
              $map['attr_symbol_path'] ='';
              foreach ($attr as $key => $value) {
                if(empty($data[$value['attr_key_id']][$k])){
                  $re['status']='0';
                  $re['msg']=$value['attr_name'].'属性不得为空';
                  $re['data']="";
                  echo json_encode($re);exit;
              }
              $map['attr_symbol_path'] .=$data[$value['attr_key_id']][$k].',';
              }
              $map['attr_symbol_path'] =rtrim($map['attr_symbol_path'],',');//去掉最右边的空格
               if($data['sku_id'][$k]){
                $map['sku_id'] = $data['sku_id'][$k];
                M('Item_sku')->save($map);
               }else{
                unset($map['sku_id']);
                M('Item_sku')->data($map)->add();
               }
            }
              $re['status']='1';
              $re['msg']="修改成功";
              $re['data']="";
              echo json_encode($re);exit;
          }else{
              $good_id =trim(I('get.good_id'));
              $good = M('Goods')->where(array('good_id'=>$good_id,'recycle'=>0))->find();
              if(!$good){
                $this->redirect('Shop/commodity');
              }
            $attr_key =M('Item_attr_key')->order('attr_key_id')->where(array('good_id'=>$good_id))->select();
            foreach ($attr_key as $k => $v) {
              $attr_val[$v['attr_key_id']] =M('Item_attr_val')->order('symbol')->where(array('attr_key_id'=>$v['attr_key_id']))->select();
              $attr[$v['attr_key_id']] =$v;    //  1 => 
              $attr[$v['attr_key_id']]['value']=$attr_val[$v['attr_key_id']];
            }
            $goods_attr =M('Item_sku')->where(array('good_id'=>$good_id,'status'=>1))->select();
            $this->assign('goods_attr',$goods_attr);
            $this->assign('attr',$attr);
            $this->display();
           }
          }

          public function commodity_attribute_delete(){
            if(IS_AJAX){
              $id['sku_id'] = trim(I('post.id'));
              $goods_attr =M('Item_sku')->where($id)->find();
              if($goods_attr){
                $res =M('Item_sku')->where($id)->save(array(
                    'status'  => '0',                
                ));
                if($res!==false){
                   if($goods_attr['attr_symbol_path']==''){
                    $goods =M('Goods')->where(array('good_id'=>$goods_attr['good_id']))->find();
                    if($goods['is_good_attr']==2){
                     $res_goods =M('Goods')->where(array('good_id'=>$goods_attr['good_id']))->save(array(
                      'recycle'  => '1',                
                    )); 
                    }
                  }
                  $this->ajaxReturn(1);
                }else{
                  $this->ajaxReturn(2);
                }  
              }else{
                $this->ajaxReturn(1);
              }
              // $res = M('Item_sku')->where($id)->delete();
              // if($res!==false){
              //   $this->ajaxReturn(1);
              // }else{
              //   $this->ajaxReturn(2);
              // }
            }
          }

          public function get_goods_attr(){
            if(IS_AJAX){
              $good_id =trim(I('get.good_id'));
              $attr =M('Item_attr_val')->where(array('good_id'=>$good_id))->find();
              if($attr){
              $re['status']='1';
              $re['msg']="";
              $re['data']="";
              echo json_encode($re);exit;
              }else{
              $re['status']='0';
              $re['msg']="请添加属性";
              $re['data']="";
              echo json_encode($re);exit;
              }
            }
          }
           
}