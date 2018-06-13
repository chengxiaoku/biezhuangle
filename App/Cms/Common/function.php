<?php

function controlrate($val_a,$val_b){
    $count = $val_a+$val_b;
    $val = $val_b/$count*100;
    return sprintf("%.1f", $val);
}



function getlevel($cid){
    $wh = array();
    $list = M('level')->where($wh)->select();
    $this->assign('level_list',$list);
}
