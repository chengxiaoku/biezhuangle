<?php 
/*
 * 公司风采模型
 * Time   : 1476688197 
 */
 
namespace Cms\Model;
use Think\Model;

class StyleModel extends Model{
	
    /*模型中定义的表*/
	protected $tableName = 'style'; 

    /* 自动验证规则 */
	protected $_validate = array(		array('title','require','标题必须'),

		array('thumb','require','缩略图必须'),

 
	);

    /* 自动完成规则 */
	protected $_auto = array(
     
	);

}