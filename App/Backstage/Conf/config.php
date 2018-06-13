<?php
return array(
    'TMPL_PARSE_STRING' => array(
        '__STATIC__' => __ROOT__ . '/Public/Static',
        '__IMG__'    => __ROOT__ . '/Public/Backstage/images',
        '__CSS__'    => __ROOT__ . '/Public/Backstage/css',
        '__JS__'     => __ROOT__ . '/Public/Backstage/js',
        '__HTML__'   => __ROOT__ . '/Public/Backstage/html',
    ),
    /* SESSION设置 */
    'SESSION_AUTO_START' => true, // 是否自动开启Session
    'SESSION_OPTIONS' => array (), // session 配置数组 支持type name id path expire domain 等参数
    'SESSION_TYPE' => '', // session hander类型 默认无需设置 除非扩展了session hander驱动
    //'SESSION_PREFIX' => 'home_', // session 前缀
    /* 后台错误页面模板 */
    //'TMPL_ACTION_ERROR'     => 'Public/error', // 默认错误跳转对应的模板文件
    //'TMPL_ACTION_SUCCESS'   =>  'Public/success', // 默认成功跳转对应的模板文件
    'TMPL_FILE_DEPR' => '_', // 模板文件CONTROLLER_NAME与ACTION_NAME之间的分割符

   
);
