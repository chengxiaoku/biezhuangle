<?php
namespace Backstage\Controller;
use Think\Controller;
/**
 * Base基类控制器
 */
class BaseController extends Controller{
    /**
     * 初始化方法
     */
    public function _initialize(){
        if (IS_GET) {
            $this->is_login();
        }
        
    }

    function is_login(){
        if(!session("?user")){
            $this->redirect("Public/login");
        }else{
            $wh['id'] = session("user.id");
            $info = M('back_admin')->where($wh)->find();
          
            $this->assign('user_info',$info);
        }
    }
    

    function ajaxSuccess($data=array(),$info="提交成功"){
        $result = array('success'=>true,'info'=>$info,'data'=>$data);
        $this->ajaxReturn($result);
    }

    function ajaxError($info="提交失败"){
        $result = array('success'=>false,'info'=>$info);
        $this->ajaxReturn($result);
    }

}
