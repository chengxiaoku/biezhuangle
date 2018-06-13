<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/8/10
 * Time: 15:34
 * 装修套餐接口
 */
namespace Apitest\Controller;
use Think\Controller;
class MealController extends BaseController{

    /**
     * @param $deco_id  订单ID
     * @param $style    风格
     */
    function get_meal_list($deco_id,$style = 0){
        //根据订单号查询用户订单信息
        $deco_data = M('decorate')->find($deco_id);
        //筛选订单方案中 哪一种比较适合

        if(!empty($style)){
            $wh['style_id'] = $style;
        }
        $wh['hall'] = $deco_data['hall'];
        $wh['dining'] = $deco_data['dining'];
        $wh['cook'] = $deco_data['cook'];
        $wh['toilet'] = $deco_data['toilet'];
        $wh['balcony'] = $deco_data['balcony'];
        $wh['room'] = $deco_data['room'];
        $data = M('meal')->field('id')->where($wh)->select();
        if(is_null($data)){
            $this->ajaxError('无内容');
        }else{
            $this->get_meal_info($data,$deco_data['area']);
        }

    }

    /**
     * 获取套餐内容
     */
    private function get_meal_info($arr_data,$area){
        if(!is_array($arr_data)){
            return '';
        }
        $meal_area_price = $area * 699;

        $datalist = array();
        foreach ($arr_data as $key => $val){
            $_data = M('meal_material a')
                ->field('a.*,b.title,b.photo,d.name as stylename,b.price,b.price_type')
                ->join('left join gms_meal_title as b ON a.order_id = b.order_id')
                ->join('left join gms_meal as c ON a.meal_id = c.id')
                ->join('left join gms_meal_style as d ON d.id = c.style_id')
                ->where(array('a.meal_id'=>$val['id']))
                ->group('a.order_id')
                ->select();
            if(!empty($_data)){
                $datalist['list'][$key] = $_data;
            }
        }

        $new_datalist = array();
        if(!empty($datalist)){
            $num = 0;
            foreach ($datalist['list'] as $key => $val){
                if(is_array($val)){
                    foreach ($val as $_key => $_val){
                        //加价
                        if($_val['price_type'] == 1){
                            $_val['price'] += $meal_area_price;
                        }elseif($_val['price_type'] == 2){
                            $_val['price'] = $meal_area_price - $_val['price'];
                        }else{
                            $_val['price'] = $meal_area_price;
                        }
                        $new_datalist['list'][$num] = $_val;
                        $num++;
                    }

                }
            }
        }
        $this->ajaxReturn($new_datalist);
    }

    /**
     * 获取样式
     */
    function get_style_list(){
        $this->ajaxReturn(M('meal_style')->select());
    }

    /**
     * 获取套餐详情
     */
    function get_meal_data($order_id,$deco_id){
        //修改套餐步骤
        M('decorate')->where(array('id'=>$deco_id))->save(array('step_bak'=>2));

        $decorate = M('decorate')->field('area')->find($deco_id);
        $area_money = $decorate['area'] * 699;
        $meal_price = M('meal_title')->field('price,price_type')->where(array('order_id'=>$order_id))->find();

        if($meal_price['price_type'] == 1){
            $area_money +=$meal_price['price'];
        }elseif($meal_price['price_type'] == 2){
            $area_money -=$meal_price['price'];
        }

        $new_data = array();
        $data = M('meal_material a')
            ->field('a.*,b.title,b.coverimg,b.markup,b.content,b.markup_price,b.unit,c.name as brand_name,c.icon as brand_icon,d.title as cat_name,d.id as cat_id')
            ->join('LEFT JOIN gms_goods as b ON b.id = a.goods_id')
            ->join('LEFT JOIN gms_brand as c ON c.id = b.brand_id')
            ->join('LEFT JOIN gms_category as d ON d.id = b.cat_id')
            ->where(array('a.order_id'=>$order_id))
            ->order('a.room_id ASC')
            ->select();

        $_arr = array();
        $_new_arr = array();
        if(is_array($data)){
            foreach ($data as $key => $val){
                if(!in_array($val['room_id'], $_arr)){
                    $new_data['data'][$val['room_id']] = M('room')->find($val['room_id']);
                    $_arr[] = $val['room_id'];
                }
                $_new_arr[$val['room_id']][] = $val;
            }
            foreach ($new_data['data'] as $key => $val){
                $new_data['data'][$key]['goods'] = $_new_arr[$key];
            }
        }
        $new_data['allprice'] = $area_money;
        $this->ajaxReturn($new_data);
    }
}
















