<?php
/*
 * 案例管理模型
 * Time   : 1476524378
 */

namespace Cms\Model;
use Think\Model;

class CaseModel extends Model{

    /*模型中定义的表*/
	protected $tableName = 'case';

    /* 自动验证规则 */
	protected $_validate = array(		array('sort','number','序号必须为数字'),


	);

    /* 自动完成规则 */
	protected $_auto = array(
        array('create_time','time',1,'function'),
	);

}
