<?php
header('content-type:text/html;charset=utf-8');
date_default_timezone_set("PRC");
// 应用入口文件

// 检测PHP环境
if(version_compare(PHP_VERSION,'5.3.0','<'))  die('require PHP > 5.3.0 !');

// 开启调试模式 建议开发阶段开启 部署阶段注释或者设为false
define('APP_DEBUG',True);

// 定义应用目录
define('APP_PATH','./Application/');

define('SITE_PATH', dirname(__FILE__).'/');

//定义上传文件目录
//define('UPLOAD_PATH', SITE_PATH.'../../php/portal_v2/data/uploads');

// 引入ThinkPHP入口文件
require '../ThinkPHP/ThinkPHP.php';
