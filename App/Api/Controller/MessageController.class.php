<?php
/*
 * 控制器
 * Time   : 2016年07月26日
 */
namespace Api\Controller;
use Think\Controller;

class MessageController extends BaseController {

    function index($user_id,$type=0){
        $wh['tag_type'] = 1;
        $wh['tag_id'] = array(0,$user_id,'or');
        if ($type) {
            $wh['type'] = $type;
        }
        $list['data'] = M('message')->where($wh)->order('`read`,id desc')->select();
        $wh['read'] = 0;
        $list['read'] = M('message')->where($wh)->count();
		$this->ajaxReturn($list);
	}

    function read(){
        if (IS_POST) {
            $wh['id'] = I('id');
            if(M('message')->where($wh)->setField('read',1)===false){
                $this->ajaxError();
            }else{
                $this->ajaxSuccess();
            }
        }
    }

    //生成合同
    function add_create_comp($obj_id){
        $wh['id'] = $obj_id;
        $info = M('decorate')->where($wh)->find();
        $data['type'] = 3;
        $data['obj_id'] = $obj_id;
        $data['tag_id'] = $info['user_id'];
        $data['content'] = "您的装修方案你通过装修公司审核，请缴费充值开始装修";
        $this->push_user($data);
    }

    //生成合同
    function add_reject_comp($obj_id){
        $wh['id'] = $obj_id;
        $info = M('decorate')->where($wh)->find();
        $data['type'] = 3;
        $data['obj_id'] = $obj_id;
        $data['tag_id'] = $info['user_id'];
        $data['content'] = "您的装修方案未通过装修公司审核，已被驳回，请查看修改";
        $this->push_user($data);
    }

    //用户充值
    function add_recharge($obj_id){
        $wh['id'] = $obj_id;
        $info = M('funds')->where($wh)->find();
        $data['type'] = 2;
        $data['goto'] = 1;
        $data['obj_id'] = $obj_id;
        $data['tag_id'] = $info['user_id'];
        $deco = $this->find_deco_byuid($info['user_id']);
        $bligation = $this->get_obligation($deco['id']);
        $data['content'] = "账户充值：【".$info['trade_amount']."】，待缴金额：【".$bligation."】";
        if($bligation == 0){
            if ($deco['status'] == 2 || $deco['status'] == 3) {
                set_order_status($deco['id'],4);
            }
            $data['content'] = "账户充值：【".$info['trade_amount']."】，等待装修装饰公司开始施工";
        }
        $this->push_user($data);
        $this->push_store($data,$deco['comp_id']);
        $this->push_admin($data);
    }

    //添加装修日记
    function add_notes($obj_id){
        $wh['a.id'] = $obj_id;
        $info = M('progress')->alias('a')
            ->field('a.*,b.user_id,c.name comp_name,d.name node_name')
			->join('gms_decorate b on a.deco_id = b.id')
            ->join('gms_company c on a.comp_id = c.id')
            ->join('gms_nodes d on a.node_id = d.id')
            ->where($wh)->find();
        $data['type'] = 3;
        $data['goto'] = 1;
        $data['obj_id'] = $obj_id;
        $data['tag_id'] = $info['user_id'];
        $data['content'] = $info['comp_name']."已为你家装修的【".$info['node_name']."】已经竣工，并上传装修日记，请查阅。";
        $this->push_user($data);
        //推送管家消息
        $realname = $this->find_user_byuid($info['user_id'],'realname');
        $data['content'] = $info['comp_name']."已为".$realname."家装修的【".$info['node_name']."】已经竣工，并上传装修日记。";
        $this->push_butler($data,3);
    }

    //添加补充申请
    function add_append($obj_id){
        $wh['a.id'] = $obj_id;
        $info = M('progress')->alias('a')
            ->field('a.*,b.user_id,c.name comp_name,d.name node_name')
			->join('gms_decorate b on a.deco_id = b.id')
            ->join('gms_company c on a.comp_id = c.id')
            ->join('gms_nodes d on a.node_id = d.id')
            ->where($wh)->find();
        $data['type'] = 3;
        $data['goto'] = 1;
        $data['obj_id'] = $obj_id;
        $data['tag_id'] = $info['user_id'];
        $data['content'] = $info['comp_name']."已为你家装修的【".$info['node_name']."】已经补充申请，并上传装修日记，请查阅。";
        $this->push_user($data);
        //推送管家消息
        $realname = $this->find_user_byuid($info['user_id'],'realname');
        $data['content'] = $info['comp_name']."已为".$realname."家装修的【".$info['node_name']."】已经补充申请，并上传装修日记。";
        $this->push_butler($data,3);
    }

    //节点验收
    function check_notes($obj_id){
        $wh['a.id'] = $obj_id;
        $info = M('progress')->alias('a')
            ->field('a.*,b.user_id,c.name comp_name,d.name node_name,e.nickname')
			->join('gms_decorate b on a.deco_id = b.id')
            ->join('gms_company c on a.comp_id = c.id')
            ->join('gms_nodes d on a.node_id = d.id')
            ->join('gms_member e on b.user_id = e.id')
            ->where($wh)->find();
        $data['type'] = 3;
        $data['goto'] = 1;
        $data['obj_id'] = $obj_id;
        $data['tag_id'] = $info['user_id'];
        $price = $this->get_note_money($info['deco_id']);
        $data['content'] = "您已验收通过".$info['comp_name']."为你家装修的【".$info['node_name']."】";
        $this->push_user($data);
        //推送管家消息
        $realname = $this->find_user_byuid($info['user_id'],'realname');
        $data['content'] = $realname."已验收通过".$info['comp_name']."装修的【".$info['node_name']."】,并支付节点费用。";
        $this->push_butler($data);
        //财务提醒
        $data['type'] = 2;
        $data['content'] = "您已向".$info['comp_name']."支付【".$info['node_name']."】支付节点费用¥".$price."元。";
        $this->push_user($data);
        //推送商户消息
        $data['content'] = $info['nickname']."已向您支付【".$info['node_name']."】支付节点费用¥".$price."元。";
        $this->push_store($data,$info['comp_id']);
    }

    function deco_finish($obj_id){
        $wh['a.id'] = $obj_id;
        $info = M('decorate')->alias('a')
            ->field('a.*,b.name comp_name')
            ->join('gms_company b on a.comp_id = b.id')
            ->where($wh)->find();
        $data['type'] = 3;
        $data['obj_id'] = $obj_id;
        $data['tag_id'] = $info['user_id'];
        $data['content'] = $info['comp_name']."已为你家的装修已经全部完成，请对本次装修做出评价。";
        $this->push_user($data);
        //推送管家消息
        $realname = $this->find_user_byuid($info['user_id'],'realname');
        $data['content'] = $info['comp_name']."已为".$realname."家的装修已经全部完成。";
        $this->push_butler($data,3);
    }

    function push_user($data,$tag_id=0){
        if ($tag_id) {
            $data['tag_id'] = $tag_id;
        }
        $data['tag_type'] = 1;
        $data['create_time'] = time();
        if(M('message')->add($data)){
            $wh['id'] = $data['tag_id'];
            $info = M('member')->where($wh)->find();
            sendNotifySpecial($info['username'],$data['content']);
            //修改信息提示标识符
        }
    }

	function push_store($data,$tag_id=0){
        if ($tag_id) {
            $data['tag_id'] = $tag_id;
        }
        $data['tag_type'] = 2;
        $data['create_time'] = time();
        M('message')->add($data);
    }

	function push_admin($data){
        $data['tag_id'] = 0;
        $data['tag_type'] = 3;
        $data['create_time'] = time();
        M('message')->add($data);
    }

    function push_butler($data,$tag_cat=2,$tag_id=0){
        if ($tag_id) {
            $data['tag_id'] = $tag_id;
        }else{
            $butler_id = $this->find_order_byuid($data['tag_id'],'butler_id');
            if ($butler_id) {
                $data['tag_id'] = $butler_id;
            }else{
                return false;
            }
        }
        $data['tag_type'] = 4;
        $data['tag_cat'] = $tag_cat;
        $data['create_time'] = time();
        //推送消息管家
        $butler_phone = get_butler_phone($tag_id);
        sendNotifySpecial_butler($butler_phone,$data['content']);
        M('message')->add($data);
    }

    /**
     * 极光推送测试
     */
    function jpush_clf(){
        //$result = sendNotifySpecial('手机号','信息');

        //$this->ajaxReturn($result);

    }
}
