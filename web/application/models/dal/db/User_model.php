<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User_model extends MyModel
{
    function __construct()
    {
        parent::__construct('Users');
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


    public function select_info(array $select, array $where , $flag = true)
    {
        if(count($select)>0)
        {
                $select_str = implode(',',$select);
                $this->db->select($select_str);
        }
        $this->db->where($where);
        $query = $this->db->get($this->table_name);
        if($flag)
        {
                return $query->result_array();
        }
        else
        {
                return  $query->row_array();
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

    public function get_list($page = 0, $limit = 15, $params = array())
    {
        $sql = "select u.Id,u.Name,u.Mobile,u.Title,u.Status from Users as u
                        where u.Status = 1 ";

        if (isset($params['GroupId']) && $params['GroupId'] != -1)
        {
            $sql = "select u.Id,u.Name,u.Mobile,u.Title,u.Status from Users as u
                    right join RltGroupUser as rgu on rgu.UserId = u.Id and rgu.Status = 1
                    where u.Status = 1 ";
            $sql .= " and rgu.GroupId = '{$params['GroupId']}'";
            $sql .= " group by u.Id ";
        }

        $offset = $page * $limit;
        $count = $this->db->query($sql)->num_rows();
        $sql .= " limit " . $offset . "," . $limit;
        $query = $this->db->query($sql);
        $query = $query->result_array();
        $return['Users'] = $query;
        $return['CurrentPage'] = $page + 1;
        $return['Total'] = ceil($count / $limit);
        $return['Records'] = $count;
        return $return;
    }

    public function get_user_by_mobile_or_email($account)
    {
        $sql = "SELECT * FROM Users WHERE (Email=? OR Mobile=?) AND Status = 1";
        $sql_arr[] = $account;
        $sql_arr[] = $account;
        $query = $this->db->query($sql, $sql_arr);
        return $query->row_array();
    }
}
