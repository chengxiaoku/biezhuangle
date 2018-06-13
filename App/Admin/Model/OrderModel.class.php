<?php 
/*
 * 领奖订单模型
 * Time   : 1471075461 
 */
 
namespace Admin\Model;
use Think\Model;

class OrderModel extends Model{
	
    /*模型中定义的表*/
	protected $tableName = 'order'; 

    /* 自动验证规则 */
	protected $_validate = array( 
	);

    /* 自动完成规则 */
	protected $_auto = array(
     
	);

}