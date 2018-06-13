<?php 
/*
 * 管家收入模型
 * Time   : 1496827561 
 */
 
namespace Cms\Model;
use Think\Model;

class IncomeModel extends Model{
	
    /*模型中定义的表*/
	protected $tableName = 'income'; 

    /* 自动验证规则 */
	protected $_validate = array( 
	);

    /* 自动完成规则 */
	protected $_auto = array(
    		array("create_time","strtotime",3,"function"),
    		array('create_time','time',1,'function'),
     
	);

}