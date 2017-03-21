<?php
require_once 'MyRestController.php';
require_once 'MySecurityController.php';
class MyController extends MySecurityController
{
    public $user_id;
    public $user_name;
    public $user_info;
    public $user_theme;

    public function __construct()
    {
        parent::__construct();
        $this->init_load();
        $this->check_login();
        $this->init_assign();
		$this->_auth();
    }

    /**
     * @function 加载必要文件
     * @author CaylaXu
     */
    function init_load()
    {
        $this->load->add_package_path(APPPATH . "../phplibs/hpf_smarty/");
        $this->load->library("hpf_smarty");

        //引入错误捕捉
        require_once ( APPPATH . "../phplibs/hpf_exception/libraries/set_my_exception_handler.php" );

        $this->load->helper('cookie');
    }

    /**
     * @function 检测登录
     * @author CaylaXu
     */
    function check_login()
    {
        if (strpos($_SERVER['REQUEST_URI'], 'login'))
        {
            return;
        }

        $user_id = get_cookie("user_id" . $this->config->item('SystemName'));
        $this->load->model('bll/user_bll');
        $user_info = $this->user_bll->select_info(array(), array(
            'Id' => $user_id,
            'Status' => 1
        ));

        if (!empty($user_info))
        {
            $login_time = get_cookie("login_time");
            $login_key = get_cookie("login_key");
            $md5_key = md5($user_id . $login_time . $this->config->item('SystemName'));
            if ($md5_key != $login_key)
            {
                header("Location:backend/login/login");
                exit;
            }
            else
            {
                $this->user_id = $user_info[0]['Id'];
                $this->user_name = $user_info[0]['Name'];
                $this->user_info = $user_info[0];

                //获取用户配置（主题）
                $this->load->model('bll/config_bll');
                $this->load->model('bll/system_config_bll');

                $this->user_theme = array();

                $where = array(
                    'UserId' => $this->user_id,
                    'Type' => 2,
                );
                $fields = 'Value';
                $config = $this->config_bll->select($where, $fields);
                if (!$config)
                {
                    return ;
                }
                $value = json_decode($config['Value'], TRUE);
                if (!isset($value['ThemeId']))
                {
                    return ;
                }
                $theme_unicode = $value['ThemeId'];

                //获取主题
                $where = array(
                    'Type' => 1,
                    'Status' => 1,
                    'Method !=' => -1,
                    'Unicode' => $theme_unicode,
                );
                $fields = 'Id, Value';
                $theme = $this->system_config_bll->select($where, $fields);
                if (!$theme)
                {
                    return ;
                }
                $theme = $theme[0];
                $this->user_theme['ThemeId'] = $theme['Id'];
                $this->user_theme['Theme'] = $theme['Theme'];
                $this->user_theme['Color'] = $theme['Color'];
                $this->user_theme['BgImg'] = $theme['BgImg'];
            }
        }
        else
        {
            header("Location:/backend/login/login");
            exit;
        }
    }

    function init_assign()
    {
        $this->user_info['Avatar'] = common::resources_full_path('avatar', $this->user_info['Avatar'], 'picture');
        $this->hpf_smarty->assign('UserInfo', $this->user_info);
        $this->hpf_smarty->assign('UserId', $this->user_id);
        $this->hpf_smarty->assign('UserName', $this->user_name);
        $this->hpf_smarty->assign('UserAvatar', $this->user_info['Avatar']);
        $this->hpf_smarty->assign('UserTheme', $this->user_theme);

        $file_domain = common::resources_full_path('', '', 'file');
        $pic_domain = common::resources_full_path('', '', 'picture');
        $common_domain = common::resources_full_path('common_js', '', 'file');
        $this->hpf_smarty->assign('FileUrl', $file_domain . "/asset");
        $this->hpf_smarty->assign('PicUrl', $pic_domain);
        $this->hpf_smarty->assign('CommonUrl', $common_domain);
    }

	/**
	 * @function 鉴权
	 * @user Peter
	 * @description $arr_power（1 => 增， 2 => 删，3 => 改，4 => 查）
	 */
	private function _auth()
	{
		//当前控制器、方法
		$cur_controller = strtolower($this->router->class);
		$cur_method = strtolower($this->router->method);

		//权限列表
		$arr_power = array(
			'project'	=> array(
				'edit'				=> 3,
				'delete'			=> 2,
				'project_list_get'	=> 4,
				'add_rlt_user'		=> 3,
				'consolidated_project'	=> 3,
				'update_members'	=> 3,
			),
			'task'		=> array(
				'copy'				=> 1,
				'delete'			=> 2,
				'change_progress'	=> 3,
				'add_rlt_user'		=> 3,
				'move_left'			=> 3,
				'move_right'		=> 3,
				'set_status'		=> 3,
			),
		);

		//不在鉴权范围
		if (!in_array($cur_controller, array_keys($arr_power)) ||
			!in_array($cur_method, array_keys($arr_power[$cur_controller])))
		{
			return ;
		}

		//获取目标（项目、任务）ID
		if (intval($this->input->get('Id')))
		{
			$obj_id = intval($this->input->get('Id'));
		}
		else if (intval($this->input->post('Id')))
		{
			$obj_id = intval($this->input->post('Id'));
		}
		else if (intval($this->input->get('TaskId')))
		{
			$obj_id = intval($this->input->get('TaskId'));
		}
		else if (intval($this->input->post('TaskId')))
		{
			$obj_id = intval($this->input->post('TaskId'));
		}
		else if (intval($this->input->get('ProjectId')))
		{
			$obj_id = intval($this->input->get('ProjectId'));
		}
		else if (intval($this->input->post('ProjectId')))
		{
			$obj_id = intval($this->input->post('ProjectId'));
		}
		if (!isset($obj_id) || $obj_id <= 0)
		{
			common::return_json(-1, '参数错误', '', true);
		}

		$user_id = intval($this->user_id);
		$type = ($cur_controller === 'project' ? 1 : 2);
		$power = $arr_power[$cur_controller][$cur_method];

		$this->load->model('bll/user_bll');
		$auth = $this->user_bll->auth($user_id, $obj_id, $type, $power);
		if (!$auth)
		{
			common::return_json(-2, '权限不足', '', true);
		}
	}
}
