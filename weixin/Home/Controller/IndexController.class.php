<?php
namespace Home\Controller;
use Think\Controller;
class IndexController extends Controller {


    public function index(){
        $arr        = array(
                            array(
                                "title" => "imooc",
                                "description"=>"imooc is a good tool",
                                "picurl"=>"http://pic12.nipic.com/20110117/1641596_145543080000_2.jpg",
                                "url"=>"http://evilair.cn/",
                                ), 
                            );
            //$wx = D("WxThink");
            //$postObj = $wx->getMsg();
            //$wx->responseMsg($postObj,"text","text");
            //echo "<hr/>";
            //$wx->responseMsg($postObj,$arr,'news');
            $this->responseMsg();
            //$wx->res($arr);
           
    }

    

    //接受事件推送并回复
    public function responseMsg(){
        //1.接受微信传过来的post数据（xml格式）
        //$postArr = $GLOBALS['HTTP_RAW_POST_DATA'];
        //$postObj = simplexml_load_string($postArr);
         $wx = D("WxThink");
         $postObj = $wx->getMsg();
        //2.处理消息类型并设置响应内容
// <xml>
// <ToUserName><![CDATA[toUser]]></ToUserName>
// <FromUserName><![CDATA[FromUser]]></FromUserName>
// <CreateTime>123456789</CreateTime>
// <MsgType><![CDATA[event]]></MsgType>
// <Event><![CDATA[subscribe]]></Event>
// </xml>
//关注公众号回复       
        if(strtolower($postObj->MsgType) == 'event'){
            //2.1如果是关注事件
            if(strtolower($postObj->Event) == 'subscribe'){
                //2.1.2回复消息
// <xml>
// <ToUserName><![CDATA[toUser]]></ToUserName>
// <FromUserName><![CDATA[fromUser]]></FromUserName>
// <CreateTime>12345678</CreateTime>
// <MsgType><![CDATA[text]]></MsgType>
// <Content><![CDATA[你好]]></Content>
// </xml>
                if(!empty($postObj->EventKey)){
                    $toUser     = $postObj->FromUserName;
                    $fromUser   = $postObj->ToUserName;
                    $time       = time();
                    $msgType   = 'text';
                    $content    = '欢迎关注，输入城市可查询天气哦，亲';
                    $content    .= $postObj->EventKey;
                    $template   ="<xml>
                                    <ToUserName><![CDATA[%s]]></ToUserName>
                                    <FromUserName><![CDATA[%s]]></FromUserName>
                                    <CreateTime>%s</CreateTime>
                                    <MsgType><![CDATA[%s]]></MsgType>
                                    <Content><![CDATA[%s]]></Content>
                                </xml>";
                    $info       = sprintf($template,$toUser,$fromUser,$time,$msgType,$content);
                    echo $info;
                    exit;
                }else{
                    $toUser     = $postObj->FromUserName;
                    $fromUser   = $postObj->ToUserName;
                    $time       = time();
                    $msgType   = 'text';
                    $content    = '欢迎订阅我的个人微信号';

                    $template   ="<xml>
                                    <ToUserName><![CDATA[%s]]></ToUserName>
                                    <FromUserName><![CDATA[%s]]></FromUserName>
                                    <CreateTime>%s</CreateTime>
                                    <MsgType><![CDATA[%s]]></MsgType>
                                    <Content><![CDATA[%s]]></Content>
                                </xml>";
                    $info       = sprintf($template,$toUser,$fromUser,$time,$msgType,$content);
                    echo $info;
                    exit;
                }
            }elseif (strtolower($postObj->Event) == 'click') {
                if(strtolower($postObj->EventKey) == '11'){
                     $arr        = array(
                                    array(
                                        "title" => "imooc",
                                        "description"=>"imooc is a good tool",
                                        "picurl"=>"http://pic12.nipic.com/20110117/1641596_145543080000_2.jpg",
                                        "url"=>"http://evilair.cn/",
                                        ), 
                                    );
                    $wx->responseMsg($postObj,$arr,'news'); 
                    exit; 
                }
                elseif(strtolower($postObj->EventKey) == '22'){
                     $imageJson = file_get_contents("media.txt");
                     $imageArr = json_decode($imageJson,true);
                     $media_id = $imageArr['media_id'];
                     $createtime = $imageArr['created_at'];
                     if($createtime < time() - 259000){
                       $file_info=array(
                            'filename'=>'/myphp/wxThink/shouquan.png',  //国片相对于网站根目录的路径
                            'content-type'=>'image/png',  //文件类型
                            'filelength'=>'2046'         //图文大小
                        );
                        $res = $wx->uploadImage($file_info,$timeout = 5);
                        $media_id = $res['media_id'];
                        file_put_contents("media.txt",json_encode($res));
                     }
                    $res = $wx->sendCusMsg($postObj,$media_id,"image");
                    //$wx->responseMsg($postObj,"nihao shijie");

                    exit;
                }elseif(strtolower($postObj->EventKey) == '23'){
                    $content = "客服消息接口";
                    $res = $wx->sendCusMsg($postObj,$content);
                }elseif(strtolower($postObj->EventKey) == '31'){
                    // $contnet = array(
                    //     "item"=>array(
                    //         "title"=>"outh",
                    //         "escription"=>"scan two demension code",
                    //         "picurl"=>"http://evilair.cn/myphp/wxThink/shouquan.png",
                    //         "url"=>"http://evilair.cn/",
                    //         ),
                    //     );
                    // $res = $wx->responseMsg($postObj,$congtent,"news");
                    $arr        = array(
                                    array(
                                        "title" => "outh",
                                        "description"=>"scan two demension code",
                                        "picurl"=>"http://evilair.cn/myphp/wxThink/shouquan.png",
                                        "url"=>"http://evilair.cn/",
                                        ), 
                                    );
                    $wx->responseMsg($postObj,$arr,'news'); 
                    exit; 
                }
            }elseif (strtolower($postObj->Event) == 'view') {
                $toUser = $postObj->FromUserName;
                $fromUser = $postObj->ToUserName;
                $time = time();
                $msgType = 'text';
                $eventKey = '跳转的URL是：';
                $eventKey .= $postObj->EventKey;
                
                $template   ="<xml>
                                <ToUserName><![CDATA[%s]]></ToUserName>
                                <FromUserName><![CDATA[%s]]></FromUserName>
                                <CreateTime>%s</CreateTime>
                                <MsgType><![CDATA[%s]]></MsgType>
                                <Content><![CDATA[%s]]></Content>
                            </xml>";
                $info       = sprintf($template,$toUser,$fromUser,$time,$msgType,$eventKey);
                echo $info;
                exit;
            }elseif(strtolower($postObj->Event) == 'scan'){
                $toUser     = $postObj->FromUserName;
                $fromUser   = $postObj->ToUserName;
                $eventKey   = $postObj->EventKey;
                $time       = time();
                $msgType   = 'text';
                $content    = '想查询天气吗，亲，输入您所在的城市吧';
                $content    .=$eventKey;

                $template   ="<xml>
                                <ToUserName><![CDATA[%s]]></ToUserName>
                                <FromUserName><![CDATA[%s]]></FromUserName>
                                <CreateTime>%s</CreateTime>
                                <MsgType><![CDATA[%s]]></MsgType>
                                <Content><![CDATA[%s]]></Content>
                            </xml>";
                $info       = sprintf($template,$toUser,$fromUser,$time,$msgType,$content);
                echo $info;
                exit;
            }
        }
//纯文本回复
// <xml>
//  <ToUserName><![CDATA[toUser]]></ToUserName>
//  <FromUserName><![CDATA[fromUser]]></FromUserName>
//  <CreateTime>1348831860</CreateTime>
//  <MsgType><![CDATA[text]]></MsgType>
//  <Content><![CDATA[this is a test]]></Content>
//  <MsgId>1234567890123456</MsgId>
//  </xml>
        if(strtolower($postObj->MsgType) == 'text'){
                $toUser     = $postObj->FromUserName;
                $fromUser   = $postObj->ToUserName;
                $time       = time();
                $msgType    = 'text';
                $template   ="<xml>
                                <ToUserName><![CDATA[%s]]></ToUserName>
                                <FromUserName><![CDATA[%s]]></FromUserName>
                                <CreateTime>%s</CreateTime>
                                <MsgType><![CDATA[%s]]></MsgType>
                                <Content><![CDATA[%s]]></Content>
                            </xml>";
            //2.2如果是文本事件
            if( trim(strtolower($postObj->Content)) == 'imooc'){
                $content    = "<a href='http://www.imooc.com'>imooc</a>";
                $info       = sprintf($template,$toUser,$fromUser,$time,$msgType,$content);
                echo $info;
                exit;
            }elseif ( trim(strtolower($postObj->Content)) == 'tuwen') {

                $arr        = array(
                                array(
                                    "title" => "imooc",
                                    "description"=>"imooc is a good tool",
                                    "picurl"=>"http://pic12.nipic.com/20110117/1641596_145543080000_2.jpg",
                                    "url"=>"http://evilair.cn/",
                                    ), 
                                array(
                                    "title" => "imooc",
                                    "description"=>"imooc is a good tool",
                                    "picurl"=>"http://pic12.nipic.com/20110117/1641596_145543080000_2.jpg",
                                    "url"=>"http://evilair.cn/",
                                    ),
                                array(
                                    "title" => "imooc",
                                    "description"=>"imooc is a good tool",
                                    "picurl"=>"http://pic12.nipic.com/20110117/1641596_145543080000_2.jpg",
                                    "url"=>"http://evilair.cn/",
                                    ),
                                );
                $msgType    = 'news';
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
                $info       = sprintf($template,$toUser,$fromUser,$time,$msgType);
                echo $info;
                exit;
            }else{
                $key     = "2f9d684ddcfd48e8b918ba01b7b6b476";
                $city    = $postObj->Content;
                $url     = "http://api.avatardata.cn/Weather/Query?key=".$key."&cityname=".$city;
                $ch      = curl_init();
                curl_setopt($ch,CURLOPT_URL,$url);
                curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
                $res = curl_exec($ch);
                curl_close($ch);
                if(!curl_errno($ch)){
                    var_dump(curl_error($ch));
                    //exit;
                }
                $content = "";
                $arr = json_decode($res,true);
                if( $arr['error_code'] == 0){
                    $content .= "阴历：".$arr['result']['realtime']['moon'];
                    $content .= "\n天气：".$arr['result']['realtime']['weather']['info'];
                    $content .= "\n提示：感冒".implode(',',$arr['result']['life']['info']['ganmao']);
                }else{
                    $content .="请输入正确的城市名";
                }
                $info       = sprintf($template,$toUser,$fromUser,$time,$msgType,$content);
                echo $info;
                //$this->weather($postObj);
                exit;
            }
        }
    }

    public function access_token(){
        //1请求url
        $appID = "wx7363caaa8882e9ab";
        $appSECRET = "1323dfac9b06e288ac0b384e8994b365";
       // $url ="https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=".$appID."&secret=".$appSECRET;
        $url ="https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=".$appID."&secret=".$appSECRET;
        //2初始化
        $ch = curl_init();
        //3初始化参数
        curl_setopt($ch,CURLOPT_URL,$url);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
        //4执行获取结果
        $res = curl_exec($ch);
        curl_close($ch);
        $arr = json_decode($res,true);
        var_dump($arr);
    }

    public function getServerIP(){
        $token = "AlaDmZR3bBdogZ5TJKSRSNBgrYV1WdhFMtcBV0wZxrrHIY7Hp9c9CUadrAnxGK4u-FDWOKpLr2goygUwnRaZ4or2O4MZ9jRfZPBauGF6DCFYlTvG_aSVdEcGUDkBJVY-LAIcAFAYNV";
        $url   = "https://api.weixin.qq.com/cgi-bin/getcallbackip?access_token=".$token;
        $ch = curl_init();
        curl_setopt($ch,CURLOPT_URL,$url);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
        $res = curl_exec($ch);
        curl_close($ch);
        if(curl_errno($ch)){
            var_dump(curl_error($ch));
        }
        $arr = json_decode($res,true);
        echo "<pre>";
        var_dump($arr);
        echo "</pre>";
    }

    private function http_curl($url,$arr,$type='get'){
        //初始化
        $ch = curl_init();
        //设置参数
        curl_setopt($ch,CURLOPT_URL,$url);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
        if($type == 'post'){
            curl_setopt($ch,CURLOPT_POST,1);
            curl_setopt($ch,CURLOPT_POSTFIELDS,$arr);
        }
        //采集
        $output = curl_exec($ch);
        curl_error($ch);
        // if($res == 'json'){
        //     return json_decode($output,true);
        // }
        if(curl_errno($ch)){
            //请求失败
            echo "http_curl";
            var_dump(curl_error($ch));
        }else{
            //请求成功
            return json_decode($output,true);
        }
    }

    private function getWxToken(){
        
        $appID = "wx7363caaa8882e9ab";
        $appSECRET = "1323dfac9b06e288ac0b384e8994b365";
        $url ="https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=".$appID."&secret=".$appSECRET;
        if($_SESSION['access_token'] && $_SESSION['expire_time'] > time() ){
            return $_SESSION['access_token'];
        }else{
            $token = $this->http_curl($url);
            $_SESSION['access_token']=$token['access_token'];
            $_SESSION['expire_time'] = time() + 7200;
            return $_SESSION['access_token'];
        }
    }

    public function defineItem(){
        header("content-type:text/html;charset=utf-8");
        $token = $this->getWxToken();
        echo $token;
        echo "<hr/>";
        $postArr = array(
            "button"=>array(
                array(
                    "name"=>urlencode("按钮1"),
                    "type"=>"click",
                    "key"=>"anniu1",
                    ),
                array(
                    "name"=>urlencode("按钮2"),
                    "sub_button"=>array(
                        array(
                            "type"=>"view",
                            "name"=>urlencode("搜索"),
                            "url"=>"http://www.baidu.com",
                            ),
                        array(
                            "type"=>"view",
                            "name"=>urlencode("视频"),
                            "url"=>"http://www.imooc.com",
                            ),
                        array(
                            "type"=>"click",
                            "name"=>urlencode("赞一下"),
                            "key"=>"anniu2",
                            ),
                        ),
                    ),
                array(
                    "name"=>urlencode("按钮3"),
                    "type"=>"click",
                    "key"=>"anniu3",
                    ),
                ),
            );
        echo "<pre>";
        var_dump($postArr);
        echo "</pre>";
        $postJson = urldecode(json_encode($postArr));
        echo "<hr/>";
        echo $postJson;
        $url = "https://api.weixin.qq.com/cgi-bin/menu/create?access_token=".$token;
        $output = $this->http_curl($url,$postJson,"post");
        echo "<hr/>";
        var_dump($output);
    }

    public function msgsend(){
        echo dirname(dirname(__FILE__));
        echo "<hr/>";
        $token = $this->getWxToken();
        echo $token;
        echo "<hr/>";
        // {     
        //     "touser":"OPENID",
        //     "text":{           
        //            "content":"CONTENT"            
        //            },     
        //     "msgtype":"text"
        // }
        //推送文字消息
        // $postArr = array(
        //         "touser"=>"olNePwXPTzaj-7f8_ji2PeB_FTjg",
        //         "text"=>array(
        //             "content"=>"imooc is very good",
        //             ),
        //         "msgtype"=>"text",
        //     );
        //推送图片消息
        // {
        //    "touser":"OPENID", 
        //    "mpnews":{              
        //             "media_id":"123dsdajkasd231jhksad"               
        //              },
        //    "msgtype":"mpnews" 
        // }
        $postArr = array(
                "touser"=>"olNePwXPTzaj-7f8_ji2PeB_FTjg",
                "mpnews"=>array(
                    "media_id"=>"123dsdajkasd231jhksad",
                    ),
                "msgtype"=>"mpnews",
            );

        $postJson = json_encode($postArr);
        echo $postJson;
        echo "<hr/>";
        $url = "https://api.weixin.qq.com/cgi-bin/message/mass/preview?access_token=".$token;
        $output = $this->http_curl($url,$postJson,"post");
        var_dump($output);
    }

    //获取授权  snsapi_base
    // public function getCode(){
    //     $appID = "wx7363caaa8882e9ab";
    //     $redirect_uri = urlencode("http://123.207.20.242/Imooc/index.php?m=Home&c=Index&a=getBaseinfo");
    //     $url = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=".$appID."&redirect_uri=".$redirect_uri."&response_type=code&scope=snsapi_base&state=123#wechat_redirect";
    //     header("location: ".$url);
    // }
    //获取授权   snsaop_userinfo
    public function getCode2(){
        // $appID = "wx7363caaa8882e9ab";
        // $redirect_uri = urlencode("http://evilair.cn/myphp/wxThink/index.php?m=Home&c=Index&a=getUserinfo");
        // $url = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=".$appID."&redirect_uri=".$redirect_uri."&response_type=code&scope=snsapi_base&state=123#wechat_redirect";
        // header("location: ".$url);
        $wx = D("WxThink");
        $wx->getCode("http://evilair.cn/myphp/wxThink/index.php?m=Home&c=Index&a=getUserinfo");
    }
    public function getBaseinfo(){
        // $appID = "wx7363caaa8882e9ab";
        // $code = $_GET['code'];
        // $secret = "1323dfac9b06e288ac0b384e8994b365";
        // $url = "https://api.weixin.qq.com/sns/oauth2/access_token?appid=".$appID."&secret=".$secret."&code=".$code."&grant_type=authorization_code";
        // $res = $this->http_curl($url);
        // var_dump($res);
        $wx = D("WxThink");
        $res = $wx->getUserinfo();
        dump($res);
    }

    public function getUserinfo(){
        // $appID = "wx7363caaa8882e9ab";
        // $code = $_GET['code'];
        // echo $code;
        // echo "<hr/>";
        // $secret = "1323dfac9b06e288ac0b384e8994b365";
        // $url = "https://api.weixin.qq.com/sns/oauth2/access_token?appid=".$appID."&secret=".$secret."&code=".$code."&grant_type=authorization_code";
        // $res = $this->http_curl($url);
        
        // //拉取用户详细信息
        // $access_token = $res['access_token'];
        // $openID = $res['openid'];
        // $url ="https://api.weixin.qq.com/sns/userinfo?access_token=".$access_token."&openid=".$openID."&lang=zh_CN";
        // $userinfo = $this->http_curl($url);
        // var_dump($userinfo);
        $wx = D("WxThink");
        $res = $wx->getUserinfo("");
        dump($res);
    }

    public function getQrcode(){

        $access_token = $this->getWxToken();
        $url = "https://api.weixin.qq.com/cgi-bin/qrcode/create?access_token=".$access_token;
        //{"expire_seconds": 604800, "action_name": "QR_SCENE", "action_info": {"scene": {"scene_id": 123}}}
        $postArr = array(
            "expire_seconds"=>604800,
            "action_name"=>"QR_SCENE",
            "action_info"=>array(
                "scene"=>array("scene_id"=>123),
                ),
            );

        $postJson = json_encode($postArr);
        $res = $this->http_curl($url,$postJson,"post");
        //echo $postJson;
        //var_dump($res);
        $ticket = urlencode($res['ticket']);
        $url = "https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket=".$ticket;
        return $url;
    }

    public function getJsapi_ticket(){
        if(!empty($_SESSION['jsapi_ticket']) && $_SESSION['jsapi_ticket_time'] > time()){
            $jsapi_ticket = $_SESSION['jsapi_ticket'];
        }else{
            $token = $this->getWxToken();
            $url = "https://api.weixin.qq.com/cgi-bin/ticket/getticket?access_token=".$token."&type=jsapi";
            $res = $this->http_curl($url);
            $jsapi_ticket = $res['ticket'];
            $_SESSION['jsapi_ticket'] = $jsapi_ticket;
            $_SESSION['jsapi_ticket_time'] = time()+7200;
        }
        return $jsapi_ticket;
    }

    public function getNoncestr($num = 16){
        $resource = array("A","B","C","D","E","F","G","H","I","G","K","L","M","N","O","P","Q","R","S","T","U","V","W","X","Y","Z","a","b","c","d","e","f","g","h","i","g","k","l","m","n","o","p","q","r","s","t","u","v","w","x","y","z",1,2,3,4,5,6,7,8,9,0);
        $count = count($resource);
        $nonceStr = '';
        for($i=0;$i<$num;$i++){
            $rand = rand(0,$count-1);
            $nonceStr .= $resource[$rand];
        }
        return $nonceStr;
    }

    public function share(){

        $timestamp = time();
        $nonceStr = $this->getNoncestr();
        $jsapi_ticket = $this->getJsapi_ticket();
        //$url = "http://123.207.20.242/Imooc/index.php/Home/Index/share";

        $url = "http://".$_SERVER[HTTP_HOST].$_SERVER[REQUEST_URI];
        //echo $url;
        //exit;
        //jsapi_ticket=sM4AOVdWfPE4DxkXGEs8VMCPGGVi4C3VM0P37wVUCFvkVAy_90u5h9nbSlYy3-Sl-HhTdfl2fzFy1AOcHKP7qg&noncestr=Wm3WZYTPz0wzccnW&timestamp=1414587457&url=http://mp.weixin.qq.com?params=value
        //$signature = "jsapi_ticket=".$jsapi_ticket."&noncestr=".$nonceStr."&timestamp=".$timestamp."&url=".$url;   
        $signature = "jsapi_ticket=".$jsapi_ticket."&noncestr=".$nonceStr."&timestamp=".$timestamp."&url=".$url;
        $signature = sha1($signature);

        $this->assign("imooc",$jsapi_ticket);
        $this->assign("timestamp",$timestamp);
        $this->assign("nonceStr",$nonceStr);
        $this->assign("signature",$signature);

        $this->display();
    }

   




    // public function weather(){
    //      //1.接受微信传过来的post数据（xml格式）
    //     //$postArr = $GLOBALS['HTTP_RAW_POST_DATA'];
    //     //$postObj = simplexml_load_string($postArr);
    //     $key     = "2f9d684ddcfd48e8b918ba01b7b6b476";
    //     //$city    = $postObj->Content;
    //     $city ="beijing";
    //     $url     = "http://api.avatardata.cn/Weather/Query?key=".$key."&cityname=".$city;
    //     $ch      = curl_init();
    //     curl_setopt($ch,CURLOPT_URL,$url);
    //     curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
    //     $res = curl_exec($ch);
    //     curl_close($ch);
    //     if(!curl_errno($ch)){
    //         var_dump(curl_error($ch));
    //     }
    //     $arr = json_decode($res,true);
    //     //         // $content    = 'this is my test page';
    //     //         // $info       = sprintf($template,$toUser,$fromUser,$time,$msgType,$content);
    //     //         // echo $info;
    //     // $content = $arr['result']['realtime']['moon'];
    //     $content = $city;
    //     $info    = sprintf($template,$toUser,$fromUser,$time,$msgType,$content);
    //     echo $info;
    //}
}