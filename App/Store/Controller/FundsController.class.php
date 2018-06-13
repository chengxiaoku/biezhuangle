<?php
namespace Store\Controller;
/**
 * 后台首页控制器
 */
class FundsController extends BaseController{

	public function index(){
		$model = M('compmoney');
		$user_id = session('user.id');

		$count = $model->alias('a')
			->join('gms_decorate b on a.deco_id = b.id')
			->join('left join gms_progress c on a.note_id = c.id')
			->where(array('a.comp_id'=>$user_id))->count('DISTINCT b.title');
		$Page = new \Think\Page($count,I('limit',10));
		$list = M()->query("
				  select SUM(a.money) money,b.title,a.deco_id from gms_compmoney a
				  join gms_decorate b on a.deco_id = b.id
				  where a.comp_id = $user_id and a.type=0
				  group by a.deco_id order by b.id desc LIMIT $Page->firstRow,$Page->listRows"
		);
		//金额
		$this->assign('list',$list);
        $this->assign('page',$Page->show());
		$this->display();
	}

	/**
	 * 财务中心详情页
	 */
	public function detail()
	{
		$id = I('get.id','','trim');
		$_money = M("compmoney");
		$count =$_money->where(array("deco_id"=>$id))->count();
		$Page = new \Think\Page($count,I('limit',10));
		$list = $_money->alias('a')
			->field('a.*,b.title,c.title note_title')
			->join('gms_decorate b on a.deco_id = b.id')
			->join('left join gms_progress c on a.note_id = c.id')
			->where(array("a.deco_id"=>$id))
			->select();
		$this->assign('list',$list);
		$this->assign('page',$Page->show());
		$this->display();

	}
}
