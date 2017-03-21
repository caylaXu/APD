<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Created by PhpStorm.
 * User: CaylaXu <caylaxu@motouch.cn>
 * Date: 2015/11/5
 * Time：19:51
 */
class Group_bll extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('dal/db/group_model');
        $this->load->model('bll/common_bll');
    }
    public function create(array $params){
        if(empty($params) || !is_array($params))
        {
            return false;
        }
        $params['Method'] = 0;
        $params['Modified'] = $this->common_bll->get_max_modified();
        $params['UniCode'] = common::create_uuid();
        return $this->group_model->create($params);
    }

    public function select_info(array $select,array $where)
    {
        return $this->group_model->select_info($select,$where);
    }

    public function update(array $data,array $where)
    {
        $data['Method'] = 1;
        $data['Modified'] = $this->common_bll->get_max_modified();
        return $this->group_model->update_info($data,$where);
    }

    public function group_info($group_id)
    {
        $group_info = $this->group_model->group_info($group_id);

        if(!$group_info)
        {
            return array();
        }

        $rlt_user = $this->RltGroupUser_model->query_by_group_id($group_id);
        $group_info['RltUser'] = $rlt_user;
        return $group_info;
    }

    public function delete($project_id)
    {
        $modified = $this->common_bll->get_max_modified();
        return $this->group_model->delete_info(array('Id'=>$project_id),$modified);
    }
}