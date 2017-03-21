<?php
/**
 * Created by PhpStorm.
 * User: cayla
 * Date: 2015/11/8
 * Time: 20:04
 */
class Milestone extends MyRestController
{

    function __construct()
    {
        parent::__construct();
        $this->load->model('bll/milestone_bll');
        $this->load->model('bll/project_bll');
    }

    /**
     * @function 新增目标
     * @author CaylaXu
     */
    public function milestone_post()
    {
            $check = array(
                'Title' => true,
                'Description' => false,
                'ProjectId'=>false,
                'ResponsibleId' => false,
                'DueDate' =>true,
//                'IsKey' => false,
                'CreatorId' => true
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

            $milestone_id = $this->milestone_bll->create($data);

            if(!$milestone_id)
            {
                $this->response(array('Result'=>-1,'Msg'=>'目标新建失败'), 200);
                exit;
            }
            $this->response(array('Result'=>0,'Msg'=>'目标新建成功','Data'=>array('MilestoneId'=>$milestone_id)), 200);
    }

    /**
     * @function 删除目标
     * @author CaylaXu
     */
    public function milestone_delete()
    {
        $milestone_id = $this->get('Id');

        if(!is_numeric($milestone_id))
        {
            $this->response(array('Result'=>-1,'Msg'=>'参数非法'), 200);
            exit;
        }

        $result = $this->milestone_bll->delete($milestone_id);

        if(!$result)
        {
            $this->response(array('Result'=>-1,'Msg'=>'删除失败'), 200);
            exit;
        }

        $this->response(array('Result'=>0,'Msg'=>'删除成功'), 200);
    }

    /**
     * @function 修改目标
     * @author CaylaXu
     */
    public function milestone_put()
    {
        $check = array(
            'Title' => true,
            'Description' => false,
            'ResponsibleId' => false,
            'DueDate' =>true,
            'IsKey' => false,
        );

        $milestone_id = $this->get('Id');

        $params = common::check_params($check,$this->put(), false);

        if (!$params || !is_numeric($milestone_id))
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
        $milestone_id = $this->milestone_bll->update($data,array('Id'=>$milestone_id));

        if(!$milestone_id)
        {
            $this->response(array('Result'=>-1,'Msg'=>'目标更新失败'), 200);
            exit;
        }

        $this->response(array('Result'=>0,'Msg'=>'更新目标成功'), 200);

    }

    /**
     * @function 分页请求目标列表
     * @author CaylaXu
     */
    function milestones_get()
    {
        $project_id = $this->get('ProjectId');
        $projects = $this->project_bll->query_project_by_id($project_id);

        if(empty($projects))
        {
            $this->response(array('Result'=>-1,'Msg'=>'项目不存在'), 200);
            exit;
        }

        $milestones = $this->milestone_bll->get_list($project_id);

        $data['Project'] = $projects;
        $data['Milestones'] = $milestones;
        $this->response(array('Result'=>0,'Msg'=>'获取成功','Data'=>$data), 200);
    }

    /**
     * @function 目标详情
     * @author CaylaXu
     */
    function milestone_get()
    {
        $id          = $this->get('Id');

        if(!is_numeric($id))
        {
            $this->response(array('Result'=>-1,'Msg'=>'参数非法'), 200);
            exit;
        }

        $result = $this->milestone_bll->milestone_info($id);

        $this->response(array('Result'=>0,'Msg'=>'获取成功','Data'=>$result), 200);
        exit;
    }
}
