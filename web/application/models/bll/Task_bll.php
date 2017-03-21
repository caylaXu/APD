<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Created by PhpStorm.
 * User: CaylaXu <caylaxu@motouch.cn>
 * Date: 2015/11/5
 * Time：19:51
 */
class Task_bll extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('dal/db/task_model');
        $this->load->model('dal/db/RltTaskUser_model');
        $this->load->model('bll/common_bll');
        $this->load->model('bll/Rlt_task_user_bll');
        $this->load->model('bll/checklist_bll');
        $this->load->model('bll/notice_bll');
    }

    public function task_list($params)
    {
        $task_list = $this->task_model->task_list($params);

        if(empty($task_list))
        {
            return $task_list;
        }

        foreach($task_list as $k=>&$v)
        {
            $v['StartDateString'] = empty($v['CreateTime']) ? "无":date('Y/m/d',$v['StartDate']);
            $v['CreateTimeString'] = empty($v['CreateTime']) ? "无":date('Y/m/d',$v['CreateTime']);
            $v['DueDateString'] = empty($v['DueDate']) ? "无":date('Y/m/d',$v['DueDate']);
            $v['TrueDueDateString'] = empty($v['TrueDueDate']) ? "无":date('Y/m/d',$v['TrueDueDate']);
            $v['ChildTask'] = $this->child_task($v['Id'],$params);
            $v['AssignedTo'] = $this->RltTaskUser_model->query_by_task_id($v['Id']);
        }
        return $task_list;
    }

    public function create(array $params){
        if(empty($params) || !is_array($params))
        {
            return false;
        }
        $params['Method'] = 0;
        $params['Modified'] = $this->common_bll->get_max_modified();
        $params['UniCode'] = common::create_uuid();
        return $this->task_model->create($params);
    }

    public function task_info($task_id)
    {
        $task = $this->task_model->task_info($task_id);
        if(empty($task))
        {
            return $task;
        }

        $task = $task[0];
        $task['Title'] = htmlspecialchars_decode($task['Title']);
        $task['Description'] = htmlspecialchars_decode($task['Description']);
        $task['StartDateString'] = common::time_cycle($task['StartDate']);
        $task['CreateTimeString'] = common::time_cycle($task['CreateTime']);
        $task['DueDateString'] = common::time_cycle($task['DueDate']);
        $task['TrueDueDateString'] = empty($task['TrueDueDate']) ? "无":date('Y/m/d',$task['TrueDueDate']);
		$directors = $this->Rlt_task_user_bll->get_rlt_user($task['Id'],'principal');
		if (is_array($directors) && count($directors) > 0)
		{
			$task['AssignedTo'] = $directors[0];
		}
		else
		{
			$task['AssignedTo'] = array();
		}
        $task['Follwers'] = $this->Rlt_task_user_bll->get_rlt_user($task['Id'],'attention');
        $task['Checklist'] = $this->checklist_bll->select_info(
            array('Id','Title','IsComplete'),array('TaskId'=>$task_id,'Status'=>1));

        return $task;
    }

    public function update(array $data,array $where)
    {
        $data['Method'] = 1;
        $data['Modified'] = $this->common_bll->get_max_modified();
        $result = $this->task_model->update_info($data,$where);
        return $result;
    }

    public function delete($task_id)
    {
        $info = $this->task_model->select_info(array('Id','ParentId'),array('Id'=>$task_id,'Status'=>1));
        if(empty($info))
        {
            return true;
        }
        $tasks = $this->task_model->select_info(array('Id','ParentId','CompleteProgress'),array('Status'=>1));
        $this->db->trans_start();
        //1、存在父级
        if(!empty($info[0]['ParentId']))
        {
            //2、查询父集的子类,如果只有自身一个子类
            $parent_child = $this->task_model->select_info(array('Id'),array('ParentId'=>$info[0]['ParentId'],'Status'=>1));
            if(count($parent_child) <= 1)
            {
                //3、更新父类的进度为当前任务的进度
                $progress = $this->get_task_progress($tasks,$task_id);
                $this->task_model->update_info(array('CompleteProgress'=>$progress),array('Id'=>$info[0]['ParentId']));
            }
        }
        //4、删除自身及子类
        $ids = common::get_child_id($tasks,$task_id);
        if(!empty($ids))
        {
            $ids[] = $task_id;
        }
        else
        {
            $ids = $task_id;
        }

        $modified = $this->common_bll->get_max_modified();
        $this->task_model->delete_by_id($ids,$modified);
        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE)
        {
            return false;
        }
        return true;
    }

    public function child_task($parent_id,$params)
    {
        $child_task = $this->task_model->child_task($parent_id,$params);

        if(empty($child_task))
        {
            return $child_task;
        }

        foreach($child_task as $k=>&$v)
        {
            $v['StartDateString'] = empty($v['CreateTime']) ? "无" : date('Y/m/d',$v['CreateTime']);
            $v['CreateTimeString'] = empty($v['CreateTime']) ? "无" : date('Y/m/d',$v['CreateTime']);
            $v['DueDateString'] = empty($v['CreateTime']) ? "无" : date('Y/m/d',$v['DueDate']);
            $v['TrueDueDateString'] = empty($v['CreateTime']) ? "无" : date('Y/m/d',$v['TrueDueDate']);
        }
        return $child_task;
    }

    public function change_progress($task_id,$progress,$user_id = 0)
    {
        $tasks = $this->task_model->select_info(array('Id','ParentId','CompleteProgress'),array('Status'=>1));
        $ids = common::get_child_id($tasks,$task_id);
        if(!empty($ids))
        {
            $ids[] = $task_id;
        }
        else
        {
            $ids = array();
            $ids[] = $task_id;
        }

        $data['FinisherId'] = $user_id;
        $data['CompleteProgress'] = empty($progress) ? 0 : 100;
        $data['TrueDueDate'] = empty($progress) ? 0 : time();
        $data['Method'] = 1;
        $data['Modified'] = $this->common_bll->get_max_modified();
        $update = $this->task_model->update_by_id($data,$ids);

        $data = array(
            'Status'=>1,
            'Method'=>1,
            'Modified' => $this->common_bll->get_max_modified()
        );

        $update_rlt = $this->RltTaskUser_model->update_by_task_id($data,$ids);

        if($progress == 100)//完成任务时通知任务责任人和关注人
        {
            $this->complete_the_task_notice($ids);
        }

        return $update && $update_rlt;
    }

    public function get_task_by_user_id($user_id,$params)
    {
        $array = $this->task_model->get_task_by_user_id($user_id, $params);
        if(empty($array))
        {
            return $array;
        }

        foreach($array as $k => &$v)
        {
            if(isset($params['Type']) && strtolower($params['Type']) != 'principal')
            {
                $v['RltUser'] = $this->Rlt_task_user_bll->get_rlt_user($v['Id'],'principal');
            }

            $v['Exceed'] = time() > $v['DueDate'] ? 1 : 0;//结束时间大于今天则标记为为期
            $v['StartDateString'] = common::time_cycle($v['StartDate']);
            $v['CreateTimeString'] = common::time_cycle($v['CreateTime']);
            $v['DueDateString'] = common::time_cycle($v['DueDate']);
            $v['TrueDueDateString'] = empty($v['TrueDueDate']) ? "无" : date('Y年m月d日 H:i',$v['TrueDueDate']);
            $v['Permission'] = 1;//我的待办有权限
            if(isset($params['Type']) && strtolower($params['Type']) == 'attention')
            {
                $v['Permission'] = $v['CreatorId'] == $user_id ? 1 : 0;
            }
        }
        return $array;
    }

    /**
     * @function 参数检查
     * @User: CaylaXu
     */
    public function check_data($params)
    {
        $task_id = isset($params['Id']) ? $params['Id'] : 0;
        $filed = array('Description', 'StartDate', 'DueDate', 'Priority', 'ProjectId','IsMilestone','ParentId','CreatorId');
        if(empty($params))
        {
            return false;
        }

        $data = array();

        foreach($params as $k => $v)
        {
            if($k == 'Title')
            {
                if(empty($v))
                {
                    return "标题不允许为空";
                }

                $data[$k] = $v;
            }
            else if($k == 'Progress')
            {
                $data['CompleteProgress'] = $v ? 100 : 0;
            }
            else if($k == 'StartDate' || $k == 'DueDate')
            {
                $data[$k] = is_numeric($v) ? $v : intval($v);
            }
            else if(in_array($k,$filed))
            {
                $data[$k] = $v;
            }
        }

        if(isset($params['StartDate']) && isset($params['DueDate']))//时间检查
        {
            if($params['StartDate'] > $params['DueDate'])
            {
                return "请确保开始时间小于结束时间哦！";
            }
        }
        else if(isset($params['DueDate']) && !empty($task_id))
        {
            $info = $this->task_model->select_info(array(),array('Id'=>$task_id),false);
            if(isset($info['StartDate']) && ($info['StartDate'] > $params['DueDate']))
            {
                return "请确保开始时间小于结束时间哦！";
            }
        }

        return $data;
    }


    /**
     * @function 手机端分页请求任务数据
     * @User: CaylaXu
     * @param $page
     * @param $limit
     * @param $params
     * @return mixed
     */
    public function paging_get_task_by_user_id($page, $limit, $params)
    {
        //今日待办
        if(isset($params['Type']) && in_array($params['Type'],array('backlog','attention','collection')))
        {
            $array = $this->task_model->paging_get_task_by_user_id($page, $limit, $params);
        }
        else if(isset($params['Type'])  && $params['Type'] == 'todotomorrow')
        {
            $params['Time'] = 'tomorrow';
            $array = $this->task_model->paging_get_task_by_params($page, $limit, $params);
        }
        else if(isset($params['Type'])  && $params['Type'] == 'done')
        {
            $array = $this->task_model->paging_get_task_done_by_user_id($page, $limit, $params);
        }
        else
        {
            $array = $this->task_model->paging_get_task_by_params($page, $limit, $params);
        }

        if(empty($array))
        {
            return $array;
        }

        foreach($array as $k => &$v)
        {
            if(isset($params['Type']) && strtolower($params['Type']) == 'attention')
            {
                $v['RltUser'] = $this->Rlt_task_user_bll->get_rlt_user($v['Id'],'principal');
            }

            $v['Exceed'] = time() > $v['DueDate'] ? 1 : 0;//结束时间大于今天则标记为过期
            $v['ExceedDays'] = common::count_days($v['DueDate'],time());//结束时间大于今天则标记为过期
            $v['StartDateString'] = common::time_cycle($v['StartDate']);
            $v['CreateTimeString'] = common::time_cycle($v['CreateTime']);
            $v['DueDateString'] = common::time_cycle($v['DueDate']);
            $v['TrueDueDateString'] = empty($v['TrueDueDate']) ? "无" : date('Y年m月d日 H:i',$v['TrueDueDate']);
            $v['Permission'] = 1;//我的待办有权限
            if(isset($params['Type']) && strtolower($params['Type']) == 'attention')
            {
                $v['Permission'] = $v['CreatorId'] == $params['UserId'] ? 1 : 0;
            }
        }
        return $array;
    }

    /**
     * @function
     * @User: CaylaXu
     * @param $page
     * @param $limit
     * @param $params
     * @return mixed
     */
    public function task_tree_by_project_id($project_id)
    {
        $tasks = $this->task_model->tasks_by_project_id($project_id);
        if(empty($tasks))
        {
            return $tasks;
        }
        $array = $tasks;
        foreach($tasks as $k => &$v)
        {
            $v['AssignedTo'] = $this->Rlt_task_user_bll->get_rlt_user($v['Id'],'principal');
            $v['StartDateString'] = common::time_cycle($v['StartDate']);
            $v['DueDateString'] = common::time_cycle($v['DueDate']);
            //获取项目进度
            $progress = $this->get_task_progress($array,$v['Id']);
            if(is_numeric($progress))
            {
                $v['CompleteProgress'] = $progress;
            }
        }

        return common::generate_tree($tasks);
    }

    public function get_task_progress($tasks,$pid)
    {
        $tree = common::generate_tree($tasks,$pid);
        if(empty($tree))
        {
            return 0;
        }

        return common::get_progress($tree);
    }



    public function check_params($params)
    {
        if(isset($params['Title']))
        {
            $title = common::check_string_length(1,750,$params['Title']);
            if(!$title)
            {
                return '标题过长';
            }
        }

        if(isset($params['Description']))
        {
            $description = common::check_string_length(0,750,$params['Description']);
            if(!$description)
            {
                return '描述过长';
            }
        }

        if(isset($params['StartDate']) && isset($params['DueDate']))
        {
            $check_date = $params['StartDate'] <= $params['DueDate'];

            if(!$check_date)
            {
                return "开始日期不允许大于结束日期";
            }
        }

        return true;
    }

    public function get_task_by_params($user_id,$params)
    {
        $array = $this->task_model->get_task_by_params($user_id, $params);

        if(empty($array))
        {
            return $array;
        }

		$this->load->model('dal/Project_model', 'project_dal');
		$project_ids = array_unique(array_column($array, 'ProjectId'));
		$fields = 'Id, Title';
		$projects = $this->project_dal->get_by_ids($project_ids, 0, $fields);
		$projects = array_column($projects, 'Title', 'Id');
        foreach($array as $k => &$v)
        {
            $v['AssignedTo'] = $this->Rlt_task_user_bll->get_rlt_user($v['Id'],'principal');
            $v['Exceed'] = time() > $v['DueDate'] ? 1 : 0;//结束时间大于今天则标记为为期
            $v['StartDateString'] = common::time_cycle($v['StartDate']);
            $v['CreateTimeString'] = common::time_cycle($v['CreateTime']);
            $v['DueDateString'] = common::time_cycle($v['DueDate']);
            $v['TrueDueDateString'] = empty($v['TrueDueDate']) ? "无" : date('Y年m月d日 H:i',$v['TrueDueDate']);
//            $v['Permission'] = 1;//我的待办有权限
            $v['Permission'] = $v['CreatorId'] == $user_id ? 1 : 0;
			$v['ProjectName'] = isset($projects[$v['ProjectId']]) ? $projects[$v['ProjectId']] : '无';
        }
        return $array;
    }


    public function paging_tasks_by_project($project_id,$page, $limit, $params)
    {
        $array = $this->task_model->paging_tasks_by_project($project_id,$page, $limit, $params);
        if(empty($array))
        {
            return $array;
        }

        if(!empty($array))
        {
            foreach($array as $k => &$v)
            {
                $v['RltUser'] = $this->Rlt_task_user_bll->get_rlt_user($v['Id'],'principal');
                $v['Exceed'] = time() > $v['DueDate'] ? 1 : 0;//结束时间大于今天则标记为过期
                $v['StartDateString'] = common::time_cycle($v['StartDate']);
                $v['CreateTimeString'] = common::time_cycle($v['CreateTime']);
                $v['DueDateString'] = common::time_cycle($v['DueDate']);
                $v['TrueDueDateString'] = empty($v['TrueDueDate']) ? "无" : date('Y年m月d日 H:i',$v['TrueDueDate']);
            }
        }
        return $array;
    }

    public function select_info(array $select,array $where,$flag = true)
    {
       return $this->task_model->select_info($select,$where,$flag);
    }

    public function move_left($info,$brothers)
    {
        if(empty($info))
        {
            return false;
        }

        if($info['ParentId'] == 0)
        {
            return true;
        }

        $parent_info = $this->task_model->select_info(array(),array('Id'=>$info['ParentId'],'Status'=>1),false);

        if(empty($parent_info))
        {
            return false;
        }

        $this->db->trans_start();

        //1、更新本任务的parentid和父级的parentid相同
        $this->update(array('ParentId'=>$parent_info['ParentId']),array('Id'=>$info['Id']));

        $sort = $parent_info['Sort']+1;

        //2、更新排序字段
        $this->update_sort($info['Id'],$sort);

        //3、更新子类的parentid等于当前任务的自身id
        if(!empty($brothers))
        {
            $data = array(
                'ParentId' => $info['Id'],
            );
            $data['Method'] = 1;
            $data['Modified'] = $this->common_bll->get_max_modified();
            $brothers = is_array($brothers) ? $brothers : explode(',',$brothers);
            $this->task_model->update_by_id($data,$brothers);
        }

        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE)
        {
            return false;
        }
        return true;
    }

    public function calendar_tasks_by_params($user_id,$params)
    {
        $array = $this->task_model->calendar_tasks_by_params($user_id, $params);

        if(empty($array))
        {
            return $array;
        }

        foreach($array as $k => &$v)
        {
            //加上类型值 1:我的待办 2：我的已办 3：我的关注
            if(!empty($v['CompleteProgress']))
            {
                $v['Type'] = "2";
            }

            $v['StartDateString'] = common::time_cycle($v['StartDate']);
            $v['CreateTimeString'] = common::time_cycle($v['CreateTime']);
            $v['DueDateString'] = common::time_cycle($v['DueDate']);
            $v['TrueDueDateString'] = empty($v['TrueDueDate']) ? "无" : date('Y年m月d日 H:i',$v['TrueDueDate']);
            $v['Permission'] = $v['CreatorId'] == $user_id ? 1 : 0;
        }
        return $array;
    }

    /**
     * @function 补全任务字段（子任务继承父任务时间与责任人)
     * @User: CaylaXu
     * @param $params
     * @return array
     */
    public function completion_tasks($params)
    {
        if(empty($params))
        {
            return array();
        }

        $parent_id = isset($params['ParentId']) ? $params['ParentId'] : '';
        if(is_numeric($parent_id) && !empty($parent_id))
        {
            $parent_info = $this->task_model->select_info(array('StartDate','DueDate'),array('Id'=>$parent_id),false);

            if(!empty($parent_info))
            {
                $params['StartDate'] = $parent_info['StartDate'];
                $params['DueDate'] = $parent_info['DueDate'];
            }

            $rlt_user = $this->Rlt_task_user_bll->get_rlt_user_id($parent_id,'principal');
            if(!empty($rlt_user))
            {
                $params['AssignedTo'] = $rlt_user;
            }
        }
        return $params;
    }

    /**
     * @function 初始化一条待办
     * @User: CaylaXu
     * @param $content 待办的内容
     * @param $user_id 关联用户id
     * @return bool
     */
    public function initial_task($content,$user_id)
    {
        $data = array(
            'Title' => $content,
            'ProjectId'=> 0,
            'StartDate'=>time(),
            'DueDate' => strtotime("+1 day"),
            'Priority'=>0,
            'CreatorId' => $user_id
        );

        $task_id = $this->create($data);

        if(empty($task_id))
        {
            return false;
        }

        $rlt_result = $this->Rlt_task_user_bll->insert_batch($task_id,array($user_id));

        if(!$rlt_result)
        {
            return false;
        }

        return true;
    }


    /**
     * @function
     * @User: CaylaXu
     * @param $id 当前要更新的任务Id
     * @param $pre_sort 前一条任务的排序
     * @param $next_sort 后一条任务的排序
     * @return mixed
     */
    function sort($id,$pre_sort,$next_sort)
    {
        if($pre_sort == 0)//插入到队首
        {
            $sort = $next_sort-1;
            if($sort < 0)
            {
                $sort = $next_sort;
            }
        }
        else//移动到队尾或中间
        {
            $sort = $pre_sort + 1;
        }

        $update = $this->update_sort($id,$sort);
        return $update;
    }

    /**
     * @function 递归更新排序
     * @User: CaylaXu
     * @param $id 当前要更新的任务id
     * @param $sort 需要更改为的排序字段
     * @return bool
     */
    function update_sort($id,$sort)
    {
        $info = $this->task_model->select_info(array(),array('Id'=>$id,'Status'=>1),false);

        if(empty($info))//1、数据错误，要更新的任务不存在
        {
            return false;
        }

        $next_info = $this->task_model->get_next_sort_task($info['ProjectId'],$info['ParentId'],$sort);

        if(isset($next_info['Sort']) && ($sort >= $next_info['Sort']))//异常出现，排序字段相等了
        {
            $result_self = $this->update(array('Sort'=>$sort),array('Id'=>$id));

            //递归更新下一条
            $result_next = $this->update_sort($next_info['Id'],$sort+1);

            return $result_self&&$result_next;
        }
        else
        {
            $result = $this->update(array('Sort'=>$sort),array('Id'=>$id));

            return $result;
        }
    }

    /**
     * @function 项目下的不同状态任务统计
     * @User: CaylaXu
     * @param $project_id
     * @param $type
     * @return mixed
     */
    function count_task_by_project($project_id,$type = '')
    {
        $count = $this->task_model->count_task_by_project($project_id,$type);
        if(empty($count))
        {
            return 0;
        }

        return $count['Sum'];
    }


    function get_max_child_sort($task_id)
    {
        $result = $this->task_model->get_max_child_sort($task_id);
        if(empty($result))
        {
            return 0;
        }
        else
        {
            return $result['Sort'];
        }
    }

    /**
     * @function 根据项目ID查出所有任务负责人
     * @author Peter
     * @param int $project_id
     * @return mixed
     */
    public function get_directors_by_project_id($project_id)
    {
        $project_id = intval($project_id);
        if ($project_id <= 0)
        {
            return array();
        }

        $directors = $this->task_model->get_directors_by_project_id($project_id);

        foreach($directors as &$director)
        {
            $director['Avatar'] = common::resources_full_path('avatar',$director['Avatar'],'picture');
        }

        return $directors;
    }

    public function get_current_tasks($user_id)
    {
        $result = $this->task_model->get_current_tasks($user_id);
        if(!empty($result))
        {
            return $result[0];
        }
        return array();
    }

    /**
     * @function 修改项目
     * @User: CaylaXu
     * @param $task_id
     * @param $project_id
     */
    public function change_project($task_id,$project_id)
    {
        $task_current = $this->task_model->select_info(array('ProjectId','ParentId'), array('Id' => $task_id), false);
        if (!$task_current)
        {
            common::return_json('-1','该任务不存在','',true);
        }
        $init_project_id = $task_current['ProjectId'];

        if($project_id == $init_project_id)
        {
            return true;
        }

        //1、更改自身的ParentId为0

        if(!empty($task_current['ParentId']))
        {
            $result = $this->update(array('ParentId'=>0),array('Id'=>$task_id));
            if(!$result)
            {
                return false;
            }
        }

        //2、更改子级的ProjectId为新的ProjectId
        $tasks = $this->task_model->select_info(array('Id','ParentId'),array('Status'=>1));
        $ids = common::get_child_id($tasks,$task_id);
        if(!empty($ids))
        {
            $ids[] = $task_id;
        }
        else
        {
            $ids = $task_id;
        }
        $data['ProjectId'] = $project_id;
        $data['Method'] = 1;
        $data['Modified'] = $this->common_bll->get_max_modified();
        $result =  $this->task_model->update_by_id($data,$ids);
        if(!$result)
        {
            return false;
        }

        //修改所属项目，查出责任人
        $where = array('TaskId' => $task_id, 'Type' => 1,'Status >'=>0);
        $rlt_task_users = $this->rlt_task_user_bll->select_info(array('UserId'), $where);

        if(empty($rlt_task_users))
        {
            return true;
        }

        //该任务有责任人，将责任人添加为目标项目的项目成员
        $directors = array_column($rlt_task_users, 'UserId');
        $result = $this->rlt_project_user_bll->insert_batch($project_id, $directors, 2);
        return $result;
    }

    /**
     * @function 根据user_id获取与之相关的任务ID
     * @author Peter
     * @param int $user_id
     * @param int $anchor
     * @return mixed
     */
    public function get_rlt_task_ids($user_id, $anchor = 0)
    {
        //与user_id直接相关的任务ID、user_id关注的项目的任务ID
        $task_ids = $this->task_model->get_rlt_task_ids($user_id, $anchor);
        $task_ids = array_unique(array_column($task_ids, 'TaskId'));
        if (!$task_ids)
        {
            return array();
        }
		if (($index = array_search(0, $task_ids)) !== FALSE)
		{
			unset($task_ids[$index]);
		}

        //以上任务的子任务ID
        $parent_ids = $task_ids;
        while($parent_ids)
        {
            $sub_task_ids = $this->task_model->get_sub_task_ids($parent_ids, $anchor);
            $sub_task_ids = array_column($sub_task_ids, 'Id');
            $task_ids = array_merge($task_ids, $sub_task_ids);
            $parent_ids = $sub_task_ids;
        }

        $task_ids = array_unique($task_ids);
        sort($task_ids);

        return $task_ids;
    }

    /**
     * @function 获取需要同步的任务
     * @author Peter
     * @param $task_ids
     * @param $anchor
     * @return mixed
     */
    public function get_sync_tasks($task_ids, $anchor = 0)
    {
        if (!is_array($task_ids) || !$task_ids)
        {
            return array();
        }

        $tasks = $this->task_model->get_by_ids($task_ids, $anchor);

        array_walk($tasks, function (&$val, $key, $table_name)
        {
            $val['Table'] = $table_name;
        }, 'Tasks');

        return $tasks;
    }


    /**
     * @function 完成任务时通知相关责任人和关注人
     * @User: CaylaXu
     * @param array $task_ids
     * @return bool
     */
    public function complete_the_task_notice(array $task_ids)
    {
        if(empty($task_ids))
        {
            return true;
        }

        $CI=&get_instance();
        if(isset($CI->user_id))
        {
            $user_id = $CI->user_id;

            foreach($task_ids as $k=>$v)
            {
                $rlt_users = $this->RltTaskUser_model->get_user_ids_string_by_task_id($v);

                if(isset($rlt_users['UserIds']) && !empty($rlt_users['UserIds']))
                {
                    $result = $this->notice_bll->notice($user_id,$rlt_users['UserIds'],7,array('RltId'=>$v));
                    if(!$result)
                    {
                        return false;
                    }
                }
            }
        }
        return true;
    }

    public function get_tasks_by_type($user_id,$type = '',$params = array())
    {
        $this->load->model('dal/db/project_model');
        $type = strtolower($type);
        switch( $type )
        {
            case '':
                $result = $this->task_model->get_task_by_type($user_id,$type,$params);
                break;
            case 'concerned'://我的关注
                $result = $this->task_model->get_task_by_type($user_id,$type,$params);
                break;
            case 'created'://我创建的
                $result = $this->task_model->get_task_by_creator_id($user_id,$params);
                break;
            case 'finished'://我完成的
                $result = $this->task_model->get_task_by_finisher_id($user_id,'all',$params);
                break;
            case 'todayfinished'://今日完成
                $params['TrueDueDate'] = time();
                $result = $this->task_model->get_task_by_finisher_id($user_id,'',$params);
                break;
            case 'concernedfinished'://关注完成
                $params['TrueDueDate'] = time();
                $result = $this->task_model->get_task_by_finisher_id($user_id,'concerned',$params);
                break;
            case 'all'://全部待办
                $result = $this->task_model->get_task_by_type($user_id,$type,$params);
                break;
            default:
                $result = array();
        }

        if(empty($result))
        {
            return $result;
        }

        foreach($result as $k => &$v)
        {
            $v['Exceed'] = time() > $v['DueDate'] ? 1 : 0;//结束时间大于今天则标记为为期
            $v['StartDateString'] = common::time_cycle($v['StartDate']);
            $v['CreateTimeString'] = common::time_cycle($v['CreateTime']);
            $v['DueDateString'] = common::time_cycle($v['DueDate']);
            $v['TrueDueDateString'] = empty($v['TrueDueDate']) ? "无" : date('Y年m月d日 H:i',$v['TrueDueDate']);

            //权限判断
            {
                if('concerned' == $type)
                {
                    $v['Permission'] = 0;//我的关注都没有权限
                }
                else if(in_array($type,array('','created','all')))
                {
                    $v['Permission'] = 1;//我的待办有权限
                }
                else if(in_array($type,array('finished','todayfinished','concernedfinished')))
                {
                    $v['Permission'] = 0;
                    if($v['CreatorId'] == $user_id)
                    {
                        $v['Permission'] = 1;
                    }
                    else
                    {
                        $rlt = $this->RltTaskUser_model->select_info(array('Id'),array('TaskId'=>$v['Id'],'UserId'=>$user_id,'Type !='=>3,'Status >'=>0));
                        if(!empty($rlt))
                        {
                            $v['Permission'] = 1;
                        }
                    }
                }
            }

            if(isset($v['ProjectId']) && !empty($v['ProjectId']))
            {
                $project = $this->project_model->select_info(array('Title'),array('Id'=>$v['ProjectId'],'Status'=>1),false);
                if(isset($project['Title']))
                {
                    $v['ProjectName'] = $project['Title'];
                }
            }

            if($type != '')
            {
                $v['RltUser'] = $this->Rlt_task_user_bll->get_rlt_user($v['Id'],'principal');
            }
        }
        return $result;
    }

	/**
	 * @function 撤销删除任务
	 * @author Peter
	 * @param $task_id
	 * @return bool
	 */
	public function undo($task_id)
	{
		$this->load->model('dal/db/Task_model', 'task_dal');

		$where = array('Id' => $task_id);
		$task = $this->task_dal->p_exist($where);
		if (!$task)
		{
			return FALSE;
		}

		$update = array('Status' => 1);
		$where = array('Id' => $task_id);
		$result = $this->task_dal->p_update($update, $where);

		return $result;
	}
}