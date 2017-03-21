<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Created by PhpStorm.
 * User: Peter
 * Date: 2016/4/5
 * Time: 10:33
 */
class Task_log_bll extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('dal/db/task_log_model');
    }

    public function create(array $data)
    {
        if (!is_array($data) || !$data)
        {
            return FALSE;
        }

        if (isset($data['Desc']))
        {
			if(empty($data['Desc']))
			{
				$data['Desc'] = "";
			}
			else
			{
				$data['Desc'] = json_encode($data['Desc']);
			}
        }
        $data['Method'] = 0;
        $data['Modified'] = $this->common_bll->get_max_modified();

        return $this->task_log_model->my_exec_insert($data);
    }

	/**
	 * @function 获取任务日志
	 * @author Peter
	 * @param $task_id
	 * @return array
	 */
	public function get_log($task_id)
	{
		$this->load->model('dal/db/Task_model', 'task_dal');
		$this->load->model('dal/db/Task_log_model', 'tl_dal');
		$this->load->model('dal/db/User_model', 'user_dal');

		$where = array('Id' => $task_id, 'Status' => 1);
		$task = $this->task_dal->p_exist($where);
		if (!$task)
		{
			return array();
		}

		$fields = 'UserId, Type, Desc, CreateTime';
		$where = array('TaskId' => $task_id, 'Status' => 1);
		$task_log = $this->tl_dal
			->p_select($fields)
			->p_where($where)
			->p_order_by('CreateTime', 'DESC')
			->p_fetch();
		$user_ids = array_unique(array_column($task_log, 'UserId'));
		if (!$user_ids)
		{
			return array();
		}

		$fields = 'Id, Name, Avatar';
		$users = $this->user_dal
			->p_select($fields)
			->p_where_in('Id', $user_ids)
			->p_fetch();
		$users = array_column($users, NULL, 'Id');
		if (!$users)
		{
			return array();
		}

		array_walk($users, function(&$value) {
			$value['Avatar'] = common::resources_full_path('avatar', $value['Avatar'], 'picture');
		});

		$types = array(
			1 => '创建',
			2 => '修改',
			3 => '完成',
			4 => '重做',
		);
		$desc = array(
			'title' => '名称',
			'description' => '描述',
			'startdate' => '开始时间',
			'duedate' => '结束时间',
			'priority' => '优先级',
			'ismilestone' => '是否里程碑',
			'completeprogress' => '完成度',
			'add_attention' => '添加关注人',
			'del_attention' => '移除关注人',
			'principal' => '责任人',
		);

		$result = array();
		foreach ($task_log as $log)
		{
			$tmp['User'] = $users[$log['UserId']]['Name'];
			$tmp['Avatar'] = $users[$log['UserId']]['Avatar'];
			$tmp['Type'] = $log['Type'];
			$tmp['TypeName'] = $types[intval($log['Type'])];
			$tmp['CreateTime'] = date('Y/m/d H:i', $log['CreateTime']);
			if ($log['Desc'] === '""' || $log['Desc'] === '[]' || !$log['Desc'])
			{
				$tmp['Desc'] = "";
				$result[] = $tmp;
				continue ;
			}
			$log['Desc'] = json_decode($log['Desc'], TRUE);
			foreach ($log['Desc'] as $key => &$item)
			{
				$field = strtolower($item['Field']);
				if (!isset($desc[$field]))
				{
					unset($log['Desc'][$key]);
					continue ;
				}
				$item['Field'] = $desc[$field];

				if (!in_array($field, array('startdate', 'duedate')))
				{
					continue ;
				}
				if (is_numeric($item['Old']) && $item['Old'] > 0)
				{
					$item['Old'] = date('Y/m/d H:i', intval($item['Old']));
				}
				if (is_numeric($item['New']) && $item['New'] > 0)
				{
					$item['New'] = date('Y/m/d H:i', intval($item['New']));
				}
			}
			$tmp['Desc'] = $log['Desc'];
			$result[] = $tmp;
		}

		return $result;
	}

}