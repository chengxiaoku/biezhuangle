<?php
/*
 * 控制器
 * Time   : 2016年07月26日
 */
namespace Api\Controller;
use Think\Controller;

class DecorateController extends BaseController {

	function signup(){
        if (IS_POST) {
			//增加用户行为记录表
			M('analy')->where(array('title'=>'order'))->setInc('visit_num');
            $m = D('Cms/decorate');
            $data = $m->create();
            if($data){
				$result = false;
				$wh['user_id'] = $data['user_id'];
				if ($m->where($wh)->count()>0) {
					$data['step'] = 2;
					if ($m->where($wh)->save($data)!==false) {
						$info = $this->find_deco_byuid($wh['user_id']);
						$result = $info['id'];
					}
				}else{
					$data['create_time'] = time();
					$result = $m->add($data);
					//2017年7月7日 禁用 ：手机号可能为假  禁用
					//同步微信登录方式手机号
					//if ($result) {
						//$this->set_user_phone($result,$data['phone']);
					//}
				}
                if($result){
                    $this->ajaxSuccess(array('id'=>$result));
                }else {
                    $error = $m->getError();
                    $this->ajaxError($error ? $error : "操作失败！");
                }
            }else{
                $error = $m->getError();
                $this->ajaxError($error ? $error : "操作失败！");
            }
        }
	}

	function set_user_phone($deco_id,$phone){
		$wh['id'] = $deco_id;
		$user_id = M('decorate')->where($wh)->getField('user_id');
		$wh['id'] = $user_id;
		$info = M('member')->where($wh)->find();
		if (!$info['username']) {
			$reuslt = M('member')->where($wh)->setField('username',$phone);
		}
	}

	function resetting(){
		if (IS_POST) {
			$wh['user_id'] = I('user_id');
			$deco_ids = M('decorate')->where($wh)->getField('id',true);
			if (M('decorate')->where($wh)->delete()) {
				$result = M('goodsdeco')->delete(implode(',',$deco_ids));
				if($result !== false){
					$this->ajaxSuccess(); exit;
				}
			}
			$this->ajaxError();
		}
	}

	function get_room($order_id,$ajax_ret=1){
		$wh['a.id'] = $order_id;
		$info = M('decorate')->alias('a')->field('a.*,b.name city_name')
			->join('gms_city b on a.city_id = b.id')
			->where($wh)->find();
		if ($info) {
			$list = array();
			$this->find_room('hall',$info['hall'],$list);
			$this->find_room('dining',$info['dining'],$list);
			$this->find_room('room',$info['room'],$list);
			$this->find_room('cook',$info['cook'],$list);
			$this->find_room('toilet',$info['toilet'],$list);
			$this->find_room('balcony',$info['balcony'],$list);
			$this->find_room('steel',$info['steel'],$list);
			$this->find_room('other',$info['other'],$list);
			if($ajax_ret){
				$info['data'] = $list;
				$this->ajaxReturn($info);
			}else{
				return $list;
			}
		}
	}
 
	function find_room($key,$value,&$list){
		for ($i=0; $i < $value; $i++) {
			$wh['type'] = $key;
			$info = M('room')->where($wh)->find();
			$info['sort_id'] = 1;
			if ($value>1) {
				$info['name'] .= $i+1;
				$info['sort_id'] = $i+1;
			}
			$list[] = $info;
		}
	}

	function get_category(){
		$wh['room_id'] = I('room_id',0);
		$catid = M('Goodstype')->where($wh)->getField('catid');
		if ($catid) {
			unset($wh);
			$wh['a.id'] = array('in',$catid);
			$list = M('category')->alias('a')->field('a.*')->distinct(true)
				->join('gms_goods b on a.id = b.cat_id')
				->where($wh)->order('sort,title')->select();
			$this->ajaxReturn($list);
		}
	}

	function get_brand(){
		$wh['cat_id'] = I('cat_id');
		$list = M('goods')->alias('a')->field('b.*')->distinct(true)
			->join('gms_brand b on a.brand_id = b.id')->order('b.sort desc')->where($wh)->select();
		$this->ajaxReturn($list);
	}

	function get_goods(){
		
		$cat_id = I('cat_id',0);
		$brand_id = I('brand_id');
		if(I('markup')){
			//$wh['markup'] = I('markup');
			$up = I('markup');
			$sql = "SELECT  CASE markup WHEN 1 THEN 3 ELSE markup END markup_new,a.sort,a.id,a.title,a.coverimg,a.brand_id,a.content,a.unit,a.price,a.markup_price,a.cat_id,a.markup  FROM gms_goods a INNER JOIN gms_category b on a.cat_id = b.id WHERE `cat_id` = ".$cat_id." AND `brand_id` = ".$brand_id." AND `markup` =".$up." ORDER BY a.sort,markup_new ";
		}else{
			$sql = "SELECT  CASE markup WHEN 1 THEN 3 ELSE markup END markup_new,a.sort,a.id,a.title,a.coverimg,a.brand_id,a.content,a.unit,a.price,a.markup_price,a.cat_id,a.markup  FROM gms_goods a INNER JOIN gms_category b on a.cat_id = b.id WHERE `cat_id` = ".$cat_id." AND `brand_id` = ".$brand_id." ORDER BY a.sort,markup_new ";
		}
		/*$list = M('goods')->alias('a')->field("CASE markup WHEN 1 THEN 3 ELSE markup END markup_new,a.id,a.title,a.coverimg,a.brand_id,a.content,a.unit,a.price,a.markup_price,a.cat_id,a.markup")
			->join('gms_category b on a.cat_id = b.id')
			->where($wh)->order('a.sort,markup')->select();*/
		$list = M()->query($sql);
		$this->ajaxReturn($list);

	}

	function find_goods_detail(){
		$wh['id'] = I('id');
		$info = M('goods')->where($wh)->find();
		//$info['images'] = M('goodsimg')->where(array('goods_id'=>$info['id']))->getField('image',true);
		$this->ajaxReturn($info);
	}

	function get_program(){
		$list = M('program')->select();
		$this->ajaxReturn($list);
	}

	function save(){
		if (IS_POST) {
			$model = M('decorate');
			$model->startTrans();
			$data = $model->create();
			$wh['id'] = $data['id'];
			$data['step'] = 3;
			if (I('step')){
				$data['step'] = I('step');
			}
			$data['status'] = 1;
			if($model->where($wh)->save($data) !== false){
				if($this->add_goods()){
					$model->commit();
					$this->ajaxSuccess(array('id'=>I('id')));
					return true;
				}else{
					$model->rollback();
				}
			}
			$error = $model->getError();
			$this->ajaxError($error ? $error : "提交失败！");
		}
	}

	function add_goods(){
		$model = M('goodsdeco');
		$goods = I('goods');
		$data['deco_id'] = I('id');
		$model->where($data)->delete();
		foreach ($goods as $key => $value) {
			$data['room_id'] = $value['room_id'];
			$data['sort_id'] = $value['sort_id']?$value['sort_id']:1;
			$data['goods_id'] = $value['goods_id'];
			$data['amount'] = $value['amount'];
			$dataList[] = $data;
		}
		return $model->addAll($dataList);
	}

	function exist_order($user_id){
		$info = $this->find_deco_byuid($user_id);
		if ($info) {
			$data = array('step'=>$info['step'],'id'=>$info['id'],'status'=>$info['status'],'step_bak'=>$info['step_bak']);
			$this->ajaxError('该用户已申请装修',$data);
		}else {
			$this->ajaxSuccess();
		}
	}

	function get_order($id){
		$wh['a.id'] = $id;
		$info = M('decorate')->alias('a')
			->field('a.*,b.name city_name,c.name prov_name,d.name comp_name,d.icon comp_icon,d.address comp_address,e.title pro_title')
			->join('gms_city b on a.city_id = b.id')
			->join('gms_city c on b.pid = c.id')
			->join('left join gms_company d on a.comp_id = d.id')
			->join('gms_program e on a.pro_id = e.id')
			->where($wh)->find();
		if ($info) {
			 $list = $this->get_room($id,0);
			 $info['data'] = $this->get_goods_slt($id,$list);
		}

		$this->ajaxReturn($info);
	}

	function get_order_byuser($user_id){
		$wh['a.user_id'] = $user_id;
		$info = M('decorate')->alias('a')
			->field('a.*,b.name city_name,c.name prov_name,d.name comp_name,d.icon comp_icon,d.address comp_address,e.title pro_title')
			->join('gms_city b on a.city_id = b.id')
			->join('gms_city c on b.pid = c.id')
			->join('left join gms_company d on a.comp_id = d.id')
			->join('gms_program e on a.pro_id = e.id')
			->where($wh)->find();
		if ($info) {
			 $list = $this->get_room($info['id'],0);
			 $info['data'] = $this->get_goods_slt($info['id'],$list);
		}
		$this->ajaxReturn($info);
	}

	function get_goods_slt($deco_id,$list){
		foreach ($list as $key => $value) {
			$wh['deco_id'] = $deco_id;
			$wh['room_id'] = $value['id'];
			$wh['sort_id'] = $value['sort_id'];
			$result = M('goodsdeco')->alias('a')
				->field('a.amount,c.title cat_name,b.*')
				->join('gms_goods b on a.goods_id = b.id ')
				->join('gms_category c on b.cat_id = c.id')
				->where($wh)->select();
			if ($result) {
				$list[$key]['goods'] = $result;
			}
		}
		return $list;
	}

	function select_comp(){
		if (IS_POST) {
			$wh['id'] = I('id');
			$data['comp_id'] = I('comp_id');
			if (M('decorate')->where($wh)->save($data)) {
				$this->ajaxSuccess();
			}else{
				$this->ajaxError();
			}
		}
	}

	function create_cont(){
		if (IS_POST) {
			$wh['id'] = I('id');
			$data['step'] = 4;
			if (I('step')){
				$data['step'] = I('step');
			}
			$data['comp_id'] = I('comp_id');
			$result = M('decorate')->where($wh)->save($data);
			if ($result === false) {
				$this->ajaxError();
			}else{
				$this->ajaxSuccess();
			}
		}
	}

	function index(){
		$share = I('get.share');

		$wh['a.status'] = array('in','5,6');
		if(I('get.user_id')){
			$wh['a.user_id'] = I('get.user_id');
		}

		$list = M('decorate')->alias('a')
			->field('a.*,b.name city_name,c.title pro_name')
			->join('gms_city b on a.city_id = b.id ')
			->join('gms_program c on a.pro_id = c.id')
			->where($wh)
			->order('a.start_date DESC')
			->page(I('get.p',1),I('get.limit',100))
			->select();
		foreach ($list as $key => $value) {
			if (!$list[$key]['coverimg']) {
				$info = $this->find_notes($value['id']);
				$list[$key]['coverimg'] = $info['photos'][0]['href'];
			}
			$list[$key]['photos'] = $this->get_coverimg($value['id']); //delete
			$list[$key]['status_name'] =  get_order_status($value['status']);
		}
		$this->ajaxReturn($list);
	}

	function get_coverimg($deco_id){
		$wh['deco_id'] = $deco_id;
		$list = M('design')->field('id,image href')->where($wh)->select();
		if (count($list)==0) {
			$info = $this->find_notes($deco_id);
			$list = $info['photos'];
		}
		return $list;
	}

	function detail($deco_id=0,$user_id = 0){
		//增加用户行为记录表
		M('analy')->where(array('title'=>'decorate_diary'))->setInc('visit_num');
		$wh['a.id'] = $deco_id;
		//个人装修日记
		if ($user_id) {
			$deco = $this->find_deco_byuid($user_id);
			$deco_id = $deco['id'];
			$wh['a.id'] = $deco['id'];
		}
		//查询管家信息
		$info = M('decorate')->alias('a')
			->field('a.*,b.icon comp_icon,b.name comp_name,c.nickname')
			->join('gms_company b on a.comp_id = b.id ')
			->join('gms_member c on a.user_id = c.id ')
			->where($wh)->find();

		if($deco_id){
			$butler = M('order')->field('butler_id')->where(array('deco_id'=>$deco_id))->find();
		}else{
			$butler = M('order')->field('butler_id')->where(array('user_id'=>$user_id))->find();
		}
		//判断管家是否存在
		if(!is_null($butler)){
			$butler_data = M('butler')->field('photo,name')->find($butler['butler_id']);
			$info['butler_name'] = $butler_data['name'];
			$info['butler_photo'] = $butler_data['photo'];
			$info['butl_id'] = $butler['butler_id'];
		}else{
			$info['butler_name'] = '';
			$info['butler_photo'] = '';
			$info['butl_id'] = '';
		}

		if (!$info['coverimg']) {
			$notes = $this->find_notes($info['id']);
			$info['coverimg'] = $notes['photos'][0]['href'];
		}
		$info['curr_node'] = M('progress')->where(array('deco_id'=>$deco_id))->count();
		$info['note_money'] = $this->get_note_money($info['id']);	
		$info['status_name'] = get_order_status($info['status']);
		$info['notes'] = $this->get_notes($info['id']);
		$info['progress'] = $this->get_progress($info['id']);
		$curr_name = M('nodes')->field('name')->find($info['curr_node']);
		$info['curr_node_name'] = $curr_name['name'];
		$share = I('get.share');
		$notes = $info['notes'];

		if(is_array($notes)){
			$where['a.order_id'] = $deco_id;

			foreach ($notes as $key => $val){
				$where['a.node_id'] = $val['node_id'];
				$_data = M('diary a')->field('b.id diary_ids ')->join('gms_diary_detail as b on a.id = b.diary_id')->where($where)->find();
				if(is_null($_data)){

					$info['notes'][$key]['identifier'] = false;
				}else{
					$info['notes'][$key]['identifier'] = true;
				}
			}
		}

		if($share && $share == 'clf'){

			//获取装修进度
			$num = $info['curr_node']/15*100;
			$_num = sprintf("%.2f", $num);

			$this->assign('num',$_num);
			$this->assign('info',$info);
			$this->display();
		}else{

			$this->ajaxReturn($info);
		}
		
	}

	function get_progress($deco_id){
		$list = array();
		$wh['deco_id'] = $deco_id;
		$count = M('progress')->where($wh)->count();
		if ($count>0) {
			$wh1['pid'] = array('gt',0);
			$info = $this->get_current_nodes($deco_id);
			$list = M('nodes')->where($wh1)->order('sort')->select();
			foreach ($list as $key => $value) {
				$list[$key]['select'] = 0;
				if ($value['id'] == $info['node_id']) {
					$list[$key]['select'] = 1;
					$list[$key]['icon'] = $list[$key]['iconed'];
				}
			}
		}
		return $list;
	}

	function completeness($deco_id){
		$wh['id'] = $deco_id;
		$info = M('decorate')->where($wh)->find();
		$comp_count = $this->get_completeness($deco_id);
		$this->assign('comp_count',$comp_count);
		$this->assign('stauts',get_order_status($info['status']));
		$this->display();
	}

	function get_notes($deco_id){
		$wh['a.deco_id'] = $deco_id;
		if(I('user_id')){
			$wh['c.user_id'] = I('user_id');
		}
		$list = M('progress')->alias('a')
			->field('a.*,b.name node_name')
			->join('gms_nodes b on a.node_id = b.id ')
			->join('gms_decorate c on a.deco_id = c.id ')
			->where($wh)->order('b.sort')->select();
		foreach ($list as $key => $value) {
			$list[$key]['photos'] = $this->get_photos($value['id']);
			$list[$key]['status_name'] = get_notes_status($value['status']);
			$list[$key]['append'] = $this->get_append($value['id']);
		}
		return $list;
	}

	function find_notes($deco_id){
		$wh['deco_id'] = $deco_id;
		$info = M('progress')->alias('a')
			->field('a.*,b.name node_name,c.area,c.title,c.total_price,d.title pro_title,e.name city_name')
			->join('gms_nodes b on a.node_id = b.id ')
			->join('gms_decorate c on a.deco_id = c.id ')
			->join('gms_program d on c.pro_id = d.id')
			->join('gms_city e on c.city_id = e.id')
			->where($wh)->order('id desc')->find();
		if ($info) {
			$wh1['type'] = 0;
			$wh1['obj_id'] = $info['id'];
			$info['photos'] = M('photos')->where($wh1)->order('sort')->select();
			$info['status_name'] = get_notes_status($info['status']);
		}
		return $info;
	}

	function get_append($note_id){
		$wh['note_id'] = $note_id;
		$list = M('append')->where($wh)->select();
		foreach ($list as $key => $value) {
			$list[$key]['photos'] = $this->get_photos($value['id'],3);
		}
		return $list;
	}

	//装修节点验收
	function check_notes(){
		if (IS_POST) {
			extract(I('post.'));

			$wh['id'] = $id;
			$wh['status'] = array('neq',4);
			$data['status'] = 4;
			$data['check_time'] = time();
			$result = M('progress')->where($wh)->save($data);

			if($result){
				//节点支付
				$info = M('progress')->where(array('id'=>$id))->find();
				A('Funds')->set_node_pay($info['deco_id'],$id);
				//消息通知
				A('Message')->check_notes($id);
				//是否完成
				$this->is_finish($info['deco_id']);
				$this->ajaxSuccess();
			}else{
				$this->ajaxError("123");
			}
		}
	}

	//装修节点是否完成
	function is_finish($deco_id=0){
        $wh['pid'] = array('gt',0);
        $wh['b.id'] = array('exp','IS NULL');
        $count = M('nodes')->alias('a')
            ->join('left join gms_progress b on a.id = b.node_id and status = 4 and deco_id = '.$deco_id)
            ->where($wh)->count();
        if ($count==0) {
            set_order_status($deco_id,6);
			$this->butler_closing($deco_id);
			A('Message')->deco_finish($deco_id);
            return true;
        }
        return false;
    }

	function butler_closing($deco_id){
		$wh['id'] = $deco_id;
		$info = M('decorate')->where($wh)->find();
		$wh['id'] = $info['butler_id'];
		$money = round($info['area']*9.9,2);
		M('butler')->where($wh)->setInc('money',$money);
    }

	//装修节点拒绝
	function reject_notes(){
		if (IS_POST) {
			extract(I('post.'));
			$wh['id'] = $id;
			$data['status'] = 6;
			$result = M('progress')->where($wh)->save($data);
			if($result !== false){
				//更新拒绝申请时间
				$wh1['note_id'] = $id;
				$info = M('append')->where($wh1)->order('id desc')->find();
				if ($info) {
					$wh['id'] = $info['id'];
					M('append')->where($wh)->setField('reject_time',time());
				}else{
					M('progress')->where($wh)->setField('reject_time',time());
				}
				$this->ajaxSuccess();
			}else{
				$this->ajaxError();
			}
		}
	}

	function find_company($id){
		$wh['id'] = $id;
		return M('company')->where($wh)->find();
	}

	//完善资料
	function perfect($deco_id){
		if (IS_POST) {
			$wh['id'] = $deco_id;
			$data['address'] = I('address');
			$data['type'] = I('type');
			$data['step'] = 5;
			if (I('step')){
				$data['step'] = I('step');
			}
			$data['status'] = 1;
			//$data['total_price'] = 0.15; //测试金额
			if(M('decorate')->where($wh)->save($data) === false){
				$this->ajaxError();
			}else {
				$uid = M('decorate')->where($wh)->getfield('user_id');
				if ($uid) {
					$wh['id'] = $uid;
					$m = M('member');
					$m->create();
					if ($m->where($wh)->save() === false) {
						$this->ajaxError();
					}else{
						$this->ajaxSuccess();
					}
				}else {
					$this->ajaxError("用户不存在");
				}
			}
		}
	}

	function get_compact($user_id){
		$wh['user_id'] = $user_id;
		$info = M('decorate')->alias('a')
			->field('a.*,b.name comp_name,c.realname,d.name city_name')
			->join('gms_company b on a.comp_id = b.id ')
			->join('gms_member c on a.user_id = c.id ')
			->join('gms_city d on a.city_id = d.id ')
			->where($wh)->find();
		$this->ajaxReturn($info);
	}

	function compact($user_id){
		$wh['user_id'] = $user_id;
		$info = M('decorate')->alias('a')
			->field("a.*,b.name comp_name,c.realname,d.name city_name,FROM_UNIXTIME(a.start_date,'%Y年%m月%d日') start_time,FROM_UNIXTIME(a.end_date,'%Y年%m月%d日') end_time")
			->join('gms_company b on a.comp_id = b.id ')
			->join('gms_member c on a.user_id = c.id ')
			->join('gms_city d on a.city_id = d.id ')
			->where($wh)->find();
		$this->assign('info',$info);
		$this->display();
	}

	function get_reject($obj_id=0,$type=1,$user_id=0){
		if ($user_id) {
			$deco = $this->find_deco_byuid($user_id);
			$obj_id = $deco['id'];
		}
		$wh['obj_id'] = $obj_id;
		$wh['read'] = 0;
		$wh['type'] = $type;
		$info = M('reject')->where($wh)->order('id desc')->find();
		if(!$info){
			$this->ajaxReturn('');
		}
		$this->ajaxReturn($info);
	}

	function set_reject(){
		if (IS_POST) {
			extract(I('post.'));
			$wh['id'] = $obj_id;

			//$result = M('decorate')->where($wh)->setField('status',1);

			//7-1月 修改为
			$result = M('decorate')
				->where($wh)
				->save(array('status'=>1,'step'=>6));

			if ($result !== false) {
				$wh1['type'] = 1;
				$wh1['obj_id'] = $obj_id;
				$result = M('reject')->where($wh1)->setField('read',1);
				if ($result !== false) {
					$this->ajaxSuccess();
				}else{
					$this->ajaxError();
				}
			}else{
				$this->ajaxError();
			}
		}
	}

	//获取装修效果图
    function get_design($deco_id,$type=1){
        $wh['type'] = $type;
        $wh['deco_id'] = $deco_id;
        $list = M('design')->where($wh)->group('room')->select();

        foreach ($list as $key => $value) {
            $list[$key]['room_name'] = get_design_room($value['room']);
            $wh['room'] = $value['room'];
            $list[$key]['data'] = M('design')->field('image')->where($wh)->select();
        }
		
        $this->ajaxReturn($list);
    }
	
}
