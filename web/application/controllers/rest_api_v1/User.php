<?php
/**
 * Created by PhpStorm.
 * User: cayla
 * Date: 2015/11/8
 * Time: 20:04
 */
class User extends MyRestController
{

    function __construct()
    {
        parent::__construct();
        $this->load->model('bll/rlt_project_user_bll');
        $this->load->model('bll/user_bll');
    }

    /**
     * @function 获取项目所属成员
     * @author CaylaXu
     */
    public function user_by_project_id_get()
    {
        $project_id = $this->get('ProjectId');

        $users = $this->rlt_project_user_bll->query_by_project_id($project_id);
        if(!is_array($users))
        {
            $this->response(array('Result'=>-1,'Msg'=>'系统繁忙'), 200);
            exit;
        }

        $this->response(array('Result'=>0,'Msg'=>'获取成功','Data'=>$users), 200);
    }

    /**
     * @function 手机通讯录、邮箱、手机添加人
     * @User: CaylaXu
     */
    public function user_post()
    {
        $name = $this->post('Name');
        $params = $this->post();
        if(isset($params['Mobile']))
        {
            $mobile= $this->post('Mobile');
            $this->add_user_mobile($mobile,$name);
        }
        else if(isset($params['Email']))
        {
            $email= $this->post('Email');
            $this->add_user_email($email,$name);
        }
        else
        {
            $this->response(array('Result'=>-1,'Msg'=>'系统繁忙'), 200);
            exit;
        }
    }

    public function add_user_email($email,$name)
    {
        if(!common::is_email($email))
        {
            $this->response(array('Result'=>-1,'Msg'=>'邮箱错误'), 200);
            exit;
        }

        //1、判断用户是否已存在
        $user_info = $this->user_bll->select_info(array('Id'),array('Email'=>$email,'Status !='=>-1));

        if(!empty($user_info))
        {
            $user_info = $this->user_bll->get_user_by_id($user_info[0]['Id']);
            $this->response(array('Result'=>10020,'Msg'=>'成员添加成功','Data'=>$user_info), 200);//已存在
            exit;
        }

        $avatar = common::get_first_char($name).'.jpg';

        $insert_data =array(
            'Name' => $name,
            'Email' => $email,
            'Avatar' => $avatar,
            'Status' => 0
        );

        $user_id = $this->user_bll->create($insert_data);

        if(!$user_id)
        {
            $this->response(array('Result'=>-1,'Msg'=>'用户新增失败'), 200);
            exit;
        }

        //邮件通知用户注册
        {
            $url = 'http://'.$_SERVER["HTTP_HOST"].'/backend/login/register?Source=email';
            $host = $_SERVER["HTTP_HOST"];
            $content = <<<BODY
<html><head></head><body><table><tr><td bgcolor=#0373d6 height=54 align=center><table><tr><td align=center><img src=http://{$host}/resource/asset/img/email-logo.png width=71 height=54></td></tr></table></td></tr><tr><td height=227 align=center style="padding: 0 15px;"><table border=0 cellspacing=0 width=480><tr><td><table width=100% border=0 cellpadding=0><tr><td><table cellspacing=0 border=0 align=left><tr><td width=550 align=left valign=top><table width=100% border=0 cellspacing=0><tr><td align=left valign=top style="font-size:14px; color:#7b7b7b; line-height: 25px; padding: 20px 0px">您的同事邀请您加入APD
立即加入{$url}</td></tr></table></td></tr></table></td></tr></table></td></tr></table></td></tr></table></body></html>
BODY;
            common::send_email("注册邀请",$content,array($email));
        }

        $user_info = $this->user_bll->get_user_by_id($user_id);
        $this->response(array('Result'=>0,'Msg'=>'成员指派成功','Data'=>$user_info), 200);
        exit;
    }

    public function add_user_mobile($mobile,$name)
    {
        if(!common::is_mobile($mobile))
        {
            $this->response(array('Result'=>-1,'Msg'=>'手机号非法'), 200);
            exit;
        }

        //1、判断用户是否已存在
        $user_info = $this->user_bll->select_info(array(),array('Mobile'=>$mobile,'Status !='=>-1));

        if(!empty($user_info))
        {
            $user_info = $this->user_bll->get_user_by_id($user_info[0]['Id']);
            $this->response(array('Result'=>10020,'Msg'=>'成员添加成功','Data'=>$user_info), 200);
            exit;
        }
        $avatar = common::get_first_char($name).'.jpg';

        $insert_data =array(
            'Name' => $name,
            'Mobile' => $mobile,
            'Avatar' => $avatar,
            'Status' => 0
        );

        $user_id = $this->user_bll->create($insert_data);

        if(!$user_id)
        {
            $this->response(array('Result'=>-1,'Msg'=>'用户新增失败'), 200);
            exit;
        }

        //短信通知用户注册
        {
            $url = 'http://'.$_SERVER["HTTP_HOST"].'/backend/login/register?Source=mobile';
            $content = "您的同事邀请您加入APD立即加入".$url;
            common::send_sms($content,$mobile);
        }

        $user_info = $this->user_bll->get_user_by_id($user_id);
        $this->response(array('Result'=>0,'Msg'=>'成员指派成功','Data'=>$user_info), 200);
        exit;
    }

    public function users_get()
    {
        $users = $this->user_bll->select_info(array('Id','Email','Mobile','Status'),array('Id !='=>-1));
        $this->response(array('Result'=>0,'Msg'=>'获取成功','Data'=>$users), 200);
        exit;
    }

    /**
     * 修改教练头像
     */
    function user_put()
    {
        $user_id = $this->get('Id');
        $avatar = $this->put('Avatar');
        $name = $this->put('Name');
        $mobile = $this->put('Mobile');
        $email = $this->put('Email');
        $auth_code = $this->put('AuthCode');
        $old_pwd = $this->put('OldPassword');
        $new_pwd = $this->put('NewPassword');
        $pwd = $this->put('Password');

        if(!is_numeric($user_id))
        {
            $this->response(array('Result' => -1, 'Msg' => '参数非法'), 200);
            exit;
        }

        $user = $this->user_bll->select_info(array(),array('Id'=>$user_id),false);
        if(empty($user))
        {
            $this->response(array('Result' => -1, 'Msg' => '用户不存在'), 200);
            exit;
        }

        if(!empty($avatar))//更换头像
        {
            $result = $this->user_bll->update_avatar($user_id, $avatar);
            $user =  $this->user_bll->select_info(array(),array('Id'=>$user_id),false);
            $this->response(array('Result' => $result ? 0:-1, 'Msg' => $result?'修改成功':'修改失败','Data'=>$user), 200);
            exit;
        }

        if(!empty($name))//修改昵称
        {
            $check_data = common::check_string_length(1,75,$name);
            if(!$check_data)
            {
                $this->response(array('Result' => -1, 'Msg' => '用户名不允许为空或超过25个字符'), 200);
                exit;
            }

            $result = $this->user_bll->update(array('Name'=>$name),array('Id'=>$user_id));
            $user =  $this->user_bll->select_info(array(),array('Id'=>$user_id),false);
            $this->response(array('Result' => $result ? 0:-1, 'Msg' => $result?'修改成功':'修改失败' ,'Data'=>$user), 200);
            exit;
        }

        if(!empty($mobile) && !empty($auth_code))//修改手机号
        {
            $result = $this->user_bll->setting_or_change_mobile($user_id,$mobile, $auth_code);
            if(empty($result['Data']))
            {
                $user['Mobile'] =  $mobile;
                $result['Data'] = $user;
            }
            $this->response($result, 200);
            exit;
        }

        if(!empty($email) && !empty($auth_code))//修改邮箱
        {
            $result = $this->user_bll->setting_or_change_email($user_id,$email, $auth_code);
            if(empty($result['Data']))
            {
                $user['Mobile'] =  $mobile;
                $result['Data'] = $user;
            }
            $this->response($result, 200);
            exit;
        }

        //修改密码
        if(!empty($old_pwd) && !empty($new_pwd))
        {
            $result = $this->user_bll->setting_or_change_password($user_id,$new_pwd,$old_pwd,true);
            $this->response($result, 200);
            exit;
        }

        //设置密码
        if(!empty($pwd))
        {
            $result = $this->user_bll->setting_or_change_password($user_id,$pwd,'',false);
            $this->response($result, 200);
            exit;
        }

        $this->response(array('Result' => -1, 'Msg' => '参数非法'), 200);
        exit;
    }


    /**
     * @function　合并用户
     * @User: CaylaXu
     */
    function combined_user_put()
    {
        $user_id = $this->put('UserId');
        $combined_id = $this->put('CombinedId');
        $is_combined = $this->put('IsCombined');//1:合并 0：不合并
        $mobile = $this->put('Mobile');
        $email = $this->put('Email');
        if(empty($combined_id))
        {
            $this->response(array('Result' => -1, 'Msg' => '参数非法'), 200);
            exit;
        }

        $check_mobile = common::is_mobile($mobile);
        $check_email = common::is_email($email);
        if($check_mobile)
        {
            $result = $this->user_bll->update(array('Mobile'=>$mobile),array('Id'=>$user_id));
            if(!$result)
            {
                $this->response(array('Result' => -1, 'Msg' => '手机号修改失败'), 200);
                exit;
            }
        }
        else if($check_email)
        {
            $result = $this->user_bll->update(array('Email'=>$email),array('Id'=>$user_id));
            if(!$result)
            {
                $this->response(array('Result' => -1, 'Msg' => '邮箱修改失败'), 200);
                exit;
            }
        }
        else
        {
            $this->response(array('Result' => -1, 'Msg' => '参数非法'), 200);
            exit;
        }

        $is_combined = $is_combined == 1 ? true : false;
        $result = $this->user_bll->combined_user($combined_id,$user_id,$is_combined);

        if(!$result)
        {
            $this->response(array('Result' => -1, 'Msg' => '参数非法'), 200);
            exit;
        }

        $this->response(array('Result' => 0, 'Msg' => '修改成功'), 200);
        exit;
    }

	/**
	 * @function 绑定第三方登录
	 * @author Peter
	 */
	public function bound_user_post()
	{
		$user_id = intval($this->post('Id'));
		$type = strtolower((string)$this->post('Type'));
		$third_id = (string)$this->post('ThirdId');
		$nickname = (string)$this->post('NickName');
		$sex = (string)$this->post('Sex');

		if ($user_id <= 0 || !is_string($type) ||
			!in_array($type, array('wechat', 'weibo', 'qq')) ||
			strlen($third_id) > 63 || strlen($nickname) > 63)
		{
			$this->response(array('Result' => -1, 'Msg' => '参数非法'), 200);
			exit;
		}

		$this->load->model('bll/Rlt_third_user_bll', 'rtu_bll');

		$result = $this->rtu_bll->bind($user_id, $type, $third_id, $nickname, $sex);

		$this->response($result, 200);
		exit;
	}

	/**
	 * @function 解绑第三方登录
	 * @author Peter
	 */
	public function bound_user_put()
	{
		$type = (string)$this->put('Type');
		$user_id = intval($this->put('Id'));

		if (!is_string($type) ||
			!in_array($type, array('wechat', 'weibo', 'qq')) ||
			$user_id <= 0)
		{
			$this->response(array('Result' => -1, 'Msg' => '参数非法'), 200);
			exit;
		}

		$this->load->model('bll/Rlt_third_user_bll', 'rtu_bll');

		$result = $this->rtu_bll->unbind($user_id, $type);
		$response = $result ?
			array('Result' => 0, 'Msg' => '解绑成功') :
			array('Result' => -1, 'Msg' => '解绑失败');

		$this->response($response, 200);
		exit;
	}
}
