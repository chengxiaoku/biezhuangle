<?php 
/*
 * 装修订单模型
 * Time   : 1488938579 
 */
 
namespace Cms\Model;
use Think\Model;

class DecorateModel extends Model{
	
    /*模型中定义的表*/
	protected $tableName = 'decorate'; 

    /* 自动验证规则 */
	protected $_validate = array(		
		array('area','currency','必须为数字'),
	);

    /* 自动完成规则 */
	protected $_auto = array(
		array('create_time','time',1,'function'),
		array("create_time","strtotime",3,"function"),
	);

}