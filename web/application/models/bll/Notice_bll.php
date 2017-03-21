<?php
/**
 * Created by PhpStorm.
 * User: CaylaXu <caylaxu@motouch.cn>
 * Date: 2016/4/6
 * Time：15:13
 */
class Notice_bll extends CI_Model
{
    /**
     * 1、注册邀请
     * 2、指派项目经理
     * 3、指派项目成员
     * 4、指派项目关注人
     * 5、指派任务
     * 6、邀请关注任务
     * 7、完成任务
     */
    public $notice_config = array(
        1=>array(
            'sms' => true,
            'email' => true,
            'app_push' => false,
            'notice' => false,
        ),
        2=>array(
            'sms' => false,
            'email' => true,
            'app_push' => true,
            'notice' => true,
        ),
        3=>array(
            'sms' => false,
            'email' => true,
            'app_push' => true,
            'notice' => true,
        ),
        4=>array(
            'sms' => false,
            'email' => false,
            'app_push' => false,
            'notice' => true,
        ),
        5=>array(
            'sms' => false,
            'email' => true,
            'app_push' => true,
            'notice' => true,
        ),
        6=>array(
            'sms' => false,
            'email' => false,
            'app_push' => false,
            'notice' => true,
        ),
        7=>array(
            'sms' => false,
            'email' => false,
            'app_push' => false,
            'notice' => true,
        ),
    );//通知配置

    public $template = array(
        1 => '您的同事 {from_user} 邀请你注册APD {rlt_content}',
        2 => '{from_user} 给你分配了新项目 {rlt_content}',
        3 => '{from_user} 把你加入了项目 {rlt_content}',
        4 => '{from_user} 提醒你关注项目 {rlt_content}',
        5 => '{from_user} 给你分配了新任务 {rlt_content}',
        6 => '{from_user} 提醒你关注任务 {rlt_content}',
        7 => '{from_user} 完成了任务 {rlt_content}',
    );

    public function __construct()
    {
        $this->load->model('dal/db/user_model');
        $this->load->model('dal/db/project_model');
        $this->load->model('dal/db/task_model');
        $this->load->model('dal/db/notice_model');
        $this->load->model('dal/db/phone_login_model');
        $this->load->model('enum/enum');
    }

    /**
     * @function 通知
     * @User: CaylaXu
     * @param $self
     * @param $user_ids
     * @param $type
     * @param array $params
     * @return bool
     */
    public function notice($self,$user_ids,$type,$params = array())
    {
        if(empty($user_ids))
        {
            return false;
        }

        //强制转换成数组
        if(!is_array($user_ids))
        {
            $user_ids = explode(',',$user_ids);
        }

        $user_ids = array_unique($user_ids);

        $notice_config = $this->notice_config[$type];

        foreach($user_ids as $k=>$v)
        {
            if($v != $self)
            {
                $user_config = array();//@todo 后期从数据库中读用户配置
                $config = $this->combine_config($notice_config,$user_config);
                $this->send($self,$v,$config,$type,$params);
            }
        }

        return true;
    }


    /**
     * @function 合并系统配置与个人配置
     * @User: CaylaXu
     * @param $arr1
     * @param $arr2
     * @return mixed
     */
    public function combine_config($arr1,$arr2)
    {
        if(empty($arr1) || empty($arr2))
        {
            return $arr1;
        }

        foreach($arr1 as $k => &$v)
        {
            $v = isset($arr2[$k]) ? $arr2[$k]&&$v : $v;
        }

        return $arr1;
    }


    /**
     * @function 执行通知
     * @User: CaylaXu
     * @param $self
     * @param $user_id
     * @param $config
     * @param $type
     * @param $params
     * @return bool
     */
    public function send($self,$user_id,$config,$type,$params)
    {
        $notice = isset($config['notice']) ? $config['notice'] : false;
        $sms = isset($config['sms']) ? $config['sms'] : false;
        $email = isset($config['email']) ? $config['email'] : false;
        $app_push = isset($config['app_push']) ? $config['app_push'] : false;

        if(!$notice && !$sms && !$email && !$app_push)//都不推送直接返回成功
        {
            return true;
        }

        $self_info = $this->user_model->select_info(array('Id','Name','UniCode'),array('Id'=>$self),false);
        $user_info = $this->user_model->select_info(array('Id','Email','Mobile','Name'),array('Id'=>$user_id),false);

        if(empty($self_info) || empty($user_info))
        {
            return false;
        }

        $from_user = $self_info['Name'];

        if($type == 1)//注册通知
        {
            $rlt_content = 'http://'.$_SERVER["HTTP_HOST"].'/backend/login/register?';
            $url = 'http://'.$_SERVER["HTTP_HOST"].'/backend/login/register?';
        }
        else if(in_array($type,array(2,3,4)))//项目相关
        {
            $project_info = $this->project_model->select_info(array('Title','UniCode'),array('Id'=>$params['RltId']),false);
            $rlt_id = $project_info['UniCode'];
            $rlt_content = isset($project_info['Title']) ? $project_info['Title'] : '';
            $url = 'http://'.$_SERVER["HTTP_HOST"].'/backend/task/task_tree?ProjectId='.$params['RltId'];
        }
        else if(in_array($type,array(5,6,7)))//任务相关
        {
            $task_info = $this->task_model->select_info(array('Title','UniCode'),array('Id'=>$params['RltId']),false);
            $rlt_id = $task_info['UniCode'];
            $rlt_content = isset($task_info['Title']) ? $task_info['Title'] : '';
            $url = 'http://'.$_SERVER["HTTP_HOST"].'/backend/workbench';
        }
        else
        {
            return false;
        }

        if($notice)//发通知（记录）
        {
            $json_data = array('FromUser' => $self_info['UniCode'], 'RltId' => $rlt_id, 'RltContent' => $rlt_content);
            $data = array(
                'UserId' => $user_id,
                'Type' => $type,
                'IsRead' => 0,
                'Content' => json_encode($json_data),
                'CreateTime' => time(),
                'Method' => 0,
                'Modified' => $this->common_bll->get_max_modified()
            );
            $this->notice_model->create($data);
        }


        if($sms)//发短信
        {
            if($type == 1)
            {
                $content = preg_replace(array('/{from_user}/','/{rlt_content}/'),array($from_user,$rlt_content.'Source=mobile'),$this->template[$type]);
            }
            else
            {
                $content = preg_replace(array('/{from_user}/','/{rlt_content}/'),array($from_user,$rlt_content),$this->template[$type]);
            }

            if(!empty($user_info['Mobile']))
            {
                common::send_sms($content,$user_info['Mobile']);
            }
        }

        if($email)//发邮件
        {
            if($type == 1)
            {
                $rlt_content .= 'Source=email';
                $url .= 'Source=email';
            }
            $content = preg_replace(array('/{from_user}/','/{rlt_content}/'),array($from_user,'<a href="'.$url.'">'.$rlt_content.'</a>'),$this->template[$type]);
            if(!empty($user_info['Email']))
            {
                common::send_email('APD系统通知',$content,$user_info['Email']);
            }

        }
        if($app_push)//发推送
        {
            $login_info = $this->phone_login_model->select_info(array('PhoneToken'),array('UserId'=>$user_id,'Status'=>enum::PhoneUserStatusOnline));
            $content = preg_replace(array('/{from_user}/','/{rlt_content}/'),array($from_user,$rlt_content),$this->template[$type]);
            if(!empty($login_info))
            {
                $token_array = array();
                foreach($login_info as $k=>$v)
                {
                    if(!empty($v['PhoneToken']))
                    {
                        $token_array[] = $v['PhoneToken'];
                    }
                }
                if(!empty($token_array))
                {
                    //消息推送
                    Common::app_push_new("APD",$token_array,"APD",$content,2,
                        array('android','ios'),array('Type'=>$type,'RltId'=>$rlt_id), $this->config->item('apns_production'));
                }
            }
        }
        return true;
    }

    /**
     * @function 同步时推送通知
     * @User: CaylaXu
     * @param $params
     * @return bool
     */
    public function sync_send($params)
    {
        $type = $params['Type'];
        $json_data = empty($params['Content']) ? array() : json_encode($params['Content']) ;

        if(empty($json_data))
        {
            return true;
        }

        $self_unicode = $json_data['FromUser'];
        $user_id = $params['UserId'];
        $rlt_id_unicode = $params['RltId'];

        $notice_config = $this->notice_config[$type];
        $user_config = array();//@todo 后期从数据库中读用户配置
        $config = $this->combine_config($notice_config,$user_config);

        $notice = isset($config['notice']) ? $config['notice'] : false;
        $sms = isset($config['sms']) ? $config['sms'] : false;
        $email = isset($config['email']) ? $config['email'] : false;
        $app_push = isset($config['app_push']) ? $config['app_push'] : false;

        if(!$notice && !$sms && !$email && !$app_push)//都不推送直接返回成功
        {
            return true;
        }

        $self_info = $this->user_model->select_info(array('Id','Name','UniCode'),array('UniCode'=>$self_unicode),false);
        $user_info = $this->user_model->select_info(array('Id','Email','Mobile','Name'),array('Id'=>$user_id),false);

        if(empty($self_info) || empty($user_info))
        {
            return false;
        }

        $from_user = $self_info['Name'];

        if($type == 1)//注册通知
        {
            $rlt_content = 'http://'.$_SERVER["HTTP_HOST"].'/backend/login/register?';
            $url = 'http://'.$_SERVER["HTTP_HOST"].'/backend/login/register?';
        }
        else if(in_array($type,array(2,3,4)))//项目相关
        {
            $project_info = $this->project_model->select_info(array('Title','Id'),array('UniCode'=>$rlt_id_unicode),false);
            $rlt_content = isset($project_info['Title']) ? $project_info['Title'] : '';
            $url = 'http://'.$_SERVER["HTTP_HOST"].'/backend/task/task_tree?ProjectId='.$project_info['Id'];
        }
        else if(in_array($type,array(5,6,7)))//任务相关
        {
            $task_info = $this->task_model->select_info(array('Title','Id'),array('UniCode'=>$rlt_id_unicode),false);
            $rlt_content = isset($task_info['Title']) ? $task_info['Title'] : '';
            $url = 'http://'.$_SERVER["HTTP_HOST"].'/backend/workbench';
        }
        else
        {
            return false;
        }

        if($sms)//发短信
        {
            if($type == 1)
            {
                $content = preg_replace(array('/{from_user}/','/{rlt_content}/'),array($from_user,$rlt_content.'Source=mobile'),$this->template[$type]);
            }
            else
            {
                $content = preg_replace(array('/{from_user}/','/{rlt_content}/'),array($from_user,$rlt_content),$this->template[$type]);
            }

            if(!empty($user_info['Mobile']))
            {
                common::send_sms($content,$user_info['Mobile']);
            }
        }

        if($email)//发邮件
        {
            if($type == 1)
            {
                $rlt_content .= 'Source=email';
                $url .= 'Source=email';
            }

            $content = preg_replace(array('/{from_user}/','/{rlt_content}/'),array($from_user,'<a href="'.$url.'">'.$rlt_content.'</a>'),$this->template[$type]);

            if(!empty($user_info['Email']))
            {
                common::send_email('APD系统通知',$content,$user_info['Email']);
            }
        }

        if($app_push)//发推送
        {
            $login_info = $this->phone_login_model->select_info(array('PhoneToken'),array('UserId'=>$user_id,'Status'=>enum::PhoneUserStatusOnline));
            $content = preg_replace(array('/{from_user}/','/{rlt_content}/'),array($from_user,$rlt_content),$this->template[$type]);
            if(isset($login_info['PhoneToken']) && !empty($login_info['PhoneToken']))
            {
                //消息推送
                Common::app_push_new("APD",array($login_info['PhoneToken']),"APD",$content,2,
                    array('android','ios'),array('Type'=>enum::EnumPushTaskType,'RltId'=>$rlt_id_unicode), $this->config->item('apns_production'));
            }
        }

        return true;
    }
}