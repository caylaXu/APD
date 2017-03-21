<?php
/**
 * Created by PhpStorm.
 * User: cayla
 * Date: 2015/11/8
 * Time: 20:04
 */
class Project extends MyRestController
{

    function __construct()
    {
        parent::__construct();
        $this->load->model('bll/project_bll');
        $this->load->model('bll/rlt_project_user_bll');
    }

    public function project_post()
    {
        try {
            $check = array(
                'Title' => true,
                'Description' => false,
                'ProjectManagerId' => false,
                'StartDate' =>true,
                'DueDate' => true,
                'CreatorId' => true,
                'Follwers'=>false
            );

            $params = common::check_params($check,$this->post(), false);

            if (!$params || !is_numeric($params['ProjectManagerId']))
            {
                throw new Exception("参数非法!", -1);
            }

            if(empty($params['ProjectManagerId']))
            {
                $params['ProjectManagerId'] = $params['CreatorId'];
            }

            if(!common::is_timestamp($params['StartDate']) || !common::is_timestamp($params['DueDate']))
            {
                throw new Exception("时间戳非法!", -1);
            }

            if($params['StartDate'] > $params['DueDate'])
            {
                throw new Exception("开始时间必须小于结束时间!", -1);
            }

            //1、新建项目
            if(!$project_id = $this->project_bll->create_project($params))
            {
                throw new Exception("新建项目失败!", -1);
            }

            $this->response(array('Result'=>0,'Msg'=>'新建项目成功','Data'=>array('ProjectId'=>$project_id)), 200);
            exit;

        } catch (Exception $e){
            $this->response(array('Result'=>$e->getCode(),'Msg'=>$e->getMessage()), 200);
            exit;
        }
    }

    public function project_delete()
    {
        $project_id = $this->get('Id');

        if(!is_numeric($project_id))
        {
            $this->response(array('Result'=>-1,'Msg'=>'参数非法'), 200);
            exit;
        }

        $result = $this->project_bll->delete($project_id);

        if(!$result)
        {
            $this->response(array('Result'=>-1,'Msg'=>'删除失败'), 200);
            exit;
        }

        $this->response(array('Result'=>0,'Msg'=>'删除成功'), 200);
        exit;
    }


    public function project_put()
    {
        $params = $this->put();
        $check_data = $this->project_bll->check_data($params);
        $project_id = $this->get('Id');

        if (!is_array($check_data))
        {
            $this->response(array('Result'=>-1,'Msg'=>$check_data), 200);
            exit;
        }

        if (!is_numeric($project_id))
        {
            $this->response(array('Result'=>-1,'Msg'=>$check_data), 200);
            exit;
        }

        $data = $check_data;
        
        //1、跟新项目
        $project_update = $this->project_bll->update($data,array('Id'=>$project_id));

        if(!$project_update)
        {
            $this->response(array('Result'=>-1,'Msg'=>'项目更新失败'), 200);
            exit;
        }

        if(!empty($params['Follwers']))
        {
            //2、插入项目关联人员
            $rlt_result = $this->rlt_project_user_bll->update_rlt_user($project_id,$params['Follwers'],3);

            if(!$rlt_result)
            {
                $this->response(array('Result'=>-1,'Msg'=>'项目成员关联失败'), 200);
                exit;
            }
        }

        $this->response(array('Result'=>0,'Msg'=>'更新项目成功'), 200);
    }

    function projects_get()
    {
        $user_id = $this->get('UserId');
        $type = $this->get('Type');
        $result = $this->project_bll->project_overview($user_id,$type);
        $this->response(array('Result'=>0,'Msg'=>'请求成功','Data'=>$result), 200);
    }

    public function project_get()
    {
        $project_id = $this->get('Id');

        if(!is_numeric($project_id))
        {
            $this->response(array('Result'=>-1,'Msg'=>'参数非法'), 200);
            exit;
        }

        $result = $this->project_bll->project_info($project_id);

        $this->response(array('Result'=>0,'Msg'=>'获取成功','Data'=>$result), 200);
        exit;
    }

    /**
     * @function 获取用户相关的项目列表
     * @User: CaylaXu
     */
    public function project_by_user_id_get()
    {
        $user_id = $this->get('UserId');

        if(!is_numeric($user_id))
        {
            $this->response(array('Result'=>-1,'Msg'=>'参数非法'), 200);
            exit;
        }

        $result = $this->project_bll->get_project_list_by_user_id($user_id,true);

        if(is_array($result))
        {
            $this->response(array('Result'=>0,'Msg'=>'获取成功','Data'=>$result), 200);
            exit;
        }

        $this->response(array('Result'=>-1,'Msg'=>'获取是啊比'), 200);
        exit;
    }
}
