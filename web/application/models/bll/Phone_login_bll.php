<?php

/**
 * 设备登陆逻辑处理文件
 * Class Phone_login_bll
 */
class Phone_login_bll extends CI_Model
{

	public function __construct()
	{
		parent::__construct();
		$this->load->model('dal/db/phone_login_model');
	}

	public function create(array $params)
	{

		if(empty($params) || !is_array($params))
		{
			return false;
		}

		return $this->phone_login_model->create($params);
	}

	public function select_info(array $select,array $where,$flag = true)
	{
		return $this->phone_login_model->select_info($select,$where,$flag);
	}

	public function update_where(array $data,array $where)
	{
		return $this->phone_login_model->update_info($data,$where);
	}

	/**
	 * @function 获取一组用户的最近一次登录信息
	 */
	function get_newest_login_info_by_id_array($id_array,$user_type)
	{
		return $this->phone_login_model->get_newest_login_info_by_id_array($id_array,$user_type);
	}

	function check_phone_login($coach_id,$phone_token)
	{
		return $this->phone_login_model->check_phone_login($coach_id,$phone_token);
	}

	function check_user_is_online($user_id,$type)
	{
		return $this->phone_login_model->check_user_is_online($user_id,$type);
	}

	public function insert($data)
	{
		$where = array(
			'UserId' => $data['UserId'],
			'PhoneToken' => $data['PhoneToken'],
			'Type' => $data['Type'],
		);
		$exit = $this->select_info(array('Id'),$where,false);
		if(!empty($exit))
		{
			return $this->update_where($data,array('Id'=>$exit['Id']));
		}
		else
		{
			return $this->create($data);
		}
	}
}
