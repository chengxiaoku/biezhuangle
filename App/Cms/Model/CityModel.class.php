<?php 
/*
 * 城市模型
 * Time   : 1488253510 
 */
 
namespace Cms\Model;
use Think\Model;

class CityModel extends Model{
	
    /*模型中定义的表*/
	protected $tableName = 'city'; 

    /* 自动验证规则 */
	protected $_validate = array(		array('name','require','名称必须'),
		array('sort','number','必须为数字'),

 
	);

    /* 自动完成规则 */
	protected $_auto = array(
     
	);

}