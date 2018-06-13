<?php
/**
 * 装修套餐
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/8/7
 * Time: 9:34
 */
namespace Backstage\Controller;
use Backstage\Controller\BaseController;
class MealController extends BaseController{
    //方案首页
    function index(){
        //获取装修方案列表
        $count = M('meal')->count();
        $Page = new \Think\Page($count,15);
        $data = M('meal a')
            ->order('id DESC')
            ->limit($Page->firstRow,$Page->listRows)
            ->select();
        $this->assign('data',$data);
        $this->assign('page',$Page->show());
        $this->display();
    }

    /**
     * 获取装修套餐列表
     */
    function material($id){
        $data = M('meal_material a')
            ->field('a.*,b.title')
            ->join('LEFT JOIN gms_meal_title as b ON b.order_id = a.order_id')
            ->where(array('a.meal_id'=>$id))
            ->group('a.order_id')
            ->order('a.order_id')
            ->select();
        $this->assign('data',$data);
        $this->display();
    }

    /**
     * 获取装修套餐列表
     */
    function materialdel($id){
        $bool = M('meal_material')->where(array('order_id'=>$id))->delete();
        M('meal_title')->where(array('order_id'=>$id))->delete();
        $this->ajaxSuccess();
    }


    /**
     * 增加套餐
     */
    function add(){
        if(IS_POST){

            $Meal = M('meal');
            extract(I('post.'));

            $data['style_name'] = $style_name;
            
            $bool = $Meal->add($data);
            if($bool){
                $this->ajaxSuccess();
            }else{
                $this->ajaxError();
            }
        }else{
            $this->display();
        }
    }

    /**
     * 方案修改
     */
    function update(){
        if(IS_POST){
            //$this->display();
            $meal = M('meal');

            $bool = $meal->save($meal->create());
            if($bool){
                $this->ajaxSuccess();
            }else{
                $this->ajaxError();
            }
        }else{
            extract(I('get.'));
            //获取 方案信息
            $data = M('meal')->find($id);
            $this->assign('data',$data);
            $this->display();
        }
    }

    /**
     * 删除方案操作
     */
    function del(){
        extract(I('get.'));
        $bool = M('meal')->delete($id);
        M('meal_title')->where(array('meal_id'=>$id))->delete();
        //删除方案下面的所有套餐
        M('meal_material')->where(array('meal_id'=>$id))->delete();
        if($bool){
            $this->ajaxSuccess(array(),'删除成功');
        }else{
            $this->ajaxError('删除失败');
        }
    }

    /**
     * 添加套餐
     */
    function materialadd($meal_id){
       
        $this->assign('meal_id',$meal_id);
        $data = M('meal')->find($meal_id);

        //标题头部
        $str = '';
        $cat_str = '';
        $sum = 0;
        if(!is_null($data)){
            $num = 1;
            $arr = array('room','hall','cook','toilet','balcony','dining','steel','other');
            //获取 所有房间标示符
//            $arr = M('room')->field('type,name')->select();
//            foreach ($arr as $key => $val){
//                $arr[$key] = $val['type'];
//                $arr[$key] = $val['name'];
//            }
            foreach ($arr as $key => $val){
                //if(in_array($key, $arr) && $val != 0){
                    $room_name = $this->get_name($val);
                    $cat = $this->get_category($room_name['id']);

                    //start
                    $_str = '';
                    foreach ($cat as $key_cat => $val_cat){
                        //获取商品信息
                        $goods_data = $this->get_brand($val_cat['id']);
                        $new_str ='';
                        $sum ++;
                        foreach ($goods_data as $_key =>$_val){
                            $new_str.= '<a href="#" onclick="goos_show('.$_val['id'].','.$val_cat['id'].',0,\'display\')">'.$_val['name'].'</a>,';
                        }
                        $_str .='
                                <div class="am-form-group">
                                    <label for="user-email" class="am-u-sm-2 am-form-label" pt="pt'.$val_cat['id'].'">'.$val_cat['title'].'</label><span>未选择商品</span> 
                                    <div class="am-u-sm-7">
                                           '.$new_str.'    
                                    </div>
                                </div>';
                    }
                    //end
                    if($num == 1){
                        $str .= '<li class="am-active" onclick="get_room_id('.$num.','.$room_name['id'].')"><a href="#tab'.$num.'" >'.$room_name['name'].'</a></li>';
                        $cat_str .='<div class="am-tab-panel am-fade am-in am-active" id="tab'.$num.'">
                        '.$_str.'</div>';

                    }else{
                        $str .= '<li onclick="get_room_id('.$num.','.$room_name['id'].')"><a href="#tab'.$num.'">'.$room_name['name'].'</a></li>';
                        $cat_str .='<div class="am-tab-panel am-fade " id="tab'.$num.'">
                        '.$_str.'</div>';
                    }
                    $num ++;
                //}

            }
        }
        $this->assign('sum',$sum);
        $this->assign('cat_str',$cat_str);
        $this->assign('title_str',$str);

        $this->display();

    }
    /**
     * 获取房间名字
     */
    private function get_name($key){
        $room_data = M('room')->field('id,name,type')->where("type = '$key'")->find();
        return $room_data;
    }

    /**
     * @param $id
     * @return mixed
     * 获取分类
     */
    private function get_category($id){
        $wh['room_id'] = $id;
        $catid = M('Goodstype')->where($wh)->getField('catid');
        if ($catid) {
            unset($wh);
            $wh['a.id'] = array('in',$catid);
            $list = M('category')->alias('a')->field('a.*')->distinct(true)
                ->join('gms_goods b on a.id = b.cat_id')
                ->where($wh)->order('sort,title')->select();
           return $list;
        }
    }

    /**
     * 获取品牌
     */
    private function get_brand($id){
        $wh['cat_id'] = $id;
        $list = M('goods')->alias('a')->field('b.*')->distinct(true)
            ->join('gms_brand b on a.brand_id = b.id')->order('b.sort desc')->where($wh)->select();
        return $list;
    }

    /**
     * 套餐信息入库
     * 数据介绍
     * room_data 数据索引是房间ID 值为商品ID
     * return bool
     */
    function savemateriala(){
        extract(I('post.'));

        if(!is_array($room_data)){
            header('location:'.U('Meal/material'));
            exit;
        }else{
            //准备工作
            //获取表中最大的分类ID  创建 新的分类 (默认为一) 随着套餐的增加依次递增
            $meal_material = M('meal_material');

            $max_order_id = $meal_material->field('max(order_id) as max_order_id')->find();
            if(is_null($max_order_id['max_order_id'])){
                $_max_order_id = 1;
            }else{
                $_max_order_id = $max_order_id['max_order_id']+1;
            }
            //组织 要增加的数据结构
            $datalist = array();
            //总金额
            $allPrice = 0;
            //金额 类型 (加价， 减价)
            $allPriceType = 1;
            foreach ($room_data as $key => $val){
                if(is_array($val)){
                    foreach ($val as $_key => $_val){
                        $amount = $room_amount_data[$key][$_key];
                        //根据 金额的 类型 进行 加减总金额
                        if($price_type[$key][$_key] == 'sub'){
                            $allPrice += $price[$key][$_key] * $amount;
                        }elseif($price_type[$key][$_key] == 'add'){
                            $allPrice -= $price[$key][$_key] * $amount;
                        }
                        $num = is_numeric($amount) ? (int)$amount : 1;
                        $datalist[] = array('meal_id'=>$meal_id,'room_id'=>$key,'goods_id'=>$_val,'amount'=>$num,'order_id'=>$_max_order_id);
                    }
                }
            }

            if($allPrice > 0){
                $allPriceType = 1;
            }else{
                $allPrice = 0 - $allPrice;
                $allPriceType = 2;
            }
            //添加套餐的名称
            if(empty($meal_name)){
                $meal_name = '默认套餐';
            }
            M('meal_title')->add(array('title'=>$meal_name,'order_id'=>$_max_order_id,'price'=>$allPrice,'price_type' => $allPriceType,'meal_id'=>$meal_id));
            $bool = $meal_material->addAll($datalist);
            if($bool){
                header('location:'.U('Meal/index'));
            }
        }
    }
}