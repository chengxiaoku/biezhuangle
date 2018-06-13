<?php
/*
 * 报名管理模型
 * Time   : 1476762629
 */

namespace Home\Model;
use Think\Model;

class FeedbackModel extends Model{

    /*模型中定义的表*/
	protected $tableName = 'feedback';

    /* 自动验证规则 */
	protected $_validate = array(
        array('name','require','姓名必须'),

		array('phone','/^1[3|4|5|8][0-9]\d{4,8}$/','电话格式不正确'),

		//array('version','require','设备型号必须'),

		array('content','require','反馈内容必须'),


	);

    /* 自动完成规则 */
	protected $_auto = array(
    	array('create_time','time',1,'function'),
	);

}
