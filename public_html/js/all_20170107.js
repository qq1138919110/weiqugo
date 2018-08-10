//*all.js in here
//* Copyright 2016-11-03 , SMW
// toggle方法
$.fn.meToggle = function( fn, fn2 ) {
    var args = arguments,guid = fn.guid || $.guid++,i=0,
    meToggle = function( event ) {
      var lastToggle = ( $._data( this, "lastToggle" + fn.guid ) || 0 ) % i;
      $._data( this, "lastToggle" + fn.guid, lastToggle + 1 );
      event.preventDefault();
      return args[ lastToggle ].apply( this, arguments ) || false;
    };
    meToggle.guid = guid;
    while ( i < args.length ) {
      args[ i++ ].guid = guid;
    }
    return this.click( meToggle );
};

 // 免费兑换首页JS
function indexJs () {
	// 判断高度last-a
	var lastHeight = $(".last-a").siblings('a').height();
	$(".last-a").height(lastHeight);
	// 搜索框
	$("#search-input").click(function() {
		$(this).siblings('div').hide();
	});
	$(".search > div").click(function() {
		$(this).hide();
		$(this).siblings('input').focus();
	});
}

// 回到顶部
function goTop () {
	var go_top ='<div class="goTop"><img src="images/go-top.png" alt=""></div>';
    $("body").append(go_top);

	$(window).scroll(function() {
		$(".goTop").show();
	});
	$(".goTop").click(function() {
		$('html,body').animate({'scrollTop':0},600);
	}); 
}

// // 签到
// function sign () {
	
// 	var Sign = '<div class="signImg footer-max"><img src="images/qiandao.png" alt=""><p>恭喜您获得11积分</p></div>'
// 	var windowHeight = $(window).height();//获取界面可视高度
// 	$("body").append(Sign);
// 	$(".signImg").height($("body").height());
// 	// 一次性点击事件
// 	function oneSign () {
// 		$(".signImg").show();
// 		var imgHeight = $(".signImg img").height();
// 		$(".signImg img").css('marginTop', windowHeight / 2 - imgHeight);
// 		setTimeout(function () {
// 	       $(".signImg").hide();
// 	    }, 3000)
// 	    $(this).text("已签到");
// 	}
// 	$("#sign").one("click",oneSign);
// 	wechatImg (".personal-service");
// }
// 签到
function sign (score) {
	//console.log(score);
	//console.log(score.is_day);
	if(score.is_day==1){
		var Sign = '<div class="signImg footer-max"><img src="images/qiandao.png" alt=""><p>恭喜您获得'+score.add_score+'积分</p></div>';
		$("#score").html(score.totai_score);
	}else{
		var Sign = '<div class="signImg footer-max"><img src="images/qiandao.png" alt=""><p>恭喜您签到成功</p></div>'
		
	}
	
	//var Sign = '<div class="signImg footer-max"><img src="images/qiandao.png" alt=""><p>恭喜您获得'+score.add_score+'积分</p></div>'
	var windowHeight = $(window).height();//获取界面可视高度
	$("body").append(Sign);
	$(".signImg").height($("body").height());
	// 一次性点击事件
	//function oneSign () {
		$(".signImg").show();
		var imgHeight = $(".signImg img").height();
		$(".signImg img").css('marginTop', windowHeight / 2 - imgHeight);
		setTimeout(function () {
	       $(".signImg").hide();
	    }, 3000)
	    $("#sign").text("已签到");
	    $("#sign").removeAttr('onClick');
	//}
	//$()
	//$("#sign").one("click",oneSign);
	wechatImg (".personal-service");
}


// 服务二维码
function wechatImg (clickObject) {
	var weChat = '<div class="chatImg footer-max"><div><img src="images/76277338195398721.jpg" alt=""><p>长按二维码添加客服</p><i class="iconfont">&#xe7cc;</i></div></div>'
	var windowHeight = $(window).height();//获取界面可视高度
	$("body").append(weChat);
	$(".chatImg").height($("body").height());
	$(clickObject).click(function() {
		$(".chatImg").show();
		var imgHeight = $(".chatImg div").height();
		$(".chatImg div").css('marginTop', windowHeight / 2 - imgHeight);
	});
	$(".chatImg i").click(function(event) {
		$(".chatImg").hide();
	});
}

// 加减
function add_reduce () {
	$(".reduce").click(function() {
		var oldres = $(this).siblings('.number').text();
  		var result = Number(oldres) - 1;
  		if ( result <= 0) {
			$(this).css('backgroundColor', '#ebebeb');
			return false;
		}
		$(this).siblings('.number').text(result);
		
	});

	$(".add").click(function() {
		var oldres = $(this).siblings('.number').text();
	  	var result = Number(oldres) + 1;
		$(this).siblings('.number').text(result);
		if ( result >= 1) {
			$(this).siblings(".reduce").css('backgroundColor', '');
		}
	});
}

// 购物车选中
function cart_circle(){
	$(".cart-main input[type=checkbox]").click(function() {
		var	priceRes = 0;
		var all_fraction = $(".all-fraction").text();
		var integral = $(this).parents('li').find(".fraction").text();
		var number = $(this).parents('li').find(".number").text();

	 	if ($(this).siblings('label').hasClass('checkColor')){
          	$(this).siblings('label').removeClass('checkColor');
          	$(this).removeAttr('checked', 'checked');
          	$(".footer-max input").siblings('label').removeClass('checkColor');
          	$(".footer-max input").removeAttr('checked', 'checked');
          	$(".cart-cancel > span").text('全部选择')
          	priceRes = Number(all_fraction) - integral * number;
 		} else{
          	$(this).siblings('label').addClass('checkColor');
          	$(this).attr('checked', 'checked');
          	priceRes = Number(all_fraction) + integral * number;
 		}
 		$(".all-fraction").text(Math.round(priceRes*100)/100);
	});

	$(".footer-max input").click(function() {
		var cart_length = $(".cart-main li").length;
		var all_fraction = $(".all-fraction").text();
		var	priceRes = 0;
		
		if ($(this).siblings('label').hasClass('checkColor')) {
          	$('label').removeClass('checkColor');
          	$(this).parents('.checkboxFour').siblings('span').text('全部选择');
          	$(".footer-max input").removeAttr('checked', 'checked');
          	$(".cart-main input[type=checkbox]").removeAttr('checked', 'checked');
          	for(var i = 0;i < cart_length;i++){
				var integral = $(".fraction").eq(i).text();
				var number = $(".number").eq(i).text();
				priceRes = 0;
			}
 		} else{
          	$('label').addClass('checkColor');
          	$(this).parents('.checkboxFour').siblings('span').text('全部取消');
          	$(".footer-max input").attr('checked', 'checked');
          	$(".cart-main input[type=checkbox]").attr('checked', 'checked');
          	for(var i = 0;i < cart_length;i++){
				var integral = $(".fraction").eq(i).text();
				var number = $(".number").eq(i).text();
				priceRes = priceRes + integral * number;
			}
 		}
 		$(".all-fraction").text(Math.round(priceRes*100)/100);
	});
	$("#jiesuan").click(function() {
		var cart_lengt = $(".cart-main li").length;
		var kk = "";
		for (var i = 0; i < cart_lengt; i++) {
			var bb = $(".cart-main li").eq(i).attr('cart');
			if ($(".cart-main li").eq(i).find('.checkboxFour > input').attr('checked') == 'checked') {
				if (kk=="") {
					kk = bb;
				}else{
					kk = kk+','+bb;
				}
			}
		}
		//console.log(kk);
		submit_cart_new(kk);
		// $.ajax({
		// 	url: 'index.php?s=/Home/Order/submit_cart',
		// 	type: 'post',
		// 	dataType: 'json',
		// 	data: {id: kk},
		// 	success:function(data){
		// 		console.info(data);
		// 	}
		// })
	});
}

// 计算选中积分
function choice_circle() {
	// $(".reduce").click(function() {
	// 	var oldres = $(this).siblings('.number').text();
 // 		var result = Number(oldres) - 1;
 // 		$(this).siblings('.number').text(result);
 // 		if ( result <= 1) {
	// 		$(this).attr('disabled','disabled');
	// 	}

	// 	var choiceTrue = $(this).parents('li').find('.checkboxFour input').attr('checked');
	// 	var	priceRes = 0;
	// 	var all_fraction = $(".all-fraction").text();
	// 	var integral = $(this).parents('li').find(".fraction").text();
	// 	var number = $(this).parents('li').find(".number").text();
	// 	if (choiceTrue == 'checked') {
	// 		priceRes = Number(all_fraction) - Number(integral);
	// 		$(".all-fraction").text(Math.round(priceRes*100)/100);	
	// 	} else{
	// 		$(".all-fraction").text(Math.round(all_fraction*100)/100);	
	// 	}
		
	// });

	// $(".add").click(function() {
	// 	var oldres = $(this).siblings('.number').text();
	//   	var result = Number(oldres) + 1;
	// 	$(this).siblings('.number').text(result);
	// 	if ( result > 1) {
	// 		$(this).siblings(".reduce").removeAttr('disabled');
	// 	}

	// 	var choiceTrue = $(this).parents('li').find('.checkboxFour input').attr('checked');
	// 	var	priceRes = 0;
	// 	var all_fraction = $(".all-fraction").text();
	// 	var integral = $(this).parents('li').find(".fraction").text();
	// 	var number = $(this).parents('li').find(".number").text();
	// 	if (choiceTrue == 'checked') {
	// 		priceRes = Number(all_fraction) + Number(integral);
	// 		$(".all-fraction").text(Math.round(priceRes*100)/100);	
	// 	} else{
	// 		$(".all-fraction").text(Math.round(all_fraction*100)/100);	
	// 	}
	// });
	$(".reduce").click(function() {
		var oldres = $(this).siblings('.number').text();
  		var result = Number(oldres) - 1;
  		if ( result <= 0) {
			$(this).css('backgroundColor', '#ebebeb');
			return false;
		}
		$(this).siblings('.number').text(result);

		var choiceTrue = $(this).parents('li').find('.checkboxFour input').attr('checked');
		var	priceRes = 0;
		var all_fraction = $(".all-fraction").text();
		var integral = $(this).parents('li').find(".fraction").text();
		var number = $(this).parents('li').find(".number").text();
		if (choiceTrue == 'checked') {
			priceRes = Number(all_fraction) - Number(integral);
			$(".all-fraction").text(Math.round(priceRes*100)/100);	
		} else{
			$(".all-fraction").text(Math.round(all_fraction*100)/100);	
		}
		
	});

	$(".add").click(function() {
		var oldres = $(this).siblings('.number').text();
	  	var result = Number(oldres) + 1;
		$(this).siblings('.number').text(result);
		if ( result >= 1) {
			$(this).siblings(".reduce").css('backgroundColor', '');
		}

		var choiceTrue = $(this).parents('li').find('.checkboxFour input').attr('checked');
		var	priceRes = 0;
		var all_fraction = $(".all-fraction").text();
		var integral = $(this).parents('li').find(".fraction").text();
		var number = $(this).parents('li').find(".number").text();
		if (choiceTrue == 'checked') {
			priceRes = Number(all_fraction) + Number(integral);
			$(".all-fraction").text(Math.round(priceRes*100)/100);	
		} else{
			$(".all-fraction").text(Math.round(all_fraction*100)/100);	
		}
	});

	$(".cart-delete").click(function(event) {
		$(this).parents('li').remove();
		var choiceTrue = $(this).parents('li').find('.checkboxFour input').attr('checked');
		var	priceRes = 0;
		var all_fraction = $(".all-fraction").text();
		var integral = $(this).parents('li').find(".fraction").text();
		var number = $(this).parents('li').find(".number").text();
		if (choiceTrue == 'checked') {
			priceRes = Number(all_fraction) - integral * number;
			$(".all-fraction").text(Math.round(priceRes*100)/100);	
		}
	});
}

//3秒后弹窗关闭
function alert_time() {
    $(".alert-message").show();
  	setTimeout(function () {
        $(".alert-message").hide();
    }, 3000)
}

// 商品详情JS
function product_detail() {
	$(".main-detail .detail2").show().siblings('ul').hide();
	$(".product-main > ul > li").click(function() {
		var li_idx = $(this).index();
		$(this).addClass('active').siblings().removeClass('active');
		$(".main-detail").children('ul').eq(li_idx).show().siblings('ul').hide();
	});

	$(".attribute span").click(function() {
		$(this).addClass('active').siblings().removeClass('active');
		var attrs = $(".attribute");//获取有多少个attribute;
		$("#has-choice span").text('');
		var attr_val="";
		for(var i = 0;i < attrs.length;i++){
			var spans = $(".attribute").eq(i).find('span').length;
			for(var j = 0;j < spans;j++){
				var temp = $(".attribute").eq(i).find('span').eq(j).attr("class");
				if( temp == 'active'){
					var temp_text = $(".attribute").eq(i).find('span').eq(j).html();
					$("#has-choice span").append('"'+temp_text+'"'+" ");
					//根据所选获取库存与积分start
					
						//if (attr_val == "") {
						//	attr_val = $(".attribute span").eq(i).attr('attr_val');
						// }else{
						// 	attr_val = attr_val +','+$(".attribute span").eq(i).attr('attr_val');
						// }
					
					
					//根据所选获取库存与积分end
				}
			}
		}
		//重新判断start
		var goods_id = $("#goods_id").val();
		var number = $(".cart-integral .number").text();
		var attr_length = $(".attribute span").length;
		var attr_val="";
		var get_attr_number=0;
		var attr_number=$("#attr_number").val();
		for(var i = 0;i < attr_length;i++){
			if ($(".attribute span").eq(i).attr('class') == 'active') {
				get_attr_number=get_attr_number+1;
				if (attr_val == "") {
					attr_val = $(".attribute span").eq(i).attr('attr_val');
				}else{
					attr_val = attr_val +','+$(".attribute span").eq(i).attr('attr_val');
				}
				
			}
		}
		if(get_attr_number==attr_number){
			$.ajax({
			      url: 'index.php?s=/Home/Selected/get_stock',
			      type: 'get',
			      dataType: 'json',
			      data: {goods_id:goods_id,attr:attr_val},
			      success:function(msg){
			      	//alert(123);
			        //console.log(msg);
			        $('#price_main').html(msg.data.item_sku_array.money+'积分'+'&nbsp;&nbsp;&nbsp;剩余库存:'+msg.data.item_sku_array.number);
			        
			      }
			    });
		}
		//重新判断end
		
	});

	$(".product-name .pull-right").click(function(event) {
		$(".alert-share").show();
	  	setTimeout(function () {
	        $(".alert-share").hide();
	    }, 3000)
	});

	// 提交购物车数据
	$(".cart-btn button").click(function() {
		$(".wrap").hide();
		var puid=$("#puid").val();
		var goods_id = $("#goods_id").val();
		var number = $(".cart-integral .number").text();
		var attr_length = $(".attribute span").length;
		var attr_val="";
		for(var i = 0;i < attr_length;i++){
			if ($(".attribute span").eq(i).attr('class') == 'active') {
				if (attr_val == "") {
					attr_val = $(".attribute span").eq(i).attr('attr_val');
				}else{
					attr_val = attr_val +','+$(".attribute span").eq(i).attr('attr_val');
				}
				
			}
		}
		var diy_url=$('#diy_url').val();
		$.ajax({
			//url: 'index.php?s=/Home/Order/add_cart',
			url: diy_url,
			type: 'post',
			dataType: 'json',
			data: {goods_id: goods_id,number: number,attr:attr_val},
			success:function (data) {
				//console.info(data);
				if(data.status==999){
					top.location='index.php?s=/Home/User/login'+puid; 
				}
				if(data.status==1){
					
					alert_msg('已加入购物车');
					
				}else{
					if(data.msg!=""){
						alert_msg(data.msg);
					}else{
						alert_msg('加入购物车失败');
					}
					
				}
				
			}
		})
	});

	$("#redeem-now").click(function(event) {
		$(".wrap").show();
		$(".alert-cart").show();
		$(".cart-btn").html('<button onClick="add_order_two()">立即兑换</button>');
		$(".cart-btn button").css({
			'background-color': '#FFE622',
			'color': '#000'
		});
	});

	$("#join-cart").click(function() {
		$(".wrap").show();
		$(".alert-cart").show();
		$(".cart-btn button").text('加入购物车');
		$(".cart-btn button").css({
			'background-color': '',
			'color': ''
		});
	});
	$("#close-cart").click(function() {
		$(".alert-cart").hide();
	});
}
// 足迹
function chioce_footprint(){
 	$(".footprint dt input").on('click', function() {
 		if ($(this).siblings('label').hasClass('checkColor')) {
          	$(this).siblings('label').removeClass('checkColor');
          	$(this).parents('dt').siblings('dd').find('label').removeClass('checkColor');
          	$(".footprint-footer .checkboxFour label").removeClass('checkColor');
          	$(this).removeAttr('checked', 'checked');
          	$(this).parents('dt').siblings('dd').find('input').removeAttr('checked', 'checked');
 		} else{
          	$(this).siblings('label').addClass('checkColor');
          	$(this).parents('dt').siblings('dd').find('label').addClass('checkColor');
          	$(this).attr('checked', 'checked');
          	$(this).parents('dt').siblings('dd').find('input').attr('checked', 'checked');
 		}
 	});
 	$(".footprint dd input").on('click', function(){
 		if ($(this).siblings('label').hasClass('checkColor')) {
          	$(this).siblings('label').removeClass('checkColor');
          	$(this).parents('dd').siblings('dt').find('label').removeClass('checkColor');
          	$(".footprint-footer .checkboxFour label").removeClass('checkColor');
          	$(this).removeAttr('checked', 'checked');
          	$(this).parents('dd').siblings('dt').find('input').removeAttr('checked', 'checked');
 		} else{
          	$(this).siblings('label').addClass('checkColor');
          	$(this).attr('checked', 'checked');
 		}
 	});
 	$(".footprint-footer .checkboxFour input").on('click', function(){
 		if ($(this).siblings('label').hasClass('checkColor')) {
          	$('label').removeClass('checkColor');
          	$(this).parents('.checkboxFour').siblings('span').text('全部选择');
          	$(".footprint input").removeAttr('checked', 'checked');
 		} else{
          	$('label').addClass('checkColor');
          	$(this).parents('.checkboxFour').siblings('span').text('全部取消');
          	$(".footprint input").attr('checked', 'checked');
 		}
 	});

 	$("#footprint-delete").click(function() {
 		var cs = $(".container").find('.checkboxFour');
 		for(var i=0;i<cs.length;i++){
 			var checked = cs.eq(i).find('input').eq(0).attr("checked");
 			if(checked == "checked"){
 				cs.eq(i).parent().remove();
 			}
 		}
 		for(var j=0;j<$('.footprint').length;j++){
 			if ($('.footprint').eq(j).find('dd').length == 0) {
 				$('.footprint').eq(j).find('dt').remove();
 			}
 		}
 		
 	});
}

// 购物车选中
function cart_choice () {
	$(".find-detail li > img").meToggle(function() {
		$(this).attr('src', 'images/gouwuche.png');
	}, function() {
		$(this).attr('src', 'images/gouwuche1.png');
	});  
	
}

// 找回密码
function find_password () {
	$('#send-code').click(function(event) {
	  	var sum = 60;
	    var stop;
	    stop = setInterval(function () {
	    sum--;
	      if (sum!=0){
	        $('#send-code').attr('disabled', true);
	        $('#send-code').val('（'+sum+'）秒后再次获取');
	      }else {
	        clearInterval(stop);
	        $('#send-code').attr('disabled', false);
	        $('#send-code').val('获取手机验证码');
	      }
	    }, 1000);
	 }); 
}

// 收货地址管理
function receiptAddress () {
	$(".delete").click(function() {
		$(this).parents(".sumbit-order").remove();
	});

	$(".receipt-address .checkboxFour input").on('click', function(){
			var  address_id =$(this).prev('input').val();
			$.ajax({
				url: 'index.php?s=/Home/User/default_address',
				type:'get',
				data:{address_id:address_id},
				dataType:'json',
				success:function(data){
					if(data.status==0){
			        	alert(data.msg);
			        }else{
			        	alert(data.msg);
			        }
				}

			})
 		if ($(this).siblings('label').hasClass('checkColor')) {
          	$(this).siblings('label').addClass('checkColor');
 		} else{
          	$(this).siblings('label').addClass('checkColor');
          	$(this).parents('.sumbit-order').siblings().find('label').removeClass('checkColor');
 		}
 	});
}

// 获取评论详情
function comment_details() {
	var goods_id = $("#goods_id").val();
	var page = $("#page").val();
	var page1 = Number(page)+1;
	
	$.ajax({
		url: 'index.php?s=/Home/Selected/get_comment',
		type: 'get',
		dataType: 'json',
		data: {goods_id: goods_id , page:page1},
		success:function(msg){
			if(msg.status==1 && msg.data!=""){
				page=Number(page)+1;
				 $("#page").val(page);
			}
			var get_comment="";
			var photo = "";
			for(var key in msg.data ){//因为是数组，所以要循环才能拿到
				for(var key1 in msg.data[key].img ){
		        	photo += "<img src="+msg.data[key].img[key1]+" />"
		        }
		        //console.log(msg.data[key].img);
				get_comment +=  "<li>"+
		              				"<div class='comment-title clearfix'>"+
		                				"<img src="+msg.data[key].head_img+">"+
		                				"<span>"+msg.data[key].user_name+"</span>"+
		                				"<span>"+msg.data[key].add_time+"</span>"+
		                			"</div>"+
		              				"<div class='text'>"+msg.data[key].comment+"</div>"+
		              				"<div class='photo'>"+photo+

		              				"</div>"+
		              				"<div class='time'>兑换日期："+msg.data[key].booking_time+"</div>"+
		            			"</li>"
		        
		        //$(".comment .photo").append(photo);
	        	$(".comment").append(get_comment);
			}
      	}
	})
}

function function_name() {
	$(window).scroll(function(event) {
		var doc_height = $(document).height();
		var scroll_top = $(document).scrollTop(); 
		var window_height = $(window).height();
		if(scroll_top >= doc_height - window_height){
			comment_details();
    	}
	});
}
function_name();

function send_verify(){
	var phone=$("#phone").val();
	if(phone.length<11){
		alert('请正确填写手机号码');
	}else{
		$.ajax({
	      url: 'index.php?s=/Home/User/send_verify',
	      type: 'post',
	      dataType: 'json',
	      data: {phone:phone},
	      success:function(msg){
	        if(msg.status==0){
	        	alert('暂时不能发送短信');
	        }else{
	        	alert('短信发送成功，请留意短信');
	        }
	      }
	    });
	}
}

function login(){
	var phone=$("#phone").val();
	var user_code=$("#user_code").val();
	var puid=$("#puid").val();
	if(puid == ""){
		puid='';
	}
	var url='index.php?s=/Home/User/login'+puid;
	//console.log(puid);return false;
	//alert(url);return false;
	$.ajax({
      url: url,
      type: 'post',
      dataType: 'json',
      data: {user_phone:phone,user_code:user_code},
      success:function(msg){
      	//alert(123);
        //console.log(msg);
        if(msg.status=='1'){
        	window.location.href="index.php?s=/Home/Selected/index"+msg.data.puid_url;
        }else{
        	alert(msg.msg);
        }
      }
    });
}

//弹出提示通用方法
function alert_msg(msg){
	$(".alert-message").show();
    setTimeout(function () {
      $(".alert-message").hide();
    }, 3000)
    $(".alert-message").find('p').text(msg);
}

//不加入购物车 直接购买产品
function add_order_two(){
		var puid=$("#puid").val();
		var goods_id = $("#goods_id").val();
		var number = $(".cart-integral .number").text();
		var attr_length = $(".attribute span").length;
		var attr_val="";
		for(var i = 0;i < attr_length;i++){
			if ($(".attribute span").eq(i).attr('class') == 'active') {
				if (attr_val == "") {
					attr_val = $(".attribute span").eq(i).attr('attr_val');
				}else{
					attr_val = attr_val +','+$(".attribute span").eq(i).attr('attr_val');
				}
				
			}
		}
		//var diy_url=$('#diy_url').val();
		$.ajax({
			//直接购买不加入购物车
			url: 'index.php?s=/Home/Order/add_order_two',
			//url: diy_url,
			type: 'post',
			dataType: 'json',
			data: {goods_id: goods_id,number: number,attr:attr_val},
			success:function (data) {
				//console.info(data); //return false;
				if(data.status==999){
					top.location='index.php?s=/Home/User/login'+puid+''; 
				}
				if(data.status==1){
					window.location.href="index.php?s=/Home/Order/submit_cart&id="+data.data.id;
				}else{
					if(data.msg!=""){
						alert_msg(data.msg);
					}else{
						alert_msg('加入购物车失败');
					}
				}
				
			}
		})
	}
	
	//提交购物车
	function submit_cart_new(kk){
		window.location.href="index.php?s=/Home/Order/submit_cart&id="+kk;
		
	}
	
	//操作库存加减
	function change_cart(status,id){
		$.ajax({
			url: 'index.php?s=/Home/Order/work_cart',
			type: 'post',
			dataType: 'json',
			data: {id: id,status:status},
			success:function(data){
				//console.info(data);
				/*if(data.status==1){
					alert_msg('加入购物车失败');
				}*/
			}
		})
	}
	
	//评论匿名按钮处理
	function chioce_comment() {
		$(".comment-footer .checkboxFour > input").on('click', function() {
	 		if ($(this).siblings('label').hasClass('checkColor')) {
	          	$(this).siblings('label').removeClass('checkColor');
	 		} else{
	          	$(this).siblings('label').addClass('checkColor');
	 		}
	 	});
	}