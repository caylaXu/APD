<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Created by PhpStorm.
 * User: CaylaXu <caylaxu@motouch.cn>
 * Date: 2015/11/5
 * Time：19:51
 */
class Project_bll extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('dal/db/project_model');
        $this->load->model('dal/db/RltProjectUser_model');
        $this->load->model('dal/db/task_model');
        $this->load->model('bll/Rlt_project_user_bll');
        $this->load->model('bll/common_bll');
        $this->load->model('bll/notice_bll');
    }

    public function projects_info($params)
    {

        $projects = $this->project_model->projects_info($params);

        if(empty($projects))
        {
            return $projects;
        }

        foreach($projects as $k=>&$v)
        {
            $v['StartDateString'] = empty($v['StartDate']) ? "无" : date('Y/m/d',$v['StartDate']);
            $v['CreateTimeString'] = empty($v['CreateTime']) ? "无" : date('Y/m/d',$v['CreateTime']);
            $v['DueDateString'] = empty($v['DueDate']) ? "无" : date('Y/m/d',$v['DueDate']);
            $v['TrueDueDateString'] = empty($v['TrueDueDate']) ? "无" : date('Y/m/d',$v['TrueDueDate']);
        }
        
        return $projects;
    }

    public function create(array $params){
        if(empty($params) || !is_array($params))
        {
            return false;
        }
        $params['Method'] = 0;
        $params['Modified'] = $this->common_bll->get_max_modified();
        $params['UniCode'] = common::create_uuid();
        return $this->project_model->create($params);
    }

    public function project_info($project_id)
    {
        $project_info = $this->project_model->query_project_by_id($project_id);

        if(empty($project_info))
        {
            return $project_info;
        }

        $project_info['Avatar'] = common::resources_full_path('avatar',$project_info['Avatar'],'picture');
        $project_info['StartDateString'] = empty($project_info['StartDate']) ? "无" : date('m/d',$project_info['StartDate']);
        $project_info['DueDateString'] = empty($project_info['DueDate']) ? "无" : date('m/d',$project_info['DueDate']);
        $project_info['Follwers'] = $this->Rlt_project_user_bll->query_by_project_id($project_info['Id'],'Follwers');
        $manager_id = $project_info['ProjectManagerId'];
        $project_info['ProjectManagerId'] = array();
        $project_info['ProjectManagerId'][] = array(
            'UserId' => $manager_id,
            'UserName' => $project_info['ProjectManager'],
            'Avatar' => $project_info['Avatar']);
        return $project_info;
    }

    public function update(array $data,array $where)
    {
        if(isset($data['ProjectManagerId']) && isset($where['Id']))
        {
            $this->rlt_project_user_bll->insert_batch($where['Id'],$data['ProjectManagerId'],1);
        }

        $data['Method'] = 1;
        $data['Modified'] = $this->common_bll->get_max_modified();
        return $this->project_model->update_info($data,$where);
    }

    public function delete($project_id)
    {
        $this->db->trans_start();
        //1、删除项目
        $modified = $this->common_bll->get_max_modified();
        $this->project_model->delete_info(array('Id'=>$project_id),$modified);

        //2、删除子项目
        $childs = $this->project_model->select_info(array('Id'),array('ParentId'=>$project_id,'Status'=>1));

        foreach($childs as $k=>$v)
        {
            $result = $this->delete($v['Id']);

            if(!$result)
            {
                return false;
            }
        }

        //3、删除项目下的任务
        $data['Status'] =  -1;
        $data['Method'] = -1;
        $data['Modified'] = $this->common_bll->get_max_modified();
        $this->task_model->update_info($data,array('ProjectId'=>$project_id));
        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE)
        {
            return false;
        }

        return true;
    }

    public function select_info(array $select,array $where,$flag = true)
    {
        return $this->project_model->select_info($select,$where,$flag);
    }

    public function get_list($page = 0, $limit = 15, $params = array())
    {
        $result = $this->project_model->get_list( $page, $limit, $params);
        return $result;
    }

    /**
     * @function 创建项目(包括项目成员)
     * @author CaylaXu
     */
    public function create_project($params)
    {
        $data = $params;
        $data['CreateTime'] = time();
        unset($data['Follwers']);
        $this->db->trans_start();
        $data['Method'] = 0;
        $data['Modified'] = $this->common_bll->get_max_modified();
        $data['UniCode'] = common::create_uuid();
        $project_id = $this->project_model->create($data);
        $this->rlt_project_user_bll->insert_batch($project_id,$params['ProjectManagerId'],1);

        if(!empty($params['Follwers']))
        {
            //2、插入项目关注人员
            $this->rlt_project_user_bll->insert_batch($project_id,$params['Follwers'],3);
        }

        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE)
        {
            return false;
        }

        return $project_id;
    }

    /**
     * @function 获取项目概览
     * @User: CaylaXu
     * @param $user_id 用户Id
     * @param string $type attention我的关注
     * @param bool $filter 是否过滤掉小迭代
     * @return mixed
     */
    public function project_overview($user_id,$type = '',$filter = false)
    {
        $this->load->model('bll/task_bll');
        //1、取项目基本信息
        $project = $this->project_model->get_leaf_project_by_user_id($user_id,$type);

        if(empty($project))
        {
            return $project;
        }

        foreach($project as $k=>&$v)
        {
            $v['Avatar'] = common::resources_full_path('avatar',$v['Avatar'],'picture');
            $v['StartDateString'] = empty($v['StartDate']) ? "无" : date('m/d',$v['StartDate']);
            $v['DueDateString'] = empty($v['DueDate']) ? "无" : date('m/d',$v['DueDate']);
            //获取项目进度
            $progress = $this->get_project_progress($v['Id']);
            $v['CompleteProgress'] = is_numeric($progress) ? $progress : 0;
            $v['DateProgress'] = $this->get_date_progress($v['StartDate'],$v['DueDate']);

            $childs = $this->project_model->select_info(array('Id'),array('ParentId'=>$v['Id'],'Status'=>1));

            $v['ChildCount'] = count($childs);
        }
        return $project;
    }

    /**
     * @function 子项目版本查询项目概览 v_2.0
     * @User: CaylaXu
     * @param $user_id 用户id
     * @param string $type 类型
     * @param bool $filter
     * @return mixed
     */
    public function project_overview_new($user_id,$type = '',$filter = false)
    {
        $this->load->model('bll/task_bll');
        //1、取项目基本信息
        $project = $this->project_model->get_project_by_user_id($user_id,$type,$filter);
        if(empty($project))
        {
            return $project;
        }
        $projects = array();
        foreach($project as $k=>&$v)
        {
            if($v['ParentId'] != 0)
            {
                if(!isset($projects[$v['ParentId']]))
                {
                    $parent_info = $this->project_model->get_project_by_id($v['ParentId']);

                    if(!empty($parent_info))
                    {
                        $data = $parent_info;
                        $data['Avatar'] = common::resources_full_path('avatar',$parent_info['Avatar'],'picture');
                        $data['StartDateString'] = empty($parent_info['StartDate']) ? "无" : date('m/d',$parent_info['StartDate']);
                        $data['DueDateString'] = empty($parent_info['DueDate']) ? "无" : date('m/d',$parent_info['DueDate']);
                        //获取项目进度
                        $progress = $this->get_project_progress($parent_info['Id']);
                        $data['CompleteProgress'] = is_numeric($progress) ? $progress : 0;
                        $data['DateProgress'] = $this->get_date_progress($parent_info['StartDate'],$parent_info['DueDate']);
                        $childs = $this->project_model->select_info(array(),array('ParentId'=>$v['ParentId'],'Status'=>1));
                        $data['ChildCount'] = count($childs);
                        $data['Permission'] = $this->authorization_check($user_id,$parent_info['Id'],false);
                        $projects[$v['ParentId']] = $data;
                    }
                }
            }
            else
            {
                $v['Avatar'] = common::resources_full_path('avatar',$v['Avatar'],'picture');
                $v['StartDateString'] = empty($v['StartDate']) ? "无" : date('m/d',$v['StartDate']);
                $v['DueDateString'] = empty($v['DueDate']) ? "无" : date('m/d',$v['DueDate']);
                //获取项目进度
                $progress = $this->get_project_progress($v['Id']);
                $v['CompleteProgress'] = is_numeric($progress) ? $progress : 0;
                $v['DateProgress'] = $this->get_date_progress($v['StartDate'],$v['DueDate']);
                $childs = $this->project_model->select_info(array(),array('ParentId'=>$v['Id'],'Status'=>1));
                $v['ChildCount'] = count($childs);
                $v['Permission'] = $this->authorization_check($user_id,$v['Id'],false);
                $projects[$v['Id']] = $v;
            }
        }
        return $projects;
    }


    public function get_date_progress($start_date,$due_data)
    {
        $time = time();
        if($time <= $start_date || $start_date > $due_data)
        {
            return 0;
        }

        if($time>=$due_data)
        {
            return 100;
        }

        $numerator = $time - $start_date;
        $denominator =  $due_data - $start_date;
        return  round($numerator/$denominator,2)*100;
    }

    /**
     * @function 获取项目进度
     * @User: CaylaXu
     * @param $project_id
     * @return float|int
     */
    public function get_project_progress($project_id)
    {
        //获取子项目
        $child = $this->project_model->select_info(array('Id'),array('ParentId'=>$project_id,'Status' => 1));

        $where = array(
            'ProjectId' => $project_id,
            'Status' => 1
        );

        $tasks = $this->task_model->select_info(array('Id','ParentId','CompleteProgress',''),$where);

        $progress = 0;
        $count = 0;
        if(!empty($tasks))
        {
            $tree = common::generate_tree($tasks);
            if(!empty($tree))
            {
                $count = 1;
                $progress = common::get_progress($tree);
            }
        }

        if(!empty($child))
        {
            foreach($child as $k=>$v)
            {
                $progress += $this->get_project_progress($v['Id']);
            }
            return round($progress/(count($child)+$count));
        }

        return $progress;
    }

    /**
     * @function 获取我负责的项目
     * @User: CaylaXu
     * @param $user_id
     * @param bool $flag 是否过滤哒项目
     * @return mixed
     */
    public function get_project_list_by_user_id($user_id,$flag = false)
    {
        $projects = $this->project_model->get_project_list_by_user_id($user_id,$flag);
        return $projects;
    }

    /**
     * @function 获取所有我可操作的项目
     * @User: CaylaXu
     * @param $user_id
     * @param array $params  1:我创建的 2：我负责的  3：我参与的  4：我关注的
     * @param bool $flag
     * @return array
     */
    public function get_all_project_by_user_id($user_id,$flag = false)
    {
        $projects = $this->project_model->get_all_project_by_user_id($user_id,$flag);
        return $projects;
    }

    public function check_data($params)
    {
        $project_id = isset($params['Id']) ? $params['Id'] : 0;
        $filed = array('Title','Description', 'ProjectManagerId','StartDate', 'DueDate','ParentId','ParentId');
        if(empty($params))
        {
            return false;
        }
        $data = array();
        foreach($params as $k => $v)
        {
            if($k == 'Title')
            {
                $title = common::check_string_length(1,70,$params['Title']);
                if(!$title)
                {
                    return '标题不能为空或过长';
                }
            }

            if($k == 'Description')
            {
                $description = common::check_string_length(0,750,$params['Description']);
                if(!$description)
                {
                    return '描述过长';
                }
            }

            if(isset($params['StartDate']) && isset($params['DueDate']))//时间检查
            {
                if($params['StartDate'] > $params['DueDate'])
                {
                    return "请确保开始时间小于结束时间哦！";
                }
            }
            else if(isset($params['StartDate']) && !empty($project_id))
            {
                $info = $this->project_model->select_info(array(),array('Id'=>$project_id),false);
                if(isset($info['DueDate']) && ($info['DueDate'] < $params['StartDate']))
                {
                    return "请确保开始时间小于结束时间哦！";
                }
            }
            else if(isset($params['DueDate']) && !empty($project_id))
            {
                $info = $this->project_model->select_info(array(),array('Id'=>$project_id),false);
                if(isset($info['StartDate']) && ($info['StartDate'] > $params['DueDate']))
                {
                    return "请确保开始时间小于结束时间哦！";
                }
            }

            if(in_array($k,$filed))
            {
                $data[$k] = $v;
            }
        }
        return empty($data) ? false : $data;
    }

    public function query_project_by_id($project_id)
    {
        $project = $this->project_model->query_project_by_id($project_id);

        if(empty($project))
        {
            return $project;
        }

        $project['Avatar'] = common::resources_full_path('avatar',$project['Avatar'],'picture');
        $project['StartDateString'] = empty($project['StartDate']) ? "无" : date('m/d',$project['StartDate']);
        $project['DueDateString'] = empty($project['DueDate']) ? "无" : date('m/d',$project['DueDate']);
        $project['DateProgress'] = $this->get_date_progress($project['StartDate'],$project['DueDate']);
        return $project;
    }

    /**
     * @function
     * @User: CaylaXu
     * @param array $data
     * @param array $where
     * @return mixed
     */
    public function update_where(array $data,array $where)
    {
        $data['Method'] = 1;
        $data['Modified'] = $this->common_bll->get_max_modified();
        return $this->project_model->update_info($data,$where);
    }

    /**
     * @function 权限检查
     * @User: CaylaXu
     * @param $user_id 用户Id
     * @param $project_id 项目Id
     * @param $flag 是否允许关注者操作
     * @return bool
     */
    public function authorization_check($user_id,$project_id,$flag = false)
    {
        $project_info = $this->project_model->authorization_check($user_id,$project_id,$flag);
        return count($project_info) > 0 ? 1 : 0;
    }


    public function get_all_projects_by_user($user_id)
    {
        $project_info = $this->project_model->get_all_projects_by_user($user_id);
        return $project_info;
    }


    /**
     * @function 合并项目
     * @User: CaylaXu
     * @param $project_id
     * @param $child_ids
     * @return bool
     */
    public function consolidated_project($project_id,$child_ids)
    {
        if(empty($child_ids))
        {
            return true;
        }

        if(!is_array($child_ids))
        {
            $child_ids = explode(',',$child_ids);
        }
        $data['Method'] = 1;
        $data['Modified'] = $this->common_bll->get_max_modified();
        $data['ParentId'] =  $project_id;
        return $this->project_model->consolidated_project($data,$child_ids);
    }

    public function get_child_projects_info($project_id,$user_id = 0)
    {
        $projects = $this->project_model->get_child_projects_info($project_id);

        if(empty($projects))
        {
            return $projects;
        }

        foreach($projects as $k=>&$v)
        {
            $v['Avatar'] = common::resources_full_path('avatar',$v['Avatar'],'picture');
            $v['StartDateString'] = empty($v['StartDate']) ? "无" : date('m/d',$v['StartDate']);
            $v['DueDateString'] = empty($v['DueDate']) ? "无" : date('m/d',$v['DueDate']);
            //获取项目进度
            $progress = $this->get_project_progress($v['Id']);
            $v['CompleteProgress'] = is_numeric($progress) ? $progress : 0;
            $v['DateProgress'] = $this->get_date_progress($v['StartDate'],$v['DueDate']);
            if($user_id !=0)
            {
                $v['Permission'] = $this->authorization_check($user_id,$v['Id'],false);
            }
        }

        return $projects;
    }


    public function consolidated_project_check($project_one,$project_two)
    {
        $one_childs = $this->project_model->select_info(array(),array('ParentId'=>$project_one,'Status'=>1));
        $two_childs = $this->project_model->select_info(array(),array('ParentId'=>$project_two,'Status'=>1));
        $result = -1;
        $msg = "系统繁忙";
        if(!empty($one_childs) && !empty($two_childs))
        {
            $msg = "您不能合并两个总项目";
        }
        else if(!empty($one_childs) && empty($two_childs))
        {
            //项目二收归到项目一
            $result = $this->consolidated_project($project_one,$project_two);
            $result = $result ? 0 : -1;
            $msg = $result ? "合并成功":"合并失败";
        }
        else if(empty($one_childs) && !empty($two_childs))
        {
            //项目一收归到项目二
            $result = $this->consolidated_project($project_two,$project_one);
            $result = $result ? 0 : -1;
            $msg = $result ? "合并成功":"合并失败";
        }
        else
        {
            //和并两个子项目，做权限检查
            $one_info = $this->project_model->select_info(array(),array('Id'=>$project_one),false);
            $two_info = $this->project_model->select_info(array(),array('Id'=>$project_two),false);
            if(isset($one_info['ProjectManagerId']) && isset($two_info['ProjectManagerId']))
            {
                if($one_info['ProjectManagerId'] == $two_info['ProjectManagerId'])
                {
                    $result = 10003;
                    $msg = "输入总项目名称";
                }
                else
                {
                    $msg = "您没有权限合并这两个项目";
                }
            }
        }

        return array('Result'=>$result,'Msg'=>$msg);
    }

    /**
     * @function 根据user_id获取与之相关的项目ID
     * @author Peter
     * @param int $user_id
     * @param int $anchor
     * @return mixed
     */
    public function get_rlt_project_ids($user_id, $anchor = 0)
    {
        //与user_id直接相关的项目ID、user_id关注的任务的项目ID
        $project_ids = $this->project_model->get_rlt_project_ids($user_id, $anchor);
        $project_ids = array_unique(array_column($project_ids, 'ProjectId'));
        if (!$project_ids)
        {
            return array();
        }
		if (($index = array_search(0, $project_ids)) !== FALSE)
		{
			unset($project_ids[$index]);
		}

        //以上项目的子项目ID
        $parent_ids = $project_ids;
        while($parent_ids)
        {
            $sub_project_ids = $this->project_model->get_sub_project_ids($parent_ids, $anchor);
            $sub_project_ids = array_column($sub_project_ids, 'Id');
            $project_ids = array_merge($project_ids, $sub_project_ids);
            $parent_ids = $sub_project_ids;
        }

        $project_ids = array_unique($project_ids);
        sort($project_ids);

        return $project_ids;
    }

    /**
     * @function 获取需要同步的项目
     * @author Peter
     * @param $project_ids
     * @param int $anchor
     * @return array
     */
    public function get_sync_projects($project_ids, $anchor = 0)
    {
        if (!is_array($project_ids) || !$project_ids)
        {
            return array();
        }

        $projects = $this->project_model->get_by_ids($project_ids, $anchor);

        array_walk($projects, function (&$val, $key, $table_name)
        {
            $val['Table'] = $table_name;
        }, 'Projects');

        return $projects;
    }


	public function get_sup_project_ids($project_ids, $anchor = 0)
	{
		$sup_project_ids = $this->project_model->get_by_ids($project_ids, $anchor, 'ParentId');
		$sup_project_ids = array_unique(array_column($sup_project_ids, 'ParentId'));
		if (($index = array_search(0, $sup_project_ids)) !== FALSE)
		{
			unset($sup_project_ids[$index]);
		}

		return $sup_project_ids;
	}

	/**
	 * @function 项目是否与用户相关
	 * @author Peter
	 * @param $project_id
	 * @param $user_id
	 * @return bool
	 */
	public function is_rlt($project_id, $user_id)
	{
		$project_id = intval($project_id);
		$user_id = intval($user_id);
		if ($project_id <= 0 || $user_id <= 0) {
			return FALSE;
		}

		//直接与用户相关的项目
		$this->load->model('dal/db/RltProjectUser_model', 'rpu');
		$result = $this->rpu
			->p_select('ProjectId')
			->p_where(array('UserId' => $user_id, 'ProjectId > ' => 0))
			->p_fetch();
		$project_ids = array_unique(array_column($result, 'ProjectId'));
		if (in_array($project_id, $project_ids))
		{
			return TRUE;
		}

		//与用户相关的任务所属的项目
		$this->load->model('dal/db/RltTaskUser_model', 'rtu');
		$this->load->model('bll/Task_model', 'task');
		$result = $this->rtu
			->p_select('TaskId')
			->p_where(array('UserId' => $user_id))
			->p_fetch();
		$task_ids = array_unique(array_column($result, 'TaskId'));
		foreach (array_chunk($task_ids, 500) as $ids)
		{
			$result = $this->task
				->p_select('ProjectId')
				->p_where(array('ProjectId >' => 0))
				->p_where_in('Id', $ids)
				->p_fetch();
			$temp_ids = array_unique(array_column($result, 'ProjectId'));
			$project_ids = array_merge($project_ids, $temp_ids);
		}
		$project_ids = array_unique($project_ids);
		if (!$project_ids)
		{
			return FALSE;
		}
		else if (in_array($project_id, $project_ids))
		{
			return TRUE;
		}

		//查出以上项目的父项目
		$this->load->model('bll/Project_model', 'project');
		foreach (array_chunk($project_ids, 500) as $ids)
		{
			$result = $this->project
				->p_select('ParentId')
				->p_where(array('ParentId >' => 0))
				->p_where_in('Id', $ids)
				->p_fetch();
			$temp_ids = array_unique(array_column($result, 'ParentId'));
			$project_ids = array_merge($project_ids, $temp_ids);
		}
		$project_ids = array_unique($project_ids);

		//验证数据
		$rlt_project_ids = array();
		foreach (array_chunk($project_ids, 500) as $ids)
		{
			$result = $this->project
				->p_select('Id')
				->p_where_in('Id', $ids)
				->p_fetch();
			$temp_ids = array_unique(array_column($result, 'Id'));
			$rlt_project_ids = array_merge($rlt_project_ids, $temp_ids);
		}

		return in_array($project_id, $rlt_project_ids);
	}

	public function statistics($project_id)
	{
		$this->load->model('dal/db/Project_model', 'project_dal');
		$this->load->model('dal/db/Task_model', 'task_dal');

		$where = array('Id' => $project_id);
		$project = $this->project_dal->p_exist($where);
		if (!$project)
		{
			return array();
		}

		$fields = 'DueDate, CompleteProgress';
		$where = array('ProjectId' => $project_id, 'Status' => 1);
		$tasks = $this->task_dal
			->p_select($fields)
			->p_where($where)
			->p_fetch();

		$now = time();
		$result['Count'] = count($tasks);
		$result['Finished'] = 0;
		$result['Overdue'] = 0;
		foreach ($tasks as $task)
		{
			if (intval($task['CompleteProgress']) === 100)
			{
				$result['Finished']++;
			}
			else if (intval($task['DueDate']) < $now)
			{
				$result['Overdue']++;
			}
		}
		$result['Progress'] = round($result['Finished'] / $result['Count'], 2);
		$result['Delay'] = round(1 - $result['Progress'], 2);
		$result['Unfinished'] = $result['Count'] - $result['Finished'];
		$result['Progress'] *= 100;
		$result['Delay'] *= 100;

		return $result;
	}

	/**
	 * @function 筛选项目日历信息
	 * @author Peter
	 * @param $project_id
	 * @param $start
	 * @param $end
	 * @param $user_id
	 * @return bool
	 */
	public function calendar_filter($project_id, $start, $end, $user_id)
	{
		$this->load->model('dal/db/Project_model', 'project_dal');
		$this->load->model('dal/db/Task_model', 'task_dal');

		$where = array('Id' => $project_id, 'Status' => 1);
		$project = $this->project_dal->p_exist($where);
		if (!$project)
		{
			return FALSE;
		}

		$fields = '*';
		$where = array('ProjectId' => $project_id, 'Status' => 1, 'StartDate <=' => $end, 'DueDate >=' => $start);
		$tasks = $this->task_dal
			->p_select($fields)
			->p_where($where)
			->p_fetch();

		array_walk($tasks, function (&$val, $key, $user_id) {
			$val['StartDateString'] = common::time_cycle($val['StartDate']);
			$val['CreateTimeString'] = common::time_cycle($val['CreateTime']);
			$val['DueDateString'] = common::time_cycle($val['DueDate']);
			$val['TrueDueDateString'] = $val['TrueDueDate'] ? "无" : date('Y年m月d日 H:i', $val['TrueDueDate']);
			$val['Permission'] = intval($val['CreatorId']) === $user_id ? 1 : 0;
		}, $user_id);

		return $tasks;
	}
}