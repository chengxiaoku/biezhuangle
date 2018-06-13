<?php 
/*
 * 装修设计图模型
 * Time   : 1491647061 
 */
 
namespace Cms\Model;
use Think\Model;

class DesignModel extends Model{
	
    /*模型中定义的表*/
	protected $tableName = 'design'; 

    /* 自动验证规则 */
	protected $_validate = array( 
		array('image','require','图片必须',1),
		array('deco_id','require','请求错误，请刷新后提交'),
	);

    /* 自动完成规则 */
	protected $_auto = array(
		array('image','base64toimg',1,'function'),
	);

}