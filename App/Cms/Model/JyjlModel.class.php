<?php
/*
 * 经验交流模型
 * Time   : 1482462835
 */

namespace Cms\Model;
use Think\Model;

class JyjlModel extends Model{

    /*模型中定义的表*/
	protected $tableName = 'jyjl';

    /* 自动验证规则 */
	protected $_validate = array(
        array('title','require','标题必须'),
        array('content','require','内容必须'),
	);

    /* 自动完成规则 */
	protected $_auto = array(
    		array("create_time","strtotime",3,"function"),
    		array('create_time','time',1,'function'),

	);

}
