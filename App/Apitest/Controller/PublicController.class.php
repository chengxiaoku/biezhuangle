<?php
/*
 * 控制器
 * Time   : 2016年07月26日
 */
namespace Apitest\Controller;
use Think\Controller;

class PublicController extends BaseController {

	//快速维权
	function rights(){
		if (IS_POST) {
            $m = M('rights');
            $data = $m->create();
            if($data){
                $data['create_time'] = time();
                $result = $m->add($data);
                if($result){
                    $this->ajaxSuccess();
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

	function set_agree(){
        $m = M('agree');
		$data['tid'] = I('tid',1);
        $data['aid'] = I('aid',0);
        $data['uid'] = I('uid',0);
		$count = $m->where($data)->count();
        if ($count>0) {
            $this->ajaxError('你已经点过赞/踩');
        }else{
            $data['type'] = I('type',1);
            $data['create_time'] = time();
            if ($m->add($data)) {
                $this->ajaxSuccess();
            }else{
                $this->ajaxError();
            }
        }
	}

    function set_comment(){
        $m = D('Cms/comment');
        $post_data = I('post.');
        $data = $m->create($post_data);
		if($data){
			$data['ip'] = get_client_ip();
            $data['create_time'] = time();
			$result = $m->add($data);
			if($reulst !== false){
				if ($post_data['tid'] == 3) {
					$this->set_grade($post_data);
				}
				$this->ajaxSuccess();
			}else{
                $this->ajaxError();
			}
		}else{
            $error = $m->getError();
            $this->ajaxError($error ? $error : "操作失败！");
		}
	}

	function set_grade($post_data){
		$grade = $post_data['grade'];
		if ($grade) {
			$data['comp_id'] = $post_data['aid'];
			$data['user_id'] = $post_data['uid'];
			$data['shigong'] = $grade['shigong'];
			$data['fuwu'] = ifnull($grade['fuwu']);
			$data['shijian'] = ifnull($grade['shijian']);
			$data['sheji'] = ifnull($grade['sheji']);
			$data['type'] = ifnull($grade['type']);
			$data['content'] = $post_data['content'];
			$data['create_time'] = time();
			M('grade')->add($data);
		}
	}

	function set_favor(){
        $m = M('favor');
        $post_data = I('post.');
        $data = $m->create($post_data);
		if($data){
			$count = $m->where($data)->count();
			if ($count == 0) {
				$data['create_time'] = time();
				if($m->add($data)){
					$this->ajaxSuccess();
				}else{
					$this->ajaxError();
				}
			}else {
				$this->ajaxError('你已经收藏过了');
			}
		}else{
            $error = $m->getError();
            $this->ajaxError($error ? $error : "操作失败！");
		}
	}

	function get_smscode_test(){
		if (IS_POST) {
			$phone = I('post.phone');
	        $code = $this->authCode(true);
	        if (isset($phone) && !empty($phone)) {
				$config = C('SMS_CONFIG');
	            $config["Phone"] = $phone;
	            $config["Message"] = str_replace("CODE", $code, $config['Message']);
	            $config["Timestamp"] = time();
	            $param = "?";
	            $param .= http_build_query($config);
	            $ch = curl_init();
	            curl_setopt($ch, CURLOPT_URL, "http://116.255.217.84:9180/service.asmx/SendMessageStr" . $param);
	            curl_setopt($ch, CURLOPT_HEADER, false);
	            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	            $result = curl_exec($ch);
	            curl_close($ch);
	            if(false!==strstr($result, "State:1")){
	                $this->ajaxSuccess(array('code'=>$code));
	            }else {
	            	$this->ajaxError("验证码发送失败！");
	            }
	        }else {
	        	$this->ajaxError("手机号不能为空！");
	        }
		}
  }

	function get_city(){
		$wh['pid'] = I('pid',0);
		if (I('name')) {
			$wh1['name'] = array('like','%'.I('name').'%');
			$wh['pid'] = M('city')->where(array_merge($wh,$wh1))->getField('id');
		}
		$list = M('city')->where($wh)->select();
		$this->ajaxReturn($list);
	}

	function get_company(){
		//增加用户行为记录表
		M('analy')->where(array('title'=>'company'))->setInc('visit_num');
		$list = M('company')->select();
		foreach ($list as $key => $value){
			$list[$key]['grade'] = count_grade($value['id']);
		}
		$this->ajaxReturn($list);
	}

	function get_help(){
		$list = M('help')->order('sort')->select();
		$this->ajaxReturn($list);
	}

	function feedback(){
		if (IS_POST) {
			$m = M('feedback');
			$m->create();
			$m->create_time = time();
			$result = $m->add();
			if ($result) {
				$this->ajaxSuccess(array('id'=>$result));
			}else {
				$this->ajaxError();
			}
		}
	}

	function get_nodes(){
		$wh['pid'] = array('gt',0);
		$list = M('nodes')->where($wh)->select();
		$this->ajaxReturn($list);
    }

	/**
	 * 添加微信用户 手机号
	 */
	function set_wxuser_phone($userid,$phone){
		$data['id'] = $userid;
		//用户名就是手机号
		$data['username'] = $phone;
		$data['phone'] = $phone;
		if(M('member')->save($data)){
			$this->ajaxSuccess();
		}else{
			$this->ajaxError();
		}
	}

	/**
	 * 下载APP分享页面
	 */
	function download(){
		$this->display();
	}

	/**
	 * 活动页面 使用
	 * 用户活动充值人员记录
	 */
	function new_funds($data){
		$data['create_time'] = time();
		$data['status'] = 1;
		M('new_funds')->add($data);
	}

	function get_smscode(){
		if (IS_POST) {
			$phone = I('post.phone');
			//$phone = "15638748938";
			$code = $this->authCode(true);
			if (isset($phone) && !empty($phone)) {
				$content = "【别装了】验证码：" . $code . "(切勿泄露给他人)，如非本人操作，请忽略本短信。";
				/*$url = "https://dx.ipyy.net/sms.aspx?action=send&userid=&account=AG00001&password=".strtoupper(md5('abc123'))."&mobile=".$phone."&content=".$content."&sendTime=&extno=";*/
				$url = "https://dx.ipyy.net/sms.aspx";
				$post_data = array("action" => "send", "userid" => "", 'account' => 'AG00001', 'password' => strtoupper(md5('AG0000144')), 'mobile' => $phone, 'content' => $content, 'sendTime' => '', 'extno' => '');

				$ch = curl_init();

				curl_setopt($ch, CURLOPT_URL, $url);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
				// post数据
				curl_setopt($ch, CURLOPT_POST, 1);
				// post的变量
				curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);

				$output = curl_exec($ch);
				curl_close($ch);
				//打印获得的数据
				$obj = simplexml_load_string($output);
				if($obj->returnstatus == 'Success'){
					$this->ajaxSuccess(array('code'=>$code));
				}else{
					$this->ajaxError("验证码发送失败！");
				}
			} else {
				$this->ajaxError("手机号不能为空！");
			}
		}
	}

	/**
	 * 获取 装修日记 管家日记 banner 图
	 * $type 1 装修日记  管家日记
	 */
	function get_banner($type = 1 ){
		$banner_data = M('public_banner')->field('imgurl')->where(array('type' => $type))->order('sort')->select();
		if(empty($banner_data)){
			$_data[0] = array('imgurl' => "/Uploads/1/image/2017-07-19/20170719105856.jpg");
		}else{
			$_data = $banner_data;
		}
		$this->ajaxReturn($_data);
	}

	/**
	 * 联系我们接口
	 */
	function getContactUs(){
		$ContactUs_data = C('ContactUs');
		$this->ajaxSuccess($ContactUs_data);
	}

	/**
	 * 活动退款
	 */
	function refund(){
		extract(I('post.'));
		if(empty($user_id) || empty($money)){
			$this->ajaxError('缺少参数');
		}else{
			//查询用户余额
			$user_money = M('member')->field('money')->find($user_id);
			if($money > $user_money){
				$this->ajaxError('余额不足');
			}
			$data['user_id'] = $user_id;
			$data['money'] = $money;
			$data['status'] = 1;
			$data['create_time'] = time();

			$bool = M('refund')->add($data);
			//用户账户减少钱
			if ($bool){
				$this->ajaxSuccess();
			}else{
				$this->ajaxError();
			}
		}
	}
	
}
