<?php
/**
 * Created by PhpStorm.
 * User: cayla
 * Date: 2015/11/8
 * Time: 20:04
 * 任务相关操作
 */
class Sync extends MyRestController
{

    function __construct()
    {
        parent::__construct();
        $this->load->model('bll/sync_bll');
        $this->load->model('bll/common_bll');
        $this->load->model('bll/user_bll');
    }

    /**
     * @function 获取更新数据
     * @author CaylaXu
     */
    function sync_get()
    {
        $anchor = $this->get('Anchor');
        $user_id = intval($this->get('UserId'));

        if (!is_numeric($anchor) || $user_id <= 0)
        {
            $this->response(array('Result'=>-1,'Msg'=>'参数非法'), 200);
            exit;
        }

        $max_modified = $this->common_bll->get_max_modified();
        if ($anchor >= $max_modified)
        {
            $this->response(array('Result'=>-1,'Msg'=>'参数非法'), 200);
        }

        $select = array('Id');
        $where = array('Id' => $user_id);
        $user = $this->user_bll->select_info($select, $where);
        if (!$user)
        {//用户不存在
            $this->response(array('Result'=>-1,'Msg'=>'该用户不存在'), 200);
        }

        //获取所有的变更记录
//        $data = $this->sync_bll->update($anchor);
        $data = $this->sync_bll->update_new($anchor, $user_id);
        $modified = $this->common_bll->get_max_modified(false);
        $return['Data'] = $data;
        $return['Anchor'] = $modified;
        $this->response(array('Result'=>0,'Msg'=>'获取成功','Data'=>$return), 200);
        exit;
    }

    /**
     * @function 提交数据
     * @author CaylaXu
     */
    function sync_put()
    {
        $anchor = $this->put('Anchor');
        $data = $this->put('Data');
        if (!is_numeric($anchor))
        {
            $this->response(array('Result'=>-1,'Msg'=>'参数非法'), 200);
            exit;
        }

        if(empty($data))
        {
            $this->response(array('Result'=>0,'Msg'=>'更新成功'), 200);
            exit;
        }

        //提交变更记录
        $result = $this->sync_bll->submit($anchor,$data);

        if(is_array($result))
        {
            $this->response(array('Result'=>0,'Msg'=>'同步成功','Data'=>$result), 200);
            exit;
        }

        $this->response(array('Result'=>-1,'Msg'=>'同步失败'), 200);
        exit;
    }
}
