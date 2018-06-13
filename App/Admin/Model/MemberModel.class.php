<?php 
/*
 * 会员模型
 * Time   : 1474253172 
 */
 
namespace Admin\Model;
use Think\Model;

class MemberModel extends Model{
	
    /*模型中定义的表*/
	protected $tableName = 'member'; 

    /* 自动验证规则 */
	protected $_validate = array( 
	);

    /* 自动完成规则 */
	protected $_auto = array(
    		array('password','md5',3,'function'),
     
	);

}