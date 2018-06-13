<?php

namespace Apitest\Controller;
use Think\Controller;

/**
 * 空模块，主要用于显示404页面，请不要删除
 */

class BaseController extends Controller{

    public $ajaxResult = array('success'=>false,'info'=>'操作失败','data'=>array());

    protected function _initialize() {
        // $this->getNav();
        // $this->getCatNav();
        // $this->get_config();
        // $this->get_user_info();
    }

    function get_config(){
        $wh['name'] = 'WEB_SITE_TITLE';
        $info = M('config')->where($wh)->getField('value');
        $this->assign('title',$info);
    }

    function get_user_info(){
        if (session('?user.id')) {
            $wh['id'] = session('user.id');
            $info = M('member')->where($wh)->find();
            $wh1['id'] = array('in',$info['group_ids']);
            $list = M('web_group')->where($wh1)->getField('title',true);
            $info['groupname'] = implode(',',$list);
            $this->assign('user_info',$info);
        }
    }

    function is_login(){
        if (!session('?user.id')) {
            $this->redirect('Index/index');
        }
    }
    //返回成功信息
    function ajaxSuccess($data=array('default'=>1),$info="提交成功"){
        $result = array('success'=>true,'info'=>$info,'data'=>$data);
        $this->ajaxReturn($result);
    }
    //返回失败信息
    function ajaxError($info="提交失败",$data=array('default'=>1)){
        $result = array('success'=>false,'info'=>$info,'data'=>$data);
        $this->ajaxReturn($result);
    }

    function getBanner($type=0){
        $wh['type'] = $type;
        $list = M('banner')->where($wh)->select();
        $this->assign("banner_list",$list);
    }

    function get_agree_count($tid,$aid){
        $wh['tid'] = $tid;
        $wh['aid'] = $aid;
        //$wh['uid'] = I('uid',0);
        $info['agree1'] = M('agree')->where(array_merge($wh,array('type'=>1)))->count();
        $info['agree2'] = M('agree')->where(array_merge($wh,array('type'=>2)))->count();
        return $info;
	}

    //获取图片
    function get_photos($id,$type=0){
		$wh['type'] = $type;
		$wh['obj_id'] = $id;
		return M('photos')->where($wh)->order('sort')->select();
	}

    //生成验证码
    function authCode($isnum=false){
        $authcode = rand(1000,9999);
        if ($isnum == false) {
            $authcode = '';
            for($i = 0; $i < 4; $i ++) {
                $randAsciiNumArray = array(rand(48,57),rand(65,90));
                $randAsciiNum = $randAsciiNumArray[rand(0,1)];
                $authcode .= chr($randAsciiNum);
            }
        }
        return $authcode;
    }

    //装修完成比例
    function get_completeness($id){
        $wh['status'] = 4;
        $wh['deco_id'] = $id;
        $comp_count = M('progress')->where($wh)->count();
        return ($comp_count) ? round($comp_count/15*100) : 0 ;
    }


    //获取当前装修节点
    function get_current_nodes($id){
        $wh['deco_id'] = $id;
        $wh['status'] = array('gt',0);
        $info = M('progress')->where($wh)->order('id desc')->find();
        return $info;
    }

    //获取节点费用
    function get_note_money($deco_id){
        $wh['id'] = $deco_id;
        $info = M('decorate')->where($wh)->find();
        if ($info) {
            $nodes_price = $info['total_price']/15;
            return round($nodes_price,2);
        }
        return -1;
    }

    //获取待付金额
    function get_obligation($id){
        $wh['id'] = $id;
        $info = M('decorate')->where($wh)->find();
        if ($info) {
            $nodes_price = $info['total_price']/15;
            $user_money = M('member')->where(array('id'=>$info['user_id']))->getField('money');
            $bligation = $user_money - $nodes_price;
            $bligation = ($bligation<0) ? abs(round($bligation,2)) : 0 ;
            return $bligation;
        }
        return 0;
    }

    //根据用户查询装修订单
    function find_deco_byuid($user_id,$field='*'){
        $wh['user_id'] = $user_id;
        $info = M('decorate')->field($field)->where($wh)->find();
        return $info;
    }

    //根据用户查询管家订单
    function find_order_byuid($user_id,$field=0){
        $wh['user_id'] = $user_id;
        if ($field) {
            return M('order')->where($wh)->getField($field);
        }else{
            return M('order')->where($wh)->find();
        }
    }

    function find_user_byuid($user_id,$field=0){
        $wh['id'] = $user_id;
        if ($field) {
            return M('member')->where($wh)->getField($field);
        }else{
            return M('member')->where($wh)->find();
        }
    }

    function setInc_view($id,$model){
        $wh['id'] = $id;
        M($model)->where($wh)->setInc('views',1);
    }


}
