<?php
namespace Backstage\Controller;
use Think\Controller;
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/7/14
 * Time: 10:50
 */
class UserController extends BaseController{
    /*
     * 用户资金流水
     */
    function index(){
        $obj_member = M('member');
        $count = $obj_member->count();
        $Page = new \Think\Page($count,I('limit',15));
        $list = $obj_member->field('id,nickname,username,last_login_time,last_login_ip,address,money,is_wx')->order("id desc")->limit($Page->firstRow.','.$Page->listRows)->select();
        $this->assign('list',$list);
        $this->assign('page',$Page->show());
        $this->display();
    }

    /**
     * 收支详情
     */
    function edit(){
        $id = I('get.id','','trim');
        if(empty($id)){
            redirect(U('User/index'));
            exit;
        }
        $username = M('member')->field('username')->find($id);
        $count = M('funds')->where(array('user_id'=>$id))->count();
        $Page = new \Think\Page($count,I('limit',15));
        $list = M('funds')->where(array('user_id'=>$id))->order('id desc')->limit($Page->firstRow.','.$Page->listRows)->select();

        $this->assign('list',$list);
        $this->assign('page',$Page->show());
        $this->assign('username',$username['username']);
        $this->display();
    }

    /**
     * 搜索页面
     */
    function search(){
        $val = I('get.val','','trim');

        $obj_member = M('member');
        if(empty($val)){
            $where = '';
        }else{
            $where = "username Like '%".$val."%'";
        }
        $count = $obj_member->where($where)->count();
        $Page = new \Think\Page($count,I('limit',15));
        $list = $obj_member->field('id,nickname,username,last_login_time,last_login_ip,address,money,is_wx')->where($where)->order("id desc")->limit($Page->firstRow.','.$Page->listRows)->select();
        $this->assign('list',$list);
        $this->assign('page',$Page->show());
        $this->assign('se_val',$val);
        $this->display('index');
    }
}