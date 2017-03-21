<?php

/**
 * Created by PhpStorm.
 * User: Peter
 * Date: 2016/7/6
 * Time: 16:18
 */
class WechatSDK
{
	const GET_AUTH_CODE_URL = "https://open.weixin.qq.com/connect/qrconnect";
	const GET_ACCESS_TOKEN_URL = "https://api.weixin.qq.com/sns/oauth2/access_token";
	const GET_USER_INFO_URL = "https://api.weixin.qq.com/sns/userinfo";

	private $config;

	public function __construct()
	{
		$CI =& get_instance();
		$CI->config->load('auth');
		$this->config = $CI->config->item('wechat');
	}

	public function login($param)
	{
		$state = md5(uniqid(rand(), TRUE));
		$redirect_uri = $this->config['callback'];
		if ($param)
		{
			$redirect_uri = $redirect_uri . '?' . http_build_query($param);
		}
		$params = array(
			'appid'			=> $this->config['app_id'],
			'redirect_uri'	=> $redirect_uri,
			'response_type'	=> 'code',
			'scope'			=> 'snsapi_login',
			'state'			=> $state,
		);

		$url = self::GET_AUTH_CODE_URL . '?' . http_build_query($params);

		header("Location: {$url}");
	}

	public function get_user_info($code)
	{
		$token = $this->_get_token($code);
		$params = array(
			'access_token'	=> $token['access_token'],
			'openid'		=> $token['openid'],
			'lang'			=> 'zh_CN',
		);
		$url = self::GET_USER_INFO_URL . '?' . http_build_query($params);

		$user_info = $this->_get_contents($url);
		$result = json_decode($user_info, TRUE);
		if (isset($result['errmsg']))
		{
			die($result['errmsg']);
		}

		return $result;
	}

	private function _get_token($code)
	{
		$params = array(
			'appid'			=> $this->config['app_id'],
			'secret'		=> $this->config['app_secret'],
			'code'			=> $code,
			'grant_type'	=> 'authorization_code',
		);
		$url = self::GET_ACCESS_TOKEN_URL . '?' . http_build_query($params);

		$token = json_decode($this->_get_contents($url), TRUE);
		if (isset($token['errmsg']))
		{
			die($token['errmsg']);
		}

		$result['access_token'] = $token['access_token'];
		$result['openid'] = $token['openid'];

		return $result;
	}

	private function _get_contents($url)
	{
		if (ini_get("allow_url_fopen") == "1") {
			$response = file_get_contents($url);
		}else{
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
			curl_setopt($ch, CURLOPT_URL, $url);
			$response =  curl_exec($ch);
			curl_close($ch);
		}

		return $response;
	}
}