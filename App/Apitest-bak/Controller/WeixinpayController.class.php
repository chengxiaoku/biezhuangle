<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/6/19
 * Time: 13:36
 */
namespace Apitest\Controller;
use Think\Controller;


class WeixinpayController extends BaseController{
    public $data = array(
        //真实数据   (以下所有信息 都为必填项 10)
        'body'             => '预存金额', //商品详情
    );
    private $appid = 'wx3f2595bbb60be87f';   //微信开放平台审核通过的应用APPID
    private $mch_id = '1482024392';  //微信支付分配的商户号
    private $notify_url = 'http://www.biezhuangle.com/api/weixinpay/validate'; // 第三次验签的回调地址 例“https://pay.weixin.qq.com/wxpay/pay.action”
    private $trade_type = 'APP';
    private $key        = '55eaf90c56769f0efb3aa0e5c9f33884';         //（密钥）与微信账号密钥保持一致   可以统一 用MD5加密
                                                                        //  MD5(123456)    :e10adc3949ba59abbe56e057f20f883e
    //接收前端传来的 IP  金额 订单号
    private $money = 0;
    private $spbill_create_ip ='';
    private $out_trade_no ='';
    /**
     * 构造函数，初始化成员变量
     * @param  String $appid  商户的应用ID
     * @param  Int $mch_id 商户编号
     * @param String $key 秘钥
     */
// 将构造函数设置为私有，禁止用户实例化该类
    function __construct($appid, $mch_id) {

        parent::__construct();
        if (is_string($appid) && is_string($mch_id)) {
            $this->appid = $appid;
            $this->mch_id = $mch_id;
            //$this->key = $key;
        }
    }

    /**
     * appid 和 mch_id 分别去到微信开放平台和微信商户平台中获取，
     * nonce_str (随机字符串) 很随意了，不长于32位就好。
     * 生成随机数并返回
     */
    private function getNonceStr() {
        $code = "";
        for ($i=0; $i > 10; $i++) {
            $code .= mt_rand(1000);        //获取随机数
        }
        $nonceStrTemp = md5($code);
        $nonce_str = mb_substr($nonceStrTemp, 5,37);      //MD5加密后截取32位字符
        return $nonce_str;
    }
    /**
     * 获取参数签名；
     * @param  Array  要传递的参数数组
     * @return String 通过计算得到的签名；
     * 注意：key的值长度不能超过32位。
     */
    private function getSign($params) {
        ksort($params);        //将参数数组按照参数名ASCII码从小到大排序
        foreach ($params as $key => $item) {
            if (!empty($item)) {         //剔除参数值为空的参数
                $newArr[] = $key.'='.$item;     // 整合新的参数数组
            }
        }
        $stringA = implode("&", $newArr);         //使用 & 符号连接参数
       
        $stringSignTemp = $stringA."&key=".$this->key;        //拼接key

        // key是在商户平台API安全里自己设置的
        $stringSignTemp = MD5($stringSignTemp);       //将字符串进行MD5加密
        $sign = strtoupper($stringSignTemp);      //将所有字符转换为大写
        return $sign;
    }

    /**
     * 获取微信支付类实例
     * 该类使用单例模式
     * @return WeEncryption         本类实例
     */
    public static function getInstance() {
        if(self::$instance == null) {
            self::$instance = new Self(APPID, MCHID, APP_KEY);
        }
        return self::$instance;
    }

    /**
     * 拼装请求的数据
     * @return  String 拼装完成的数据
     */
    private function setSendData($data) {
        $this->sTpl = "<xml>
                        <appid><![CDATA[%s]]></appid>
                        <body><![CDATA[%s]]></body>
                        <mch_id><![CDATA[%s]]></mch_id>
                        <nonce_str><![CDATA[%s]]></nonce_str>
                        <notify_url><![CDATA[%s]]></notify_url>
                        <out_trade_no><![CDATA[%s]]></out_trade_no>
                        <spbill_create_ip><![CDATA[%s]]></spbill_create_ip>
                        <total_fee><![CDATA[%d]]></total_fee>
                        <trade_type><![CDATA[%s]]></trade_type>
                        <sign><![CDATA[%s]]></sign>
                    </xml>";                          //xml数据模板

        $nonce_str = $this->getNonceStr();        //调用随机字符串生成方法获取随机字符串

        //生成订单号
        $out_no = $this->out_trade_no();
        $this->out_trade_no = $out_no;
        $data['body'] =$this->data['body'];
        $data['out_trade_no'] = $out_no;
        $data['spbill_create_ip'] = $this->spbill_create_ip;
        $data['total_fee'] = $this->money;
        $data['trade_type'] = 'APP';
        $data['appid'] = $this->appid;
        $data['mch_id'] = $this->mch_id;
        $data['nonce_str'] = $nonce_str;
        $data['notify_url'] = $this->notify_url;
        $data['trade_type'] = $this->trade_type;      //将参与签名的数据保存到数组
        // 注意：以上几个参数是追加到$data中的，$data中应该同时包含开发文档中要求必填的剔除sign以外的所有数据
        $sign = $this->getSign($data);        //获取签名

        $data = sprintf($this->sTpl, $this->appid, $data['body'], $this->mch_id, $nonce_str, $this->notify_url, $out_no, $data['spbill_create_ip'], $data['total_fee'], $this->trade_type, $sign);

        return $data;
    }


    /**
     * 发送下单请求；
     * @param  Curl   $curl 请求资源句柄
     * @return mixed       请求返回数据
     */
    public function sendRequest($curl, $data) {

        $data = $this->setSendData($data);//获取要发送的数据
        $url = "https://api.mch.weixin.qq.com/pay/unifiedorder";
        $curl->setUrl($url);          //设置请求地址
        $content = $curl->execute(true, 'POST', $data);       //执行该请求
        return $content;      //返回请求到的数据
    }

    /**
     * 生成订单号（退款单号）
     */
    function out_trade_no(){

        $yCode = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J');
        $orderSn = $yCode[intval(date('Y')) - 2011] . strtoupper(dechex(date('m'))) . date('d') . substr(time(), -5) . substr(microtime(), 2, 5) . sprintf('%02d', rand(0, 99));
        return $orderSn;
    }

    function index(){
        extract(I('post.'));
        //接收APP端传来的 金额 和 IP地址
        //$this->out_trade_no = $out_trade_no;
        $this->money = $money;
        $this->spbill_create_ip = $spbill_create_ip;
		
	/*	//模拟提交
		$this->out_trade_no = $this->out_trade_no();
		$this->money = 1;
		$this->spbill_create_ip = '127.0.0.1';*/
		// 模拟提交 end		

        Vendor('Curl.Curl');
        $curl = new \Curl();         //实例化传输类；
        $xml_data = $this->sendRequest($curl, $this->data);//发送请求

        //第一次验证返回来的结果集   $xml_data    继续完成第二次的验证
        //我们重点关注一下返回数据中的 prepay_id，
        //该参数是微信生成的预支付回话标识，
        //用于后续接口调用中使用，该值有效期为2小时
        $postObj = $this->xmlToObject($xml_data);            //解析返回数据

        if ($postObj === false) {
            echo 'FAIL';
            exit;             // 如果解析的结果为false，终止程序
        }

        if ($postObj->return_code == 'FAIL') {
            echo $postObj->return_msg;            // 如果微信返回错误码为FAIL，则代表请求失败，返回失败信息；
        } else {
            //如果上一次请求成功，那么我们将返回的数据重新拼装，进行第二次签名
            $resignData = array(
                'appid'        =>    $postObj->appid, //应用ID
                'partnerid'    =>    $postObj->mch_id, //商户号
                'prepayid'     =>    $postObj->prepay_id, //预支付交易会话ID
				'package'      =>    'Sign=WXPay', //扩展字段 （固定参数）
                'noncestr'     =>    $postObj->nonce_str,//随机字符串
                'timestamp'    =>    time(),//时间戳 
                
            );
			//二次签名；
			$sign = $this->getSign($resignData);
            //$sign = $this->getClientPay($resignData);
            //sign，appId，partnerId，prepayId，
            //nonceStr，timeStamp，package 这七个值一起返回个 APP 客户端。
            //将值传给APP端
            $resignData['sign'] = $sign;
            //将订单号传到APP 端 以便以后数据的储存
            $resignData['out_trade_no'] = $this->out_trade_no;

            //6-29 接口增加用户ID   用户预下单 (并没有真正支付)
            //  数据表中有一个字段是支付状态，
            //目前是未支付当支付成功时将其状态改为已支付


            $add_user_funds = array(
                'user_id' => $user_id,
                'trade_no' => $this->out_trade_no,
                //7月1日添加  可以删除
                'trade_amount' => $this->money/100
            );
            M('funds')->add($add_user_funds);
            //第二次签名结束
            $this->ajaxReturn($resignData);
        }
    }



    /**
     * 解析xml文档，转化为对象
     * @author 栗荣发 2016-09-20
     * @param  String $xmlStr xml文档
     * @return Object         返回Obj对象
     */
    public function xmlToObject($xmlStr) {
        if (!is_string($xmlStr) || empty($xmlStr)) {
            return false;
        }
        // 由于解析xml的时候，即使被解析的变量为空，依然不会报错，会返回一个空的对象，所以，我们这里做了处理，当被解析的变量不是字符串，或者该变量为空，直接返回false
        $postObj = simplexml_load_string($xmlStr, 'SimpleXMLElement', LIBXML_NOCDATA);
        $postObj = json_decode(json_encode($postObj));
        //将xml数据转换成对象返回
        return $postObj;
    }

    /**
     * 获取客户端支付信息
     * @author 栗荣发 2016-09-18
     * @param  Array $data 参与签名的信息数组
     * @return String       签名字符串
     */
    public function getClientPay($data) {
        $sign = $this->getSign($data);        // 生成签名并返回
        return $sign;
    }

    /**
     *
     *接收异步信息    以后要用  勿删
     */
    function validate(){
        //第三次验签
        $obj = $this->getNotifyData();
        if ($obj) {
            $data = array(
                'appid'                =>    $obj->appid,
                'mch_id'            =>    $obj->mch_id,
                'nonce_str'            =>    $obj->nonce_str,
                'result_code'        =>    $obj->result_code,
                'openid'            =>    $obj->openid,
                'trade_type'        =>    $obj->trade_type,
                'bank_type'            =>    $obj->bank_type,
                'total_fee'            =>    $obj->total_fee,
                'cash_fee'            =>    $obj->cash_fee,
                'transaction_id'    =>    $obj->transaction_id,
                'out_trade_no'        =>    $obj->out_trade_no,
                'time_end'            =>    $obj->time_end
            );
            // 拼装数据进行第三次签名
            $sign = $this->getSign($data);        // 获取签名

            /** 将签名得到的sign值和微信传过来的sign值进行比对，如果一致，则证明数据是微信返回的。 */
            //if ($sign == $obj->sign) {
            //向微信发送 成功的结果
                $reply = "<xml>
                    <return_code><![CDATA[SUCCESS]]></return_code>
                    <return_msg><![CDATA[OK]]></return_msg>
                </xml>";
                echo $reply;      // 向微信后台返回结果。
            //}
            //将数据存入数据库

            Vendor('Curl.Curl');
            $curl = new \Curl();
            $no = $obj->out_trade_no;
            $nonce_str = $this->getNonceStr();
            $data = array(
                'appid' => $this->appid,
                'mch_id' => $this->mch_id,
                'out_trade_no' => $no,
                'nonce_str' => $nonce_str
            );
            $sign = $this->getSign($data);
            $xml_data = '<xml>
                   <appid>%s</appid>
                   <mch_id>%s</mch_id>
                   <nonce_str>%s</nonce_str>
                   <out_trade_no>%s</out_trade_no>
                   <sign>%s</sign>
                </xml>';
            $xml_data = sprintf($xml_data, $this->appid, $this->mch_id, $nonce_str, $no, $sign);
            $url = "https://api.mch.weixin.qq.com/pay/orderquery";
            $curl->setUrl($url);
            $content = $curl->execute(true, 'POST', $xml_data);
            //return $content;

            $obj = $this->xmlToObject($content);
            //如果微信返回的信息是成功的 则将用忽的充值记录添加到 用户资金收支表
            if ($obj->trade_state == 'SUCCESS') {
                //计算实际金额
                $_money = $obj->cash_fee / 100;
                $data_add = array(
                    //储存用户充值金额
                    'trade_amount' => $_money,
                    'create_time' => time(),
                    'type' => 0,
                    'msg' => '微信充值',
                    'status' => 0,   //0不能动 在pay_success 充当搜索条件
                );
                //向用户资金收支表添加记录
                M('funds')->where(array('trade_no'=>$no))->save($data_add);

                //信息通知 (同时修改预订单的状态)
                $obj = A('Alipay');
                //参数2 用于 区分 支付方式
                $obj->pay_success($no,1);
            }
                exit;
        }
    }

    /**
     * 接收支付结果通知参数
     * @return Object 返回结果对象；
     */
    public function getNotifyData() {
        $postXml = $GLOBALS["HTTP_RAW_POST_DATA"];    // 接受通知参数；

        if (empty($postXml)) {
            return false;
        }
        $postObj = $this->xmlToObject($postXml);      // 调用解析方法，将xml数据解析成对象
        if ($postObj === false) {
            return false;
        }
        if (!empty($postObj->return_code)) {
            if ($postObj->return_code == 'FAIL') {
                return false;
            }
        }
        return $postObj;          // 返回结果对象；
    }


    /**
     * 查询订单状态
     * @param  Curl   $curl         工具类
     * @param  string $out_trade_no 订单号
     * @return xml               订单查询结果
     */
    public function queryOrder()
    {
        //接收APP端的 订单号  充值用户ID  用户IP
        //  out_trade_no  user_id  user_ip

        extract(I('post.'));
        $no = $out_trade_no;
        Vendor('Curl.Curl');
        $curl = new \Curl();
        $nonce_str = $this->getNonceStr();
        $data = array(
            'appid' => $this->appid,
            'mch_id' => $this->mch_id,
            'out_trade_no' => $no,
            'nonce_str' => $nonce_str
        );
        $sign = $this->getSign($data);
        $xml_data = '<xml>
                   <appid>%s</appid>
                   <mch_id>%s</mch_id>
                   <nonce_str>%s</nonce_str>
                   <out_trade_no>%s</out_trade_no>
                   <sign>%s</sign>
                </xml>';
        $xml_data = sprintf($xml_data, $this->appid, $this->mch_id, $nonce_str, $no, $sign);
        $url = "https://api.mch.weixin.qq.com/pay/orderquery";
        $curl->setUrl($url);
        $content = $curl->execute(true, 'POST', $xml_data);
        //return $content;

        $obj = $this->xmlToObject($content);
        //如果微信返回的信息是成功的 则将用忽的充值记录添加到 用户资金收支表
        if ($obj->trade_state == 'SUCCESS') {
                $this->ajaxSuccess('','支付成功');
        }else{
            $this->ajaxError('未支付');
        }
    }

    /**
     * 接收用户退款请求
     * (并未能正常使用)
     */
    function get_user(){
        extract(I('post'));
        $usermoney = M('member')->field('money')->where(array('id'=>$userid))->find();
        if($money > $usermoney){
            $this->ajaxError('余额不足');
        }else{
            $this->Home_index($userid,$money);
        }
    }

    //退款操作
    function Home_index($userid,$money){
        $data = M('funds')->field('trade_no,trade_amount')->where("type=0 AND status=1 AND user_id = $userid")->order('id DESC')->find();
        //获取随机字符串
        $getNonceStr = $this->getNonceStr();
        //获取退款订单号
        $out_trade_no = $this->out_trade_no();
        $string = "appid=".$this->appid."&mch_id=".$this->mch_id."&nonce_str=".$getNonceStr."&out_refund_no=".$out_trade_no."&out_trade_no=".$data['trade_no']."&refund_fee=".$money."&total_fee=".$data['trade_amount']."&key=".$this->key;

        $ref= md5($string);//sign加密MD5

        $refund=array(
            'appid' => $this->appid,
            'mch_id' => $this->mch_id,//商户号
            'nonce_str' => $getNonceStr,//随机字符串
            'out_refund_no'=>$out_trade_no,
            'out_trade_no'=>$data['trade_no'],
            'refund_fee'=>$money,
            'total_fee'=>$data['trade_amount'],
            'sign'=>$ref
        );

        $url="https://api.mch.weixin.qq.com/secapi/pay/refund";;//微信退款地址，post请求
        $xml=arrayToXml($refund);


        $ch=curl_init();
        curl_setopt($ch,CURLOPT_URL,$url);
        curl_setopt($ch,CURLOPT_HEADER,1);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,1);//证书检查
        curl_setopt($ch,CURLOPT_SSLCERTTYPE,'pem');
        curl_setopt($ch,CURLOPT_SSLCERT,dirname(__FILE__).'/WxPayPubHelper/cacert/apiclient_cert.pem');
        curl_setopt($ch,CURLOPT_SSLCERTTYPE,'pem');
        curl_setopt($ch,CURLOPT_SSLKEY,dirname(__FILE__).'/WxPayPubHelper/cacert/apiclient_key.pem');
        curl_setopt($ch,CURLOPT_SSLCERTTYPE,'pem');
        curl_setopt($ch,CURLOPT_CAINFO,dirname(__FILE__).'/WxPayPubHelper/cacert/rootca.pem');
        curl_setopt($ch,CURLOPT_POST,1);
        curl_setopt($ch,CURLOPT_POSTFIELDS,$xml);

        $data=curl_exec($ch);
        if($data){ //返回来的是xml格式需要转换成数组再提取值，用来做更新
            curl_close($ch);
            dump($data);
        }else{
            $error=curl_errno($ch);
            echo "curl出错，错误代码：$error"."<br/>";
            echo "<a href='http://curl.haxx.se/libcurl/c/libcurs.html'>;错误原因查询</a><br/>";
            curl_close($ch);
            echo false;
        }
    }















}

