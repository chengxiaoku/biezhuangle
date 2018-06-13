<?php 
/*
 * 汽车模型
 * Time   : 1472197953 
 */
 
namespace Admin\Model;
use Think\Model;

class CarModel extends Model{
	
    /*模型中定义的表*/
	protected $tableName = 'car'; 

    /* 自动验证规则 */
	protected $_validate = array(		array('upset_price','number','原车价格必须'),
		array('upset_price','number','起拍价格必须'),

		array('end_time','require','结束时间必须'),

		array('mileage','number','行驶里程必须为数字'),

		array('cur_price','','必须为数字',),

		array('step_price','number','必须为数字'),

		array('mar_price','','保证金必须'),

		array('bidding_cycle','number',''),

		array('delay_cycle','number','延时周期必须'),

 
	);

    /* 自动完成规则 */
	protected $_auto = array(
    		array("buy_time","strtotime",3,"function"),
    		array("end_time","strtotime",3,"function"),
    		array("create_time","strtotime",3,"function"),
    		array('create_time', date, 1, 'fucntion', array('Y-m-d H:i:s')),
     
	);

}