<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Project_model extends MySyncModel
{
    function __construct()
    {
        parent::__construct('Projects');
    }

    function create(array $params)
    {
        if (empty($params) || !is_array($params))
        {
            return false;
        }

        $this->db->insert($this->table_name, $params);
        return $this->db->insert_id();
    }

    public function select_info(array $select, array $where, $flag = true)
    {
        if (count($select) > 0)
        {
            $select_str = implode(',', $select);
            $this->db->select($select_str);
        }
        $this->db->where($where);
        $query = $this->db->get($this->table_name);
        if ($flag)
        {
            return $query->result_array();
        }
        else
        {
            return $query->row_array();
        }
    }

    public function update_info(array $data, array $where)
    {
        $this->db->where($where);
        return $this->db->update($this->table_name, $data);
    }

    public function delete_info(array $where, $modified)
    {
        $this->db->where($where);
        $data = array(
            'Status' => '-1',
            'Method' => '-1',
            'Modified' => $modified
        );
        return $this->db->update($this->table_name, $data);
    }

    public function projects_info($params)
    {
        $sql = "select p.* ,u.Name as ProjectManager
                from Projects as p
                left join Users as u on u.Id=p.ProjectManagerId
                where p.Status=1";
        $query = $this->db->query($sql);
        return $query->result_array();
    }

    public function get_list($page = 0, $limit = 15, $params = array())
    {
        $sql = "select p.* ,u.Name as ProjectManager from Projects as p
                left join Users as u on u.Id=p.ProjectManagerId
                left join RltProjectUser as rpu on rpu.ProjectId = p.Id and rpu.Status = 1
                WHERE p.Status=1 ";
        $sql_arr = array();
        if (isset($params['UserId']) && $params['UserId'] != -1)
        {
            $sql .= " and rpu.UserId = ? ";
            $sql_arr[] = $params['UserId'];
        }

        $sql .= " group by p.Id";

        $offset = $page * $limit;
        $count = $this->db->query($sql, $sql_arr)->num_rows();

        $sql .= " limit " . $offset . "," . $limit;
        $query = $this->db->query($sql, $sql_arr);
        $query = $query->result_array();

        $return['Projects'] = $query;
        $return['CurrentPage'] = $page + 1;
        $return['Total'] = ceil($count / $limit);
        $return['Records'] = $count;
        return $return;
    }

    public function get_project_by_user_id($user_id, $type, $filter = false)
    {
        $sql = "select p.Id,p.Title,p.StartDate,p.DueDate,p.TrueDueDate,p.CompleteProgress,p.ParentId,u.Name
                as ProjectManager,u.Avatar
                from Projects as p
                left join Users as u on u.Id=p.ProjectManagerId";
        $sql_arr = array();
        if ($type == 'attention' || $type == 'participant')
        {
            if ($type == 'attention')
            {
                $rlt_type = 3;
            }
            else
            {
                $rlt_type = 2;
            }

            $sql .= " left join RltProjectUser as rtu on rtu.ProjectId = p.Id where rtu.Status>0 and rtu.Type='{$rlt_type}'
                     and p.Status=1";
            if (!empty($user_id))
            {
                $sql .= " and rtu.UserId = ? ";
                $sql_arr[] = $user_id;
            }

            if ($filter)
            {
                $sql .= " and p.ParentId = 0 ";
            }

            $sql .= " group by p.Id";
        }
        else
        {
            $sql .= " WHERE p.Status=1 ";

            if (!empty($user_id))
            {
                $sql .= "and p.ProjectManagerId = ?";
                $sql_arr[] = $user_id;
            }

            if ($filter)
            {
                $sql .= " and p.ParentId = 0 ";
            }
        }
        $query = $this->db->query($sql, $sql_arr);
        return $query->result_array();
    }

    /**
     * @function 查询所有最小单元项目
     * @User: CaylaXu
     * @param $user_id
     * @param $type
     * @return mixed
     */
    public function get_leaf_project_by_user_id($user_id, $type)
    {
        $sql = "select p.Id,p.Title,p.StartDate,p.DueDate,p.TrueDueDate,p.CompleteProgress,p.ParentId,u.Name
                as ProjectManager,u.Avatar
                from Projects as p
                left join Users as u on u.Id=p.ProjectManagerId";
        $sql_arr = array();
        if ($type == 'attention')
        {
            $sql .= " left join RltProjectUser as rtu on rtu.ProjectId = p.Id where rtu.Status>0 and rtu.Type=3
                     and p.Status=1";
            if (!empty($user_id))
            {
                $sql .= " and rtu.UserId = ? ";
                $sql_arr[] = $user_id;
            }
        }
        else
        {
            $sql .= " WHERE p.Status=1 ";

            if (!empty($user_id))
            {
                $sql .= "and p.ProjectManagerId = ?";
                $sql_arr[] = $user_id;
            }
        }
        $sql .= " and p.Id not in (select ParentId from Projects where Status = 1) ";
        $sql .= " group by p.Id";
        $query = $this->db->query($sql, $sql_arr);
        return $query->result_array();
    }

    /**
     * @function 获取所有我负责的项目
     * @User: CaylaXu
     * @param $user_id
     * @param bool $flag
     * @return mixed
     */
    public function get_project_list_by_user_id($user_id, $flag = false)
    {
        $sql = "select p.Id,p.Title from Projects as p
                where p.Status =1 and (p.ProjectManagerId = ?)";
        if ($flag)
        {
            $sql .= " and p.Id not in (select ParentId from Projects where Status=1)";
        }
        $query = $this->db->query($sql, array($user_id));
        $result = $query->result_array();
        return $result;
    }

    public function get_all_project_by_user_id($user_id,$flag = false)
    {
        $sql = "select p.Id,p.Title from Projects as p
                LEFT JOIN RltProjectUser as rpu on p.Id = rpu.ProjectId and rpu.Status = 1 and rpu.Type in (2,3)
                where p.Status =1 and (p.ProjectManagerId = ? or rpu.UserId = ?)";

        if ($flag)
        {
            $sql .= " and p.Id not in (select ParentId from Projects where Status=1)";
        }

        $sql .= " group by p.Id order by p.Title";

        $query = $this->db->query($sql, array($user_id,$user_id));
        $result = $query->result_array();
        return $result;
    }

    public function query_project_by_id($project_id)
    {
        $this->db->select(
            'p.Id,
             p.Title,
             p.Description,
             p.ProjectManagerId,
             p.StartDate,
             p.DueDate,
             p.TrueDueDate,
             u.Name as ProjectManager,
             u.Avatar'
        );
        $this->db->join('Users as u', 'u.Id = p.ProjectManagerId', 'left');
        $this->db->where('p.Id', $project_id);
        $this->db->where('p.Status', 1);
        $query = $this->db->get($this->table_name . " as p");
        return $query->row_array();
    }

    public function authorization_check($user_id, $project_id, $flag = true)
    {
        $sql = "select * from Projects as p
                left join RltProjectUser as rpu on p.Id=rpu.ProjectId and rpu.Status=1
                where p.Id = ?
                and (p.ProjectManagerId = ?
                or p.CreatorId = ? ";

        $sql_arr = array($project_id, $user_id, $user_id);

        if ($flag)
        {
            $sql .= "or rpu.UserId = ?)";
            $sql_arr[] = $user_id;
        }
        else
        {
            $sql .= ")";
        }

        $query = $this->db->query($sql, $sql_arr);
        return $query->result_array();
    }

    public function get_all_projects_by_user($user_id)
    {
        $sql = "select p.Id,p.Title from Projects as p left join RltProjectUser as rpu on p.Id=rpu.ProjectId and rpu.Status=1
                where p.ProjectManagerId = ?
                or p.CreatorId = ?
                or rpu.UserId = ?
                group by p.Id";
        $sql_arr = array($user_id, $user_id, $user_id);
        $query = $this->db->query($sql, $sql_arr);
        return $query->result_array();
    }

    /**
     * @function 合并用户，也可用作按id批量更新项目
     * @User: CaylaXu
     * @param $data
     * @param $child_ids
     * @return mixed
     */
    public function consolidated_project($data, $child_ids)
    {
        $this->db->where_in('Id', $child_ids);
        return $this->db->update($this->table_name, $data);
    }

    public function get_child_projects_info($project_id)
    {
        $this->db->select(
            'p.Id,
             p.Title,
             p.Description,
             p.ProjectManagerId,
             p.StartDate,
             p.DueDate,
             p.TrueDueDate,
             u.Name as ProjectManager,
             u.Avatar'
        );
        $this->db->join('Users as u', 'u.Id = p.ProjectManagerId', 'left');
        $this->db->where('p.ParentId', $project_id);
        $this->db->where('p.Status', 1);
        $query = $this->db->get($this->table_name . " as p");
        return $query->result_array();
    }

    public function get_project_by_id($project_id)
    {
        $sql = "select p.Id,p.Title,p.StartDate,p.DueDate,p.TrueDueDate,p.CompleteProgress,p.ParentId,u.Name
                as ProjectManager,u.Avatar
                from Projects as p
                left join Users as u on u.Id=p.ProjectManagerId
                where p.Id = ? ";

        $sql_arr = array($project_id);
        $query = $this->db->query($sql, $sql_arr);
        return $query->row_array();
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
        $sql = "SELECT ProjectId FROM RltProjectUser as rpu
				LEFT JOIN Projects as p on p.Id = rpu.ProjectId
				WHERE (rpu.Modified > ? or p.Modified > ? ) AND rpu.UserId = ? AND ProjectId != 0
                UNION
                SELECT DISTINCT t.ProjectId FROM Tasks AS t
                LEFT JOIN RltTaskUser AS rtu ON rtu.TaskId=t.Id
                WHERE t.Modified > ? AND rtu.UserId = ? AND t.ProjectId != 0";
        $bind = array($anchor, $anchor,$user_id, $anchor, $user_id);
        $query = $this->db->query($sql, $bind);

        return $query->result_array();
    }

    /**
     * @function 获取子项目ID
     * @author Peter
     * @param array $parent_ids
     * @param int $anchor
     * @return array
     */
    public function get_sub_project_ids($parent_ids = array(), $anchor = 0)
    {
        $sql = "SELECT Id FROM Projects WHERE Modified > ? AND ParentId IN ?";
        $bind = array($anchor, $parent_ids);
        $query = $this->db->query($sql, $bind);

        return $query->result_array();
    }

	/**
	 * @function 根据ID获取项目
	 * @author Peter
	 * @param array $project_ids
	 * @param int $anchor
	 * @param string $fields
	 * @return mixed
	 */
    public function get_by_ids(array $project_ids, $anchor = 0, $fields = '*')
    {
        if(empty($project_ids))
        {
            return array();
        }

        $sql = "SELECT {$fields} FROM Projects WHERE Modified > ? AND Id IN ?";
        $bind = array($anchor, $project_ids);
        $query = $this->db->query($sql, $bind);

        return $query->result_array();
    }
}
