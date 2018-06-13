<?php
/*
 * 前台菜单模型
 * Time   : 1481961784
 */

namespace Cms\Model;
use Think\Model;

class MenusModel extends Model{

    /*模型中定义的表*/
	protected $tableName = 'menus';

    /* 自动验证规则 */
	protected $_validate = array(
        array('name', 'require', '节点不能为空！'),
        array('title', 'require', '标题不能为空！'),
	);

    /* 自动完成规则 */
	protected $_auto = array(

	);

}
