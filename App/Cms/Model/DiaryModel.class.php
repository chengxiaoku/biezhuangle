<?php 
/*
 * 管家日记模型
 * Time   : 1490584721 
 */
 
namespace Cms\Model;
use Think\Model;

class DiaryModel extends Model{
	
    /*模型中定义的表*/
	protected $tableName = 'diary'; 

    /* 自动验证规则 */
	protected $_validate = array( 
	);

    /* 自动完成规则 */
	protected $_auto = array(
    		array("create_time","strtotime",3,"function"),
    		array('create_time','time',1,'function'),
     
	);

}