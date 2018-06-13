<?php
/*
 * 控制器
 * Time   : 2016年07月26日
 */
namespace Apitest\Controller;
use Think\Controller;

class MemberController extends BaseController {

    function login(){
        if(IS_POST){
            $phone = I("phone");
            $password = I("password");
            if($phone){
                $wh['username'] = $phone;
                if($password){
                    $wh['password'] = md5($password);
                }
                $info = M("member")->where($wh)->find();
                if($info){
                    $data['last_login_time'] = time();
                    $data['last_login_ip'] = get_client_ip();
                    M("member")->where($wh)->save($data);
                    $this->ajaxSuccess($info,"登录成功！");
                }else{
                   $this->ajaxError("账号或密码错误");
                }
            }else{
                $this->ajaxError("请输入手机号！");
            }
        }
    }

    function login_sms(){
        if (IS_POST) {
            $phone = I("phone");
            $wh['username'] = $phone;
            $info = M('member')->where($wh)->find();
            if($info){
                $this->ajaxSuccess($info,"登录成功！");
            }else {
                $data['phone'] = $phone;
                $data['username'] = $phone;
                $data['nickname'] = substr($phone,-4);
                $data['password'] = md5('biezhuangle');
                $data['create_time'] = time();
                $data['last_login_time'] = time();
                $data['last_login_ip'] = get_client_ip();
                $result = M('member')->add($data);
                if($result){
                    $info = M("member")->where(array('id'=>$result))->find();
                    $this->ajaxSuccess($info,"登录成功！");
                }else {
                    $this->ajaxError();
                }
            }
        }
    }

    function login_wx(){
        if (IS_POST) {
            extract(I('post.'));
            if ($access_token && $openid) {
                $json_data = curl_get("https://api.weixin.qq.com/sns/userinfo?access_token=".$access_token."&openid=".$openid);
                $wx_data = json_decode($json_data,true);
                if ($wx_data['openid']) {
                    $wh['openid'] = $wx_data['openid'];
                    $wechat = M('loginwx')->where($wh)->find();
                    if (!$wechat) {
                        //创建用户
                        $data['nickname'] = $wx_data['nickname'];
                        $data['head_img'] = $wx_data['headimgurl'];
                        //默认密码 : biezhuangle
                        $data['password'] = md5('biezhuangle');
                        $data['create_time'] = time();
                        $data['last_login_time'] = time();
                        $data['last_login_ip'] = get_client_ip();
                        $data['is_wx'] = 1;
                        $result = M('member')->add($data);
                        if($result){
                            $wx_data['user_id'] = $result;
                            if(M('loginwx')->add($wx_data)){
                                $info = M("member")->where(array('id'=>$result))->find();
                                $this->ajaxSuccess($info,"登录成功！");
                            }else{
                                $this->ajaxError();
                            }
                        }else {
                            $this->ajaxError();
                        }
                    }else {
                        $info = M("member")->where(array('id'=>$wechat['user_id']))->find();
                        $this->ajaxSuccess($info,"登录成功！");
                    }
                }else {
                    $this->ajaxError();
                }
            }else{
                $this->ajaxError();
            }
        }
    }

	function register(){
        if (IS_POST) {
            $m = D('member');
            $data = $m->create();
            if($data){
                $data['username'] = I('phone');
                $ph = I('phone');
                $data['nickname'] = substr($ph,0,3 ).'****'.substr($ph,-4);
                $data['create_time'] = time();
                $data['last_login_time'] = time();
                $data['last_login_ip'] = get_client_ip();
                $result = $m->add($data);
                if($result){
                    $this->ajaxSuccess(array('uid'=>$result),"注册成功！");
                }else {
                    $error = $m->getError();
                    $this->ajaxError($error ? $error : "操作失败！");
                }
            }else{
                $error = $m->getError();
                $this->ajaxError($error ? $error : "操作失败！");
            }
        }
	}

    function exist_phone($phone){
        $wh['phone'] = $phone;
        $info = M('member')->where($wh)->find();
        if ($info) {
            $this->ajaxSuccess($info,"账户存在！");
        }else{
            $this->ajaxError("该账号不存在！");
        }
    }

    function forget(){
        if (IS_POST) {
            $wh['phone'] = I('phone');
            $data['password'] = md5(I('password'));
            if(M('member')->where($wh)->save($data)){
                $info = M('member')->where($wh)->find();
                $this->ajaxSuccess($info);
            }else{
                $this->ajaxError();
            }
        }
    }

    function find(){
        $wh['id'] = I('id');
        $info = M('member')->where($wh)->find();
        $this->ajaxReturn($info);
    }

    function edit(){
        if(IS_POST){
            $m = M('member');
            $data = $m->create();
            if($data){
                if($data['head_img']){
                    $data['head_img'] = base64toimg($data['head_img']);
                }
                $result = $m->save($data);
                if($result === false){
                    $error = $m->getError();
                    $this->ajaxError($error ? $error : "操作失败！");
                }else {
                    $wh['id'] = $data['id'];
                    $info = M('member')->where($wh)->find();
                    $this->ajaxSuccess($info);
                }
            }else{
                $error = $m->getError();
                $this->ajaxError($error ? $error : "操作失败！");
            }
        }
    }

    function get_favor($type=1,$user_id){
        $wh['tid'] = $type;
        $wh['uid'] = $user_id;
        $list = M('favor')->alias('a')
            ->field("a.aid,b.id user_id,b.nickname,b.head_img")
            ->join('gms_member b on a.uid = b.id')->where($wh)->select();

        $list = $this->each_data($type,$list);

        $this->ajaxReturn($list);
    }

    function get_comment($type,$user_id=0){

        $wh['tid'] = $type;
        $wh['uid'] = $user_id;
        $list = M('comment')->alias('a')
            ->field("a.aid,a.content comment,b.id user_id,b.nickname,b.head_img")
            ->join('gms_member b on a.uid = b.id')->where($wh)->select();
        $list = $this->each_data($type,$list);
        $this->ajaxReturn($list);
    }

    function each_data($type,$list){
        $i =1;
        foreach ($list as $key => $value) {
            switch ($type) {
                case 1:
                    $data = A('Article')->find_data($value['aid']);
                    if ($data) {
                        $list[$key] = array_merge($list[$key],$data);
                    }else{
                        unset($list[$key]);
                    }
                    break;
                case 2:
                    $data = A('Decorate')->find_notes($value['aid']);
                    if ($data) {
                        $list[$key] = array_merge($list[$key],$data);
                    }else{
                        unset($list[$key]);
                    }
                    break;
                case 3:
                    $data = A('Decorate')->find_company($value['aid']);
                    if ($data) {
                        $list[$key] = array_merge($list[$key],$data);
                    }
                    break;
                case 4:
                    $i = 2;
                    //根据ID查找管家日记
                    $whe['a.order_id'] = $value['aid'];
                    $_date = M('diary')->alias('a')
                        ->field('a.id diary_id,a.order_id,a.title diary_title,a.views,c.name,c.photo,c.id butler_id,d.*')
                        ->join('gms_order b on a.order_id = b.deco_id')
                        ->join('gms_butler c on b.butler_id = c.id')
                        ->join('gms_decorate d on b.deco_id = d.id')
                        ->where($whe)
                        ->group('a.order_id')
                        ->order('diary_id desc')
                        ->select();
                    foreach ($_date as $k => $v) {
                        $_date[$k]['comm_count'] = $this->get_comm_count($v['diary_id']);
                        $_date[$k]['image'] = $this->get_photoc($v['diary_id']);
                    }
                    if(!is_null($_date[0])){
                        $list['list'][$key] = $_date[0];
                    }
                    break;
                case 5:
                    $list = M('gallery_album a')
                        ->where(array('id'=>$value['aid']))
                        ->select();
                    break;
            }
        }
        if($i ==2){
            return $list['list'];
        }else{
            return $list;
        }
    }

    function get_comm_count($id){
        $count = M('butler a')
            ->join('gms_diary b ON b.butler_id = a.id')
            ->join('gms_comment c ON c.pid = b.id and c.tid = 2')
            ->where(array('a.id'=>$id))->count();
        return $count;
    }

    //获取日记任意张图片
    function get_photoc($id){
        $list = M('diary_detail')->field('b.image')->alias('a')->join('gms_diary_image b on b.pid = a.id')->where(array("a.diary_id"=>$id))->select();
        $new_array = array();
        if(is_array($list)){
            foreach ($list as $key => $val ){
                $new_array[] = $val['image'];
            }
        }
        return $new_array;
    }
    function get_project($user_id){
        $info = $this->get_funds_info($user_id);
        $info['progress'] = $this->get_completeness($info['id']);
        $info['obligation'] = $this->get_note_money($info['id']);
        $this->ajaxReturn($info);
    }

    //获取财务状况
    function get_funds_info($user_id){
        $info = $this->find_deco_byuid($user_id,'id,status,total_price');
        $wh['type'] = 0;
        $wh['user_id'] = $user_id;
        $wh['status'] = 1;
        $amount = M('funds')->where($wh)->sum('trade_amount');
        $info['prestore'] = $amount?$amount:0;
        $wh['type'] = 1;
        $amount = M('funds')->where($wh)->sum('trade_amount');
        $info['payment'] = $amount?$amount:0;
        return $info;
    }

    //获取装修效果图
    function get_design($user_id,$type=1){
        $deco = $this->find_deco_byuid($user_id);
        $wh['type'] = $type;
        $wh['deco_id'] = $deco['id'];
        $list = M('design')->where($wh)->group('room')->select();
        foreach ($list as $key => $value) {
            $list[$key]['room_name'] = get_design_room($value['room']);
            $wh['room'] = $value['room'];
            $list[$key]['data'] = M('design')->field('image')->where($wh)->select();
        }
        $this->ajaxReturn($list);
    }

    //获取补充合同
    function get_compact($user_id){
        $deco = $this->find_deco_byuid($user_id);
        $wh['status'] = 0;
        $wh['deco_id'] = $deco['id'];
        $list = M('compact')->where($wh)->select();
        $this->ajaxReturn($list);
    }

    function find_compact($id){
        $info = M('compact')->where($wh)->find();
        $this->ajaxReturn($info);
    }

    //申请补充合同
    function apply_compact(){
        if (IS_POST) {
            $deco = $this->find_deco_byuid(I('user_id'));
            $data['deco_id'] = $deco['id'];
            $data['content'] = I('content');
            $data['create_time'] = time();
            $result = M('compact')->add($data);
            if ($result) {
                $this->ajaxSuccess(array('id'=>$result));
            }else{
                $this->ajaxError();
            }
        }
    }

    //终止合同
    function stop_compact(){
        if (IS_POST) {
            $deco = $this->find_deco_byuid(I('user_id'));
            $wh['id'] = $deco['id'];
            $data['status'] = 7;
            if(M('decorate')->where($wh)->save($data) === false){
                $this->ajaxError();
            }else{
                $this->ajaxSuccess();
            }
        }
    }

    //施工详情
    function get_works($user_id){
        $info = $this->find_deco_byuid($user_id);
        $wh['deco_id'] = $info['id'];
        $info['data'] = M('works')->where($wh)->select();
        $this->ajaxReturn($info);
    }

    /**
     * 管家向用户发起收款请求
     * @param $butler_id
     */
    function user_pay_butler($butler_id,$user_id){
        //判断管家是否 能够发起 收款通知  butler_pay_num
        $request = M('order')->field('request,user_agree')->where(array('butler_id'=>$butler_id,'user_id'=>$user_id))->find();
        if($request['user_agree'] == 2){
            $this->ajaxError('您已支付过管家费用');
        }
        if($request['request'] > C('butler_pay_num')){
            $this->ajaxError('您已超过最大请求次数');
        }else{
            M('order')->where(array('butler_id'=>$butler_id,'user_id'=>$user_id))->setInc('request',1);
            //向管家发起推送
            //获取用户name
            //修改管家费用支付状态
            M('order')->where(array('butler_id'=>$butler_id,'user_id'=>$user_id))->setField('user_agree',1);

            $realname = M('member')->field('realname,username')->find($user_id);
            $data['type'] = 1;
            $data['tag_type'] = 4;
            $data['create_time'] = time();
            $data['obj_id'] = $user_id;
            $data['tag_id'] = $butler_id;
            $content = '尊敬的管家您好，您的收款申请已发送给用户'.$realname['realname'].'请耐心等待用户确认。';
            $data['content'] = $content;
            M('message')->add($data);
            $butlername = get_butler_phone($butler_id);
            sendNotifySpecial_butler($butlername,$content);
            //向用户发送推送，消息
            //获取管家名称
            $butler_name = M('butler')->field('name')->find($butler_id);
            $_data['obj_id'] = $butler_id;
            $_data['tag_id'] = $user_id;
            $price = $this->get_pay_butler_money($butler_id,$user_id);
            $_content = '您的装修管家'.$butler_name['name'].'向您发起收款请求，金额共合计￥'.$price;
            $_data['content'] = $_content;
            $_data['type'] = 1;
            $_data['create_time'] = time();
            $_data['tag_type'] = 1;
            M('message')->add($_data);
            sendNotifySpecial($realname['username'],$_content);
        }
    }

    /**
     *
     * 计算699 与 非 699 用户 要支付 管家多少钱
     */
    function get_pay_butler_money($butler_id,$user_id){
        $deco = M('order')->field('deco_id,area')->where(array('butler_id'=>$butler_id,'user_id'=>$user_id))->find();
        //699用户 有装修订单
        $_area = 0;
        if($deco['deco_id']){
            $area = M('decorate')->field('area')->find($deco['deco_id']);
            $_area = $area['area'];
        }else{
            //非699用户  待开发   面积在 order 表中的erae 字段中
            $_area = $deco['area'];
        }
        //面积 乘 管家每平方的价格
        $butler_price = $_area * C('butler_price');
        return $butler_price;
    }

    /**
     * 用户拒绝管家的申请
     */
    function user_nopay($butler_id,$user_id){
        //向管家发送通知
        M('order')->where(array('butler_id'=>$butler_id,'user_id'=>$user_id))->setField('user_agree',0);
        $realname = M('member')->field('realname,username')->find($user_id);
        $data['type'] = 1;
        $data['tag_type'] = 4;
        $data['create_time'] = time();
        $data['obj_id'] = $user_id;
        $data['tag_id'] = $butler_id;
        $content = '尊敬的管家您好，您的收款申请被用户'.$realname['realname'].'拒绝，请联系用户查询原因。';
        $data['content'] = $content;
        M('message')->add($data);
        $butlername = get_butler_phone($butler_id);
        sendNotifySpecial_butler($butlername,$content);
        //向用户发送通知
        $butler_name = M('butler')->field('name')->find($butler_id);
        $_data['obj_id'] = $butler_id;
        $_data['tag_id'] = $user_id;
        //$price = $this->get_pay_butler_money($butler_id,$user_id);
        $_content = '您拒绝了您的装修管家'.$butler_name['name'].'向您发起的收款请求';
        $_data['content'] = $_content;
        $_data['type'] = 1;
        $_data['create_time'] = time();
        $_data['tag_type'] = 1;
        M('message')->add($_data);
        sendNotifySpecial($realname['username'],$_content);
    }


    /**
     * 用户同意管家的收款申请
     * 管家监理结束后 用户支付费用
     */
    function user_yespay($butler_id,$user_id){
        //$model = M('diary');
        //$model->startTrans();
        //$model->commit();
        //$model->rollback();
        //判断该用户是否是699用户
        //996
        if(IS_POST){
            //查询需要支付的金额
            $price = $this->get_pay_butler_money($butler_id,$user_id);
            //查询用户的余额
            $user = M('member')->field('money')->find($user_id);
            if($user['money'] < $price){
                $this->ajaxError('余额不足');
            }else{
                //开始进行支付
                $member = M('member');
                $member->startTrans();
                $member->where(array('id'=>$user_id))->setDec('money',$price);
                $bool = M('butler')->where(array('id'=>$butler_id))->setInc('money',$price);
                if($bool){
                    $member->commit();
                    M('order')->where(array('butler_id'=>$butler_id,'user_id'=>$user_id))->setField('user_agree',2);
                    $realname = M('member')->field('realname,username')->find($user_id);
                    $data['type'] = 1;
                    $data['tag_type'] = 4;
                    $data['create_time'] = time();
                    $data['obj_id'] = $user_id;
                    $data['tag_id'] = $butler_id;
                    $content = '尊敬的管家您好，您已收到用户'.$realname['realname'].'的收款。金额为'.$price;
                    $data['content'] = $content;
                    M('message')->add($data);
                    $butlername = get_butler_phone($butler_id);
                    sendNotifySpecial_butler($butlername,$content);
                    //向用户发送通知
                    $butler_name = M('butler')->field('name')->find($butler_id);
                    $_data['obj_id'] = $butler_id;
                    $_data['tag_id'] = $user_id;
                    //$price = $this->get_pay_butler_money($butler_id,$user_id);
                    $_content = '您支付了您的装修管家'.$butler_name['name'].'金额为'.$price;
                    $_data['content'] = $_content;
                    $_data['type'] = 1;
                    $_data['create_time'] = time();
                    $_data['tag_type'] = 1;
                    M('message')->add($_data);
                    sendNotifySpecial($realname['username'],$_content);
                    $this->ajaxSuccess(array(),'支付成功');
                }else{
                    $member->rollback();
                    $this->ajaxError('支付失败');
                }
            }

        }
    }

    /*
     * 获取信息
     */
    function get_order_info($user_id){

        $data = M('order a')->join('gms_butler as b ON a.butler_id = b.id')->where(array('a.user_id'=>$user_id))->select();
        $this->ajaxReturn($data);
    }


}
