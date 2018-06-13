<?php
/*
* 控制器
* Time   : 2016年07月26日
*/
namespace Api\Controller;
use Think\Controller;

class AlipayController extends BaseController {

    /**
    * return_url接收页面
    */
    public function alipay_return(){
        // 引入支付宝
        vendor('Alipay.alipay_notify','','.class.php');
        $config = C('ALIPAY_CONFIG');
        $notify = new \AlipayNotify($config);
        // 验证支付数据
        $status = $notify->verifyReturn();
        if($status){
            // 下面写验证通过的逻辑 比如说更改订单状态等等 $_GET['out_trade_no'] 为订单号；
            $result = $this->pay_success($_GET['out_trade_no']);
            if ($result) {
                
                $this->success('支付成功',U('Funds/payment',array('user_id'=>$result)));
            }
        }else{
            $this->success('支付失败',U('Funds/payment',array('user_id'=>10)));
        }
    }
    
    /**
    * notify_url接收页面
    */
    public function alipay_notify(){
        // 引入支付宝
        vendor('aop.AopClient');
        $config = C('ALIPAY_CONFIG.APP_pay');
        $aop = new \AopClient;
        $aop->alipayrsaPublicKey = $config['alipayrsaPublicKey'];
        \Think\Log::write("---------------支付宝通知请求开始1----------------");
        \Think\Log::write('post_data:'.http_build_query($_POST));
        $flag = $aop->rsaCheckV1($_POST, NULL, "RSA");
        if ($flag) {
            $result = $this->pay_success($_POST['out_trade_no']);
        }else {
            \Think\Log::write("！！！！！！！支付宝请求失败！！！！！！！");
            \Think\Log::write("flag:".$flag);
        }
    }

    /**
    * 手机网站notify_url接收页面
    */
    public function aliwap_notify(){
        // 引入支付宝
        vendor('Alipay.alipay_notify','','.class.php');
        $config = C('ALIPAY_CONFIG');
        $alipayNotify = new \AlipayNotify($config);
        // 验证支付数据
        $verify_result = $alipayNotify->verifyNotify();
        if($verify_result) {
            // 下面写验证通过的逻辑 比如说更改订单状态等等 $_POST['out_trade_no'] 为订单号；
            $result = $this->pay_success($_GET['out_trade_no']);
            $this->ajaxSuccess();
        }else {
            $this->ajaxError();
        }
    }
    
    function pay_success($trade_no,$biaoshi = 2){
        \Think\Log::write('订单'.$trade_no.':'."请求开始");
        $wh['status'] = 0;
        $wh['trade_no'] = $trade_no;
        $info = M('funds')->where($wh)->find();
        if ($info) {
            $result = true;
            $trans = M();
            $trans->startTrans();
            if (M('funds')->where($wh)->setField('status',1)) {
                //开始 (活动用户支付成功入库)
                $data['user_id'] = $info['user_id'];
                $data['trade_no'] = $info['trade_no'];
                $data['amount'] = $info['trade_amount'];
                $data['msg'] = $biaoshi;
                $public = A('Public');
                $public->new_funds($data);
                //结束
                $wh1['id'] = $info['user_id'];
                if (M('member')->where($wh1)->setInc('money',$info['trade_amount'])) {
                    $trans->commit();
                    R('Api/Message/add_recharge',array($info['id']));
                    \Think\Log::write('订单'.$trade_no.':'.$error ? $error : "操作成功！");
                    return  $info['user_id'];
                }else {
                    $trans->rollback();
                    $error = M()->getError();
                    \Think\Log::write('订单'.$trade_no.':'.$error ? $error : "操作失败！",'WARN');
                }
            }
        }
        \Think\Log::write('订单'.$trade_no.':'."---------------支付宝通知请求结束----------------");
        return false;
    }

    function alipay_gateway(){
        
    }

   
}