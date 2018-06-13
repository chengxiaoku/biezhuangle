<?php 
/*
 * 文章管理模型
 * Time   : 1486620116 
 */
 
namespace Cms\Model;
use Think\Model;

class ArticleModel extends Model{
	
    /*模型中定义的表*/
	protected $tableName = 'article'; 

    /* 自动验证规则 */
	protected $_validate = array( 
	);

    /* 自动完成规则 */
	protected $_auto = array(
    		array("create_time","strtotime",3,"function"),
    		array('create_time', 'time', 1, 'function'),
     
	);

}