<?php
/**
 * 日历相关页面
 * Created by PhpStorm.
 * User: CaylaXu <caylaxu@motouch.cn>
 * Date: 2016/1/18
 * Time：14:11
 */
class Calendar extends MyController
{
    function __construct()
    {
        parent::__construct();
        $this->load->model('bll/project_bll');
        $this->load->model('bll/task_bll');
    }

    /**
     * @function 日历首页
     * @User: CaylaXu
     */
    public function index()
    {
        //获取所有与用户相关的项目
        $projects = $this->project_bll->get_project_list_by_user_id($this->user_id);
        $this->hpf_smarty->assign('ProjectList', $projects);//项目筛选项
        $this->hpf_smarty->display('backend/calendar/index.tpl');
    }

    public function calendar_tasks_by_params()
    {
        $params = $_POST;
        $user_id = $this->user_id;

        if(!is_numeric($user_id))
        {
            common::return_json('-1','参数非法','',true);
        }

        $task_list = $this->task_bll->calendar_tasks_by_params($user_id,$params);
        common::return_json(0,'获取成功',$task_list,true);
    }
}