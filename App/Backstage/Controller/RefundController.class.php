<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/7/22
 * Time: 10:17
 */
namespace Backstage\Controller;
use  Backstage\Controller\BaseController;

/**
 * Class Refund
 * @package Backstage\Controller
 * 用户退款管理
 */
class RefundController extends BaseController{
    function index(){
        $count = M('refund a')->join('gms_member as b ON a.user_id = b.id')->count();
        $Page = new \Think\Page($count,I('limit',15));
        $data = M('refund a')
            ->field('a.*,b.username,b.nickname,b.money as user_money,b.status as user_status,b.id as user_id')
            ->join('gms_member as b ON a.user_id = b.id')
            ->order('a.create_time DESC')
            ->limit($Page->firstRow.','.$Page->listRows)
            ->select();
        $this->assign('data',$data);
        $this->assign('page',$Page->show());
        $this->display();
    }

    /**
     * 审核
     */
    function Auditing(){
        if(IS_POST){
            $type = I('post.type','','trim');
            $id = I('post.id','','trim');
            // ok 代表同意 NO代表拒绝
            if($type == 'ok'){
                $data['status'] = 3;
            }else if($type == 'no'){
                $data['status'] = 2;
            };
            $trans = M();
            $trans->startTrans();
            $data['update_time'] = time();
            $refund = M('refund');
            $money = $refund->field('money,user_id')->where(array('id'=>$id,'status'=>1))->find();
            $bool = $refund->where(array('id'=>$id,'status'=>1))->save($data);
            if($type == 'no'){
                $trans->commit();
                //拒绝成功
                echo 3;
                exit;
            }
            M('member')->where(array('id'=>$money['user_id']))->setDec('money',$money['money']);
            if($bool){
                $trans->commit();
                //操作成功
                echo 1;
            }else{
                $trans->rollback();
                echo 2;
            }
        }
    }
}