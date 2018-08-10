<?php
// 检测输入的验证码是否正确，$code为用户输入的验证码字符串
function check_verify($code, $id = ''){
	$verify = new \Think\Verify();
	return $verify->check($code, $id);
}


// md5密钥加密
function md5_pwd($pwd){

	return md5(md5($pwd).MD5_PWD);
}
function array_tree($menu){
	foreach ($menu as $key => $value) {
          if($value['pid']==0){
            $data[$key] =$value;//拿到顶级的全部数据
            // var_dump($data[$key]);exit;
           unset($menu[$key]);
          foreach ($menu as $ke => $va) {
            if($va['pid']==$value['id']){//找到子级
              // var_dump($va);exit;
              $data[$key]['son'][$ke] =$va;//把他的子级全部数据赋值给$ke
              unset($menu[$ke]);
            // var_dump($data);

            }
          }
        }
        }
        return $data;
}
 function getTree(&$data,$pid = 0,$count = 1){ //$count 循环第几遍
          if(!isset($data['old'])){ //循环第一遍，把衣服把在新的(news)地方里去，剩下的注释掉，变成老的(old)
            $data=array('new'=>array(),'old'=>$data);
          }
        foreach ($data['old'] as $key => $value) {
          if($value['pid']==$pid){
            $value['count']=$count;
            $data['new'][$value['id']]=$value;
            unset($data['old'][$key]); //把循环得到的衣服注释掉,第二次循环不会循环到他
            getTree($data,$value['id'],$count+1);//调用自身
          }
        }

        return $data['new'];
      }
      /**
 * IsUsername函数:检测是否符合用户名格式
 * $Argv是要检测的用户名参数
 * $RegExp是要进行检测的正则语句
 * 返回值:符合用户名格式返回用户名,不是返回false
 */
 Function IsUsername($Argv){
 $RegExp='/^[a-zA-Z][a-zA-Z0-9_]{3,15}$/'; //由大小写字母跟数字组成并且长度在4-16字符直接
 return preg_match($RegExp,$Argv)?$Argv:false;
 }

 Function IsPassword($Argv){
 $RegExp='/^[a-zA-Z][a-zA-Z0-9_]{5,19}$/'; //由大小写字母跟数字组成并且长度在6-20字符直接
 return preg_match($RegExp,$Argv)?$Argv:false;
 }

 /**
 * IsMail函数:检测是否为正确的邮件格式
 * 返回值:是正确的邮件格式返回邮件,不是返回false
 */
 Function IsMail($Argv){
 $RegExp='/^[a-z0-9][a-z\.0-9-_]+@[a-z0-9_-]+(?:\.[a-z]{0,3}\.[a-z]{0,2}|\.[a-z]{0,3}|\.[a-z]{0,2})$/i';
 return preg_match($RegExp,$Argv)?$Argv:false;//preg_match('正则','要匹配的字符串')
 }

/**
 * IsSmae函数:检测参数的值是否相同
 * 返回值:相同返回true,不相同返回false
 */
 Function IsSame($ArgvOne,$ArgvTwo,$Force=false){
 return $Force?$ArgvOne===$ArgvTwo:$ArgvOne==$ArgvTwo;
 }

/**
* IsQQ函数:检测参数的值是否符合QQ号码的格式
 * 返回值:是正确的QQ号码返回QQ号码,不是返回false
 */
Function IsQQ($Argv){
 $RegExp='/^[1-9][0-9]{5,11}$/';
 return preg_match($RegExp,$Argv)?$Argv:false;
}

/**
* IsMobile函数:检测参数的值是否为正确的中国手机号码格式
 * 返回值:是正确的手机号码返回手机号码,不是返回false
*/
Function IsMobile($Argv){
 $RegExp='/^(?:13|15|18|17|14)[0-9]{9}$/';
 return preg_match($RegExp,$Argv)?$Argv:false;
}

/**
 * IsTel函数:检测参数的值是否为正取的中国电话号码格式包括区号
* 返回值:是正确的电话号码返回电话号码,不是返回false
*/
 Function IsTel($Argv){
 $RegExp='/[0-9]{3,4}-[0-9]{7,8}$/';
 return preg_match($RegExp,$Argv)?$Argv:false;
}

/**
* IsNickname函数:检测参数的值是否为正确的昵称格式(Beta)
 * 返回值:是正确的昵称格式返回昵称格式,不是返回false
*/
Function IsNickname($Argv){
 $RegExp='/^\s*$|^c:\\con\\con$|[%,\*\"\s\t\<\>\&\'\(\)]|\xA1\xA1|\xAC\xA3|^Guest|^\xD3\xCE\xBF\xCD|\xB9\x43\xAB\xC8/is'; //Copy From DZ
 return preg_match($RegExp,$Argv)?$Argv:false;
}

/**
 * IsChinese函数:检测参数是否为中文
 * 返回值:是返回参数,不是返回false
*/
Function IsChinese($Argv,$Encoding='utf8'){
 $RegExp = $Encoding=='utf8'?'/^[\x{4e00}-\x{9fa5}]+$/u':'/^([\x80-\xFF][\x80-\xFF])+$/';
Return preg_match($RegExp,$Argv)?$Argv:False;
}
function get_goods_title($id,$name='name'){
  if(!$id){
    return '';
  }else{
    $data['id'] =$id;
    $res=M('Goods')->field($name)->where($data)->find();
    return $res[$name];
  }
}
//数字转中文
function numToWord($num)
{
$chiNum = array('零', '一', '二', '三', '四', '五', '六', '七', '八', '九');
$chiUni = array('','十', '百', '千', '万', '亿', '十', '百', '千');

$chiStr = '';

$num_str = (string)$num;

$count = strlen($num_str);
$last_flag = true; //上一个 是否为0
$zero_flag = true; //是否第一个
$temp_num = null; //临时数字

$chiStr = '';//拼接结果
if ($count == 2) {//两位数
$temp_num = $num_str[0];
$chiStr = $temp_num == 1 ? $chiUni[1] : $chiNum[$temp_num].$chiUni[1];
$temp_num = $num_str[1];
$chiStr .= $temp_num == 0 ? '' : $chiNum[$temp_num];
}else if($count > 2){
$index = 0;
for ($i=$count-1; $i >= 0 ; $i--) {
$temp_num = $num_str[$i];
if ($temp_num == 0) {
if (!$zero_flag && !$last_flag ) {
$chiStr = $chiNum[$temp_num]. $chiStr;
$last_flag = true;
}
}else{
$chiStr = $chiNum[$temp_num].$chiUni[$index%9] .$chiStr;

$zero_flag = false;
$last_flag = false;
}
$index ++;
}
}else{
$chiStr = $chiNum[$num_str[0]];
}
return $chiStr;
}
function alipayy($post_data){
// require_once("alipay.config.php");
// require_once("lib/alipay_submit.class.php");//文件夹直接用点，文件名之间用#,.php直接省略
// vendor('aililili.alipay#config');
vendor('alilili.lib.alipay_submit#class');
// var_dump($alipay_config['partner']);exit;
/************************************************配置信息*/
$alipay_config['partner']   = '2088911848594940';

//收款支付宝账号
$alipay_config['seller_email']  = '2355637280@qq.com';

//安全检验码，以数字和字母组成的32位字符
$alipay_config['key']     = '7uf7gh3mptulhzt3cvyc6e9hp452qpsi';


//↑↑↑↑↑↑↑↑↑↑请在这里配置您的基本信息↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑


//签名方式 不需修改
$alipay_config['sign_type']    = strtoupper('MD5');

//字符编码格式 目前支持 gbk 或 utf-8
$alipay_config['input_charset']= strtolower('utf-8');

//ca证书路径地址，用于curl中ssl校验
//请保证cacert.pem文件在当前文件夹目录中
$alipay_config['cacert']    = getcwd().'\\cacert.pem';

//访问模式,根据自己的服务器是否支持ssl访问，若支持请选择https；若不支持请选择http
$alipay_config['transport']    = 'http';
/**************************请求参数**************************/

        //支付类型
        $payment_type = "1";
        //必填，不能修改
        //服务器异步通知页面路径    客户支付时 订单状态发生改变  立刻告诉我
        $notify_url = "http://localhost/thinkphp/index.php/Home/User/pay_ts.html";
        //需http://格式的完整路径，不能加?id=123这类自定义参数
        // 成功 我们  返回 'success' 字符串给支付宝  失败 返回 'fail'
        // echo "success";        //请不要修改或删除
        //         }else {                 //验证失败
        //         echo "fail";

        //页面跳转同步通知页面路径    支付后返回的页码
        $return_url = "http://localhost/thinkphp/index.php/Home/User/pay_s.html";
        //需http://格式的完整路径，不能加?id=123这类自定义参数，不能写成http://localhost/

        //商户订单号
        $out_trade_no = $post_data['order_id'];//$_POST['WIDout_trade_no'];
        //商户网站订单系统中唯一订单号，必填

        //订单名称
        $subject = $post_data['goods_name'];//$_POST['WIDsubject'];
        //必填

        //付款金额
        $total_fee = $post_data['allmoney'];//$_POST['WIDtotal_fee'];
        //必填

        //订单描述

        $body = $post_data['goods_intro'];//$_POST['WIDbody'];
        //商品展示地址
        $show_url = $post_data['goods_url'];// $_POST['WIDshow_url'];
        //需以http://开头的完整路径，例如：http://www.商户网址.com/myorder.html

        //防钓鱼时间戳
        $anti_phishing_key = "";
        //若要使用请调用类文件submit中的query_timestamp函数

        //客户端的IP地址
        $exter_invoke_ip = "";
        //非局域网的外网IP地址，如：221.0.0.1


/************************************************************/

//构造要请求的参数数组，无需改动
$parameter = array(
    "service" => "create_direct_pay_by_user",
    "partner" => trim($alipay_config['partner']),
    "seller_email" => trim($alipay_config['seller_email']),
    "payment_type"  => $payment_type,
    "notify_url"  => $notify_url,
    "return_url"  => $return_url,
    "out_trade_no"  => $out_trade_no,
    "subject" => $subject,
    "total_fee" => $total_fee,
    "body"  => $body,
    "show_url"  => $show_url,
    "anti_phishing_key" => $anti_phishing_key,
    "exter_invoke_ip" => $exter_invoke_ip,
    "_input_charset"  => trim(strtolower($alipay_config['input_charset']))
);
// var_dump($parameter);
// exit;

//建立请求
$alipaySubmit = new AlipaySubmit($alipay_config);
$html_text = $alipaySubmit->buildRequestForm($parameter,"get", "确认");
// var_dump($html_text);
return $html_text;

}
function  log_result($file,$word)
{
    $fp = fopen($file,"a");
    flock($fp, LOCK_EX) ;
    fwrite($fp,"执行日期：".strftime("%Y-%m-%d-%H：%M：%S",time())."\n".$word."\n\n");
    flock($fp, LOCK_UN);
    fclose($fp);
}
function get_logistics($no,$com){
    $key = 'ddcde627ebd546079aaa619420497082'; //调用接口的唯一key
    // $no =  快递单号
    // $com =  公司名称
    $url = 'http://apis.haoservice.com/lifeservice/exp?com='.$com.'&no='.$no.'&key='.$key;
    $res = file_get_contents($url);
    return $res;
}
/**
 * 远程获取数据，POST模式
 * 注意：
 * 1.使用Crul需要修改服务器中php.ini文件的设置，找到php_curl.dll去掉前面的";"就行了
 * @param string $url 指定URL完整路径地址
 * @param string $para 请求的数据
 * @param $input_charset 编码格式。默认值：空值
 * * return 远程输出的数据
 */
function getHttpResponsePOST1($url, $para,$input_charset='' ) {
    if (empty($url) || empty($para)) {
        return false;
    }
    if (trim($input_charset) != '') {
        $url = $url."_input_charset=".$input_charset;
    }
    $postUrl = $url;
    $curlPost = $para;

    $ch = curl_init();//初始化curl
    curl_setopt($ch, CURLOPT_URL,$postUrl);//抓取指定网页
    curl_setopt($ch, CURLOPT_HEADER, 0);//设置header
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);//要求结果为字符串且显示输出
    curl_setopt($ch, CURLOPT_POST, 1);//post提交方式
    curl_setopt($ch, CURLOPT_POSTFIELDS, $curlPost);// post传输数据
    $data = curl_exec($ch);//运行curl
    curl_close($ch);

    return $data;
}

function getHttpResponseGET1($no,$com,$input_charset='' ) {
    // if (empty($url) || empty($para)) {
    //     return false;
    // }
    $key = 'ddcde627ebd546079aaa619420497082'; //调用接口的唯一key

    if (trim($input_charset) != '') {
        $url = $url."_input_charset=".$input_charset;
    }
    // $postUrl = $url;
    // $curlPost = $para;
    // $url.= '?='
    $url = 'http://apis.haoservice.com/lifeservice/exp?com='.$com.'&no='.$no.'&key='.$key;

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "$url");
    curl_setopt($ch, CURLOPT_TIMEOUT, 2);
    curl_setopt($ch, CURLOPT_HEADER, 0); //如果设为0，则不使用header
    curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
    $data = curl_exec($ch);
    curl_close($ch);
// var_dump($data);
    return $data;
}

    //发送验证码到手机
    function send_sms($phone){
        include_once "./Application/Home/Library/Ucpaas.class.php";
          $where['user_phone']=$phone;
          //$user_info=m('home_user')->field('id')->where($where)->find();
          $user_info=m('home_user')->where($where)->find();
          $user_info=$user_info?$user_info:0;
          if(!$user_info){
            $user_info['id']=0;
          }
        //初始化必填
        //$phone=i('post.phone');
        $options['accountsid']='79fa050e10b3af28076a182f1fcae761'; //填写自己的
        $options['token']='c96dfcf22e3769afcfef39d7d788c958'; //填写自己的
        //初始化 $options必填
        //import("Org.Util.Ucpaas");
        $ucpass = new \Ucpaas($options);
        //随机生成6位验证码
        srand((double)microtime()*1000000);//create a random number feed.
        $ychar="0,1,2,3,4,5,6,7,8,9";
        $list=explode(",",$ychar);
        for($i=0;$i<6;$i++){
          $randnum=rand(0,9); // 10+26;
          $authnum.=$list[$randnum];
        }
        //短信验证码（模板短信）,默认以65个汉字（同65个英文）为一条（可容纳字数受您应用名称占用字符影响），超过长度短信平台将会自动分割为多条发送。分割后的多条短信将按照具体占用条数计费。
        $appId = "5b1ec5587b034786aa80640782ac0b10";  //填写自己的
        //$to = $phone;

        $templateId = "35031";
        $param=$authnum;
        //dump($appId.','.$to.','.$templateId.','.$param);
        $arr=$ucpass->templateSMS($appId,$phone,$templateId,$param);
        //file_put_contents('sms.txt',$arr);
        //file_put_contents('verify.txt',$arr);
        $arr=json_decode($arr,true);
        //dump($arr);
        if($arr['resp']['respCode']=='000000'){
          $data['verify']=$param;
          $data['user_id']=$user_info['id']?$user_info['id']:0;
          $data['add_time']=time();
          $data['phone']=$phone;
          //$data['use_time']=0;
          //$data['status']=0;
          $data['remark']=i('post.remark');
          //dump($data);
          //file_put_contents('new_verify.txt',json_encode($data));
          $re=m('verify_sms')->add($data);
          //dump(m('verify_sms'));
          $re['status']='1';
            $re['msg']="发送成功请留意短信";
            $re['data']="";
            echo json_encode($re);exit;
        }else{
            $re['status']='0';
            $re['msg']="暂时不能发送短信";
            $re['data']="";
            echo json_encode($re);exit;
        }
      }

      //curl get
      function curl_get($url){
        $ch = curl_init();
        // 2. 设置选项，包括URL
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);

        //

        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);


        // 3. 执行并获取HTML文档内容
        $output = curl_exec($ch);
        // 4. 释放curl句柄
        curl_close($ch);
        return   $output;
    }

    //curl post
    function curl_post($url,$post){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

        // 我们在POST数据哦！
        curl_setopt($ch, CURLOPT_POST, 1);
        // 把post的变量加上
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
        $output = curl_exec($ch);
        curl_close($ch);
        return $output;
    }

		//获取code
		function code($appid,$appsecret){
				//dump("test");exit;
			 //$re_http="http://weiqugou.php-study.com/index.php?s=/Home/Selected";
			 $re_http='http://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'].'?'.$_SERVER['QUERY_STRING'];;
			 $re_http_encode=urlencode($re_http);
			 $http="https://open.weixin.qq.com/connect/oauth2/authorize?appid=".$appid."&redirect_uri=".$re_http_encode."&response_type=code&scope=snsapi_base&state=123#wechat_redirect";
			 header("Location:".$http);
			 //echo $http;
			 //$re_data=curl_get($http);
			 //file_put_contents('code.txt',$re_data);exit;
			 //dump($re_data);exit;
	 }

	 //获取openid
	 function openid($appid,$appsecret,$openid){
			$GLOBALS['token']=get_token($appid,$appsecret);
			$user_info_wechat=curl_get('https://api.weixin.qq.com/cgi-bin/user/info?access_token='.$GLOBALS['token'].'&openid='.$openid.'&lang=zh_CN');
			$user_info_wechat=$user_info_wechat?$user_info_wechat:'';
			return $user_info_wechat;

	 }


	 //判断该用户已经关注了公众号，如果没有则弹出关注公众号的二维码
		function is_concerns($user_info_wechat,$openid=""){
			 $user_info_wechat_array=json_decode($user_info_wechat,true);
			 //dump($user_info_wechat_array);
			 if(!isset($_COOKIE['login_user_id'])){
					 header("Location:index.php?s=/Home/User/login".$GLOBALS['puid_url']);exit;
			 }else{
					 //查询该用户是否已经登录，如果没有关注则弹出让其关注的弹框
					 $where['id']=$_COOKIE['login_user_id'];
					 if($_COOKIE['login_user_id']!=""){
									$user_info=m('home_user')->where($where)->find();
									 if($user_info['openid']!=""){
											 //如果不为空则是已经关注过
											 $GLOBALS['is_concerns']=0;

									 }else{
											 $GLOBALS['is_concerns']=1;
											 //如果为空则是没有关注过,添加openid进home_user数据库表
											 $data['openid']=$openid;
											 m('home_user')->where($where)->save($data);

									 }
									 //判断该用户是否已经关注start
									 if($user_info_wechat_array['subscribe']==0){
													 //该用户未关注公众号
													 header("Location:./code.jpg");exit;
									 }
									 //判断该用户是否已经关注end
							 }else{
									 header("Location:index.php?s=/Home/User/login".$GLOBALS['puid_url']);exit;
							 }

			 }
			 return true;
	 }

	 //获取基础access_token
	 function get_token($appid,$appsecret){
                 if(!file_exists("../access_token_notweb.txt")){
                            file_put_contents("../access_token_notweb.txt",i('get.'));
                 }

			 $shuzu=array('');
			 $put_contents="";
			 $url='https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid='.$appid.'&secret='.$appsecret.'';
			 $access_token_json=file_get_contents("../access_token_notweb.txt");
			 if($access_token_json!=""){
				 $json_token=json_decode($access_token_json,true);
			 }
			 if($access_token_json!="" and (isset($json_token['access_token']))){
							 $json_token=json_decode($access_token_json,true);
							 if(isset($json_token['time'])){
											 if(time()-$json_token['time']>7100){
																			 $jieguo=curl_get($url);
																			 if($jieguo){
																							 $shuzu=json_decode($jieguo,TRUE);
																							 $shuzu['time']=time();
																							 $put_contents=json_encode($shuzu);
																							 file_put_contents("../access_token_notweb.txt",$put_contents);
																			 }
											 }else{
															 $json_token=json_decode($access_token_json,true);
															 $shuzu=$json_token;
											 }
							 }else{
														$jieguo=curl_get($url);
														if($jieguo){
																	 $shuzu=json_decode($jieguo,TRUE);
																	 $shuzu['time']=time();
																	 $put_contents=json_encode($shuzu);
																	 file_put_contents("../access_token_notweb.txt",$put_contents);
														 }
							 }
			 }else{
							 $jieguo=curl_get($url);
														if($jieguo){
																	 $shuzu=json_decode($jieguo,TRUE);
																	 $shuzu['time']=time();
																	 $put_contents=json_encode($shuzu);
																	 file_put_contents("../access_token_notweb.txt",$put_contents);
														 }
			 }

			 //var_dump($jieguo);exit;
			 $token=$shuzu['access_token'];
			 return $token;
}

?>
