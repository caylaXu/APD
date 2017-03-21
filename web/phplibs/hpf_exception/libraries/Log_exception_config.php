<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Log_exception_config
{

        public $log_exception_config;

        function __construct()
        {
                $this->log_exception_config['is_send_log']     = FALSE;
//                $this->log_exception_config['log_system_host'] = '123.56.102.104';
                $this->log_exception_config['log_system_host'] = '112.74.14.46';//beta环境告警中心
//                $this->log_exception_config['log_system_host'] = '120.24.66.7';//正式环境告警中心
                $this->log_exception_config['port']            = '8080';
                //可以配置请求api的地址
                $this->log_exception_config['api_path']        = array(
                                                                        'api/app_version', //到controller
                                                                        'api', //到module
                                                                        'api/student/get_student_info'//到function
                                                                    );
        }
}
