<?php 
/*
 * 通知公告模型
 * Time   : 1486612853 
 */
 
namespace Cms\Model;
use Think\Model;

class NoticeModel extends Model{
	
    /*模型中定义的表*/
	protected $tableName = 'notice'; 

    /* 自动验证规则 */
	protected $_validate = array( 
	);

    /* 自动完成规则 */
	protected $_auto = array(
    		array("create_time","strtotime",3,"function"),
    		array('create_time','time',1,'function'),
     
	);

}