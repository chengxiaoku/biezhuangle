<?php
/*
 * 学习建议控制器
 * Time   : 2016年12月21日
 */

namespace Cms\Controller;
use Admin\Controller\AdminCoreController;

class AdviseController extends AdminCoreController {

	//系统默认模型
   private $Model = null;

    protected function _initialize(){
        parent::_initialize ();
		//设置控制器默认模型
        $this->Model = D('Advise');
    }

    /* 列表(默认首页)
     * Time   : 2016年12月21日
     **/
	public function index(){
		if (IS_POST) {
			$post_data = I ( 'post.' );
			$post_data ['first'] = $post_data ['rows'] * ($post_data ['page'] - 1);
			$map = array ();
        	$map = $this->_search();
			$total = $this->Model->where ( $map )->count ();
			if ($total == 0) {
				$_list = '';
			} else {
				$_list = $this->Model->where ( $map )->order ( $post_data ['sort'] . ' ' . $post_data ['order'] )->limit ( $post_data ['first'] . ',' . $post_data ['rows'] )->select ();
			}
			$data = array (
					'total' => $total,
					'rows' => $_list
			);
			$this->ajaxReturn ( $data );
		} else {
        	$this->meta_title = '模型列表';
			$this->display ();
		}
	}

    /* 搜索
     * Time   : 2016年12月21日
     **/
	protected function _search() {
		$map = array ();
		$post_data=I('post.');
		/* 名称：分数 字段：score 类型：num*/
		if($post_data['s_score']!=''){
			$map['score']=$post_data['s_score'];
		}
		/* 名称：内容 字段：content 类型：textarea*/
		if($post_data['s_content']!=''){
			$map['content']=array('like', '%'.$post_data['s_content'].'%');
		}
		return $map;
	}

    /* 添加
     * Time   : 2016年12月21日
     **/
	public function add(){
		if(IS_POST){
			$post_data=I('post.');

			$data=$this->Model->create($post_data);
			if($data){
				$result = $this->Model->add($data);
				if($result){
					action_log('Add_Advise', 'Advise', $result);
					$this->success ( "操作成功！",U('index'));
				}else{
					$error = $this->Model->getError();
					$this->error($error ? $error : "操作失败！");
				}
			}else{
                $error = $this->Model->getError();
                $this->error($error ? $error : "操作失败！");
			}
		}else{
        	$this->display();
		}
	}

    /* 编辑
     * Time   : 2016年12月21日
     **/
	public function edit(){
		if(IS_POST){
			$post_data=I('post.');

			$data=$this->Model->create($post_data);
			if($data){
				$result = $this->Model->where(array('id'=>$post_data['id']))->save($data);
				if($result){
					action_log('Edit_Advise', 'Advise', $post_data['id']);
					$this->success ( "操作成功！",U('index'));
				}else{
					$error = $this->Model->getError();
					$this->error($error ? $error : "操作失败！");
				}
			}else{
                $error = $this->Model->getError();
                $this->error($error ? $error : "操作失败！");
			}
		}else{
			$_info=I('get.');
			$_info = $this->Model->where(array('id'=>$_info['id']))->find();
			$this->assign('_info', $_info);
        	$this->display();
		}
	}

    /* 删除
     * Time   : 2016年12月21日
     **/
	public function del(){
		$id=I('get.id');
		empty($id)&&$this->error('参数不能为空！');
		$res=$this->Model->delete($id);
		if(!$res){
			$this->error($this->Model->getError());
		}else{
			action_log('Del_Advise', 'Advise', $id);
			$this->success('删除成功！');
		}
	}
}
