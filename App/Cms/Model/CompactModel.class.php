<?php 
/*
 * 装修合同模型
 * Time   : 1491647072 
 */
 
namespace Cms\Model;
use Think\Model;

class CompactModel extends Model{
	
    /*模型中定义的表*/
	protected $tableName = 'compact'; 

    /* 自动验证规则 */
	protected $_validate = array( 
	);

    /* 自动完成规则 */
	protected $_auto = array(
    		array("create_time","strtotime",3,"function"),
    		array('create_time','time',1,'function'),
     
	);

}