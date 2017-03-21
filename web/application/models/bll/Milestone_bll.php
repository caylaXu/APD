<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Created by PhpStorm.
 * User: CaylaXu <caylaxu@motouch.cn>
 * Date: 2015/11/5
 * Time：19:51
 */
class Milestone_bll extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('dal/db/task_model');
        $this->load->model('bll/task_bll');
        $this->load->model('bll/common_bll');
    }

    public function get_list($project_id)
    {
        $milestone_list = $this->task_model->milestone_list($project_id);
        $tasks = $this->task_model->tasks_by_project_id($project_id,true);
        if(empty($milestone_list))
        {
            return $milestone_list;
        }

        $data = array();
        //1、求各个里程碑的进度
        foreach($milestone_list as $k=>&$v)
        {
            $v['DueDateString'] = common::time_cycle($v['DueDate']);
            $v['StartDateString'] = common::time_cycle($v['StartDate']);
            $progress = $this->task_bll->get_task_progress($tasks,$v['Id']);
            if($progress)
            {
                $v['CompleteProgress'] = $progress;
            }
            $data[$v['Day']]['Milestones'][] = $v;
        }

        $project_progress = 0;
        $all_count = count($data);
        if($all_count > 0)
        {
            foreach($data as $k=>&$v)
            {
                $count = count($v['Milestones']);
                $sum = 0;
                if($count > 0)
                {
                    foreach($v['Milestones'] as $key=>$val)
                    {
                        $sum += $val['CompleteProgress'];
                    }
                }
                $v['CompleteProgress'] = round($sum/$count,2);
                $project_progress += $v['CompleteProgress'];
            }
            $project_progress = round($project_progress/$all_count);
        }

        return array('Rows'=>$data,'Progress'=>$project_progress);
    }
}
