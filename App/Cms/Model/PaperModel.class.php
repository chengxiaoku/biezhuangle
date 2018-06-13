<?php
/*
 * 试卷模型
 * Time   : 1482206679
 */

namespace Cms\Model;
use Think\Model;

class PaperModel extends Model{

    /*模型中定义的表*/
	protected $tableName = 'paper';

    /* 自动验证规则 */
	protected $_validate = array(
        array('title','require','标题必须'),
        array('score1','require','单选每题分数必须'),
        array('intro1','require','单选题描述必须'),
        array('score2','require','多选每题分数必须'),
        array('intro2','require','多选题描述必须'),
	);

    /* 自动完成规则 */
	protected $_auto = array(

	);

}
