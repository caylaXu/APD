<?php
/**
 * Created by PhpStorm.
 * User: cayla
 * Date: 2015/11/8
 * Time: 20:04
 */
class Checklist extends MyController
{

    function __construct()
    {
        parent::__construct();
        $this->load->model('bll/checklist_bll');
    }

    public function create()
    {
            $check = array('Title' => true, 'TaskId' => true);
            $params = common::check_get_post($check,'post', false);

            if (!$params)
            {
                common::return_json('-1','参数非法','',true);
            }

            $check_data =  common::check_string_length(1,750,$params['Title']);
            if(!$check_data)
            {
                common::return_json('-1','检查项过长','',true);
            }

            if(!$check_data)
            {
                common::return_json('-1','格式错误','',true);
            }

            $data = $params;
            $data['CreateTime'] = time();
            $checklist_id = $this->checklist_bll->create($data);

            if(!$checklist_id)
            {
                common::return_json('-1','新建检查项失败','',true);
            }

            common::return_json('0','新建检查项成功',array('Id'=>$checklist_id),true);
    }

    public function edit()
    {
        if($_POST)
        {
            $id = $_POST['Id'];
            if (!is_numeric($id))
            {
                common::return_json('-1','参数非法','',true);
            }

            $data = $_POST;
            unset($data['Id']);

            if(empty($data))
            {
                common::return_json('-1','修改检查项失败','',true);
            }

            $check_data =  common::check_string_length(1,750,$data['Title']);
            if(!$check_data)
            {
                common::return_json('-1','检查项过长','',true);
            }

            $checklist_id = $this->checklist_bll->update_where($data,array('Id'=>$id));

            if(!$checklist_id)
            {
                common::return_json('-1','修改查项失败','',true);
            }
            common::return_json('0','修改检查项成功');
        }
    }

    public function delete()
    {
        if($_POST)
        {
            $id = $_POST['Id'];
            if (!is_numeric($id))
            {
                common::return_json('-1','参数非法','',true);
            }

            $result = $this->checklist_bll->delete($id);

            if(!$result)
            {
                common::return_json('-1','删除失败','',true);
            }
            common::return_json('0','删除成功');
        }
    }
}
