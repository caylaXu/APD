<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Created by PhpStorm.
 * User: CaylaXu <caylaxu@motouch.cn>
 * Date: 2015/11/5
 * Time：19:51
 */
class Rlt_task_user_bll extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('dal/db/RltTaskUser_model');
        $this->load->model('bll/common_bll');
        $this->load->model('dal/db/Task_model');
        $this->load->model('bll/notice_bll');
        $this->load->model('bll/task_log_bll');
    }

    public function insert_batch($task_id,$params,$type = 1,$remind = -1)
    {
        if(is_string($params))
        {
            $params = explode(',',$params);
        }

        if(empty($params))
        {
            return false;
        }


        if($type == 1)//值提醒责任人
        {
            if($remind == -1)//remind没有传值时，通过原有关联取提醒数据
            {
                $info = $this->RltTaskUser_model->select_info(array('Remind'),array('TaskId'=>$task_id,'Type'=>$type,'Status >'=>0),false);
                $remind = isset($info['Remind']) ? $info['Remind'] : '';
            }
        }
        else
        {
            $remind = '';
        }

        $notice_type = 5;
        switch($type){
            case 1:
                $notice_type = 5;
                break;   // 跳出循环
            case 3:
                $notice_type = 6;
                break;
        }

        $notice_ids = array();
        $data = array();
        $modified = $this->common_bll->get_max_modified();
        $this->db->trans_start();

        foreach($params as $k=>$v)
        {
            $exit = $this->RltTaskUser_model->select_info(array('Id','Status','Type'),array('TaskId'=>$task_id,'UserId'=>$v,'Type'=>$type));
            if(count($exit) > 0)
            {
                if(!($exit[0]['Type']==$type && $exit[0]['Status'] > 0))//排除已存在情况
                {
                    $notice_ids[] = $v;
                }
                    //更新操作
                    $update = array(
                        'Remind' => $remind,
                        'Type' => $type,
                        'Method' => 1,
                        'Modified'=> $modified,
                        'Status'=>1,
                    );
                    $this->RltTaskUser_model->update_info($update,array('TaskId'=>$task_id,'UserId'=>$v));
            }
            else
            {
                $notice_ids[] = $v;
                $temp['TaskId'] = $task_id;
                $temp['UserId'] = $v;
                $temp['Type'] = $type;
                $temp['Method'] = 0;
                $temp['Modified'] = $modified;
                $temp['Remind'] = $remind;
                $data[] = $temp;
            }
        }

        if(!empty($data))
        {
            $this->RltTaskUser_model->insert_batch($data);
        }

        //通知相关的人
        $CI=&get_instance();
        if(isset($CI->user_id))
        {
            $this->notice_bll->notice($CI->user_id,$notice_ids,$notice_type,array('RltId'=>$task_id));
        }

        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE)
        {
            return false;
        }
        return true;
    }

    /**
     * @function 批量更新
     * @User: CaylaXu
     * @param $task_id
     * @param $params
     * @return bool
     */
    public function update($task_id,$params)
    {
        $modified = $this->common_bll->get_max_modified();
        $delete = $this->RltTaskUser_model->delete_info(array('TaskId'=>$task_id),$modified);

        if(!$delete)
        {
            return false;
        }

        if(is_string($params))
        {
            $params = explode(',',$params);
        }

        if(empty($params))
        {
            return false;
        }

        $data = array();
        $modified = $this->common_bll->get_max_modified();

        foreach($params as $k=>$v)
        {
            $temp['TaskId'] = $task_id;
            $temp['UserId'] = $v;
            $temp['Method'] = 0;
            $temp['Status'] = 1;
            $temp['Modified'] = $modified;
            $data[] = $temp;
        }

        return $this->RltTaskUser_model->insert_batch($data);
    }


    public function get_rlt_user($task_id,$type)
    {
        $res = $this->RltTaskUser_model->query_by_task_id($task_id,$type);

        if(empty($res))
        {
            return $res;
        }

        foreach($res as $k=>&$v)
        {
            $v['Avatar'] = common::resources_full_path('avatar',$v['Avatar'],'picture');
        }

        return $res;
    }

    public function create($task_id,$user_id,$type = 1)
    {
        $notice_type = 5;
        switch($type){
            case 1:
                $notice_type = 5;
                break;   // 跳出循环
            case 3:
                $notice_type = 6;
                break;
        }

        $exit = $this->RltTaskUser_model->select_info(array('Id','Status','Type'),array('TaskId'=>$task_id,'UserId'=>$user_id,'Type'=>$type));
        //如果是责任人则添加到项目
        $this->insert_user_to_project($task_id,$user_id,$type);

        if(count($exit) > 0)
        {
            //更新操作
            $update = array(
                'Type' => $type,
                'Method' => 1,
                'Modified'=> $this->common_bll->get_max_modified(),
                'Status'=>1
            );

            if(!$this->RltTaskUser_model->update_info($update,array('TaskId'=>$task_id,'UserId'=>$user_id)))
            {
                return false;
            }

            if(!($exit[0]['Type']==$type && $exit[0]['Status'] > 0))//排除已存在情况
            {
                //通知相关的人
                $CI=&get_instance();
                if(isset($CI->user_id))
                {
                    $this->notice_bll->notice($CI->user_id,$user_id,$notice_type,array('RltId'=>$task_id));
                }
            }

            return $exit[0]['Id'];
        }
        else
        {
            $data = array(
                'TaskId'=>$task_id,
                'UserId' => $user_id,
                'Type' => $type,
                'Status' => 1,
            );
            $data['Method'] = 0;
            $data['Modified'] = $this->common_bll->get_max_modified();

            //通知相关的人
            $CI=&get_instance();
            if(isset($CI->user_id))
            {
                $this->notice_bll->notice($CI->user_id,$user_id,$notice_type,array('RltId'=>$task_id));
            }

            return $this->RltTaskUser_model->create($data);
        }
    }

    public function select_info(array $select,array $where)
    {
        return $this->RltTaskUser_model->select_info($select,$where);
    }

    /**
     * @function 条件更新
     * @User: CaylaXu
     * @param array $data
     * @param array $where
     * @return mixed
     */
    public function update_where(array $data,array $where)
    {
        $data['Method'] = 1;
        $data['Modified'] = $this->common_bll->get_max_modified();
        return $this->RltTaskUser_model->update_info($data,$where);
    }

    public function delete($id)
    {
        $modified = $this->common_bll->get_max_modified();
        return $this->RltTaskUser_model->delete_info(array('Id'=>$id),$modified);
    }

    public function insert_user_to_project($task_id,$params,$type = 1)
    {
        if($type != 1)
        {
            return true;
        }
        $this->load->model('bll/Rlt_project_user_bll');
        $task_info = $this->Task_model->select_info(array('Id','ProjectId'),array('Id'=>$task_id));
        if(empty($task_info))
        {
            return true;
        }

        $project_id = $task_info[0]['ProjectId'];

        if(empty($project_id))
        {
            return true;
        }

        return $this->Rlt_project_user_bll->insert_batch($project_id,$params,$type);
    }

    public function update_rlt_user($task_id,$user_ids,$type = 1,$remind = -1)
    {
        $old_ids_array = $this->RltTaskUser_model->select_info(array('UserId'),array('TaskId'=>$task_id,'Status >'=>0,'Type'=>$type));
        $old_ids = array();
        if(!empty($old_ids_array))
        {
            foreach($old_ids_array as $k=>$v)
            {
                $old_ids[] = $v['UserId'];
            }
        }

        if(is_string($user_ids))
        {
            $user_ids = explode(',',$user_ids);
        }

        //存在old中不存在后面
        $delete = array_diff($old_ids,$user_ids);
        $add = array_diff($user_ids,$old_ids);

        if(!empty($delete))
        {
            $modified = $this->common_bll->get_max_modified();
            $res = $this->RltTaskUser_model->delete_rows_by_user_id($task_id,$delete,$type,$modified);
            if(!$res)
            {
                return false;
            }
        }

        if(!empty($user_ids))
        {
            //@todo 增加新添加的人
            $res = $this->insert_batch($task_id,$user_ids,$type,$remind);
            if(!$res)
            {
                return false;
            }
        }

        return true;
    }

    /**
     * @function 获取任务相关人员
     * @User: CaylaXu
     * @param $task_id
     * @param $type
     * @return string id拼接字符串
     */
    public function get_rlt_user_id($task_id,$type)
    {
        $res = $this->RltTaskUser_model->get_rlt_user_id($task_id,$type);

        if(isset($res['UserIds']))
        {
            return $res['UserIds'];
        }

        return '';
    }

    /**
     * @function 获取需要同步的RltTaskUser
     * @author Peter
     * @param $task_ids
     * @param int $anchor
     * @return mixed
     */
    public function get_sync_rtus($task_ids, $anchor = 0)
    {
        if (!is_array($task_ids) || !$task_ids)
        {
            return array();
        }

        $tasks = $this->RltTaskUser_model->get_sync_rtus($task_ids, $anchor);

        array_walk($tasks, function (&$val, $key, $table_name)
        {
            $val['Table'] = $table_name;
        }, 'RltTaskUser');

        return $tasks;
    }

	/**
	 * @function 添加任务责任人
	 * @author Peter
	 * @param $task_id
	 * @param $user_id
	 * @return bool
	 */
	public function add_director($task_id, $user_id)
	{
		$this->load->model('dal/db/User_model', 'user_dal');
		$this->load->model('dal/db/Task_model', 'task_dal');
		$this->load->model('dal/db/RltTaskUser_model', 'rtu_dal');

		//校验数据
		$where = array('Id' => $task_id, 'Status !=' => -1);
		$task = $this->task_dal->p_exist($where);
		if (!$task)
		{//任务不存在或已删除
			return FALSE;
		}
		$where = array('Id' => $user_id, 'Status !=' => -1);
		$user = $this->user_dal->select_info(array('Name'),$where,false);
		if (empty($user))
		{//用户不存在或已删除
			return FALSE;
		}

		//是否已存在关联
		$fields = 'Status';
		$where = array('TaskId' => $task_id, 'UserId' => $user_id, 'Type' => 1);
		$task_user = $this->rtu_dal
			->p_select($fields)
			->p_where($where)
			->p_fetch(TRUE);

		//启用事务
		$this->db->trans_start();
		//1、删除该任务所有的责任人
		$this->rtu_dal->p_delete(array('TaskId' => $task_id, 'UserId !=' => $user_id, 'Type' => 1));
		//2、添加/更新关联
		if ($task_user)
		{
			if (intval($task_user['Status'] !== 1))
			{
				$update = array('Status' => 1);
				$where = array('TaskId' => $task_id, 'UserId' => $user_id, 'Type' => 1);
				$this->rtu_dal->p_update($update, $where);
			}
		}
		else
		{
			$insert = array('TaskId' => $task_id, 'UserId' => $user_id, 'Type' => 1);
			$this->rtu_dal->p_insert($insert);
		}

        //3、通知责任人
        $CI=&get_instance();
        $this->notice_bll->notice($CI->user_id,$user_id,5,array('RltId'=>$task_id));

        //4、记录修改日志
        $log['UserId'] = $CI->user_id;
        $log['TaskId'] = $task_id;
        $log['Type'] = 2;
        $log['CreateTime'] = time();
        $log['Desc'] = array();
        $temp['Field'] = 'principal';
        $temp['Old'] = '';
        $temp['New'] = $user['Name'];
        $log['Desc'][] = $temp;
        $this->task_log_bll->create($log);
		$this->db->trans_complete();

		return $this->db->trans_status();
	}

	/**
	 * @function 移除任务责任人
	 * @author Peter
	 * @param $task_id
	 * @return bool
	 */
	public function remove_director($task_id)
	{
		$this->load->model('dal/db/Task_model', 'task_dal');
		$this->load->model('dal/db/RltTaskUser_model', 'rtu_dal');

		//校验数据
		$where = array('Id' => $task_id, 'Status !=' => -1);
		$task = $this->task_dal->p_exist($where);
		if (!$task)
		{//任务不存在或已删除
			return FALSE;
		}

		//是否已存在关联
		$where = array('TaskId' => $task_id, 'Status !=' => -1, 'Type' => 1);
		$task_user = $this->rtu_dal->p_exist($where);
		if (!$task_user)
		{//关联不存在或已删除
			return TRUE;
		}

		//删除关联
		$where = array('TaskId' => $task_id, 'Type' => 1);
		$result = $this->rtu_dal->p_delete($where);

		return $result;
	}

	public function add_follower($task_id, $user_id)
	{
		$this->load->model('dal/db/User_model', 'user_dal');
		$this->load->model('dal/db/Task_model', 'task_dal');
		$this->load->model('dal/db/RltTaskUser_model', 'rtu_dal');

		//校验数据
		$where = array('Id' => $task_id, 'Status !=' => -1);
		$task = $this->task_dal->p_exist($where);
		if (!$task)
		{//任务不存在或已删除
			return FALSE;
		}
		$where = array('Id' => $user_id, 'Status !=' => -1);
		$user = $this->user_dal->p_exist($where);
		if (!$user)
		{//用户不存在或已删除
			return FALSE;
		}

		//是否已存在关联
		$fields = 'Status';
		$where = array('TaskId' => $task_id, 'UserId' => $user_id, 'Type' => 3);
		$task_user = $this->rtu_dal
			->p_select($fields)
			->p_where($where)
			->p_fetch(TRUE);

		//添加/更新关联
		if ($task_user)
		{
			if (intval($task_user['Status']) !== 1)
			{
				$update = array('Status' => 1);
				$where = array('TaskId' => $task_id, 'UserId' => $user_id, 'Type' => 3);
				$result = $this->rtu_dal->p_update($update, $where);
			}
			else
			{
				$result = TRUE;
			}
		}
		else
		{
			$insert = array('TaskId' => $task_id, 'UserId' => $user_id, 'Type' => 3);
			$result = $this->rtu_dal->p_insert($insert);
		}

		return $result;
	}

	public function remove_follower($task_id, $user_id)
	{
		$this->load->model('dal/db/User_model', 'user_dal');
		$this->load->model('dal/db/Task_model', 'task_dal');
		$this->load->model('dal/db/RltTaskUser_model', 'rtu_dal');

		//校验数据
		$where = array('Id' => $task_id, 'Status !=' => -1);
		$task = $this->task_dal->p_exist($where);
		if (!$task)
		{//任务不存在或已删除
			return FALSE;
		}
		$where = array('Id' => $user_id, 'Status !=' => -1);
		$user = $this->user_dal->p_exist($where);
		if (!$user)
		{//用户不存在或已删除
			return TRUE;
		}

		//是否已存在关联
		$fields = 'Status';
		$where = array('TaskId' => $task_id, 'UserId' => $user_id, 'Type' => 3);
		$task_user = $this->rtu_dal
			->p_select($fields)
			->p_where($where)
			->p_fetch(TRUE);
		if ($task_user)
		{
			if (intval($task_user['Status']) !== 1)
			{
				//删除关联
				$where = array('TaskId' => $task_id, 'UserId' => $user_id, 'Type' => 3);
				$result = $this->rtu_dal->p_delete($where);
				return $result;
			}
		}

		return TRUE;
	}
}