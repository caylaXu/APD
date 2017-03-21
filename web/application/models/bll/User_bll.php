<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Created by PhpStorm.
 * User: CaylaXu <caylaxu@motouch.cn>
 * Date: 2015/11/5
 * Time：19:51
 */
class User_bll extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('dal/db/user_model');
        $this->load->model('dal/db/group_model');
        $this->load->model('bll/common_bll');
        $this->load->model('dal/db/project_model');
        $this->load->model('dal/db/task_model');
        $this->load->model('dal/db/RltProjectUser_model');
        $this->load->model('dal/db/RltTaskUser_model');
        $this->load->model('dal/db/RltThirdUser_model');
        $this->load->model('bll/Rlt_third_user_bll');
    }

    public function select_info(array $select,array $where,$flag = true)
    {
        return $this->user_model->select_info($select,$where,$flag);
    }

    public function update(array $data,array $where)
    {
        $data['Method'] = 1;
        $data['Modified'] = $this->common_bll->get_max_modified();
        return $this->user_model->update_info($data,$where);
    }

    public function create(array $params)
    {

        if(empty($params) || !is_array($params))
        {
            return false;
        }
        
        $params['Method'] = 0;
        $params['Modified'] = $this->common_bll->get_max_modified();
        $params['UniCode'] = common::create_uuid();
        $user_id =  $this->user_model->create($params);

        if(is_numeric($user_id))
        {
            $this->load->model('bll/Task_bll');
            $this->Task_bll->initial_task("这是一条分配给你的任务",$user_id);
            $this->Task_bll->initial_task("点击【新建任务】，试试创建一条新的任务",$user_id);
        }

        return $user_id;
    }

    public function delete($id)
    {
        $modified = $this->common_bll->get_max_modified();
        return $this->user_model->delete_info(array('Id'=>$id),$modified);
    }

    /**
     * @function 分页请求用户列表
     * @author CaylaXu
     */
    public function get_list($page = 0, $limit = 15, $params = array())
    {
        $result = $this->user_model->get_list( $page, $limit, $params);

        if(isset($result['Users']) && !empty($result['Users']))
        {
            foreach($result['Users'] as $k=>&$v)
            {
                $v['Groups'] = $this->group_model->get_group_by_user_id($v['Id']);
            }
        }

        return $result;
    }

    public function get_user_name_by_email($email)
    {
        $name = 'anonymity';
        if(preg_match('/([\s\S]*?)@/',$email,$match))
        {
            if(isset($match[1]))
            {
                $name = $match[1];
            }
        }
        return $name;
    }

    public function get_user_by_mobile_or_email($account)
    {
        $info = $this->user_model->get_user_by_mobile_or_email($account);
        if(empty($info))
        {
            return $info;
        }

        $info['Avatar'] = common::resources_full_path('avatar',$info['Avatar'],'picture');

        return $info;
    }

    public function get_user_by_id($id)
    {
        $select = array(
            'Id as UserId',
            'Name as UserName',
            'Avatar',
            'Email',
            'Mobile',
            'Status',
			'Password',
        );

        $info = $this->user_model->select_info($select,array('Id'=>$id));

        if(empty($info))
        {
            return $info;
        }

        $info = $info[0];
        $info['AvatarName'] = $info['Avatar'];
        $info['Avatar'] = common::resources_full_path('avatar',$info['Avatar'],'picture');
        $info['Email'] = empty($info['Email']) ? '无' : $info['Email'];
        $info['Mobile'] = empty($info['Mobile']) ? '无' : $info['Mobile'];
		$info['HasPwd'] = $info['Password'] ? 1 : 0;
		unset($info['Password']);
        return $info;
    }


    /**
     * @function 合并用户逻辑
     * @User: CaylaXu
     * @param $user_id_old 旧有的用户Id
     * @param string $user_id_new 要合并的用户Id
     * @param bool $is_combined 是否需要合并
     * @return bool
     */
    public function combined_user($user_id_old,$user_id_new = 0,$is_combined = false)
    {
        //将原有用户删除
        $del_result = $this->delete($user_id_old);

        //将原有用户第三方删除
        $del_third = $this->Rlt_third_user_bll->delete(array('UserId'=>$user_id_old));

        //如果无需合并则直接返回
        if($is_combined == false)
        {
            return $del_result;
        }

        $this->load->model('bll/Project_bll');
        $this->load->model('bll/Rlt_project_user_bll');
        $this->load->model('bll/Task_bll');

        $this->db->trans_start();
        //需要合并则将原用户的管理数据全部更改为现有用户数据
        //1、更新项目表ProjectManageId,CreatorId
        $this->Project_bll->update_where(array('ProjectManagerId'=>$user_id_new),array('ProjectManagerId'=>$user_id_old));
        $this->Project_bll->update_where(array('CreatorId'=>$user_id_new),array('CreatorId'=>$user_id_old));
        //2、更新用户项目关联表UserId
        $this->Rlt_project_user_bll->update_where(array('UserId'=>$user_id_new),array('UserId'=>$user_id_old));
        //3、更新任务表CreatorId
        $this->Task_bll->update(array('CreatorId'=>$user_id_new),array('CreatorId'=>$user_id_old));
        //4、更新任务用户关联表UserId
        $this->Rlt_task_user_bll->update_where(array('UserId'=>$user_id_new),array('UserId'=>$user_id_old));
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE)
        {
            return false;
        }
        return true;
    }

    /**
     * @function 获取需要同步的用户
     * @author Peter
     * @param $anchor
     * @return mixed
     */
    public function get_sync_users($anchor = 0)
    {
        $users = $this->user_model->select_info(array(), array('Modified >' => $anchor));

        array_walk($users, function (&$val, $key, $table_name)
        {
            $val['Table'] = $table_name;
        }, 'Users');

        return $users;
    }

	/**
	 * @function 获取合作过的人
	 * @author Peter
	 * @param $user_id
	 * @return mixed
	 */
	public function get_cooperators($user_id)
	{
		$this->load->model('db/dal/RltProjectUser_model', 'rpu_dal');
		$this->load->model('db/dal/User_model', 'user_dal');

		$where = array('Id' => $user_id);
		$user = $this->user_dal->p_exist($where);
		if (!$user)
		{
			return array();
		}

		//参与过的项目
		$result = $this->rpu_dal
			->p_select('ProjectId')
			->p_where(array('UserId' => $user_id))
			->p_fetch();
		$project_ids = array_unique(array_column($result, 'ProjectId'));
		if (!$project_ids)
		{
			return array();
		}

		//合作过的人
		$result = $this->rpu_dal
			->p_select('UserId')
			->p_where_in('ProjectId', $project_ids)
			->p_fetch();
		$user_ids = array_unique(array_column($result, 'UserId'));
		if (($index = array_search($user_id, $user_ids)) === FALSE)
		{
			$user_ids[] = $user_id;
		}
		$users = $this->user_dal
			->p_select('Id UserId, Name UserName, Email, Avatar, Status, Email')
			->p_where(array('Status >=' => 0))//非已删除状态
			->p_where_in('Id', $user_ids)
			->p_fetch();
		array_walk($users, function (&$user) {
			$user['Avatar'] = common::resources_full_path('avatar', $user['Avatar'], 'picture');
		});

		return $users;
	}

	/**
	 * @function 获取相同邮箱后缀的人
	 * @author Peter
	 * @param string $suffix
	 * @return mixed
	 */
    public function get_similar_by_email($suffix = '')
    {
		$this->load->model('db/dal/User_model', 'user_dal');

		$users = $this->user_dal
			->p_select('Id UserId, Name UserName, Avatar, Email, Mobile')
			->p_like('Email', $suffix, 'before')
			->p_where(array('Status' => 1))
			->p_fetch();

		array_walk($users, function (&$user) {
			$user['AvatarName'] = $user['Avatar'];
			$user['Avatar'] = common::resources_full_path('avatar', $user['Avatar'], 'picture');
			$user['Email'] = $user['Email'] ? $user['Email'] : '无';
			$user['Mobile'] = $user['Mobile'] ? $user['Mobile'] : '无';
		});
        
        return $users;
    }


    /**
     * @function 社会化组件注册登录接口
     * @User: CaylaXu
     * @param array $params
     * @return bool
     */
    public function social_login(array $params)
    {
//        $params = array(
//            'Type' => 'weibo',
//            'ThirdId' => '12345632567',
//            'NickName' => '测试啊',
//            'Avatar' => 'http://tva1.sinaimg.cn/crop.4.296.741.741.50/ed9661b1jw8f25q1uh0t2j20ku112tbq.jpg',
//            'Sex' => 'm',
//        );

        //1、参数检查
        $type = isset($params['Type']) ? $params['Type'] : '';
        $type = strtolower($type);
        $third_id = isset($params['ThirdId']) ? $params['ThirdId']:'';
        $nick_name = isset($params['NickName']) ? $params['NickName']:'';
        $avatar = isset($params['Avatar']) ? $params['Avatar']:'';
        $sex = isset($params['Sex']) ? $params['Sex']:'';

        switch ($type)
        {
            case 'wechat':
                $third_id = isset($params['unionid']) ? $params['unionid']:$third_id;
                $nick_name = isset($params['nickname']) ? $params['nickname']:$nick_name;
                $avatar = isset($params['headimgurl']) ? $params['headimgurl']:$avatar;
                $sex = isset($params['sex']) ? $params['sex']:$sex;
                break;

            case 'qq':
                $third_id = isset($params['openid']) ? $params['openid']:$third_id;
                $nick_name = isset($params['nickname']) ? $params['nickname']:$nick_name;
                $avatar = isset($params['figureurl']) ? $params['figureurl']:$avatar;
                $sex = isset($params['gender']) ? $params['gender']:$sex;
                break;

            case 'weibo':
                $third_id = isset($params['uid']) ? $params['uid']:$third_id;
                $nick_name = isset($params['name']) ? $params['name']:$nick_name;
                $avatar = isset($params['profile_image_url']) ? $params['profile_image_url']:$avatar;
                $sex = isset($params['gender']) ? $params['gender']:$sex;
                break;
        }

        if(empty($third_id))
        {
            return false;
        }

        $third_info = $this->RltThirdUser_model->select_info(array(),array('ThirdId' => $third_id,'Type'=>$type,'Status'=>1),false);

        if(!empty($third_info))//已经注册过的直接返回
        {
            return $third_info['UserId'];
        }//第三方登录新建用户
        else
        {
            $data['Name'] = $nick_name;
            $data['RegistrationDate'] = time();

            //@todo 下载头像到本地来需要时间，可以考虑异步
            $file_name = Common::create_new_guid();
            $file_name .= '.jpg';
//            $file_path = FCPATH . "resource/upload/Avatar/" . $file_name;
//            $img = file_get_contents($avatar);
//            file_put_contents($file_path,$img);
            $result = common::put_file_from_url_content($avatar,$file_name,FCPATH . "resource/upload/avatar/");

            if($result)
            {
                $data['Avatar'] = $file_name;
            }
            else
            {
                $data['Avatar'] = 'default.jpg';
            }
            //@todo 需要开启事务
            //1、创建用户
            $user_id = $this->create($data);

            if(!$user_id)
            {
                return false;
            }


            //2、创建关联
            $third_data['UserId'] = $user_id;
            $third_data['ThirdId'] = $third_id;
            $third_data['NickName'] = $nick_name;
            $third_data['Type'] = $type;
            $rlt_id = $this->Rlt_third_user_bll->create($third_data);
            if(!$rlt_id)
            {
                return false;
            }

            return $user_id;
        }
    }

	/**
	 * @function 权限鉴定
	 * @param $user_id
	 * @param int $obj_id
	 * @param int $type（1 => 项目，2 => 任务）
	 * @param int $power（1 => 增， 2 => 删，3 => 改，4 => 查）
	 * @return bool
	 */
	public function auth($user_id, $obj_id = 0, $type = 1, $power = 1)
	{
		if ($type === 1)
		{//项目鉴权
			//是否与项目有关联
			$this->load->model('dal/db/project_model', 'project_dal');
			$this->load->model('dal/db/RltProjectUser_model', 'rpu_dal');

			$fields = 'ProjectId';
			$where = array('UserId' => $user_id, 'Status !=' => -1);
			$result = $this->rpu_dal
				->p_select($fields)
				->p_where($where)
				->p_fetch();
			$project_ids = array_unique(array_column($result, 'ProjectId'));
			if (in_array($obj_id, $project_ids))
			{
				return TRUE;
			}

			//查出子项目
			$fields = array('Id');
			$result = $this->project_dal
				->p_select($fields)
				->p_where_in('ParentId', $project_ids)
				->p_fetch();
			$sub_ids = array_unique(array_column($result, 'Id'));
			if ($sub_ids)
			{
				foreach ($sub_ids as $sub_id)
				{
					if (in_array($sub_id, $project_ids))
					{
						return TRUE;
					}
				}
			}

			return FALSE;
		}
		else if ($type === 2)
		{//任务鉴权
			//是否属于某项目
			$this->load->model('dal/db/task_model', 'task_dal');
			$fields = 'ProjectId';
			$where = array('Id' => $obj_id);
			$task = $this->task_dal
				->p_select($fields)
				->p_where($where)
				->p_fetch(TRUE);
			if ($task)
			{//属于某项目
				//是否与项目有关联
				$this->load->model('dal/db/RltProjectUser_model', 'rpu_dal');
				$fields = 'ProjectId';
				$where = array('UserId' => $user_id, 'Status =' => 1);
				$project = $this->rpu_dal
					->p_select($fields)
					->p_where($where)
					->p_fetch();
				$project_ids = array_unique(array_column($project, 'ProjectId'));
				if (in_array($task['ProjectId'], $project_ids))
				{
					return TRUE;
				}
			}

			$where = array('Id' => $obj_id, 'CreatorId' => $user_id);
			$result = $this->task_dal->p_exist($where);
			if ($result)
			{//创建者
				return TRUE;
			}

			$this->load->model('dal/db/RltTaskUser_model', 'rlt_task_user_dal');
			$fields = 'Type';
			$where = array('TaskId' => $obj_id, 'UserId' => $user_id);
			$task_user = $this->rlt_task_user_dal
				->p_select($fields)
				->p_where($where)
				->p_fetch();
			$user_types = array_unique(array_column($task_user, 'Type'));
			if (in_array(1, $user_types) || in_array(2, $user_types))
			{//责任人，主责任人
				return TRUE;
			}

			return FALSE;
		}
	}

    /**
     * @function
     * @User: CaylaXu
     * @param $user_id
     * @param $avatar
     */
    function update_avatar($user_id, $avatar)
    {
        //base64上传
        $file_name = Common::create_new_guid() . ".png";
        $full_save_path = FCPATH . "resource/upload/avatar/". $file_name;
        Common::save_base64_jpeg_data_to_files($avatar, $full_save_path);
        $result = $this->update(array('Avatar'=>$file_name),array('Id'=>$user_id));
        return $result;
    }

    /**
     * @function 设置或更换手机号
     * @User: CaylaXu
     * @param $mobile 手机号
     * @param $auth_code 验证码
     * @return array 返回码、信息、数据
     */
    function setting_or_change_mobile($user_id,$mobile,$auth_code)
    {
        if(!common::is_mobile($mobile))
        {
            return array('Result'=>-1,'Msg'=>'手机号格式不正确','Data'=>array());
        }

        //验证验证码是否正确
        $check_code = $this->redis_bll->my_check_mobile_and_verif($mobile, $auth_code,'apd_bound_random');

        if(!$check_code)
        {
            return array('Result'=>-1,'Msg'=>'验证码错误','Data'=>array());
        }

        $user_info = $this->select_info(array(),array('Mobile'=>$mobile,'Status !='=>-1,'Id !='=>$user_id));
        if(!empty($user_info))
        {
            //邮箱不为空则被占用
            if(!empty($user_info[0]['Email']))
            {
                return array('Result'=>-1,'Msg'=>'手机号被占用','Data'=>array());
            }
            else
            {
                $user_info = $this->get_user_by_id($user_info[0]['Id']);
                return array('Result'=>10010,'Msg'=>'请合并用户','Data'=>$user_info);
            }
        }

        $result = $this->user_bll->update(array('Mobile'=>$mobile),array('Id'=>$user_id));

        if(!$result)
        {
            return array('Result'=>-1,'Msg'=>'修改失败','Data'=>array());
        }

        return array('Result'=>0,'Msg'=>'修改成功','Data'=>array());
    }

    /**
     * @function 设置或修改邮箱
     * @User: CaylaXu
     * @param $email
     * @param $auth_code
     * @return array
     */
    public function setting_or_change_email($user_id,$email,$auth_code)
    {
        if(!common::is_email($email))
        {
            return array('Result'=>-1,'Msg'=>'邮箱格式不正确','Data'=>array());
        }
        //验证验证码是否正确
        $check_code = $this->redis_bll->my_check_mobile_and_verif($email, $auth_code,'apd_bound_random');

        if(!$check_code)
        {
            return array('Result'=>-1,'Msg'=>'验证码错误','Data'=>array());
        }

        $user_info = $this->user_bll->select_info(array(),array('Email'=>$email,'Status !='=>-1,'Id !='=>$user_id));
        if(!empty($user_info))
        {
            //邮箱不为空则被占用
            if(!empty($user_info[0]['Mobile']))
            {
                return array('Result'=>-1,'Msg'=>'邮箱被占用','Data'=>array());
            }
            else
            {
                $user_info = $this->user_bll->get_user_by_id($user_info[0]['Id']);
                return array('Result'=>10011,'Msg'=>'请合并用户','Data'=>$user_info);
            }
        }

        $result = $this->user_bll->update(array('Email'=>$email),array('Id'=>$user_id));

        if(!$result)
        {
            return array('Result'=>-1,'Msg'=>'修改失败','Data'=>array());
        }

        return array('Result'=>0,'Msg'=>'修改成功','Data'=>array());
    }


    public function setting_or_change_password($user_id,$password,$old_password,$flag = false)
    {
        $user_info = $this->user_bll->select_info(array(),array('Id'=>$user_id,'Status !='=>-1));

        if(empty($user_info))
        {
            return array('Result'=>-1,'Msg'=>'用户不存在','Data'=>array());
        }

        if($flag)//需要验证旧密码
        {
            if(!common::check_password($user_info[0],md5($old_password)))
            {
                return array('Result'=>-1,'Msg'=>'原始密码错误','Data'=>array());
            }
        }

        $hash = common::hash(md5($password));
        $data['Password'] = $hash['hash'];
        $data['Salt'] = $hash['salt'];

        $result = $this->update($data,array('Id'=>$user_id));

        if(!$result)
        {
            return array('Result'=>-1,'Msg'=>'更新失败','Data'=>array());
        }

        return array('Result'=>0,'Msg'=>'更新成功','Data'=>array());
    }

	/**
	 * @function 邀请项目成员
	 * @author Peter
	 * @param $user_name
	 * @param $contact
	 * @param $type
	 * @param $project_id
	 * @return array|bool
	 */
	public function invite_to_project($user_name, $contact, $type, $project_id, $role)
	{
		$this->load->model('db/dal/User_model', 'user_dal');
		$this->load->model('bll/Rlt_project_user_bll', 'rpu_bll');

		$user = $this->user_dal
			->p_select('Id UserId, Name UserName, Email, Mobile, Status, Avatar')
			->p_where(array('Mobile' => $contact, 'Status' => 1))
			->p_or_where(array('Email' => $contact))
			->p_fetch(TRUE);
		if ($user)
		{
			$user['Avatar'] = common::resources_full_path('avatar', $user['Avatar'], 'picture');
		}
		else
		{
			$insert = array(
				'Name' => $user_name,
				'Avatar' => common::get_first_char($user_name).'.jpg',
				'Status' => 0,	//未激活
				'UniCode' => common::create_uuid(),
			);
			if ($type === 1)
			{
				$insert['Mobile'] = $contact;
				$insert['Email'] = NULL;
			}
			else if ($type === 2)
			{
				$insert['Mobile'] = NULL;
				$insert['Email'] = $contact;
			}
			$result = $this->user_dal->p_insert($insert);

			if (!is_numeric($result))
			{
				return FALSE;
			}

			$user = array();
			$user['UserId'] = $result;
			$user['UserName'] = $insert['Name'];
			$user['Email'] = $insert['Email'];
			$user['Mobile'] = $insert['Mobile'];
			$user['Status'] = $insert['Status'];
			$user['Avatar'] = common::resources_full_path('avatar', $insert['Avatar'], 'picture');
		}

		if (!$project_id)
		{//创建项目时的邀请
			return $user;
		}

		//与项目建立关联
		if ($role === 1)
		{//项目经理
			$result = $this->rpu_bll->add_manager($project_id, $user['UserId']);
		}
		else if ($role === 3)
		{//关注人
			$result = $this->rpu_bll->add_follower($project_id, $user['UserId']);
		}

		return $result ? $user : FALSE;
	}

	/**
	 * @function 邀请任务成员
	 * @author Peter
	 * @param $user_name
	 * @param $contact
	 * @param $type
	 * @param $project_id
	 * @param $task_id
	 * @param $role
	 * @return array|bool
	 */
	public function invite_to_task($user_name, $contact, $type, $project_id, $task_id, $role)
	{
		$this->load->model('db/dal/User_model', 'user_dal');
		$this->load->model('bll/Rlt_project_user_bll', 'rpu_bll');
		$this->load->model('bll/Rlt_task_user_bll', 'rtu_bll');

		$user = $this->user_dal
			->p_select('Id UserId, Name UserName, Email, Mobile, Status, Avatar')
			->p_where(array('Mobile' => $contact, 'Status' => 1))
			->p_or_where(array('Email' => $contact))
			->p_fetch(TRUE);
		if ($user)
		{
			$user['Avatar'] = common::resources_full_path('avatar', $user['Avatar'], 'picture');
		}
		else
		{
			$insert = array(
				'Name' => $user_name,
				'Avatar' => common::get_first_char($user_name).'.jpg',
				'Status' => 0,	//未激活
				'UniCode' => common::create_uuid(),
			);
			if ($type === 1)
			{
				$insert['Mobile'] = $contact;
				$insert['Email'] = NULL;
			}
			else if ($type === 2)
			{
				$insert['Mobile'] = NULL;
				$insert['Email'] = $contact;
			}
			$result = $this->user_dal->p_insert($insert);

			if (!is_numeric($result))
			{
				return FALSE;
			}

			$user = array();
			$user['UserId'] = $result;
			$user['UserName'] = $insert['Name'];
			$user['Email'] = $insert['Email'];
			$user['Mobile'] = $insert['Mobile'];
			$user['Status'] = $insert['Status'];
			$user['Avatar'] = common::resources_full_path('avatar', $insert['Avatar'], 'picture');
		}

		//与项目建立关联
		if ($project_id && $role === 1)
		{
			$this->load->model('bll/Rlt_project_user_bll', 'rpu_bll');
			$result = $this->rpu_bll->add_member($project_id, $user['UserId']);
			if (!$result)
			{
				return FALSE;
			}
		}

		if (!$task_id)
		{//在创建任务时邀请
			return $user;
		}

		//与任务建立关联
		if ($role === 1)
		{//责任人
			$result = $this->rtu_bll->add_director($task_id, $user['UserId']);
		}
		else if ($role === 3)
		{//关注人
			$result = $this->rtu_bll->add_follower($task_id, $user['UserId']);
		}

		return $result ? $user : FALSE;
	}

	/**
	 * @function 用户登录
	 * @author Peter
	 * @param $user_id
	 */
	public function login($user_id)
	{
		$this->input->set_cookie("login_time", time(), 86400 * 30);
		$this->input->set_cookie("user_id" . $this->config->item('SystemName'), $user_id, 86400 * 30);
		$this->input->set_cookie("login_key", md5($user_id . time() . $this->config->item('SystemName')), 86400 * 30);
	}

	/**
	 * @function 能否进入项目
	 * @author Peter
	 * @param $user_id
	 * @param $project_id
	 * @return bool
	 */
	public function can_enter($user_id, $project_id)
	{
		$this->load->model('dal/db/Project_model', 'project_dal');
		$this->load->model('dal/db/RltProjectUser_model', 'rpu_dal');
		$this->load->model('dal/db/Task_model', 'task_dal');
		$this->load->model('dal/db/RltTaskUser_model', 'rtu_dal');

		$where = array('Id' => $project_id, 'Status !=' => -1);
		$project = $this->project_dal->p_exist($where);
		if (!$project)
		{
			return FALSE;
		}

		//是项目成员
		$fields = 'UserId';
		$where = array('ProjectId' => $project_id, 'Status' => 1);
		$project_user = $this->rpu_dal
			->p_select($fields)
			->p_where($where)
			->p_fetch();
		$user_ids = array_unique(array_column($project_user, 'UserId'));
		if (in_array($user_id, $user_ids))
		{
			return TRUE;
		}

		//是任务成员
		$fields = 'Id';
		$where = array('ProjectId' => $project_id, 'Status' => 1);
		$result = $this->task_dal
			->p_select($fields)
			->p_where($where)
			->p_fetch();
		$task_ids = array_column($result, 'Id');
		if (!$task_ids)
		{
			return FALSE;
		}
		$fields = 'UserId';
		$where = array('Status' => 1, 'Type !=' => 3);
		$task_user = $this->rtu_dal
			->p_select($fields)
			->p_where($where)
			->p_where_in('TaskId', $task_ids)
			->p_fetch();
		$user_ids = array_unique(array_column($task_user, 'UserId'));
		if (in_array($user_id, $user_ids))
		{
			return TRUE;
		}
		else
		{
			return FALSE;
		}
	}
}