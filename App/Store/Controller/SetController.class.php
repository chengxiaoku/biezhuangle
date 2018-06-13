<?php
namespace Store\Controller;
/**
 * 账号设置控制器
 * @CLF 710425820@qq.com
 */
class SetController extends BaseController
{
    /**
     * 首页
     */
    public function index(){
        //获取当前登录用户的 ID

        $userid = session('user.id');
        $co = M('company');
        $user_data = $co->field('icon,address,phone,alipay_name,alipay_no,position_x,position_y')->find($userid);
        if(IS_POST){

            //更换地址
           if($user_data['address'] != $_POST['adress']){
               $adress = $_POST['adress'];
            }else{
               $adress = $user_data['address'];
           }
           $tel = I("post.tel",'','trim');
            if(!empty($tel)){
                if(strlen($tel)>12){
                    $this->ajaxError("手机号长度不能超过11个字符！");
                    exit;
                }elseif(strlen($tel)<6){
                    $this->ajaxError("密码长度不能小于6个字符！");
                    exit;
                }
                $_tel = $tel;
            }else{
                $this->ajaxError("手机号不能为空！");
                exit;
                //$_tel = $user_data['phone'];
            }
            $img = $_POST['image'];

            if(is_null($img)){
                $_img = $user_data['icon'];
            }else{
                $_img = base64toimg($img);
            }
            $data = array(
                'address'=>$adress,
                'phone'=>$_tel,
                'icon'=>$_img,
                'alipay_name'=>I('post.alipay_name','','trim'),
                'alipay_no'=>I('post.alipay_no','','trim'),
            );
            $bol = $co->where("id=$userid")->save($data);
            $this->ajaxSuccess('','信息修改成功！');

        }else{
            $this->assign('data',$user_data);
            $this->display();
        }

    }
}