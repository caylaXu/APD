<?php
/**
 * Created by PhpStorm.
 * User: cayla
 * Date: 2015/11/8
 * Time: 20:04
 */
class Group extends MyController
{

    function __construct()
    {
        parent::__construct();
        $this->load->model('bll/group_bll');
        $this->load->model('bll/user_bll');
        $this->load->model('bll/rlt_group_user_bll');
    }

    public function create()
    {
        if ($_POST)
        {
            $check = array(
                'Title' => true,
                'HeadId' => false,
                'RltUser' => false,
                'CreatorId' =>true
            );

            $params = common::check_get_post($check,'post', false);
            if (!$params)
            {
                common::return_json('-1','参数非法','',true);
            }

            $check_data = true;//@todo 参数检查

            if(!$check_data)
            {
                common::return_json('-1','格式错误','',true);
            }

            $data = $params;
            $data['CreateTime'] = time();
            unset($data['RltUser']);
            //1、新建团队
            $group_id = $this->group_bll->create($data);

            if(!$group_id)
            {
                common::return_json('-1','新建团队失败','',true);
            }

            if(!empty($params['RltUser']))
            {
                //2、插入团队关联成员
                $rlt_result = $this->rlt_group_user_bll->insert_batch($group_id,$params['RltUser']);

                if(!$rlt_result)
                {
                    common::return_json('-1','团队成员关联失败','',true);
                }

            }
            common::return_json('0','新建团队成功','',true);
        }
        $this->hpf_smarty->display('backend/user/create.tpl');
    }

    public function edit()
    {
        if ($_POST)
        {
            $check = array(
                'Id'=>true,
                'Title' => true,
                'HeadId' => false,
                'RltUser' => false
            );

            $params = common::check_get_post($check,'post', false);

            if (!$params)
            {
                common::return_json('-1','参数非法','',true);
            }

            $check_data = true;//@todo 参数检查

            if(!$check_data)
            {
                common::return_json('-1','格式错误','',true);
            }

            $data = $params;
            unset($data['RltUser']);
            unset($data['Id']);

            //1、修改团队信息
            $group_update = $this->group_bll->update($data,array('Id'=>$params['Id']));

            if(!$group_update)
            {
                common::return_json('-1','修改团队失败','',true);
            }

            if(!empty($params['RltUser']))
            {
                //2、修改项目关联人员
                $rlt_result = $this->rlt_group_user_bll->update($params['Id'],$params['RltUser']);

                if(!$rlt_result)
                {
                    common::return_json('-1','团队成员关联失败','',true);
                }
            }

            common::return_json('0','修改团队成功','',true);
        }

        $group_id = isset($_GET['GroupId']) ?  $_GET['GroupId']:'';

        if(!is_numeric($group_id))
        {
            common::return_json('-1','参数非法','',true);
        }
        $group_info = $this->group_bll->group_info($group_id);
        common::return_json('0','获取成功',$group_info);
    }

    public function delete()
    {
        $group_id = isset($_GET['GroupId']) ?  $_GET['GroupId']:'';

        if(!is_numeric($group_id))
        {
            common::return_json('-1','参数非法','',true);
        }

        //1、删除团队
        $result = $this->group_bll->delete($group_id);

        if(!$result)
        {
            common::return_json('-1','删除失败','',true);
        }

        //2、删除团队成员关联
        $result = $this->rlt_group_user_bll->delete_info(array('GroupId'=>$group_id));

        if(!$result)
        {
            common::return_json('-1','删除团队成员关联失败','',true);
        }

        common::return_json('0','删除成功','',true);
    }
}
