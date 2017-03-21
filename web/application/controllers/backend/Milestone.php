<?php
/**
 * Created by PhpStorm.
 * User: cayla
 * Date: 2015/11/8
 * Time: 20:04
 */
class Milestone extends MyController
{

    function __construct()
    {
        parent::__construct();
        $this->load->model('bll/milestone_bll');
        $this->load->model('bll/task_bll');
        $this->load->model('bll/project_bll');
    }

    public function index()
    {
        $project_id = isset($_GET['ProjectId']) ?  $_GET['ProjectId'] : '';
        //获取子项目
        $childs = $this->project_bll->get_child_projects_info($project_id);

        if(empty($childs))
        {
            //权限检查
            $authorization_check = $this->project_bll->authorization_check($this->user_id,$project_id,true);
            if(!$authorization_check)
            {
                echo "您没有权限操作该项目";
                exit;
            }
        }

        $project_info = $this->project_bll->select_info(array('Title','ParentId','ProjectManagerId','CreatorId'),array('Id'=>$project_id));
        $project = $this->project_bll->query_project_by_id($project_id);
        $milestones = $this->milestone_bll->get_list($project_id);
        $project_name = isset($project_info[0]['Title']) ? $project_info[0]['Title'] : "未知";
        $permission = 0;
        $parent_id = 0;
        if(isset($project_info[0]))
        {
            $parent_id = $project_info[0]['ParentId'];
            if($this->user_id == $project_info[0]['ProjectManagerId'] || $this->user_id == $project_info[0]['CreatorId'])
            {
                $permission = 1;
            }
        }

        if(!empty($parent_id))
        {
            $parent_info = $this->project_bll->select_info(array('Title','Id'),array('Id'=>$parent_id),false);
            $this->hpf_smarty->assign("ParentProject",$parent_info);
        }

        $projects = $this->project_bll->get_project_list_by_user_id($this->user_id);

        $this->hpf_smarty->assign("ProjectId",$project_id);
        $this->hpf_smarty->assign("Project",$project);
        $this->hpf_smarty->assign("Projects",$projects);
        $this->hpf_smarty->assign("Milestones",$milestones);
        $this->hpf_smarty->assign("ProjectName",$project_name);
        $this->hpf_smarty->assign("Permission",$permission);//1：允许操作0：不允许操作
        $this->hpf_smarty->assign("Childs",$childs);
        $this->hpf_smarty->display('backend/milestone/index.tpl');
    }

    /**
     * @function 分页请求目标列表
     * @author CaylaXu
     */
    function milestone_list_get()
    {
        $project_id = isset($_POST['ProjectId']) ?  $_POST['ProjectId'] : '';
        $question_list = $this->milestone_bll->get_list($project_id);
        common::return_json('0','获取成功',$question_list,true);
    }
}
