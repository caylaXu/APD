<?php
/**
 * Created by PhpStorm.
 * User: CaylaXu <caylaxu@motouch.cn>
 * Date: 2016/2/25
 * Time：17:02
 */
class Statistics extends MyController
{

    function __construct()
    {
        parent::__construct();
        $this->load->model('bll/project_bll');
        $this->load->model('bll/task_bll');
        $this->load->model('bll/rlt_project_user_bll');
    }

    /**
     * @function 报表首页
     * @User: CaylaXu
     */
    public function index()
    {
        $project_id = isset($_GET['ProjectId']) ?  $_GET['ProjectId'] : 0;
        $projects = $this->project_bll->get_project_list_by_user_id($this->user_id);

        if(empty($projects))
        {
            $this->hpf_smarty->assign("Message", "别急嘛，您还没有项目呢，先去创建项目吧！");
            $this->hpf_smarty->display('backend/error/message.php');
            exit;
        }

        if($project_id != 0)
        {
            //权限检查
            $authorization_check = $this->project_bll->authorization_check($this->user_id,$project_id,true);

            if(!$authorization_check)
            {
                $this->hpf_smarty->assign("Message", "您没有权限操作该项目");
                $this->hpf_smarty->display('backend/error/message.php');
                exit;
            }
        }
        else
        {
            $project_id = $projects[0]['Id'];
        }

        $project_info = $this->project_bll->project_info($project_id);

        //项目进度
        $progress = $this->project_bll->get_project_progress($project_id);

        //项目延误率
        $pass = $this->task_bll->count_task_by_project($project_id,7);
        $count = $this->task_bll->count_task_by_project($project_id);
        $count = $count > 0 ? $count : 100;
        $pass_percent = round($pass*100/$count);

        //未完成任务
        $unfinished = $this->task_bll->count_task_by_project($project_id,1);
        //已完成任务
        $completed = $this->task_bll->count_task_by_project($project_id,2);
        //总任务
        $all = $this->task_bll->count_task_by_project($project_id,3);
        //过期任务
        $past = $this->task_bll->count_task_by_project($project_id,4);

        //今天完成了几个任务
        $have_down = $this->task_bll->count_task_by_project($project_id,5);

        //新建了几个任务
        $create = $this->task_bll->count_task_by_project($project_id,6);

        $statistic_data = array(
            "Progress" => $progress,
            "Unfinished" => $unfinished,
            "Pass" => $pass_percent,
            "Completed" => $completed,
            "All" => $all,
            "Past" => $past,
            "HaveDown" => $have_down,
            "Create" => $create
        );

        $smarty_data = array(
            "ProjectInfo" => $project_info,
            "Projects" => $projects,
            "StatisticData" => $statistic_data
        );

        $this->hpf_smarty->assign('Data',$smarty_data);
        $this->hpf_smarty->display('backend/statistics/index.tpl');
    }
}
