<?php
/**
 * Created by PhpStorm.
 * User: CaylaXu <caylaxu@motouch.cn>
 * Date: 2015/11/10
 * Time：11:56
 * 公共接口
 */
class  Common_function extends MyRestController
{
    function __construct()
    {
        parent::__construct();
        $this->load->model('bll/milestone_bll');
        $this->load->model('bll/task_bll');
        $this->load->model('bll/rlt_task_user_bll');
        $this->load->model('bll/rlt_project_user_bll');
        $this->load->model('bll/checklist_bll');
    }

    /**
     * @function 更改完成度
     * @author CaylaXu
     */
    function progress_put()
    {
        $check = array(
            'Id'=>true,
            'Type' => true,
            'Progress'=>false
        );

        $params = common::check_params($check,$this->put(),true);

        if(!$params)
        {
            $this->response(array('Result'=>-1,'Msg'=>'参数非法'), 200);
            exit;
        }

        $result = false;

        if(strtolower($params['Type']) == 'task')
        {
            $result = $this->task_bll->change_progress($params['Id'],$params['Progress'],$this->put('UserId'));
        }

        if(strtolower($params['Type']) == 'checklist')
        {
            $result = $this->checklist_bll->change_progress($params['Id'],$params['Progress']);
        }

        if(!$result)
        {
            $this->response(array('Result'=>-1,'Msg'=>'操作失败'), 200);
            exit;
        }

        $this->response(array('Result'=>0,'Msg'=>'操作成功'), 200);
    }

    /**
     * @function 添加责任人或关注人
     * @User: CaylaXu
     */
    function rlt_user_post()
    {
        $check = array(
            'RltId'=>true,
            'UserId'=>true,
            'UserType' => true,
            'Type' => true
        );

        $params = common::check_params($check,$this->post(),true);
        if(!$params)
        {
            $this->response(array('Result'=>-1,'Msg'=>'参数非法'), 200);
            exit;
        }

        switch (strtolower($params['UserType']))
        {
            case 'principal':
                $type = 1;
                break;
            case 'follwers':
                $type = 3;
                break;
            default:
                $type = 3;
        }

        if(strtolower($params['Type']) == 'task')
        {
            $result = $this->rlt_task_user_bll->create($params['RltId'],$params['UserId'],$type);
        }
        else if(strtolower($params['Type']) == 'project')
        {
            $result = $this->rlt_project_user_bll->create($params['RltId'],$params['UserId'],$type);
        }
        else
        {
            $result = false;
        }

        if(!$result)
        {
            $this->response(array('Result'=>-1,'Msg'=>'操作失败'), 200);
            exit;
        }

        $this->response(array('Result'=>0,'Msg'=>'操作成功','Data'=>array('Id'=>$result)), 200);
    }

    /**
     * @function 添加责任人或关注人
     * @User: CaylaXu
     */
    function rlt_user_delete()
    {
        $check = array(
            'RltId'=>true,
            'Type' => true
        );

        $params = common::check_params($check,$this->delete(),true);
        if(!$params)
        {
            $this->response(array('Result'=>-1,'Msg'=>'参数非法'), 200);
            exit;
        }

        if(strtolower($params['Type']) == 'task')
        {
            $result = $this->rlt_task_user_bll->delete($params['RltId']);
        }
        else if(strtolower($params['Type']) == 'project')
        {
            $result = $this->rlt_project_user_bll->delete($params['RltId']);
        }
        else
        {
            $result = false;
        }

        if(!$result)
        {
            $this->response(array('Result'=>-1,'Msg'=>'操作失败'), 200);
            exit;
        }

        $this->response(array('Result'=>0,'Msg'=>'操作成功'), 200);

    }
}
