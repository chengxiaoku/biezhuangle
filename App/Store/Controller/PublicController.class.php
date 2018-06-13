<?php
namespace Store\Controller;
use Think\Controller;
/**
 * 通用基类控制器
 */
class PublicController extends Controller{
	/**
	 * 初始化方法
	 */
	public function login(){
		if (IS_POST) {
			$wh['name'] = I('name');
			$wh['password'] = md5(I('password'));
			$info = M('company')->where($wh)->find();
			if ($info) {
				session('user.id',$info['id']);
				session('user.name',$info['name']);
				$this->ajaxReturn(array('success'=>true,'info'=>'登录成功！'));
			}else{
				$this->ajaxReturn(array('success'=>false,'info'=>'登录失败！'));
			}
		}else {
			$this->display();
		}
	}

	function logout(){
		session('user',null);
		$this->redirect("Public/login");
	}

	function error(){
		$this->display();
	}

	function amap(){
		$user_id = session('user.id');
		$data = M('company')->field('position_x,position_y')->find($user_id);
		$this->assign('position_x',$data['position_x']);
		$this->assign('position_y',$data['position_y']);
		$this->display();
	}

	/**
	 * 处理前台的用户地理坐标 及位置
	 */
	function add_map_data(){
		$user_id = session('user.id');
		$position_x = I('get.position_x','','trim');
		$position_y = I('get.position_y','','trim');
		$user_position_data = I('get.user_position_data','','trim');

		$data = array(
			'position_x' => $position_x,
			'position_y' => $position_y,
			'address' => $user_position_data,
		);
		$bool = M('company')->where('id='.$user_id)->save($data);
		if($bool){
			echo 1;
		}else{
			echo 2;
		}
	}

}

