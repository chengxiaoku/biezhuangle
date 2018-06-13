<?php
function is_append($note_id){
    $wh['reject_time'] = 0;
    $wh['note_id'] = $note_id;
    if (M('append')->where($wh)->count()>0) {
        return false;
    }
    return true;
}

function allow_add_note($deco_id,$node_id){
    $wh['a.pid'] = array('gt',0);
    $wh['b.id'] = array('exp','IS NULL');
    $result = M('nodes')->alias('a')
        ->join('left join gms_progress b on a.id = b.node_id and status = 4 and deco_id = '.$deco_id)
        ->where($wh)->order('sort')->getField('a.id');
    return $result==$node_id?true:false;
}
