<?php
/*
 * 视频管理模型
 * Time   : 1480039485
 */

namespace Store\Model;
use Think\Model;

class AppendModel extends Model{

    /*模型中定义的表*/
	protected $tableName = 'append'; 

    /* 自动验证规则 */
	protected $_validate = array(
        array('title','require','标题必须'),
        array('note_id','require','提交异常'),
        array('status','require','提交异常'),

	);

    /* 自动完成规则 */
	protected $_auto = array(
        array('create_time','time',1,'function'),
	);

}
