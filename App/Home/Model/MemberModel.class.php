<?php
/*
 * 报名管理模型
 * Time   : 1476762629
 */

namespace Home\Model;
use Think\Model;

class MemberModel extends Model{

    /*模型中定义的表*/
	protected $tableName = 'member';

    /* 自动验证规则 */
	protected $_validate = array(
        //array('telphone','/^1[3|4|5|8][0-9]\d{4,8}$/','手机号格式不正确'),
        array('username','','手机号已经存在！',0,'unique',1),
        array('nickname','require','昵称必须'),
        array('password','require','密码必须'),
        array('repassword','password','确认密码不正确',0,'confirm'),
        array('password','checkPwd','密码格式不正确',0,'function'),
        // array('email','email','邮箱格式不正确'),
        // array('content','require','留言内容必须'),
        // array('name','checkName','帐号错误！',1,'function',4),
        array('password','checkPwd','密码错误！',1,'function',4),
	);

    /* 自动完成规则 */
	protected $_auto = array(
        array('password','md5',3,'function'),
		array('create_time','time',1,'function'),
	);

}
