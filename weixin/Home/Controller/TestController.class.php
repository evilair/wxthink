<?php
namespace Home\Controller;
use Think\Controller;
class TestController extends Controller{

	protected $wxThink;
	function __construct(){
		parent::__construct();
		$this->wxThink = D("WxThink");
	}
	public function test(){
		$this->wxThink->test();
	}

	public function setButton(){
		$arr = array(
				'button' => array(
					array(
						'type' => 'click',
						'name' => urlencode('图文'),
						'key'  => '11',
						),
					array(
						'name' => urlencode('二级'),
						'sub_button' => array(
							array(
								'type' => 'view',
								'name' => urlencode('二维码'),
								'url'  => 'http://evilair.cn/myphp/wxThink/index.php/Home/Test/getQRcode',
								),
							array(
								'type' => 'click',
								'name' => urlencode('客服发送授权码'),
								'key'  => '22',
								),
							array(
								'type' => 'click',
								'name' => urlencode('客服'),
								'key'  => '23',
								),
							),
						),
					array(
					'type' => 'click',
					'name' => 'Click3',
					'key'  => '31',
					),
					),
			);
		$postJson = urldecode(json_encode($arr));
		dump($postJson);
		$res = $this->wxThink->setButton($postJson);
		echo $res;
	}

	public function getAccess_token(){
		$arr = $this->wxThink->getAccess_token();
		dump($arr);
	}

	public function uploadimage(){
		// $file_info=array(
		//     'filename'=>'/myphp/wxThink/Koala.jpg',  //国片相对于网站根目录的路径
		//     'content-type'=>'image/jpg',  //文件类型
		//     'filelength'=>'780831'         //图文大小
		// );
		$file_info=array(
		    'filename'=>'/myphp/wxThink/shouquan.png',  //国片相对于网站根目录的路径
		    'content-type'=>'image/png',  //文件类型
		    'filelength'=>'2046'         //图文大小
		);
		$res = $this->wxThink->uploadImage($file_info);
		dump($res);
	}

	public function imageinfo(){
		$real_path="{$_SERVER['DOCUMENT_ROOT']}".'/myphp/wxThink/Koala.jpg';
		echo $real_path;
		echo "<br/>";
		echo filesize($real_path);
	}

	public function getQRcode(){
		$postArr = array(
	            "expire_seconds"=>2592000,
	            "action_name"=>"QR_SCENE",
	            "action_info"=>array(
	                "scene"=>array("scene_id"=>123),
	                ),
	            );
		 $url = $this->wxThink->getQRcode($postArr);
		$this->assign('url',$url);
		 $this->display();
	}

	public function sentCusMsg(){
		 $imageJson = file_get_contents("media.txt");
         $imageArr = json_decode($imageJson,true);
         $media_id = $imageArr['media_id'];

        $res = $this->wxThink->sendCusMsg($postObj,$media_id,"image");
		dump($res);
	}

	public function getCode(){
		$this->wxThink->getCode2();
	}

	public function getUserinfo(){
		$this->wxThink->getUserinfo();
	}
}