<?php
/**
 * Created by PhpStorm.
 * User: cayla
 * Date: 2015/11/8
 * Time: 20:04
 * 任务相关操作
 */
class Task extends MyRestController
{

    function __construct()
    {
        parent::__construct();
        $this->load->model('bll/task_bll');
        $this->load->model('bll/rlt_task_user_bll');
        $this->load->model('bll/rlt_project_user_bll');
        $this->load->model('bll/checklist_bll');
    }

    /**
     * @function 创建任务
     * @author CaylaXu
     */
    function task_post()
    {
        $check = array(
            'Title' => true,
            'ProjectId'=>false,
            'StartDate'=>false,
            'DueDate' =>false,
            'Priority'=>false,
            'CreatorId' => true,
            'AssignedTo' => false,
            'Follwers' => false,
            'Checklist' => false,
            'Remind' => false,
        );
        $params = common::check_params($check,$this->post(), false);

        if (!$params)
        {
            $this->response(array('Result'=>-1,'Msg'=>'参数非法'), 200);
            exit;
        }

        $check_data = true;//@todo 参数检查

        if(!$check_data)
        {
            $this->response(array('Result'=>-1,'Msg'=>'格式错误'), 200);
            exit;
        }

        $data = $params;
        $data['CreateTime'] = time();
        $data['Sort'] = time()*1000;
        unset($data['AssignedTo']);
        unset($data['Follwers']);
        unset($data['Checklist']);
        unset($data['Remind']);
        $task_id = $this->task_bll->create($data);

        if(!$task_id)
        {
            $this->response(array('Result'=>-1,'Msg'=>'任务新建失败'), 200);
            exit;
        }

        //未指派责任人时,责任人为自己
        if(empty($params['AssignedTo']))
        {
            $params['AssignedTo'] = array();
            $params['AssignedTo'][] = $params['CreatorId'];
        }

        //添加责任人
        if(isset($params['AssignedTo']) && !empty($params['AssignedTo']))
        {
            $remind = isset($params['Remind']) ? $params['Remind'] : -1;

            $rlt_result = $this->rlt_task_user_bll->insert_batch($task_id,$params['AssignedTo'],1,$remind);

            if(!$rlt_result)
            {
                $this->response(array('Result'=>-1,'Msg'=>'任务指派失败'), 200);
                exit;
            }

            if(!empty($params['ProjectId']))
            {
                $rlt_result = $this->rlt_project_user_bll->insert_batch($params['ProjectId'],$params['AssignedTo'],2);
            }
        }

        //添加关注人
        if(isset($params['Follwers']) && !empty($params['Follwers']))
        {
            $rlt_result = $this->rlt_task_user_bll->insert_batch($task_id,$params['Follwers'],3);

            if(!$rlt_result)
            {
                $this->response(array('Result'=>-1,'Msg'=>'添加关注人失败'), 200);
                exit;
            }
        }

        //添加检查项
        if(isset($params['Checklist']) && !empty($params['Checklist']))
        {
            $checklist = $this->checklist_bll->insert_batch($task_id,$params['Checklist']);

            if(!$checklist)
            {
                $this->response(array('Result'=>-1,'Msg'=>'检查项添加失败'), 200);
                exit;
            }
        }

        $this->response(array('Result'=>0,'Msg'=>'新建任务成功','Data'=>array('TaskId'=>$task_id)), 200);
    }

    public function task_delete()
    {
        $task_id = $this->get('Id');

        if(!is_numeric($task_id))
        {
            $this->response(array('Result'=>-1,'Msg'=>'参数非法'), 200);
            exit;
        }

        $result = $this->task_bll->delete($task_id);

        if(!$result)
        {
            $this->response(array('Result'=>-1,'Msg'=>'删除失败'), 200);
            exit;
        }

        $this->response(array('Result'=>0,'Msg'=>'删除成功'), 200);
        exit;
    }

    public function task_put()
    {
        $params = $this->put();
        $task_id = $this->get('Id');

        if(!is_numeric($task_id))
        {
            $this->response(array('Result'=>-1,'Msg'=>'参数非法'), 200);
            exit;
        }

        $check_data = $this->task_bll->check_data($params);

        if($check_data === false)
        {
            $this->response(array('Result'=>-1,'Msg'=>'格式错误'), 200);
            exit;
        }

        $data = $check_data;

        if(isset($params['ProjectId']))
        {
            $task_current = $this->task_bll->select_info(array('ProjectId'), array('Id' => $task_id), false);
            if (!$task_current)
            {
                $this->response(array('Result'=>-1,'Msg'=>'该任务不存在'), 200);
                exit;
            }
            $init_project_id = $task_current['ProjectId'];
            if ($params['ProjectId'] != $init_project_id)
            {//修改所属项目，查出责任人
                $select = array('UserId');
                $where = array('TaskId' => $task_id, 'Type' => 1);
                $rlt_task_users = $this->rlt_task_user_bll->select_info($select, $where);

                if ($rlt_task_users)
                {//该任务有责任人，将责任人添加为目标项目的项目成员
                    $directors = array_column($rlt_task_users, 'UserId');
                    $result = $this->rlt_project_user_bll->insert_batch($params['ProjectId'], $directors, 2);
                    if (!$result)
                    {
                        $this->response(array('Result'=>-1,'Msg'=>'添加项目成员失败'), 200);
                        exit;
                    }
                }
            }
        }

        if(!empty($data))
        {
            $update = $this->task_bll->update($data,array('Id'=>$task_id));

            if(!$update)
            {
                $this->response(array('Result'=>-1,'Msg'=>'任务更新失败'), 200);
                exit;
            }
        }

        //修改责任人
        if(isset($params['AssignedTo']))
        {
            if(isset($params['Remind']) && is_string($params['Remind']))
            {
                $rlt_result = $this->rlt_task_user_bll->update_rlt_user($task_id,$params['AssignedTo'],1,$params['Remind']);
            }
            else
            {
                $rlt_result = $this->rlt_task_user_bll->update_rlt_user($task_id,$params['AssignedTo']);
            }

            if(!$rlt_result)
            {
                $this->response(array('Result'=>-1,'Msg'=>'任务指派失败'), 200);
                exit;
            }

            if(!empty($params['ProjectId']))
            {
                $rlt_result = $this->rlt_project_user_bll->insert_batch($params['ProjectId'],$params['AssignedTo'],2);
            }
        }
        else
        {
            if(isset($params['Remind']) && is_string($params['Remind']))
            {
                $data = array('Remind'=>$params['Remind']);
                $where = array('TaskId'=>$task_id,'Type'=>1,'Status >'=>0);
                $result = $this->rlt_task_user_bll->update_where($data,$where);
                if(!$result)
                {
                    $this->response(array('Result'=>-1,'Msg'=>'提醒设置失败'), 200);
                    exit;
                }
            }
        }

        //修改关注人
        if(isset($params['Follwers']))
        {
            $rlt_result = $this->rlt_task_user_bll->update_rlt_user($task_id,$params['Follwers'],3);

            if(!$rlt_result)
            {
                $this->response(array('Result'=>-1,'Msg'=>'添加关注人失败'), 200);
                exit;
            }
        }

        if(isset($params['Checklist']))
        {
            $result = $this->checklist_bll->update_checklist_batch($params['Checklist']);

            if(!$result)
            {
                $this->response(array('Result'=>-1,'Msg'=>'检查项修改失败'), 200);
                exit;
            }
        }

        $this->response(array('Result'=>0,'Msg'=>'任务更新成功'), 200);
    }

    /**
     * @function 分页请求任务列表
     * @author CaylaXu
     */
    function tasks_get()
    {
        $page          = $this->get('Page');
        $limit         = $this->get('Rows');
        $user_id = $this->get('UserId');

        if(!is_numeric($user_id))
        {
            $this->response(array('Result'=>-1,'Msg'=>'参数非法'), 200);
            exit;
        }
        $params = $this->get();
        $result = $this->task_bll->paging_get_task_by_user_id($page - 1, $limit,$params);

        if(is_array($result))
        {
            $this->response(array('Result'=>0,'Msg'=>'请求成功','Data'=>$result), 200);
            exit;
        }

        $this->response(array('Result'=>-1,'Msg'=>'请求失败'), 200);
    }

    public function task_get()
    {
        $task_id = $this->get('Id');

        if(!is_numeric($task_id))
        {
            $this->response(array('Result'=>-1,'Msg'=>'参数非法'), 200);
            exit;
        }

        $result = $this->task_bll->task_info($task_id);

        $this->response(array('Result'=>0,'Msg'=>'获取成功','Data'=>$result), 200);
        exit;
    }


    /**
     * @function 获取项目下的任务列表
     * @User: CaylaXu
     */
    public function tasks_by_project_get()
    {
        $project_id = $this->get('ProjectId');
        $page          = $this->get('Page');
        $limit         = $this->get('Rows');
        $params = $this->get();

        if(!is_numeric($project_id))
        {
            $this->response(array('Result'=>-1,'Msg'=>'参数非法'), 200);
            exit;
        }

        $result = $this->task_bll->paging_tasks_by_project($project_id,$page - 1, $limit,$params);
        $this->response(array('Result'=>0,'Msg'=>'获取成功','Data'=>$result), 200);
        exit;
    }
}
