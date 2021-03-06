<?php 
/*
 * 商品类型控制器 
 * Time   : 2017年03月04日 
 */
 
namespace Cms\Controller;
use Admin\Controller\AdminCoreController;

class GoodstypeController extends AdminCoreController {
	
	//系统默认模型
	private $Model = null;

    protected function _initialize() {
		//继承初始化方法
		parent::_initialize ();
		//设置控制器默认模型
        $this->Model = D('Goodstype');
    }
	
    /* 列表(默认首页)   
     * Time   : 2017年03月04日 
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
     * Time   : 2017年03月04日 
     **/
	protected function _search() {
		$map = array ();
		$post_data=I('post.');
		/* 名称：房间 字段：room_id 类型：select*/
		if($post_data['s_room_id']!=''){
			$map['room_id']=$post_data['s_room_id'];
		}
		/* 名称：商品类型 字段：catid 类型：select*/
		if($post_data['s_catid']!=''){
			$map['catid']=$post_data['s_catid'];
		}
		return $map;
	}
    
    /* 添加    
     * Time   : 2017年03月04日 
     **/
	public function add(){
		if(IS_POST){
			$post_data=I('post.');
$post_data["catid"]=I("post.catid");$post_data["catid"]=implode(",",$post_data["catid"]); 
			$data=$this->Model->create($post_data);
			if($data){
				$result = $this->Model->add($data);
				if($result){
					action_log('Add_Goodstype', 'Goodstype', $result);
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
     * Time   : 2017年03月04日 
     **/
	public function edit(){
		if(IS_POST){
			$post_data=I('post.');
$post_data["catid"]=I("post.catid");$post_data["catid"]=implode(",",$post_data["catid"]); 
			$data=$this->Model->create($post_data);
			if($data){
				$result = $this->Model->where(array('id'=>$post_data['id']))->save($data);
				if($result){
					action_log('Edit_Goodstype', 'Goodstype', $post_data['id']);
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
     * Time   : 2017年03月04日 
     **/
	public function del(){
		$id=I('get.id');
		empty($id)&&$this->error('参数不能为空！');
		$res=$this->Model->delete($id);
		if(!$res){
			$this->error($this->Model->getError());
		}else{
			action_log('Del_Goodstype', 'Goodstype', $id);
			$this->success('删除成功！');
		}
	}
}