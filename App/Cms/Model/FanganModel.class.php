<?php 
/*
 * 解决方案模型
 * Time   : 1476841099 
 */
 
namespace Cms\Model;
use Think\Model;

class FanganModel extends Model{
	
    /*模型中定义的表*/
	protected $tableName = 'fangan'; 

    /* 自动验证规则 */
	protected $_validate = array(		array('title','require','标题必须'),

 
	);

    /* 自动完成规则 */
	protected $_auto = array(
     
	);

}