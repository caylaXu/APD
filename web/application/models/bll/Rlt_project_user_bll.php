<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Created by PhpStorm.
 * User: CaylaXu <caylaxu@motouch.cn>
 * Date: 2015/11/5
 * Time：19:51
 */
class Rlt_project_user_bll extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('dal/db/RltProjectUser_model');
        $this->load->model('bll/common_bll');
		$this->load->model('bll/notice_bll');
    }

    public function insert_batch($project_id,$params,$type = 1)
    {
        if(!is_array($params))
        {
            $params = explode(',',$params);
        }

        if(empty($params))
        {
            return false;
        }

        $notice_type = 2;
        switch($type){
            case 1:
                $notice_type = 2;
                break;   // 跳出循环
            case 2:
                $notice_type = 3;
                break;
            case 3:
                $notice_type = 4;
                break;
        }

        $notice_ids = array();
        $data = array();
        $modified = $this->common_bll->get_max_modified();
        $this->db->trans_start();
        foreach($params as $k=>$v)
        {
            $exit = $this->RltProjectUser_model->select_info(array('Id','Status'),array('ProjectId'=>$project_id,'UserId'=>$v,'Type'=>$type));

            if(count($exit) > 0)
            {
                if($exit[0]['Status'] != 1)
                {
                    $notice_ids[] = $v;
                    //更新操作
                    $update = array(
                        'Type' => $type,
                        'Method' => 1,
                        'Modified'=> $modified,
                        'Status'=>1
                    );

                    $this->RltProjectUser_model->update_info($update,array('Id'=>$exit[0]['Id']));
                }
            }
            else
            {
                $notice_ids[] = $v;
                $temp['ProjectId'] = $project_id;
                $temp['UserId'] = $v;
                $temp['Type'] = $type;
                $temp['Method'] = 0;
                $temp['Modified'] = $modified;
                $data[] = $temp;
            }
        }

        if(!empty($data))
        {
            $this->RltProjectUser_model->insert_batch($data);
        }

        //通知相关的人
        $CI=&get_instance();

        if(isset($CI->user_id))
        {
            $this->notice_bll->notice($CI->user_id,$notice_ids,$notice_type,array('RltId'=>$project_id));
        }

        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE)
        {
            return false;
        }
        return true;
    }

    public function update($project_id,$params)
    {
        $modified = $this->common_bll->get_max_modified();
        $delete = $this->RltProjectUser_model->delete_info(array('ProjectId'=>$project_id),$modified);
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
            $temp['ProjectId'] = $project_id;
            $temp['UserId'] = $v;
            $temp['Method'] = 0;
            $temp['Modified'] = $modified;
            $data[] = $temp;
        }
        return $this->RltProjectUser_model->insert_batch($data);
    }

    public function query_by_project_id($project_id,$type = -1)
    {
        $res = $this->RltProjectUser_model->query_by_project_id($project_id,$type);

        if(empty($res))
        {
            return $res;
        }

        foreach($res as $k => &$v)
        {
            $v['Avatar'] = common::resources_full_path('avatar',$v['Avatar'],'picture');
        }

        return $res;
    }

    public function create($project_id,$user_id,$type = 1)
    {
        $exit = $this->RltProjectUser_model->select_info(array('Id','Status'),array('ProjectId'=>$project_id,'UserId'=>$user_id,'Type'=>$type));
        if(count($exit) > 0)
        {
            if($exit[0]['Status'] != 1)
            {
                //更新操作
                $update = array(
                    'Type' => $type,
                    'Method' => 1,
                    'Modified'=> $this->common_bll->get_max_modified(),
                    'Status'=>1
                );

                if(!$this->RltProjectUser_model->update_info($update,array('Id'=>$exit[0]['Id'])))
                {
                    return false;
                }
            }
            $result =  $exit[0]['Id'];
        }
        else
        {
            $data = array(
                'ProjectId'=>$project_id,
                'UserId' => $user_id,
                'Type' => $type,
                'Status' => 1,
            );
            $data['Method'] = 0;
            $data['Modified'] = $this->common_bll->get_max_modified();
            $result = $this->RltProjectUser_model->create($data);
        }

        //通知相关的人
        $notice_type = 2;
        switch($type){
            case 1:
                $notice_type = 2;
                break;   // 跳出循环
            case 2:
                $notice_type = 3;
                break;
            case 3:
                $notice_type = 4;
                break;
        }
        $CI=&get_instance();
        if(isset($CI->user_id))
        {
            $this->notice_bll->notice($CI->user_id,$user_id,$notice_type,array('RltId'=>$project_id));
        }

        return $result;
    }

    public function delete($id)
    {
        $modified = $this->common_bll->get_max_modified();
        return $this->RltProjectUser_model->delete_info(array('Id'=>$id),$modified);
    }

    public function select_info(array $select,array $where)
    {
        return $this->RltProjectUser_model->select_info($select,$where);
    }

    public function update_where(array $data,array $where)
    {
        $data['Method'] = 1;
        $data['Modified'] = $this->common_bll->get_max_modified();
        return $this->RltProjectUser_model->update_info($data,$where);
    }

    public function update_rlt_user($project_id,$user_ids,$type = 1)
    {
        $old_ids_array = $this->RltProjectUser_model->select_info(array('UserId'),array('ProjectId'=>$project_id,'Status'=>1,'Type'=>$type));
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
            $res = $this->RltProjectUser_model->delete_rows_by_user_id($project_id,$delete,$type,$modified);
            if(!$res)
            {
                return false;
            }
        }

        if(!empty($add))
        {
            //@todo 增加新添加的人
            $res = $this->insert_batch($project_id,$add,$type);

//            $notice_type = 2;

//            //通知相关的人
//            switch($type){
//                case 1:
//                    $notice_type = 2;
//                    break;   // 跳出循环
//                case 2:
//                    $notice_type = 3;
//                    break;
//                case 3:
//                    $notice_type = 4;
//                    break;
//            }

//            $CI=&get_instance();
//            if(isset($CI->user_id))
//            {
//                $this->notice_bll->notice($CI->user_id,$add,$notice_type,array('RltId'=>$project_id));
//            }

            if(!$res)
            {
                return false;
            }
        }
        return true;
    }

    /**
     * @function 获取需要同步的RltProjectUser
     * @author Peter
     * @param $project_ids
     * @param $anchor
     * @return mixed
     */
    public function get_sync_rpus($project_ids, $anchor = 0)
    {
        if (!is_array($project_ids) || !$project_ids)
        {
            return array();
        }

        $tasks = $this->RltProjectUser_model->get_sync_rpus($project_ids, $anchor);

        array_walk($tasks, function (&$val, $key, $table_name)
        {
            $val['Table'] = $table_name;
        }, 'RltProjectUser');

        return $tasks;
    }

	/**
	 * @function 添加项目经理
	 * @author Peter
	 * @param $project_id
	 * @param $user_id
	 * @return bool
	 */
	public function add_manager($project_id, $user_id)
	{
		$this->load->model('dal/db/User_model', 'user_dal');
		$this->load->model('dal/db/Project_model', 'project_dal');
		$this->load->model('dal/db/RltProjectUser_model', 'rpu_dal');

		//校验数据
		$where = array('Id' => $project_id, 'Status !=' => -1);
		$project = $this->project_dal->p_exist($where);
		if (!$project)
		{//项目不存在或已删除
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
		$where = array('ProjectId' => $project_id, 'UserId' => $user_id, 'Type' => 1);
		$project_user = $this->rpu_dal
			->p_select($fields)
			->p_where($where)
			->p_fetch(TRUE);

		//启用事务
		$this->db->trans_start();
		//1、删除该项目所有的项目经理
		$where = array('ProjectId' => $project_id, 'UserId !=' => $user_id, 'Type' => 1);
		$this->rpu_dal->p_delete($where);
		//2、添加/更新关联
		if ($project_user)
		{
			if (intval($project_user['Status']) !== 1)
			{
				$update = array('Status' => 1);
				$where = array('ProjectId' => $project_id, 'UserId' => $user_id, 'Type' => 1);
				$this->rpu_dal->p_update($update, $where);
			}
		}
		else
		{
			$insert = array('ProjectId' => $project_id, 'UserId' => $user_id, 'Type' => 1);
			$this->rpu_dal->p_insert($insert);
		}
		//3、更新项目表
		$update = array('ProjectManagerId' => $user_id);
		$where = array('Id' => $project_id);
		$this->project_dal->p_update($update, $where);
		$this->db->trans_complete();

		return $this->db->trans_status();
	}

	/**
	 * @function 移除项目经理
	 * @author Peter
	 * @param $project_id
	 * @return bool
	 */
	public function remove_manager($project_id)
	{
		$this->load->model('dal/db/Project_model', 'project_dal');
		$this->load->model('dal/db/RltProjectUser_model', 'rpu_dal');

		//校验数据
		$where = array('Id' => $project_id, 'Status !=' => -1);
		$project = $this->project_dal->p_exist($where);
		if (!$project)
		{//项目不存在或已删除
			return FALSE;
		}
		$where = array('ProjectId' => $project_id, 'Status !=' => -1, 'Type' => 1);
		$project_user = $this->rpu_dal->p_exist($where);
		if ($project_user)
		{//关联不存在或已删除
			return TRUE;
		}

		//启用事务
		$this->db->trans_start();
		//1、删除关联
		$where = array('ProjectId' => $project_id, 'Type' => 1);
		$this->rpu_dal->p_delete($where);
		//2、更新项目表
		$update = array('ProjectManagerId' => 0);
		$where = array('Id' => $project_id);
		$this->project_dal->p_update($update, $where);

		return $this->db->trans_status();
	}

	public function add_member($project_id, $user_id)
	{
		$this->load->model('dal/db/Project_model', 'project_dal');
		$this->load->model('dal/db/User_model', 'user_dal');
		$this->load->model('dal/db/RltProjectUser_model', 'rpu_dal');

		$where = array('Id' => $project_id, 'Status !=' => -1);
		$project = $this->project_dal->p_exist($where);
		if (!$project)
		{//项目不存在或已删除
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
		$where = array('ProjectId' => $project_id, 'UserId' => $user_id, 'Type' => 2);
		$project_user = $this->rpu_dal
			->p_select($fields)
			->p_where($where)
			->p_fetch(TRUE);
		if ($project_user)
		{
			if (intval($project_user['Status']) !== 1)
			{
				$update = array('Status' => 1);
				$where = array('ProjectId' => $project_id, 'UserId' => $user_id, 'Type' => 2);
				$result = $this->rpu_dal->p_update($update, $where);
			}
			else
			{
				$result = TRUE;
			}
		}
		else
		{
			$insert = array('ProjectId' => $project_id, 'UserId' => $user_id, 'Type' => 2);
			$result = $this->rpu_dal->p_insert($insert);
		}

		return $result;
	}

	public function remove_member($project_id, $user_id)
	{
		//校验数据
		$where = array('Id' => $project_id, 'Status !=' => -1);
		$project = $this->project_dal->p_exist($where);
		if (!$project)
		{//项目不存在或已删除
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
		$where = array('ProjectId' => $project_id, 'UserId' => $user_id, 'Type' => 2);
		$project_user = $this->rpu_dal
			->p_select($fields)
			->p_where($where)
			->p_fetch(TRUE);
		if ($project_user)
		{
			if (intval($project_user['Status']) !== -1)
			{
				//删除关联
				$where = array('ProjectId' => $project_id, 'UserId' => $user_id, 'Type' => 2);
				$result = $this->rpu_dal->p_delete($where);
				return $result;
			}
		}

		return TRUE;
	}

	public function add_follower($project_id, $user_id)
	{
		$this->load->model('dal/db/Project_model', 'project_dal');
		$this->load->model('dal/db/User_model', 'user_dal');
		$this->load->model('dal/db/RltProjectUser_model', 'rpu_dal');

		//校验数据
		$where = array('Id' => $project_id, 'Status !=' => -1);
		$project = $this->project_dal->p_exist($where);
		if (!$project)
		{//项目不存在或已删除
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
		$where = array('ProjectId' => $project_id, 'UserId' => $user_id, 'Type' => 3);
		$project_user = $this->rpu_dal
			->p_select($fields)
			->p_where($where)
			->p_fetch(TRUE);
		//更新关联
		if ($project_user)
		{
			if (intval($project_user['Status']) !== 1)
			{
				$update = array('Status' => 1);
				$where = array('ProjectId' => $project_id, 'UserId' => $user_id, 'Type' => 3);
				$result = $this->rpu_dal->p_update($update, $where);
			}
			else
			{
				$result = TRUE;
			}
		}
		else
		{
			$insert = array('ProjectId' => $project_id, 'UserId' => $user_id, 'Type' => 3);
			$result = $this->rpu_dal->p_insert($insert);
		}

		return $result;
	}

	public function remove_follower($project_id, $user_id)
	{
		//校验数据
		$where = array('Id' => $project_id, 'Status !=' => -1);
		$project = $this->project_dal->p_exist($where);
		if (!$project)
		{//项目不存在或已删除
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
		$where = array('ProjectId' => $project_id, 'UserId' => $user_id, 'Type' => 3);
		$project_user = $this->rpu_dal
			->p_select($fields)
			->p_where($where)
			->p_fetch(TRUE);
		if ($project_user)
		{
			if (intval($project_user['Status']) !== -1)
			{
				//删除关联
				$where = array('ProjectId' => $project_id, 'UserId' => $user_id, 'Type' => 3);
				$result = $this->rpu_dal->p_delete($where);
				return $result;
			}
		}

		return TRUE;
	}

	public function add_rlt_user($user_id, $project_id, $type)
	{
		$this->load->model('bll/common_bll');
		$this->load->model('dal/db/Project_model', 'project_dal');
		$this->load->model('dal/db/RltProjectUser_model', 'rpu_dal');

		if ($type == 1)
		{//项目经理（只有一个）

			//1、删除该项目的所有项目经理
			$set = array(
				'Status' => -1,
				'Method' => -1,
				'Modified' => $this->common_bll->get_max_modified(),
			);
			$where = array(
				'ProjectId' => $project_id,
				'UserId !=' => $user_id,
			);
			$this->rpu_dal->p_update($set, $where);

			//2、更新项目表的项目经理字段
			$set = array(
				'ProjectManagerId' => $user_id,
			);
			$where = array(
				'Id' => $project_id,
			);
			$this->project_dal->p_update($set, $where);
		}

		//是否已经存在关联
		$row = $this->rpu_dal
			->p_where(array('ProjectId' => $project_id, 'UserId' => $user_id, 'Type' => $type))
			->p_fetch(TRUE);
		if ($row)
		{//存在
			if ($row['Status'] == -1)
			{//已删除，则激活
				$set = array(
					'Status' => 1,
					'Method' => 1,
					'Modified' => $this->common_bll->get_max_modified(),
				);
				$where = array(
					'ProjectId' => $project_id,
					'UserId' => $user_id,
				);
				$result = $this->rpu_dal->p_update($set, $where);
				if ($result)
				{
					$output = array(
						'code' => 0,
						'msg' => '添加成功',
						'data' => array('RltId' => $row['Id']),
					);
				}
				else
				{
					$output = array(
						'code' => -1,
						'msg' => '添加失败',
						'data' => array('RltId' => $row['Id']),
					);
				}
			}
			else if($row['Status'] == 1)
			{//激活，则删除
				$set = array(
					'Status' => -1,
					'Method' => -1,
					'Modified' => $this->common_bll->get_max_modified(),
				);
				$where = array(
					'ProjectId' => $project_id,
					'UserId' => $user_id,
				);
				$result = $this->rpu_dal->p_update($set, $where);
				if ($result)
				{
					$output = array(
						'code' => 0,
						'msg' => '删除成功',
						'data' => array('RltId' => $row['Id']),
					);
				}
				else
				{
					$output = array('code' => -1, 'msg' => '删除失败');
				}
			}
		}
		else
		{//不存在则创建
			$insert = array(
				'ProjectId'=>$project_id,
				'UserId' => $user_id,
				'Type' => $type,
				'Status' => 1,
				'Method' => 0,
				'Modified' => $this->common_bll->get_max_modified(),
			);
			$result = $this->rpu_dal->p_insert($insert);
			if ($result)
			{
				$output = array(
					'code' => 0,
					'msg' => '添加成功',
					'data' => array('RltId' => $result),
				);
			}
			else
			{
				$output = array('code' => -1, 'msg' => '添加失败');
			}
		}

		return $output;
	}
}