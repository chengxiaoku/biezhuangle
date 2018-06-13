<?php
/*
 * 友情链接模型
 * Time   : 1482720670
 */

namespace Cms\Model;
use Think\Model;

class MylinkModel extends Model{

    /*模型中定义的表*/
	protected $tableName = 'mylink';

    /* 自动验证规则 */
	protected $_validate = array(
        array('title', 'require', '标题不能为空！'),
        array('href', 'require', '链接不能为空！'),
	);

    /* 自动完成规则 */
	protected $_auto = array(

	);

}
