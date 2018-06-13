<?php
namespace Backstage\Controller;
use Think\Crypt\Driver\Think;


	/**
	 * 后台首页控制器
	 */
class IndexController extends BaseController{
	/**
	 * 监理小哥信息显示页面
	 */
	public function index()
	{
		$user_num = M('member')->count();
		$butler_num = M('butler')->count();
		$this->assign('user',$user_num);
		$this->assign('butler',$butler_num);
		$this->display();
	}

	/**
	 * 修改数据
	 */
	function update(){

	}

	/**
	 * 删除数据
	 */
	function del(){

	}





}
