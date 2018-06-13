<?php 
/*
 * 评论控制器 
 * Time   : 2017年02月11日 
 */
 
namespace Cms\Controller;
use Admin\Controller\AdminCoreController;

class CommentController extends AdminCoreController {
	
	//系统默认模型
	private $Model = null;

    protected function _initialize() {
		//继承初始化方法
		parent::_initialize ();
		//设置控制器默认模型
        $this->Model = D('Comment');
    }
	
    /* 列表(默认首页)   
     * Time   : 2017年02月11日 
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
     * Time   : 2017年02月11日 
     **/
	protected function _search() {
		$map = array ();
		$post_data=I('post.');
		/* 名称：类型 字段：tid 类型：num*/
		if($post_data['s_tid']!=''){
			$map['tid']=$post_data['s_tid'];
		}
		/* 名称：文章 字段：aid 类型：num*/
		if($post_data['s_aid']!=''){
			$map['aid']=$post_data['s_aid'];
		}
		/* 名称：用户 字段：uid 类型：num*/
		if($post_data['s_uid']!=''){
			$map['uid']=$post_data['s_uid'];
		}
		/* 名称：上级 字段：pid 类型：num*/
		if($post_data['s_pid']!=''){
			$map['pid']=$post_data['s_pid'];
		}
		/* 名称：内容 字段：content 类型：textarea*/
		if($post_data['s_content']!=''){
			$map['content']=array('like', '%'.$post_data['s_content'].'%');
		}
		/* 名称：IP 字段：ip 类型：string*/
		if($post_data['s_ip']!=''){
			$map['ip']=array('like', '%'.$post_data['s_ip'].'%');
		}
		/* 名称：时间 字段：create_time 类型：datetime*/
		if($post_data['s_create_time_min']!=''){
			$map['create_time'][]=array('gt',strtotime($post_data['s_create_time_min']));
		}
		if($post_data['s_create_time_max']!=''){
			$map['create_time'][]=array('lt',strtotime($post_data['s_create_time_max']));
		}
		return $map;
	}
    
    /* 添加    
     * Time   : 2017年02月11日 
     **/
	public function add(){
		if(IS_POST){
			$post_data=I('post.');
 
			$data=$this->Model->create($post_data);
			if($data){
				$result = $this->Model->add($data);
				if($result){
					action_log('Add_Comment', 'Comment', $result);
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
     * Time   : 2017年02月11日 
     **/
	public function edit(){
		if(IS_POST){
			$post_data=I('post.');
 
			$data=$this->Model->create($post_data);
			if($data){
				$result = $this->Model->where(array('id'=>$post_data['id']))->save($data);
				if($result){
					action_log('Edit_Comment', 'Comment', $post_data['id']);
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
     * Time   : 2017年02月11日 
     **/
	public function del(){
		$id=I('get.id');
		empty($id)&&$this->error('参数不能为空！');
		$res=$this->Model->delete($id);
		if(!$res){
			$this->error($this->Model->getError());
		}else{
			action_log('Del_Comment', 'Comment', $id);
			$this->success('删除成功！');
		}
	}
}