<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class System_config_model extends MyModel
{
    public function __construct()
    {
        parent::__construct('SystemConfig');
    }

    public function get_themes($where = array(), $fields = '*', $limit = 0, $offset = 0)
    {
        $this->db->select($fields);
        if (is_array($where) && $where)
        {
            $this->db->where($where);
        }
        $this->db->order_by('Id', 'DESC');
        if ($limit > 0 && $offset > 0)
        {
            $this->db->limit($limit, $offset);
        }
        $query = $this->db->get($this->table_name);

        return $query->result_array();
    }

    public function get_sync_sys_configs($anchor = 0)
    {
        $sql = "SELECT * FROM SystemConfig WHERE Type = 2 AND Modified > ?";
        $bind = array($anchor);
        $query = $this->db->query($sql, $bind);

        return $query->result_array();
    }
}
