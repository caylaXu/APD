<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class RltGroupUser_model extends MyModel
{
    function __construct()
    {
        parent::__construct('RltGroupUser');
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
        $this->db->update($this->table_name, $data);
        return $this->db->affected_rows();
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

    function query_by_group_id($group_id)
    {
        $sql = "select u.Id as UserId,u.Name as UserName from RltGroupUser as rgu
                join Users as u on u.Id = rgu.UserId
                where rgu.GroupId= ? ";

        $query = $this->db->query($sql, array($group_id));
        return $query->result_array();
    }
}
