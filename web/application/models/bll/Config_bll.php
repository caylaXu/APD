<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Created by PhpStorm.
 * User: Peter <peterpan@motouch.cn>
 * Date: 2016/03/24
 * Time：15:27
 */
class Config_bll extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('dal/db/config_model');
    }

    /**
     * @function 获取需要同步的配置
     * @author Peter
     * @param $user_id
     * @param int $anchor
     * @return array
     */
    public function get_sync_configs($user_id, $anchor = 0)
    {
        if ($user_id <= 0)
        {
            return array();
        }

        $configs = $this->config_model->get_sync_configs($user_id, $anchor);

        array_walk($configs, function (&$val, $key, $table_name)
        {
            $val['Table'] = $table_name;
        }, 'Config');

        return $configs;
    }

    public function select($where = array(), $field = '*')
    {
        if (isset($where['Id']) && $where['Id'] <= 0)
        {
            return array();
        }

        return $this->config_model->my_find($where, $field);
    }

    public function create($data)
    {
        if (isset($data['Value']))
        {
            $data['Value'] = json_encode($data['Value']);
        }
        $data['Method'] = 0;
        $data['Modified'] = $this->common_bll->get_max_modified();

        return $this->config_model->my_exec_insert($data);
    }

    public function update($data, $id)
    {
        if (isset($data['Value']))
        {
            $data['Value'] = json_encode($data['Value']);
        }
        $data['Method'] = 1;
        $data['Modified'] = $this->common_bll->get_max_modified();

        return $this->config_model->my_exec_update($data, $id);
    }
}