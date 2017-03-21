<?php

/**
 * Class Login
 * @author CaylaXu <caylaxu@motouch.cn>
 */
    class Login extends MyRestController
    {
        function __construct()
        {
            parent::__construct();
            $this->load->model('bll/user_bll');
            $this->load->model('bll/task_bll');
            $this->load->model('bll/redis_bll');
            $this->load->model('enum/enum');
            $this->load->model('bll/phone_login_bll');
        }


        /**
         * @function 1.0登录接口不支持极光推送
         * @User: CaylaXu
         */
        function login_put()
        {
            $mobile = trim($this->put('Mobile'));
            $password = trim($this->put('Password'));

            //参数校验
            if(empty($mobile) || (!common::is_mobile($mobile)&&!common::is_email($mobile)))
            {
                $this->response(array('Result'=>-1,'Msg'=>'参数非法'), 200);
                exit;
            }

            $user_info = $this->user_bll->get_user_by_mobile_or_email($mobile);

            if(empty($user_info))
            {
                $this->response(array('Result'=>-1,'Msg'=>'账号不存在'), 200);
                exit;
            }

            $check_password = common::check_password($user_info,$password);

            if(!$check_password)
            {
                $this->response(array('Result'=>-1,'Msg'=>'密码错误'), 200);
                exit;
            }

            $info = array(
                'Id'=>$user_info['Id'],
                'Name' => $user_info['Name'],
                'Email' => $user_info['Email'],
                'Mobile' => $user_info['Mobile'],
                'Avatar' => $user_info['Avatar'],
                'Path' => common::resources_full_path('avatar', '', 'picture')
            );

            //生成会话id
            $session_id = common::generate_session_id($user_info['Id'],'apd');
            $this->redis_bll->set_online_session($session_id,'apd');
            $this->response(array('Result'=>0,'Msg'=>'登录成功','Data'=>$info), 200);
        }

        /**
         * @function 新的登录接口记录session_id
         * @User: CaylaXu
         */
        function login_new_put()
        {
            $mobile = trim($this->put('Mobile'));
            $password = trim($this->put('Password'));
            $phone_token = trim($this->put('PhoneToken'));
            //参数校验
            if(empty($mobile) || (!common::is_mobile($mobile)&&!common::is_email($mobile)))
            {
                $this->response(array('Result'=>-1,'Msg'=>'参数非法'), 200);
                exit;
            }

            $ip_address=  $this->input->ip_address;
            $user_info = $this->user_bll->get_user_by_mobile_or_email($mobile);

            if(empty($user_info))
            {
                $this->response(array('Result'=>-1,'Msg'=>'账号不存在'), 200);
                exit;
            }

            $check_password = common::check_password($user_info,$password);

            if(!$check_password)
            {
                $this->response(array('Result'=>-1,'Msg'=>'密码错误'), 200);
                exit;
            }

            if(!empty($phone_token))
            {

//                {//这一段逻辑支持单点登录
//
//                    $login_by_other = $this->phone_login_bll->check_user_is_online($user_info['Id'],enum::PhoneLoginTypeApd);
//
//                    if(isset($login_by_other['PhoneToken']))//有设备在线
//                    {
//                        //修改状态
//                        $this->phone_login_bll->update_where(array('Status' => 0),array('Id' => $login_by_other['Id']));
//
//                        if(!empty($login_by_other['CustomSessionId']))
//                        {
//                            $this->redis_bll->set_offline_session($login_by_other['CustomSessionId'],'coach');
//                        }
//
//                        //判断phone token不为空，且不是当前这台登录的设备
//                        if(!empty($login_by_other['PhoneToken']) && ($login_by_other['PhoneToken']!= $phone_token))
//                        {
//                            //消息推送
//                            $res = Common::app_push_new("APD", (array) $login_by_other['PhoneToken'],"APD","您的账号已经在其他设备登陆",2,
//                            array('android','ios'),array('Type'=>enum::EnumPushType), $this->config->item('apns_production'));
//                        }
//                    }
//                }

                //生成会话id
                $session_id = common::generate_session_id($user_info['Id'],'apd');

                $insert_data = array(
                    'UserId' => $user_info['Id'],
                    'PhoneToken'=>$phone_token,
                    'IpAddress'=>$ip_address,
                    'loginTime'=>time(),
                    'status'=>enum::PhoneUserStatusOnline,
                    'Type' => enum::PhoneLoginTypeApd,
                    'CustomSessionId' =>$session_id
                );

                $update_result = $this->phone_login_bll->insert($insert_data);

                if(!$update_result)
                {
                    $this->response(array('Result'=>-1,'Msg'=>'登陆失败'), 200);
                    exit;
                }
            }

            $info = array(
                'Id'=>$user_info['Id'],
                'Name' => $user_info['Name'],
                'Email' => $user_info['Email'],
                'Mobile' => $user_info['Mobile'],
                'Avatar' => $user_info['Avatar'],
                'Path' => common::resources_full_path('avatar', '', 'picture'),
                'CustomSessionId' => '',
            );

            //生成会话id
//            $session_id = common::generate_session_id($user_info['Id'],'apd');
//            $this->redis_bll->set_online_session($session_id,'apd');
            $this->response(array('Result'=>0,'Msg'=>'登录成功','Data'=>$info), 200);
        }

        /**
         * @function 用户登出接口1.0不支持极光推送
         * @author CaylaXu
         */
        function quit_put()
        {
            $user_id = $this->put("UserId");
            if(!is_numeric($user_id))
            {
                $this->response(array('result' => -1, 'msg' => '参数非法'), 200);
                exit;
            }

            $user_info = $this->user_bll->select_info(array(),array('Id'=>$user_id,'Status'=>1));

            if(empty($user_info))
            {
                $this->response(array('Result'=>-1,'Msg'=>'账号不存在'), 200);
                exit;
            }

            $login_info = $this->phone_login_bll->select_info(array(),array('UserId' => $user_id,'Type'=>enum::PhoneLoginTypeApd,'Status'=>enum::PhoneUserStatusOnline),false);

            $result_update = $this->phone_login_bll->update_where(array('status' => 0), array('UserId' => $user_id,'Type'=>enum::PhoneLoginTypeApd));

            if(!$result_update)
            {
                $this->response(array('Result'=>-1,'Msg'=>'登出失败'), 200);
                exit;
            }

            if(isset($login_info['CustomSessionId']) && !empty($login_info['CustomSessionId']))
            {
                $this->redis_bll->set_offline_session($login_info['CustomSessionId'],'apd');
            }

            $this->response(array('Result'=>0,'Msg'=>'登出成功'), 200);
        }

        /**
         * @function 用户登出接口
         * @author CaylaXu
         */
        function quit_new_put()
        {
            $user_id = $this->put("UserId");
            $phone_token = $this->put('PhoneToken');
            if(!is_numeric($user_id))
            {
                $this->response(array('result' => -1, 'msg' => '参数非法'), 200);
                exit;
            }

            $user_info = $this->user_bll->select_info(array(),array('Id'=>$user_id,'Status'=>1));

            if(empty($user_info))
            {
                $this->response(array('Result'=>-1,'Msg'=>'账号不存在'), 200);
                exit;
            }



            if(!empty($phone_token))
            {
                $where = array(
                    'UserId' => $user_id,
                    'Type' =>enum::PhoneLoginTypeApd,
                    'PhoneToken' => $phone_token,
                );

                $result_update = $this->phone_login_bll->update_where(array('Status' => 0),$where);

                if(!$result_update)
                {
                    $this->response(array('Result'=>-1,'Msg'=>'登出失败'), 200);
                    exit;
                }
            }
//            if(isset($login_info['CustomSessionId']) && !empty($login_info['CustomSessionId']))
//            {
//                $this->redis_bll->set_offline_session($login_info['CustomSessionId'],'apd');
//            }

            $this->response(array('Result'=>0,'Msg'=>'登出成功'), 200);
        }


        /**
         * @function 获取验证码
         * @User: CaylaXu
         */
        function validate_code_get()
        {
            $mobile = trim($this->get('Mobile'));
            $type = trim($this->get('Type'));
            $email = trim($this->get('Email'));

            if(!in_array($type,array('register','forget','bound')) || (empty($mobile) && empty($email)))
            {
                $this->response(array('Result' => -1, 'Msg' => '参数非法'), 200);
                exit;
            }

            if(!empty($mobile))
            {
                if(!common::is_mobile($mobile))
                {
                    $this->response(array('Result' => -1, 'Msg' => '手机号码格式不正确'), 200);
                    exit;
                }

                $user_info = $this->user_bll->select_info(array(),array('Mobile'=>$mobile,'Status'=>1));
                if($type == 'register')
                {
                    if(!empty($user_info))
                    {
                        $this->response(array('Result' => -1, 'Msg' => '该手机号已注册'), 200);
                        exit;
                    }
                }
                else if($type == 'forget')//忘记密码
                {
                    if(empty($user_info))
                    {
                        $this->response(array('Result' => -1, 'Msg' => '该手机号还未注册'), 200);
                        exit;
                    }
                }
                $key = 'apd_'.$type.'_random' . $mobile;
                $random_code      = Common::get_random_code();
                $this->redis_bll->my_set($key,$random_code,40 * 60);
                $message_content  = "91恋车验证码".$random_code;
                $send_result = common::send_sms($message_content,$mobile);
            }
            else if(!empty($email))
            {
                if(!common::is_email($email))
                {
                    $this->response(array('Result' => -1, 'Msg' => '邮箱格式不正确'), 200);
                    exit;
                }

                $user_info = $this->user_bll->select_info(array(),array('Email'=>$email,'Status'=>1));
                if($type == 'register')
                {
                    if(!empty($user_info))
                    {
                        $this->response(array('Result' => -1, 'Msg' => '该邮箱已注册'), 200);
                        exit;
                    }
//                    $key = 'apd_'.$type.'_random' . $email;
                }
                else if($type == 'forget')//忘记密码
                {
                    if(empty($user_info))
                    {
                        $this->response(array('Result' => -1, 'Msg' => '该邮箱还未注册'), 200);
                        exit;
                    }
//                    $key = 'apd_forget_random'.$email;//忘记密码验证码
                }

                $key = 'apd_'.$type.'_random' . $email;

                $random_code = Common::get_random_code();
                $this->redis_bll->my_set($key,$random_code,40 * 60);
                $host = $_SERVER["HTTP_HOST"];
                $content = <<<BODY
<html><head></head><body><table><tr><td bgcolor=#0373d6 height=54 align=center><table><tr><td align=center><img src=http://{$host}/resource/asset/img/email-logo.png width=71 height=54></td></tr></table></td></tr><tr><td height=227 align=center style="padding: 0 15px;"><table border=0 cellspacing=0 width=480><tr><td><table width=100% border=0 cellpadding=0><tr><td><table cellspacing=0 border=0 align=left><tr><td width=550 align=left valign=top><table width=100% border=0 cellspacing=0><tr><td align=left valign=top style="font-size:14px; color:#7b7b7b; line-height: 25px; padding: 20px 0px">你此次重置密码的验证码如下,请在 30 分钟内输入验证码进行下一步操作。 如非你本人操作，请忽略此邮件。</td></tr><tr><td align=center><table border=0 cellspacing=0 cellpadding=0><tr><td><span><div style="padding:10px 18px;border-radius:3px;text-align:center;background-color:#ecf4fb;color:#4581E9;font-size:20px;font-weight:700;letter-spacing:2px; margin:0;"><span>{$random_code}</span></div></span></td></tr></table></td></tr></table></td></tr></table></td></tr></table></td></tr></table></td></tr></table></body></html>
BODY;
                $send_result = common::send_email('APD系统验证码',$content,$email);
                if($send_result != false)
                {
                    $result['Result'] = 0;
                    $result['Msg']    = '恭喜您，获取成功';
                }
            }

            if($send_result != false)
            {
                $result['Result'] = 0;
                $result['Msg']    = '恭喜您，获取成功';
                $result['Data'] = array('AuthCode'=>$random_code);
            }
            else
            {
                $result['Result'] = -1;
                $result['Msg'] = '发送失败,请稍后再试';
            }
            $this->response($result, 200);
        }

        /**
         * @function 注册接口
         * @User: CaylaXu
         */
        function register_post()
        {
            $mobile = trim($this->post('Mobile'));
            $password = trim($this->post('Password'));
            $auth_code = trim($this->post('AuthCode'));

            if(!common::is_mobile($mobile))
            {
                $this->response(array('Result' => -1, 'Msg' => '手机号码格式不正确'), 200);
                exit;
            }

            //@todo 校验密码格式
            $check_password = true;

            if(!$check_password)
            {
                $this->response(array('Result' => -1, 'Msg' => '密码格式不正确'), 200);
                exit;
            }

            //@todo 过期需要返回过期
            //验证验证码是否正确
            $check_code = $this->redis_bll->my_check_mobile_and_verif($mobile,$auth_code,'apd_register_random');
//            $check_code = true;
            if(!$check_code)
            {
                common::return_json(-1,'验证码过期或错误','',true);
            }

            $user_info = $this->user_bll->select_info(array(),array('Mobile'=>$mobile));
            $params['RegistrationDate'] = time();
            $params['Mobile'] = $mobile;
            $result = false;
            $user_id = 0;
            if(!empty($user_info))
            {
                if(!empty($user_info[0]['RegistrationDate']) && $user_info[0]['Status'] == 1)
                {
                    $this->response(array('Result' => -1, 'Msg' => '手机号被占用'), 200);
                    exit;
                }
                else//注册时间为空（说明是别人帮他添加的自己未注册）
                {
                    $hash = common::hash(md5($password));
                    $params['Avatar'] = "default.jpg";
                    $params['Password'] = $hash['hash'];
                    $params['Salt'] = $hash['salt'];
                    $params['Status'] = 1;
                    $result = $this->user_bll->update($params,array('Id'=>$user_info[0]['Id']));
                    $user_id = $user_info[0]['Id'];
                }
            }
            else
            {
                $hash = common::hash(md5($password));
                $params['Name'] = $mobile;
                $params['Avatar'] = 'default.jpg';
                $params['Password'] = $hash['hash'];
                $params['Salt'] = $hash['salt'];
                $result = $this->user_bll->create($params);
                $user_id = $result;
            }

            if(!$result)
            {
                $this->response(array('Result' => -1, 'Msg' => '注册失败'), 200);
                exit;
            }

            $params['Id'] = $user_id;
            $this->response(array('Result' => 0, 'Msg' => '注册成功' ,'Data' => $params), 200);
        }


        /**
         * @function 修改密码
         * @User: CaylaXu
         */
        public function password_put()
        {
            $mobile = trim($this->put('Mobile'));
            $email = trim($this->put('Email'));
            $password = trim($this->put('Password'));
            $auth_code = trim($this->put('AuthCode'));

            if(empty($password) || empty($auth_code) || (empty($mobile) && empty($email)))
            {
                common::return_json(-1,'参数非法','',true);
            }

            if(!empty($mobile))
            {
                if(!common::is_mobile($mobile))
                {
                    common::return_json(-1,'手机号格式不正确','',true);
                }

                $user_info = $this->user_bll->select_info(array(),array('Mobile'=>$mobile,'Status'=>1));
                if(empty($user_info))
                {
                    $this->response(array('Result' => -1, 'Msg' => '该手机号还未注册'), 200);
                    exit;
                }
                //验证验证码是否正确
                $check_code = $this->redis_bll->my_check_mobile_and_verif($mobile, $auth_code,'apd_forget_random');
            }
           else
           {
               if(!common::is_email($email))
               {
                   $this->response(array('Result' => -1, 'Msg' => '邮箱格式不正确'), 200);
                   exit;
               }

               $user_info = $this->user_bll->select_info(array(),array('Email'=>$email,'Status'=>1));
               if(empty($user_info))
               {
                   $this->response(array('Result' => -1, 'Msg' => '该邮箱还未注册'), 200);
                   exit;
               }
               //验证验证码是否正确
               $check_code = $this->redis_bll->my_check_mobile_and_verif($email, $auth_code,'apd_forget_random');
           }
            if(!$check_code)
            {
                common::return_json(-1,'验证码错误','',true);
            }

            $hash = common::hash(md5($password));
            $data['Password'] = $hash['hash'];
            $data['Salt'] = $hash['salt'];
            $result = $this->user_bll->update($data,array('Id'=>$user_info[0]['Id']));

            if(!$result)
            {
                $this->response(array('Result' => -1, 'Msg' => '修改失败'), 200);
                exit;
            }

            $this->response(array('Result' => 0, 'Msg' => '修改成功'), 200);
            exit;
        }


        /**
         * @function 第三方登录接口
         * @User: CaylaXu
         */
        function third_login_post()
        {
            $type = strtolower($this->post('Type'));

            if(!in_array($type,array('wechat','qq','weibo')))
            {
                $this->response(array('Result' => -1, 'Msg' => '参数非法'), 200);
                exit;
            }

            $user_id = $this->user_bll->social_login($this->post());


            if(!is_numeric($user_id))
            {
                $this->response(array('Result' => -1, 'Msg' => '请求失败'), 200);
                exit;
            }

            $user_info =  $this->user_bll->select_info(array('Id','Name','Avatar'),array('Id' => $user_id),false);
            $user_info['Path'] = common::resources_full_path('avatar', '', 'picture');
            if(empty($user_info))
            {
                $this->response(array('Result' => -1, 'Msg' => '用户不存在'), 200);
                exit;
            }

            $phone_token = $this->post('PhoneToken');
            if(!empty($phone_token))
            {
                $insert_data = array(
                    'UserId' => $user_info['Id'],
                    'PhoneToken'=>$phone_token,
                    'IpAddress'=>'',
                    'status'=>enum::PhoneUserStatusOnline,
                    'Type' => enum::PhoneLoginTypeApd,
                    'LoginTime' => time(),
                    'CustomSessionId' =>''
                );

                $update_result = $this->phone_login_bll->insert($insert_data);

                if(!$update_result)
                {
                    $this->response(array('Result'=>-1,'Msg'=>'登陆失败'), 200);
                    exit;
                }
            }

            $user_info['Path'] = common::resources_full_path('avatar', '', 'picture');
            $this->response(array('Result' => 0, 'Msg' => '请求成功', 'Data'=> $user_info), 200);
            exit;
        }
    }
