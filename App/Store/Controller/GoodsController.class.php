<?php
namespace Store\Controller;
/**
 * 后台首页控制器
 */
class GoodsController extends BaseController{

	public function index($deco_id){
		$wh['deco_id'] = $deco_id;
        $count = M()->table('vw_select_goods')->where($wh)->count();
        $Page = new \Think\Page($count,I('limit',15));
		$list = M()->table('vw_select_goods')
            ->where($wh)->where($wh)->order('room_id,cat_id,brand_id')->limit($Page->firstRow.','.$Page->listRows)->select();
		$this->assign('list',$list);
        $this->assign('deco_id',$deco_id);
        $this->assign('page',$Page->show());
		$this->display();
	}
	
}
