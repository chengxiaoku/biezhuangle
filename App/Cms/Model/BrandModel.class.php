<?php 
/*
 * 品牌模型
 * Time   : 1488614221 
 */
 
namespace Cms\Model;
use Think\Model;

class BrandModel extends Model{
	
    /*模型中定义的表*/
	protected $tableName = 'brand'; 

    /* 自动验证规则 */
	protected $_validate = array(		array('name','require','名称必须'),
 
	);

    /* 自动完成规则 */
	protected $_auto = array(
     
	);

}