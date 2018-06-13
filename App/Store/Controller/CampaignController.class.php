<?php
/**
 * Created by PhpStorm.
 * 7月初活动策划
 * User: 程龙飞  710425820@QQ.com
 * Date: 2017/6/28
 * Time: 14:01
 */
namespace Store\Controller;
use Think\Controller;

class CampaignController extends Controller{

    /**
     * 效果展示
     */
    function index(){
        $this->display();
    }

    /**
     * 获取缴费的用户信息
     */
    function get_user_data(){
        //M('funds')
        extract(I('get.'));
        $funds = M('new_funds');
        $data = $funds->find($id);
        //用户不存在
        if(empty($data)){
            echo 1;
        }else{
            $user_data =
                $funds->alias('a')
                ->field('a.amount,b.username,b.phone,b.nickname,a.status')
                ->join('gms_member b on a.user_id = b.id')
                ->where(array('a.id'=>$id))
                ->select();
            echo json_encode($user_data[0]);
        }
    }
    /*
     * 获取现有最后的ID
     */
    function get_user_lastdata_sum(){
        $data = M('new_funds')->field('max(id) lastid')->find();
        //存储最后一个用户
        M('hd')->add(array('hd_id'=>$data['lastid']));
        echo $data['lastid'];
    }

    /**
     * 查看活动会员缴费情况
     */
    function userinfo(){
        $hdlastid =M('hd')->field('max(hd_id) as hd_id')->find();
        $hdlastid = $hdlastid['hd_id'];
      
        //$hdlastid = 801;
        if(empty($hdlastid)){
            header('Content-type:text/html;charset=utf-8');
            echo '暂无数据';
        }else{

            $data = M('new_funds')->alias('a')
                ->field('b.id,b.username,b.nickname,b.email,b.phone,b.address,a.amount,a.status,a.timestamp,a.msg')
                ->join('gms_member b on a.user_id = b.id')
                ->where("a.id > $hdlastid AND a.status =1")
                ->select();
            //获取人数
            $num = M('new_funds')->alias('a')
                ->join('gms_member b on a.user_id = b.id')
                ->where("a.id > $hdlastid AND a.status =1")
                ->count();
            $this->assign('num',$num);
            $this->assign('data',$data);
            $this->display();
        }
    }

}