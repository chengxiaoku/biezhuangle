<?php 
/*
 * 装修订单控制器 
 * Time   : 2017年03月08日 
 */
 
namespace Cms\Controller;
use Admin\Controller\AdminCoreController;

class DecorateController extends AdminCoreController {
	
	//系统默认模型
	private $Model = null;

    protected function _initialize() {
		//继承初始化方法
		parent::_initialize ();
		//设置控制器默认模型
        $this->Model = D('Decorate');
    }
	
    /* 列表(默认首页)   
     * Time   : 2017年03月08日 
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
     * Time   : 2017年03月08日 
     **/
	protected function _search() {
		$map = array ();
		$post_data=I('post.');
		/* 名称：城市 字段：city_id 类型：num*/
		if($post_data['s_city_id']!=''){
			$map['city_id']=$post_data['s_city_id'];
		}
		/* 名称：面积 字段：area 类型：num*/
		if($post_data['s_area']!=''){
			$map['area']=$post_data['s_area'];
		}
		/* 名称：室 字段：room 类型：num*/
		if($post_data['s_room']!=''){
			$map['room']=$post_data['s_room'];
		}
		/* 名称：厅 字段：hall 类型：num*/
		if($post_data['s_hall']!=''){
			$map['hall']=$post_data['s_hall'];
		}
		/* 名称：厨 字段：cook 类型：num*/
		if($post_data['s_cook']!=''){
			$map['cook']=$post_data['s_cook'];
		}
		/* 名称：卫 字段：toilet 类型：num*/
		if($post_data['s_toilet']!=''){
			$map['toilet']=$post_data['s_toilet'];
		}
		/* 名称：阳台 字段：balcony 类型：num*/
		if($post_data['s_balcony']!=''){
			$map['balcony']=$post_data['s_balcony'];
		}
		/* 名称：申请时间 字段：create_time 类型：datetime*/
		if($post_data['s_create_time_min']!=''){
			$map['create_time'][]=array('gt',strtotime($post_data['s_create_time_min']));
		}
		if($post_data['s_create_time_max']!=''){
			$map['create_time'][]=array('lt',strtotime($post_data['s_create_time_max']));
		}
		/* 名称：总价 字段：total_price 类型：num*/
		if($post_data['s_total_price']!=''){
			$map['total_price']=$post_data['s_total_price'];
		}
		/* 名称：方案标题 字段：title 类型：string*/
		if($post_data['s_title']!=''){
			$map['title']=array('like', '%'.$post_data['s_title'].'%');
		}
		/* 名称：方案说明 字段：explain 类型：textarea*/
		if($post_data['s_explain']!=''){
			$map['explain']=array('like', '%'.$post_data['s_explain'].'%');
		}
		/* 名称：状态 字段：status 类型：select*/
		if($post_data['s_status']!=''){
			$map['status']=$post_data['s_status'];
		}
		/* 名称：详细地址 字段：address 类型：textarea*/
		if($post_data['s_address']!=''){
			$map['address']=array('like', '%'.$post_data['s_address'].'%');
		}
		return $map;
	}
    
    /* 添加    
     * Time   : 2017年03月08日 
     **/
	public function add(){
		if(IS_POST){
			$post_data=I('post.');
 
			$data=$this->Model->create($post_data);
			if($data){
				$result = $this->Model->add($data);
				if($result){
					action_log('Add_Decorate', 'Decorate', $result);
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
     * Time   : 2017年03月08日 
     **/
	public function edit(){
		if(IS_POST){
			$post_data=I('post.');
 
			$data=$this->Model->create($post_data);
			if($data){
				$result = $this->Model->where(array('id'=>$post_data['id']))->save($data);
				if($result){
					action_log('Edit_Decorate', 'Decorate', $post_data['id']);
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
     * Time   : 2017年03月08日 
     **/
	public function del(){
		$id=I('get.id');
		empty($id)&&$this->error('参数不能为空！');
		$res=$this->Model->delete($id);
		if(!$res){
			$this->error($this->Model->getError());
		}else{
			action_log('Del_Decorate', 'Decorate', $id);
			$this->success('删除成功！');
		}
	}
}