<?php
namespace Apitest\Controller;
use Think\Controller;
class IndexController extends BaseController {
    public function index(){
        $list['banner'] = $this->get_banner();
        $list['news'] = $this->get_article();
        $list['meal'] = $this->get_meal();
        $list['notes'] = $this->get_notes();
        $list['diary'] = $this->get_diary();
        $list['design'] = $this->get_design();
        $this->ajaxReturn($list);
    }

    function get_meal(){
        $list = M('gallery_album a')
            ->field('a.*,b.name as areaname,c.name as stylename')
            ->join('left join gms_gallery_area as b ON a.area_id = b.id')
            ->join('left join gms_gallery_style as c ON a.style_id = c.id')
            ->order('a.view DESC')
            ->limit(1)
            ->select();
        return $list;
    }

    function get_banner(){
        $wh = array();
		$list = M('banner')->where($wh)->limit(10)->order('id desc')->select();
        return $list;
    }

    function get_article(){
        $wh = array();
		$list = M('article')->alias('a')->field('a.*,b.title catname')
			->join('gms_category b on a.catid = b.id')
			->where($wh)->limit(1)->order('is_top desc,sort,create_time desc')->select();
		$list = for_comment_count($list,1);
        return $list;
    } 
 
    function get_notes(){
        //合同生成的显示
        $wh = array('a.status'=>array('in','5,6,4'));
		$list = M('decorate')->alias('a')
			->field('a.*,b.name city_name,c.title pro_name')
			->join('gms_city b on a.city_id = b.id ')
			->join('gms_program c on a.pro_id = c.id')
			->where($wh)->limit(3)->select();
		foreach ($list as $key => $value) {
			$info = A('Decorate')->find_notes($value['id']);
			$list[$key]['views'] = $info['views'];
            $list[$key]['status_name'] = $info['status_name'];
            $list[$key]['photo'] = $this->get_coverimg($value['id']);
		}
		return $list;
    }

    function get_coverimg($deco_id){
		$wh['deco_id'] = $deco_id;
		$info = M('design')->where($wh)->getField('image');
		if (!$info) {
            $info = M('progress')->alias('a')
                ->join('gms_photos b on a.id = b.obj_id and b.type = 0')
                ->where($wh)->getField('href');
		}
		return $info;
	}

    /**
     *
     * 获取管家日记
     */
    function get_diary(){
        return array();
        //全部拉取出来
        $list = M('diary')->alias('a')
            ->field('a.id diary_id,a.order_id,a.title diary_title,a.views,c.name,c.photo,c.id butler_id,d.*')
            ->join('gms_order b on a.order_id = b.deco_id')
            ->join('gms_butler c on b.butler_id = c.id')
            ->join('gms_decorate d on b.deco_id = d.id')
            ->group('a.order_id')
            ->limit(4)
            ->order('diary_id desc')
            ->select();
        $butler_obj = A('Butler');
        foreach ($list as $k => $v) {
            $list[$k]['comm_count'] = $butler_obj->get_comm_count($v['diary_id']);
            $list[$k]['image'] = $butler_obj->get_photoc($v['diary_id']);
        }
        return $list;
    }

    function get_progress($user_id){
        $info = $this->find_deco_byuid($user_id);
        $list = M('nodes')->field('*,0 deco_id,0 status')->order('sort')->select();
        $list = list_to_tree($list);
        foreach ($list as $key => $value) {
            $arr_status = array('work' => 0,'finish'=>0);   //父级节点完成状态（默认完成）
            $count_finish = 0;
            $child = $value['_child'];
            foreach ($child as $k => $v) {
                $wh['node_id'] = $v['id'];
                $wh['deco_id'] = $info['id'];
                $node = M('progress')->where($wh)->find();
                if ($v['id'] == $node['node_id']) {
                    $child[$k]['status'] = $node['status'];
                    $child[$k]['deco_id'] = $node['deco_id'];
                    if ($node['status'] == 4) {
                        $arr_status['finish']++;
                    }else{
                        $arr_status['work']++;
                    }
                }
                $child[$k]['status_name'] = get_notes_status($child[$k]['status']);
            }
            if ($arr_status['finish'] >= count($child)) {
                $list[$key]['status'] = 4;
            }elseif ($arr_status['work'] > 0 || $arr_status['finish'] > 0) {
                $list[$key]['status'] = 2;
            }
            $list[$key]['_child'] = $child;
        }
        $this->ajaxReturn($list);
    }

    function get_progress1($user_id){
        $info = $this->find_deco_byuid($user_id);
        $list = M('nodes')->field('*,0 deco_id,0 status')->order('sort')->select();
        foreach ($list as $key => $value) {
            $wh['deco_id'] = $info['id'];
            $wh['node_id'] = $value['id'];
            $node = M('progress')->where($wh)->find();
            if ($value['id'] == $node['node_id']) {
                $list[$key]['status'] = $node['status'];
                $list[$key]['deco_id'] = $node['deco_id'];
            }
            $list[$key]['status_name'] = get_notes_status($list[$key]['status']);
        }
        $this->ajaxReturn(list_to_tree($list));
    }

    /**
     * @return mixed
     * 装修案例
     */
    function get_design(){
        $wh['status'] = array('in','5,6');
     $list = M('decorate')->alias('a')
            ->field('a.*,b.name comp_name,b.icon comp_icon')
            ->join('gms_company b on a.comp_id = b.id ')
            ->join('gms_design c on a.id = c.deco_id ')
            ->where($wh)->limit(4)
            ->group("a.id")
            ->order('a.id asc')->select();
        $de = M('design');
        foreach ($list as $key => $value) {
            $wh1['type'] = 1;
            $wh1['deco_id'] = $value['id'];
            $wh2['type'] = 1;
            $wh2['room'] = 1;
            $wh2['deco_id'] = $value['id'];
            $_img = $de->field('image')->where($wh2)->find();
            $img_data = $de->where($wh1)->select();
            $list[$key]['coverimg'] = $_img['image'];
            $list[$key]['image'] = $img_data;
        }
        return $list;
    }

    /**
     * 图库
     */
    function gallery(){
        //增加用户行为记录表
        M('analy')->where(array('title'=>'gallery'))->setInc('visit_num');

        extract(I('get.'));
        $style_data = M('gallery_style')->select();
        //if(!empty($style)){
            foreach ($style_data as $key => $val){
                if($val['id'] == $style){
                    $style_data[$key]['check'] = true;
                }else{
                    $style_data[$key]['check'] = false;
                }
            }
        //}
        $data['title_style'] = $style_data;
        $area_data = M('gallery_area')->select();
        //if(!empty($area_type)){
            foreach ($area_data as $key => $val){
                if($val['id'] == $area_type){
                    $area_data[$key]['check'] = true;
                }else{
                    $area_data[$key]['check'] = false;
                }
            }
        //}
        $data['title_area'] = $area_data;
        if(empty($area_type) && empty($style)){
            $where = '';
        }elseif(empty($area_type)){
            $where['style_id'] = $style;
        }elseif(empty($style)){
            $where['area_id'] = $area_type;
        }else{
            $where['style_id'] = $style;
            $where['area_id'] = $area_type;
        }

        $gallery_album_obj = M('gallery_album');
        //相册根据浏览量来排序
        $data['gallery_list'] = $gallery_album_obj->where($where)
            ->order('view DESC')
            ->Page(I('get.pag',1),I('get.pag_num',10))
            ->select();
       
        $this->ajaxReturn($data);
    }

    /**
     * 图库详情
     */
    function gallery_detail(){
        extract(I('get.'));
        //获取相册标题
        $album_data = M('gallery_album a')
            ->field('a.create_time,a.name,a.area_id,a.style_id,b.name as area_name,c.name as style_name')
            ->join('gms_gallery_area as b ON a.area_id = b.id')
            ->join('gms_gallery_style as c ON a.style_id = c.id')
            ->where(array('a.id'=>$album))->find();
        //根据房间类型进行排序
        $data = M('gallery_image')->where(array('album_id' => $album))->order('type ASC')->select();
        $type = array('客厅','卧室','餐厅','卫生间');
        foreach ($data as $key => $val){
            $data[$key]['type_name'] = $type[$val['type']-1];
        }
        //增加用户浏览量
        M('gallery_album')->where(array('id' => $album))->setInc('view',1);
        $new_data['album_data'] = $album_data;
        $new_data['list'] = $data;
        if($share && $share == 'clf'){
            $this->assign('data',$new_data);
            $this->display('gallery');
        }else{
            $this->ajaxReturn($new_data);
        }

    }


    /**
     * @return mixed
     * 装修案例  （独立）
     */
    function get_design_bak($p=1,$num=10){
        $wh['status'] = array('in','5,6');
        $list = M('decorate')->alias('a')
            ->field('a.*,b.name comp_name,b.icon comp_icon')
            ->join('gms_company b on a.comp_id = b.id ')
            ->join('gms_design c on a.id = c.deco_id ')
            ->where($wh)
            ->group("a.id")
            ->Page($p,$num)
            ->order('a.id asc')->select();
        $de = M('design');
        foreach ($list as $key => $value) {
            //2017年7-21更新
            $wh1['type'] = 1;
            $wh1['deco_id'] = $value['id'];
            $wh2['type'] = 1;
            $wh2['room'] = 1;
            $wh2['deco_id'] = $value['id'];
            $_img = $de->field('image')->where($wh2)->find();
            $img_data = $de->where($wh1)->select();
//            更换首页图片
            $list[$key]['coverimg'] = $_img['image'];
            $list[$key]['image'] = $img_data;
        }
        $this->ajaxReturn($list);
    }

    /**
     * 测试 随时可以删
     */
    function test(){
        $data = array(
            'name' => '程龙飞',
            'data' => array(
                0 => array(
                        'age' => 21,
                        'sex' => '男'
                        ),
                1 => array(
                        'age1' => 22,
                        'sex1' => 12,
                )
            )
        );
        exit(json_encode($data));
    }
}
