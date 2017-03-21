<?php

/**
 * Created by PhpStorm.
 * User: Peter
 * Date: 2016/7/6
 * Time: 16:18
 */
class WeiboSDK
{
	const GET_AUTH_CODE_URL = 'https://api.weibo.com/oauth2/authorize';
	const GET_ACCESS_TOKEN_URL = 'https://api.weibo.com/oauth2/access_token';
	const GET_USER_INFO_URL = 'https://api.weibo.com/2/users/show.json';

	private $config;

	public function __construct()
	{
		$CI =& get_instance();
		$CI->config->load('auth');
		$this->config = $CI->config->item('weibo');
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
			'client_id' => $this->config['app_key'],
			'redirect_uri' => $redirect_uri,
			'state' => $state,
		);

		$url = self::GET_AUTH_CODE_URL . '?' . http_build_query($params);

		header("Location: {$url}");
	}

	public function get_user_info($code)
	{
		$token = $this->_get_token($code);

		$params = array(
			'access_token' => $token['access_token'],
			'uid' => $token['uid'],
		);
		$url = self::GET_USER_INFO_URL . '?' . http_build_query($params);

		$user_info = $this->_get_contents($url);
		$result = json_decode($user_info, TRUE);
		if (isset($result['error']))
		{
			die($result['error']);
		}

		return $result;
	}

	private function _get_token($code)
	{
		$params = array(
			'client_id' => $this->config['app_key'],
			'client_secret' => $this->config['app_secret'],
			'grant_type' => 'authorization_code',
			'code' => $code,
			'redirect_uri' => $this->config['callback'],
		);
		$body = http_build_query($params);

		$url = self::GET_ACCESS_TOKEN_URL;

		$token = json_decode($this->_get_contents($url, 'post', $body), TRUE);
		if (isset($token['error']))
		{
			die($token['error']);
		}

		$result['access_token'] = $token['access_token'];
		$result['uid'] = $token['uid'];

		return $result;
	}

	private function _get_contents($url, $method = 'get', $postfields = NULL)
	{
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		if (strtolower($method) === 'post')
		{
			curl_setopt($ch, CURLOPT_POST, TRUE);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $postfields);
		}
		curl_setopt($ch, CURLOPT_URL, $url);
		$response = curl_exec($ch);
		curl_close($ch);

		return $response;
	}
}