<?php

include_once('MessageCenterConfig.php');

/**
 * Class MessageCenter
 * @author Leo Yang <leoyang@motouch.cn>
 *
 * $m = new MessageCenter('127.0.0.1', 80);
 * $m->sendSms('233333', array(13361163805));
 *
 */
class MessageCenter
{

    /**
     * @var resource
     */
    private $_socket;
    /**
     * @var string
     */
    private $_domain;
    /**
     * @var int
     */
    private $_port;


    private static $_instance;
    const TYPE_EMAIL    = 'email';
    const TYPE_SMS      = 'sms';
    const TYPE_APP_PUSH = 'app_push';
    const TYPE_APP_PUSH_MSG  = 'app_push_msg';

    /**
     * @param string $domain
     * @param int    $port
     */
    private function __construct()
    {
        if(isset(MessageCenterConfig::$CurrentHost))
        {
            $this->_domain = MessageCenterConfig::$message_center_host[MessageCenterConfig::$CurrentHost]['HOST'];
            $this->_port   = MessageCenterConfig::$message_center_host[MessageCenterConfig::$CurrentHost]['PORT'];
        }
        else
        {
            $this->_domain = MessageCenterConfig::HOST;
            $this->_port   = MessageCenterConfig::PORT;
        }

        if (defined('APPPATH'))
        {
            $this->local_log = APPPATH . '/logs/send.log';
        }
        else
        {
            $this->local_log = '/home/wwwlogs/send.log';
        }
    }

    //单例方法,用于访问实例的公共的静态方法
    public static function getInstance()
    {
        if(!(self::$_instance instanceof self))
        {
            self::$_instance = new self();
        }
        return self::$_instance;
    }



    //创建__clone方法防止对象被复制克隆
    public function __clone(){
        trigger_error('Clone is not allow!',E_USER_ERROR);
    }

    /**
     * app推送
     * @param $appkey
     * @param string $receiver
     * @param string $title
     * @param string $content
     * @param array $platform
     * @param array $params
     * @param string $m_time
     * @return int
     */
    public function appPush($appkey ,$master_secret,$receiver = 'all', $title = '',$content = '',$builder_id = 0,$platform = array('android', 'ios'), array $params, $m_time = '86400')
    {
        //Receiver,Content,Platform,MesType,MesValue,LiveTime
        return $this->send(self::TYPE_APP_PUSH, array(
            'AppKey'   => $appkey,
            'MasterSecret'  => $master_secret,
            'Receiver' => $receiver,
            'Title' => $title,
            'Content'  => $content,
            'BuilderId' => $builder_id,
            'Platform' => $platform,
            'Params'   => $params,
            'LiveTime' => $m_time,
        ));
    }

    public function appPush_new($app_type='APD',$receiver = 'all', $title = '',$content = '',$builder_id = 0,$platform = array('android', 'ios'), array $params,$apns_production=1, $m_time = '86400',$sound='default')
    {
        //Receiver,Content,Platform,MesType,MesValue,LiveTime
        return $this->send(self::TYPE_APP_PUSH, array(
            'AppType'   => $app_type,
            'Receiver' => $receiver,
            'Title' => $title,
            'Content'  => $content,
            'BuilderId' => $builder_id,
            'Platform' => $platform,
            'Params'   => $params,
            'LiveTime' => $m_time,
            'apns_production'=>$apns_production,
        	'Sound' 	=> $sound,
        ));
    }

    public function app_push_msg($app_type='Coach',$receiver = 'all', $title = '',$content = '',$builder_id = 0,array $params,$apns_production=1, $m_time = '86400')
    {
        return $this->send(self::TYPE_APP_PUSH_MSG, array(
            'AppType'   => $app_type,
            'Receiver' => $receiver,
            'Title' => $title,
            'Content'  => $content,
            'BuilderId' => $builder_id,
            'Params'   => $params,
            'LiveTime' => $m_time,
            'apns_production'=>$apns_production,
        ));
    }

    /**
     * @param $subject
     * @param $content
     * @param $addresses
     * @return int
     */
    public function sendEmail($subject, $content, $addresses)
    {
        return $this->send(self::TYPE_EMAIL, array(
            'Subject'   => $subject,
            'Content'   => $content,
            'ToAddress' =>is_array( $addresses) ?  $addresses : explode(",",  $addresses),
        ));
    }

    /**
     * @param $content
     * @param $mobiles
     * @return int
     */
    public function sendSms($content, $mobiles)
    {
        return $this->send(self::TYPE_SMS, array(
            'Content' => $content."【91恋车】",
            'Mobile'  => is_array($mobiles) ? $mobiles : explode(",", $mobiles),
        ));
    }

    /**
     * @Function: 20160229最新的发信息方法
     * @Author: MartinChen
     * @param $message_id
     * @param array $content
     * @param $mobiles
     * @return array|string
     * @throws Exception
     */
    public function new_sendSms($message_id, array $content, $mobiles)
    {
        return $this->send(self::TYPE_SMS, array(
            'MessageId' => $message_id,
            'Content' => $content,
            'Mobile'  => is_array($mobiles) ? $mobiles : explode(",", $mobiles),
        ));
    }

    /**
     * @Function:
     * @param $type
     * @param array $message
     * @return array|string
     * @throws Exception
     */
    private function send($type, array $message, $number = 1)
    {
        $message['Type'] = $type;
        $json = json_encode($message);
        $socket = $this->getSocket();
        if($socket == false)
        {
            if($number == 1)
            {
                $result = $this->send($type, $message ,2);
                return $result;
            }
            else
            {
                file_put_contents($this->local_log, date('Y-m-d H:i:s',time())."-".$json."-"."Socket 创建失败" . "\n", FILE_APPEND);
                return false;
            }
        }
        $w = socket_write($socket, $json, strlen($json));
        if ($w)
        {
        	$res = socket_read($socket, 8192 );
            $this->distory();
        	if (!$res) {
                if($number == 1)
                {
                    $result = $this->send($type, $message ,2);
                    return $result;
                }
                else
                {
                    file_put_contents($this->local_log, date('Y-m-d H:i:s',time())."-".$json."-"."Socket 读取失败" . "\n", FILE_APPEND);
                    return false;
                }
        	}
        	return $res;
        }
        else
        {
            $this->distory();
            if($number == 1)
            {
                $result = $this->send($type, $message ,2);
                return $result;
            }
            else
            {
                file_put_contents($this->local_log, date('Y-m-d H:i:s',time())."-".$json."-"."Socket 写入失败" . "\n", FILE_APPEND);
                return false;
            }
        }
    }

//    /**
//     * @return resource
//     * @throws Exception
//     */
//    private function getSocket()
//    {
//
//        if (!is_resource($this->_socket))
//        {
//            $this->_socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
//            if ($this->_socket === false)
//            {
//                return false;
//            }
//
//            socket_set_option($this->_socket, SOL_SOCKET, SO_RCVTIMEO, array('sec'=> 0, 'usec'=>100 * 1000));
//            socket_set_option($this->_socket, SOL_SOCKET, SO_SNDTIMEO, array('sec'=> 0, 'usec'=>100 * 1000));
//
//            $s = @socket_connect($this->_socket, $this->_domain, $this->_port);
//            if($s === false)
//            {
//                socket_close($this->_socket);
//                return false;
//            }
//        }
//        return $this->_socket;
//    }

    /**
     * @return resource
     * @throws Exception
     */
    private function getSocket()
    {
        $timeout = 2;//设置两秒超时
        if (!is_resource($this->_socket))
        {
            $this->_socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
            if ($this->_socket === false)
            {
                return false;
            }

            if(!socket_set_block($this->_socket))
            {
                socket_close($this->_socket);
                return false;
            }

            $time = time();
            while (!@socket_connect($this->_socket, $this->_domain, $this->_port))
            {
                $err = socket_last_error($this->_socket);
                if ($err == 115 || $err == 114)
                {
                    if ((time() - $time) >= $timeout)
                    {
                        socket_close($this->_socket);
                        return false;
                    }
                    sleep(1);
                    continue;
                }
                socket_close($this->_socket);
                return false;
            }
//            if(!socket_set_nonblock($this->_socket))
//            {
//                socket_close($this->_socket);
//                return false;
//            }
        }
        return $this->_socket;
    }

    private function distory()
    {
        if (is_resource($this->_socket))
        {
            socket_close($this->_socket);
        }
    }

    /**
     * 关闭socket连接
     */
    public function __destruct()
    {
        if (is_resource($this->_socket))
        {
            socket_close($this->_socket);
        }
    }

}

