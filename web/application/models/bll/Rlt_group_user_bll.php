<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Created by PhpStorm.
 * User: CaylaXu <caylaxu@motouch.cn>
 * Date: 2015/11/5
 * Time：19:51
 */
class Rlt_group_user_bll extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('dal/db/RltGroupUser_model');
        $this->load->model('bll/common_bll');
    }

    public function insert_batch($group_id,$params){

        if(is_string($params))
        {
            $params = explode(',',$params);
        }

        if(empty($params))
        {
            return false;
        }

        $data = array();

        $modified = $this->common_bll->get_max_modified();

        foreach($params as $k=>$v)
        {
            $temp['GroupId'] = $group_id;
            $temp['UserId'] = $v;
            $temp['Method'] = 0;
            $temp['Modified'] = $modified;
            $data[] = $temp;
        }
        return $this->RltGroupUser_model->insert_batch($data);
    }

    public function update($group_id,$params)
    {
        $modified = $this->common_bll->get_max_modified();
        $delete = $this->RltGroupUser_model->delete_info(array('GroupId'=>$group_id),$modified);

        if(!$delete)
        {
            return false;
        }

        if(is_string($params))
        {
            $params = explode(',',$params);
        }

        if(empty($params))
        {
            return false;
        }

        $data = array();

        $modified = $this->common_bll->get_max_modified();

        foreach($params as $k=>$v)
        {
            $temp['GroupId'] = $group_id;
            $temp['UserId'] = $v;
            $temp['Method'] = 0;
            $temp['Modified'] = $modified;
            $data[] = $temp;
        }

        return $this->RltGroupUser_model->insert_batch($data);
    }

    public function delete_info(array $where)
    {
        if(empty($where))
        {
            return false;
        }

        $modified = $this->common_bll->get_max_modified();

        return $this->RltGroupUser_model->delete_info($where,$modified);
    }

}