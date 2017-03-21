<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class RltTaskUser_model extends MySyncModel
{
    function __construct()
    {
        parent::__construct('RltTaskUser');
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

    function insert_batch(array $params)
    {
        if (empty($params) || !is_array($params))
        {
            return false;
        }
        $res = $this->db->insert_batch($this->table_name, $params);
        return $res;
    }

    function query_by_task_id($task_id, $type = '')
    {
        $sql = "select u.Id as UserId,u.Status,u.Name as UserName,u.Avatar from RltTaskUser as rtu
                join Users as u on u.Id = rtu.UserId
                where rtu.TaskId='{$task_id}' and rtu.Status > 0";
        $sql_arr = array();
        switch ($type)
        {
            case "principal":
                $type = 1;
                break;
            case "attention":
                $type = 3;
                break;
            default:
                break;
        }

        if (!empty($type))
        {
            $sql .= " and rtu.Type=?";
            $sql_arr[] = $type;
        }

        $sql .= " group by u.Id";

        $query = $this->db->query($sql, $sql_arr);
        return $query->result_array();
    }

    public function delete_rows_by_user_id($task_id, $user_ids, $type, $modified)
    {
        $this->db->where('TaskId', $task_id);
        $this->db->where('Type', $type);
        $this->db->where_in('UserId', $user_ids);
        $data = array(
            'Status' => '-1',
            'Method' => '-1',
            'Modified' => $modified
        );
        return $this->db->update($this->table_name, $data);
    }

    function get_rlt_user_id($task_id, $type = '')
    {
        $sql = "select group_concat(UserId) as UserIds from RltTaskUser
                where  Status > 0
                and TaskId = ? ";
        $sql_arr = array($task_id);
        switch ($type)
        {
            case "principal":
                $type = 1;
                break;
            case "attention":
                $type = 3;
                break;
            default:
                break;
        }
        if (!empty($type))
        {
            $sql .= " and Type=?";
            $sql_arr[] = $type;
        }
        $sql .= " group by TaskId";
        $query = $this->db->query($sql, $sql_arr);
        return $query->row_array();
    }

    /**
     * @function 根据TaskId更新数据
     * @User: CaylaXu
     * @param $data
     * @param $ids
     * @return mixed
     */
    public function update_by_task_id($data, $ids)
    {
        if (is_array($ids))
        {
            $this->db->where_in('TaskId', $ids);
        }
        else
        {
            $this->db->where('TaskId', $ids);
        }
        $this->db->where('Status', 2);
        return $this->db->update($this->table_name, $data);
    }

    /**
     * @function 获取需要同步的RltTaskUser
     * @author Peter
     * @param $task_ids
     * @param $anchor
     * @return mixed
     */
    public function get_sync_rtus($task_ids, $anchor)
    {
        $sql = "SELECT * FROM RltTaskUser WHERE Modified > ? AND TaskId IN ?;";
        $bind = array($anchor, $task_ids);
        $query = $this->db->query($sql, $bind);

        return $query->result_array();
    }

    public function get_user_ids_by_task_ids($task_ids)
    {
        $sql = "SELECT UserId FROM RltTaskUser WHERE TaskId IN ?";
        $bind = array($task_ids);
        $query = $this->db->query($sql, $bind);

        return $query->result_array();
    }

    public function get_user_ids_string_by_task_id($task_id)
    {
        $sql = "SELECT group_concat(UserId) as UserIds FROM RltTaskUser WHERE TaskId = ? GROUP BY TaskId";
        $bind = array($task_id);
        $query = $this->db->query($sql, $bind);
        return $query->row_array();
    }
}
