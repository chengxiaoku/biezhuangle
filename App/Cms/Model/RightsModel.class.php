<?php 
/*
 * 快速维权模型
 * Time   : 1491034073 
 */
 
namespace Cms\Model;
use Think\Model;

class RightsModel extends Model{
	
    /*模型中定义的表*/
	protected $tableName = 'rights'; 

    /* 自动验证规则 */
	protected $_validate = array( 
	);

    /* 自动完成规则 */
	protected $_auto = array(
    		array("create_time","strtotime",3,"function"),
    		array('create_time','time',1,'function'),
     
	);

}