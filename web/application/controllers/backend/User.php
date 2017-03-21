<?php
/**
 * Created by PhpStorm.
 * User: cayla
 * Date: 2015/11/8
 * Time: 20:04
 */
class User extends MyController
{

    function __construct()
    {
        parent::__construct();
        $this->load->model('bll/group_bll');
        $this->load->model('bll/user_bll');
        $this->load->model('bll/rlt_project_user_bll');
        $this->load->model('bll/Rlt_task_user_bll');
        $this->load->library('upload_remote_servers',$this->config->item('upload_remote_servers'));
        $this->load->model('bll/system_config_bll');
        $this->load->model('bll/config_bll');
    }

    public function index()
    {
        //侧边栏显示所有团队
        $groups = $this->group_bll->select_info(array('Id', 'Title'), array('Status' => 1));

        $group_id = -1;
        if ($_GET && $_GET["GroupId"]) {
            $group_id = $_GET["GroupId"];
        }

        $this->hpf_smarty->assign('GroupId', $group_id);
        $this->hpf_smarty->assign('Groups',$groups);
        $this->hpf_smarty->assign('PageName',"成员");
        $this->hpf_smarty->display('backend/user/index.tpl');
    }

    /**
     * @function 分页获取学员详情
     * @author CaylaXu
     */
    public function user_list_get()
    {
        $page          = isset($_POST['Page']) ? $_POST['Page'] : 1;
        $limit         = isset($_POST['Rows']) ? $_POST['Rows'] : 10;
        $params = $_POST;
        $user_list = $this->user_bll->get_list($page - 1, $limit,$params);
        echo json_encode($user_list);
    }

    /**
     * @function 获取项目所属成员
     * @author CaylaXu
     */
    public function get_user_by_project_id()
    {
        $project_id = isset($_POST['ProjectId']) ? $_POST['ProjectId'] :0;

        if(empty($project_id))
        {
            $users =  $users = $this->user_bll->get_cooperators($this->user_id);
        }
        else
        {
            $users = $this->rlt_project_user_bll->query_by_project_id($project_id, 2);
        }

        if(!is_array($users))
        {
            common::return_json(-1,'系统繁忙','',true);
        }

        common::return_json(0,'获取成功',$users,true);
    }

    public function test()
    {
        $result = $this->user_bll->combined_user(20,43,true);
        if($result)
        {
            echo "合并成功";
        }
        else
        {
            echo "有幺蛾子";
        }
    }


    /**
     * @function 插入用户
     * @User: CaylaXu
     */
    function input_user()
    {
        $params = $_POST;
        $params['ProjectId'] = isset($params['ProjectId']) ? $params['ProjectId'] : 0;
        $params['TaskId'] = isset($params['TaskId']) ? $params['TaskId'] : 0;
        $params['Email'] = isset($params['Email']) ? $params['Email'] : 0;

        if(!$params)
        {
            common::return_json(-1,'参数非法','',true);
        }

        if(!common::is_email($params['Email']))
        {
            common::return_json(-1,'邮箱格式不正确','',true);
        }

        $email = $params['Email'];
        $name  = $this->user_bll->get_user_name_by_email($email);
        $avatar = common::get_first_char($email).'.jpg';

        //1、判断用户是否已存在
        $user_info = $this->user_bll->select_info(array(),array('Email'=>$email,'Status !='=>-1));

        if(!empty($user_info))
        {
            $user_info = $this->user_bll->get_user_by_id($user_info[0]['Id']);
            common::return_json(0,'成员添加成功',$user_info,true);
        }

        $insert_data =array(
            'Name' => $name,
            'Email' => $email,
            'Avatar' => $avatar,
            'Status' => 0
        );

        $user_id = $this->user_bll->create($insert_data);

        //邮件或短信通知用户注册
        {
            $url = 'http://'.$_SERVER["HTTP_HOST"].'/backend/login/register?Source=email';
            $host = $_SERVER["HTTP_HOST"];
            $content = <<<BODY
<html><head></head><body><table><tr><td bgcolor=#0373d6 height=54 align=center><table><tr><td align=center><img src=http://{$host}/resource/asset/img/email-logo.png width=71 height=54></td></tr></table></td></tr><tr><td height=227 align=center style="padding: 0 15px;"><table border=0 cellspacing=0 width=480><tr><td><table width=100% border=0 cellpadding=0><tr><td><table cellspacing=0 border=0 align=left><tr><td width=550 align=left valign=top><table width=100% border=0 cellspacing=0><tr><td align=left valign=top style="font-size:14px; color:#7b7b7b; line-height: 25px; padding: 20px 0px">您的同事{$this->user_info['Name']}邀请您加入APD
立即加入{$url}</td></tr></table></td></tr></table></td></tr></table></td></tr></table></td></tr></table></body></html>
BODY;
            common::send_email("注册邀请",$content,array($email));
        }

        if(!$user_id)
        {
            common::return_json(-1,'用户新增失败','',true);
        }

        //2、用户加入项目成员
//        if(is_numeric($params['ProjectId']) && $params['ProjectId']>0)
//        {
//            $rlt_project_id = $this->rlt_project_user_bll->create($params['ProjectId'],$user_id,1);
//
//            if(!$rlt_project_id)
//            {
//                common::return_json(-1,'项目添加成员失败','',true);
//            }
//        }
//
//        //3、建立用户任务关联
//        if(is_numeric($params['TaskId']) && $params['TaskId']>0)
//        {
//            $rlt_task_id = $this->Rlt_task_user_bll->create($params['TaskId'],$user_id,1);
//
//            if(!$rlt_task_id)
//            {
//                common::return_json(-1,'项目添加成员失败','',true);
//            }
//        }
//        $insert_data['Id'] = $user_id;
        $user_info = $this->user_bll->get_user_by_id($user_id);
        common::return_json(0,'成员添加成功',$user_info);
    }

    /**
     * @function 更新用户信息
     * @User: CaylaXu
     */
    public function update()
    {
        if ($_POST)
        {
            $data = array();
            if(isset($_POST['Name']))
            {
                $name = $this->input->post('Name');
                if(!common::check_string_length(1,75,$name))
                {
                    common::return_json(-1,'用户名不允许为空或超过25个字符','',true);
                }
                $data['Name'] = $name;
            }


            if(isset($_POST['Password'])&&isset($_POST['RePassword']))
            {
                $password = $this->input->post('Password');
                $re_password = $this->input->post('RePassword');

                if(empty($password) || empty($re_password))
                {
                    common::return_json(-1,'修改密码参数不允许为空','',true);
                }

                $user_info = $this->user_bll->select_info(array(),array('Id'=>$this->user_id,'Status !='=>-1));

                if(empty($user_info))
                {
                    common::return_json(-1,'用户不存在','',true);
                }

                if(isset($_POST['OldPassword']))
                {
                    $old_password = $this->input->post('OldPassword');
                    if(!common::check_password($user_info[0],md5($old_password)))
                    {
                        common::return_json(-1,'原始密码错误','',true);
                    }
                }

                if($password != $re_password)
                {
                    common::return_json(-1,'新密码输入不一致','',true);
                }

                $hash = common::hash(md5($password));
                $data['Password'] = $hash['hash'];
                $data['Salt'] = $hash['salt'];


            }

            if(empty($data))
            {
                common::return_json('0','参数非法','',true);
            }

            $result = $this->user_bll->update($data,array('Id'=>$this->user_id));

            if(!$result)
            {
                common::return_json(-1,'更新失败','',true);
            }

            $user_info = $this->user_bll->get_user_by_id($this->user_id);
            common::return_json('0','更新成功',$user_info,true);
        }

		$this->load->model('bll/Rlt_third_user_bll', 'rlt_third_user_bll');

        $user_info = $this->user_bll->get_user_by_id($this->user_id);
		$fields = 'Type, NickName';
		$where = array('UserId' => $this->user_id, 'Status' => 1);
		$third_user_info = $this->rlt_third_user_bll->fetch($fields, $where);
		$user_info = array_merge($user_info, array_column($third_user_info, 'NickName', 'Type'));
        $this->hpf_smarty->assign('Info',$user_info);
        $this->hpf_smarty->display('backend/user/info.tpl');
    }

    /**
     * @function 上传头像
     * @User: CaylaXu
     */
    function upload_avatar()
    {
        $config['upload_path'] = FCPATH .common::resources_relative_path('avatar','');
        $config['allowed_types'] = 'gif|jpg|png|swf|mp4';
        $config['max_size'] = '1024';   //10M
        $config['file_name']  = time();
        $this->load->library('Upload', $config);

        $callback_function = isset($_POST['CallbackFunction']) ? $_POST['CallbackFunction'] : '';
        $request_id = isset($_POST['RequestId']) ? $_POST['RequestId']:0;

        if (!$this->upload->do_upload('Img')) {
            echo $str = "<script>".$callback_function."(".$request_id.",-1,'".$this->upload->display_errors()."','','')</script>";
        }
        else {
            $upload_data = $this->upload->data();
            $file_name = $upload_data['file_name'];
            $file_name_new = Common::create_new_guid();
            $file_name_new.= $upload_data['file_ext'];
            rename($config['upload_path'].$file_name,$config['upload_path'].$file_name_new);
            //远程上传资源
            {
                $cdn_storage_dir = common::resources_relative_path('avatar','',TRUE);
                $local_img_path=  $config['upload_path'].$file_name_new;
                $result=$this->upload_remote_servers->curl_img($cdn_storage_dir, $local_img_path);
            }


            $img_src = common::resources_full_path('avatar',$file_name_new,'picture');
            echo $str = "<script>".$callback_function."(".$request_id.",0,'上传成功','".$img_src."','".$file_name_new."')</script>";
        }
    }

    /**
     * @function 剪切头像
     * @User: CaylaXu
     */
    function cut_avatar()
    {
        $check = array(
            'Image' => true,
            'X' => false,
            'Y' => false,
            'W' => true,
            'H' => true
        );
        $params = common::check_get_post($check,'post',false);
        if(!$params)
        {
            common::return_json(-1,'参数非法','',true);
        }

        //根据坐标与长宽剪切图片
        $upload_path = common::resources_relative_path('avatar','');
        $image = $upload_path.$params['Image'];
        $thumb_name = Common::create_new_guid().'.jpg';
        $thumb = $upload_path.$thumb_name;
        $cut = common::thumb($image,$thumb,$params['X'],$params['Y'],$params['W'],$params['H']);
        if(!$cut)
        {
            common::return_json(-1,'上传失败','',true);
        }

        //上传到cdn
        {
            $cdn_storage_dir = common::resources_relative_path('avatar','',TRUE);
            $result=$this->upload_remote_servers->curl_img($cdn_storage_dir, FCPATH.$thumb);
        }

        $result = $this->user_bll->update(array('Avatar'=>$thumb_name),array('Id'=>$this->user_id));
        if(!$result)
        {
            common::return_json(-1,'更新失败','',true);
        }

        common::return_json(0,'操作成功');
    }

    /**
     * @function 修改手机号
     * @User: CaylaXu
     */
    function change_mobile()
    {
        $mobile = $this->input->post('Mobile');
        $auth_code = $this->input->post('AuthCode');
        if(!common::is_mobile($mobile))
        {
            common::return_json(-1,'手机号格式不正确','',true);
        }
        //验证验证码是否正确
        $check_code = $this->redis_bll->my_check_mobile_and_verif($mobile, $auth_code,'apd_random');

        if(!$check_code)
        {
            common::return_json(-1,'验证码错误','',true);
        }
        $user_info = $this->user_bll->select_info(array(),array('Mobile'=>$mobile,'Status !='=>-1,'Id !='=>$this->user_id));

        if(!empty($user_info))
        {
            //邮箱不为空则被占用
            if(!empty($user_info[0]['Email']))
            {
                common::return_json(-1,'手机号被占用','',true);
            }
            else
            {
                $user_info = $this->user_bll->get_user_by_id($user_info[0]['Id']);
                $data = $user_info;
                $data['Result'] =  10010;//手机号合并用户
                common::return_json(0,'请合并用户',$data,true);
            }
        }

        $result = $this->user_bll->update(array('Mobile'=>$mobile),array('Id'=>$this->user_id));

        if(!$result)
        {
            common::return_json(-1,'修改失败',$user_info,true);
        }

        common::return_json(0,'修改成功',$user_info,true);
    }

    /**
     * @function 修改密码
     * @User: CaylaXu
     */
    function change_email()
    {
        $email = $this->input->post('Email');
        $auth_code = $this->input->post('AuthCode');
        if(!common::is_email($email))
        {
            common::return_json(-1,'邮箱格式不正确','',true);
        }
        //验证验证码是否正确
        $check_code = $this->redis_bll->my_check_mobile_and_verif($email, $auth_code,'apd_random');
//        $check_code = true;
        if(!$check_code)
        {
            common::return_json(-1,'验证码错误','',true);
        }
        $user_info = $this->user_bll->select_info(array(),array('Email'=>$email,'Status !='=>-1,'Id !='=>$this->user_id));

        if(!empty($user_info))
        {
            //邮箱不为空则被占用
            if(!empty($user_info[0]['Mobile']))
            {
                common::return_json(-1,'邮箱被占用','',true);
            }
            else
            {
                $user_info = $this->user_bll->get_user_by_id($user_info[0]['Id']);
                $data = $user_info;
                $data['Result'] =  10011;//邮箱合并用户
                common::return_json(0,'请合并用户',$data,true);
            }
        }

        $result = $this->user_bll->update(array('Email'=>$email),array('Id'=>$this->user_id));
        if(!$result)
        {
            common::return_json(-1,'修改失败','',true);
        }
        common::return_json(0,'修改成功','',true);
    }

    /**
     * @function 合并用户
     * @User: CaylaXu
     */
    function combined_user()
    {
        $combined_id = $this->input->post('CombinedId');
        $is_combined = $this->input->post('IsCombined');//1:合并 0：不合并
        $mobile = $this->input->post('Mobile');
        $email = $this->input->post('Email');
        if(empty($combined_id))
        {
            common::return_json(-1,'参数非法','',true);
        }

        $check_mobile = common::is_mobile($mobile);
        $check_email = common::is_email($email);
        if($check_mobile)
        {
            $result = $this->user_bll->update(array('Mobile'=>$mobile),array('Id'=>$this->user_id));
            if(!$result)
            {
                common::return_json(-1,'手机号修改失败','',true);
            }
        }
        else if($check_email)
        {
            $result = $this->user_bll->update(array('Email'=>$email),array('Id'=>$this->user_id));
            if(!$result)
            {
                common::return_json(-1,'邮箱修改失败','',true);
            }
        }
        else
        {
            common::return_json(-1,'参数非法','',true);
        }

        $is_combined = $is_combined == 1 ? true : false;
        $result = $this->user_bll->combined_user($combined_id,$this->user_id,$is_combined);
        if(!$result)
        {
            common::return_json(-1,'参数非法','',true);
        }
        common::return_json(0,'修改成功','',true);
    }

    /**
     * @function 获取合作过的人
     * @author Peter
     */
    public function get_cooperators()
    {
        if (!$this->input->is_ajax_request())
        {
            common::return_json(-1, '请求方法错误', '', TRUE);
        }

        $user_id = $this->user_id;

        if ($user_id <= 0)
        {
            common::return_json(-1,'参数非法','',true);
        }

        $users = $this->user_bll->get_cooperators($user_id);

        common::return_json(0, '获取成功' , $users, true);
    }

    /**
     * @function 获取相同邮箱后缀的人
     * @author Peter
     */
    public function get_similar_by_email()
    {
        if (!$this->input->is_ajax_request() || !$this->input->get())
        {
            common::return_json(-1, '请求方法错误', '', TRUE);
        }

        $email = (string)$this->input->get('Email');

        if (!Common::is_email($email))
        {
            common::return_json(-1, '邮箱非法', '', TRUE);
        }

        $suffix = substr($email, strpos($email, '@') + 1);

        $users = $this->user_bll->get_similar_by_email($suffix);

        common::return_json(0, '获取成功' , $users, true);
    }

    /**
     * @function 设置主题
     * @author Peter
     */
    public function set_theme()
    {
        if (!$this->input->is_ajax_request() || !$this->input->post())
        {
            common::return_json(-1, '请求方法错误', '', TRUE);
        }

        $theme_id = intval($this->input->post('ThemeId'));

        if ($theme_id <= 0)
        {
            common::return_json(-1, '参数非法', '', TRUE);
        }
        $where = array(
            'Id' => $theme_id,
            'Type' => 1,
        );
        $fields = 'Id, Value, Unicode';
        $theme = $this->system_config_bll->select($where, $fields);
        if (!$theme)
        {
            common::return_json(-1, '该主题不存在', '', TRUE);
        }
        $theme = $theme[0];

        $where = array(
            'UserId' => $this->user_id,
            'Type' => 2,
        );
        $config = $this->config_bll->select($where);
        if (!$config)
        {
            $create = array(
                'Type' => 2,
                'UserId' => $this->user_id,
                'Value' => array(
                    'ThemeId' => $theme['Unicode'],
                ),
            );
            if (!$this->config_bll->create($create))
            {
                common::return_json(-1, '设置失败', '', TRUE);
            }
        }
        else
        {
            $update = array(
                'Value' => array(
                    'ThemeId' => $theme['Unicode'],
                ),
            );
            if (!$this->config_bll->update($update, $config['Id']))
            {
                common::return_json(-1, '设置失败', '', TRUE);
            }
        }

        unset($theme['Value']);
        unset($theme['Unicode']);
        common::return_json(0, '设置成功', $theme, TRUE);
    }

	/**
	 * @function 解绑第三方登录
	 * @author Peter
	 */
	public function unbind()
	{
		if (!$this->input->is_ajax_request() || !$this->input->post())
		{
			common::return_json(-1, '请求方法错误', '', TRUE);
		}

		$type = $this->input->post('Type');
		$user_id = $this->user_id;

		if (!is_string($type) || !in_array($type, array('wechat', 'weibo', 'qq')))
		{
			common::return_json(-1, '参数非法', '', TRUE);
		}

		$this->load->model('bll/Rlt_third_user_bll', 'rtu_bll');
		$result = $this->rtu_bll->unbind($user_id, $type);
		if ($result)
		{
			common::return_json(0, '解绑成功', '', TRUE);
		}
		else
		{
			common::return_json(-1, '解绑失败', '', TRUE);
		}
	}

	/**
	 * @function 邀请项目成员
	 * @author Peter
	 */
	public function invite_to_project()
	{
		if (!$this->input->is_ajax_request() || !$this->input->get())
		{
			common::return_json(-1, '请求方法错误', '', TRUE);
		}

		$user_name = (string)$this->input->get('UserName');
		$contact = (string)$this->input->get('Contact');
		$project_id = intval($this->input->get('ProjectId'));
		$role = intval($this->input->get('Role'));

		if (strlen($user_name) <= 0 || strlen($user_name) > 255 ||
			strlen($contact) <= 0 || strlen($contact) > 255 ||
			(!common::is_mobile($contact) && !common::is_email($contact)) ||
			!in_array($role, array(1, 3)))
		{
			common::return_json(-1, '参数非法', '', TRUE);
		}

		if (common::is_mobile($contact))
		{
			$type = 1;
		}
		else if(common::is_email($contact))
		{
			$type = 2;
		}

		$this->load->model('bll/User_bll', 'user_bll');

		$result = $this->user_bll->invite_to_project($user_name, $contact, $type, $project_id, $role);

		if (!$result)
		{
			common::return_json(-1, '邀请失败，请稍后再试', '', TRUE);
		}
		else
		{
			common::return_json(0, '邀请成功', $result, TRUE);
		}
	}

	public function invite_to_task()
	{
		if (!$this->input->is_ajax_request() || !$this->input->get())
		{
			common::return_json(-1, '请求方法错误', '', TRUE);
		}

		$user_name = (string)$this->input->get('UserName');
		$contact = (string)$this->input->get('Contact');
		$project_id = intval($this->input->get('ProjectId'));
		$task_id = intval($this->input->get('TaskId'));
		$role = intval($this->input->get('Role'));

		if (strlen($user_name) <= 0 || strlen($user_name) > 255 ||
			strlen($contact) <= 0 || strlen($contact) > 255 ||
			(!common::is_mobile($contact) && !common::is_email($contact)) ||
			!in_array($role, array(1, 3)))
		{
			common::return_json(-1, '参数非法', '', TRUE);
		}

		if (common::is_mobile($contact))
		{
			$type = 1;
		}
		else if(common::is_email($contact))
		{
			$type = 2;
		}

		$this->load->model('bll/User_bll', 'user_bll');


		$result = $this->user_bll->invite_to_task($user_name, $contact, $type, $project_id, $task_id, $role);

		if (!$result)
		{
			common::return_json(-1, '邀请失败，请稍后再试', '', TRUE);
		}
		else
		{
			common::return_json(0, '邀请成功', $result, TRUE);
		}

	}
}
