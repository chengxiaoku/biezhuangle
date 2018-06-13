<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/8/10
 * Time: 15:34
 * 装修套餐接口
 */
namespace Api\Controller;
use Think\Controller;
class MealController extends BaseController{

    /**
     * 获取风格类型(套餐种类)
     */
    function get_style_list(){
        //获取套餐列表
        $data = M('meal')->field('id,style_name')->select();

        if(!is_null($data)){
            foreach ($data as $key => $val ){
                //获取方案
                $data[$key]['plan_list'] = M('meal_title')->field('title,id,order_id')->where(array('meal_id'=>$val['id']))->select();
            }
            $new_data['list'] = $data;
        }else{
            $this->ajaxError('暂时无数据');
        }
        $this->ajaxReturn($new_data);
    }

    /**
     * 获取商品信息
     * @param $room_id  房间ID
     * @param $plan_id  方案ID
     */
    function get_goods_data($room_id,$plan_id){
        $paly_data = M('meal_title')->field('order_id')->find($plan_id);
        if(!$paly_data){
            $this->ajaxError('暂无数据');
        }else{
            $data = M('meal_material')->where(array('order_id'=>$paly_data['order_id'],'room_id'=>$room_id))->select();
            if(!is_null($data)){
                foreach ($data as $key => $val){
                    $data[$key] = M('goods')->find($val['goods_id']);
                }
                $this->ajaxReturn($data);
            }else{
                $this->ajaxError('暂无数据');
            }
        }
    }
}
















