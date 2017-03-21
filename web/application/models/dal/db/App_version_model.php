<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class App_version_model extends MyModel
{
    public function __construct()
    {
        parent::__construct('AppVersion');
    }

    public function query($app_name, $app_type, $version_name)
    {
        $this->db->from($this->table_name);
        $this->db->where('AppName', $app_name);
        $this->db->where('AppType', $app_type);
        $this->db->where('VersionName', $version_name);
        $this->db->order_by('Id', 'desc');
        $query = $this->db->get();
        return $query->row_array();
    }

    /**
     * @function 获取最新的app
     * @param unknown $app_name
     * @param unknown $app_type
     * @return unknown
     */
    public function get_newest_app($app_name, $app_type)
    {
        $this->db->from($this->table_name);
        $this->db->where('AppName', $app_name);
        $this->db->where('AppType', $app_type);
        $this->db->order_by('Id', 'desc');
        $query = $this->db->get();
        return $query->row_array();
    }

    public function insert_new($params)
    {
        $res = $this->db->insert($this->table_name, $params);
        if ($res)
        {
            return $this->db->insert_id();
        }
        else
        {
            return false;
        }
    }

    public function query_by_id($target_id)
    {
        $this->db->from($this->table_name);
        $this->db->where('Id', $target_id);
        $query = $this->db->get();
        return $query->row_array();
    }


    /**
     * @function 根据条件获取app总记录数
     * @param array $condition
     * @return unknown
     */
    public function query_count($condition = array())
    {
        $this->db->from($this->table_name);
        if (!empty($condition['AppName']))
        {
            $this->db->where('AppName', $condition['AppName']);
        }
        if (!empty($condition['AppType']))
        {
            $this->db->where('AppType', $condition['AppType']);
        }
        $this->db->order_by('Id', 'desc');
        $query = $this->db->get();
        return $query->num_rows();
    }

    /**
     * @function 根据条件获取app记录
     * @param array $condition
     * @return unknown
     */
    public function query_rows($condition = array())
    {
        $this->db->from($this->table_name);
        if (!empty($condition['AppName']))
        {
            $this->db->where('AppName', $condition['AppName']);
        }
        if (!empty($condition['AppType']))
        {
            $this->db->where('AppType', $condition['AppType']);
        }
        $this->db->order_by('Id', 'desc');
        $query = $this->db->get();
        $rows = $query->result_array();
        if (!empty($rows))
        {
            return $rows;
        }
        else
        {
            return array();
        }
    }

    /**
     * @function 查询
     * @author Peter
     * @param string $select
     * @param array $where
     * @param int $limit
     * @param int $offset
     * @return mixed
     */
    public function fetch($select = '*', $where = array(), $limit = 10, $offset = 0)
    {
        $result = array();
        $this->db->select($select);
        $this->db->from($this->table_name);
        $this->db->where($where);
        $this->db->limit($limit, $offset);
        $this->db->order_by('Id', 'DESC');
        $query = $this->db->get();
        $result['data'] = $query->result_array();
        $result['count'] = $this->db->count_all_results($this->table_name);

        return $result;
    }

}
