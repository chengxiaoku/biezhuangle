<?php
/*
 * 控制器
 * Time   : 2016年07月26日
 */
namespace Home\Controller;
use Think\Controller;


class IndexController extends BaseController {

	public function index(){
		$data = $this->isMobile();
		//判断是否为手机端
		if($data){
			//APPID 应该来自公众平台
			//$appid = 'wx3f2595bbb60be87f';
			$appid = 'wx19ffb6169e2104d5';
			$APPSECRET = '5f47d52a413d963ab53e99223bf01407';
			//引入微信分享类

			$jssdk = new \Org\Util\WeixinShare($appid, $APPSECRET);
			$signPackage = $jssdk->GetSignPackage();

			//获取分享所需要的图片地址
			$url = C('img_url');
			$url = $url.'/Public/Home/mindex/images/chart.png';

			$this->assign('url',$url);
			$this->assign('signPackage',$signPackage);
			$this->display('mindex');
		}else{
			$this->display('index');
		}
	}



}
