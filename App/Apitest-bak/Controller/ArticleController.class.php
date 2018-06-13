<?php
/*
 * 控制器
 * Time   : 2016年07月26日
 */
namespace Apitest\Controller;
use Think\Controller;

class ArticleController extends BaseController {

	function index(){
        $wh = array();
        $catid = I('catid');
        if ($catid) {
            $wh['catid'] = $catid;
        }
		$count = M('article')->where($wh)->count();
		$Page = new \Think\Page($count,I('limit',10));
		$list = M('article')->alias('a')->field('a.*,b.title catname')
			->join('gms_category b on a.catid = b.id')
			->where($wh)->limit($Page->firstRow.','.$Page->listRows)->order('sort,create_time desc')->select();
		$list = for_comment_count($list,1);
        $this->ajaxReturn($list);
	}

	function detail($id){

		//增加用户行为记录表
		M('analy')->where(array('title'=>'new'))->setInc('visit_num');

		//获取分享的标识符
		$share = I('get.share');

		$wh['id'] = $id;
		$info['info'] = M('article')->where($wh)->find();
		if ($info['info']) {
			$this->setInc_view($id,'article');
			$info['agree'] = $this->get_agree_count(1,$id);
			$info['recommend'] = $this->get_recommend($info['info']);
			//评论数量
			$wh1['tid'] = 1;
			$wh1['aid'] = $id;
			$info['comm_count'] = M('comment')->alias('a')->join('gms_member b on a.uid = b.id')->where($wh1)->count();
		}
		$title = M('category')->field('title')->find($info['info']['catid']);
		$info['info']['catname'] = $title['title'];
		if($share && $share == 'clf'){
			$text = html_entity_decode($info['info']['content'], ENT_QUOTES, 'UTF-8');
			/*$_text = str_replace("width=\"auto\""," ",$text);
			$_text1 = str_replace("width:auto !important"," ",$_text);
			$_text2 = str_replace("width:auto"," ", $_text1);

			$_text3 = str_replace("width:670px !important;"," ", $_text2);*/
			/*dump($_text2);
			exit;*/
			$_text = str_replace(array("width=\"auto\"","width:auto !important","width:auto","width:670px !important;")," " , $text);
			$this->assign('text',$_text);
			$this->assign('info',$info);
			$this->display();
		}else{
			$this->ajaxReturn($info);
		}
	}

    function get_category(){
		$wh['type'] = 1;
        $count = M('category')->where($wh)->count();
        $Page = new \Think\Page($count,I('limit',10));
        $list = M('category')->where($wh)->limit($Page->firstRow.','.$Page->listRows)->order('sort')->select();
        $this->ajaxReturn($list);
    }

	//推荐
	function get_recommend($info){
		$wh['a.catid'] = $info['catid'];
		$wh['a.id'] = array('NEQ',$info['id']);
		$list = M('article a')
			->field('a.*,b.title as catname')
			->join('gms_category b ON a.catid = b.id')
			->where($wh)->limit(3)
			->order('create_time desc')
			->select();
		$list = for_comment_count($list,1);
		return $list;
	}

	function find_data($id){
		$wh['a.id'] = $id;
		$info = M('article')->alias('a')->field('a.*,b.title cat_name')
			->join('gms_category b on a.catid = b.id')->where($wh)->find();
		if ($info) {
			$info['agree'] = $this->get_agree_count(1);
			$info['recommend'] = $this->get_recommend($info['info']);
			$wh1['id'] = $id;
			$wh1['type'] = 1;
			$info['comm_count'] = M('comment')->where($wh1)->count();
		}
		return $info;
	}

	function get_comment($id=0){
        $wh['tid'] = 1;
        $wh['aid'] = $id;
        $list = M('comment')->alias('a')
            ->field("a.*,b.nickname,b.head_img")
            ->join('gms_member b on a.uid = b.id')->where($wh)->select();
        $this->ajaxReturn($list);
    }


}
