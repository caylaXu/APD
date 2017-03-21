<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Created by PhpStorm.
 * User: CaylaXu <caylaxu@motouch.cn>
 * Date: 2015/11/5
 * Time：19:51
 */
class Rlt_third_user_bll extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('dal/db/RltThirdUser_model');
        $this->load->model('bll/common_bll');
    }

    public function select_info(array $select,array $where)
    {
        return $this->RltThirdUser_model->select_info($select,$where);
    }

    public function create($params)
    {
        $where = $params;
        unset($where['NickName']);
        $where['Status <'] = 1;
        $info = $this->RltThirdUser_model->select_info(array(),$where,false);
        $modified = $this->common_bll->get_max_modified();
        if(!empty($info))
        {
            //更新操作
            $params['Method'] = 1;
            $temp['Status'] = 1;
            $params['Modified'] = $modified;
            return $this->RltThirdUser_model->update_info($params,array('Id' => $info['Id']));
        }
        else
        {

            $params['Method'] = 0;
            $params['Status'] = 1;
            $params['Modified'] = $modified;
            return $this->RltThirdUser_model->create($params);
        }
    }

	public function get_sync_rlt_third_users($anchor)
	{
		$users = $this->RltThirdUser_model->select_info(array(), array('Modified >' => $anchor));

		array_walk($users, function (&$val, $key, $table_name)
		{
			$val['Table'] = $table_name;
		}, 'RltThirdUser');

		return $users;
	}

	public function fetch($fields, $where, $one = FALSE)
	{
		$this->load->model('dal/db/RltThirdUser_model', 'rlt_third_user_dal');
		$third_user_info = $this->rlt_third_user_dal
			->p_select($fields)
			->p_where($where)
			->p_fetch($one);
		return $third_user_info;
	}

	/**
	 * @function 绑定第三方登录
	 * @author Peter
	 * @param $user_id
	 * @param $type
	 * @param $third_id
	 * @param $nickname
	 * @param $sex
	 * @return mixed
	 */
	public function bind($user_id, $type, $third_id, $nickname, $sex)
	{
		$this->load->model('dal/db/User_model', 'user_dal');
		$this->load->model('dal/db/RltThirdUser_model', 'rtu_dal');

		$where = array('Id' => $user_id);
		$user = $this->user_dal->p_exist($where);
		if (!$user)
		{
			return array('Result' => -1, 'Msg' => '该用户不存在');
		}

		//是否已经被绑定
		$where = array('Type' => $type, 'ThirdId' => $third_id, 'Status' => 1);
		$rtu = $this->rtu_dal->p_exist($where);
		if ($rtu)
		{
			return array('Result' => -1, 'Msg' => '该第三方账号已被绑定');
		}

		//是否已存在关联
		$where = array('UserId' => $user_id, 'Type' => $type);
		$rtu = $this->rtu_dal->p_exist($where);

		if ($rtu)
		{
			$update = array(
				'ThirdId' => $third_id,
				'NickName' => $nickname,
				'Status' => 1,
			);
			$where = array('UserId' => $user_id, 'Type' => $type);
			$result = $this->rtu_dal->p_update($update, $where);
		}
		else
		{
			$insert = array(
				'UserId' => $user_id,
				'ThirdId' => $third_id,
				'Type' => $type,
				'NickName' => $nickname,
			);
			$result = $this->rtu_dal->p_insert($insert);
		}

		$return = $result ?
			array('Result' => 0, 'Msg' => '绑定成功') :
			array('Result' => -1, 'Msg' => '绑定失败');

		return $return;
	}

	/**
	 * @function 解绑第三方登录
	 * @author Peter
	 * @param $user_id
	 * @param $type
	 * @return bool
	 */
	public function unbind($user_id, $type)
	{
		$this->load->model('dal/db/User_model', 'user_dal');
		$this->load->model('dal/db/RltThirdUser_model', 'rtu_dal');

		$where = array('Id' => $user_id);
		$user = $this->user_dal->p_exist($where);
		if (!$user)
		{
			return FALSE;
		}

		//当前用户绑定数小于1，不允许解绑
		$result = $this->rtu_dal
			->p_select('COUNT(Id) as count')
			->p_where(array('UserId' => $user_id, 'Status >=' => 0))
			->p_fetch(TRUE);
		if (intval($result['count']) <= 1)
		{
			return FALSE;
		}

		//解绑
		$where = array('UserId' => $user_id, 'Type' => $type);
		$result = $this->rtu_dal->p_delete($where);

		return $result;
	}

	public function delete(array $where)
	{
		if($where)
		{
			return false;
		}

		$modified = $this->common_bll->get_max_modified();

		return $this->rtu_dal->delete_info($where,$modified);
	}

	/**
	 * @function 注册第三方用户
	 * @author Peter
	 * @param $type
	 * @param $third_id
	 * @param $nickname
	 * @param $avatar
	 * @param $sex
	 * @return bool
	 */
	public function third_register($type, $third_id, $nickname, $avatar, $sex)
	{
		$this->load->model('dal/db/User_model', 'user_dal');
		$this->load->model('dal/db/RltThirdUser_model' ,'rtu_dal');

		$fields = 'UserId, Status';
		$where = array('ThirdId' => $third_id, 'Type' => $type);
		$third_user = $this->rtu_dal
			->p_select($fields)
			->p_where($where)
			->p_fetch(TRUE);
		if ($third_user)
		{
			if (intval($third_user['Status']) !== 1)
			{
				$update = array('Status' => 1);
				$where = array('ThirdId' => $third_id, 'Type' => $type);
				$result = $this->rtu_dal->p_update($update, $where);
				if (!$result)
				{
					return FALSE;
				}
			}
			$user_id = $third_user['UserId'];
		}
		else
		{
			$this->db->trans_start();
			//1、新建用户
			$insert = array(
				'Name' => $nickname,
				'Avatar' => $avatar,
				'RegistrationDate' => time(),
			);
			$user_id = $this->user_dal->p_insert($insert);
			//2、建立关联
			$insert = array(
				'NickName' => $nickname,
				'UserId' => $user_id,
				'ThirdId' => $third_id,
				'Type' => $type,
			);
			$this->rtu_dal->p_insert($insert);
			$this->db->trans_complete();
			if ($this->db->trans_status() === FALSE)
			{
				return FALSE;
			}
		}

		return intval($user_id);
	}
}