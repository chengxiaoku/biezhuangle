<?php 
/*
 * 评论模型
 * Time   : 1486780619 
 */
 
namespace Cms\Model;
use Think\Model;

class CommentModel extends Model{
	
    /*模型中定义的表*/
	protected $tableName = 'comment'; 

    /* 自动验证规则 */
	protected $_validate = array(
		array('uid','require','提交参数异常'),
		array('tid','require','提交参数异常'),
		array('aid','require','提交参数异常'),
	);

    /* 自动完成规则 */
	protected $_auto = array(
    		array("create_time","strtotime",3,"function"),
    		array('create_time','time',1,'function'),
     
	);

}