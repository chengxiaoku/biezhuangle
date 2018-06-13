<?php 
/*
 * 加盟管理模型
 * Time   : 1480039790 
 */
 
namespace Cms\Model;
use Think\Model;

class JoininModel extends Model{
	
    /*模型中定义的表*/
	protected $tableName = 'joinin'; 

    /* 自动验证规则 */
	protected $_validate = array(		array('name','require','姓名必须'),

		array('email','email','邮箱格式不正确'),
		array('phone','/^1[3|4|5|8][0-9]\d{4,8}$/','电话格式不正确'),
		array('content','require','内容必须'),
 
	);

    /* 自动完成规则 */
	protected $_auto = array(
     
	);

}