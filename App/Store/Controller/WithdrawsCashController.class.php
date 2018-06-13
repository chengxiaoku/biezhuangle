<?php
namespace Store\Controller;
/**
 * Created by PhpStorm.
 * User: CLF
 * Date: 2017/6/15
 * Time: 15:17
 * 提现管理
 */

class WithdrawsCashController extends BaseController
{

    public function index()
    {
        $user_id = session('user.id');
        $tocash = M('tocash');
        //获取余额
        $money = M('company')->field('money')->find($user_id);
        $count = $tocash->alias('a')
                ->join("gms_company b ON b.id=a.obj_id")
                ->where(array('a.obj_id'=>$user_id))->count();
        $Page = new \Think\Page($count,I('limit',10));
        $list = $tocash->alias('a')
                ->field('b.name,a.create_time,a.amount,a.status')
                ->join("gms_company b ON b.id=a.obj_id")
                ->order('a.create_time DESC')
                ->where(array('a.obj_id'=>$user_id))
                ->limit($Page->firstRow.','.$Page->listRows)
                ->select();

        $this->assign('money',$money);
        $this->assign('list',$list);
        $this->assign('page',$Page->show());
        $this->display();
    }

    /**
     * 提现的具体操作
     */
    public function show()
    {
        $user_id = session('user.id');
        $company = M('company');
        if(IS_POST){
            //提现金额计算
            $txje = I("post.txje",'','trim');
            $txje = (float)$txje;
            if(!is_float($txje) || empty($txje)){
                $this->ajaxError("提现金额不正确！");
                exit;
            }
            //$company->field('alipay_no,money')->find($user_id);
            $list = $company->field('money,alipay_no')->find($user_id);
            $money = $list['money'];
            if($txje > $money){
                $this->ajaxError("账户余额不足！");
                exit;
            }else{
                $val = trim($list['alipay_no']);
                if(empty($val)){
                    $this->ajaxError("请先添加支付宝账号！");
                    exit;
                }
                //得出提现后的余额
                $_money = $money - $txje;
                $data = array(
                    'id' => $user_id,
                    'money' => $_money,
                );
                $money_save_bool = $company->save($data);
                if(!$money_save_bool){
                    $error = $company->getError();
                    $this->ajaxError($error ? $error : "提现失败！");
                }
                //提现记录增加一条
                $data = array(
                    'obj_id'=>$user_id,
                    'create_time' => time(),
                    'amount' => $txje
                );
                $tocash = M('tocash');
                $add_bool = $tocash->add($data);
                if($add_bool){
                    $this->ajaxSuccess('','提现成功！');
                }else{
                    $error = $tocash->getError();
                    $this->ajaxError($error ? $error : "提现失败！");
                }
            }
        }else{

            $list = $company->field('alipay_no,money')->find($user_id);
            $this->assign('list',$list);
            $this->display();
        }

    }
}