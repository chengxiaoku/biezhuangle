<?php 
/*
 * 收藏模型
 * Time   : 1486780611 
 */
 
namespace Cms\Model;
use Think\Model;

class FavorModel extends Model{
	
    /*模型中定义的表*/
	protected $tableName = 'favor'; 

    /* 自动验证规则 */
	protected $_validate = array( 
	);

    /* 自动完成规则 */
	protected $_auto = array(
    		array("create_time","strtotime",3,"function"),
    		array('create_time','time',1,'function'),
     
	);

}