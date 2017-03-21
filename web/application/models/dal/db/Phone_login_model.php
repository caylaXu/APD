<?php

class Phone_login_model extends MyModel
{
    public function __construct()
    {
        parent::__construct('PhoneLoginInfo');
    }

    function create(array $params)
    {
        if(empty($params) || !is_array($params))
        {
            return false;
        }

        $this->db->insert($this->table_name, $params);
        return $this->db->insert_id();
    }

    public function select_info(array $select, array $where, $flag = true)
    {
        if(count($select) > 0)
        {
            $select_str = implode(',', $select);
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
            return $query->row_array();
        }
    }

    public function update_info(array $data, array $where)
    {
        $this->db->where($where);
        $result = $this->db->update($this->table_name, $data);
        return $result;
    }


    function check_phone_login($coach_id, $phone_token)
    {
        $sql = "select * from PhoneLoginInfo where UserId = '{$coach_id}' and Status =1 and PhoneToken != '{$phone_token}' and UserType=0";
        $query = $this->db->query($sql);
        return $query->row_array();
    }

    /**
     * @function 检查除自己以外的登录信息
     * @author CaylaXu
     * @param $coach_id
     * @param $device_id
     * @return mixed
     */
    function check_phone_login_by_device_id($coach_id, $device_id, $phone_token)
    {
        $sql = "select * from PhoneLoginInfo where UserId = '{$coach_id}' and Status =1 and DeviceId != '{$device_id}' and UserType=0";

        if($phone_token != '')
        {
            $sql .= " and PhoneToken != '{$phone_token}'";
        }

        $query = $this->db->query($sql);
        return $query->row_array();
    }

    public function get_newest_login_info_by_id_array($id_array, $user_type)
    {
        if(is_array($id_array))
        {
            $status_str = 'and UserId in (';
            $status_str .= implode(',', $id_array);
            $status_str .= ')';
        }
        $sql = "select * from
					(select * from PhoneLoginInfo order by LoginTime desc) as L
					where L.Status =1 and UserType=$user_type $status_str
					group by L.UserId";
        $query = $this->db->query($sql);
        return $query->result_array();
    }

    /**
     *获取驾校工作人员登录信息
     */
    public function get_school_user_login_info($user_type, $school_id_arr)
    {
        if(is_array($school_id_arr))
        {
            $status_str = 'and DrivingSchoolId in (';
            $status_str .= implode(',', $school_id_arr);
            $status_str .= ')';
        }
        $sql = "select * from
			(select * from PhoneLoginInfo order by LoginTime desc) as L
			where L.Status =1 and UserType=$user_type $status_str
			group by L.UserId";
        $query = $this->db->query($sql);
        return $query->result_array();
    }

    /**
     * @function 获取用户登录信息
     * @author CaylaXu
     * @param $coach_id
     * @param $user_type
     * @return array
     */
    public function check_user_is_online($user_id, $type)
    {
        $this->db->select('*');
        $this->db->where('UserId', $user_id);
        $this->db->where('Type', $type);
        $this->db->where('Status', enum::PhoneUserStatusOnline);
        $query = $this->db->get($this->table_name);
        return $query->row_array();
    }
}