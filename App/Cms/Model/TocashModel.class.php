<?php 
/*
 * 提现申请模型
 * Time   : 1496479484 
 */
 
namespace Cms\Model;
use Think\Model;

class TocashModel extends Model{
	
    /*模型中定义的表*/
	protected $tableName = 'tocash'; 

    /* 自动验证规则 */
	protected $_validate = array( 
	);

    /* 自动完成规则 */
	protected $_auto = array(
    		array("create_time","strtotime",3,"function"),
    		array('create_time','time',1,'function'),
     
	);

}