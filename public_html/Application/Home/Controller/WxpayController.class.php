<?php
/* 
+------------------------------------------------------+
| 设计开发：Webster	Tel:17095135002	邮箱：312549912@qq.com	   |
+------------------------------------------------------+
*/
namespace Home\Controller;
use Think\Controller;
class WxpayController extends HomeController {
	//初始化
	public function _initialize()
	{
		// //获取来源地址
		 $URL['PHP_SELF'] = isset($_SERVER['PHP_SELF']) ? $_SERVER['PHP_SELF'] : (isset($_SERVER['SCRIPT_NAME']) ? $_SERVER['SCRIPT_NAME'] : $_SERVER['ORIG_PATH_INFO']);   //当前页面名称
		 $URL['DOMAIN'] = $_SERVER['SERVER_NAME'];  //域名(主机名)
		 $URL['QUERY_STRING'] = $_SERVER['QUERY_STRING'];   //URL 参数
		 $URL['URI'] = $URL['PHP_SELF'].($URL['QUERY_STRING'] ? "?".$URL['QUERY_STRING'] : "");
		 $this->fromurl = "http://".$URL['DOMAIN'].$URL['PHP_SELF'].($URL['QUERY_STRING'] ? "?".$URL['QUERY_STRING'] : ""); //完整URL地址
	}
	public function new_pay(){
		//全局引入微信支付类
		Vendor('Wxpay.WxPayPubHelper.WxPayPubHelper');
		//使用jsapi接口
        $order_id =I('order_id');
        if(empty($order_id)){
        	$this->error('非法错误');
        }
        $order =M('Chongzhi')->where(array('id'=>$order_id,'status'=>0))->find();
        if(!$order){
        	$this->error('非法错误');
        }
        //session('c_did',$cres['id']);
        //dump($cres);
        //die;
		 $jsApi = new \JsApi_pub();
		
        
		//=========步骤1：网页授权获取用户openid============
		//通过code获得openid
		if (!isset($_GET['code']))
		{
			//触发微信返回code码
			$url = $jsApi->createOauthUrlForCode(urlencode($this->fromurl));
			Header("Location: $url");
		}else
		{
			//获取code码，以获取openid
			$code = $_GET['code'];
			$jsApi->setCode($code);
			$openid = $jsApi->getOpenId();
		}
		 
		//=========步骤2：使用统一支付接口，获取prepay_id============
		//使用统一支付接口
		$unifiedOrder = new \UnifiedOrder_pub();

		
		//设置统一支付接口参数
		//设置必填参数
		//appid已填,商户无需重复填写
		//mch_id已填,商户无需重复填写
		//noncestr已填,商户无需重复填写
		//spbill_create_ip已填,商户无需重复填写
		//sign已填,商户无需重复填写
        
		$unifiedOrder->setParameter("openid",$openid);//商品描述
		$unifiedOrder->setParameter("body","冠驰π充值π币");//商品描述
		//自定义订单号，此处仅作举例
		// $timeStamp = time();
		// $out_trade_no = \WxPayConf_pub::APPID.$timeStamp;
        $out_trade_no =$order['order_id'];
        
		$unifiedOrder->setParameter("out_trade_no",$out_trade_no);//商户订单号
        
		$total_fee = $order['price'] *100;
		$unifiedOrder->setParameter("total_fee",$total_fee);//总金额
		$unifiedOrder->setParameter("notify_url",'http://p.gzguanchi.com/chongzhi.php');//通知地址
		$unifiedOrder->setParameter("trade_type","JSAPI");//交易类型
		//非必填参数，商户可根据实际情况选填
		//$unifiedOrder->setParameter("sub_mch_id","XXXX");//子商户号
		//$unifiedOrder->setParameter("device_info","XXXX");//设备号
		//$unifiedOrder->setParameter("attach","XXXX");//附加数据
		//$unifiedOrder->setParameter("time_start","XXXX");//交易起始时间
		//$unifiedOrder->setParameter("time_expire","XXXX");//交易结束时间
		//$unifiedOrder->setParameter("goods_tag","XXXX");//商品标记
		//$unifiedOrder->setParameter("openid","XXXX");//用户标识
		//$unifiedOrder->setParameter("product_id","XXXX");//商品ID
		
		$prepay_id = $unifiedOrder->getPrepayId();
		//=========步骤3：使用jsapi调起支付============
		$jsApi->setPrepayId($prepay_id);
		
		$jsApiParameters = $jsApi->getParameters();
		$this->assign('order',$order);
		$this->assign('jsApiParameters',$jsApiParameters);
		$this->display();
	}
	//JSAPI支付通知
	public function notify(){
		//使用通用通知接口
        Vendor('Wxpay.WxPayPubHelper.WxPayPubHelper');
		$notify = new \Notify_pub();
	
		//存储微信的回调
		$xml = $GLOBALS['HTTP_RAW_POST_DATA'];
		file_put_contents("8.txt",var_export($xml,true),8);
		$notify->saveData($xml);
	   
		//验证签名，并回应微信。
		//对后台通知交互时，如果微信收到商户的应答不是成功或超时，微信认为通知失败，
		//微信会通过一定的策略（如30分钟共8次）定期重新发起通知，
		//尽可能提高通知的成功率，但微信不保证通知最终能成功。
		if($notify->checkSign() == FALSE){
			$notify->setReturnParameter("return_code","FAIL");//返回状态码
			$notify->setReturnParameter("return_msg","签名失败");//返回信息
		}else{
			$notify->setReturnParameter("return_code","SUCCESS");//设置返回码
		}
		$returnXml = $notify->returnXml();
		echo $returnXml;
	
		//==商户根据实际情况设置相应的处理流程，此处仅作举例=======
	
		//以log文件形式记录回调信息
		//         $log_ = new Log_();
		$log_name= APP_ROOT."/Public/Weixin/notify_url.log";//log文件路径
	       
		//log_result($log_name,"【接收到的notify通知】:\n".$xml."\n");
	
		if($notify->checkSign() == TRUE)
		{
            $sn             = $notify->data['out_trade_no'];     //订单号
            $total_fee      = $notify->data['total_fee'];        //交易金额
            $order_time     = $notify->data['time_end'];         //交易完成时间
            $order_time     = strtotime($order_time);
            $modelOrder     = M('Order');
            $result = $modelOrder->where(array('order_no'=>$sn))->find();
            
			if ($notify->data["return_code"] == "FAIL") {
				//此处应该更新一下订单状态，商户自行增删操作
				//log_result($log_name,"【通信出错】:\n".$xml."\n");
                
			}
			elseif($notify->data["result_code"] == "FAIL"){
				//此处应该更新一下订单状态，商户自行增删操作
				//log_result($log_name,"【业务出错】:\n".$xml."\n");
               
			}
			else{    
				//此处应该更新一下订单状态，商户自行增删操作
				//log_result($log_name,"【支付成功】:\n".$xml."\n");
                $modelOrder->where(array('order_no'=>$sn))->save(array(
                    'pay_status'  => '1',                
                ));
                $order =M('Order')->where(array('order_no'=>$sn))->find();
                $shop =M('Shopping_cart')->where(array('order_no'=>$order['order_id']))->select();
	              foreach ($shop as $key => $value) {
	                $goods_id =$value['goods_id'];
	                $shop_goods[$value['id']] =M('Goods')->where(array('good_id'=>$goods_id))->find();
	                $goodsdata['good_id']      =$shop_goods[$value['id']]['good_id'];
	                $goodsdata['sales_amount'] =$shop_goods[$value['id']]['sales_amount']+$value['amount'];
	                $goodsdatares =M('Goods')->data($goodsdata)->save();
                  $sales['values']        =$shop_goods[$value['id']]['good_type'];
                  $sales['goods_id']      =$value['goods_id'];
                  $sales['sales_amount']  =$value['amount'];
                  $sales['values']        =$shop_goods[$value['id']]['good_value'];
                  $sales['order_id']      =$order['order_id'];
                  $sales['sales_time']    =time();
                  $salesres =M('Sales')->data($sales)->add();
                  }
			}
	
			//商户自行增加处理流程,
			//例如：更新订单状态
			//例如：数据库操作
			//例如：推送支付完成信息
		}
	}
	
}