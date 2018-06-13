<?php

/*
 * 公共方法控制器
 * Auth : Ghj
 * Time : 2015年4月11日
 * QQ : 912524639
 * Email : 912524639@qq.com
 * Site : http://guanblog.sinaapp.com/
 */
namespace Admin\Controller;
use Common\Controller\CoreController;

class FunctionController extends CoreController {

	//核心继承
    protected function _initialize() {
		//继承CoreController的初始化函数
        parent::_initialize();
		if(session(C('AUTH_KEY'))){
			return false;
		}
	}

	public function get_module_list() {

		$_Data = M('Module')->where(array('status'=>1,'disabled'=>1))->getField ( 'name as id,title as text');

		$_Data[]=array('id'=>'Admin','text'=>'系统管理模块');
		$r_type = I ( 'get.r_type' );
		if ($r_type == 'json') {
			echo json_encode ( $_Data );
		} else {
			return $_Data;
		}
	}
	/*
	 * 根据传入config的名称获取config然后返回
	 * Auth : Ghj
	 * Time : 2015年4月16日
	 */
	public function get_config($_Cname = '',$R_name = '') {
		if ($_Cname == '') {
			$_Cname = I ( 'get.cname' );
		}
		if ($_Rname == '') {
			$_Rname = I ( 'get.r_name' );
		}

		//设置返回键名
		$k_name='id';
		$v_name='text';
		//分解配置数据
		$_Ops = model_field_attr (C ( $_Cname ));
		//获取返回类型
		$r_type = I ( 'get.r_type' );
		if ($r_type == 'json') {
			foreach ( $_Ops as $key_one => $ops_arr_val ) {
				$data_ls ['id'] = $key_one;
				$data_ls ['text'] = $ops_arr_val;
				$data [] = $data_ls;
			}
			$data_ls ['id'] = null;
			$data_ls ['text'] = '请选择';
			$data [] = $data_ls;
			$this->ajaxReturn ( $data );
		} elseif ($r_type == 'json_list') {
			foreach ( $_Ops as $key_one => $ops_arr_val ) {
				$data[strval($key_one)] = $ops_arr_val;
			}
			echo 'var op_'.$_Rname.'=';
			echo json_encode( $data , JSON_FORCE_OBJECT);
		} else {
			foreach ( $_Ops as $key_one => $ops_arr_val ) {
				$data[$key_one] = $ops_arr_val;
			}
			return $data;
		}
	}

	/*
	 * 根据传入ID获取字段配置中的参数中的option，并且解析
	 * Auth : Ghj
	 * Time : 2015年4月16日
	 */
	public function get_field_option($f_id = '') {
		if ($f_id == '') {
			$f_id = I ( 'get.f_id', 0 );
		}
		$_Extra = M ( 'ModelField' )->where (array('id'=>$f_id))->field('name,extra')->find ();
		$_Option=unserialize($_Extra['extra']);
		$_Ops = model_field_attr ($_Option['option']);
		$r_type = I ( 'get.r_type' );
		if ($r_type == 'json') {
			$data_ls ['id'] = null;
			$data_ls ['text'] = '请选择';
			$data [] = $data_ls;
			foreach ( $_Ops as $opkey => $opkeyval ) {
				$data_ls ['id'] = $opkey;
				$data_ls ['text'] = $opkeyval;
				$data [] = $data_ls;
			}
			$this->ajaxReturn ( $data );
		} elseif($r_type == 'json_list') {
			foreach ( $_Ops as $opkey => $opkeyval ) {
				$data[$opkey] = $opkeyval;
			}
			echo 'var op_'.$_Extra["name"].'=';
			echo json_encode( $data , JSON_FORCE_OBJECT);
		} else {
			return $_Ops;
		}
	}

	/*
	 * 获取用户组树
	 * Auth : Ghj
	 * Time : 2015年4月16日
	 */
	public function get_auth_group($_pid = '0') {
		if ($_pid == '0') {
			$_pid = I ( 'get.pid', 0 );
		}
		$map ['status'] = 1;
		$_list = M ( 'AuthGroup' )->where ( $map )->order ( 'id asc' )->field ( 'id,title as text' )->select ();
		$r_type = I ( 'get.r_type' );
		if ($r_type == 'json') {
			$this->ajaxReturn ( $_list );
		} else {
			return $_list;
		}
	}

    public function get_web_group($_pid = '0') {
		if ($_pid == '0') {
			$_pid = I ( 'get.pid', 0 );
		}
		$map ['status'] = 1;
		$_list = M ( 'WebGroup' )->where ( $map )->order ( 'id asc' )->field ( 'id,title as text' )->select ();
		$r_type = I ( 'get.r_type' );
		if ($r_type == 'json') {
			$this->ajaxReturn ( $_list );
		} else {
			return $_list;
		}
	}

	/*
	 * 获取节点树
	 * Auth : Ghj
	 * Time : 2015年4月16日
	 */
	public function get_auth_rule($_pid = '0') {
		if ($_pid == '0') {
			$_pid = I ( 'get.pid', 0 );
		}
        $map ['status'] = 1;
		$r_type = I ( 'get.r_type' );
		if ($r_type == 'json') {
			$_list = M ( 'AuthRule' )->where ( $map )->order ( 'sort asc' )->getField ( 'id,pid,title as text,icon' );
			foreach($_list as $key=>$_list_one){
				$_list[$key]['iconCls']=$_list_one['icon'];
			}
			$_list [] = array (
				'id' => '0',
				'pid' => '-1',
				'text' => '根节点',
				'iconCls'=>'iconfont icon-viewlist'
			);
			$data = list_to_tree ( $_list, 'id', 'pid', 'children', '-1' );
			echo json_encode ( $data );
		} elseif ($r_type == 'json_list'){
			$data = M ( 'AuthRule' )->getField ( 'id,title as text' );
			echo "op_pid=".json_encode ( $data );
		} else{
			return $data;
		}
	}

    public function get_web_menu($_pid = '0') {
		if ($_pid == '0') {
			$_pid = I ( 'get.pid', 0 );
		}
		$map ['status'] = 1;
		$_list = M ( 'Menus' )->where ( $map )->order ( 'sort asc' )->getField('id,pid,title as text');
        foreach($_list as $key=>$_list_one){
            $_list[$key]['iconCls']='iconfont icon-viewlist';
        }
		if ($_pid == '-1') {
			$_list [] = array (
					'id' => '0',
					'pid' => '-1',
					'text' => '根节点',
                    'iconCls'=>'iconfont icon-viewlist'
			);
			$data = list_to_tree ( $_list, 'id', 'pid', 'children', '-1' );
		} else {
			$data = list_to_tree ( $_list, 'id', 'pid', 'children' );
		}
		$r_type = I ( 'get.r_type' );
		if ($r_type == 'json') {
			echo json_encode ( $data );
		} else {
			return $data;
		}
	}

	/*
	 * 获取图标
	 * Auth : Ghj
	 * Time : 2015年4月16日
	 */
	public function get_icon($_pid = '0') {
		$iconfont=file_get_contents('./Public/Static/IconFont/iconfont.css');
		$preg='/.(.*):before/U';
		preg_match_all($preg,$iconfont,$arr);
		foreach($arr[1] as $one){
			$data_ls['id']='iconfont '.$one;
			$data_ls['text']=$one;
			$data[]=$data_ls;
		}
		$r_type = I ( 'get.r_type' );
		if ($r_type == 'json') {
			echo json_encode ( $data );
		} else {
			return $data;
		}
	}

	/*
	 * 获取指定字段类型配置文件
	 * Auth : Ghj
	 * Time : 2015年4月16日
	 */
	public function field_setting($fieldtype = '') {
		if ($fieldtype == '') {
			$fieldtype = I ( 'get.fieldtype', 'num' );
		}
		$f_type = I ( 'get.f_type', 'add' );
		$field_config = C ( 'FIELD_LIST' ); // 获取字段类型列表
		$field = $field_config [$fieldtype] ['field'];
		$fiepath = MODULE_PATH . 'Fields/' . $fieldtype . '/';
		ob_start();
        include $fiepath . "field_" . $f_type . "_form.inc.php";
		$data_setting = $data_setting . ob_get_contents ();
		ob_end_clean ();
		$settings = array (
				'field' => $field,
				'extra' => $data_setting
		);
		$r_type = I ( 'get.r_type' );
		if ($r_type == 'json') {
			echo json_encode ( $settings );
		} else {
			return $settings;
		}
	}
	/*
	 * 获取指定字段类型的相应字段数据类型配置组
	 * Auth : Ghj
	 * Time : 2015年4月16日
	 */
	public function get_field_default($fieldtype = '') {
		if ($fieldtype == '') {
			$fieldtype = I ( 'get.fieldtype', 'num' );
		}
		$field_config = C ('FIELD_LIST'); // 获取字段类型列表
		$field = $field_config [$fieldtype] ['field'];
		foreach($field as $one){
			$field_ls['id']=$one;
			$field_ls['text']=$one;
			$field_z[]=$field_ls;
		}
		echo json_encode($field_z);
	}


	/*
	 * 获取用户级别List
	 * Auth : Ghj
	 * Time : 2015年4月16日
	 */
	public function get_user_level() {

		if ($r_name == '') {
			$r_name = I ( 'get.r_name' );
		}
		$r_type = I ( 'get.r_type' );

		if ($r_type == 'json') {
			$_list = M ( 'UserLevel' )->where ( $map )->order ( 'level asc' )->field ( 'level as id,title as text' )->select ();
			$this->ajaxReturn ( $_list );
		} elseif ($r_type == 'json_list') {
			$_list = M ( 'UserLevel')->getField ( 'level,title' );
			echo 'var op_'.$r_name.'=';
			$this->ajaxReturn ( $_list );
		} else {
			return $_list;
		}
	}

	public function get_category_all($_pid = '0') {
		$map = array();
        $r_type = I ( 'get.r_type' );
		if ($r_type == 'json'){
            $list = M('category')->field('id,title as text')->where($map)->select();
			$this->ajaxReturn($list);
		}elseif ($r_type == 'json_list'){
            $list = M ('category')->where($map)->getField( 'id,title as text' );
			echo "op_catid=".json_encode ($list);
		}  else{
			return $list;
		}
	}

    public function get_category_goods($_pid = '0') {
		$map['type'] = 2;
        $r_type = I ( 'get.r_type' );
		if ($r_type == 'json'){
            $list = M('category')->field('id,title as text')->where($map)->select();
			$this->ajaxReturn($list);
		}elseif ($r_type == 'json_list'){
            $list = M ('category')->where($map)->getField( 'id,title as text' );
			echo "op_catid=".json_encode ($list);
		}  else{
			return $list;
		}
	}

	public function get_position_all() {
		$r_type = I ( 'get.r_type' );
		if ($r_type == 'json'){
            $list = M('position')->field('id,name as text')->select();
			$this->ajaxReturn($list);
		}elseif ($r_type == 'json_list'){
            $list = M ( 'position' )->getField ( 'id,name as text' );
			echo "op_position=".json_encode ($list);
		}  else{
			return $list;
		}
	}

    public function get_province(){
        $r_type = I ( 'get.r_type' );
		if ($r_type == 'json'){
            $map['pid'] = 0;
            $_list = M ( 'city' )->where ( $map )->order ( 'sort asc' )->getField ( 'id,pid,name as text' );
			$_list [] = array (
				'id' => '0',
				'pid' => '-1',
				'text' => '根节点',
			);
			$data = list_to_tree ( $_list, 'id', 'pid', 'children', '-1' );
			echo json_encode ( $data );
		}elseif ($r_type == 'json_list'){
            $list = M ( 'city' )->getField ( 'id,name as text' );
			echo "op_pid=".json_encode ($list);
		}  else{
			return $list;
		}
    }

    public function get_brand_all() {
		$r_type = I ( 'get.r_type' );
		if ($r_type == 'json'){
            $list = M('brand')->field('id,name as text')->select();
			$this->ajaxReturn($list);
		}elseif ($r_type == 'json_list'){
            $list = M ('brand')->getField ( 'id,name as text' );
			echo "op_brand_id=".json_encode ($list);
		}  else{
			return $list;
		}
	}

    public function get_room_all() {
		$r_type = I ( 'get.r_type' );
		if ($r_type == 'json'){
            $list = M('room')->field('id,name as text')->select();
			$this->ajaxReturn($list);
		}elseif ($r_type == 'json_list'){
            $list = M ('room')->getField ( 'id,name as text' );
			echo "op_room_id=".json_encode ($list);
		}  else{
			return $list;
		}
	}

	public function get_nodes() {
		$r_type = I ( 'get.r_type' );
		if ($r_type == 'json'){
			$wh['pid'] = 0;
            $list = M('nodes')->field('id,name as text')->where($wh)->select();
			$this->ajaxReturn($list);
		}elseif ($r_type == 'json_list'){
            $list = M ('room')->getField ( 'id,name as text' );
			echo "op_pid=".json_encode ($list);
		}  else{
			return $list;
		}
	}
}
