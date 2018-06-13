<?php 
/*
 * 设计师模型
 * Time   : 1492501390 
 */
 
namespace Cms\Model;
use Think\Model;

class DesignerModel extends Model{
	
    /*模型中定义的表*/
	protected $tableName = 'designer'; 

    /* 自动验证规则 */
	protected $_validate = array( 
		array('name','require','姓名必须'),
		array('year','number','工作年限必须为数字！'),
		array('phone','/^1[3|4|5|7|8][0-9]\d{4,8}$/','电话格式不正确'),
	);

    /* 自动完成规则 */
	protected $_auto = array(
		array('photo','base64toimg',3,'function'),
	);

}