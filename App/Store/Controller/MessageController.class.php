<?php
namespace Store\Controller;
/**
 * 后台首页控制器
 */
class MessageController extends BaseController{

	public function index(){
		$wh['tag_type'] = 2;
        $wh['tag_id'] = session('user.id');
        $count = M('message')->where($wh)->count();
        $Page = new \Think\Page($count,I('limit',15));
		$list = M('message')->where($wh)->where($wh)->order("`read`,id desc")->limit($Page->firstRow.','.$Page->listRows)->select();
		$this->assign('list',$list);
        $this->assign('page',$Page->show());
		$this->display();
	}
}
