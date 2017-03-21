<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Group_model extends MyModel
{
    function __construct()
    {
        parent::__construct('Groups');
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

    public function get_group_by_user_id($user_id)
    {
        $sql = "select g.Id as GroupId,g.Title as GroupTitle from Groups as g
                left join RltGroupUser as rgu on g.Id = rgu.GroupId and rgu.Status = 1
                where rgu.UserId = ? ";
        $query = $this->db->query($sql, array($user_id));
        return $query->result_array();
    }

    public function group_info($group_id)
    {
        $sql = "select g.*,u.Name as HeadName from Groups as g
                left join Users as u on u.Id = g.HeadId
                where g.Id = ? ";
        $query = $this->db->query($sql, array($group_id));
        return $query->row_array();
    }
}
