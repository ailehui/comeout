<?php


# Tag
define('BOCTAG','0.9');

# 数据库
define('DB_TYPE'   , 'mysqli');
define('DB_HOST'   , '127.0.0.1');
define('DB_USER'   , 'root');
define('DB_PASS'   , 'root');
define('DB_NAME'   , 'hq_clb');
define('DB_PREFIX' , 'boc_');

# 全局URL路径
// 主域名 保留最后的 /
define('GLOBAL_URL'  , 'http://'.$_SERVER['HTTP_HOST'].'/clb/out/');
//define('GLOBAL_URL'  , 'http://localhost:9003/');
// 提供给后台做链接用的
define('STATIC_URL'  , GLOBAL_URL.'static/');
define('UPLOAD_URL'  , GLOBAL_URL.'upload/');
// 对应APP
define('SITE_URL'  ,   GLOBAL_URL.'index.php/');
define('ADMINER_URL' , GLOBAL_URL.'houtai/');
define('MOBILE_URL'  , GLOBAL_URL.'mobile/');

// define('GLOBAL_URL'  , 'http://localhost:9000/');
// define('STATIC_URL'  , 'http://localhost:9001/');
// define('UPLOAD_URL'  , 'http://localhost:9002/');
// define('ADMINER_URL' , 'http://localhost:9003/');
// define('MOBILE_URL'  , 'http://localhost:9004/');

// // 快捷提供给JS
define('IMG_URL'     , STATIC_URL.'img/');

# 引用绝对路径PATH定义
define('ROOT'        , __DIR__.'/');
define('LIBS_PATH'   , ROOT.'boc/libs/');
define('CI_PATH'     , ROOT.'boc/libs/ci/');
define('STATIC_PATH' , ROOT.'out/static/');
define('UPLOAD_PATH' , ROOT.'out/upload/');
define('SITE_PATH'   , ROOT.'boc/site');
define('ADMIN_PATH'  , ROOT.'boc/bocadmin');

# 可忽略 当css|js改变时替换本地缓存,将false 替换为 'v[1,2...]'
define('STATIC_V','v3');

# 密钥设置;设置多个 用于 md5/sha1(hmac.value.time) 外部数据输入输出
# 提供给 app 的config 的 encryption_key
define('HMACPWD','SA1S2D3F4G5H6J7K8L9'); // PASSWD and cookie
define('HMAC','SA1S2D3F4G5H6J7K8L8');    // 提供第三方API验证使用

define('KEY','BOCCLB');                  // 提供移动接口解密加密API验证使用
define('DUMP_FLG', FALSE);               // 提供移动接口解密加密API验证使用

define('CLIENT_JPUSH_APPKEY','3163c03b831728f00357e59c'); // 极光帐号
define('CLIENT_JPUSH_MASTERSSCRET','3784f76ab64016e78974b700');    // 极光密码


define('REG_SMS_TEXT', '亲爱的用户欢迎您注册出来吧，以下是您的短信验证码，请及时输入:');// 注册
define('LOGIN_SMS_TEXT', '亲爱的用户您正在登录出来吧，以下是您的短信验证码，请及时输入:');// 登录
define('FORGET_PWD_SMS_TEXT', '亲爱的用户您正在找回密码，以下是您的短信验证码，请及时输入:');// 登录


define('MININTERVAL',60);// 发送短信验证码间隔
define('EXPIRE',8600);//短信验证码有效期
define('SMS_ACCOUNT','shiyuan_yilianwang');//短信接口帐号
define('SMS_PASSWORD','Yilianwang666');//短信接口密码

/*
 * 开发模式
 * 配置项目运行的环境，该配置会影响错误报告的显示和配置文件的读取。
 * development
 * testing
 * production
 * 使用 error_reporting();
 */
define('ENVIRONMENT', 'development');
// 有些服务器不支持调试，需要开启错误调试
// error_reporting(E_ALL);
// ini_set("display_errors", 1);
// ini_set("error_reporting", 1);

// PHP 5 尝试加载未定义的类
// 挂载本地库 其他 core Controller
// 使用第三方报错工具可能会出现未加载的现象出现使
 function BocLoader($class)
 {
 	if(strpos($class, 'CI_') !== 0)
 	{
 		if (file_exists(APPPATH . 'core/'. $class . EXT)) {
 			@include_once( APPPATH . 'core/'. $class . EXT );
 		}elseif(file_exists(LIBS_PATH . 'core/'. $class . EXT)) {
 			@include_once( LIBS_PATH . 'core/'. $class . EXT );
 		}
 	}
 }
//注册自动加载,解决与其他自动加载第三方插件冲突
spl_autoload_register('BocLoader');

function v($str){
	echo "<pre>";
	print_r($str);
	echo "</pre>" ;
}