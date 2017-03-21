<?php
defined('BASEPATH') OR exit('No direct script access allowed');


//QQ第三方登录
$config['qq'] = array(
	'app_id'	=> '101323506',
	'app_key'	=> '8ebafb956d649413fcb50eeb061cadff',
	'callback'	=> 'http://apd.motouch.cn/backend/auth/qq_callback',
	'scope'		=> 'get_user_info',
);

//新浪微博第三方登录
$config['weibo'] = array(
	'app_key'		=> '733537784',
	'app_secret'	=> '614e3934c28cabec34e65eea0e582a41',
	'callback'		=> 'http://apd.motouch.cn/backend/auth/weibo_callback',
);

//微信第三方登录
$config['wechat'] = array(
	'app_id'		=> 'wx68430591b511d7fb',
	'app_secret'	=> 'f3eaa9c16f1d457f566b5cadebef6841',
	'callback'		=> 'http://apd.motouch.cn/backend/auth/wechat_callback',
);
