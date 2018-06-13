<?php


const APP_ADDON_PATH = './App/Addons/';

function isMobile(){
    //return true;
    // 如果有HTTP_X_WAP_PROFILE则一定是移动设备
    if (isset($_SERVER['HTTP_X_WAP_PROFILE'])) {
        return true;
    }
    // 如果via信息含有wap则一定是移动设备,部分服务商会屏蔽该信息
    if (isset($_SERVER['HTTP_VIA'])) {
        // 找不到为flase,否则为true
        return stristr($_SERVER['HTTP_VIA'], "wap") ? true : false;
    }
    // 脑残法，判断手机发送的客户端标志,兼容性有待提高
    if (isset($_SERVER['HTTP_USER_AGENT'])) {
        $clientkeywords = array('nokia', 'sony', 'ericsson', 'mot', 'samsung', 'htc', 'sgh', 'lg', 'sharp', 'sie-', 'philips', 'panasonic', 'alcatel', 'lenovo', 'iphone', 'ipod', 'blackberry', 'meizu', 'android', 'netfront', 'symbian', 'ucweb', 'windowsce', 'palm', 'operamini', 'operamobi', 'openwave', 'nexusone', 'cldc', 'midp', 'wap', 'mobile');
        // 从HTTP_USER_AGENT中查找手机浏览器的关键字
        if (preg_match("/(" . implode('|', $clientkeywords) . ")/i", strtolower($_SERVER['HTTP_USER_AGENT']))) {
            return true;
        }
    }
    // 协议法，因为有可能不准确，放到最后判断
    if (isset($_SERVER['HTTP_ACCEPT'])) {
        // 如果只支持wml并且不支持html那一定是移动设备
        // 如果支持wml和html但是wml在html之前则是移动设备
        if (strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') !== false && (strpos($_SERVER['HTTP_ACCEPT'], 'text/html') === false || strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') < strpos($_SERVER['HTTP_ACCEPT'], 'text/html'))) {
            return true;
        }
    }
    return false;
}

/**
 * 检测用户是否登录
 * @return integer 0-未登录，大于0-当前登录用户ID
 */
function is_login(){
    $user = session(C('AUTH_KEY'));
    if (empty($user)) {
        return 0;
    } else {
        return session(C('AUTH_KEY'));
    }
}

/**
 * 检测当前用户是否为管理员
 * @return boolean true-管理员，false-非管理员
 */
function is_admin($uid = null){
    $uid = is_null($uid) ? is_login() : $uid;
	if(in_array($uid,C('AUTH_ADMIN' ))){
		return true;
	}else{
		return false;
	}
}

function Is_Auth($Auth_Rule,$isweb=false){
    //die('123');
	$Auth = new \Common\Libs\Auth();
    if ($isweb) {
        $Auth = new \Common\Libs\WebAuth();
        if(M('menus')->where(array('name'=>$Auth_Rule))->getField('is_auth')==0){
            return true;
        }
    }
	$AUTH_KEY=session(C('AUTH_KEY'));
	//判断当前认证key是否不在 超级管理组配置中,或者当前模块是否为非认证模块
	if (! is_admin($AUTH_KEY) && ! in_array ( CONTROLLER_NAME, explode ( ",", C ( "NOT_AUTH_MODULE" ) ) )) {
		//当前权限表达式
        $Auth_Rule = MODULE_NAME . '/' . CONTROLLER_NAME . '/' . ACTION_NAME;
		//$Auth_Rule = MODULE_NAME . '/' . CONTROLLER_NAME . '/index';
        //die($Auth_Rule);
		if (! $Auth->check ($Auth_Rule,$AUTH_KEY)) {
			return false;
		}else{
			return true;
		}
	}else{
		return true;
	}
}



/**
 * 根据用户ID获取用户昵称
 * @param  integer $uid 用户ID
 * @return string       用户昵称
 */
function get_nickname($uid = 0){
    static $list;
    if(!($uid && is_numeric($uid))){ //获取当前登录用户名
        return session('userinfo.nickname');
    }
    /* 获取缓存数据 */
    if(empty($list)){
        $list = S('sys_user_nickname_list');
    }
    /* 查找用户信息 */
    $key = "u{$uid}";
    if(isset($list[$key])){ //已缓存，直接使用
        $name = $list[$key];
    } else { //调用接口获取用户信息
        $info = M('User')->field('nickname')->find($uid);
        if($info !== false && $info['nickname'] ){
            $nickname = $info['nickname'];
            $name = $list[$key] = $nickname;
            /* 缓存用户 */
            $count = count($list);
            $max   = C('USER_MAX_CACHE');
            while ($count-- > $max) {
                array_shift($list);
            }
            S('sys_user_nickname_list', $list);
        } else {
            $name = '';
        }
    }
    return $name;
}


/**
 * 根据模块的标识和版本判断是否可以安装模块
 * @param  string $name 模块名称
 * @param  string $module_version 模块版本
 * @return integer       模块比较结果
 * 1 不存在次模块
 * 2 当前版本低于需求版本
 * 3 需求模块未启用
 * 9 模块比较正常
 */
function validate_module($name, $module_version){
	//设置模块路径
	$path = APP_PATH . $name;
	//判断是否存在相应模块的配置文件
	if (file_exists($path . DIRECTORY_SEPARATOR . 'Install' . DIRECTORY_SEPARATOR . 'Config.inc.php')) {
		//读取配置文件
        @include ($path . DIRECTORY_SEPARATOR . 'Install' . DIRECTORY_SEPARATOR . 'Config.inc.php');
		//判断模块的已安装模块的版本和当前需求版本的比较，如果已安装模块的版本低于当前需求模块的版本
		if (!version_compare($version,$module_version,'<')) {
			return 2;
		}else{
			$module_info=M('Module')->where(array('module'=>$name))->find();
			if($module_info['version']){
				return 9;
			}else{
				return 3;
			}
		}
	}else{
		return 1;
	}
}


function del_AuthRule($AuthRule_arr){
	foreach($AuthRule_arr as $AuthRule){
		M("AuthRule")->where(array("name"=>$AuthRule))->delete();
	}
}

function check_verify($code, $id = 1){
	$verify = new \Think\Verify();
	return $verify->check($code, $id);
}

function list_to_tree($list, $pk='id', $pid = 'pid', $child = '_child', $root = 0) {
	// 创建Tree
	$tree = array();
	if(is_array($list)) {
		// 创建基于主键的数组引用
		$refer = array();
		foreach ($list as $key => $data) {
			$refer[$data[$pk]] =& $list[$key];
		}
		foreach ($list as $key => $data) {
			// 判断是否存在parent
			$parentId =  $data[$pid];
			if ($root == $parentId) {
				$tree[] =& $list[$key];
			}else{
				if (isset($refer[$parentId])) {
					$parent =& $refer[$parentId];
					$parent[$child][] =& $list[$key];
				}
			}
		}
	}
	return $tree;
}

/**
 * select返回的数组进行整数映射转换
 *
 * @param array $map  映射关系二维数组  array(
 *                                          '字段名1'=>array(映射关系数组),
 *                                          '字段名2'=>array(映射关系数组),
 *                                           ......
 *                                       )
 * @author 朱亚杰 <zhuyajie@topthink.net>
 * @return array
 *
 *  array(
 *      array('id'=>1,'title'=>'标题','status'=>'1','status_text'=>'正常')
 *      ....
 *  )
 *
 */
function int_to_string(&$data,$map=array('status'=>array(1=>'正常',-1=>'删除',0=>'禁用',2=>'未审核',3=>'草稿'))) {
    if($data === false || $data === null ){
        return $data;
    }
    $data = (array)$data;
    foreach ($data as $key => $row){
        foreach ($map as $col=>$pair){
            if(isset($row[$col]) && isset($pair[$row[$col]])){
                $data[$key][$col.'_text'] = $pair[$row[$col]];
            }
        }
    }
    return $data;
}

/**
* 对查询结果集进行排序
* @access public
* @param array $list 查询结果
* @param string $field 排序的字段名
* @param array $sortby 排序类型
* asc正向排序 desc逆向排序 nat自然排序
* @return array
*/
function list_sort_by($list,$field, $sortby='asc') {
   if(is_array($list)){
       $refer = $resultSet = array();
       foreach ($list as $i => $data)
           $refer[$i] = &$data[$field];
       switch ($sortby) {
           case 'asc': // 正向排序
                asort($refer);
                break;
           case 'desc':// 逆向排序
                arsort($refer);
                break;
           case 'nat': // 自然排序
                natcasesort($refer);
                break;
       }
       foreach ( $refer as $key=> $val)
           $resultSet[] = &$list[$key];
       return $resultSet;
   }
   return false;
}
/**
 * 字符串转换为数组，主要用于把分隔符调整到第二个参数
 * @param  string $str  要分割的字符串
 * @param  string $glue 分割符
 * @return array
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
function str2arr($str, $glue = ','){
    return explode($glue, $str);
}

/**
 * 数组转换为字符串，主要用于把分隔符调整到第二个参数
 * @param  array  $arr  要连接的数组
 * @param  string $glue 分割符
 * @return string
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
function arr2str($arr, $glue = ','){
    return implode($glue, $arr);
}
/**
 * 获取行为数据
 * @param string $id 行为id
 * @param string $field 需要获取的字段
 */
function get_action($id = null, $field = null){
    if(empty($id) && !is_numeric($id)){
        return false;
    }
    $list = S('action_list');
    if(empty($list[$id])){
		D('Action')->cache();
		$list = S('action_list');
    }
    return empty($field) ? $list[$id]['title'] : $list[$id][$field];
}

/**
 * 记录行为日志，并执行该行为的规则
 * @param string $action 行为标识
 * @param string $model 触发行为的模型名
 * @param int $record_id 触发行为的记录id
 * @param int $user_id 执行行为的用户id
 * @return boolean
 * @author huajie <banhuajie@163.com>
 */
function action_log($action = null, $model = null, $record_id = null, $user_id = null){

    //参数检查
    if(empty($action) || empty($model) || empty($record_id)){
        return '参数不能为空';
    }
    if(empty($user_id)){
        $user_id = is_login();
    }

    //查询行为,判断是否执行
    $action_info = M('Action')->where(array('name'=>$action))->find();
    if($action_info['status'] != 1){
        return '该行为被禁用或删除';
    }

    //插入行为日志
    $data['action_id']      =   $action_info['id'];
    $data['user_id']        =   $user_id;
    $data['action_ip']      =   get_client_ip();
    $data['model']          =   $model;
    $data['record_id']      =   $record_id;
    $data['create_time']    =   NOW_TIME;

    //解析日志规则,生成日志备注
    if(!empty($action_info['log'])){
        if(preg_match_all('/\[(\S+?)\]/', $action_info['log'], $match)){
            $log['user']    =   $user_id;
            $log['record']  =   $record_id;
            $log['model']   =   $model;
            $log['time']    =   NOW_TIME;
            $log['data']    =   array('user'=>$user_id,'model'=>$model,'record'=>$record_id,'time'=>NOW_TIME);
            foreach ($match[1] as $value){
                $param = explode('|', $value);
                if(isset($param[1])){
                    $replace[] = call_user_func($param[1],$log[$param[0]]);
                }else{
                    $replace[] = $log[$param[0]];
                }
            }
            $data['remark'] =   str_replace($match[0], $replace, $action_info['log']);
        }else{
            $data['remark'] =   $action_info['log'];
        }
    }else{
        //未定义日志规则，记录操作url
        $data['remark']     =   '操作url：'.$_SERVER['REQUEST_URI'];
    }

    M('ActionLog')->add($data);

    if(!empty($action_info['rule'])){
        //解析行为
        $rules = parse_action($action, $user_id);

        //执行行为
        $res = execute_action($rules, $action_info['id'], $user_id);
    }
}

/**
 * 根据ID和PID返回一个树形结构
 */
function list_to_tree2($list, $pk='id', $pid = 'pid', $child = '_child', $root = 0) {
	// 创建Tree
	$tree = array();
	if(is_array($list)) {
		// 创建基于主键的数组引用
		$refer = array();
		foreach ($list as $key => $data) {
			$refer[$data[$pk]] =& $list[$key];
		}
		foreach ($list as $key => $data) {
			// 判断是否存在parent
			$parentId =  $data[$pid];
			if ($root == $parentId) {
				$tree[$data['id']] =& $list[$key];
			}else{
				if (isset($refer[$parentId])) {
					$parent =& $refer[$parentId];
					$parent[$child][$data['id']] =& $list[$key];
				}
			}
		}
	}
	return $tree;
}
/**
 * 解析模型中选项字段的分解
 */
function model_field_attr($str,$estr1='|',$estr2=':') {
	$arr1=array();
	$arr1 = explode($estr1,$str);
	if(count($arr1)>0){
		foreach ($arr1 as $arr1_one) {
			$arr2=array();
			$arr2 = explode($estr2,$arr1_one);
			if(count($arr2)>0){
				$strarr[$arr2[0]]=$arr2[1];
			}
		}
	}
	return $strarr;
}
/**
 * 处理插件钩子
 * @param string $hook   钩子名称
 * @param mixed $params 传入参数
 * @return void
 */
function hook($hook,$params=array()){
    \Think\Hook::listen($hook,$params);
}

/**
 * 获取插件类的类名
 * @param strng $name 插件名
 */
function get_addon_class($name){
    $class = "Addons\\{$name}\\{$name}Addon";
    return $class;
}
/**
 * 获取插件类的配置文件数组
 * @param string $name 插件名
 */
function get_addon_config($name){
    $class = get_addon_class($name);
    if(class_exists($class)) {
        $addon = new $class();
        return $addon->getConfig();
    }else {
        return array();
    }
}

/**
 * 插件显示内容里生成访问插件的url
 * @param string $url url
 * @param array $param 参数
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
function addons_url($url, $param = array()){
    $url        = parse_url($url);
    $case       = C('URL_CASE_INSENSITIVE');
    $addons     = $case ? parse_name($url['scheme']) : $url['scheme'];
    $controller = $case ? parse_name($url['host']) : $url['host'];
    $action     = trim($case ? strtolower($url['path']) : $url['path'], '/');

    /* 解析URL带的参数 */
    if(isset($url['query'])){
        parse_str($url['query'], $query);
        $param = array_merge($query, $param);
    }

    /* 基础参数 */
    $params = array(
        '_addons'     => $addons,
        '_controller' => $controller,
        '_action'     => $action,
    );
    $params = array_merge($params, $param); //添加额外参数

    return U('Addons/execute', $params);
}
/*文件夹操作*/

/**
    * 建立文件夹
    *
    * @param string $aimUrl
    * @return viod
    */
function createDir($aimUrl) {
    $aimUrl = str_replace('', '/', $aimUrl);
    $aimDir = '';
    $arr = explode('/', $aimUrl);
    $result = true;
    foreach ($arr as $str) {
        $aimDir .= $str . '/';
        if (!file_exists($aimDir)) {
            $result = mkdir($aimDir);
        }
    }
    return $result;
}

/**
    * 建立文件
    *
    * @param string $aimUrl
    * @param boolean $overWrite 该参数控制是否覆盖原文件
    * @return boolean
    */
function createFile($aimUrl, $overWrite = false) {
    if (file_exists($aimUrl) && $overWrite == false) {
        return false;
    } elseif (file_exists($aimUrl) && $overWrite == true) {
        unlinkFile($aimUrl);
    }
    $aimDir = dirname($aimUrl);
    createDir($aimDir);
    touch($aimUrl);
    return true;
}

/**
    * 移动文件夹
    *
    * @param string $oldDir
    * @param string $aimDir
    * @param boolean $overWrite 该参数控制是否覆盖原文件
    * @return boolean
    */
function moveDir($oldDir, $aimDir, $overWrite = false) {
    $aimDir = str_replace('', '/', $aimDir);
    $aimDir = substr($aimDir, -1) == '/' ? $aimDir : $aimDir . '/';
    $oldDir = str_replace('', '/', $oldDir);
    $oldDir = substr($oldDir, -1) == '/' ? $oldDir : $oldDir . '/';
    if (!is_dir($oldDir)) {
        return false;
    }
    if (!file_exists($aimDir)) {
        createDir($aimDir);
    }
    @ $dirHandle = opendir($oldDir);
    if (!$dirHandle) {
        return false;
    }
    while (false !== ($file = readdir($dirHandle))) {
        if ($file == '.' || $file == '..') {
            continue;
        }
        if (!is_dir($oldDir . $file)) {
            moveFile($oldDir . $file, $aimDir . $file, $overWrite);
        } else {
            moveDir($oldDir . $file, $aimDir . $file, $overWrite);
        }
    }
    closedir($dirHandle);
    return rmdir($oldDir);
}

/**
    * 移动文件
    *
    * @param string $fileUrl
    * @param string $aimUrl
    * @param boolean $overWrite 该参数控制是否覆盖原文件
    * @return boolean
    */
function moveFile($fileUrl, $aimUrl, $overWrite = false) {
    if (!file_exists($fileUrl)) {
        return false;
    }
    if (file_exists($aimUrl) && $overWrite = false) {
        return false;
    } elseif (file_exists($aimUrl) && $overWrite = true) {
        unlinkFile($aimUrl);
    }
    $aimDir = dirname($aimUrl);
    createDir($aimDir);
    rename($fileUrl, $aimUrl);
    return true;
}

/**
    * 删除文件夹
    *
    * @param string $aimDir
    * @return boolean
    */
function unlinkDir($aimDir) {
    $aimDir = str_replace('', '/', $aimDir);
    $aimDir = substr($aimDir, -1) == '/' ? $aimDir : $aimDir . '/';
    if (!is_dir($aimDir)) {
        return false;
    }
    $dirHandle = opendir($aimDir);
    while (false !== ($file = readdir($dirHandle))) {
        if ($file == '.' || $file == '..') {
            continue;
        }
        if (!is_dir($aimDir . $file)) {
            unlinkFile($aimDir . $file);
        } else {
            unlinkDir($aimDir . $file);
        }
    }
    closedir($dirHandle);
    return rmdir($aimDir);
}

/**
    * 删除文件
    *
    * @param string $aimUrl
    * @return boolean
    */
function unlinkFile($aimUrl) {
    if (file_exists($aimUrl)) {
        unlink($aimUrl);
        return true;
    } else {
        return false;
    }
}

/**
    * 复制文件夹
    *
    * @param string $oldDir
    * @param string $aimDir
    * @param boolean $overWrite 该参数控制是否覆盖原文件
    * @return boolean
    */
function copyDir($oldDir, $aimDir, $overWrite = false) {
    $aimDir = str_replace('', '/', $aimDir);
    $aimDir = substr($aimDir, -1) == '/' ? $aimDir : $aimDir . '/';
    $oldDir = str_replace('', '/', $oldDir);
    $oldDir = substr($oldDir, -1) == '/' ? $oldDir : $oldDir . '/';
    if (!is_dir($oldDir)) {
        return false;
    }
    if (!file_exists($aimDir)) {
        createDir($aimDir);
    }
    $dirHandle = opendir($oldDir);
    while (false !== ($file = readdir($dirHandle))) {
        if ($file == '.' || $file == '..') {
            continue;
        }
        if (!is_dir($oldDir . $file)) {
            copyFile($oldDir . $file, $aimDir . $file, $overWrite);
        } else {
            copyDir($oldDir . $file, $aimDir . $file, $overWrite);
        }
    }
    return closedir($dirHandle);
}

/**
    * 复制文件
    *
    * @param string $fileUrl
    * @param string $aimUrl
    * @param boolean $overWrite 该参数控制是否覆盖原文件
    * @return boolean
    */
function copyFile($fileUrl, $aimUrl, $overWrite = false) {
    if (!file_exists($fileUrl)) {
        return false;
    }
    if (file_exists($aimUrl) && $overWrite == false) {
        return false;
    } elseif (file_exists($aimUrl) && $overWrite == true) {
        unlinkFile($aimUrl);
    }
    $aimDir = dirname($aimUrl);
    createDir($aimDir);
    copy($fileUrl, $aimUrl);
    return true;
}

function is_weixin() {
    if (strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false) {
        return true;
    } return false;
}



//curl post请求
/*
参数1：post数组数据
参数2：接口地址
    */
function Post($curlPost,$url){
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_HEADER, false);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_NOBODY, true);
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $curlPost);
    $return_str = curl_exec($curl);
    curl_close($curl);
    return $return_str;
}

//curl get请求
/*
参数1：接口地址
    */
function curl_get($url){
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_TIMEOUT, 10);
    curl_setopt($curl, CURLOPT_HEADER, false);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    $return_str = curl_exec($curl);
    curl_close($curl);
    return $return_str;
}

/*
xml转数组
    */
function xml_to_array($xml){
    $reg = "/<(\w+)[^>]*>([\\x00-\\xFF]*)<\\/\\1>/";
    if(preg_match_all($reg, $xml, $matches)){
        $count = count($matches[0]);
        for($i = 0; $i < $count; $i++){
        $subxml= $matches[2][$i];
        $key = $matches[1][$i];
            if(preg_match( $reg, $subxml )){
                $arr[$key] = xml_to_array( $subxml );
            }else{
                $arr[$key] = $subxml;
            }
        }
    }
    return $arr;
}

//生成随机串
function random($length = 6 , $numeric = 0) {
        PHP_VERSION < '4.2.0' && mt_srand((double)microtime() * 1000000);
        if($numeric) {
            $hash = sprintf('%0'.$length.'d', mt_rand(0, pow(10, $length) - 1));
        } else {
            $hash = '';
            $chars = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789abcdefghjkmnpqrstuvwxyz';
            $max = strlen($chars) - 1;
            for($i = 0; $i < $length; $i++) {
                $hash .= $chars[mt_rand(0, $max)];
            }
        }
        return $hash;
}

function num_to_letter($num){
    $chars = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789abcdefghjkmnpqrstuvwxyz';
    //$chars[$num];
    return $chars[$num-1];
}

function num_to_chinese($num){
    $arr=array("零","一","二","三","四","五","六","七","八","九");
    return $arr[$num];
}

///////

/************************极光推送**********************/

/**     
    * 将数据先转换成json,然后转成array 
    */  
function json_array($result){  
    $result_json = json_encode($result);  
    return json_decode($result_json,true);  
}  

/**
*向所有设备推送消息
*@paramstring $message 需要推送的消息
*/
function sendNotifyAll($message){
    //require_once "JPush\JPush.php";
    vendor('JPush.JPush');
    $app_key = C('JPUSH_CONFIG.app_key');
    $master_secret = C('JPUSH_CONFIG.master_secret');
    $client = new\JPush($app_key,$master_secret);
    $result=$client->push()->setPlatform('all')->addAllAudience()->setNotificationAlert($message)->send();
    return  json_array($result);
}


/**
*向特定设备推送消息 客户端
*@paramarray $alias 特定的别名
*@paramstring $message 需要推送的消息
*/
function sendNotifySpecial($alias,$message){
    vendor('JPush.JPush');
    $app_key = C('JPUSH_CONFIG.app_key');
    $master_secret = C('JPUSH_CONFIG.master_secret');
    $client = new\JPush($app_key,$master_secret);

    //配置  setMessage 通过ID来绑定文章
    $result=$client->push()
        ->setPlatform('all')
        ->addAlias($alias)
        ->setNotificationAlert($message)
        //->setMessage('这个是推送消息', '这是标题', '', array('extras'=>'1'))
        //->setOptions(time(), 3600, null, true)
        ->send();
   
    //$result=$client->push()->setPlatform('all')->addAlias($alias)->setNotificationAlert($message)->send();
    return  json_array($result);
    

}

/**
 *向特定设备推送消息  管家端
 *@paramarray $alias 特定的别名
 *@paramstring $message 需要推送的消息
 */
function sendNotifySpecial_butler($alias,$message){
    vendor('JPush.JPush');
    $app_key = C('JPUSH_CONFIG_butler.app_key');
    $master_secret = C('JPUSH_CONFIG_butler.master_secret');
    $client = new\JPush($app_key,$master_secret);
    $result=$client->push()->setPlatform('all')->addAlias($alias)->setNotificationAlert($message)->send();
    return  json_array($result);
}


/**
 * 根据管家ID 获取管家手机号 用户极光推送
 */
function get_butler_phone($id){
    $phone = M('butler')->field('phone')->find($id);
    return $phone['phone'];
}

/**
*得到各类统计数据
*@paramarray$msgIds推送消息返回的msg_id列表
*/
function reportNotify($msgIds){ 
    vendor('JPush.JPush');
    $app_key = C('JPUSH_CONFIG.app_key');
    $master_secret = C('JPUSH_CONFIG.master_secret');
    $client = new\JPush($app_key,$master_secret);
    $response=$client->report()->getReceived($msgIds);
    return  json_array($response);
}

//照片上传
function base64toimg($base64_content,$path='./Uploads/1/image/'){
    if(!empty($base64_content)){
        if (preg_match('/^(data:\s*image\/(\w+);base64,)/', $base64_content, $result)){
            $path .= date('Y-m-d').'/';
            if (! file_exists($path)) {
				mkdir($path);
			}
            $type = $result[2];
            $time = time().mt_rand(100,999);
            $filename = "$time.{$type}";
            $filepath = $path.$filename;
            $content =  base64_decode(str_replace($result[1], '', $base64_content));
            if (file_put_contents($filepath,$content)>0) {
                return substr($path,1).$filename;
            }else {
                return '';
            }
        }
        return $base64_content;
    }
}

//验证装修结束时间
function verify_enddate($start_date,$end_date){
    $cost = 45;
    for ($i=0; $i < $cost; $i++) { 
        $cur_date = strtotime('+'.$i.' day',$start_date);
        if (date('w',$cur_date) == 1) {
            $cost++;
        }
    }
    if (strtotime('+'.$cost.' day',$start_date)>$end_date) {
        return true;
    }
    return false;
    return strtotime('+'.$cost.' day',$start_date);
    return date('Y-m-d H:i:s',strtotime('+'.$cost.' day',$start_date));
}

//计算装修结束时间
function count_enddate($deco_id){
    $cost = 45;
    $wh['id'] = $deco_id;
    $start_date = M('decorate')->where($wh)->getfield('start_date');
    for ($i=0; $i < $cost; $i++) { 
        $cur_date = strtotime('+'.$i.' day',$start_date);
        if (date('w',$cur_date) == 1) {
            $cost++;
        }
    }
    return strtotime('+'.$cost.' day',$start_date);
    return date('Y-m-d H:i:s',strtotime('+'.$cost.' day',$start_date));
}

//获取装修订单状态
function get_order_status($status){
    $arr = array(0=>'客户下单',1=>'申请合同',2=>'合同生效',3=>'待缴费',4=>'未开始',5=>'施工中',6=>'已完成',7=>'终止合同',8=>'订单驳回');
    return $arr[$status];
}

function set_order_status($id,$status){
    $wh['id'] = $id;
    M('decorate')->where($wh)->setField('status',$status);
    //同步更新管家订单状态
    if (in_array($status,array(5,6))) {
        $wh1['deco_id'] = $id;
        M('order')->where($wh)->setField('status',$status-4);
    }
}

//获取装修节点状态
function get_notes_status($status){
    $arr = array('未开始','未施工','施工中','待验收','已完成','代缴费','拒绝验收');
    return $arr[$status];
}

function set_notes_status($id,$status){
    $wh['id'] = $id;
    M('progress')->where($wh)->setField('status',$status);
}

//获取设计图房间名称
function get_design_room($room){
    $arr = array(1=>'客厅',2=>'卧室',3=>'餐厅',4=>'卫生间',5=>'其他');
    return $arr[$room];
}

//添加消息
function message($type,$obj_id,$content){
    $data['type'] = $type;
    $data['obj_id'] = $obj_id;
    $data['content'] = $content;
    $data['create_time'] = time();
    M('message')->add($data);
}

//计算综合评价
function count_grade($comp_id){
    $wh['comp_id'] = $comp_id;
    $count = M('grade')->where($wh)->count();
    $info = array('fuwu' => 0,'shigong' => 0,'sheji' => 0,'haoping' => 0);
    if ($count) {
        $info = M('grade')->field("round(avg(fuwu)) fuwu,round(avg(shigong)) shigong,round(avg(sheji)) sheji")->where($wh)->find();
        $wh['type'] = 1;
        $haoping = M('grade')->where($wh)->count()/$count*100;
        $info['haoping'] = round($haoping,2);
    }
    return $info;
}


/**
 * 通过IP获取对应城市信息(该功能基于淘宝第三方IP库接口)
 * @param $ip IP地址,如果不填写，则为当前客户端IP
 * @return  如果成功，则返回数组信息，否则返回false
 */
function getIpInfo($ip){
    if(empty($ip)) $ip=get_client_ip();  //get_client_ip()为tp自带函数，如没有，自己百度搜索。此处就不重复复制了
    $url='http://ip.taobao.com/service/getIpInfo.php?ip='.$ip;
    $result = file_get_contents($url);
    $result = json_decode($result,true);
    if($result['code']!==0 || !is_array($result['data'])) return false;
    return $result['data'];
}

 
