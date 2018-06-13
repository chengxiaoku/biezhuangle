<?php 
/*
 * 说明文档模型
 * Time   : 1479643803 
 */
 
namespace Cms\Model;
use Think\Model;

class DocumentModel extends Model{
	
    /*模型中定义的表*/
	protected $tableName = 'document'; 

    /* 自动验证规则 */
	protected $_validate = array(		array('title','require','标题必须'),
		array('version','require','版本必须'),
		array('path','require','文件必须'),
 
	);

    /* 自动完成规则 */
	protected $_auto = array(
    		array("create_time","strtotime",3,"function"),
    		array('create_time','time',1,'function'),
     
	);

}