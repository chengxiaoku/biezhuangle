<?php
namespace Store\Controller;
/**
 * 后台首页控制器
 * CLF 710425820@qq.com
 */
class PasswordController extends BaseController{
    /**
     * 首页
     */
    public function index(){
        if(IS_POST){
            $userid = session('user.id');
            $co = M('company');
            $old_paw = I('post.old_paw','','trim');
            $new_paw1 = I('post.new_paw1','','trim');
            $new_paw2 = I('post.new_paw2','','trim');
            $data = $co->field('password')->find($userid);
            if(empty($old_paw)){
                $this->ajaxError("原密码不能为空！");
                exit;
            }
            if(empty($new_paw1)){
                $this->ajaxError("请输入密码！");
                exit;
            }
            if(empty($new_paw2)){
                $this->ajaxError("请再次输入密码！");
                exit;
            }
            if(strlen($new_paw1) < 6){
                $this->ajaxError("密码不能少于6个长度！");
                exit;
            }
            if(md5($old_paw) != $data['password']){
                $this->ajaxError("原密码输入不正确！");
                exit;
            }
            if($new_paw2 != $new_paw1){
                $this->ajaxError("两次密码不相同！");
                exit;
            }

            if(md5($new_paw1) == $data['password']){
                $this->ajaxError("密码未修改！");
                exit;
            }
            $new_paw = md5($new_paw1);
            $data['password'] = $new_paw;
            $data['id'] = $userid;
            $bool = $co->save($data);
            if($bool){
                $this->ajaxSuccess('密码修改成功！');
                exit;
            }else{
                $error = $co->getError();
                $this->ajaxError($error ? $error : "密码修改失败！");
                exit;
            }
        }else{
            $this->display();
        }


    }



}
