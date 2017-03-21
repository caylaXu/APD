<?php
/**
 * Created by PhpStorm.
 * User: cayla
 * Date: 2015/11/8
 * Time: 20:04
 */
class Task extends MyController
{

    function __construct()
    {
        parent::__construct();
        $this->load->model('bll/task_bll');
        $this->load->model('bll/rlt_task_user_bll');
        $this->load->model('bll/project_bll');
        $this->load->model('bll/rlt_project_user_bll');
        $this->load->model('bll/task_log_bll');
    }

    /**
     * @function 任务列表首页
     * @author CaylaXu
     */
    public function index()
    {
        $milestone_list = $this->task_bll->task_list(array());
        $this->hpf_smarty->assign('MilestoneList', $milestone_list);
        $this->hpf_smarty->assign('MilestoneListJSON', json_encode($milestone_list));
        $this->hpf_smarty->assign('PageName', "任务");
        $this->hpf_smarty->display('backend/task/index.tpl');
    }

    /**
     * @function 分页请求任务列表
     * @author CaylaXu
     */
    function task_list_get()
    {
        $page          = isset($_POST['Page']) ? $_POST['Page'] : 1;
        $limit         = isset($_POST['Rows']) ? $_POST['Rows'] : 10;
        $params = $_POST;
        $question_list = $this->task_bll->get_list($page - 1, $limit,$params);
        if(isset($_POST['Draw']))
        {
            $question_list['Draw'] = $_POST['Draw'];
        }
        echo json_encode($question_list);
    }


    /**
     * @function 创建任务
     * @author CaylaXu
     */
    public function create()
    {
        if ($_POST) {
            $check = array(
                'Title' => true,
                'Description' => false,
                'ProjectId'=>false,
                'StartDate'=>false,
                'DueDate' =>false,
                'ParentId'=>false,
                'IsMilestone'=>false,
                'CreatorId' => true,
                'AssignedTo'=>false,
                'Follwers'=>false,
                'Checklist' => false
            );

//            $params = common::check_get_post($check,'post', false);
            $params = $_POST;
            $check_data = $this->task_bll->check_data($_POST);
            if (!is_array($check_data))
            {
                common::return_json('-1',$check_data,'',true);
            }

            $params = $this->task_bll->completion_tasks($params);

            $data = $check_data;
            $data['CreateTime'] = time();
            $data['Sort'] = time()*1000;
            unset($data['AssignedTo']);
            unset($data['Follwers']);
            unset($data['Checklist']);
            unset($data['IsCollected']);
            $data['Priority'] = isset($_POST['Priority']) ? $_POST['Priority'] : 0;
            $data['StartDate'] = intval($data['StartDate']);
            $data['DueDate'] = intval($data['DueDate']);
            $task_id = $this->task_bll->create($data);

            if(!$task_id)
            {
                common::return_json('-1','任务新建失败','',true);
            }

            //未指派责任人时,责任人为自己
//            if(empty($params['AssignedTo']))
//            {
//                $params['AssignedTo'] = array();
//                $params['AssignedTo'][] = $params['CreatorId'];
//            }

            if(!empty($params['AssignedTo']))
            {
                $rlt_result = $this->rlt_task_user_bll->insert_batch($task_id,$params['AssignedTo']);

                if(!$rlt_result)
                {
                    common::return_json('-1','任务指派失败','',true);
                }

                if(!empty($params['ProjectId']))
                {
                    $rlt_result = $this->rlt_project_user_bll->insert_batch($params['ProjectId'],$params['AssignedTo'],2);
                }
            }

            //添加关注人
            if(isset($params['Follwers']) && !empty($params['Follwers']))
            {
                $rlt_result = $this->rlt_task_user_bll->insert_batch($task_id,$params['Follwers'],3);

                if(!$rlt_result)
                {
                    $this->response(array('Result'=>-1,'Msg'=>'添加关注人失败'), 200);
                    exit;
                }
            }

            //添加检查项
            if(isset($params['Checklist']) && !empty($params['Checklist']))
            {
                if(!is_array($params['Checklist']))
                {
                    $temp[] = $params['Checklist'];
                    $params['Checklist'] = $temp;
                }

                $checklist = $this->checklist_bll->insert_batch($task_id,$params['Checklist']);

                if(!$checklist)
                {
                    $this->response(array('Result'=>-1,'Msg'=>'检查项添加失败'), 200);
                    exit;
                }
            }

            //记录创建日志
            $log['UserId'] = intval($this->user_id);
            $log['TaskId'] = intval($task_id);
            $log['Type'] = 1;
            $log['CreateTime'] = time();
            $log['Desc'] = '';
            $this->task_log_bll->create($log);

            $task_info = $this->task_bll->task_info($task_id);
            common::return_json('0','新建任务成功',$task_info,true);
        }
        $this->hpf_smarty->display('backend/task/create.tpl');
    }

    /**
     * @function 复制任务
     * @author Peter
     */
    public function copy()
    {
        if (!$this->input->is_ajax_request() || !$this->input->post())
        {
            common::return_json(-1, '请求方法错误', '', true);
        }

        $task_id = intval($this->input->post('Id'));
        $creator_id = intval($this->user_id);

        if ($task_id <= 0 || $creator_id <= 0)
        {
            common::return_json(-1, '参数错误', '', true);
        }

        $select = array();
        $where = array('Id' => $task_id,);
        $task = $this->task_bll->select_info($select, $where, false);

        if (!$task)
        {
            common::return_json(-1, '任务不存在', '', true);
        }

        unset($task['Id']);
        $task['CreatorId'] = $creator_id;
        $task_new_id = $this->task_bll->create($task);
        if ($task_new_id <= 0)
        {
            common::return_json(-1, '任务复制失败', '', true);
        }
        $task_info = $this->task_bll->task_info($task_new_id);

        //记录创建日志
        $log['UserId'] = intval($this->user_id);
        $log['TaskId'] = intval($task_new_id);
        $log['Type'] = 1;
        $log['CreateTime'] = time();
        $log['Desc'] = '';
        $this->task_log_bll->create($log);

        common::return_json(0, '任务复制成功', $task_info, true);
    }

    /**
     * @function 编辑任务
     * @author CaylaXu
     */
    public function edit()
    {
        if ($_POST) {
			$params = $_POST;
            $task_id = isset($_POST['Id']) ? $_POST['Id'] : '';

			//权限鉴定
			$this->load->model('bll/user_bll');
			$auth = $this->user_bll->auth($this->user_id, $task_id, 2, 3);
			if (!$auth)
			{
				common::return_json(-2, '权限不足', '', true);
			}

            if(!is_numeric($task_id))
            {
                common::return_json('-1','参数非法','',true);
            }

            $check_data = $this->task_bll->check_data($params);

            if(!is_array($check_data))
            {
                common::return_json('-1',$check_data,'',true);
            }

            $msg = $this->task_bll->check_params($check_data);

            if( $msg !== true)
            {
                common::return_json('-1',$msg,'',true);
            }

            $data = $check_data;

            $task = $this->task_bll->task_info($params['Id']);
            if (!$task)
            {
                common::return_json('-1','该项目不存在','',true);
            }

            if(isset($params['ProjectId']))
            {
                $change_project = $this->task_bll->change_project($task_id,$params['ProjectId']);
                if(!$change_project)
                {
                    common::return_json('-1','修改项目失败','',true);
                }
            }

            $update = $this->task_bll->update($data,array('Id'=>$params['Id']));

            if(!$update)
            {
                common::return_json('-1','任务更新失败','',true);
            }

            //记录修改日志
            $log['UserId'] = intval($this->user_id);
            $log['TaskId'] = intval($params['Id']);
            $log['Type'] = 2;
            $log['CreateTime'] = time();
            $log['Desc'] = array();
            $fields = array('Title', 'Description', 'StartDate', 'DueDate',
                'Priority', 'IsMilestone', 'CompleteProgress');
            foreach($fields as $field)
            {
                if (!isset($params[$field]))
                {
                    continue;
                }
                $temp['Field'] = $field;
                $temp['Old'] = $task[$field];
                $temp['New'] = $params[$field];
                $log['Desc'][] = $temp;
            }

            if(!empty($log['Desc']))
            {
                $this->task_log_bll->create($log);
            }

            if(isset($data['StartDate']))
            {
                $data['StartDateString'] = common::time_cycle($data['StartDate']);
            }

            if(isset($data['DueDate']))
            {
                $data['DueDateString'] = common::time_cycle($data['DueDate']);
                $data['Exceed'] = time() > $data['DueDate'] ? 1 : 0;//结束时间大于今天则标记为为期
            }

            common::return_json('0','任务更新成功',$data,true);
        }

        $task_id = isset($_GET['Id']) ?  $_GET['Id']:'';

        if(!is_numeric($task_id))
        {
            common::return_json('-1','参数非法','',true);
        }

        $task_info = $this->task_bll->task_info($task_id);
        common::return_json('0','获取成功',$task_info);
    }

    /**
     * @function 删除任务
     * @author CaylaXu
     */
    public function delete()
    {
        $task_id = isset($_POST['Id']) ?  $_POST['Id']:'';

        if(!is_numeric($task_id))
        {
            common::return_json('-1','参数非法','',true);
        }

        $result = $this->task_bll->delete($task_id);

        if(!$result)
        {
            common::return_json('-1','删除失败','',true);
        }

        common::return_json('0','删除成功','',true);
    }

    /**
     * @function 改变任务状态
     * @author CaylaXu
     */
    public function change_progress()
    {
        $params = $params = common::check_get_post(array('TaskId'=>true,'Progress'=>false),'post', true);

        if(!$params)
        {
            common::return_json('-1','参数非法','',true);
        }

        $result = $this->task_bll->change_progress($params['TaskId'],$params['Progress'],$this->user_id);

        if(!$result)
        {
            common::return_json('-1','进度更新失败','',true);
        }

        //记录完成日志
        if (intval($params['Progress']) === 100)
        {//完成
            $log['Type'] = 3;
        }
        else if(intval($params['Progress']) === 0)
        {//重做
            $log['Type'] = 4;
        }
        $log['UserId'] = intval($this->user_id);
        $log['TaskId'] = intval($params['TaskId']);
        $log['CreateTime'] = time();
        $log['Desc'] = '';
        $this->task_log_bll->create($log);

        common::return_json('0','进度更新成功');
    }


    /**
     * @function 获取我的待办、我的关注、收集箱
     * @User: CaylaXu
     */
    function get_task_by_user_id()
    {
        $params = $_POST;
        $user_id = $this->user_id;
        if(!is_numeric($user_id))
        {
            common::return_json('-1','参数非法','',true);
        }
        $task_list = $this->task_bll->get_task_by_user_id($user_id,$params);
        common::return_json(0,'获取成功',$task_list,true);
    }

	/**
	 * @function 添加任务责任人
	 * @author Peter
	 */
	public function add_director()
	{
		if (!$this->input->is_ajax_request() || !$this->input->post())
		{
			common::return_json(-1, '请求方法错误', '', TRUE);
		}

		$task_id = intval($this->input->post('Id'));
		$user_id = intval($this->input->post('UserId'));
		$operator = $this->user_id;

		if ($task_id <= 0 || $user_id <= 0)
		{
			common::return_json(-1, '参数非法', '', TRUE);
		}
		$this->load->model('bll/rlt_task_user_bll', 'rtu_bll');
		$result = $this->rtu_bll->add_director($task_id, $user_id, $operator);
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
	 * @function 移除任务责任人
	 * @author Peter
	 */
	public function remove_director()
	{
		if (!$this->input->is_ajax_request() || !$this->input->post())
		{
			common::return_json(-1, '请求方法错误', '', TRUE);
		}

		$task_id = intval($this->input->post('Id'));

		if ($task_id <= 0)
		{
			common::return_json(-1, '参数非法', '', TRUE);
		}

		$this->load->model('bll/rlt_task_user_bll', 'rtu_bll');
		$result = $this->rtu_bll->remove_director($task_id);
		if ($result)
		{
			common::return_json(0, '删除成功', '', TRUE);
		}
		else
		{
			common::return_json(-1, '删除失败', '', TRUE);
		}
	}

	public function add_follower()
	{
		if (!$this->input->is_ajax_request() || !$this->input->post())
		{
			common::return_json(-1, '请求方法错误', '', TRUE);
		}

		$task_id = intval($this->input->post('Id'));
		$user_id = intval($this->input->post('UserId'));

		if ($task_id <= 0 || $user_id <= 0)
		{
			common::return_json(-1, '参数非法', '', TRUE);
		}

		$this->load->model('bll/rlt_task_user_bll', 'rtu_bll');
		$result = $this->rtu_bll->add_follower($task_id, $user_id);
		if ($result)
		{
			common::return_json(0, '添加成功', '', TRUE);
		}
		else
		{
			common::return_json(-1, '添加失败', '', TRUE);
		}
	}

	public function remove_follower()
	{
		if (!$this->input->is_ajax_request() || !$this->input->post())
		{
			common::return_json(-1, '请求方法错误', '', TRUE);
		}

		$task_id = intval($this->input->post('Id'));
		$user_id = intval($this->input->post('UserId'));

		if ($task_id <= 0 || $user_id <= 0)
		{
			common::return_json(-1, '参数非法', '', TRUE);
		}

		$this->load->model('bll/rlt_task_user_bll', 'rtu_bll');
		$result = $this->rtu_bll->remove_follower($task_id, $user_id);
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
     * @function 添加任务责任人
     * @User: CaylaXu
     */
    function add_rlt_user()
    {
        $params = $_POST;
        $user_id = isset($params['UserId']) ?  $params['UserId'] : '';
        $task_id = isset($params['Id']) ?  $params['Id'] : '';
        $type = isset($params['Type']) ?  $params['Type'] : '';

        if(!is_numeric($user_id) && !is_numeric($task_id) && !in_array($type,array(1,3)))
        {
            common::return_json(-1,'参数非法','',true);
        }

        $this->load->model('bll/User_bll', 'user_bll');
        $user_info = $this->user_bll->select_info(array('Name'),array('Id'=>$user_id,'Status!='=>-1),false);
        if(empty($user_info))
        {
            common::return_json(-1, '用户不存在');
        }

        $where = array(
            'TaskId' => $task_id,
            'UserId' => $user_id,
            'Type' => $type
        );
        $rlt_info = $this->rlt_task_user_bll->select_info(array(),$where);
        $result = false;
        $msg = '';
        $data = array();
        //1、不存在关联则新建
        if(empty($rlt_info))
        {
            $id = $this->rlt_task_user_bll->create($task_id,$user_id,$type);
            $result = $id ? true : false;
            $msg = $result ? '添加成功' : '添加失败';
            $field = 'add_attention';
//            $data = array('Id'=>$id);
        }
        else
        {
            $info = $rlt_info[0];
            //2、存在但已删除则激活
            if(!empty($info) && $info['Status'] == -1)
            {
                $id = $this->rlt_task_user_bll->create($task_id,$user_id,$type);
                $result = $id ? true : false;
                $msg = $result ? '添加成功' : '添加失败';
                $field = 'add_attention';
            }//3、存在且激活的，则删除
            else if(!empty($info) && $info['Status'] == 1)
            {
                $result = $this->rlt_task_user_bll->delete($info['Id']);
                $msg = $result ? '删除成功':'删除失败';
//                $data = array('Id'=>$info['Id']);
                $field = 'del_attention';
            }
        }

        //记录修改日志
        $log['UserId'] = intval($this->user_id);
        $log['TaskId'] = $task_id;
        $log['Type'] = 2;
        $log['CreateTime'] = time();
        $log['Desc'] = array();
        $temp['Field'] = $type == 1 ? 'principal' : $field;
        $temp['Old'] = '';
        $temp['New'] = $user_info['Name'];
        $log['Desc'][] = $temp;
        $this->task_log_bll->create($log);

        $rlt_user = $this->rlt_task_user_bll->get_rlt_user($task_id,'principal');
        $follwers = $this->rlt_task_user_bll->get_rlt_user($task_id,'attention');
        $data['AssignedTo'] = $rlt_user;
        $data['Follwers'] = $follwers;
        common::return_json($result ? 0 : -1, $msg,$data);
    }


    /**
     * @function 树形列表页
     * @User: CaylaXu
     */
    function task_tree()
    {
        $project_id = isset($_GET['ProjectId']) ?  $_GET['ProjectId'] : '';

		//与我相关的项目
		$this->load->model('bll/Project_bll', 'project_bll');
		$is_rlt = $this->project_bll->is_rlt($project_id, $this->user_id);
		if (!$is_rlt)
		{
			$this->hpf_smarty->assign("Message", "您没有权限操作该项目");
			$this->hpf_smarty->display('backend/error/message.php');
			exit;
		}

        //获取子项目
        $childs = $this->project_bll->get_child_projects_info($project_id,$this->user_id);

        /*if(empty($childs))
        {
            //权限检查
            $authorization_check = $this->project_bll->authorization_check($this->user_id,$project_id,true);
            if(!$authorization_check)
            {
                $this->hpf_smarty->assign("Message", "您没有权限操作该项目");
                $this->hpf_smarty->display('backend/error/message.php');
                exit;
            }
        }*/

        $project_info = $this->project_bll->select_info(array('Title','ProjectManagerId','CreatorId','ParentId','UniCode'),array('Id'=>$project_id));
        $project_name = isset($project_info[0]['Title']) ? $project_info[0]['Title'] : "未知";
		$project_unicode = $project_info[0]['UniCode'];
        $permission = 1;
        $parent_id = 0;
        if(isset($project_info[0]))
        {
            $parent_id = $project_info[0]['ParentId'];
            if($this->user_id == $project_info[0]['ProjectManagerId'] || $this->user_id == $project_info[0]['CreatorId'])
            {
                $permission = 1;
            }
        }

        if(!empty($parent_id))
        {
            $parent_info = $this->project_bll->select_info(array('Title','Id'),array('Id'=>$parent_id),false);
            $this->hpf_smarty->assign("ParentProject",$parent_info);
        }

        $projects = $this->project_bll->get_project_list_by_user_id($this->user_id);
        $tasks = $this->task_bll->task_tree_by_project_id($project_id);
        $directors = $this->task_bll->get_directors_by_project_id($project_id);

		//权限判定
		$this->load->model('bll/User_bll', 'user_bll');
		$project_power = intval($this->user_bll->can_enter($this->user_id, $project_id));
		foreach ($childs as &$project)
		{
			$project['power'] = intval($this->user_bll->can_enter($this->user_id, $project['Id']));
		}
        $this->hpf_smarty->assign("Tasks",isset($tasks['Child']) ? $tasks['Child'] : array());
        $this->hpf_smarty->assign("ProjectId",$project_id);
        $this->hpf_smarty->assign("ProjectName",$project_name);
        $this->hpf_smarty->assign("ProjectUniCode",$project_unicode);
        $this->hpf_smarty->assign("ProjectPower",$project_power);
        $this->hpf_smarty->assign("ProjectParentId",$parent_id);//项目的父级Id
        $this->hpf_smarty->assign("Projects",$projects);
        $this->hpf_smarty->assign("Permission",$permission);//1：允许操作0：不允许操作
        $this->hpf_smarty->assign("Childs",$childs);
        $this->hpf_smarty->assign("Directors", $directors);
        $this->hpf_smarty->display('backend/task/index.tpl');
    }

    /**
     * @function 获取项目下的树形列表
     * @User: CaylaXu
     */
    function get_task_tree()
    {
        $project_id = isset($_GET['ProjectId']) ?  $_GET['ProjectId'] : '';
        $tasks = $this->task_bll->task_tree_by_project_id($project_id);
        common::return_json(0,'获取成功',isset($tasks['Child']) ? $tasks['Child'] : array(),true);
    }


    /**
     * @function 任务筛选接口
     * @User: CaylaXu
     */
    function get_task_by_params()
    {
        $params = $_POST;
        $user_id = $this->user_id;
        if(!is_numeric($user_id))
        {
            common::return_json('-1','参数非法','',true);
        }

        if (isset($params['DirectorIds']))
        {
            if(!is_string($params['DirectorIds']))
            {
                common::return_json('-1','参数错误','',true);
            }

            $params['DirectorIds'] = explode(',', $params['DirectorIds']);

            array_walk($params['DirectorIds'], function (&$value, $key)
            {
                $value = intval($value);
                if ($value < 0)
                {
                    common::return_json(-1, '参数错误', '', true);
                }
            });
        }


        $task_list = $this->task_bll->get_task_by_params($user_id,$params);
        common::return_json(0,'获取成功',$task_list,true);
    }

    /**
     * @function 向左移动层级
     * @User: CaylaXu
     */
    function move_left()
    {
        $id = $this->input->post('Id');
        $brothers = $this->input->post('Brothers');

        try
        {
            $info = $this->task_bll->select_info(array(),array('Id'=>$id),false);

            if(empty($info))
            {
                throw new Exception('参数非法');
            }

            $result = $this->task_bll->move_left($info,$brothers);

            if(!$result)
            {
                throw new Exception('操作失败');
            }

            common::return_json(0,'操作成功');
        }
        catch(Exception $e)
        {
            common::return_json(-1,$e->getMessage());
        }
    }

    /**
     * @function 向右移动层级
     * @User: CaylaXu
     */
    function move_right()
    {
        $id = $this->input->post('Id');
        $brother_id = $this->input->post('BrotherId');

        try
        {
            $info = $this->task_bll->select_info(array(),array('Id'=>$id,'Status'=>1),false);
            $brother_info = $this->task_bll->select_info(array(),array('Id'=>$brother_id,'Status'=>1),false);

            if(empty($info))
            {
                throw new Exception('参数非法');
            }

            if($brother_id != 0 && empty($brother_info))
            {
                throw new Exception('参数非法');
            }

            if(!empty($brother_info))
            {
                $data = array(
                    'ParentId'=>$brother_id
                );
                $sort = $this->task_bll->get_max_child_sort($brother_id);

                if(!empty($sort))
                {
                    $data['Sort'] = $sort + 1;
                }

                $result = $this->task_bll->update($data,array('Id'=>$id));

                if(!$result)
                {
                    throw new Exception('操作失败');
                }
            }

            common::return_json(0,'操作成功');
        }
        catch(Exception $e)
        {
            common::return_json(-1,$e->getMessage());
        }
    }

    /**
     * @function 排序接口
     * @User: CaylaXu
     */
    function sort()
    {
        $id = $this->input->post('Id');
        $pre_sort = $this->input->post('PreSort');
        $next_sort = $this->input->post('NextSort');
        try
        {
            if(!is_numeric($id) || !is_numeric($pre_sort) || !is_numeric($next_sort))
            {
                throw new Exception('参数非法');
            }

            $result = $this->task_bll->sort($id, $pre_sort, $next_sort);

            if(!$result)
            {
                throw new Exception('移动失败');
            }

            common::return_json(0,'操作成功');
        }
        catch(Exception $e)
        {
            common::return_json(-1,$e->getMessage());
        }
    }

    /**
     * @function 将任务标记为进行中、暂停、放弃
     * @author Peter
     */
    public function set_status()
    {
        if (!$this->input->is_ajax_request() || !$this->input->post())
        {
            common::return_json(-1, '请求方法错误', '', true);
        }

        $task_id = $this->input->post('TaskId') ? intval($this->input->post('TaskId')) : 0;
        $user_id = $this->input->post('UserId') ? intval($this->input->post('UserId')) : 0;
        $status = $this->input->post('Status') ? intval($this->input->post('Status')) : 0;
        $duration = $this->input->post('Duration') ? intval($this->input->post('Duration')) : 0;

        //状态范围（1=>放弃 ,2=>进行中、暂停）
        $status_range = array(1, 2);

        if ($task_id <= 0 || $user_id <= 0 || !in_array($status, $status_range))
        {
            common::return_json(-1, '参数错误', '', true);
        }

        $data['Status'] = $status;
        $data['Duration'] = $duration;
        $where = array(
            'TaskId' => $task_id,
            'UserId' => $user_id,
        );

        $res = $this->rlt_task_user_bll->update_where($data, $where);

        if(!$res)
        {
            common::return_json(-1, '标记失败', '', true);
        }

        common::return_json(0, '标记成功', '', true);

    }

	/**
	 * @function 获取任务日志
	 * @author Peter
	 */
	public function get_log()
	{
		if (!$this->input->is_ajax_request() || !$this->input->get())
		{
			common::return_json(-1, '请求方法错误', '', TRUE);
		}

		$task_id = intval($this->input->get('Id'));

		if ($task_id <= 0)
		{
			common::return_json(-1, '参数非法', '', TRUE);
		}

		$this->load->model('bll/Task_log_bll', 'tl_bll');

		$task_log = $this->tl_bll->get_log($task_id);

		if ($task_log === false)
		{
			common::return_json(-1, '获取失败', '', TRUE);
		}
		else
		{
			common::return_json(0, '获取成功', $task_log, TRUE);
		}
	}

	/**
	 * @function 撤销删除任务
	 * @author Peter
	 */
	public function undo()
	{
		if (!$this->input->is_ajax_request() || !$this->input->post())
		{
			common::return_json(-1, '请求方法错误', '', TRUE);
		}

		$task_id = intval($this->input->post('Id'));

		if ($task_id <= 0)
		{
			common::return_json(-1, '参数非法', '', TRUE);
		}

		$this->load->model('bll/Task_bll', 'task_bll');

		$result = $this->task_bll->undo($task_id);

		if (!$result)
		{
			common::return_json(-1, '撤销失败', '', TRUE);
		}
		else
		{
			common::return_json(0, '撤销成功', '', TRUE);
		}
	}
}
