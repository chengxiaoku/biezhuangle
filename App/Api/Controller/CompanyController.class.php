<?php
/*
 * 控制器
 * Time   : 2016年07月26日
 */
namespace Api\Controller;
use Think\Controller;

class CompanyController extends BaseController {

    function detail($comp_id,$limit=100){
        $wh['id'] = $comp_id;
        $info = M('company')->where($wh)->find();
        $wh1['comp_id'] = $comp_id;
        $info['designer'] = M('designer')->where($wh1)->select();
        $info['summary'] = count_grade($comp_id);
        $info['cases'] = $this->get_list($comp_id,6);
        $info['work'] = $this->get_list($comp_id,5);
        $info['grade'] = $this->get_grade($comp_id,$limit);
        $this->setInc_view($comp_id,'company');
        $this->ajaxReturn($info);
    }

    function get_list($comp_id,$status){
        $wh['comp_id'] = $comp_id;
        $wh['status'] = $status;
        $list = M('decorate')->alias('a')
			->field('a.*,b.name city_name,c.title pro_name')
			->join('gms_city b on a.city_id = b.id ')
			->join('gms_program c on a.pro_id = c.id')
			->where($wh)->select();
		foreach ($list as $key => $value) {
			$info = A('Decorate')->find_notes($value['id']);
			$list[$key]['views'] = $info['views'];
            $list[$key]['status_name'] = get_order_status($value['status']) ;
            $wh1['type'] = 1;
            $wh1['deco_id'] = $value['id'];
            $list[$key]['photo'] = M('design')->where($wh1)->getField('image');
		}
		return $list;
    }

    function get_grade($comp_id,$limit){
        $wh['comp_id'] = $comp_id;
        $list = M('grade')->alias('a')
            ->field('a.*,b.nickname,b.head_img')
            ->join('gms_member b on a.user_id = b.id')
            ->where($wh)->limit($limit)->select();
        foreach ($list as $key => $value) {
            $wh1['grad_id'] = $value['id'];
            $list[$key]['reply'] = M('reply')->where($wh1)->select();
        }
        return $list;
    }

    function find_designer($id,$ret_type='json'){
        $wh['id'] = $id;
        $info = M("designer")->where($wh)->find();
        $info['photos'] = $this->get_photos($id,2);
        if ($ret_type == 'json') {
            $this->ajaxReturn($info);
        }else{
            return $info;
        }
    }


}
