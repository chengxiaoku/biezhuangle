<?php 
/*
 * 公司动态模型
 * Time   : 1476667296 
 */
 
namespace Cms\Model;
use Think\Model;

class NewsModel extends Model{
	
    /*模型中定义的表*/
	protected $tableName = 'news'; 

    /* 自动验证规则 */
	protected $_validate = array(		array('title','require','标题必须'),

		array('intro','require','描述必须'),

		array('thumb','require','缩略图必须'),

 
	);

    /* 自动完成规则 */
	protected $_auto = array(
    		array("create_time","strtotime",3,"function"),
    		array('create_time','time',1,'function'), 
     
	);

}