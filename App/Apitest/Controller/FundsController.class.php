<?php
/*
 * 控制器
 * Time   : 2016年07月26日
 */
namespace Apitest\Controller;
use Think\Controller;

class FundsController extends BaseController {

	function prestore($user_id){
		$this->assign('uid',$user_id);
		$this->display();
	}

	function payment($user_id){
		$this->assign('uid',$user_id);
		$this->display();
	}

	function get_data($type,$uid){
		$wh['status'] = 1;
		$wh['type'] = $type;
		$wh['user_id'] = $uid;
		$list = array('count'=>0,'data'=>array(),'xAxis'=>array());
		$count = M('funds')->where($wh)->sum('trade_amount');
		if ($count) {
			$list['count'] = $count;
			$xAxis = M('funds')->field('MIN(create_time) start,MAX(create_time) end')->where($wh)->find();
			$data = M('funds')->field("SUM(trade_amount) trade_amount,FROM_UNIXTIME(create_time,'%Y-%m-%d') create_time")
					//获取10个数据
					->where($wh)->group("FROM_UNIXTIME(create_time,'%Y-%m-%d')")->limit(10)->select();


			/*while ($xAxis['start'] <= $xAxis['end']) {
				$cur_val = '0';
				$cur_key = strtotime(date('Y-m-d',$xAxis['start']));
				foreach ($data as $key => $value) {
					if(strtotime($value['create_time']) == $cur_key){
						$cur_val = $value['trade_amount'];
					}
				}

				$list['data'][] = $cur_val;
				$list['xAxis'][] = date('n-j',$xAxis['start']);
				$xAxis['start'] = strtotime("+1 day",$xAxis['start']);
			}*/
			//最后一次改动7月02
			foreach ($data as $key => $val){
				$list['xAxis'][] = date('n-j',strtotime($val['create_time']));
				$list['data'][] = $val['trade_amount'];
			}
		}
		$this->ajaxReturn($list);
	}

	/**
	 * 获取用户 金额
	 * @param $user_id 登录用户ID
	 */
	function get_money(){
		extract(I('get.'));
		$wh['id'] = $user_id;
		$money = M('member')->field('money')->where($wh)->find();
		$this->ajaxReturn($money);
		//$this->ajaxReturn($money);
	}

	/**
	 * 参数1,user_id  2.type获取资金明细类型 3.$num 要几条数据
	 * 获取用户预存明细  0
	 * 支出明细 1
	 */
	function get_umoney_info(){
		extract(I('get.'));

		$data = M('funds')->field("sum(trade_amount) money,FROM_UNIXTIME(create_time,'%Y-%m-%d') time")
			->where(array("user_id"=>$user_id,'type'=>$type))
			->order("create_time ASC")
			->limit($num)
			->group('time')
			->select();
		if(empty($data)){
			$this->ajaxError(array('无数据'));
		}else{
			$this->ajaxReturn($data);
		}

	}
	//用户充值
	function recharge(){
		if (IS_POST) {
			$price = I('price');
			$user_id = I('user_id');
			$trade_no = time().rand(100,999);

			$wh['status'] = 1;
			$wh['user_id'] = $user_id;
			$info = M('funds')->where($wh)->order('id desc')->find();
			$data['pre_balance'] = 0;
			if ($info) {
				$data['pre_balance'] = $info['total_amount'];
			}
			$data['user_id'] = $user_id;
			$data['trade_no'] = $trade_no;
			$data['trade_amount'] = $price;
			$data['total_amount'] = $data['pre_balance']+$data['trade_amount'];
			$data['create_time'] = time();
			$data['msg'] = '支付宝充值';
			if(M('funds')->add($data)){
				$param = array(
					'out_trade_no'=>$trade_no,
					'price'=>$price,
					'subject'=>'预存金额'
				);
				$result['trade_no'] = $trade_no;
				$result['orderString'] = aliapp($param);
				$this->ajaxReturn($result);
			}else{
				$this->ajaxError();
			}
		}
    }

	function alipay_notify(){
		if (IS_POST) {
			extract(I('post.'));
			if ($sign == md5(md5($trade_no).'YOUjiawang') && $trade_no) {
				if(A('Alipay')->pay_success($trade_no)){
					$this->ajaxSuccess(array('trade_no'=>$trade_no)); exit;
				}else {
					$this->ajaxError();
				}
			}else {
				$this->ajaxError("验证失败");
			}
		}
	}

	//节点支付(用户向装修公司支付)
	function set_node_pay($deco_id=0,$note_id){
		$trans = M();
		$trans->startTrans();
		$wh['id'] = $deco_id;
		$deco = M('decorate')->where($wh)->find();
		if ($deco) {
			$price = $this->get_note_money($deco_id);
			if($this->add_funds($deco['user_id'],$price)){
				if($this->set_member_money($deco['user_id'],$price,'dec')){
					if($this->add_comp_money($deco_id,$note_id,$deco['comp_id'],$price)){
						 $trans->commit();
                   		 return true;
					}
				}
				$trans->rollback();
			}
		}
		return false;
	}

	//添加用户支付交易流水
	function add_funds($user_id,$price){
		$trade_no = time().rand(100,999);
		$wh['status'] = 1;
		$wh['user_id'] = $user_id;
		$info = M('funds')->where($wh)->order('id desc')->find();
		$data['pre_balance'] = 0;
		if ($info) {
			$data['pre_balance'] = $info['total_amount'];
		}
		$data['type'] = 1;
		$data['status'] = 1;
		$data['user_id'] = $user_id;
		$data['trade_no'] = $trade_no;
		$data['trade_amount'] = $price;
		$data['total_amount'] = $data['pre_balance'] - $price;
		$data['create_time'] = time();
		return M('funds')->add($data);
	}

	//更改用户账户余额
	function set_member_money($user_id,$price,$type='inc'){
		$wh['id'] = $user_id;
		$result = false;
		if ($type=="dec") {
			$result = M('member')->where($wh)->setDec('money',$price);
		}else{
			$result = M('member')->where($wh)->setInc('money',$price);
		}
		return $result === false?false:true;
	}

	//添加装饰公司账户流水
	function add_comp_money($deco_id,$note_id,$comp_id,$price){
		$data['deco_id'] = $deco_id;
		$data['note_id'] = $note_id;
		$data['comp_id'] = $comp_id;
		$data['money'] = $price;
		$data['create_time'] = time();
		//2017年7月7号增加装修公司余额
		M('company')->where(array('id'=>$comp_id))->setInc('money',$price);
		return M('compmoney')->add($data);
	}

}
