<?php
/*
 * 控制器
 * Time   : 2016年07月26日
 */
namespace Apitest\Controller;
use Boris\DumpInspector;
use Think\Controller;

class ButlerController extends BaseController {

    /**
     * @param int $limit
     * 获取管家列表
     */
	function index($p=1,$limit=10){
        //增加用户行为记录表
        M('analy')->where(array('title'=>'butler'))->setInc('visit_num');

        $list = M('butler')->page($p,$limit)->order('id desc')->select();
        foreach($list as $k=>$v){
            //评论数
            $list[$k]['comm_count'] = $this->get_comm_count($v['id']);
            //全部工地(装修日记数量)
            $list[$k]['note_count'] = $this->get_note_count($v['id']);
            //在建工地
            $list[$k]['site_count'] = $this->get_site_count($v['id']);
            $list[$k]['level_name'] = $this->get_level_name($v['level']);
        }
        $this->ajaxReturn($list);
    }

    function detail(){
        extract(I("get."));
        $wh['id'] = $id;
        $info = M('butler')->find($id);
        $info['comm_count'] = $this->get_comm_count($id);
        $info['note_count'] = $this->get_note_count($id);
        $info['site_count'] = $this->get_site_count($id);
        $info['level_name'] = $this->get_level_name($info['level']);
        $this->ajaxReturn($info);
    }

    function diary($limit=100){
        //管家ID    施工状态 (订单表)
        extract(I('get.'));
        //按条件筛选
        if(empty($butler_id) && empty($status)){
            $whe = '';
        }elseif(!empty($butler_id) && !empty($status)){
            $whe = array(
                'a.node_id' => 1,
                'a.butler_id' => $butler_id,
                //'d.status' => 5,  //将施工中的 数据拉取出来
                'b.status' => 1,
            );
        }elseif(empty($status)){
            $whe = array(
                'a.node_id' => 1,
                'a.butler_id' => $butler_id,
            );
        }elseif(empty($butler_id)){
            $whe = array(
                'a.node_id' => 1,
                //'d.status' => 5,
                'b.status' => 1,
            );
        }

        //全部拉取出来
        $list = M('diary')->alias('a')
            ->field('a.id diary_id,a.order_id,a.title diary_title,a.views,c.name,c.photo,c.id butler_id,d.*,c.name butler_name')
            ->join('gms_order b on a.order_id = b.deco_id')
            ->join('gms_butler c on b.butler_id = c.id')
            ->join('gms_decorate d on b.deco_id = d.id')
            ->where($whe)
            ->group('a.order_id')
            ->order('diary_id desc')
            ->select();
        $schedule_sum = C('Schedule_sum');

        foreach ($list as $k => $v) {
            $list[$k]['comm_count'] = $this->get_comm_count($v['diary_id']);
            $order_id = $v['order_id'];
            $max_id = $this->get_last_diry_detail($order_id);
            $_num = $this->get_baifen($order_id)/$schedule_sum*100;
            $list[$k]['schedule'] = round($_num,2);
            $list[$k]['image'] = $this->get_photoc($max_id);
        }
        $this->ajaxReturn($list);
    }

    /**
     * 计算施工进度百分比
     */
    function get_baifen($_id){
        $count = M('diary')->where(array('order_id'=>$_id))->count();
        return $count;
    }

    /**
     * @param $_id
     * @return mixed
     * 获取最后一个节点的ID
     */
    function get_last_diry_detail($_id){
        $where['order_id'] = $_id;
        $data = M('diary')->field('id')->where($where)->order('node_id desc')->find();
        return $data['id'];
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
    //日记数量
    function get_note_count($id){
        $data = M('diary')->alias('a')
            ->field('a.id diary_id,a.order_id,a.title diary_title,a.views,c.name,c.photo,c.id butler_id,d.*')
            ->join('gms_order b on a.order_id = b.deco_id')
            ->join('gms_butler c on b.butler_id = c.id')
            ->join('gms_decorate d on b.deco_id = d.id')
            ->where(array('a.butler_id'=>$id))
            ->group('a.order_id')
            ->select();
        return count($data);
    }

    //监管工地数量
    function get_site_count($id){
        $where['a.butler_id'] = $id;
        $where['_logic'] = 'AND';
        //$where['d.status'] = 5;
        $where['b.status'] = 1;
        $data = M('diary a')
            ->join('gms_order b on a.order_id = b.deco_id')
            ->join('gms_decorate d on b.deco_id = d.id')
            ->where($where)->group('a.order_id')->select();
        return count($data);
    }

    function get_comm_count($id){
        $count = M('butler a')
            ->join('gms_diary b ON b.butler_id = a.id')
            ->join('gms_comment c ON c.pid = b.id and c.tid = 2')
            ->where(array('a.id'=>$id))->count();
        return $count;
    }

    function get_level_name($level){
        $data = array("普通","银牌","金牌");
        return $data[$level];
    }

    function get_message($butler_id=0,$tag_cat=0){
        $wh['tag_type'] = 4;
        $wh['tag_id'] = $butler_id;
        if ($tag_cat) {
            $wh['tag_cat'] = $tag_cat;
        }
        $list['data'] = M('message')->where($wh)->order('`read`,id desc')->select();
        $wh['read'] = 0;
        $list['read'] = M('message')->where($wh)->count();
		$this->ajaxReturn($list);
    }

    function send_message(){
        if (IS_POST) {
            $model = M('message');
            $data = $model->create();
            $data['type'] = 4;
            $data['obj_id'] = I('butler_id');
            $data['tag_type'] = 1;
            $data['create_time'] = time();
            $tag_ids = explode(',',I('tag_ids'));
            foreach ($tag_ids as $key => $value) {
                $data['tag_id'] = $value;
                $dataList[] = $data;
            }
            if($model->addAll($dataList)){
                $this->ajaxSuccess();
            }else {
                $this->ajaxError();
            }
        }
    }

    function find_order_byuser($user_id=0){

        $wh['a.user_id'] = $user_id;
        $info = M('order')->alias('a')
            ->field('a.*,a.id order_id,b.address deco_address,b.status as c_status,c.id comp_id,c.name comp_name,c.phone comp_phone,d.*')
            ->join('left join gms_decorate b on a.deco_id = b.id')
            ->join('left join gms_company c on b.comp_id = c.id')
            ->join('gms_member d on a.user_id = d.id')
            ->where($wh)->find();
        if ($info) {
            $node = $this->get_current_nodes($info['deco_id']);
            $info['node_id'] = $node['id'];
            $info['node_title'] = $node['title'];
        }
        $this->ajaxReturn($info);
    }

    function find_butler($butler_id=0){
        $wh['id'] = $butler_id;
        $info = M('butler')->where($wh)->find();
        $this->ajaxReturn($info);
    }

    function set_butler(){
        if (IS_POST) {
            extract(I('post.'));
            $wh['id'] = $butler_id;
            $data['name'] = $name;
            if(!empty($photo)){
                $data['photo'] = base64toimg($photo);
            }
            $data['phone'] = $phone;
            $data['wechat'] = $wechat;
            $data['intro'] = $intro;
            $result = M('butler')->where($wh)->save($data);
            if ($result !== false) {
                $data = M('butler')->find($butler_id);
                $this->ajaxSuccess($data);
            }else{
                $this->ajaxError();
            }
        }
    }

    function set_order(){
        if (IS_POST) {
            extract(I('post.'));
            $wh['user_id'] = $user_id;
            $count = M('order')->where($wh)->count();

            $deco = $this->find_deco_byuid($user_id);

            if(0< $deco['status'] && $deco['status'] < 8) {
            //有699订单
                if ($count == 0) {
                    //生成预约管家订单
                    $data['user_id'] = $user_id;
                    $data['butler_id'] = $butl_id;
                    $data['deco_id'] = $deco['id'];
                    $data['create_time'] = time();
                    $result = M('order')->add($data);

                    //查询用户的电话
                    $phone = M('member')->field('username,realname,phone')->find($user_id);
                    //给管家发送消息
                    $data['tag_id'] = $butl_id;
                    $content  = $phone['realname'].'客户在'.date('Y年m月d日 H:i:s',time()).'预约了您，请您尽快与客户联系，客户联系电话:'.$phone['phone'];
                    $data['content'] = $content;
                    $data['read'] = 0;
                    $data['type'] = 1;
                    $data['create_time'] = time();
                    $data['tag_type'] = 4;
                    $result1 = M('message')->add($data);
                    //发送推送

                    $butler_phone = get_butler_phone($butl_id);
                    sendNotifySpecial_butler($butler_phone,$content);

                    if ($result && $result1) {
                        //发送消息 与推送
                        $this->send_butler_message($butl_id,$user_id,$phone);
                        //向后台返回数据 标示预约成功
                        $this->ajaxSuccess(array('id' => $result));
                    } else {
                        $this->ajaxError();
                    }
                } else {
                    $this->ajaxError("您已预约过装修管家");
                }
            }else{
            //没有699订单
            //APP端实现跳转
            //"user_id = $user_id AND butler_id = $butler_id"
                $where['user_id'] = $user_id;
                $where['_logic'] = 'AND';
                $where['butler_id'] = $butl_id;
                $bool = M('order')->where($where)->find();
                if($bool){
                    $this->ajaxError('',array('default'=>'false'));
                }else{
                    $this->ajaxError('',array('default'=>'success'));
                }
            }
        }
    }

    /**
     * @internal  $butl_id  @管家ID
     * @internal  $user_id  @用户ID
     * @param $phone    @资源
     *    预约装修小哥消息提示
     */
    private function send_butler_message($butl_id,$user_id,$phone){
        //查询装修小哥姓名
        $butler_name = M('butler')->field('name')->find($butl_id);
        $add_data['tag_id'] = $user_id;
        $add_data['type'] = 1;
        $add_data['create_time'] = time();
        $add_data['tag_type'] = 1;
        $add_data['tag_cat'] = 1;
        $text = "尊敬的用户您好，您已成功预约装修小哥 ".$butler_name['name'].',请保持电话畅通，装修小哥会尽快与您联系。';
        $add_data['content'] = $text;
        M('message')->add($add_data);
        sendNotifySpecial($phone['username'],$text);
    }
    /**
     * 用户没有699套餐 直接预约装修小哥的话
     * APP 端实现页面跳转 请用户输入管架订单信息
     * 此接口用于保存用户的信息
     */
    function add_butler_order(){
        if (IS_POST){
            extract(I('post.'));
            $data['butler_id'] = $butler_id;
            $data['user_id'] = $user_id;
            //$data['deco_id'] = ''  非699用户没有订单号
            $data['address'] = $address;
            $data['realname'] = $realname;
            $data['tel'] = $tel;
            $data['area'] = $area;
            $data['create_time'] = time();
            $bool = M('order')->add($data);
            if($bool){
                $this->ajaxSuccess($bool,'您预约成功');
            }else{
                $this->ajaxError();
            }
        }
    }
    /**
     * 备份  暂时没有启用
     */
    function set_order_bak(){
        if (IS_POST) {
            extract(I('post.'));
            $wh['user_id'] = $user_id;
            $count = M('order')->where($wh)->count();

            $deco = $this->find_deco_byuid($user_id);

            if(0< $deco['status'] && $deco['status'] < 8) {
                //有699订单
                if ($count == 0) {
                    $data['user_id'] = $user_id;
                    $data['butler_id'] = $butl_id;
                    $data['deco_id'] = $deco['id'];
                    $data['create_time'] = time();
                    $result = M('order')->add($data);
                    if ($result) {
                        $this->ajaxSuccess(array('id' => $result));
                    } else {
                        $this->ajaxError();
                    }
                } else {

                    $this->ajaxError("您已预约过装修管家");
                }
            }else{
                //没有699订单
                $this->ajaxError("您还未预约装修服务");
            }
        }
    }
    function login(){
        if(IS_POST){
            extract(I('post.'));
            if($username && $password){
                $wh['phone|account'] = $username;
                $wh['password'] = md5($password);
                $info = M("butler")->where($wh)->find();
                if($info){
                    $this->ajaxSuccess($info,"登录成功！");
                }else{
                   $this->ajaxError("账号或密码错误");
                }
            }else{
                $this->ajaxError("账号或密码错误");
            }
        }
    }

    function get_money($butler_id=0){
        $wh['id'] = $butler_id;
        $info = M('butler')->where($wh)->getField('money');
        $this->ajaxReturn($info);
    }

    function get_income($butler_id,$p=1,$num=10){

        $wh['butler_id'] = $butler_id;
        $list = M('income')
            ->where($wh)
            ->order('create_time DESC')
            ->page($p,$num)
            ->select();
        $this->ajaxReturn($list);
    }

    function set_alipay(){
        if (IS_POST) {
            extract(I('post.'));
            $wh['id'] = $butler_id;
            $data['alipay_no'] = $alipay_no;
            $data['alipay_name'] = $alipay_name;
            $result = M('butler')->where($wh)->save($data);
            if($result !== false){
                $this->ajaxSuccess();
            }else {
                $this->ajaxError();
            }
        }
    }

    function tocash(){
        if (IS_POST) {
            $m = D('Cms/tocash');
            $data = $m->create();
            if($data){
                //判断账户余额
                $wh['id'] = $data['obj_id'];
                $amount = M('butler')->where($wh)->getField('money');
                if ($amount >= $data['amount'] && $data['obj_type'] == 1) {
                    $result = $m->add($data);
                    //存入管家收支情况
                    extract(I('post.'));
                    $_data['obj_id'] = $data['obj_id'];
                    $_data['butler_id'] = $data['obj_id'];
                    $_data['type'] = 1;
                    $_data['title'] = '提现';
                    $_data['amount'] = $data['amount'];
                    $_data['create_time'] = time();
                    M('income')->add($_data);
                    if($result){
                        M('butler')->where($wh)->setDec('money',$data['amount']);
                        //获取管家的余额
                        if($obj_type == 1){
                            $money = M('butler')->field('money')->find($data['obj_id']);
                        }
                        $this->ajaxSuccess(array('id'=>$result,'money'=>$money['money']));
                    }else {
                        $error = $m->getError();
                        $this->ajaxError($error ? $error : "操作失败！");
                    }
                }else {
                    $this->ajaxError("余额不足！");
                }
            }else{
                $error = $m->getError();
                $this->ajaxError($error ? $error : "操作失败！");
            }
        }
    }

    function validate(){
        if (IS_POST) {
            $wh['id'] = I('butler_id');
            $wh['password'] = md5(I('password'));
            if (M('butler')->where($wh)->find()) {
                $this->ajaxSuccess();
            }else{
                $this->ajaxError();
            }
        }
    }

    function feature($butler_id){

        $data = M('order')->field('deco_id')->where("butler_id = $butler_id")->select();

        if (is_array($data)){
            $str = '';
            foreach ($data as $key => $value){
                $str .=','.$value['deco_id'];
            }
        }
        $str = trim($str,',');

        $data = array();
        $wh['a.id'] = array('in',$str);

        $wh['d.status'] = array('EGT',3);
        $list = M('decorate')->alias('a')
            ->field('d.*,b.name comp_name,c.nickname,c.realname,c.head_img')
            ->join('gms_company b on a.comp_id = b.id ')
            ->join('gms_member c on a.user_id = c.id ')
            ->join('gms_progress d on a.id = d.deco_id ')
            ->where($wh)->select();

        foreach ($list as $key => $value) {
            $wh1['node_id'] = $value['node_id'];
            $wh1['butler_id'] = $butler_id;

            if(!M('diary')->where($wh1)->find()){
                $list[$key]['status_name'] = get_notes_status($value['status']);
                $data[] = $list[$key];
            }
        }

        //评论的数量
        $count = M('butler a')
            ->join('gms_diary b ON b.butler_id = a.id')
            ->join('gms_comment c ON c.pid = b.id and c.tid = 2')
            ->where(array('a.id'=>$butler_id))->count();

        $result['comm_count'] = $count;
        $result['want_count'] = count($data);
        $result['income'] = M('butler')->where(array('id'=>$butler_id))->getField('money');
        $result['data'] = $data;

        $this->ajaxReturn($result);
    }

    /**
     *装修日记详情
     */
    function rijixq(){

        //增加用户行为记录表
        M('analy')->where(array('title'=>'butler_diary'))->setInc('visit_num');
        //获取日记ID
        extract(I('get.'));
        $_rj_id = $rj_id;
        $data = M('diary')->alias('a')
            ->field("FROM_UNIXTIME(c.check_time,'%Y-%m-%d') end_time,a.node_id,c.status,a.score,b.name,a.order_id")
            ->join('gms_nodes b on a.node_id = b.id')
            ->join('gms_progress c on c.deco_id = a.order_id and c.node_id = a.node_id')
            ->where("order_id = $_rj_id")->select();

        foreach ($data as $key => $value){
                $data[$key]['status'] = $this->sg_zt($value['status']);
                $data[$key]['status_no'] = $value['status'];
        }
        if(!$data){
            $this->ajaxError();
        }
        $i = 0 ;
        foreach ($data as $key => $value){
            $data[$i]['score'] =$this->score($value['score']-1);
            $i++;
        }

        //获取停工天数 get_downtime
        $order_id = $data[0]['order_id'];

        $mes_data = M('decorate')->where(array('id'=>$order_id))->find();
        $start_date = $mes_data['start_date'];
        //实际天数 = 现在时间减 开始时间

        $sjts = time() - $start_date;
        $sjts = date('d', $sjts);

        //获取停工天数
        $works = M('works');
        $downtime = $works->field('status')->where("deco_id = $_rj_id AND status = 2")->count();
        $downtime_string = $works->field('create_time')->where("deco_id = $_rj_id AND status = 2")->select();
        if($downtime_string){
            foreach ($downtime_string as $key => $val){
                $downtimelist[0]['downtime_string'][] = $val['create_time'];
            }
        }else{
            $downtimelist[0]['downtime_string'] = array();
        }

        //停工开始日期
        $stop_work_create_time = $works->field('create_time')->where("deco_id = $_rj_id AND status = 2")->select();
        foreach ($stop_work_create_time as $key => $value){
            $downtimelist[0]['stop_work_create_time'][$key] = $value['create_time'];
        }
        //停工天数
        //停工时间
        $downtimelist[0]['downtime'] =$downtime;
        $downtimelist[0]['list'] = $data;

        $butler = M('order')->field('butler_id')->where(array('deco_id'=>$rj_id))->find();
        $_data = M('butler')->select($butler['butler_id']);
        $_data[0]['level_name'] = $this->get_level_name($_data[0]['level']);

        $com_data = M('company')->find($mes_data);

        $downtimelist[0]['list_butler'] = $_data;
        $downtimelist[0]['list_com'] = $com_data;
        $downtimelist[0]['list_time'] = $mes_data;
        $downtimelist[0]['sjts'] = $sjts;

        if($share && $share == 'clf'){
            //判断是否是分享动作
            //计算计划天数  与实际天数

            $data = $downtimelist[0];
            $start_date = $data['list_time']['start_date'];
            $end_date = $data['list_time']['end_date'];
            $_time = $end_date - $start_date;
            //实际施工
            $this->assign('order_id',$data['list_time']['id']);
            $this->assign('gq',date('d',$_time));
            $this->assign('info',$downtimelist[0]);
            $this->display();
        }else{
            $this->ajaxReturn($downtimelist[0]);
        }
    }


    /**
     *获取施工状态
     */
    function sg_zt($key){
        //1:施工|2:停工|3:待验收|4:待缴费
        $data = array("未开始","未施工","施工中","待验收","已完工","待缴费","拒绝验收");
        return $data[$key];
    }
    /**
     * 用户评价
     */
    function score($key){
        //1.完美2.优秀.3满意4及格5差6很差
        $score=array('很差','差','及格','满意','优秀');
        return $score[$key];
    }

    /**
     * 获取具体日记内某个节点的内容
     */
    function rj_jd(){
        extract(I('get.'));

        $data = M('diary')->alias('a')
            ->field("FROM_UNIXTIME(c.check_time,'%Y-%m-%d') end_time,a.score,b.name")
            ->join('gms_nodes b on a.node_id = b.id')
            ->join('gms_progress c on c.deco_id = a.order_id and c.node_id = a.node_id')
            ->where("order_id = $butlerid AND a.node_id = $jdid")->group("b.id")->select();
        if(!$data){
            $this->ajaxError();
        }
        $i = 0 ;
        foreach ($data as $key => $value){
            $data[$i]['user_pj'] =$this->score($value['score']-1);
            $i++;
        }

        $_id = $this->get_diary_id($butlerid,$jdid);

        $data1 = M('diary_detail')->where("diary_id=$_id")->select();
        $img = M('diary_image');
        $_new_array = array();

        foreach ($data1 as $key => $val){
            $pid = $val['id'];
            $img_data = $img->where("pid=$pid")->select();

            $new_arr = array();
            if(is_array($img_data)){
                foreach ($img_data as $key=>$img_key){
                    $new_arr[] = $img_key['image'];
                }

            }else{
                $new_arr[] = '';
            }
             $_new_array[] = $val;
             $_new_array1[] =$new_arr;
        }

        for ($i= 0; $i<count($_new_array);$i++){
            $_new_array[$i]['img'] = $_new_array1[$i];
        }
        foreach ($_new_array as $key => $val){
            $data[0]['list'][] = $val;
        }
        if($share && $share == 'clf'){
           /* header('Content-type:text/html;charset=utf-8');
           dump($data[0]);
            exit;*/

            $_id = $this->get_diary_id($butlerid,$jdid);

            $this->assign('info',$data[0]);
            //获取用户评论
            $data1 = M('comment')->alias('a')
                ->field("b.nickname,b.head_img,a.content,a.create_time")
                ->join("gms_member b on a.uid = b.id")
                ->where("a.pid=$_id")->select();
            $this->assign('user_com',$data1);
            $this->assign('jdid',$jdid);
            $this->display('rjjd');
        }else{
            $this->ajaxReturn($data[0]);
        }
    }


    /**
     * 获取ID
     * @param $butlerid 日记ID
     * @param $jdid 节点ID
     */
    function get_diary_id($butlerid,$jdid){
        $list = M('diary')->field("id")->where("order_id = $butlerid AND node_id=$jdid")->find();
        if(empty($list)){
            $this->ajaxReturn(array());
        }else{
            return $list['id'];
        }
    }
    /**
     * 获取用户评论
     */
    function get_user_comment(){
        extract(I('get.'));

        $_id = $this->get_diary_id($butlerid,$jdid);

        $data = M('comment')->alias('a')
            ->field("b.nickname,b.head_img,a.content,a.create_time")
            ->join("gms_member b on a.uid = b.id")
            ->where("a.pid=$_id")->select();
        $this->ajaxReturn($data);
    }

    /**
     * 2017-07-05
     * 管家装修日记节点详情添加用户评论
     * 参数 用户 ID  文章ID  内容
     */
    function add_user_comment(){
        extract(I('post.'));

        $_id = $this->get_diary_id($order_id,$jdid);
        $data['pid'] = $_id;
        $data['uid'] = $user_id;
        $data['content'] = $content;
        $data['ip'] = get_client_ip();
        $data['create_time'] = time();
        $data['tid'] = 2;
        $bool = M('comment')->add($data);
        if($bool){
            $this->ajaxSuccess();
        }else{
            $this->ajaxError();
        }
    }

    /**
     * 获取管家评论
     * 参数: 管家ID
     */
    function get_butler_comment($butler_id,$page=1,$num=10){
        $data = M('butler a')
            ->field('c.create_time,c.content,d.nickname,d.head_img')
            ->join('gms_diary b ON b.butler_id = a.id')
            ->join('gms_comment c ON c.pid = b.id and c.tid = 2')
            ->join('gms_member d ON d.id = c.uid')
            ->page($page,$num)
            ->where(array('a.id'=>$butler_id))->select();
        $this->ajaxReturn($data);
    }

    /**
     * 判断用户是否有管家日记
     * 返回值 无日记 false 有几日返回日记ID
     */
    function judge_user_order($user_id){
        //$data = M('decorate')->field('id')->where(array('user_id'=>$user_id))->find();
        //该用户无订单
        $data = M('order')->field('deco_id,butler_id')->where(array('user_id'=>$user_id))->find();
        if(!$data){
            $this->ajaxError();
        }else{
            $this->ajaxSuccess(array('order_id'=>$data['deco_id'],'butler_id'=>$data['butler_id']));
        }
    }

    /**
     * 管家日记 用户是否已读
     */
    function set_user_look($diary_detail_id){
        $look = M('diary_detail')->field('look')->find($diary_detail_id);
        if($look == 1 ){
            $this->ajaxError();
        }
        $look = M('diary_detail')->where(array('id'=>$diary_detail_id))->setField('look',1);
        if($look){
            //向管家发送用户已读信息
            //获取用户姓名
            $diary_id =  M('diary_detail')->field('diary_id')->find($diary_detail_id);
            $diary = M('diary')->field('order_id,butler_id')->find($diary_id['diary_id']);
            $user= M('decorate')->field('user_id')->find($diary['order_id']);
            $member = M('member')->field('realname')->find($user['user_id']);

            $data['tag_id'] = $diary['butler_id'];
            $content = '您的用户'.$member['realname'].'在'.date('Y年m月d日 H:i:s',time()).'查看了您的管家节点日记';
            $data['content'] = $content;
            $data['read'] = 0;
            $data['type'] = 1;
            $data['create_time'] = time();
            $data['tag_type'] = 4;
            $result1 = M('message')->add($data);
            //发送客户端推送
            $butler_phone = get_butler_phone($diary['butler_id']);
            sendNotifySpecial_butler($butler_phone,$content);
            $this->ajaxSuccess();
        }else{
            $this->ajaxError();
        }
    }

    /**
     * 激光测试
     */
    function test(){
        sendNotifySpecial_butler('13213692344','这是个极光测试');
    }

}

