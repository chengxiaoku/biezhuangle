<?php 
/*
 * 驳回申请模型
 * Time   : 1496723823 
 */
 
namespace Cms\Model;
use Think\Model;

class RejectModel extends Model{
	
    /*模型中定义的表*/
	protected $tableName = 'reject'; 

    /* 自动验证规则 */
	protected $_validate = array( 
	);

    /* 自动完成规则 */
	protected $_auto = array(
    		array("create_time","strtotime",3,"function"),
    		array('create_time','time',1,'function'),
     
	);

}