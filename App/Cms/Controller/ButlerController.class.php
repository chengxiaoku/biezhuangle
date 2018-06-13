<?php 
/*
 * 装修管家控制器 
 * Time   : 2017年03月24日 
 */
 
namespace Cms\Controller;
use Admin\Controller\AdminCoreController;

class ButlerController extends AdminCoreController {
	
	//系统默认模型
	private $Model = null;

    protected function _initialize() {
		//继承初始化方法
		parent::_initialize ();
		//设置控制器默认模型
        $this->Model = D('Butler');
    }
	
    /* 列表(默认首页)   
     * Time   : 2017年03月24日 
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
     * Time   : 2017年03月24日 
     **/
	protected function _search() {
		$map = array ();
		$post_data=I('post.');
		/* 名称：姓名 字段：name 类型：string*/
		if($post_data['s_name']!=''){
			$map['name']=array('like', '%'.$post_data['s_name'].'%');
		}
		/* 名称：照片 字段：photo 类型：pictures*/
		if($post_data['s_photo']!=''){
			$map['photo']=$post_data['s_photo'];
		}
		/* 名称：手机 字段：phone 类型：string*/
		if($post_data['s_phone']!=''){
			$map['phone']=array('like', '%'.$post_data['s_phone'].'%');
		}
		/* 名称：微信 字段：wechat 类型：string*/
		if($post_data['s_wechat']!=''){
			$map['wechat']=array('like', '%'.$post_data['s_wechat'].'%');
		}
		/* 名称：等级 字段：level 类型：select*/
		if($post_data['s_level']!=''){
			$map['level']=$post_data['s_level'];
		}
		/* 名称：简介 字段：intro 类型：textarea*/
		if($post_data['s_intro']!=''){
			$map['intro']=array('like', '%'.$post_data['s_intro'].'%');
		}
		return $map;
	}
    
    /* 添加    
     * Time   : 2017年03月24日 
     **/
	public function add(){
		if(IS_POST){
			$post_data=I('post.');
 
			$data=$this->Model->create($post_data);
			if($data){
				$result = $this->Model->add($data);
				if($result){
					action_log('Add_Butler', 'Butler', $result);
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
     * Time   : 2017年03月24日 
     **/
	public function edit(){
		if(IS_POST){
			$post_data=I('post.');
 
			$data=$this->Model->create($post_data);
			if($data){
				$result = $this->Model->where(array('id'=>$post_data['id']))->save($data);
				if($result){
					action_log('Edit_Butler', 'Butler', $post_data['id']);
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
     * Time   : 2017年03月24日 
     **/
	public function del(){
		$id=I('get.id');
		empty($id)&&$this->error('参数不能为空！');
		$res=$this->Model->delete($id);
		if(!$res){
			$this->error($this->Model->getError());
		}else{
			action_log('Del_Butler', 'Butler', $id);
			$this->success('删除成功！');
		}
	}
}