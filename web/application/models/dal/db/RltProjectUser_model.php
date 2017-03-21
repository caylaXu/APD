<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class RltProjectUser_model extends MySyncModel
{
    function __construct()
    {
        parent::__construct('RltProjectUser');
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

    public function select_info(array $select, array $where)
    {
        if (count($select) > 0)
        {
            $select_str = implode(',', $select);
            $this->db->select($select_str);
        }
        $this->db->where($where);
        $query = $this->db->get($this->table_name);
        return $query->result_array();
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

    function insert_batch(array $params)
    {
        if (empty($params) || !is_array($params))
        {
            return false;
        }
        return $this->db->insert_batch($this->table_name, $params);
    }

    function query_by_project_id($project_id, $type = -1)
    {
        $sql = "select u.Id as UserId,u.Status,u.Name as UserName, u.Email, u.Avatar from RltProjectUser as rpu
                join Users as u on u.Id = rpu.UserId and rpu.Status = 1
                where rpu.Status = 1 and u.Status!=-1";
        $sql_arr = array();

        if ($project_id != -1)
        {
            $sql .= " and rpu.ProjectId= ? ";
            $sql_arr[] = $project_id;
        }

        if (!empty($type) && $type != -1)
        {
            if (strtolower($type) == 'follwers' || $type == 3)
            {
                $sql .= " and rpu.Type = 3";
            }
            else if ($type == 2)
            {
                    $sql .= " and rpu.Type in (1,2)";
            }
        }
        $sql .= " group by u.Id";
        $query = $this->db->query($sql, $sql_arr);
        return $query->result_array();
    }

    public function delete_rows_by_user_id($project_id, $user_ids, $type, $modified)
    {
        $this->db->where('ProjectId', $project_id);
        $this->db->where('Type', $type);
        $this->db->where_in('UserId', $user_ids);
        $data = array(
            'Status' => '-1',
            'Method' => '-1',
            'Modified' => $modified
        );
        return $this->db->update($this->table_name, $data);
    }

    /**
     * @function 获取需要同步的RltProjectUser
     * @author Peter
     * @param $project_ids
     * @param $anchor
     * @return mixed
     */
    public function get_sync_rpus($project_ids, $anchor)
    {
        $sql = "SELECT * FROM RltProjectUser WHERE Modified > ? AND ProjectId IN ?;";
        $bind = array($anchor, $project_ids);
        $query = $this->db->query($sql, $bind);

        return $query->result_array();
    }

    public function get_user_ids_by_project_ids($project_ids)
    {
        $sql = "SELECT UserId FROM RltProjectUser WHERE ProjectId IN ?";
        $bind = array($project_ids);
        $query = $this->db->query($sql, $bind);

        return $query->result_array();
    }
}
