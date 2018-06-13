<?php
return array(
	//'配置项'=>'配置值'
    'SMS_CONFIG' => array(
        "Id" => "300",
        "Name" => "youjiawang",
        "Psw" => "youjiawang123456",
        "Phone" => "",
        "Message" => "验证码：CODE(切勿泄露给他人)，如非本人操作，请忽略本短信。",
        "Timestamp" => ""
    ),
    'TMPL_FILE_DEPR' => '_', // 模板文件CONTROLLER_NAME与ACTION_NAME之间的分割符
    'TMPL_PARSE_STRING' => array(
        '__STATIC__' => __ROOT__ . '/Public/Static',
        '__IMG__'    => __ROOT__ . '/Public/Apitest/images',
        '__CSS__'    => __ROOT__ . '/Public/Apitest/css',
        '__JS__'     => __ROOT__ . '/Public/Apitest/js',
        '__HTML__'   => __ROOT__ . '/Public/Apitest/html',
    ),
    //联系我们
    'ContactUs' => array(
        'tel' => 80882044,
        'weixin' => 17703799560,
        'qq' => 778281274,
        'banquan' => 'Copyright © 2015-2017 改造家 All rights reserved',
        'info' => '郑州改造家网络技术有限公司股权所有',
    ),
    //节点总数配置
    'Schedule_sum' => 15,
);
