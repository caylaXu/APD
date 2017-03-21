<?php
/**
 * Created by PhpStorm.
 * User: Peter
 * Date: 2016/4/13
 * Time: 15:45
 */

class Theme extends MyController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('bll/system_config_bll');
    }

    public function get_themes()
    {
        if (!$this->input->is_ajax_request() || $this->input->method() != 'get')
        {
            common::return_json(-1, '请求方法错误', '', TRUE);
        }

        $where = array(
            'Type' => 1,
            'Status' => 1,
        );
        $fields = 'Id, Value';
        $themes = $this->system_config_bll->select($where, $fields);

        $return = array();
        foreach ($themes as $theme)
        {
            $temp['Id'] = $theme['Id'];
            $temp['Theme'] = $theme['Theme'];
            $temp['Color'] = $theme['Color'];
            $temp['Thumb'] = $theme['Thumb'];
            $temp['BgImg'] = $theme['BgImg'];
            $return[] = $temp;
        }

        common::return_json(0, '获取成功' , $return, true);
    }
}