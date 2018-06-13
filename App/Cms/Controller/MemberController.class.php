<?php 
/*
 * 用户管理控制器 
 * Time   : 2017年04月01日 
 */
 
namespace Cms\Controller;
use Admin\Controller\AdminCoreController;

class MemberController extends AdminCoreController {
	
	//系统默认模型
	private $Model = null;

    protected function _initialize() {
		//继承初始化方法
		parent::_initialize ();
		//设置控制器默认模型
        $this->Model = D('Member');
    }
	
    /* 列表(默认首页)   
     * Time   : 2017年04月01日 
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
     * Time   : 2017年04月01日 
     **/
	protected function _search() {
		$map = array ();
		$post_data=I('post.');
		/* 名称：用户名 字段：username 类型：string*/
		if($post_data['s_username']!=''){
			$map['username']=array('like', '%'.$post_data['s_username'].'%');
		}
		/* 名称：昵称/姓名 字段：nickname 类型：string*/
		if($post_data['s_nickname']!=''){
			$map['nickname']=array('like', '%'.$post_data['s_nickname'].'%');
		}
		/* 名称：邮箱 字段：email 类型：string*/
		if($post_data['s_email']!=''){
			$map['email']=array('like', '%'.$post_data['s_email'].'%');
		}
		/* 名称：手机 字段：phone 类型：string*/
		if($post_data['s_phone']!=''){
			$map['phone']=array('like', '%'.$post_data['s_phone'].'%');
		}
		/* 名称：头像 字段：head_img 类型：pictures*/
		if($post_data['s_head_img']!=''){
			$map['head_img']=$post_data['s_head_img'];
		}
		/* 名称：创建时间 字段：create_time 类型：datetime*/
		if($post_data['s_create_time_min']!=''){
			$map['create_time'][]=array('gt',strtotime($post_data['s_create_time_min']));
		}
		if($post_data['s_create_time_max']!=''){
			$map['create_time'][]=array('lt',strtotime($post_data['s_create_time_max']));
		}
		/* 名称：状态 字段：status 类型：select*/
		if($post_data['s_status']!=''){
			$map['status']=$post_data['s_status'];
		}
		/* 名称：真实姓名 字段：realname 类型：string*/
		if($post_data['s_realname']!=''){
			$map['realname']=array('like', '%'.$post_data['s_realname'].'%');
		}
		/* 名称：性别 字段：sex 类型：select*/
		if($post_data['s_sex']!=''){
			$map['sex']=$post_data['s_sex'];
		}
		/* 名称：装修地址 字段：address 类型：textarea*/
		if($post_data['s_address']!=''){
			$map['address']=array('like', '%'.$post_data['s_address'].'%');
		}
		return $map;
	}
    
    /* 添加    
     * Time   : 2017年04月01日 
     **/
	public function add(){
		if(IS_POST){
			$post_data=I('post.');
 
			$data=$this->Model->create($post_data);
			if($data){
				$result = $this->Model->add($data);
				if($result){
					action_log('Add_Member', 'Member', $result);
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
     * Time   : 2017年04月01日 
     **/
	public function edit(){
		if(IS_POST){
			$post_data=I('post.');
 
			$data=$this->Model->create($post_data);
			if($data){
				$result = $this->Model->where(array('id'=>$post_data['id']))->save($data);
				if($result){
					action_log('Edit_Member', 'Member', $post_data['id']);
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
     * Time   : 2017年04月01日 
     **/
	public function del(){
		$id=I('get.id');
		empty($id)&&$this->error('参数不能为空！');
		$res=$this->Model->delete($id);
		if(!$res){
			$this->error($this->Model->getError());
		}else{
			action_log('Del_Member', 'Member', $id);
			$this->success('删除成功！');
		}
	}
}