<?php 
/*
 * 装修日记模型
 * Time   : 1489548232 
 */
 
namespace Cms\Model;
use Think\Model;

class ProgressModel extends Model{
	
    /*模型中定义的表*/
	protected $tableName = 'progress'; 

    /* 自动验证规则 */
	protected $_validate = array(
		array('title','require','标题必须'),
		array('deco_id','require','提交参数异常'),
		array('comp_id','require','提交参数异常'),
		array('node_id','require','提交参数异常'),
	);

    /* 自动完成规则 */
	protected $_auto = array(
		array("create_time","strtotime",3,"function"),
     
	);

}