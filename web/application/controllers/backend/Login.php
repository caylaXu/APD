<?php
class Login extends MyController
{

    function __construct()
    {
        parent::__construct();
        $this->load->model('bll/user_bll');
        $this->load->model('bll/task_bll');
        $this->load->model('bll/redis_bll');
    }

    /**
     * @function 登录
     * @author CaylaXu
     */
    function login()
    {
        if ($_POST)
        {
            $params = common::check_get_post(array('Mobile'=>true,'Password'=>true),'post',false);

            if(!$params)
            {
                common::return_json(-1,'参数非法','',true);
            }

            $admin_name = $params['Mobile'];
            $password   = $params['Password'];

            if(empty($admin_name) || (!common::is_mobile($admin_name) && !common::is_email($admin_name)))
            {
                common::return_json(-1,'请输入正确的手机号或邮箱','',true);
            }

            $user_info = $this->user_bll->get_user_by_mobile_or_email($admin_name);

            if(empty($user_info))
            {
                common::return_json(-1,'账号不存在','',true);
            }

            $check_password = common::check_password($user_info,md5($password));

//            $check_password = true;

            if(!$check_password)
            {
                common::return_json(-1,'密码错误','',true);
            }

            $user_id = $user_info['Id'];

            set_cookie("login_time", time(), 86400 * 30);
            set_cookie("user_id".$this->config->item('SystemName'), $user_id, 86400 * 30);
//            get_cookie("user_id".$this->config->item('SystemName'));
            set_cookie("login_key", md5($user_id . time() . $this->config->item('SystemName')), 86400 * 30);
            common::return_json(0,'登录成功',$user_info['Id'],true);
        }
        $this->hpf_smarty->display('backend/login/login.tpl');
    }

    /**
     * @function 注册
     * @author CaylaXu
     */
    function register()
    {
        if($_POST)
        {
            $check = array(
                'Name' => true,
                'Email' => true,
                'Mobile' => true,
                'Title' => false,
                'Password' => true,
                'RePassword' => true,
            );

            $params = common::check_get_post($check,'post',false);

            if(!$params)
            {
                common::return_json(-1,'参数非法','',true);
            }

            if($params['Password'] != $params['RePassword'])
            {
                common::return_json(-1,'两次输入不一致','',true);
            }

            if(!common::is_email($params['Email']))
            {
                common::return_json(-1,'邮箱格式不正确','',true);
            }

            if(!common::is_mobile($params['Mobile']))
            {
                common::return_json(-1,'手机号格式不正确','',true);
            }

            unset($params['RePassword']);

            $user_info = $this->user_bll->select_info(array(),array('Mobile'=>$params['Mobile']));
            $params['RegistrationDate'] = time();

            if(!empty($user_info))
            {
                if(!empty($user_info[0]['RegistrationDate']))
                {
                    common::return_json(-1,'手机号被占用','',true);
                }
                else//注册时间为空（说明是别人帮他添加的自己未注册）
                {
                    $hash = common::hash(md5($params['Password']));
                    $params['Avatar'] = common::get_first_char($params['Name']).".jpg";
                    $params['Password'] = $hash['hash'];
                    $params['Salt'] = $hash['salt'];
                    $params['Status'] = 1;
                    $result = $this->user_bll->update($params,array('Id'=>$user_info[0]['Id']));
                }
            }
            else
            {
                $user_info_email = $this->user_bll->select_info(array(),array('Email'=>$params['Email']));
                if(!empty($user_info_email))
                {
                    if(!empty($user_info_email[0]['RegistrationDate']))
                    {
                        common::return_json(-1,'邮箱被占用','',true);
                    }
                    else//注册时间为空（说明是别人帮他添加的自己未注册）
                    {
                        $hash = common::hash(md5($params['Password']));
                        $params['Avatar'] = common::get_first_char($params['Name']).".jpg";
                        $params['Password'] = $hash['hash'];
                        $params['Salt'] = $hash['salt'];
                        $params['Status'] = 1;
                        $result = $this->user_bll->update($params,array('Id'=>$user_info_email[0]['Id']));
                    }
                }
                else
                {
                    $hash = common::hash(md5($params['Password']));
                    $params['Avatar'] = common::get_first_char($params['Name']).".jpg";
                    $params['Password'] = $hash['hash'];
                    $params['Salt'] = $hash['salt'];
                    $result = $this->user_bll->create($params);
                }
            }

            if(!$result)
            {
                common::return_json(-1,'注册失败','',true);
            }

            common::return_json(0,'注册成功','',true);
        }
        $source = isset($_GET['Source']) ? $_GET['Source'] : '';
        $this->hpf_smarty->assign('Source',$source);//email只显示邮箱注册，mobile只显示手机号注册
        $this->hpf_smarty->display('backend/login/register.tpl');
    }

    /*
     * @function 邀请加入项目组  链接
     * @author Nick
     * 此页面：判断已登录未登录显示
     * */
    function addProject()
    {
//        if($_POST)
//        {
//            $check = array(
//                'Name' => true,
//                'Email' => true,
//                'Mobile' => true,
//                'Title' => false,
//                'Password' => true,
//                'RePassword' => true,
//            );
//
//            $params = common::check_get_post($check,'post',false);
//
//            if(!$params)
//            {
//                common::return_json(-1,'参数非法','',true);
//            }
//
//            if($params['Password'] != $params['RePassword'])
//            {
//                common::return_json(-1,'两次输入不一致','',true);
//            }
//
//            if(!common::is_email($params['Email']))
//            {
//                common::return_json(-1,'邮箱格式不正确','',true);
//            }
//
//            if(!common::is_mobile($params['Mobile']))
//            {
//                common::return_json(-1,'手机号格式不正确','',true);
//            }
//
//            unset($params['RePassword']);
//
//            $user_info = $this->user_bll->select_info(array(),array('Mobile'=>$params['Mobile']));
//            $params['RegistrationDate'] = time();
//
//            if(!empty($user_info))
//            {
//                if(!empty($user_info[0]['RegistrationDate']))
//                {
//                    common::return_json(-1,'手机号被占用','',true);
//                }
//                else//注册时间为空（说明是别人帮他添加的自己未注册）
//                {
//                    $hash = common::hash(md5($params['Password']));
//                    $params['Avatar'] = common::get_first_char($params['Name']).".jpg";
//                    $params['Password'] = $hash['hash'];
//                    $params['Salt'] = $hash['salt'];
//                    $params['Status'] = 1;
//                    $result = $this->user_bll->update($params,array('Id'=>$user_info[0]['Id']));
//                }
//            }
//            else
//            {
//                $user_info_email = $this->user_bll->select_info(array(),array('Email'=>$params['Email']));
//                if(!empty($user_info_email))
//                {
//                    if(!empty($user_info_email[0]['RegistrationDate']))
//                    {
//                        common::return_json(-1,'邮箱被占用','',true);
//                    }
//                    else//注册时间为空（说明是别人帮他添加的自己未注册）
//                    {
//                        $hash = common::hash(md5($params['Password']));
//                        $params['Avatar'] = common::get_first_char($params['Name']).".jpg";
//                        $params['Password'] = $hash['hash'];
//                        $params['Salt'] = $hash['salt'];
//                        $params['Status'] = 1;
//                        $result = $this->user_bll->update($params,array('Id'=>$user_info_email[0]['Id']));
//                    }
//                }
//                else
//                {
//                    $hash = common::hash(md5($params['Password']));
//                    $params['Avatar'] = common::get_first_char($params['Name']).".jpg";
//                    $params['Password'] = $hash['hash'];
//                    $params['Salt'] = $hash['salt'];
//                    $result = $this->user_bll->create($params);
//                }
//            }
//
//            if(!$result)
//            {
//                common::return_json(-1,'注册失败','',true);
//            }
//
//            common::return_json(0,'注册成功','',true);
//        }
        $source = isset($_GET['Source']) ? $_GET['Source'] : '';
        $this->hpf_smarty->assign('Source',$source);//email只显示邮箱注册，mobile只显示手机号注册
        $this->hpf_smarty->display('backend/login/addProject.tpl');
    }

    /*
    * @function 加入项目未注册的页面
    * @author Nick
    * */
    function addProjectRegister(){
        $source = isset($_GET['Source']) ? $_GET['Source'] : '';
        $this->hpf_smarty->assign('Source',$source);//email只显示邮箱注册，mobile只显示手机号注册
        $this->hpf_smarty->display('backend/login/addProjectRegister.tpl');
    }

    function logout()
    {
        set_cookie("login_time",'',0);
        set_cookie("user_id".$this->config->item('SystemName'),'',0);
        set_cookie("login_key",'',0);
        echo "<meta http-equiv='refresh' Cache-Control='no cache' content='0;url=/backend/login/login'>";
    }

    /**
     * @function 获取验证码(手机号或邮箱)
     * @User: CaylaXu
     */
    function get_validate_code()
    {
        $mobile = isset($_GET['Mobile']) ? trim($_GET['Mobile']) : '';
        $email = isset($_GET['Email']) ? trim($_GET['Email']) : '';

        if(empty($mobile) && empty($email))
        {
            common::return_json(-1,'参数非法','',true);
        }

        $result = array('Result'=>-1,'Msg'=>'验证码发送失败');
        if(!empty($mobile))
        {
            if(!common::is_mobile($mobile))
            {
                common::return_json(-1,'手机号码格式不正确','',true);
            }
            $key              = 'apd_random' . $mobile;
            $random_code      = Common::get_random_code();
            $this->redis_bll->my_set($key,$random_code,40 * 60);
            $message_content  = "91恋车验证码".$random_code;
            $send_result = common::send_sms($message_content,$mobile);
            if($send_result != false)
            {
                $result['Result'] = 0;
                $result['Msg']    = '恭喜您，获取成功';
            }
        }
        else if(!empty($email))
        {
            if(!common::is_email($email))
            {
                common::return_json(-1,'邮箱格式不正确','',true);
            }

            $key = 'apd_random'.$email;
            $random_code = Common::get_random_code();
            $this->redis_bll->my_set($key,$random_code,40 * 60);
            $host = $_SERVER["HTTP_HOST"];
            $content = <<<BODY
<html><head></head><body><table><tr><td bgcolor=#0373d6 height=54 align=center><table><tr><td align=center><img src=http://{$host}/resource/asset/img/email-logo.png width=71 height=54></td></tr></table></td></tr><tr><td height=227 align=center style="padding: 0 15px;"><table border=0 cellspacing=0 width=480><tr><td><table width=100% border=0 cellpadding=0><tr><td><table cellspacing=0 border=0 align=left><tr><td width=550 align=left valign=top><table width=100% border=0 cellspacing=0><tr><td align=left valign=top style="font-size:14px; color:#7b7b7b; line-height: 25px; padding: 20px 0px">你此次重置密码的验证码如下,请在 30 分钟内输入验证码进行下一步操作。 如非你本人操作，请忽略此邮件。</td></tr><tr><td align=center><table border=0 cellspacing=0 cellpadding=0><tr><td><span><div style="padding:10px 18px;border-radius:3px;text-align:center;background-color:#ecf4fb;color:#4581E9;font-size:20px;font-weight:700;letter-spacing:2px; margin:0;"><span>{$random_code}</span></div></span></td></tr></table></td></tr></table></td></tr></table></td></tr></table></td></tr></table></td></tr></table></body></html>
BODY;
            $send_result = common::send_email('APD系统验证码',$content,$email);
            if($send_result != false)
            {
                $result['Result'] = 0;
                $result['Msg']    = '恭喜您，获取成功';
            }
        }
        common::return_json($result['Result'],$result['Msg'],'',true);
    }

    /**
     * @function 手机号注册
     * @User: CaylaXu
     */
    function register_by_mobile()
    {
        $check = array(
            'Name' => true,
            'Mobile' => true,
            'Password' => true,
            'AuthCode'=>true
        );

        $params = common::check_get_post($check,'post',false);

        if(!$params)
        {
            common::return_json(-1,'参数非法','',true);
        }

        if(!common::is_mobile($params['Mobile']))
        {
            common::return_json(-1,'手机号格式不正确','',true);
        }

        //验证验证码是否正确
        $check_code = $this->redis_bll->my_check_mobile_and_verif($params['Mobile'], $params['AuthCode'],'apd_random');

        if(!$check_code)
        {
            common::return_json(-1,'验证码错误','',true);
        }

        $user_info = $this->user_bll->select_info(array(),array('Mobile'=>$params['Mobile']));
        unset($params['AuthCode']);
        $params['RegistrationDate'] = time();
        $result = false;
        $user_id = 0;
        if(!empty($user_info))
        {
            if(!empty($user_info[0]['RegistrationDate']))
            {
                common::return_json(-1,'手机号被占用','',true);
            }
            else//注册时间为空（说明是别人帮他添加的自己未注册）
            {
                $hash = common::hash(md5($params['Password']));
                $params['Avatar'] = common::get_first_char($params['Name']).".jpg";
                $params['Password'] = $hash['hash'];
                $params['Salt'] = $hash['salt'];
                $params['Status'] = 1;
                $result = $this->user_bll->update($params,array('Id'=>$user_info[0]['Id']));
                $user_id = $user_info[0]['Id'];
            }
        }
        else
        {
            $hash = common::hash(md5($params['Password']));
            $params['Avatar'] = common::get_first_char($params['Name']).".jpg";
            $params['Password'] = $hash['hash'];
            $params['Salt'] = $hash['salt'];
            $result = $this->user_bll->create($params);
            $user_id = $result;
        }

        if(!$result)
        {
            common::return_json(-1,'注册失败','',true);
        }

        common::return_json(0,'注册成功',$user_id,true);
    }

    function register_by_email()
    {
        $check = array(
            'Name' => true,
            'Email' => true,
            'Password' => true,
            'AuthCode'=>true
        );

        $params = common::check_get_post($check,'post',false);

        if(!$params)
        {
            common::return_json(-1,'参数非法','',true);
        }

        if(!common::is_email($params['Email']))
        {
            common::return_json(-1,'邮箱格式不正确','',true);
        }

        //验证验证码是否正确
        $check_code = $this->redis_bll->my_check_mobile_and_verif($params['Email'], $params['AuthCode'],'apd_random');
        if(!$check_code)
        {
            common::return_json(-1,'验证码错误','',true);
        }

        $user_info_email = $this->user_bll->select_info(array(),array('Email'=>$params['Email'],'Status !='=>-1));
        unset($params['AuthCode']);
        $params['RegistrationDate'] = time();
        $result = false;
        $user_id = 0;
        if(!empty($user_info_email))
        {
            if(!empty($user_info_email[0]['RegistrationDate']))
            {
                common::return_json(-1,'邮箱被占用','',true);
            }
            else//注册时间为空（说明是别人帮他添加的自己未注册）
            {
                $hash = common::hash(md5($params['Password']));
                $params['Avatar'] = common::get_first_char($params['Name']).".jpg";
                $params['Password'] = $hash['hash'];
                $params['Salt'] = $hash['salt'];
                $params['Status'] = 1;
                $result = $this->user_bll->update($params,array('Id'=>$user_info_email[0]['Id']));
                $user_id = $user_info_email[0]['Id'];
            }
        }
        else
        {
            $hash = common::hash(md5($params['Password']));
            $params['Avatar'] = common::get_first_char($params['Name']).".jpg";
            $params['Password'] = $hash['hash'];
            $params['Salt'] = $hash['salt'];
            $result = $this->user_bll->create($params);
            $user_id = $result;
        }

        if(!$result)
        {
            common::return_json(-1,'注册失败','',true);
        }

        common::return_json(0,'注册成功',$user_id,true);
    }

    /**
     * @function 忘记密码
     * @User: CaylaXu
     */
    public function forget_password()
    {
        $this->hpf_smarty->display('backend/login/forgetpassword.tpl');
    }

    /**
     * @function 手机号修改密码
     * @User: CaylaXu
     */
    public function change_by_mobile()
    {
        $check = array(
            'Mobile' => true,
            'AuthCode' => true,
            'Password' => true,
        );

        $params = common::check_get_post($check,'post',false);

        if(!$params)
        {
            common::return_json(-1,'参数非法','',true);
        }

        if(!common::is_mobile($params['Mobile']))
        {
            common::return_json(-1,'手机号格式不正确','',true);
        }

        $user_info = $this->user_bll->select_info(array(),array('Mobile'=>$params['Mobile'],'Status'=>1));

        if(empty($user_info))
        {
            common::return_json(-1,'账号不存在','',true);
        }

        //验证验证码是否正确
        $check_code = $this->redis_bll->my_check_mobile_and_verif($params['Mobile'], $params['AuthCode'],'apd_random');

        if(!$check_code)
        {
            common::return_json(-1,'验证码错误','',true);
        }

        $hash = common::hash(md5($params['Password']));
        $data['Password'] = $hash['hash'];
        $data['Salt'] = $hash['salt'];
        $result = $this->user_bll->update($data,array('Id'=>$user_info[0]['Id']));

        if(!$result)
        {
            common::return_json(-1,'修改失败','',true);
        }

        common::return_json(0,'修改成功','',true);
    }

    /**
     * @function 邮箱修改密码
     * @User: CaylaXu
     */
    public function change_by_email()
    {
        $check = array(
            'Email' => true,
            'AuthCode' => true,
            'Password' => true
        );

        $params = common::check_get_post($check,'post',false);

        if(!$params)
        {
            common::return_json(-1,'参数非法','',true);
        }

        if(!common::is_email($params['Email']))
        {
            common::return_json(-1,'邮箱格式不正确','',true);
        }

        $user_info = $this->user_bll->select_info(array(),array('Email'=>$params['Email'],'Status'=>1));

        if(empty($user_info))
        {
            common::return_json(-1,'账号不存在','',true);
        }

        //验证验证码是否正确
        $check_code = $this->redis_bll->my_check_mobile_and_verif($params['Email'], $params['AuthCode'],'apd_random');

        if(!$check_code)
        {
            common::return_json(-1,'验证码错误','',true);
        }

        $hash = common::hash(md5($params['Password']));
        $data['Password'] = $hash['hash'];
        $data['Salt'] = $hash['salt'];
        $result = $this->user_bll->update($data,array('Id'=>$user_info[0]['Id']));

        if(!$result)
        {
            common::return_json(-1,'修改失败','',true);
        }

        common::return_json(0,'修改成功','',true);
    }

	/**
	 * @function 访问加入链接
	 * @author Peter
	 */
	public function join_by_link()
	{
		if (!$this->input->get())
		{
			die('访问非法');
		}

		$unicode = $this->input->get('Id');
		if (!$unicode)
		{
			die('参数非法');
		}

		//获取项目相关信息
		$this->load->model('bll/project_bll');
		$fields = array('Id', 'Title', 'ProjectManagerId');
		$where = array('UniCode' => $unicode);
		$project = $this->project_bll->select_info($fields, $where, FALSE);
		if (!$project)
		{
			die('该项目不存在');
		}
		$data['project'] = array(
			'Id' => $project['Id'],
			'Name' => $project['Title'],
		);

		//获取项目经理信息
		$this->load->model('bll/user_bll');
		$data['manager'] = $this->user_bll->get_user_by_id($project['ProjectManagerId']);

		//判断是否登录
		$user_id = get_cookie("user_id" . $this->config->item('SystemName'));
		$login_time = get_cookie("login_time");
		$login_key = get_cookie("login_key");
		$md5_key = md5($user_id . $login_time . $this->config->item('SystemName'));
		if ($md5_key != $login_key)
		{//未登录
			$this->hpf_smarty->assign('data', $data);
			$this->hpf_smarty->display('backend/login/addProject.tpl');
		}
		else
		{//已登录
			$this->load->model('bll/user_bll');
			$data['user'] = $this->user_bll->get_user_by_id($user_id);
			$this->hpf_smarty->assign('data', $data);
			$this->hpf_smarty->display('backend/login/addProject.tpl');
		}
	}

	/**
	 * @function 加入项目
	 * @author Peter
	 */
	public function join_a_project()
	{
		if (!$this->input->is_ajax_request() || !$this->input->post())
		{
			common::return_json(-1, '访问非法', '', true);
		}

		$user_id = intval($this->input->post('UserId'));
		$project_id = intval($this->input->post('ProjectId'));

		if ($user_id <= 0 || $project_id <= 0)
		{
			common::return_json(-1, '参数非法', '', true);
		}

		//校验用户
		$this->load->model('bll/user_bll');
		$fields = array('Id');
		$where = array('Id' => $user_id);
		$user = $this->user_bll->select_info($fields, $where, FALSE);
		if (!$user)
		{
			common::return_json(-1, '该用户不存在', '', true);
		}

		//校验项目
		$this->load->model('bll/project_bll');
		$fields = array('Id');
		$where = array('Id' => $project_id);
		$project = $this->project_bll->select_info($fields, $where, FALSE);
		if (!$project)
		{
			common::return_json(-1, '该项目不存在', '', true);
		}

		//加入项目
		$this->load->model('bll/rlt_project_user_bll');
		$type = 2;//项目成员
		$result = $this->rlt_project_user_bll->create($project_id, $user_id, $type);
		if (!$result)
		{
			common::return_json(-1, '加入项目失败，请稍后再试', '', true);
		}
		else
		{
			common::return_json(0, '加入成功', '', true);
		}
	}
}
