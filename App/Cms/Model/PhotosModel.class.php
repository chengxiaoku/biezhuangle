<?php 
/*
 * 图片管理模型
 * Time   : 1489559704 
 */
 
namespace Cms\Model;
use Think\Model;

class PhotosModel extends Model{
	
    /*模型中定义的表*/
	protected $tableName = 'photos'; 

    /* 自动验证规则 */
	protected $_validate = array( 
	);

    /* 自动完成规则 */
	protected $_auto = array(
    		array("create_time","strtotime",3,"function"),
     
	);

}