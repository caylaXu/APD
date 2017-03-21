<?php
/**
 * Created by PhpStorm.
 * User: cayla
 * Date: 2016/3/15
 * Description:app版本相关接口
 */
class App_version extends REST_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->model('bll/app_version_bll');
    }

    public function app_version_get()
    {
        $app_name = trim($this->get('AppName'));
        $app_type = trim($this->get('AppType'));
        $version_name = trim($this->get('VersionName'));

        if(empty($app_name)|| empty($app_type) || empty($version_name))
        {
            $this->response(array('result'=>-1,'msg'=>'非法请求'), 200);
            exit;
        }

        $data = $this->app_version_bll->query($app_name,$app_type,$version_name);

        if($data)
        {
            $result['Result'] = 0;
            $result['Msg']    = '获取成功';
            $result['Data']    = $data;
        }
        else
        {
            $result['Result'] = -1;
            $result['Msg']    = '无需升级';
            $result['Data']    = array();
        }

        $this->response($result, 200);
    }
}
