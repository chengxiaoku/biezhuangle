<?php
/*
 * 试题模型
 * Time   : 1482197510
 */

namespace Cms\Model;
use Think\Model;

class QuizModel extends Model{

    /*模型中定义的表*/
	protected $tableName = 'quiz';

    /* 自动验证规则 */
	protected $_validate = array(
        array('title','require','问题必须'),
        array('option1','require','选项A必须'),
        array('option2','require','选项B必须'),
        array('option3','require','选项C必须'),
        array('option4','require','选项D必须'),
        array('answer','require','答案必须'),
	);

    /* 自动完成规则 */
	protected $_auto = array(

	);

}
