<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class RltThirdUser_model extends MySyncModel
{
    function __construct()
    {
        parent::__construct('RltThirdUser');
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
}
