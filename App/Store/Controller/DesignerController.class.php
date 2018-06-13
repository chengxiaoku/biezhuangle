<?php
namespace Store\Controller;
/**
 * 后台首页控制器
 */
class DesignerController extends BaseController{

	public function index(){
		$wh['comp_id'] = session('user.id');
		$designer = M('designer');
		$count = $designer->where($wh)->count();
		$Page = new \Think\Page($count,I('limit',15));
		$list = M('designer')->where($wh)->limit($Page->firstRow.','.$Page->listRows)->select();
		$this->assign('list',$list);
		$this->assign('page',$Page->show());
		$this->display();
	}

    public function add(){
		if (IS_POST) {
			$M = D('Cms/designer');
			if ($M->create()) {
				$M->comp_id = session('user.id');
				if($M->add()){
					$this->ajaxSuccess(); exit;
				}
			}
			$error = $M->getError();
			$this->ajaxError($error ? $error : "操作失败！");
		}else {
			$this->display();	
		}
	}

    public function edit(){
		if (IS_POST) {
			$M = D('Cms/designer');
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
			$info = M('designer')->where($wh)->find();
			$this->assign('info',$info);
			$this->display();	
		}
	}

    public function del(){
		if (IS_POST) {
			$id=I('id');
			$res=M('designer')->delete($id);
			if(!$res){
				$this->ajaxError($this->Model->getError());
			}else{
				action_log('Del_Designer', 'Designer', $id);
				$this->ajaxSuccess('删除成功！');
			}
		}
	}

	public function photo(){
		if (IS_POST) {
			$M = M('photos');
			$href = I('post.image');
			$desg_id = I('post.desg_id');
			if ($href && $desg_id) {
				$data['type'] = 2;
				$data['href'] = base64toimg($href);
				$data['obj_id'] = $desg_id;
				$data['create_time'] = time();
				if($M->add($data)){
					/*echo "<script>layer.closeAll();</script>";
					header("Location: ".U('Designer/photo'));*/
					$desg_id = I('desg_id',0);
					$wh['obj_id'] = I('desg_id',0);
					$list = M('photos')->where($wh)->select();
					$this->assign('list',$list);
					$this->assign('desg_id',$desg_id);
					$this->display();
				}else {
					$error = $M->_sql();
					$this->ajaxError($error ? $error : "操作失败！");
				}
			}else {
				$this->ajaxError("提交失败！".$desg_id);
			}
		}else {
			$desg_id = I('desg_id',0);
			$wh['obj_id'] = I('desg_id',0);
			$list = M('photos')->where($wh)->select();
			$this->assign('list',$list);
			$this->assign('desg_id',$desg_id);
			$this->display();	
		}
	}

	function photoadd(){
		if (IS_POST) {

		}else {
			$this->display();	
		}
	}

	function photodel(){
		if (IS_POST) {
			
		}
	}
	function del_photo(){
		extract(I('get.'));
		$data = M('photos')->where("id=$img_id")->delete();
		if($data){
			echo 1;
		}
	}
	//搜索操作
	function search(){
		$wh['comp_id'] = session('user.id');
		$designer = M('designer');
		$val = I('get.val','','trim');
		if(!empty($val)){
			$wh['name'] = array('Like','%'.$val.'%');
		}
		$count = $designer->where($wh)->count();
		$Page = new \Think\Page($count,I('limit',15));
		$list = $designer->where($wh)->limit($Page->firstRow.','.$Page->listRows)->select();
		$this->assign('list',$list);
		$this->assign('page',$Page->show());
		$this->assign('se_val',$val);
		$this->display('index');
	}
}
