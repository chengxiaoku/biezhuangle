<?php

return array(

	/* 默认设定 */
	'DEFAULT_M_LAYER' => 'Model', // 默认的模型层名称
	'DEFAULT_C_LAYER' => 'Controller', // 默认的控制器层名称
	'DEFAULT_V_LAYER' => 'View', // 默认的视图层名称
	'DEFAULT_LANG' => 'zh-cn', // 默认语言
	'DEFAULT_THEME' => '', // 默认模板主题名称
	'DEFAULT_MODULE' => 'Home', // 默认模块
	//'MODULE_ALLOW_LIST' => array('Admin','Api','Backstage','Cms','Home','Store'),//允许访问的模块列表
	'MODULE_DENY_LIST' => array ('Common','Runtime'),
	'DEFAULT_CONTROLLER' => 'Index', // 默认控制器名称
	'DEFAULT_ACTION' => 'index', // 默认操作名称
	'DEFAULT_CHARSET' => 'utf-8', // 默认输出编码
	'DEFAULT_TIMEZONE' => 'PRC', // 默认时区
	'DEFAULT_AJAX_RETURN' => 'JSON', // 默认AJAX 数据返回格式,可选JSON XML ...
	'DEFAULT_JSONP_HANDLER' => 'jsonpReturn', // 默认JSONP格式返回的处理方法
	'DEFAULT_FILTER' => 'htmlspecialchars', // 默认参数过滤方法 用于I函数...
	'LOAD_EXT_CONFIG' => 'self_config',//加载自定义

	'SOFT_NAME'=>'别装了管理系统',
	'SOFT_SITE'=>'',
	'SOFT_BBS'=>'',
	'SOFT_VERSION'=>'2.0.1',
	'SOFT_AUTHOR'=>'linfeng',
	'SOFT_AUTHOR_QQ'=>'',
	'SOFT_AUTHOR_EMAIL'=>'',
	'SOFT_AUTHOR_SITE'=>'',
	'CLOUD_SHOP_SITE'=>'http://127.0.0.1/TGW/index.php?m=CloudShop&c=Index&a=lists',//云商店地址

	/* 数据库设置 */
	'DB_TYPE' => 'mysql', // 数据库类型 
	'DB_HOST' => 'localhost', // 服务器地址47.93.4.46
	'DB_NAME' => 'biezhuangle', // 数据库名
	'DB_USER' => 'root', // 用户名
	'DB_PWD' => 'root', // 密码biezhuangleDBPP
	'DB_PORT' => '3306', // 端口 
	'DB_PREFIX' => 'gms_', // 数据库表前缀
	'DB_FIELDTYPE_CHECK' => true, // 是否进行字段类型检查
	'DB_FIELDS_CACHE' => true, // 启用字段缓存
	'DB_CHARSET' => 'utf8', // 数据库编码默认采用utf8
	'DB_DEPLOY_TYPE' => 0, // 数据库部署方式:0 集中式(单一服务器),1 分布式(主从服务器)
	'DB_RW_SEPARATE' => false, // 数据库读写是否分离 主从式有效
	'DB_MASTER_NUM' => 1, // 读写分离后 主服务器数量
	'DB_SLAVE_NO' => '', // 指定从服务器序号
	'DB_SQL_BUILD_CACHE' => false, // 数据库查询的SQL创建缓存
	'DB_SQL_BUILD_QUEUE' => 'file', // SQL缓存队列的缓存方式 支持 file xcache和apc
	'DB_SQL_BUILD_LENGTH' => 20, // SQL缓存的队列长度
	'DB_SQL_LOG' => true, // SQL执行日志记录
	'DB_BIND_PARAM' => false, // 数据库写入数据自动参数绑定

	/* 数据缓存设置 */
	'DATA_CACHE_TIME' => 0, // 数据缓存有效期 0表示永久缓存
	'DATA_CACHE_COMPRESS' => false, // 数据缓存是否压缩缓存
	'DATA_CACHE_CHECK' => false, // 数据缓存是否校验缓存
	'DATA_CACHE_PREFIX' => '', // 缓存前缀
	'DATA_CACHE_TYPE' => 'File', // 数据缓存类型,支持:File|Db|Apc|Memcache|Shmop|Sqlite|Xcache|Apachenote|Eaccelerator
	'DATA_CACHE_PATH' => TEMP_PATH, // 缓存路径设置 (仅对File方式缓存有效)
	'DATA_CACHE_SUBDIR' => false, // 使用子目录缓存 (自动根据缓存标识的哈希创建子目录)
	'DATA_PATH_LEVEL' => 1, // 子目录缓存级别

	/* 错误设置 */
	'ERROR_MESSAGE' => '页面错误！请稍后再试～', // 错误显示信息,非调试模式有效
	'ERROR_PAGE' => '', // 错误定向页面
	'SHOW_ERROR_MSG' => false, // 显示错误信息
	'TRACE_MAX_RECORD' => 100, // 每个级别的错误信息 最大记录数

	/* 日志设置 */
	'LOG_RECORD' => false, // 默认不记录日志
	'LOG_TYPE' => 'File', // 日志记录类型 默认为文件方式
	'LOG_LEVEL' => 'EMERG,ALERT,CRIT,ERR', // 允许记录的日志级别
	'LOG_FILE_SIZE' => 2097152, // 日志文件大小限制
	'LOG_EXCEPTION_RECORD' => true, // 是否记录异常信息日志

	/* SESSION设置 */
	'SESSION_AUTO_START' => true, // 是否自动开启Session
	'SESSION_OPTIONS' => array (), // session 配置数组 支持type name id path expire domain 等参数
	'SESSION_TYPE' => '', // session hander类型 默认无需设置 除非扩展了session hander驱动
	'SESSION_PREFIX' => 'gms_', // session 前缀

	/* Cookie设置 */
	'COOKIE_EXPIRE' => 0, // Cookie有效期
	'COOKIE_DOMAIN' => '', // Cookie有效域名
	'COOKIE_PATH' => '/', // Cookie路径
	'COOKIE_PREFIX' => '', // Cookie前缀 避免冲突
	'COOKIE_HTTPONLY' => '', // Cookie httponly设置

	/* Auth设置 */
	'AUTH_ON' => true, // 认证开关
	'AUTH_KEY' => 'Uid', // 认证key
	'AUTH_TYPE' => 1, // 认证方式，1为实时认证；2为登录认证。
	'AUTH_GROUP' => 'auth_group', // 用户组数据表名
	'AUTH_RULE' => 'auth_rule', // 权限规则表
	'AUTH_GROUP_ACCESS' => 'auth_group_access', // 用户-用户组关系表
	'AUTH_USER' => 'User', // 用户信息表
	'AUTH_ADMIN'=>array('1'),
	'NOT_AUTH_MODULE' => 'Public,Index',
	"AUTH_USER_INDEX" => "Admin/Index/index",//网关
	"AUTH_USER_GATEWAY" => "Admin/Public/login",//网关

	/* 模板引擎设置 */
	'TMPL_CONTENT_TYPE' => 'text/html', // 默认模板输出类型
	'TMPL_DETECT_THEME' => false, // 自动侦测模板主题
	'TMPL_TEMPLATE_SUFFIX' => '.html', // 默认模板文件后缀
	'TMPL_FILE_DEPR' => '/', // 模板文件CONTROLLER_NAME与ACTION_NAME之间的分割符

	// 布局设置
	'TMPL_ENGINE_TYPE' => 'Think', // 默认模板引擎 以下设置仅对使用Think模板引擎有效
	'TMPL_CACHFILE_SUFFIX' => '.php', // 默认模板缓存后缀
	'TMPL_DENY_FUNC_LIST' => 'echo,exit', // 模板引擎禁用函数
	'TMPL_DENY_PHP' => false, // 默认模板引擎是否禁用PHP原生代码
	'TMPL_L_DELIM' => '{', // 模板引擎普通标签开始标记
	'TMPL_R_DELIM' => '}', // 模板引擎普通标签结束标记
	'TMPL_VAR_IDENTIFY' => 'array', // 模板变量识别。留空自动判断,参数为'obj'则表示对象
	'TMPL_STRIP_SPACE' => true, // 是否去除模板文件里面的html空格与换行
	'TMPL_CACHE_ON' => false, // 是否开启模板编译缓存,设为false则每次都会重新编译
	'TMPL_CACHE_PREFIX' => '', // 模板缓存前缀标识，可以动态改变
	'TMPL_CACHE_TIME' => 0, // 模板缓存有效期 0 为永久，(以数字为值，单位:秒)
	'TMPL_LAYOUT_ITEM' => '{__CONTENT__}', // 布局模板的内容替换标识
	'LAYOUT_ON' => false, // 是否启用布局
	'LAYOUT_NAME' => 'layout', // 当前布局名称 默认为layout

	/* URL设置 */
	'URL_CASE_INSENSITIVE' => true, // 默认false 表示URL区分大小写 true则表示不区分大小写
	'URL_MODEL' => 2, // URL访问模式,可选参数0、1、2、3,代表以下四种模式：
	'URL_PATHINFO_DEPR' => '/', // PATHINFO模式下，各参数之间的分割符号
	'URL_PATHINFO_FETCH' => 'ORIG_PATH_INFO,REDIRECT_PATH_INFO,REDIRECT_URL', // 用于兼容判断PATH_INFO 参数的SERVER替代变量列表
	'URL_REQUEST_URI' => 'REQUEST_URI', // 获取当前页面地址的系统变量 默认为REQUEST_URI
	'URL_HTML_SUFFIX' => 'html', // URL伪静态后缀设置
	'URL_DENY_SUFFIX' => 'ico|png|gif|jpg', // URL禁止访问的后缀设置
	'URL_PARAMS_BIND' => true, // URL变量绑定到Action方法参数
	'URL_PARAMS_BIND_TYPE' => 0, // URL变量绑定的类型 0 按变量名绑定 1 按变量顺序绑定
	'URL_PARAMS_FILTER' => false, // URL变量绑定过滤
	'URL_PARAMS_FILTER_TYPE' => '', // URL变量绑定过滤方法 如果为空 调用DEFAULT_FILTER
	'URL_404_REDIRECT' => '', // 404 跳转页面 部署模式有效
	'URL_ROUTER_ON' => false, // 是否开启URL路由
	'URL_ROUTE_RULES' => array (), // 默认路由规则 针对模块
	'URL_MAP_RULES' => array (),

	/*模型字段类型*/
	'FIELD_LIST' => array (
			'num'=>array('type'=>'num','title'=>'数字','field'=>array('int(10) UNSIGNED NOT NULL','decimal(10,2) UNSIGNED NOT NULL')),
			'string'=>array('type'=>'string','title'=>'文本框','field'=>array('varchar(255) NOT NULL')),
			'textarea'=>array('type'=>'textarea','title'=>'文本区域','field'=>array('text NOT NULL')),
			'datetime'=>array('type'=>'datetime','title'=>'日期时间','field'=>array('int(11) UNSIGNED NOT NULL')),
			'select'=>array('type'=>'select','title'=>'下拉框','field'=>array('tinyint(1) NOT NULL','int(10) UNSIGNED NOT NULL','varchar(10) NOT NULL')),
			'checkbox'=>array('type'=>'checkbox','title'=>'选择框','field'=>array('varchar(10) NOT NULL')),
			'editor'=>array('type'=>'editor','title'=>'编辑器','field'=>array('text NOT NULL')),
			'pictures'=>array('type'=>'pictures','title'=>'图片','field'=>array('varchar(80) NOT NULL')),
			'files'=>array('type'=>'files','title'=>'附件','field'=>array('varchar(80) NOT NULL'))
	),


	'TAGLIB_BUILD_IN'        => 'Cx,Common\Tag\My',              // 加载自定义标签

	//支付宝配置参数 
	'ALIPAY_CONFIG'	=>array(
		'partner'		=> '2088911347181223',	//合作身份者ID，签约账号，以2088开头由16位纯数字组成的字符串，查看地址：https://b.alipay.com/order/pidAndKey.htm
		'seller_id'		=> '2088911347181223',	//收款支付宝账号，以2088开头由16位纯数字组成的字符串，一般情况下收款账号就是签约账号
		'seller_email'  => '2019149398@qq.com', // email 从支付宝商户版个人中心获取
		'key'			=> 'f1jjakd8aeg5s71gzf89ifcz9bt0zy37',	// MD5密钥，安全检验码，由数字和字母组成的32位字符串，查看地址：https://b.alipay.com/order/pidAndKey.htm
		'notify_url' 	=> "http://cloudhouse.youjiawang.com/Api/Alipay/alipay_notify",	// 服务器异步通知页面路径  需http://格式的完整路径，不能加?id=123这类自定义参数，必须外网可以正常访问
		'return_url' 	=> "http://cloudhouse.youjiawang.com/Api/Alipay/alipay_return",		// 页面跳转同步通知页面路径 需http://格式的完整路径，不能加?id=123这类自定义参数，必须外网可以正常访问
		'sign_type'    	=> strtoupper('MD5'),	//签名方式
		'input_charset'	=> strtolower('utf-8'),	//字符编码格式 目前支持utf-8
		'cacert'    	=> VENDOR_PATH.'Alipay/cacert.pem',	//ca证书路径地址，用于curl中ssl校验 请保证cacert.pem文件在当前文件夹目录中
		'transport'    	=> 'http',	//访问模式,根据自己的服务器是否支持ssl访问，若支持请选择https；若不支持请选择http
		'payment_type' 	=> "1",	// 支付类型 ，无需修改
		'service' 		=> "alipay.wap.create.direct.pay.by.user",	// 产品类型，无需修改
		//APP支付
		'APP_pay'		=> array(
			'app_id'			=> '2017062007532051',
			'rsaPrivateKey'		=> 'MIIEpAIBAAKCAQEAoTbnvcwbmDs2c3GRHcSwZz9U2uKXuMKv2EoHLFGcNmvYHMH5q26kWbiO7K8U2WsJjmUnPWgkJrP6dQbK3FAvX325wUXWLV/n0WADy94g2btLHPnvbaug4aaZx0o4+7pUO09zQimHYRijKMPreYebK6GaJMZqaJ1GHr6KF3BvKCjtnmNYrXsnIh2ghAGeYa93ZMJ2aa7Ssjv4DrwIAhF1PEdm56nWUwWbhya2KVhMwYja6CX8vGCJ36+pyX0SEVG8blEdCsu6o5HVTA8UyVChsOpEI4l/n0WiLWOhBYJ3ItaJ0QQ341C18hOceipP7QSmvtH+LQMJFhY+VZkhX5gvKQIDAQABAoIBAF5Idg+SJzFIr/ekG3KFbYV0jHPWwVTUx3J/GOLad7l8KM/owdN4ybWPMkdJva/J4bTdg61uEnZwG7IFXu6qFoqKRPC7nx66UCErPg0kw6GS38L6iQRis5xEivr6dYkC1/A37vhqwToP5pwYuMZCgIVr5XJMeD7me/PPC4PZxah+GUAboLSwWP5WWEBefyexCBA3kPa8klVmIBQoD9yfUtZx+QFq0e/QuK73M3EcIfgFcB6c81SzC4yq/oTA8D5hofNY5szBQujKyQdVY1zdRtMd1w0DytMKREt6WpaE4aqH6AjxGUE7GA9AMOPqZ/TANzL8nos7+IOFA9PdWT5/HGECgYEA1odCneMXGzYoWvoL6R7Us3RhTBs4q7wk2vbkTyc66R/VMFfZCyo18Z4PHefN7DlOaNDU5hOymoXcPPLIjzsz6ifww9Bw70wEQ+u3AxU53AH0NQcuPeLIXcMAN4zWIgiEqNi9uz0WQ4b+RzrEL/I6MqEB9LVK2P0EpfjSPAdoEu8CgYEAwGE1IsUltINkovMEud5YSEkU6GGAFWwD10sw+4/gVEm5w6g1i/u/vLig9M0BPwBnfTtMDYLAopGexZfsouarGO8nX8Myj6Semdut3YmNNa1vEkw2w0RWNjeisLFDR+idvLx5xLcw7VppD/hqzRwh2C0RslGNGG4mwlrzzKHsf2cCgYEAhsMnTOmH7VcjGKt06fetJCIasOV0vyUMfeSUXUjGkAWoNZspxAK7KlHhKycfy6HgKKXu560+CCXIyRy2cot9PD3k2A1LtHcrQsODDtO5qgQsNVeSa9vXhFbn/v1g0rZJJ4wn+8QPBVJ6z6IR9hCTEJTmFqQAJbkjv2NEJeN9NE8CgYEAtBGI6e28uDUQWpG7x7o9yhNV1ZmFiQecpMVFqQHnyR1lGqV00X0n4B600c6drvnS5F4/dpn/c2t4QJ1Oqr/cQK+BnFoaFmfQ6FS+bhGVMjwPLgJWc/mf9Imo51hUkJdEJegI1j9eNZydoIw2c5w0daLh4JYCym44K010zAJ4WlsCgYA2wtvuMBzmi9NyOjdmp8UG9QagBKYHGicZCWG53NBKZNWJPe5AxA2Q5WIdL0bffhhjbzELVJl3atAwfe5Opq+AqpZ32LNT3LwfC02wr2AlekS/TZ0MgRGYwbgbExoBrslFSnMJZcJ/WbmaO0VlL4QD/53B5EyAzFoUmCXCXj2RFA==',
			'format'			=> 'json',
			'charset'			=> 'UTF-8',
			'signType'			=> 'RSA',
			'alipayrsaPublicKey'=> 'MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAoTbnvcwbmDs2c3GRHcSwZz9U2uKXuMKv2EoHLFGcNmvYHMH5q26kWbiO7K8U2WsJjmUnPWgkJrP6dQbK3FAvX325wUXWLV/n0WADy94g2btLHPnvbaug4aaZx0o4+7pUO09zQimHYRijKMPreYebK6GaJMZqaJ1GHr6KF3BvKCjtnmNYrXsnIh2ghAGeYa93ZMJ2aa7Ssjv4DrwIAhF1PEdm56nWUwWbhya2KVhMwYja6CX8vGCJ36+pyX0SEVG8blEdCsu6o5HVTA8UyVChsOpEI4l/n0WiLWOhBYJ3ItaJ0QQ341C18hOceipP7QSmvtH+LQMJFhY+VZkhX5gvKQIDAQAB',
			'notify_url' 		=> 'http://cloudhouse.youjiawang.com/Api/Alipay/alipay_notify',
		),
	),

	//极光推送参数 (别装了客户端)
	'JPUSH_CONFIG' => array(
        'app_key' => '2220d98b2cd2883fa13a76cb',
        'master_secret' => '6dfb178f322c7962d4df5996',
    ),
	//极光推送参数 (管家端)
	'JPUSH_CONFIG_butler' => array(
		'app_key' => 'e56a16e83165eda18ee98fdd',
		'master_secret' => '9d776f939a7ef0a631426013',
	),
	'WEIXINPAY_CONFIG'       => array(
        'APPID'              => '', // 微信支付APPID
        'MCHID'              => '', // 微信支付MCHID 商户收款账号
        'KEY'                => '', // 微信支付KEY
        'APPSECRET'          => '',  //公众帐号secert
        'NOTIFY_URL'         => 'http://www.biezhuangle.com/Api/WeixPay/notify/order_number/', // 接收支付状态的连接
	),
 
);
