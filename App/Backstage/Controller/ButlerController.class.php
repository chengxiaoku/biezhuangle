<?php
namespace Backstage\Controller;
use Think\Crypt\Driver\Think;


/**
 * 后台首页控制器
 */
class ButlerController extends BaseController{
    /**
     * 监理小哥信息显示页面
     */
    public function index()
    {
        //拉取装修小哥信息
        $butler_obj = M('butler');
        $count = $butler_obj->count();
        $Page = new \Think\Page($count,10);
        $list = $butler_obj->order("id desc")->limit($Page->firstRow.','.$Page->listRows)->select();

        $this->assign('list',$list);
        $this->assign('page',$Page->show());

        $this->display();
    }

    /**
     * 添加装修小哥
     */
    public function add(){
        if(IS_GET){
            $this->display();
        }else{
            extract(I('post.'));
            $data['name'] = $name;
            $data['photo'] = base64toimg($photo);
            $data['phone'] = $phone;
            $data['wechat'] = $wechat;
            $data['level'] = $level;
            $data['intro'] = $intro;
            $data['alipay_name'] = $alipay_name;
            $data['alipay_no'] = $alipay_no;
            if(M('Butler')->add($data)){
                $this->ajaxSuccess('添加成功');
            }else{
                $this->ajaxError('添加失败');
            };
        }
    }

    /**
     *修改操作
     */
    function update(){
        if(IS_GET){
            $id = I('get.id','','trim');
            if(empty($id)){
                $this->redirect(U('Butler/index'));
            }
            $info = M('Butler')->find($id);

            $this->assign('info',$info);
            $this->display();
        }else{
            extract(I('post.'));
            $data['name'] = $name;
            $data['photo'] = base64toimg($photo);
            $data['phone'] = $phone;
            $data['wechat'] = $wechat;
            $data['level'] = $level;
            $data['intro'] = $intro;
            $data['alipay_name'] = $alipay_name;
            $data['alipay_no'] = $alipay_no;
            M('Butler')->where(array('id'=>$id))->save($data);
            $this->ajaxSuccess('修改成功');

        }
    }

    /**
     * 删除操作
     */
    function del(){

        $id = I('get.id','','trim');
        if(empty($id)){
            $this->redirect(U('Butler/index'));
        }
        if(M('butler')->delete($id)){
            echo 1;
        }else{
            echo 2;
        }
    }

    /**
     * 搜索功能
     */
    function search(){
        $val = I('get.val','','trim');
        //拉取装修小哥信息
        $butler_obj = M('butler');
        if(empty($val)){
            $where = '';
        }else{
            $where = "name Like '%".$val."%'";
        }
        $count = $butler_obj->where($where)->count();
        $Page = new \Think\Page($count,10);
        $list = $butler_obj->order("id desc")->limit($Page->firstRow.','.$Page->listRows)->where($where)->select();
        $this->assign('list',$list);
        $this->assign('page',$Page->show());
        $this->assign('se_val',$val);
        $this->display('index');
    }
}