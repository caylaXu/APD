<?php
    /**
     * Created by PhpStorm.
     * User: CaylaXu <caylaxu@motouch.cn>
     * Date: 2015/8/6
     * Time：10:56
     */
    require APPPATH . '/libraries/REST_Controller.php';
    class MyRestController extends REST_Controller
    {
        function __construct()
        {
            parent::__construct();
            $this->load->model('enum/return_code');
            $rsegments = $this->uri->rsegments;

            if(isset($rsegments[2]))
            {
                $object_called = $rsegments[2];
                $controller_method = $object_called . '_' . $this->request->method;
                if($controller_method != 'login_put')//除登录页外全部做会话验证
                {
//                    $check = $this->check_custom_session_id($this->header('CustomSessionId'));
                  $check = true;
                    if(is_array($check))
                    {
                        $this->response($check, 200);
                        exit;
                    }
                }
            }
        }

        /**
         * @function 检查会话id是否过期
         * @author CaylaXu
         * @param string $custom_session_id
         * @return bool
         */
        function check_custom_session_id($custom_session_id = '')
        {
            $this->load->model('bll/redis_bll');
            if(empty($custom_session_id))
            {
                return return_code::$CommonCode['InvalidArgument'];
            }

            $expiration_time = $this->redis_bll->get_online_session($custom_session_id,'apd');

            if(empty($expiration_time))
            {
                return return_code::$AppCode['UserOffline'];
            }

            if(time() > $expiration_time)
            {
                return return_code::$AppCode['PassDue'];
            }

            return true;
        }
    }