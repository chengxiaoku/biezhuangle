<?php
/*
 * 在线咨询模型
 * Time   : 1482372779
 */

namespace Home\Model;
use Think\Model;

class ConsultModel extends Model{

    /*模型中定义的表*/
	protected $tableName = 'consult';

    /* 自动验证规则 */
	protected $_validate = array(
        array('qid','require','请刷新重试',1),
        array('content','require','内容必须'),
	);

    /* 自动完成规则 */
	protected $_auto = array(
		array('create_time','time',1,'function'),
	);

}
