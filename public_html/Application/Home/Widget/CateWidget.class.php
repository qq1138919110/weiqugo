<?php
namespace Home\Widget;
use Think\Controller;
class CateWidget extends Controller {
   public function Menu(){
         $list =M('HomeMenu')->order('sort')->select();
         // var_dump($list);exit;
         $this->assign('list',$list);
		$this->display('Menu:menu');//加载视图
		}
}