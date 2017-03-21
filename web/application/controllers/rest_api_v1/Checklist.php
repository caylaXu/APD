<?php
/**
 * Created by PhpStorm.
 * User: cayla
 * Date: 2015/11/8
 * Time: 20:04
 */
class Checklist extends MyRestController
{

    function __construct()
    {
        parent::__construct();
        $this->load->model('bll/checklist_bll');
    }

    public function checklist_post()
    {
        $check = array('Title' => true, 'TaskId' => true);
        $params = common::check_params($check,$this->post(),false);

        if (!$params)
        {
            $this->response(array('Result'=>-1,'Msg'=>'参数非法'), 200);
            exit;
        }

        $data = $params;
        $data['CreateTime'] = time();
        $checklist_id = $this->checklist_bll->create($data);

        if(!$checklist_id)
        {
            $this->response(array('Result'=>-1,'Msg'=>'新建检查项失败'), 200);
            exit;
        }
        $this->response(array('Result'=>0,'Msg'=>'新建检查项成功','Data'=>array('Id'=>$checklist_id)), 200);
    }

    public function checklist_put()
    {
        $id = $this->get('Id');
        if (!is_numeric($id))
        {
            common::return_json('-1','参数非法','',true);
        }

        $check_data = $this->checklist_bll->check_data($this->put());

        if(!$check_data)
        {
            common::return_json('-1','格式错误','',true);
        }

        $data = $check_data;
        $update = $this->checklist_bll->update_where($data,array('Id'=>$id));

        if(!$update)
        {
            $this->response(array('Result'=>-1,'Msg'=>'修改查项失败'), 200);
            exit;
        }
        $this->response(array('Result'=>0,'Msg'=>'修改检查项成功'), 200);
    }


    public function checklist_delete()
    {
        $id = $this->get('Id');

        if (!is_numeric($id))
        {
            common::return_json('-1','参数非法','',true);
        }

        $result = $this->checklist_bll->delete($id);

        if(!$result)
        {
            $this->response(array('Result'=>-1,'Msg'=>'删除失败'), 200);
            exit;
        }
        $this->response(array('Result'=>0,'Msg'=>'删除成功'), 200);
    }
}
