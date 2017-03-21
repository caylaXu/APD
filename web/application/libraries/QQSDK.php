<?php

/**
 * Created by PhpStorm.
 * User: Peter
 * Date: 2016/7/6
 * Time: 16:16
 */
class QQSDK
{
	const GET_AUTH_CODE_URL = "https://graph.qq.com/oauth2.0/authorize";
	const GET_ACCESS_TOKEN_URL = "https://graph.qq.com/oauth2.0/token";
	const GET_OPENID_URL = "https://graph.qq.com/oauth2.0/me";
	const GET_USER_INFO_URL = "https://graph.qq.com/user/get_user_info";

	public $config;

	public function __construct()
	{
		$CI =& get_instance();
		$CI->config->load('auth');
		$this->config = $CI->config->item('qq');
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
			'response_type' => 'code',
			'client_id' => $this->config['app_id'],
			'redirect_uri' => $redirect_uri,
			'state' => $state,
			'scope' => $this->config['scope'],
		);

		$url = self::GET_AUTH_CODE_URL . '?' . http_build_query($params);

		header("Location: {$url}");
	}

	public function get_user_info($code)
	{
		$access_token = $this->_get_access_token($code);
		$openid = $this->_get_openid($access_token);

		$params = array(
			'access_token' => $access_token,
			'oauth_consumer_key' => $this->config['app_id'],
			'openid' => $openid,
			'lang' => 'zh_CN',
		);
		$url = self::GET_USER_INFO_URL . '?' . http_build_query($params);

		$user_info = json_decode($this->_get_contents($url), TRUE);

		if (intval($user_info['ret']) !== 0)
		{
			die($user_info['msg']);
		}

		$user_info['openid'] = $openid;

		return $user_info;
	}

	private function _get_access_token($code)
	{
		$params = array(
			'grant_type' => 'authorization_code',
			'client_id' => $this->config['app_id'],
			'client_secret' => $this->config['app_key'],
			'code' => $code,
			'redirect_uri' => $this->config['callback'],
		);
		$url = self::GET_ACCESS_TOKEN_URL . '?' . http_build_query($params);

		$response = $this->_get_contents($url);

		if (strpos($response, "callback") !== false)
		{
			$lpos = strpos($response, "(");
			$rpos = strrpos($response, ")");
			$response = substr($response, $lpos + 1, $rpos - $lpos - 1);
			$msg = json_decode($response, TRUE);

			if (isset($msg['error']))
			{
				die($msg['error']);
			}
		}

		$token = array();
		parse_str($response, $token);

		return $token['access_token'];
	}

	private function _get_openid($access_token)
	{
		$params = array(
			'access_token' => $access_token,
		);

		$url = self::GET_OPENID_URL . '?' . http_build_query($params);
		$response = $this->_get_contents($url);

		if (strpos($response, "callback") !== false)
		{
			$lpos = strpos($response, "(");
			$rpos = strrpos($response, ")");
			$response = substr($response, $lpos + 1, $rpos - $lpos - 1);
		}

		$response = json_decode($response, TRUE);

		if (isset($response['error']))
		{
			die($response['error']);
		}

		return $response['openid'];
	}

	private function _get_contents($url)
	{
		if (ini_get("allow_url_fopen") == "1")
		{
			$response = file_get_contents($url);
		}
		else
		{
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
			curl_setopt($ch, CURLOPT_URL, $url);
			$response = curl_exec($ch);
			curl_close($ch);
		}

		return $response;
	}

}