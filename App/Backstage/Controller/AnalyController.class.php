<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/8/15
 * Time: 9:11
 */
namespace Backstage\Controller;
class AnalyController extends BaseController{
    function index(){
        //获取微信用户登录人数
        $member_sum = M('member')->count();
        $wx_member_sum = M('member')->where(array('is_wx'=>1))->count();
        //获取 微信 以及支付宝 支付人数
        $wx = M('new_funds')->where(array('msg'=>1))->count();
        $zfb = M('new_funds')->where(array('msg'=>2))->count();

        $this->assign('wx',$wx);
        $this->assign('zfb',$zfb);
        $this->display();
    }

    function echatMoney(){
       
        $data = M('analy')->field('title,title_ch,visit_num')->select();
        $newarr = array();
        foreach ($data as $key => $val){
            $newarr['title'][$key] = $val['title_ch'];
            $newarr['visit_num'][$key] = $val['visit_num'];
        }
        $this->ajaxReturn($newarr);
    }

    /**
     * 获取用户充值时间金额
     */
    function get_user_fund(){
        $data = M('new_funds')
            ->field("FROM_UNIXTIME(create_time,'%Y-%m-%d') as time,SUM(amount) as amount")
            ->group('time')
            ->order('time DESC')
            ->limit($num = 5)
            ->select();

        $newarr = array();
        foreach ($data as $key => $val){
            $newarr['time'][$key] = $val['time'];
            $newarr['amount'][$key] = $val['amount'];
        }
        //数据反转
        $new_data['time'] = array_reverse($newarr['time']);
        $new_data['amount'] = array_reverse($newarr['amount']);
        $this->ajaxReturn($new_data);
    }
}