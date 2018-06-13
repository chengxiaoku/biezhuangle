<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/7/28
 * Time: 13:39
 */
namespace Backstage\Controller;
use Think\Controller;
class GalleryController extends BaseController{
    /**
     * 图库管理首页
     */
    function index(){
        //获取图库信息
        $count = M('gallery_album a')
            ->join('left join gms_gallery_area as b ON a.area_id = b.id')
            ->join('left join gms_gallery_style as c ON a.style_id = c.id')
            ->count();
        $Page = new \Think\Page($count,I('limit',15));
        $list = M('gallery_album a')
                ->field('a.*,b.name as areaname,c.name as stylename')
                ->join('left join gms_gallery_area as b ON a.area_id = b.id')
                ->join('left join gms_gallery_style as c ON a.style_id = c.id')
                ->limit($Page->firstRow.','.$Page->listRows)
                ->order('a.create_time DESC')
                ->select();
        $this->assign('list',$list);
        $this->assign('page',$Page->show());
        $this->display();
        // 1客厅2卧室3餐厅4卫生间
    }

    /**
     * 相册添加
     */
    function add(){
        if(IS_POST){
            //数据入库处理
            extract(I('post.'));
            $data['name'] = $name;
            $data['area_id'] = $area_id;
            $data['style_id'] = $style_id;
            $data['style_id'] = $style_id;
            $data['img'] = base64toimg($image);
            $data['create_time'] = time();
            $bool = M('gallery_album')->add($data);
            if($bool){
                $this->ajaxSuccess();
            }else{
                $this->ajaxError();
            }
        }else{
            //获取图库户型
            $area_data = M('gallery_area')->where('id > 0')->select();
            $this->assign('area_data',$area_data);
            //获取图库风格
            $style_data = M('gallery_style')->where('id > 0')->select();
            $this->assign('style_data',$style_data);
            $this->display();
        }

    }

    /**
     * 删除相册
     */
    function del(){
        $id = I('get.id','','trim');
        if(empty($id) || !is_numeric($id)){
            echo 2;
            exit;
        }
        $bool = M('gallery_album')->where(array('id'=>$id))->delete();
        if($bool){
            M('gallery_image')->where(array('album_id'=>$id))->delete();
            echo  1;
        }else{
            echo 2;
        }
    }

    /**
     * 图库编辑操作
     */
    function update(){
        if(IS_GET){
            $id = I('get.id','','trim');
            if(empty($id) || !is_numeric($id)){
                $this->redirect(U('Gallery/index'));
            }
            $data = M('gallery_album')->find($id);
            //获取图库户型
            $area_data = M('gallery_area')->where('id > 0')->select();
            $this->assign('area_data',$area_data);
            //获取图库风格
            $style_data = M('gallery_style')->where('id > 0')->select();
            $this->assign('style_data',$style_data);
            $this->assign('data',$data);
            $this->display();
        }else{
            extract(I('post.'));
            $data['name'] = $name;
            $data['area_id'] = $area_id;
            $data['style_id'] = $style_id;
            $data['img'] = base64toimg($image);
            $bool = M('gallery_album')->where(array('id'=>$id))->save($data);
            if($bool){
                $this->ajaxSuccess();
            }else{
                $this->ajaxError();
            }
        }
    }

    /**
     * 图片首页
     */
    function imageindex(){
        $id = I('get.id');
        $this->assign('id',$id);
        $data = M('gallery_image')->where(array('album_id'=>$id))->order('type ASC')->select();
        
        $this->assign('data',$data);
        $this->display();
    }
    /**
     * 图片添加
     */
    function imageadd(){
        if(IS_GET){
            $id = I('get.id');
            $this->assign('id',$id);
            $this->display();
        }elseif(IS_POST){
            extract(I('post.'));
            $type = I('post.type');
            $data['album_id'] = I('post.id');
            $data['content'] = I('post.content');
            $data['type'] = $type;
            $data['img'] = base64toimg(I('post.image'));
            $data['create_time'] = time();
            if(!empty($type)){
                $bool = M('gallery_image')->add($data);
            }
            if($bool){
                $this->ajaxSuccess();
            }else{
                $this->ajaxError();
            }
        }
    }

    /**
     *图片删除
     */
    function imagedel(){
        $id = I('post.id','','trim');
        $bool = M('gallery_image')->delete($id);
        if($bool){
            $this->ajaxSuccess();
        }else{
            $this->ajaxError();
        }
    }
}