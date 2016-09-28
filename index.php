<?php
/***********************************************************
Filename: {weburl}/index.php
Note	: 入口文件
Version : 4.0
Web		: www.gzwebcreate.com
Author  : ryante <ryante@163.com>
Update  : 2014-10-19 13:03
 ***********************************************************/
/**
 * 定义常量，所有PHP文件仅允许从这里入口
 */
define("PHPOK_SET",true);
/**
 * 定义APP_ID，不同APP_ID调用不同的文件
 */
define("APP_ID","www");

/**
 * 定义根目录，如果此项出错，请将定义改成
 *     define("ROOT","./");
 */
define("ROOT",str_replace("\\","/",dirname(__FILE__))."/");

/**
 * 定义框架目录
 */

define("FRAMEWORK",ROOT."framework/");

/**
 * 检测是否已安装，如未安装跳转到安装页面，建议您在安装成功后去除这个判断。
 */
if(!file_exists(ROOT."data/install.lock")){
	header("Location:phpokinstall.php");
	exit;
}

/**
 * 引入初始化文件
 */
require(FRAMEWORK.'init.php');
?>
