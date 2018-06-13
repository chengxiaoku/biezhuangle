<?php

//获取评论数量
function for_comment_count($list,$tid=1){
    $wh['tid'] = $tid;
    foreach ($list as $key => $value) {
        $wh['aid'] = $value['id'];
        $count = M('comment')->alias('a')->join('gms_member b on a.uid = b.id')->where($wh)->count();
        $list[$key]['comm_count'] = $count;
    }
    return $list;
}

//人民币转大写
function toChineseNumber($money){
    $money = round($money,2);
    $cnynums = array("零","壹","贰","叁","肆","伍","陆","柒","捌","玖");
    $cnyunits = array("圆","角","分");
    $cnygrees = array("拾","佰","仟","万","拾","佰","仟","亿");
    list($int,$dec) = explode(".",$money,2);
    $dec = array_filter(array($dec[1],$dec[0]));
    $ret = array_merge($dec,array(implode("",cnyMapUnit(str_split($int),$cnygrees)),""));
    $ret = implode("",array_reverse(cnyMapUnit($ret,$cnyunits)));
    return str_replace(array_keys($cnynums),$cnynums,$ret);
}
function cnyMapUnit($list,$units) {
    $ul=count($units);
    $xs=array();
    foreach (array_reverse($list) as $x) {
        $l=count($xs);
        if ($x!="0" || !($l%4))
            $n=($x=='0'?'':$x).($units[($l-1)%$ul]);
        else $n=is_numeric($xs[0][0])?$x:'';
            array_unshift($xs,$n);
    }
    return $xs;
}


//处理订单函数
//更新订单状态，写入订单支付后返回的数据
function order_handle($ordid){
    
    $data['support_time']=time();
    $data['status']      =2;
    
    M('item_order')->where('orderId='.$ordid)->save($data);
}

function alipay($order){
    require_once VENDOR_PATH.'Aliapp/lib/alipay_core.function.php';
    require_once VENDOR_PATH.'Aliapp/lib/alipay_rsa.function.php';
    $alipay_config=C('ALIPAY_CONFIG.APP_pay');

    //构造要请求的参数数组，无需改动
    $parameter = array(
        'partner'=>'2088911347181223',//合作者身份ID
        'seller_id'=>'',
        'out_trade_no'=>$order['out_trade_no'],//商户网站唯一订单号
        'subject'=>$order['subject'],//商品名称
        'body'=>'订单支付',//商品详情
        'total_fee'=>$order['price'],
        'notify_url'=>$alipay_config['notify_url'],//服务器异步通知页面路径
        'service'=>'mobile.securitypay.pay',//接口名称
        'payment_type'=>1,//支付类型
        '_input_charset'=>$alipay_config['charset'],//参数编码字符集
    );
    //将post接收到的数组所有元素，按照“参数=参数值”的模式用“&”字符拼接成字符串。
    $data = createLinkstring($parameter);
    //打印待签名字符串。工程目录下的log文件夹中的log.txt。
    logResult($data);
    //将待签名字符串使用私钥签名,且做urlencode. 注意：请求到支付宝只需要做一次urlencode.
    $rsa_sign = urlencode(rsaSign($data, $alipay_config['rsaPrivateKey']));
    //把签名得到的sign和签名类型sign_type拼接在待签名字符串后面。
    $data = $data.'&sign='.'"'.$rsa_sign.'"'.'&sign_type='.'"'.$alipay_config['signType'].'"';
    //返回给客户端,建议在客户端使用私钥对应的公钥做一次验签，保证不是他人传输。
    //echo $data;
    return $data;
}

function aliapp($order){
    vendor('aop.AopClient');
    vendor('aop.request.AlipayTradeAppPayRequest');
    $config=C('ALIPAY_CONFIG.APP_pay');
    $aop = new \AopClient;
    $aop->gatewayUrl = "https://openapi.alipay.com/gateway.do";
    $aop->appId = $config['app_id'];
    $aop->rsaPrivateKey = $config['rsaPrivateKey'];
    $aop->format = $config['format'];
    $aop->charset = $config['charset'];
    $aop->signType = $config['signType'];
    $aop->alipayrsaPublicKey = $config['alipayrsaPublicKey'];
    //实例化具体API对应的request类,类名称和接口名称对应,当前调用接口名称：alipay.trade.app.pay
    $request = new \AlipayTradeAppPayRequest();
    //SDK已经封装掉了公共参数，这里只需要传入业务参数
    $bizcontent = "{\"body\":\"test\","
                    . "\"subject\":\"".$order['subject']."\","
                    . "\"out_trade_no\":\"".$order['out_trade_no']."\","
                    . "\"timeout_express\":\"30m\","
                    . "\"total_amount\":\"".$order['price']."\","
                    . "\"product_code\":\"QUICK_MSECURITY_PAY\""
                    . "}";
    $request->setNotifyUrl($config['notify_url']);
    $request->setBizContent($bizcontent);
    //这里和普通的接口调用不同，使用的是sdkExecute 
    $response = $aop->sdkExecute($request); 
    //htmlspecialchars是为了输出到页面时防止被浏览器将关键参数html转义，实际打印到日志以及http传输不会有这个问题

    \Think\Log::write("---------------支付宝请求开始----------------");
    \Think\Log::write('response_data:'.urlencode($response));
    return urldecode($response);
    return htmlspecialchars($response);//就是orderString 可以直接给客户端请求，无需再做处理。
}

function ifnull($value,$default=0){
    return $value?$value:$default;
}

//获取中文字符拼音首字母
function getFirstCharter($str)
{
    if (empty($str)) {
        return '';
    }
    $fchar = ord($str{0});
    if ($fchar >= ord('A') && $fchar <= ord('z')) return strtoupper($str{0});
    $s1 = iconv('UTF-8', 'gb2312', $str);
    $s2 = iconv('gb2312', 'UTF-8', $s1);
    $s = $s2 == $str ? $s1 : $str;
    $asc = ord($s{0}) * 256 + ord($s{1}) - 65536;
    if ($asc >= -20319 && $asc <= -20284) return 'A';
    if ($asc >= -20283 && $asc <= -19776) return 'B';
    if ($asc >= -19775 && $asc <= -19219) return 'C';
    if ($asc >= -19218 && $asc <= -18711) return 'D';
    if ($asc >= -18710 && $asc <= -18527) return 'E';
    if ($asc >= -18526 && $asc <= -18240) return 'F';
    if ($asc >= -18239 && $asc <= -17923) return 'G';
    if ($asc >= -17922 && $asc <= -17418) return 'H';
    if ($asc >= -17417 && $asc <= -16475) return 'J';
    if ($asc >= -16474 && $asc <= -16213) return 'K';
    if ($asc >= -16212 && $asc <= -15641) return 'L';
    if ($asc >= -15640 && $asc <= -15166) return 'M';
    if ($asc >= -15165 && $asc <= -14923) return 'N';
    if ($asc >= -14922 && $asc <= -14915) return 'O';
    if ($asc >= -14914 && $asc <= -14631) return 'P';
    if ($asc >= -14630 && $asc <= -14150) return 'Q';
    if ($asc >= -14149 && $asc <= -14091) return 'R';
    if ($asc >= -14090 && $asc <= -13319) return 'S';
    if ($asc >= -13318 && $asc <= -12839) return 'T';
    if ($asc >= -12838 && $asc <= -12557) return 'W';
    if ($asc >= -12556 && $asc <= -11848) return 'X';
    if ($asc >= -11847 && $asc <= -11056) return 'Y';
    if ($asc >= -11055 && $asc <= -10247) return 'Z';
    return '#';
}


/**
 * 相减，供模板使用
 * @param <type> $a
 * @param <type> $b
 */
function template_substract($a,$b){
    echo $a-$b;
}

function arrayToXml($arr){
    $xml = "<root>";
    foreach ($arr as $key=>$val){
        if(is_array($val)){
            $xml.="<".$key.">".arrayToXml($val)."</".$key.">";
        }else{
            $xml.="<".$key.">".$val."</".$key.">";
        }
    }
    $xml.="</root>";
    return $xml;
}
?>