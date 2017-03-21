<?php
/**
 * Created by PhpStorm.
 * User: cayla
 * Date: 2015/11/8
 * Time: 20:04
 */
class Project extends MyController
{

    function __construct()
    {
        parent::__construct();
        $this->load->model('bll/project_bll');
        $this->load->model('bll/rlt_project_user_bll');
    }

    /**
     * @function 项目管理首页
     * @author CaylaXu
     */
    public function index()
    {
        $project_list = $this->project_bll->projects_info(array());
        $this->hpf_smarty->assign('ProjectList', $project_list);
        $this->hpf_smarty->assign('ProjectListJSON', json_encode($project_list));
        $this->hpf_smarty->assign('PageName', "项目");
        $this->hpf_smarty->display('backend/project/index.tpl');
    }

    /**
     * @function 项目详情页
     * @author CaylaXu
     */
    public function all_project()
    {
        $project_list = $this->project_bll->select_info(array('Id','Title'),array('Status'=>1));
        $my_projects = $this->project_bll->get_all_project_by_user_id($this->user_id);
        common::return_json('0','请求成功',array("Projects" => $project_list, "MyProjects" => $my_projects));
    }

    public function create()
    {
        if ($_POST)
        {
            try {
                if(!isset($_POST['ChildIds']))
                {
                    $params = $_POST;
                    $check_data = $this->project_bll->check_data($_POST);

                    if (!is_array($check_data))
                    {
                        throw new Exception($check_data, -1);
                    }

                    /**
                     * 未指定项目经理则为自己
                     */
                    if(empty($params['ProjectManagerId']))
                    {
                        $params['ProjectManagerId'] = $this->user_id;
                    }

                    if(!common::check_string_length(0,750,$params['Description']))
                    {
                        throw new Exception("描述过长!", -1);
                    }

                    if(!common::is_timestamp($params['StartDate']) || !common::is_timestamp($params['DueDate']))
                    {
                        throw new Exception("时间不能为空!", -1);
                    }

                    if($params['StartDate'] >= $params['DueDate'])
                    {
                        throw new Exception("开始时间必须小于结束时间!", -1);
                    }

                    unset($params['Id']);
                    unset($params['ChildIds']);

                    //1、新建项目
                    if(!$project_id = $this->project_bll->create_project($params))
                    {
                        throw new Exception("新建项目失败!", -1);
                    }

                    $project_info = $this->project_bll->project_info($project_id);
                    common::return_json('0','新建项目成功',$project_info,true);
                }
                else //2、如果存在子项目Id则说明是合并子项目
                {
                    $title = isset($_POST['Title']) ? $_POST['Title'] : '';
                    $child_ids = $_POST['ChildIds'];

                    if(!common::check_string_length(1,70,$title))
                    {
                        throw new Exception("标题不能为空或过长!", -1);
                    }

                    if(empty($child_ids))
                    {
                        throw new Exception("参数非法!", -1);
                    }

                    $params['Title'] = $title;
                    $params['ProjectManagerId'] = $this->user_id;
                    $params['CreatorId'] = $this->user_id;

                    //1、新建项目
                    if(!$project_id = $this->project_bll->create_project($params))
                    {
                        throw new Exception("新建项目失败!", -1);
                    }

                    if(!$this->project_bll->consolidated_project($project_id,$child_ids))
                    {
                        throw new Exception("合并项目失败!", -1);
                    }

                    common::return_json('0','合并成功','',true);
                }

            } catch (Exception $e){

                common::return_json($e->getCode(),$e->getMessage(),'',true);
            }
        }
        $this->hpf_smarty->display('backend/project/create.tpl');
    }

    public function edit()
    {
        if ($_POST) {
            $params = $_POST;
            $project_id = isset($_POST['Id']) ? $_POST['Id'] : '';

            if(!is_numeric($project_id))
            {
                common::return_json('-1','参数非法','',true);
            }

            $check_data = $this->project_bll->check_data($params);

            if(!is_array($check_data))
            {
                common::return_json('-1',$check_data,'',true);
            }

            $data = $check_data;

            $update = $this->project_bll->update($data,array('Id'=>$params['Id']));


            if(!$update)
            {
                common::return_json('-1','项目更新失败','',true);
            }

            $project_info = $this->project_bll->project_info($project_id);
            common::return_json('0','项目更新成功',$project_info,true);
        }

        $project_id = isset($_GET['Id']) ?  $_GET['Id']:'';

        if(!is_numeric($project_id))
        {
            common::return_json('-1','参数非法','',true);
        }

        $project_info = $this->project_bll->project_info($project_id);
        common::return_json('0','获取成功',$project_info);
    }

    public function delete()
    {
        $project_id = isset($_POST['Id']) ?  $_POST['Id']:'';

        if(!is_numeric($project_id))
        {
            common::return_json('-1','参数非法','',true);
        }

        $result = $this->project_bll->delete($project_id);

        if(!$result)
        {
            common::return_json('-1','删除失败','',true);
        }

        common::return_json('0','删除成功','',true);
    }

    public function project_list_get()
    {
        $page          = isset($_POST['Page']) ? $_POST['Page'] : 1;
        $limit         = isset($_POST['Rows']) ? $_POST['Rows'] : 10;
        $params = $_POST;
        $project_list = $this->project_bll->get_list($page - 1, $limit,$params);
        echo json_encode($project_list);
    }


    /**
     * @function 项目概览页
     * @author CaylaXu
     */
    public function overview()
    {
        if($_POST)
        {
            $type = $this->input->post('Type');

            if(!in_array($type,array('','participant','attention')))
            {
                common::return_json('-1','参数非法','',true);
            }

            $projects = $this->project_bll->project_overview_new($this->user_id,$type);

            common::return_json('0','获取成功',array_values($projects));

            exit;

        }

        //我的项目
        $my_project = $this->project_bll->project_overview_new($this->user_id,'');

        //我参与的项目
        $participant = $this->project_bll->project_overview_new($this->user_id,'participant');

        //我关注的项目
        $att_project = $this->project_bll->project_overview_new($this->user_id,'attention');

        $this->hpf_smarty->assign('MyProject', $my_project);
        $this->hpf_smarty->assign('AttProject', $att_project);
        $this->hpf_smarty->assign('Participant', $participant);
        $this->hpf_smarty->display('backend/project/overview.tpl');
    }

	/**
	 * @function  添加项目经理
	 * @author Peter
	 */
	public function add_manager()
	{
		if (!$this->input->is_ajax_request() || !$this->input->post())
		{
			common::return_json(-1, '请求方法错误', '', TRUE);
		}

		$project_id = intval($this->input->post('Id'));
		$user_id = intval($this->input->post('UserId'));

		if ($project_id <= 0 || $user_id <= 0)
		{
			common::return_json(-1, '参数非法', '', TRUE);
		}

		$this->load->model('bll/rlt_project_user_bll', 'rpu_bll');
		$result = $this->rpu_bll->add_manager($project_id, $user_id);
		if ($result)
		{
			common::return_json(0, '添加成功', '', TRUE);
		}
		else
		{
			common::return_json(-1, '添加失败', '', TRUE);
		}
	}

	/**
	 * @function 移除项目经理
	 * @author Peter
	 */
	public function remove_manager()
	{
		if (!$this->input->is_ajax_request() || !$this->input->post())
		{
			common::return_json(-1, '请求方法错误', '', TRUE);
		}

		$project_id = intval($this->input->post('Id'));

		if ($project_id <= 0)
		{
			common::return_json(-1, '参数非法', '', TRUE);
		}

		$this->load->model('bll/rlt_project_user_bll', 'rpu_bll');
		$result = $this->rpu_bll->remove_manager($project_id);
		if ($result)
		{
			common::return_json(0, '删除成功', '', TRUE);
		}
		else
		{
			common::return_json(-1, '删除失败', '', TRUE);
		}
	}

	/**
	 * @function 添加项目成员
	 * @author Peter
	 */
	public function add_member()
	{

	}

	/**
	 * @function 移除项目成员
	 * @author Peter
	 */
	public function remove_member()
	{

	}

	/**
	 * @function 添加项目关注人
	 * @author Peter
	 */
	public function add_follower()
	{
		if (!$this->input->is_ajax_request() || !$this->input->post())
		{
			common::return_json(-1, '请求方法错误', '', TRUE);
		}

		$project_id = intval($this->input->post('Id'));
		$user_id = intval($this->input->post('UserId'));

		if ($project_id <= 0 || $user_id <= 0)
		{
			common::return_json(-1, '参数非法', '', TRUE);
		}

		$this->load->model('bll/rlt_project_user_bll', 'rpu_bll');
		$result = $this->rpu_bll->add_follower($project_id, $user_id);
		if ($result)
		{
			common::return_json(0, '添加成功', '', TRUE);
		}
		else
		{
			common::return_json(-1, '添加失败', '', TRUE);
		}
	}

	/**
	 * @function 添加项目关注人
	 * @author Peter
	 */
	public function remove_follower()
	{
		if (!$this->input->is_ajax_request() || !$this->input->post())
		{
			common::return_json(-1, '请求方法错误', '', TRUE);
		}

		$project_id = intval($this->input->post('Id'));
		$user_id = intval($this->input->post('UserId'));

		if ($project_id <= 0 || $user_id <= 0)
		{
			common::return_json(-1, '参数非法', '', TRUE);
		}

		$this->load->model('bll/rlt_project_user_bll', 'rpu_bll');
		$result = $this->rpu_bll->remove_follower($project_id, $user_id);
		if ($result)
		{
			common::return_json(0, '删除成功', '', TRUE);
		}
		else
		{
			common::return_json(-1, '删除失败', '', TRUE);
		}
	}

    public function add_rlt_user()
    {
		if (!$this->input->is_ajax_request() || !$this->input->post())
		{
			common::return_json(-1, '请求方法错误', '', TRUE);
		}

		$user_id = intval($this->input->post('UserId'));
		$project_id = intval($this->input->post('Id'));
		$type = intval($this->input->post('Type'));

		if($user_id <= 0 || $project_id <= 0 || !in_array($type, array(1, 3)))
		{
			common::return_json(-1, '参数非法', '', TRUE);
		}

		$this->load->model('bll/rlt_project_user_bll', 'rpu_bll');

		$result = $this->rpu_bll->add_rlt_user($user_id, $project_id, $type);

		common::return_json($result['code'], $result['msg'], $result['data'], TRUE);

		//------------- old --------------------

        /*$params = $_POST;
        $user_id = isset($params['UserId']) ?  $params['UserId'] : '';
        $task_id = isset($params['Id']) ?  $params['Id'] : '';
        $type = isset($params['Type']) ?  $params['Type'] : '';

        if(!is_numeric($user_id) && !is_numeric($task_id) && !in_array($type,array(1,3)))
        {
            common::return_json(-1,'参数非法','',true);
        }

        if($type == 1)
        {
            $update = $this->project_bll->update(array('ProjectManagerId'=>$user_id),array('Id'=>$task_id));


            if(!$update)
            {
                common::return_json('-1','项目更新失败','',true);
            }

            common::return_json('0','项目更新成功','',true);
        }

        $where = array(
            'ProjectId' => $task_id,
            'UserId' => $user_id,
            'Type' => $type
        );

        $rlt_info = $this->rlt_project_user_bll->select_info(array(),$where);

        $result = false;
        $msg = '';
        $data = array();
        //1、不存在关联则新建
        if(empty($rlt_info))
        {
            $id = $this->rlt_project_user_bll->create($task_id,$user_id,$type);
            $result = $id ? true : false;
            $msg = $result ? '添加成功' : '添加失败';
            $data = array('RltId'=>$id);
        }
        else
        {
            $info = $rlt_info[0];
            //2、存在但已删除则激活
            if(!empty($info) && $info['Status'] == -1)
            {
                $result = $this->rlt_project_user_bll->update_where(array('Status'=>1),array('Id'=>$info['Id']));
                $msg = $result ? '添加成功':'添加失败';
                $data = array('RltId'=>$info['Id']);
            }//3、存在且激活的，则删除
            else if(!empty($info) && $info['Status'] == 1)
            {
                $result = $this->rlt_project_user_bll->delete($info['Id']);
                $msg = $result ? '删除成功':'删除失败';
                $data = array('RltId'=>$info['Id']);
            }
        }
        common::return_json($result ? 0 : -1, $msg,$data);*/
    }

    /**
     * @function 所有用户具有操作权限的项目
     * @User: CaylaXu
     */
    public function project_by_user_id()
    {
        $user_id = $_GET['UserId'];

        if(!is_numeric($user_id))
        {
            common::return_json(-1,'参数非法','',true);
        }

        $result = $this->project_bll->get_project_list_by_user_id($user_id);

        if(is_array($result))
        {
            common::return_json(0,'获取成功',$result,true);
        }

        common::return_json(-1,'获取失败');
    }

    /**
     * @function 合并用户
     * @User: CaylaXu
     * @param $project_one 项目Id
     * @param $project_one 另一个项目Id
     */
    public function consolidated_project()
    {
        $params = $_POST;
        $project_one = isset($params['ProjectOne']) ?  $params['ProjectOne'] : '';
        $project_two = isset($params['ProjectTwo']) ?  $params['ProjectTwo'] : '';

        if(!is_numeric($project_one) && !is_numeric($project_two))
        {
            common::return_json(-1,'参数非法','',true);
        }

        $result = $this->project_bll->consolidated_project_check($project_one,$project_two);

        common::return_json($result['Result'],$result['Msg']);
    }

    /**
     * @function 更新项目成员
     * @author Peter
     */
    public function update_members()
    {
        if (!$this->input->is_ajax_request() || !$this->input->post())
        {
            common::return_json(-1, '请求方法错误', '', TRUE);
        }

        $project_id = intval($this->input->post('ProjectId'));
        $member_ids = explode(',', (string)$this->input->post('MemberIds'));

        if($project_id <= 0 || count($member_ids) < 1)
        {
            common::return_json(-1, '参数错误', '', TRUE);
        }

        array_walk($member_ids, function (&$value)
        {
            $value = intval($value);
            if ($value <= 0)
            {
                common::return_json(-1, '参数错误', '', TRUE);
            }
        });

        $this->rlt_project_user_bll->update_rlt_user($project_id, $member_ids, 2);

        common::return_json(0, '添加成功', '', true);
    }

	//统计页面
	public function statistics(){
		if (!$this->input->get())
		{
			return ;
		}

		$project_id = intval($this->input->get('Id'));

		$this->load->model('bll/Project_bll', 'project_bll');
		$this->load->model('bll/Task_bll', 'task_bll');

		$projects = $this->project_bll->get_project_list_by_user_id($this->user_id);

		if(empty($projects))
		{
			$this->hpf_smarty->assign("Message", "别急嘛，您还没有项目呢，先去创建项目吧！");
			$this->hpf_smarty->display('backend/error/message.php');
			exit;
		}

		if($project_id != 0)
		{
			//权限检查
			$authorization_check = $this->project_bll->authorization_check($this->user_id,$project_id,true);

			if(!$authorization_check)
			{
				$this->hpf_smarty->assign("Message", "您没有权限操作该项目");
				$this->hpf_smarty->display('backend/error/message.php');
				exit;
			}
		}
		else
		{
			$project_id = $projects[0]['Id'];
		}

		$project_info = $this->project_bll->project_info($project_id);
		$parent = $this->project_bll->select_info(array('ParentId'),array('Id'=>$project_id));
		if (intval($parent[0]['ParentId']) !== 0)
		{
			$parent_info = $this->project_bll->select_info(array('Title','Id'),array('Id'=>$parent[0]['ParentId']),false);
			$this->hpf_smarty->assign("ParentProject",$parent_info);
		}

		//项目进度
		$progress = $this->project_bll->get_project_progress($project_id);

		//项目延误率
		$pass = $this->task_bll->count_task_by_project($project_id,7);
		$count = $this->task_bll->count_task_by_project($project_id);
		$count = $count > 0 ? $count : 100;
		$pass_percent = round($pass*100/$count);

		//未完成任务
		$unfinished = $this->task_bll->count_task_by_project($project_id,1);
		//已完成任务
		$completed = $this->task_bll->count_task_by_project($project_id,2);
		//总任务
		$all = $this->task_bll->count_task_by_project($project_id,3);
		//过期任务
		$past = $this->task_bll->count_task_by_project($project_id,4);

		//今天完成了几个任务
		$have_down = $this->task_bll->count_task_by_project($project_id,5);

		//新建了几个任务
		$create = $this->task_bll->count_task_by_project($project_id,6);

		$statistic_data = array(
			"Progress" => $progress,
			"Unfinished" => $unfinished,
			"Pass" => $pass_percent,
			"Completed" => $completed,
			"All" => $all,
			"Past" => $past,
			"HaveDown" => $have_down,
			"Create" => $create
		);

		$smarty_data = array(
			"ProjectInfo" => $project_info,
			"Projects" => $projects,
			"StatisticData" => $statistic_data
		);

		$this->hpf_smarty->assign('Data',$smarty_data);
		$this->hpf_smarty->display('backend/project/statistics.tpl');
	}

	/**
	 * @function 项目日历页
	 */
	public function calendar()
	{
		if (!$this->input->get())
		{
			return ;
		}

		$this->load->model('bll/Project_bll', 'project_bll');
		$this->load->model('bll/Task_bll', 'task_bll');

		$project_id = intval($this->input->get('Id'));

		if ($project_id <= 0)
		{
			$this->hpf_smarty->assign("Message", "参数非法！");
			$this->hpf_smarty->display('backend/error/message.php');
			exit;
		}

		$project_info = $this->project_bll->project_info($project_id);
		$parent = $this->project_bll->select_info(array('ParentId'),array('Id'=>$project_id));
		if (intval($parent[0]['ParentId']) !== 0)
		{
			$parent_info = $this->project_bll->select_info(array('Title','Id'),array('Id'=>$parent[0]['ParentId']),false);
			$this->hpf_smarty->assign("ParentProject",$parent_info);
		}

		$this->hpf_smarty->assign('ProjectInfo', $project_info);
		$this->hpf_smarty->display('backend/project/calendar.tpl');
	}

	/**
	 * @function 筛选项目日历信息
	 * @author Peter
	 */
	public function calendar_filter()
	{
		if (!$this->input->is_ajax_request() || !$this->input->get())
		{
			common::return_json(-1, '请求方法错误', '', TRUE);
		}

		$project_id = intval($this->input->get('Id'));
		$start = intval($this->input->get('StartDate'));
		$end = intval($this->input->get('DueDate'));
		$user_id = intval($this->user_id);

		if ($project_id <= 0 || $start <= 0 || $end <= 0)
		{
			common::return_json(-1, '参数非法', '', TRUE);
		}

		$this->load->model('bll/Project_bll', 'project_bll');

		$result = $this->project_bll->calendar_filter($project_id, $start, $end, $user_id);
		if ($result !== FALSE)
		{
			common::return_json(0, '获取成功', $result, TRUE);
		}
		else
		{
			common::return_json(-1, '获取失败', '', TRUE);
		}
	}
}
