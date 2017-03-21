<?php
/**
 * Created by PhpStorm.
 * User: cayla
 * Date: 2015/11/8
 * Time: 20:04
 */
class Workbench extends MyController
{

    function __construct()
    {
        parent::__construct();
        $this->load->model('bll/project_bll');
        $this->load->model('bll/Task_bll');
    }

    /**
     * @function 工作台显示我的待办、我的关注、收集箱
     * @User: CaylaXu
     */
    public function index()
    {
        if($_POST)
        {
            //我的待办、我的关注、我创建的、我完成的

            $type = $this->input->post('Type');

            if(!in_array($type,array('','Concerned','Created','Finished','TodayFinished','ConcernedFinished','All')))
            {
                common::return_json('-1','参数非法','',true);
            }
            
            $params =  $this->input->post();
            $tasks = $this->Task_bll->get_tasks_by_type($this->user_id,$type,$params);

            common::return_json('0','获取成功',$tasks);
            exit;
        }
        $this->hpf_smarty->display('backend/workbench/index.tpl');
    }


    public function collection()
    {
        $projects = $this->project_bll->get_project_list_by_user_id($this->user_id);
        $this->hpf_smarty->assign("Projects",$projects);
        $this->hpf_smarty->display('backend/workbench/collection.tpl');
    }

    public function test()
    {
        $this->load->model('bll/notice_bll');
        $result = $this->notice_bll->notice(2,1,6,array('RltId'=>2));
        if($result)
        {
            print_r("测试通过");
        }
    }
}
