<?php 
/*
 * 主材料模型
 * Time   : 1488856180 
 */
 
namespace Cms\Model;
use Think\Model;

class GoodsModel extends Model{
	
    /*模型中定义的表*/
	protected $tableName = 'goods'; 

    /* 自动验证规则 */
	protected $_validate = array(		array('unit','require','单位必须'),
 
	);

    /* 自动完成规则 */
	protected $_auto = array(
     
	);

}