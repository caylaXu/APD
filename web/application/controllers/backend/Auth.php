<?php

/**
 * Created by PhpStorm.
 * User: Peter
 * Date: 2016/6/4
 * Time: 14:45
 */
class Auth extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();

		$this->load->add_package_path(APPPATH . "../phplibs/hpf_smarty/");
		$this->load->library("hpf_smarty");

		//引入错误捕捉
		require_once ( APPPATH . "../phplibs/hpf_exception/libraries/set_my_exception_handler.php" );
	}

	public function qq_login()
	{
		$this->load->library('QQSDK');

		$project_id = intval($this->input->get('ProjectId'));
		$user_id = intval($this->input->get('UserId'));

		$param = array();
		if ($project_id)
		{//通过链接（二维码）加入项目
			$this->load->model('bll/Project_bll', 'project_bll');

			$fields = array('Id');
			$where = array('Id' => $project_id);
			$project = $this->project_bll->select_info($fields, $where);
			if (!$project)
			{
				die('该项目不存在');
			}

			$param['pid'] = $project_id;
		}
		else if ($user_id)
		{//用户绑定第三方账号
			$this->load->model('bll/User_bll', 'user_bll');

			$fields = array('Id');
			$where = array('Id' => $user_id);
			$user = $this->user_bll->select_info($fields, $where);
			if (!$user)
			{
				die('该用户不存在');
			}

			$param['uid'] = $user_id;
		}
		else
		{//使用第三方账号登录

		}

		$this->qqsdk->login($param);
	}

	public function qq_callback()
	{
		$this->load->library('QQSDK');

		$project_id = intval($this->input->get('pid'));
		$user_id = intval($this->input->get('uid'));

		//获取第三方用户的信息
		$code = $this->input->get('code');
		$result = $this->qqsdk->get_user_info($code);
		$type = 'qq';
		$third_id = $result['openid'];
		$nickname = $result['nickname'];
		$avatar = $result['figureurl_1'];
		$sex = $result['gender'];

		if ($project_id)
		{//通过链接（二维码）加入项目
			$this->load->model('bll/Rlt_third_user_bll', 'rtu_bll');
			$this->load->model('bll/Rlt_project_user_bll', 'rpu_bll');

			//注册第三方用户
			$user_id = $this->rtu_bll->third_register($type, $third_id, $nickname, $avatar, $sex);
			if (!$user_id)
			{
				die('注册第三方用户失败');
			}

			//加入项目
			$result = $this->rpu_bll->create($project_id, $user_id, 2);
			if (!$result)
			{
				die('加入项目失败，请稍后再试');
			}
			else
			{
				$this->load->model('bll/User_bll', 'user_bll');
				$this->user_bll->login($user_id);

				header("Location: /backend/task/task_tree?ProjectId={$project_id}");
				exit;
			}
		}
		else if ($user_id)
		{//用户绑定第三方账号
			$this->load->model('bll/User_bll', 'user_bll');
			$this->load->model('bll/Rlt_third_user_bll', 'rtu_bll');

			//绑定第三方用户
			$result = $this->rtu_bll->bind($user_id, $type, $third_id, $nickname, $sex);
			if ($result['Result'] !== 0)
			{
				$this->hpf_smarty->assign("Message", $result['Msg']);
				$this->hpf_smarty->display('backend/error/message.php');
				exit;
			}
			else
			{
				header("Location: /backend/user/update");
				exit;
			}
		}
		else
		{//使用第三方账号登录
			$this->load->model('bll/User_bll', 'user_bll');

			$param = array(
				'Type' => $type,
				'ThirdId' => $third_id,
				'NickName' => $nickname,
				'Avatar' => $avatar,
				'Sex' => $sex,
			);
			$user_id = $this->user_bll->social_login($param);
			if (!$user_id)
			{
				die('注册第三方用户失败');
			}
			else
			{
				$this->load->model('bll/User_bll', 'user_bll');
				$this->user_bll->login($user_id);
				header("Location: /backend/workbench/index");
				exit;
			}
		}
	}

	public function weibo_login()
	{
		$this->load->library('WeiboSDK');

		$project_id = intval($this->input->get('ProjectId'));
		$user_id = intval($this->input->get('UserId'));

		$param = array();
		if ($project_id)
		{//通过链接（二维码）加入项目
			$this->load->model('bll/Project_bll', 'project_bll');

			$fields = array('Id');
			$where = array('Id' => $project_id);
			$project = $this->project_bll->select_info($fields, $where);
			if (!$project)
			{
				die('该项目不存在');
			}

			$param['pid'] = $project_id;
		}
		else if ($user_id)
		{//用户绑定第三方账号
			$this->load->model('bll/User_bll', 'user_bll');

			$fields = array('Id');
			$where = array('Id' => $user_id);
			$user = $this->user_bll->select_info($fields, $where);
			if (!$user)
			{
				die('该用户不存在');
			}

			$param['uid'] = $user_id;
		}
		else
		{//使用第三方账号登录

		}

		$this->weibosdk->login($param);
	}

	public function weibo_callback()
	{
		$this->load->library('WeiboSDK');

		$project_id = intval($this->input->get('pid'));
		$user_id = intval($this->input->get('uid'));

		$code = $this->input->get('code');
		if (!$code)
		{
			if ($user_id)
			{
				header("Location: /backend/user/update");
				exit;
			}
			else
			{
				header("Location: /backend/login/login");
				exit;
			}
		}

		//获取第三方用户的信息
		$result = $this->weibosdk->get_user_info($code);
		$type = 'weibo';
		$third_id = $result['id'];
		$nickname = $result['screen_name'];
		$avatar = $result['profile_image_url'];
		$sex = $result['gender'];

		if ($project_id)
		{//通过链接（二维码）加入项目
			$this->load->model('bll/Rlt_third_user_bll', 'rtu_bll');
			$this->load->model('bll/Rlt_project_user_bll', 'rpu_bll');

			//注册第三方用户
			$user_id = $this->rtu_bll->third_register($type, $third_id, $nickname, $avatar, $sex);
			if (!$user_id)
			{
				die('注册第三方用户失败');
			}

			//加入项目
			$result = $this->rpu_bll->create($project_id, $user_id, 2);
			if (!$result)
			{
				die('加入项目失败，请稍后再试');
			}
			else
			{
				$this->load->model('bll/User_bll', 'user_bll');
				$this->user_bll->login($user_id);

				header("Location: /backend/task/task_tree?ProjectId={$project_id}");
				exit;
			}
		}
		else if ($user_id)
		{//用户绑定第三方账号
			$this->load->model('bll/User_bll', 'user_bll');
			$this->load->model('bll/Rlt_third_user_bll', 'rtu_bll');

			//绑定第三方用户
			$result = $this->rtu_bll->bind($user_id, $type, $third_id, $nickname, $sex);
			if ($result['Result'] !== 0)
			{
				$this->hpf_smarty->assign("Message", $result['Msg']);
				$this->hpf_smarty->display('backend/error/message.php');
				exit;
			}
			else
			{
				header("Location: /backend/user/update");
				exit;
			}
		}
		else
		{//使用第三方账号登录
			$this->load->model('bll/User_bll', 'user_bll');

			$param = array(
				'Type' => $type,
				'ThirdId' => $third_id,
				'NickName' => $nickname,
				'Avatar' => $avatar,
				'Sex' => $sex,
			);
			$user_id = $this->user_bll->social_login($param);
			if (!$user_id)
			{
				die('注册第三方用户失败');
			}
			else
			{
				$this->load->model('bll/User_bll', 'user_bll');
				$this->user_bll->login($user_id);
				header("Location: /backend/workbench/index");
				exit;
			}
		}
	}

	public function wechat_login()
	{
		$this->load->library('WechatSDK');

		$project_id = intval($this->input->get('ProjectId'));
		$user_id = intval($this->input->get('UserId'));

		$param = array();
		if ($project_id)
		{//通过链接（二维码）加入项目
			$this->load->model('bll/Project_bll', 'project_bll');

			$fields = array('Id');
			$where = array('Id' => $project_id);
			$project = $this->project_bll->select_info($fields, $where);
			if (!$project)
			{
				die('该项目不存在');
			}

			$param['pid'] = $project_id;
		}
		else if ($user_id)
		{//用户绑定第三方账号
			$this->load->model('bll/User_bll', 'user_bll');

			$fields = array('Id');
			$where = array('Id' => $user_id);
			$user = $this->user_bll->select_info($fields, $where);
			if (!$user)
			{
				die('该用户不存在');
			}

			$param['uid'] = $user_id;
		}
		else
		{//使用第三方账号登录

		}

		$this->wechatsdk->login($param);
	}

	public function wechat_callback()
	{
		$this->load->library('WechatSDK');

		$project_id = intval($this->input->get('pid'));
		$user_id = intval($this->input->get('uid'));

		//获取第三方用户的信息
		$code = $this->input->get('code');
		$result = $this->wechatsdk->get_user_info($code);
		$type = 'wechat';
		$third_id = $result['openid'];
		$nickname = $result['nickname'];
		$avatar = $result['headimgurl'];
		$sex = $result['sex'];

		if ($project_id)
		{//通过链接（二维码）加入项目
			$this->load->model('bll/Rlt_third_user_bll', 'rtu_bll');
			$this->load->model('bll/Rlt_project_user_bll', 'rpu_bll');

			//注册第三方用户
			$user_id = $this->rtu_bll->third_register($type, $third_id, $nickname, $avatar, $sex);
			if (!$user_id)
			{
				die('注册第三方用户失败');
			}

			//加入项目
			$result = $this->rpu_bll->create($project_id, $user_id, 2);
			if (!$result)
			{
				die('加入项目失败，请稍后再试');
			}
			else
			{
				$this->load->model('bll/User_bll', 'user_bll');
				$this->user_bll->login($user_id);

				header("Location: /backend/task/task_tree?ProjectId={$project_id}");
				exit;
			}
		}
		else if ($user_id)
		{//用户绑定第三方账号
			$this->load->model('bll/User_bll', 'user_bll');
			$this->load->model('bll/Rlt_third_user_bll', 'rtu_bll');

			//绑定第三方用户
			$result = $this->rtu_bll->bind($user_id, $type, $third_id, $nickname, $sex);
			if ($result['Result'] !== 0)
			{
				$this->hpf_smarty->assign("Message", $result['Msg']);
				$this->hpf_smarty->display('backend/error/message.php');
				exit;
			}
			else
			{
				header("Location: /backend/user/update");
				exit;
			}
		}
		else
		{//使用第三方账号登录
			$this->load->model('bll/User_bll', 'user_bll');

			$param = array(
				'Type' => $type,
				'ThirdId' => $third_id,
				'NickName' => $nickname,
				'Avatar' => $avatar,
				'Sex' => $sex,
			);
			$user_id = $this->user_bll->social_login($param);
			if (!$user_id)
			{
				die('注册第三方用户失败');
			}
			else
			{
				$this->load->model('bll/User_bll', 'user_bll');
				$this->user_bll->login($user_id);
				header("Location: /backend/workbench/index");
				exit;
			}
		}
	}
}