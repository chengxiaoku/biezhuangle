<?php
namespace Store\Controller;

class OrderController extends BaseController{

    public function index(){
        $wh['comp_id'] = session('user.id');
        $count = M('decorate')->alias('a')
			->join('gms_city b on a.city_id = b.id')
			->join('gms_city c on b.pid = c.id')
            ->join('gms_member d on a.user_id = d.id')
            ->join('gms_program e on a.pro_id = e.id')->where($wh)->count();
        $Page = new \Think\Page($count,I('limit',10));
        $list = M('decorate')->alias('a')
			->field('a.*,b.name city_name,c.name prov_name,d.realname,e.title pro_title,d.id userid')
			->join('gms_city b on a.city_id = b.id')
			->join('gms_city c on b.pid = c.id')
            ->join('gms_member d on a.user_id = d.id')
            ->join('gms_program e on a.pro_id = e.id')
            ->limit($Page->firstRow.','.$Page->listRows)->order('id desc')->where($wh)->select();
        $this->assign('list',$list);
        $this->assign('page',$Page->show());
        $this->display();
    }

    //生成合同
    function create_comp(){
        if (IS_POST) {
            $id = I('id');
            $wh['id'] = $id;
            $data['status'] = 3;
            $obligation = A('Api/Base')->get_obligation($id);
            if ($obligation == 0) {
                $data['status'] = 4;
            }
            $coverimg = I('coverimg');
            if ($coverimg) {
                $data['coverimg'] = base64toimg($coverimg);
                $data['start_date'] = strtotime(I('start_date'));
                $data['end_date'] = strtotime(I('end_date'));
                if(empty($data['end_date'])){
                    $this->ajaxError("结束日期不能为空");
                }
                if($data['start_date'] > $data['end_date']){
                    $this->ajaxError("结束日期应大于开始日期");
                }
                if(verify_enddate($data['start_date'],$data['end_date'])){
                    if (M('decorate')->where($wh)->save($data)) {
                        A('Api/Message')->add_create_comp($wh['id']);
                        $this->ajaxSuccess();
                    }else{
                        $this->ajaxError();
                    }
                }else {
                    $this->ajaxError("结束时间必须小于45个工作日");
                }
            }else {
                $this->ajaxError("请上传封面图");
            }
        }
    }

    //驳回合同
    function reject_comp(){
        if (IS_POST) {
            extract(I('post.'));
            $wh['id'] = $id;
            $data['status'] = 8;
            $result = M('decorate')->where($wh)->save($data);
            if ($result !== false) {
                //添加驳回信息
                $data['obj_id'] = $id;
                $data['content'] = $content;
                $data['create_time'] = time();
                if (M('reject')->add($data)) {
                   A('Api/Message')->add_reject_comp($id);
                    $this->ajaxSuccess();
                    exit;
                }
            }
            $this->ajaxError();
        }
    }

    function notes($deco_id){
        $wh['pid'] = array('gt',0);
        $list = M('nodes')->where($wh)->order('sort')->select();
        foreach ($list as $key => $value) {
            $wh1['deco_id'] = $deco_id;
            $wh1['node_id'] = $value['id'];
            $list[$key]['info'] = M('progress')->where($wh1)->find();
        }
        $this->assign('list',$list);
        $this->assign('deco_id',$deco_id);
        $this->display();
    }

    function noteadd(){
        if (IS_POST) {
            $trans = D('Cms/Progress');
            $trans->startTrans();
            $data = $this->get_noteadd_data();
            //是否存在
            $exist = $this->exist_note($data['deco_id'],$data['comp_id'],$data['node_id']);
            if (!$exist) {
                if($trans->create($data)){
                    $result = $trans->add($data);
                    if ($result) {
                        set_order_status($data['deco_id'],5);   //更新状态
                        //上传装修日记图片
                        $rest_img = $this->upload_img($result,I('href'));
                        if($rest_img !== false){
                            $trans->commit();
                            A('Api/Message')->add_notes($result);
                            
                            $this->ajaxSuccess(); exit;
                        }else{
                            $trans->rollback();
                        }
                    }
                }
                $error = $trans->getError();
                $this->ajaxError($error?$error:'提交失败');
            }else {
                $this->ajaxSuccess();
            }
        }else{
            $this->assign('deco_id',I('deco_id'));
            $this->assign('node_id',I('node_id'));
            $this->display();
        }
    }

    function get_noteadd_data(){
        extract(I('post.'));
        $data['title'] = $title;
        $data['status'] = $status;
        $data['deco_id'] = $deco_id;
        $data['node_id'] = $node_id;
        $data['comp_id'] = session('user.id');
        if ($create_time) {
            $data['create_time'] = strtotime($create_time);
        }else{
            $data['create_time'] = time();
        }
        return $data;
    }

    function exist_note($deco_id,$comp_id,$node_id){
        $wh['deco_id'] = $deco_id;
        $wh['comp_id'] = $comp_id;
        $wh['node_id'] = $node_id;
        $count = M('progress')->where($wh)->count();
        return $count>0?true:false;
    }

    function upload_img($id,$data){
        if ($data) {
            foreach ($data as $key => $value) {
                $href = base64toimg($value);
                $dataList[] = array('obj_id'=>$id,'href'=>$href,'create_time'=>time());
            }
            return M('photos')->addAll($dataList);
        }
        return true;
    }

    function noteedit(){
        if (IS_POST) {
            $trans = M();
            $trans->startTrans();
            extract(I('post.'));
            $wh['id'] = $id;
            $data['title'] = $title;
            $data['status'] = $status;
            if (M('progress')->where($wh)->save($data)) {
                $wh1 = array('type'=>0,'obj_id'=>$id);
                M('photos')->where($wh1)->delete();
                foreach ($href as $key => $value) {
                    $href = base64toimg($value,'./Uploads/1/image/');
                    $dataList[] = array('obj_id'=>$id,'href'=>$href,'create_time'=>time());
                }
                if(M('photos')->addAll($dataList)){
                    $trans->commit();
                    $this->ajaxSuccess(); exit;
                }else{
                    $trans->rollback();
                }
            }
            $this->ajaxError();
        }else{
            $wh['id'] = I('id');
            $info = M('progress')->where($wh)->find();
            $wh1['type'] = 0;
            $wh1['obj_id'] = I('id');
            $info['photos'] = M('photos')->where($wh1)->select();
            $this->assign('info',$info);
            $this->display();
        }
    }

    function noteappend(){
        if (IS_POST) {
            $trans = M();
            $trans->startTrans();
            extract(I('post.'));
            $data['title'] = $title;
            //$data['status'] = 3;
            $data['note_id'] = $note_id;
            $data['create_time'] = time();
            //是否存在
            $exist = $this->exist_append($note_id);
            if (!$exist) {
                $result = M('append')->add($data);
                if ($result) {
                    set_notes_status($note_id,3);   //更新日记状态
                    if ($href) {
                        foreach ($href as $key => $value) {
                            $href = base64toimg($value);
                            $dataList[] = array('obj_id'=>$result,'href'=>$href,'type'=>3,'create_time'=>time());
                        }
                        if(M('photos')->addAll($dataList)){
                            $trans->commit();
                            A('Api/Message')->add_append($note_id);
                            $this->ajaxSuccess(); exit;
                        }else{
                            $trans->rollback();
                        }
                    }else {
                        A('Api/Message')->add_append($note_id);
                        $this->ajaxSuccess(); exit;
                    }
                }
                $this->ajaxError();
            }else{
                $this->ajaxSuccess();
            }
        }else{
            $this->assign('note_id',I('note_id'));
            $this->display();
        }
        
    }

    function exist_append($note_id){
        $wh['note_id'] = $note_id;
        $wh['reject_time'] = 0;
        $count = M('append')->where($wh)->count();
        return $count>0?true:false;
    }
}