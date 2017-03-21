<?php
defined('BASEPATH') OR exit('No direct script access allowed');


//QQ第三方登录
$config['qq'] = array(
	'app_id'	=> '101319223',
	'app_key'	=> '6ccaddbf6b3493291087c4d5876333d9',
	'callback'	=> 'http://apd.91lianche.com.cn/backend/auth/qq_callback',
	'scope'		=> 'get_user_info',
);

//新浪微博第三方登录
$config['weibo'] = array(
	'app_key'		=> '948275236',
	'app_secret'	=> 'f167f1568a1c98fb2975e3f3e2886a66',
	'callback'		=> 'http://apd.91lianche.com.cn/backend/auth/weibo_callback',
);

//微信第三方登录
$config['wechat'] = array(
	'app_id'		=> 'wx89ab6f7451c178f1',
	'app_secret'	=> '1b9a6007a6f71aaa26efc4a52ec59f1a',
	'callback'		=> 'http://apd.91lianche.com.cn/backend/auth/wechat_callback',
);
