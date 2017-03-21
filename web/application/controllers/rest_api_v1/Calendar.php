<?php
/**
 * 日历相关页面
 * Created by PhpStorm.
 * User: CaylaXu <caylaxu@motouch.cn>
 * Date: 2016/1/18
 * Time：14:11
 */
class Calendar extends MyRestController
{
    function __construct()
    {
        parent::__construct();
        $this->load->model('bll/project_bll');
        $this->load->model('bll/task_bll');
    }

    /**
     * @function 获取日历任务
     * @User: CaylaXu
     */
    public function tasks_get()
    {
        $user_id = $this->get('UserId');
        if(!is_numeric($user_id))
        {
            $this->response(array('Result'=>-1,'Msg'=>'参数非法'), 200);
            exit;
        }

        $params = $this->get();
        $task_list = $this->task_bll->calendar_tasks_by_params($user_id,$params);
        $this->response(array('Result'=>0,'Msg'=>'获取成功','Data'=>$task_list), 200);
        exit;
    }
}