<?php
namespace Store\Controller;
use Think\Crypt\Driver\Think;

/**
	 * 后台首页控制器
	 */
class IndexController extends BaseController{
	/**
	 * 首页
	 * 余额 总收入 提现总数
	 */
	public function index(){
		
		//$this->redirect('Order/index');
		$company = M('company');
		$compmoney = M('compmoney');

 		$user_id = session('user.id');
		//获取用户的余额
		$money = $company->field('money')->find($user_id);
		$this->assign('money',$money['money']);
		//获取用户总收入
//		$sql = "SELECT SUM(money) money FROM  gms_compmoney  WHERE comp_id = $user_id";
//		$sum = M()->query($sql);
		$sum = M('compmoney')->field("SUM(money) money")->where("comp_id = $user_id")->select();
		$this->assign('sum_money',$sum[0]['money']);

		//提现总数
		//$sql2 = "SELECT SUM(a.amount) amount FROM gms_company b join gms_tocash a on b.id = $user_id WHERE a.obj_id = $user_id";
		$data = M("company b")->field("SUM(a.amount) amount")->join("gms_tocash a on b.id = $user_id")->where("a.obj_id = $user_id")->select();
		$this->assign('amount',$data[0]['amount']);
		$this->display();
	}

	/**
	 * 处理折线图收入情况事务
	 */
	public function echatMoney(){
		//获取最近5条数据
		$num =  I("get._num",5);
		//获取用户ID
		$user_id = session('user.id');

		/*$sql ="SELECT FROM_UNIXTIME(create_time,'%Y-%m-%d') as time , sum(money) as money from
FROM gms_compmoney WHERE comp_id=$user_id GROUP BY time ORDER BY create_time ASC limit $num";
		$data = M()->query($sql);*/
		$data = M('compmoney')->field("sum(money) money,FROM_UNIXTIME(create_time,'%Y-%m-%d') time")
			->where(array('comp_id'=>$user_id))->group("time")->order("time desc")->limit($num)->select();
		$i=0;
		foreach ($data as $key => $val){
			$new_data['time'][$i] =$val['time'];
			$new_data['money'][$i] = $val['money'];
			$i++;
		}

		//数据反转
		$new_data['time'] = array_reverse($new_data['time']);
		$new_data['money'] = array_reverse($new_data['money']);

		//$_new_data = array_reverse($new_data);
		echo $this->ajaxReturn($new_data);
	}


	/**
	 * TP3.2 日志的测试使用 随时可以删除
	 */
	public function log_test()
	{
		\Think\Log::write('测试');
	}
	
	/**
	 * welcome
	 */
	public function welcome(){
	    $this->display();
	}

	/**
	 * 修改装修日记节点时间用
	 * 随时可删
	 *
	 * 使用说明  API/decorate/detail返回值  return $info
	 */
	public function test(){
		header('Content-type:text/html;charset=utf-8');
		$list = M('decorate')->alias('a')
			->field('a.*,b.name city_name,c.title pro_name')
			->join('gms_city b on a.city_id = b.id ')
			->join('gms_program c on a.pro_id = c.id')
			->order('a.create_time DESC')
			->select();
		echo "<table>";
		foreach ($list as $key => $value){
			echo "<tr>";
			echo "<td>".$key."</td>";
			echo "<td><a href='".U('Index/test_update',array('id'=>$value['id']))."'>".$value['address']."</a></td>";
			echo "</tr>";
		}
		echo "</table>";
	}

	/**
	 * @param $id随时可以删除
	 */
	public function test_update($id){
		header('Content-type:text/html;charset=utf-8');
		$obj = A('Api/Decorate');
		$data = $obj->detail($id);
		echo $data['id'];
		echo $data['address'];
		echo "开工时间:".date('Y-m-d',$data['start_date']);
		//action="form_action.asp" method="get">
		echo "<form action='".U('Index/test_update_db')."' method='get'>";
		echo "<table>";
		foreach ($data['notes'] as $key => $value){
			echo "<tr>";
			echo "<td>".$key."</td>";
			echo "<td>".$value['title']."</td>";
			echo "<td>".$value['id']."</td>";
			$time = date('Y-m-d',$value['create_time']);
			echo "<td>".$time."</td>";
			echo "<td><input name='time_arr[]' value='".$value['create_time']."'></td>";
			echo "<td><input name='id_arr[]' value='".$value['id']."'></td>";
			echo "</tr>";
		}
		echo "</table>";
		echo "<input type='submit' value='确定'></form>";
	}
	function test_update_db(){
		header('Content-type:text/html;charset=utf-8');
		extract(I('get.'));
		foreach ($id_arr as $key => $value){
			$time = $time_arr[$key];
			M('progress')->where(array('id'=>$value))->setField('create_time',$time);
		}
		echo '成功!';
	}

}
