<?php
namespace Store\Controller;
/**
 * 后台首页控制器
 */
class WorksController extends BaseController{

	public function index($deco_id){
		$wh['deco_id'] = $deco_id;
        $count = M('works')->where($wh)->count();
        $Page = new \Think\Page($count,I('limit',10));
		$list = M('works')->where($wh)->where($wh)->limit($Page->firstRow.','.$Page->listRows)->select();
		$this->assign('list',$list);
        $this->assign('deco_id',$deco_id);
        $this->assign('page',$Page->show());
		$this->display();
	}

    public function add(){
		if (IS_POST) {
			extract(I('post.'));
			$M = D('Cms/works');
			$post_data = $M->create();
			if ($post_data) {
				//判断当天是否添加
				$wh['deco_id'] = $post_data['deco_id'];

				if($M->add()){
					$this->ajaxSuccess();
					exit;
				}
			}
			$error = $M->getError();
			$this->ajaxError($error ? $error : "操作失败！");
		}else {
            $this->exist_deco();
            $this->assign('deco_id',I('deco_id'));
			$this->display();	
		}
	}

    public function edit(){
		if (IS_POST) {
			$M = D('Cms/works');
			if ($M->create()) {
				$wh['id'] = I('id');
				if($M->where($wh)->save()){
					$this->ajaxSuccess(); exit;
				}
			}
			$error = $M->getError();
			$this->ajaxError($error ? $error : "操作失败！");
		}else {
			$wh['id'] = I('id');
			$info = M('works')->where($wh)->find();
			$this->assign('info',$info);
			$this->display();	
		}
	}

    public function del(){
		if (IS_POST) {
			$id=I('id');
			$res=M('works')->delete($id);
			if(!$res){
				$this->ajaxError($this->Model->getError());
			}else{
				action_log('Del_works', 'works', $id);
				$this->ajaxSuccess('删除成功！');
			}
		}
	}
}
