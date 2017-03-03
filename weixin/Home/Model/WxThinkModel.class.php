<?php
namespace Home\Model;
class WxThinkModel{
	
	protected $appID;
	protected $appSecret;
	protected $toUser;
	protected $fromUser;
	// protected $msgType;

	protected $text = "<xml>
							<ToUserName><![CDATA[%s]]></ToUserName>
							<FromUserName><![CDATA[%s]]></FromUserName>
							<CreateTime>%s</CreateTime>
							<MsgType><![CDATA[%s]]></MsgType>
							<Content><![CDATA[%s]]></Content>
						</xml>";

	function __construct(){
		$this->appID = C("APPID");
		$this->appSecret = C("APPSECRET");
	}
	public function test(){
		echo $this->appID."-->".$this->appSecret;
	}

	/**
	*获取从微信客户端获得的消息，返回一个Object，对应消息的XML结构
	*/
	public function getMsg(){
		$postXML = $GLOBALS['HTTP_RAW_POST_DATA'];
        $postObj = simplexml_load_string($postXML);
        // $this->toUser = $postObj->FromUserName;
        // $this->fromUser = $postObj->ToUserName;
        // $this->msgType = $postObj->MsgType; 
        return $postObj;
	}

	/**
	*设置微信按钮事件
	*$arr 按钮信息的数组
	*@return 返回按钮设置成功与否
	*/
	public function setButton($arr){
		$access_token = $this->getAccess_token();
		$url = "https://api.weixin.qq.com/cgi-bin/menu/create?access_token=".$access_token;
		$res = $this->http_curl($url,$arr);
		dump($access_token);
		dump($arr);
		return $res['errmsg'];
	}

	/**
	*回复文本或图文消息
	*$content 传递要发送的信息，图文消息的时候要数组，文本消息的时候要字符串
	*$msgType 回复的类型  'text' OR 'news'
	$arr= array(
	        array(
	            "title" => "outh",
	            "description"=>"scan two demension code",
	            "picurl"=>"http://evilair.cn/myphp/wxThink/shouquan.png",
	            "url"=>"http://evilair.cn/",
	            ), 
	        );
	*/

	public function responseMsg($postObj,$content,$msgType='text'){
		$toUser = $postObj->FromUserName;
		$fromUser   = $postObj->ToUserName;
		$time = time();
		if($msgType == 'text'){
			$template = $this->text;
			$info = sprintf($template,$toUser,$fromUser,$time,$msgType,$content);
		}elseif($msgType == 'news'){
            $template = $this->responseNews($content);
            $info       = sprintf($template,$toUser,$fromUser,$time,$msgType);
		}
		echo $info;
	}
	/**
	*发送客服消息
	*$postObj 微信服务器发送过来的用户信息
	*$content 要发送给客户的内容， 'text' 为要发送的文本 'image' 为要发送的 image_id
	*$type    发送的客服消息类型
	*/
	public function sendCusMsg($postObj,$content,$type="text"){
		$access_token = $this->getAccess_token();
		$url = "https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token={$access_token}";
		$toUser = $postObj->FromUserName;
		if($type == 'text'){
			
			$content = urlencode($content);
			$postArr = array(
				"touser"=>"{$toUser}",
				"msgtype"=>"text",
				"text"=>array("content"=>$content),
				);
			$postJson = urldecode(json_encode($postArr));
			$res = $this->http_curl($url,$postJson);			
		}elseif($type == 'image'){
			file_put_contents("log.txt","hello");
			$postArr = array(
				"touser"=>"{$toUser}",
				"msgtype"=>"image",
				"image"=>array("media_id"=>"{$content}"),
				);
			$postJson = json_encode($postArr);
			//file_put_contents("log.txt",$postJson);
			$res = $this->http_curl($url,$postJson);
		}
		return $res;
	}

	/**
	*获取临时二维码
	*$postArr = array(
	*            "expire_seconds"=>2592000,
	*            "action_name"=>"QR_SCENE",
	*            "action_info"=>array(
	*                "scene"=>array("scene_id"=>123),
	*                ),
	*            );
	*参数二维码的基本设置信息
	*@return $url 根据此url可以得到公众号的二维码
	//现在先这样，此时的问题是如果想设置新的场景参数就要立即生成新的二维码，而不是要在缓存文件中获得
	//思路：加一个 flag 判断是要生成新的还是使用已经存在的
	*/

	public function getQRcode($postArr){
		$qrinfo = file_get_contents('qrcode.txt');
		$qrinfo = json_decode($qrinfo,true);
		if(isset($qrinfo['expire_seconds']) && $qrinfo['expire_seconds'] > time()){
			$ticket = $qrinfo['ticket'];
		}else{
			$access_token = $this->getAccess_token();
			$url = "https://api.weixin.qq.com/cgi-bin/qrcode/create?access_token=".$access_token;
	        //{"expire_seconds": 604800, "action_name": "QR_SCENE", "action_info": {"scene": {"scene_id": 123}}}
	        $postJson = json_encode($postArr);
	        $res = $this->http_curl($url,$postJson);
	        $res['expire_seconds'] = $res['expire_seconds'] + time();
	        //echo $postJson;
	        //var_dump($res);
	        file_put_contents('qrcode.txt',json_encode($res));
	        $ticket = urlencode($res['ticket']);
		}
        $url = "https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket={$ticket}";
        return $url;
        //return "<img src='{$url}'>";
	}

	/**
	*获取网页授权信息 其中getBaseinfo是基本信息 getUserinfo是详细信息
	*$redirect_uri 重定向要调用的函数
	*$type         获取用户信息的类型  --加载 $redirect_uri后边，若想获得 'base' 类型 添加 '&type=base'
	*			   若想获得userinfo类型，则可以不需要添加任何信息
	*/

	public function getCode($redirect_uri){
        $appID = $this->appID;
        $redirect_uri = urlencode($redirect_uri);
        $url = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=".$appID."&redirect_uri=".$redirect_uri."&response_type=code&scope=snsapi_base&state=123#wechat_redirect";
        header("location: ".$url);
    }
    public function getUserinfo(){
    	$type = $_GET['type'];
    	if($type == "base"){
    		$appID = $this->appID;
	        $code = $_GET['code'];
	        $secret = $this->appSecret;
	        $url = "https://api.weixin.qq.com/sns/oauth2/access_token?appid=".$appID."&secret=".$secret."&code=".$code."&grant_type=authorization_code";
        	$userinfo = $this->http_curl($url);
    	}else{
	        $appID = $this->appID;
	        $code = $_GET['code'];
	        $secret = $this->appSecret;
	        //获取网页授权的 access_token
	        $url = "https://api.weixin.qq.com/sns/oauth2/access_token?appid=".$appID."&secret=".$secret."&code=".$code."&grant_type=authorization_code";
	        $res = $this->http_curl($url);
	        //拉取用户详细信息
	        $access_token = $res['access_token'];
	        $openID = $res['openid'];
	        $url ="https://api.weixin.qq.com/sns/userinfo?access_token=".$access_token."&openid=".$openID."&lang=zh_CN";
	        $userinfo = $this->http_curl($url);
    	}
        return $userinfo;
    }


	/**
	*上传图片
	*$file_info=array(
	*	    'filename'=>'/myphp/wxThink/Koala.jpg',  //国片相对于网站根目录的路径
	*	    'content-type'=>'image/jpg',  //文件类型
	*	    'filelength'=>'780831'         //图文大小
	*	);
	*$timeout 设置上传时间，要是不设置就是5秒，也算是可以的了
	*@return 返回image数组
	*/
	public function uploadImage($file_info,$timeout = 5){
		$access_token = $this->getAccess_token();
		$url="https://api.weixin.qq.com/cgi-bin/media/upload?access_token={$access_token}&type=image";
		$ch1 = curl_init ();
		$real_path="{$_SERVER['DOCUMENT_ROOT']}{$file_info['filename']}";
		//echo $real_path;
		//$real_path=str_replace("/", "\\", $real_path);
		$data= array("media"=>"@{$real_path}",'form-data'=>$file_info);
		curl_setopt ( $ch1, CURLOPT_URL, $url );
		curl_setopt ( $ch1, CURLOPT_POST, 1 );
		curl_setopt ( $ch1, CURLOPT_RETURNTRANSFER, 1 );
		curl_setopt ( $ch1, CURLOPT_CONNECTTIMEOUT, $timeout );
		curl_setopt ( $ch1, CURLOPT_SSL_VERIFYPEER, FALSE );
		curl_setopt ( $ch1, CURLOPT_SSL_VERIFYHOST, false );
		curl_setopt ( $ch1, CURLOPT_POSTFIELDS, $data );
		$result = curl_exec ( $ch1 );
		curl_close ( $ch1 );
		echo curl_errno($ch1);
		if(curl_errno($ch1)==0){
			$result=json_decode($result,true);
			//var_dump($result);
			return $result;
		}else {
			return curl_error($ch1);
		}
	}
	
	//拼装图文模板
	private function responseNews($arr){
        $template   = "<xml>
                                <ToUserName><![CDATA[%s]]></ToUserName>
                                <FromUserName><![CDATA[%s]]></FromUserName>
                                <CreateTime>%s</CreateTime>
                                <MsgType><![CDATA[%s]]></MsgType>
                                <ArticleCount>".count($arr)."</ArticleCount>
                                <Articles>";
                
                foreach ($arr as $k => $v) {
                $template   .= "<item>
                                    <Title><![CDATA[".$v['title']."]]></Title> 
                                    <Description><![CDATA[".$v['description']."]]></Description>
                                    <PicUrl><![CDATA[".$v['picurl']."]]></PicUrl>
                                    <Url><![CDATA[".$v['url']."]]></Url>
                                </item>";
                }
                               
                $template   .= "</Articles>
                                </xml>";
        return $template;
	}

	//获得 access_token  获取微信接口需要的
	public function getAccess_token(){
		$token_json = file_get_contents("token.txt");
		$token_arr = json_decode($token_json , true);
		if(isset($token_arr['access_token']) && $token_arr['expires_in'] > time()){
			return $token_arr['access_token'];
		}else{
			$url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid="
					.$this->appID."&secret=".$this->appSecret;
			$res = $this->http_curl($url);
			$res['expires_in'] = time() + 7000;
			file_put_contents("token.txt",json_encode($res));
			return $res['access_token'];	
		}
	}
	

	/**
	*通过curl获取接口信息
	*$url  请求路径
	*$arr  post请求传递的参数，形式为JSON或者ARRAY
	*@return 返回数据格式为数组
	*/
	private function http_curl($url,$arr=NULL){
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER ,true);
		if(!empty($arr)){
			curl_setopt($ch, CURLOPT_POST , true);
			curl_setopt($ch, CURLOPT_POSTFIELDS , $arr);
		}
		$res = curl_exec($ch);
		if(curl_errno($ch)){
			dump(curl_error($ch));
		}else{					
			curl_close($ch);
			return $res = json_decode($res,true);
		}
	}

	//验证ip地址的有效性
	public function valid(){
		$signature = $_GET['signature'];
        $timestamp = $_GET['timestamp'];
        $nonce     = $_GET['nonce'];
        $token     = 'weixin';
        $echostr   = $_GET['echostr'];

        $arr = array($timestamp,$nonce,$token);
        sort($arr);
        $tempstr = sha1(implode("",$arr));
        if($tempstr == $signature && $echostr){
        	echo $echostr;
        	exit;
		}
	}
}