<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Created by PhpStorm.
 * User: CaylaXu <caylaxu@motouch.cn>
 * Date: 2015/12/15
 * Time：11:27
 */
class Checklist_bll extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('bll/common_bll');
        $this->load->model('dal/db/Checklist_model');
    }

    public function insert_batch($task_id,$params){

        if(empty($params))
        {
            return false;
        }

        $data = array();
        $modified = $this->common_bll->get_max_modified();
        foreach($params as $k=>$v)
        {
            $temp['TaskId'] = $task_id;
            $temp['Title'] = $v;
            $temp['Method'] = 0;
            $temp['Modified'] = $modified;
            $temp['CreateTime'] = time();
            $temp['UniCode'] = common::create_uuid();
            $data[] = $temp;
        }
        return $this->Checklist_model->insert_batch($data);
    }

    public function create(array $params)
    {

        if(empty($params) || !is_array($params))
        {
            return false;
        }

        $params['Method'] = 0;
        $params['Modified'] = $this->common_bll->get_max_modified();
        $params['UniCode'] = isset($params['UniCode']) ? $params['UniCode'] : common::create_uuid();
        return $this->Checklist_model->create($params);
    }

    public function select_info(array $select,array $where)
    {
        return $this->Checklist_model->select_info($select,$where);
    }

    /**
     * @function 条件更新
     * @User: CaylaXu
     * @param array $data
     * @param array $where
     * @return mixed
     */
    public function update_where(array $data,array $where)
    {
        $data['Method'] = 1;
        $data['Modified'] = $this->common_bll->get_max_modified();
        return $this->Checklist_model->update_info($data,$where);
    }

    public function delete($id)
    {
        $modified = $this->common_bll->get_max_modified();
        return $this->Checklist_model->delete_info(array('Id'=>$id),$modified);
    }

    public function change_progress($id,$progress)
    {
        $data['IsComplete'] = empty($progress) ? 0 : 1;
        $data['Method'] = 1;
        $data['Modified'] = $this->common_bll->get_max_modified();
        return $this->Checklist_model->update_info($data,array('Id'=>$id));
    }

    public function check_data($params)
    {
        $filed = array('Title','IsComplete');

        if(empty($params))
        {
            return false;
        }

        $data = array();

        foreach($params as $k => $v)
        {
            if($k == 'Title')
            {
                if(empty($v)) return false;
                $data[$k] = $v;
            }
            else if($k == 'IsComplete')
            {
                $data[$k] = empty($v) ? 0:1;
            }
            else if(in_array($k,$filed))
            {
                $data[$k] = $v;
            }
        }
        return $data;
    }

    /**
     * @function 批量更新检查项
     * @User: CaylaXu
     * @param $checklist
     * @return bool
     */
    public function update_checklist_batch($checklist)
    {
        if(empty($checklist) || !is_array($checklist))
        {
            return true;
        }

        foreach($checklist as $k=>$v)
        {
            //1、检查字段是否正确
            $data = $v;
            unset($data['Id']);
            //2、根据字段更新数据库
            if($v['Id'] == 0)//新增
            {
                $data['CreateTime'] = time();
                $result = $this->create($data);
            }
            else//更新或删除
            {
                $result = $this->update($data,array('Id'=>$v['Id']));
            }

            if(!$result)
            {
                return false;
            }
        }

        return true;
    }

    public function update(array $data,array $where)
    {
        $data['Method'] = isset($data['Method']) ? $data['Method'] : 1;
        $data['Modified'] = $this->common_bll->get_max_modified();
        $result = $this->Checklist_model->update_info($data,$where);
        return $result;
    }

    /**
     * @function 获取需要同步的checklist
     * @author Peter
     * @param $task_ids
     * @param int $anchor
     * @return mixed
     */
    public function get_sync_checklists($task_ids, $anchor = 0)
    {
        if (!is_array($task_ids) || !$task_ids)
        {
            return array();
        }

        $checklists = $this->Checklist_model->get_sync_checklists($task_ids, $anchor);

        array_walk($checklists, function (&$val, $key, $table_name)
        {
            $val['Table'] = $table_name;
        }, 'Checklist');

        return $checklists;
    }
}