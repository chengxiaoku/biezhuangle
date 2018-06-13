<?php 
/*
 * 主材料控制器 
 * Time   : 2017年03月07日 
 */
 
namespace Cms\Controller;
use Admin\Controller\AdminCoreController;

class GoodsController extends AdminCoreController {
	
	//系统默认模型
	private $Model = null;

    protected function _initialize() {
		//继承初始化方法
		parent::_initialize ();
		//设置控制器默认模型
        $this->Model = D('Goods');
    }
	
    /* 列表(默认首页)   
     * Time   : 2017年03月07日 
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
     * Time   : 2017年03月07日 
     **/
	protected function _search() {
		$map = array ();
		$post_data=I('post.');
		/* 名称：标题 字段：title 类型：string*/
		if($post_data['s_title']!=''){
			$map['title']=array('like', '%'.$post_data['s_title'].'%');
		}
		/* 名称：封面图 字段：coverimg 类型：pictures*/
		if($post_data['s_coverimg']!=''){
			$map['coverimg']=$post_data['s_coverimg'];
		}
		/* 名称：品牌 字段：brand_id 类型：select*/
		if($post_data['s_brand_id']!=''){
			$map['brand_id']=$post_data['s_brand_id'];
		}
		/* 名称：分类 字段：cat_id 类型：select*/
		if($post_data['s_cat_id']!=''){
			$map['cat_id']=$post_data['s_cat_id'];
		}
		/* 名称：型号 字段：model 类型：string*/
		if($post_data['s_model']!=''){
			$map['model']=array('like', '%'.$post_data['s_model'].'%');
		}
		/* 名称：商品主材 字段：material 类型：string*/
		if($post_data['s_material']!=''){
			$map['material']=array('like', '%'.$post_data['s_material'].'%');
		}
		/* 名称：系列 字段：series 类型：string*/
		if($post_data['s_series']!=''){
			$map['series']=array('like', '%'.$post_data['s_series'].'%');
		}
		/* 名称：颜色 字段：color 类型：string*/
		if($post_data['s_color']!=''){
			$map['color']=array('like', '%'.$post_data['s_color'].'%');
		}
		/* 名称：规格 字段：size 类型：string*/
		if($post_data['s_size']!=''){
			$map['size']=array('like', '%'.$post_data['s_size'].'%');
		}
		/* 名称：产地 字段：origin 类型：string*/
		if($post_data['s_origin']!=''){
			$map['origin']=array('like', '%'.$post_data['s_origin'].'%');
		}
		/* 名称：加价 字段：markup 类型：select*/
		if($post_data['s_markup']!=''){
			$map['markup']=$post_data['s_markup'];
		}
		/* 名称：排序 字段：sort 类型：num*/
		if($post_data['s_sort']!=''){
			$map['sort']=$post_data['s_sort'];
		}
		/* 名称：单位 字段：unit 类型：string*/
		if($post_data['s_unit']!=''){
			$map['unit']=array('like', '%'.$post_data['s_unit'].'%');
		}
		/* 名称：原价 字段：original_price 类型：num*/
		if($post_data['s_original_price']!=''){
			$map['original_price']=$post_data['s_original_price'];
		}
		/* 名称：单价 字段：price 类型：num*/
		if($post_data['s_price']!=''){
			$map['price']=$post_data['s_price'];
		}
		/* 名称：加价 字段：markup_price 类型：num*/
		if($post_data['s_markup_price']!=''){
			$map['markup_price']=$post_data['s_markup_price'];
		}
		return $map;
	}
    
    /* 添加    
     * Time   : 2017年03月07日 
     **/
	public function add(){
		if(IS_POST){
			$post_data=I('post.');
 
			$data=$this->Model->create($post_data);
			if($data){
				$result = $this->Model->add($data);
				if($result){
					action_log('Add_Goods', 'Goods', $result);
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
     * Time   : 2017年03月07日 
     **/
	public function edit(){
		if(IS_POST){
			$post_data=I('post.');
 
			$data=$this->Model->create($post_data);
			if($data){
				$result = $this->Model->where(array('id'=>$post_data['id']))->save($data);
				if($result){
					action_log('Edit_Goods', 'Goods', $post_data['id']);
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
     * Time   : 2017年03月07日 
     **/
	public function del(){
		$id=I('get.id');
		empty($id)&&$this->error('参数不能为空！');
		$res=$this->Model->delete($id);
		if(!$res){
			$this->error($this->Model->getError());
		}else{
			action_log('Del_Goods', 'Goods', $id);
			$this->success('删除成功！');
		}
	}
}