<?php

function IS_WebAuth($Auth_Rule){
    $Auth = new \Common\Libs\WebAuth();
    if(M('menus')->where(array('name'=>$Auth_Rule))->getField('is_auth')==0){
        return true;
    }
    if (session('?uid')) {
        $AUTH_KEY=session('user.id');
        if (empty($Auth_Rule)) {
            $Auth_Rule = MODULE_NAME . '/' . CONTROLLER_NAME . '/' . ACTION_NAME;
        }
        if ($Auth->check($Auth_Rule,$AUTH_KEY)) {
            return true;
        }else{
            return false;
        }
    }else{
        return false;
    }

}

function mdate($time = NULL) {
    $text = '';
    $time = $time === NULL || $time > time() ? time() : intval($time);
    $t = time() - $time; //时间差 （秒）
    $y = date('Y', $time)-date('Y', time());//是否跨年
    switch($t){
     case $t == 0:
       $text = '刚刚';
       break;
     case $t < 60:
      $text = $t . '秒前'; // 一分钟内
      break;
     case $t < 60 * 60:
      $text = floor($t / 60) . '分钟前'; //一小时内
      break;
     case $t < 60 * 60 * 24:
      $text = floor($t / (60 * 60)) . '小时前'; // 一天内
      break;
     case $t < 60 * 60 * 24 * 3:
      $text = floor($time/(60*60*24)) ==1 ?'昨天 ' . date('H:i', $time) : '前天 ' . date('H:i', $time) ; //昨天和前天
      break;
     case $t < 60 * 60 * 24 * 30:
      $text = date('m月d日 H:i', $time); //一个月内
      break;
     case $t < 60 * 60 * 24 * 365&&$y==0:
      $text = date('m月d日', $time); //一年内
      break;
     default:
      $text = date('Y年m月d日', $time); //一年以前
      break;
    }
    return $text;
}

function getnickname($id){
    $wh['id'] = $id;
    return M('member')->where($wh)->getField('nickname');
}

function get_comm_count($pid,$type=1){
    $wh['pid'] = $pid;
    $wh['type'] = $type;
    return M('comment')->where($wh)->count();
}

/**
* 跳向支付宝付款
* @param  array $order 订单数据 必须包含 out_trade_no(订单号)、price(订单金额)、subject(商品名称标题)
*/
function alipay($order){
    vendor('Alipay.alipay_submit','','.class.php');
    // 获取配置
    $config=C('ALIPAY_CONFIG');
    $data=array(
        "service" => $config['service'],
        "partner" => $config['partner'], // partner 从支付宝商户版个人中心获取
        "seller_id" => $config['seller_id'],
        "payment_type" => $config['payment_type'], // 支付类型对应请求时的 payment_type 参数,原样返回。固定设置为1即可
        "return_url" => $config['return_url'], // 页面跳转 同步通知 页面路径 支付宝处理完请求后,当前页面自 动跳转到商户网站里指定页面的 http 路径。
        "notify_url" => $config['notify_url'], // 异步接收支付状态通知的链接
        "out_trade_no" => $order['out_trade_no'], // 订单号
        "total_fee" => $order['price'], // 订单价格单位为元
        "subject" => $order['subject'], // 商品名称商品的标题/交易标题/订单标 题/订单关键字等
        "show_url" => "http://cloudhouse.youjiawang.com/Home/Public/index", // 商品展示网址,收银台页面上,商品展示的超链接。
        "app_pay" => "Y",//启用此参数能唤起钱包APP支付宝
        "_input_charset" => $config['input_charset'], // 编码格式
    );
    $alipay=new \AlipaySubmit($config);
    $new=$alipay->buildRequestPara($data);
    $go_pay=$alipay->buildRequestForm($new, 'get','支付');
    echo $go_pay;
}
