<?php
/*
 * 控制器
 * Time   : 2016年07月26日
 */
namespace Api\Controller;
use Think\Controller;

class DiaryController extends BaseController {

	function find_data($id){
        $wh['id'] = $id;
        $info = M('diary')->where($wh)->find();
        if ($info) {
            M('diary')->where($wh)->setInc('views',1);
        }
        return $info;
    }

    function get_user_list($butler_id=0,$status=0,$device=0){
        $wh['a.butler_id'] = $butler_id;
        if ($status) {
            $wh['a.status'] = $status;
        }
        $list = M('order')->alias('a')->field('c.status as c_status,a.deco_id order_id,b.*')
            ->join('gms_member b on a.user_id = b.id')
            ->join('gms_decorate c on c.id = a.deco_id')
            ->where($wh)->select();

        if(is_array($list)){
            foreach ($list as $key => $val){
                $list[$key]['status_name'] = get_order_status($val['c_status']);
            }
        }
        $result['data'] = $this->list_to_group($list,$device);

        $wh['c.status'] = 5;
        $result['work'] = M('order')->alias('a')
            ->join('gms_member b on a.user_id = b.id')
            ->join('gms_decorate c on c.id = a.deco_id')
            ->where($wh)->count();
        $wh['c.status'] = 6;
        $result['finish'] = M('order')->alias('a')
            ->join('gms_member b on a.user_id = b.id')
            ->join('gms_decorate c on c.id = a.deco_id')
            ->where($wh)->count();

        $this->ajaxReturn($result);

    }

    /*
     * 根据姓名的首字母，如果非数字等的为 #
     */
    function list_to_group($list,$device){
        foreach ($list as $key => $value) {
            $initial = '#';
            //如果以数字开头  为'#'；

            if ($value['realname']) {
                $initial = getFirstCharter($value['realname']);
            }
            $list[$key]['initial'] = $initial;
        }
        $list = list_sort_by($list,'initial');
        if ($device==1) {
            foreach ($list as $key => $value) {
                $result[$value['initial']][] = $value;
            }
            return $result;
        }
        return $list;
    }



    function get_nodes(){
        $wh['pid'] = array('gt',0);
        $list = M('nodes')->where($wh)->order('sort')->select();
        $this->ajaxReturn($list);
    }
	
    function submit(){
        if (IS_POST) {
            $model = M('diary');
			$model->startTrans();
			$data = $model->create();
            //判断是否 已经储存过该节点的 日记
            extract(I('post.'));
            $da = $model->where("order_id = $order_id AND node_id = $node_id")->find();

            if(!empty($da)){
                $this->ajaxError('该节点已发送过日记');
                exit;
            }
			$data['create_time'] = time();
            $result = $model->add($data);
			if($result){
				if($this->add_detail($result)){
					$model->commit();
                    //根据日记ID获取用户ID
                    $or_id = M('diary')->field('order_id')->find($result);
                    //获取 用户 ID
                    $user_id = M('decorate')->field('user_id')->find($or_id['order_id']);
                    //获取用户username
                    $username = M('member')->field('username,realname')->find($user_id['user_id']);
                    //获取管家姓名
                    $butler_name = M('butler')->field('name')->find($butler_id);
                    //管家发完管家日记 然后给用户发推送
                    $text = '尊敬的用户您好,您的管家 '.$butler_name['name'].' 发布了一条新的管家日记，请查看';
                    //储存通知
                    $add_data['tag_id'] = $user_id['user_id'];
                    $add_data['type'] = 1;
                    $add_data['create_time'] = time();
                    $add_data['tag_type'] = 1;
                    $add_data['tag_cat'] = 1;
                    $add_data['content'] = $text;
                    M('message')->add($add_data);
                    //给管家自己发送消息
                    $_add_data['tag_id'] = $butler_id;
                    $_add_data['type'] = 1;
                    $_add_data['create_time'] = time();
                    $_add_data['tag_type'] = 4;
                    $_add_data['tag_cat'] = 1;
                    $_add_data['content'] = "尊敬的管家您好，您已成功向".$username['realname'].'发送了管家日记，等待用户查看';
                    M('message')->add($_add_data);

                    sendNotifySpecial($username['username'],$text);
					$this->ajaxSuccess(array('id'=>$result));
					return true;
				}else{
					$model->rollback();
				}
			}
            $this->ajaxSuccess();
		}
    }

    function add_detail($diary_id){
        $detail = I('detail');
        foreach ($detail as $key => $value) {
			$data['diary_id'] = $diary_id;
			$data['content'] = $value['content'];
            $data['propose'] = $value['propose'];
            $result = M('diary_detail')->add($data);
            if ($result) {
                if (!$this->add_image($result,$value['image'])) {
                    return false;
                }
            }else{
                return false;
            }
		}
        return true;
    }

    function add_image($pid,$images){
        foreach ($images as $key => $value) {
			$data['pid'] = $pid;
			$data['image'] = base64toimg($value);
            $dataList[] = $data;
		}
        return M('diary_image')->addAll($dataList);
    }

    function history($butler_id=0,$user_id=0,$page=1,$num=10){

        $result = array();
        $wh['a.butler_id'] = $butler_id;
        if($user_id){
            $wh['a.user_id'] = $user_id;
            $result['user'] = M('member')->field('realname,head_img')->where(array('id'=>$user_id))->find();
        }

        $list = M('order')->alias('a')->field("b.*,FROM_UNIXTIME(b.create_time,'%Y-%m-%d') time,c.realname,c.id user_id,c.head_img,d.name node_name")
            ->join('gms_diary b on a.deco_id = b.order_id')
            ->join('gms_member c on a.user_id = c.id ')
            ->join('gms_nodes d on b.node_id = d.id ')
            ->order('time DESC')
            ->page($page,$num)
            ->where($wh)->select();

        $result['data'] = $list;

        $this->ajaxReturn($result);
    }

}
