<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Created by PhpStorm.
 * User: CaylaXu <caylaxu@motouch.cn>
 * Date: 2015/11/5
 * Time：19:51
 */
class Sync_bll extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('dal/db/common_model');
        $this->load->model('bll/common_bll');
        $this->load->model('bll/checklist_bll');
        $this->load->model('bll/project_bll');
        $this->load->model('bll/rlt_project_user_bll');
        $this->load->model('bll/rlt_task_user_bll');
        $this->load->model('bll/task_bll');
        $this->load->model('bll/user_bll');
        $this->load->model('bll/config_bll');
        $this->load->model('bll/system_config_bll');
        $this->load->model('bll/rlt_third_user_bll');
    }

    public function update_new($anchor, $user_id)
    {
        $anchor = intval($anchor);
        $user_id = intval($user_id);

        if ($anchor < 0 || $user_id <= 0)
        {
            return array();
        }

        $rlt_task_ids = $this->task_bll->get_rlt_task_ids($user_id, $anchor);//与user_id相关的任务ID
        $rlt_project_ids = $this->project_bll->get_rlt_project_ids($user_id, $anchor);//与user_id相关的项目ID
        $rlt_task_ids_all = $this->task_bll->get_rlt_task_ids($user_id, 0);//与user_id相关的任务ID
        $checklists = $this->checklist_bll->get_sync_checklists(array_unique(array_merge($rlt_task_ids,$rlt_task_ids_all)), $anchor);
        $rpus = $this->rlt_project_user_bll->get_sync_rpus($rlt_project_ids, $anchor);
        $rtus = $this->rlt_task_user_bll->get_sync_rtus($rlt_task_ids, $anchor);
        $projects = $this->project_bll->get_sync_projects($rlt_project_ids, $anchor);
        $tasks = $this->task_bll->get_sync_tasks($rlt_task_ids, $anchor);
        $users = $this->user_bll->get_sync_users($anchor);
        $configs = $this->config_bll->get_sync_configs($user_id, $anchor);
        $system_configs = $this->system_config_bll->get_sync_sys_configs($anchor);
		$rlt_third_users = $this->rlt_third_user_bll->get_sync_rlt_third_users($anchor);

        if ($anchor > 0)
        {//非首次同步数据
            $select = array('Id');
            $where = array('UserId' => $user_id, 'Modified >' => $anchor);
            $rlt_task_ids = $this->rlt_task_user_bll->select_info($select, $where);
            $rlt_task_ids = array_column($rlt_task_ids, 'Id');
            $tasks = array_merge($tasks, $this->task_bll->get_sync_tasks($rlt_task_ids));

            $select = array('Id');
            $where = array('UserId' => $user_id, 'Modified >' => $anchor);
            $rlt_project_ids = $this->rlt_project_user_bll->select_info($select, $where);
            $rlt_project_ids = array_column($rlt_project_ids, 'Id');
            $projects = array_merge($projects, $this->project_bll->get_sync_projects($rlt_project_ids));

            //去重
            $tasks = array_column($tasks, NULL, 'Id');
            $projects = array_column($projects, NULL, 'Id');
            $task_ids = array();
            $project_ids = array();
            foreach($tasks as $key => $value)
            {
                if (!in_array($key, $task_ids))
                {
                    $task_ids[] = $key;
                }
                else
                {
                    unset($tasks[$key]);
                }
            }
            foreach($projects as $key => $value)
            {
                if (!in_array($key, $project_ids))
                {
                    $project_ids[] = $key;
                }
                else
                {
                    unset($projects[$key]);
                }
            }
            $tasks = array_values($tasks);
            $projects = array_values($projects);
        }

        $data = array_merge($checklists, $projects, $rpus, $rtus, $tasks, $users, $configs, $system_configs, $rlt_third_users);

        return common::sortArrayAsc($data);
    }

    /**
     * @function 提交更新数据
     * @author CaylaXu
     * @param $anchor
     * @param $data
     * @return array
     */
    public function submit($anchor,$data)
    {
        $result = array();
        $success = array();
        $this->db->trans_start();
        foreach($data as $k=>$v)
        {
            $temp = array();
            $table_name = $v['Table'];
            $method = $v['Method'];
            $client_id = $v['Id'];
            $server_id = $v['ServerId'];
            $sync_data = $v;
            unset($sync_data['ServerId']);
            unset($sync_data['Table']);
            unset($sync_data['Id']);
            $modified = $this->common_bll->get_max_modified();
            $sync_data['Modified'] = $modified;
            //如果是新建,或者本地新建后更新一律视为新建
            if($method == 0 || $server_id==0)
            {
                $sync_data['Method'] = 0;
                $id = $this->common_model->return_insert_id($table_name,$sync_data);
                $temp['Result'] = empty($id) ? -1 : 0;
                $temp['ServerId'] = $id;
                $temp['Msg'] = empty($id) ? "数据同步失败" : "数据同步成功";
            }
            else
            {
                //更新数据
                //1、判断数据是否存在
                $info = $this->common_model->get_where($table_name,array('Id'=>$server_id));


                if(empty($info))
                {
                    $temp['Result'] = -1;
                    $temp['ServerId'] = $server_id;
                    $temp['Msg'] = "要更新的数据不存在";
                }//判断同步期间是否被更改
                else if(isset($info[0]['Modified']) && $info[0]['Modified']>$anchor)
                {
                    $temp['Result'] = -1;
                    $temp['ServerId'] = $server_id;
                    $temp['Msg'] = "服务器有更新版本";
                }
                else
                {
                    $update = $this->common_model->update($table_name,$sync_data,array('Id'=>$server_id));
                    $temp['Result'] = $update ? 0 : -1;
                    $temp['ServerId'] = $server_id;
                    $temp['Msg'] = $update ? "同步成功" : "同步失败";
                }
            }
            $temp['Id'] = $client_id;
            $temp['Table'] = $table_name;
            $result[] = $temp;

            if($temp['Result'] == 0)
            {
                $success_temp = $v;
                $success_temp['ServerId'] = $temp['ServerId'];
                $success[] = $success_temp;
            }
        }
        $this->change_unicode($success);
        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE)
        {
            return false;
        }

        $anchor = isset($modified) ? $modified : $anchor;
        return array('Anchor'=>$anchor,'Data'=>$result);
    }

    /**
     * @function 转换关联表的唯一码
     * @author CaylaXu
     * @param $data
     * @return bool
     */
    public function change_unicode($data)
    {
        if(empty($data))
        {
            return true;
        }

        foreach($data as $k=>$v)
        {
            $table_name = $v['Table'];
            $server_id = $v['ServerId'];
            $unicode_array = array('HeadId','CreatorId','FinisherId','ProjectManagerId','UserId','ProjectId','RelationId','GroupId','TagId','TaskId');
            $keys = array_keys($v);
            $array = array_intersect($keys,$unicode_array);
            $update = array();

            if(empty($array))
            {
                break;
            }

            if(isset($v['HeadId']) && !empty($v['HeadId']))
            {
                $info = $this->common_model->get_where('Users',array('UniCode'=>$v['HeadId']));

                if(!empty($info))
                {
                    $update['HeadId'] = $info[0]['Id'];
                }
            }

            if(isset($v['CreatorId']) && !empty($v['CreatorId']))
            {
                $info = $this->common_model->get_where('Users',array('UniCode'=>$v['CreatorId']));
                if(!empty($info))
                {
                    $update['CreatorId'] = $info[0]['Id'];
                }
            }

            if(isset($v['FinisherId']) && !empty($v['FinisherId']))
            {
                $info = $this->common_model->get_where('Users',array('UniCode'=>$v['FinisherId']));
                if(!empty($info))
                {
                    $update['FinisherId'] = $info[0]['Id'];
                }
            }

            if(isset($v['ProjectManagerId']) && !empty($v['ProjectManagerId']))
            {
                $info = $this->common_model->get_where('Users',array('UniCode'=>$v['ProjectManagerId']));
                if(!empty($info))
                {
                    $update['ProjectManagerId'] = $info[0]['Id'];
                }
            }

            if(isset($v['UserId']) && !empty($v['UserId']))
            {
                $info = $this->common_model->get_where('Users',array('UniCode'=>$v['UserId']));
                if(!empty($info))
                {
                    $update['UserId'] = $info[0]['Id'];
                }
            }

            if(isset($v['ProjectId']) && !empty($v['ProjectId']))
            {
                $info = $this->common_model->get_where('Projects',array('UniCode'=>$v['ProjectId']));
                if(!empty($info))
                {
                    $update['ProjectId'] = $info[0]['Id'];
                }
            }

            if(isset($v['RelationId']) && !empty($v['RelationId']))
            {
                $info = $this->common_model->get_where('Projects',array('UniCode'=>$v['RelationId']));
                if(!empty($info))
                {
                    $update['RelationId'] = $info[0]['Id'];
                }
            }

            if(isset($v['GroupId']) && !empty($v['GroupId']))
            {
                $info = $this->common_model->get_where('Groups',array('UniCode'=>$v['GroupId']));
                if(!empty($info))
                {
                    $update['GroupId'] = $info[0]['Id'];
                }
            }

            if(isset($v['TagId']) && !empty($v['TagId']))
            {
                $info = $this->common_model->get_where('Tags',array('UniCode'=>$v['TagId']));
                if(!empty($info))
                {
                    $update['TagId'] = $info[0]['Id'];
                }
            }

            if(isset($v['TaskId']) && !empty($v['TaskId']))
            {
                $info = $this->common_model->get_where('Tasks',array('UniCode'=>$v['TaskId']));
                if(!empty($info))
                {
                    $update['TaskId'] = $info[0]['Id'];
                }
            }

            if(empty($update))
            {
                break;
            }

            $update = $this->common_model->update($table_name,$update,array('Id'=>$server_id));
        }
        return true;
    }


    public function update_by_user_id($anchor,$user_id)
    {
        if(empty($user_id))
        {
            return $this->update($anchor);
        }

        $tables = array('Users','Groups','RltGroupUser','RltProjectUser','RltTag','RltTaskUser','Tags','Projects','Tasks','Checklist');

        $data = array();

        //1、选出所有Modified大于anchor的数据
        foreach($tables as $table_name)
        {
            $a = $this->common_model->get_where($table_name,array('Modified>'=>$anchor));

            if($table_name == 'RltProjectUser')
            {
                //将用户想关的项目id放入数组$project_ids
            }

            if($table_name == 'RltTaskUser')
            {
                //将用户相关的任务id放入数组
            }

            if($table_name == 'Projects')
            {
                //将项目id追加到数组
            }

            if($table_name == 'Tasks')
            {

            }

            array_walk($a, function(&$t,$key,$p){
                $t['Table'] = $p;
            },$table_name);

            if(count($a) > 0)
            {
                $data = array_merge($data,$a);
            }
        }

        if(empty($data))
        {
            return array();
        }

        //2、按照modified升序排序
        return common::sortArrayAsc($data);
    }
}