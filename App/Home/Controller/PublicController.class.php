<?php
/*
 * 控制器
 * Time   : 2016年07月26日
 */
namespace Home\Controller;
use Think\Controller;

class PublicController extends BaseController {

	public function index(){
		//增加用户行为记录表
		M('analy')->where(array('title'=>'summary'))->setInc('visit_num');
 	 	$this->display();
	}

    public function peizhi(){
 	 	$this->display();
	}

    public function gongyi(){
 	 	$this->display();
	}

    public function fucai(){
 	 	$this->display();
	}

    public function fuwu(){
 	 	$this->display();
	}

	public function guanjia(){
		$this->display();
	}



}
