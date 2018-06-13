<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/7/8
 * Time: 16:59
 */
namespace Home\Controller;
use Think\Controller;

class EmptyController extends Controller{
    function _empty(){
        echo "<img src='".C('website_url')."'>";
    }

}