<?php 
/*
 * 在线咨询模型
 * Time   : 1482372779 
 */
 
namespace Cms\Model;
use Think\Model;

class ConsultModel extends Model{
	
    /*模型中定义的表*/
	protected $tableName = 'consult'; 

    /* 自动验证规则 */
	protected $_validate = array( 
	);

    /* 自动完成规则 */
	protected $_auto = array(
    		array("create_time","strtotime",3,"function"),
    		array('create_time','time',1,'function'),
     
	);

}