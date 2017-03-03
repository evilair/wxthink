<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html>
<head>
	<title>微信SDK</title>
	<meta charset="utf-8" />
	<meta name="viewpoint" content="initial-scale=1.0;width=device-width" />
	<script  src="http://res.wx.qq.com/open/js/jweixin-1.0.0.js"></script>

</head>
<body>
<?php echo ($imooc); ?>==========<?php echo ($timestamp); ?>========<?php echo ($nonceStr); ?>=========<?php echo ($signature); ?>
<script >
	wx.config({
	    debug: true, // 开启调试模式,调用的所有api的返回值会在客户端alert出来，若要查看传入的参数，可以在pc端打开，参数信息会通过log打出，仅在pc端时才会打印。
	    appId: 'wx7363caaa8882e9ab', // 必填，公众号的唯一标识
	    timestamp: '<?php echo ($timestamp); ?>', // 必填，生成签名的时间戳
	    nonceStr: '<?php echo ($nonceStr); ?>', // 必填，生成签名的随机串
	    signature: '<?php echo ($signature); ?>',// 必填，签名，见附录1
	    jsApiList: [
	    	'onMenuShareTimeline',
			'onMenuShareAppMessage'
	    ] // 必填，需要使用的JS接口列表，所有JS接口列表见附录2
	});

	wx.ready(function(){
		//分享到朋友圈
   		wx.onMenuShareTimeline({
		    title: 'nihao', // 分享标题
		    link: 'http://evilair/', // 分享链接
		    imgUrl: 'http://www.logoquan.com/upload/list/20160911/logoquan14817160417.PNG', // 分享图标
		    success: function () { 
		        // 用户确认分享后执行的回调函数
		    },
		    cancel: function () { 
		        // 用户取消分享后执行的回调函数
		    }
		});

		//分享给朋友
		wx.onMenuShareAppMessage({
		    title: 'nihao', // 分享标题
		    desc: 'testshare', // 分享描述
		    link: 'http://evilair/', // 分享链接
		    imgUrl: 'http://www.logoquan.com/upload/list/20160911/logoquan14817160417.PNG', // 分享图标
		    type: '', // 分享类型,music、video或link，不填默认为link
		    dataUrl: '', // 如果type是music或video，则要提供数据链接，默认为空
		    success: function () { 
		        // 用户确认分享后执行的回调函数
		    },
		    cancel: function () { 
		        // 用户取消分享后执行的回调函数
		    }
		});
	});

	wx.error(function(res){
    
	});

</script>
</body>
</html>