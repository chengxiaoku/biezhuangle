<?php 
/*
 * 施工进度模型
 * Time   : 1493717944 
 */
 
namespace Cms\Model;
use Think\Model;

class WorksModel extends Model{
	
    /*模型中定义的表*/
	protected $tableName = 'works'; 

    /* 自动验证规则 */
	protected $_validate = array(		array('deco_id','require','参数提交异常'),
		array('status','require','施工状态必须'),
		array('content','require','施工内容必须'),
 
	);

    /* 自动完成规则 */
	protected $_auto = array(
    		array("create_time","strtotime",3,"function"),
    		array('create_time','time',1,'function'),
     
	);

}