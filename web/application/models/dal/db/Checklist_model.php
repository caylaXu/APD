<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Checklist_model extends MyModel
{
    function __construct()
    {
        parent::__construct('Checklist');
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
        $result = $this->db->update($this->table_name, $data);
        return $result;

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

    /**
     * @function 获取需要同步的checklist
     * @author Peter
     * @param $task_ids
     * @param $anchor
     * @return mixed
     */
    public function get_sync_checklists($task_ids, $anchor)
    {
        $sql = "SELECT * from Checklist where Modified > ? AND TaskId IN ?";
        $bind = array($anchor, $task_ids);
        $query = $this->db->query($sql, $bind);
        return $query->result_array();
    }
}
