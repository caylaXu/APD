<?php

/**
 *
 * @version 2014-8-7
 * @author fetionhe
 *         我的自定义异常处理类
 */
// ------------------------------------------------------------------------

/**
 * Exceptions Class
 *
 * @package CodeIgniter
 * @subpackage Libraries
 * @category Exceptions
 * @author ExpressionEngine Dev Team
 * @link http://codeigniter.com/user_guide/libraries/exceptions.html
 */
require_once 'Log_exception_config.php';

class HPFExceptions
{

        var $action;
        var $severity;
        var $message;
        var $filename;
        var $line;
        static public $is_send_log;
        static public $log_system_host;
        static public $port;
        static public $guid;
        static public $api_path;

        /**
         * Nesting level of the output buffering mechanism
         *
         * @var int
         * @access public
         */
        var $ob_level;

        /**
         * List if available error levels
         *
         * @var array
         * @access public
         */
        var $levels = array(
            E_ERROR           => 'Error',
            E_WARNING         => 'Warning',
            E_PARSE           => 'Parsing Error',
            E_NOTICE          => 'Notice',
            E_CORE_ERROR      => 'Core Error',
            E_CORE_WARNING    => 'Core Warning',
            E_COMPILE_ERROR   => 'Compile Error',
            E_COMPILE_WARNING => 'Compile Warning',
            E_USER_ERROR      => 'User Error',
            E_USER_WARNING    => 'User Warning',
            E_USER_NOTICE     => 'User Notice',
            E_STRICT          => 'Runtime Notice');

        /**
         * Constructor
         */
        public function __construct($options = array())
        {
                $this->ob_level        = ob_get_level();
                $log_exception_config  = new Log_exception_config();
                self::$is_send_log     = isset($options['is_send_log']) ? $options['is_send_log'] : $log_exception_config->log_exception_config['is_send_log'];
                self::$log_system_host = isset($options['log_system_host']) ? $options['log_system_host'] : $log_exception_config->log_exception_config['log_system_host'];
                self::$port            = isset($options['port']) ? $options['port'] : $log_exception_config->log_exception_config['port'];
                self::$guid            = isset($options['guid']) ? $options['guid'] : $this->create_guid();
                self::$api_path        = isset($options['api_path']) ? array_merge($options['api_path'], $log_exception_config->log_exception_config['api_path']) : $log_exception_config->log_exception_config['api_path'];
        }

        // --------------------------------------------------------------------

        /**
         * Exception Logger
         *
         * This function logs PHP generated error messages
         *
         * @access private
         * @param
         *        	string	the error severity
         * @param
         *        	string	the error string
         * @param
         *        	string	the error filepath
         * @param
         *        	string	the error line number
         * @return string
         */
        function log_exception($severity, $message, $filepath, $line)
        {
                $severity = (!isset($this->levels [$severity])) ? $severity : $this->levels [$severity];
                log_message('error', 'Severity: ' . $severity . '  --> ' . $message . ' ' . $filepath . ' ' . $line, TRUE);
        }

        // --------------------------------------------------------------------

        /**
         * Native PHP error handler
         *
         * @access private
         * @param
         *        	string	the error severity
         * @param
         *        	string	the error string
         * @param
         *        	string	the error filepath
         * @param
         *        	string	the error line number
         * @return string
         */
        function show_php_error($severity, $message, $filepath, $line)
        {
                $php_error_code = $severity;
                $severity       = (!isset($this->levels [$severity])) ? $severity : $this->levels [$severity];
                $filepath       = str_replace("\\", "/", $filepath);

                // For safety reasons we do not show the full file path
                if (FALSE !== strpos($filepath, '/'))
                {
                        $x        = explode('/', $filepath);
                        $filepath = $x [count($x) - 2] . '/' . end($x);
                }

                if (ob_get_level() > $this->ob_level + 1)
                {
                        ob_end_flush();
                }
                ob_start();
                include (dirname(__FILE__) . '/errors/error_php.php');
                $buffer = ob_get_contents();
                ob_end_clean();
                $this->send_logger($php_error_code, $buffer, $type   = 'RunTime');
                $this->output_error($buffer);
        }

        /**
         * 错误处理函数
         * @param unknown $severity
         * @param unknown $message
         * @param unknown $filepath
         * @param unknown $line
         */
        public function error_handler($severity, $message, $filepath, $line)
        {
                // We don't bother with "strict" notices since they tend to fill up
                // the log file with excess information that isn't normally very helpful.
                // For example, if you are running PHP 5 and you use version 4 style
                // class functions (without prefixes like "public", "private", etc.)
                // you'll get notices telling you that these have been deprecated.
                if ($severity == E_STRICT)
                {
                        // return;
                }
                // Should we display the error? We'll get the current error_reporting
                // level and add its bits with the severity bits to find out.
                if (($severity & error_reporting()) == $severity)
                {
                        $this->show_php_error($severity, $message, $filepath, $line);
                }

                // Should we log the error?  No?  We're done...
                if (config_item('log_threshold') == 0)
                {
                        return;
                }
                $this->log_exception($severity, $message, $filepath, $line);
        }

        /**
         * shutdown 处理
         */
        public function shutdown_handler()
        {
                $error  = error_get_last();
                //获得当前缓存区内容,包括echo print vardump 等输出, 并清除当前缓冲区
                $buffer = ob_get_clean();
                if (strpos($buffer, 'Database Error'))
                {


                        $this->send_logger($level          = 1, $buffer, $type           = 'sql', $custom_context = '数据库错误');

                        $this->output_error($buffer);
                }
                else if (strpos($buffer, 'socket_'))
                {
                        log_message('error', 'sock is no working,message is ' . $buffer);
                        $this->output_error($buffer);
                }
                else if ($error)
                {
                        /*
                         * 如果发送函数发送udp报错 则不做显示
                         */
                        if (strpos($error['message'], 'socket_') === false && strpos($error['file'], 'HPFExceptions') === false)
                        {
                                $error_message = 'Fatal error: ' . $error ['message'] . ' in ' . $error ['file'] . ' on line ' . $error ['line'];
                                $this->send_logger($error['type'], $error_message, $type          = 'Runtime');
                        }
                        $this->output_error($buffer);
                }
                else
                {
                        $this->output_error($buffer);
                }
        }

        /**
         * @todo 发送消息到日志服务器上
         * @param string $message 可以是数组，可以是字符串
         * @param mix $level  php标准错误码 或者自定义在错误级别
         * @param string $type 消息类型
         * @param sting $custom_context 用户自定义错误
         */
        static function send_logger($level = 3, $message, $type = 'Runtime', $custom_context = '')
        {
                if (self::$is_send_log === false )
                {
                        return;
                }
                $level                     = self::php_level_to_log_level($level);
                $context['REQUEST_URI']    = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '未知';
                $context['request_method'] = isset($_SERVER['REQUEST_METHOD']) ? $_SERVER['REQUEST_METHOD'] : '未知';
                $context['content']        = $custom_context;
                $context['get']            = $_GET;
                $context['post']           = $_POST;
                $context['other']          = file_get_contents("php://input");
                $context                   = array_filter($context);
                $log                       = array(
                    'SystemName' => isset($_SERVER ['HTTP_HOST']) ? $_SERVER ['HTTP_HOST'] : 'I dont know',
                    'Level'      => $level,
                    'Guid'       => self::$guid,
                    'Time'       => time(),
                    'Context'    => $context,
                    'Message'    => $message,
                    'Type'       => $type,
                );
                $sock                      = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP);
                $str_log                   = json_encode($log);
                $len                       = strlen($str_log);
                $res                       = socket_sendto($sock, $str_log, $len, 0, self::$log_system_host, self::$port);
//                var_dump($res);
                socket_close($sock);
        }

        public function output_error($buffer)
        {

                //一般生产环境 这里都是off
                if (ini_get('display_errors') == 'Off')
                {
                        foreach (self::$api_path as $v)
                        {
                                if (strpos($_SERVER['REQUEST_URI'], $v)!=false)
                                {
                                        $json_decode_buffe = json_decode($buffer, true);
                                        if (isset($json_decode_buffe['result']))
                                        {
                                                echo $buffer;
                                        }
                                        else
                                        {
                                                $reslut['result'] = 99999;
                                                $reslut['msg']    = "网络异常";
                                                echo json_encode($reslut);
                                        }
                                }
                        }
                }
                else
                {
                           echo $buffer;
                }
        }

        static function php_level_to_log_level($level)
        {
                switch ($level)
                {
                        case 'error':
                        case E_ERROR:
                        case E_PARSE:
                        case E_CORE_ERROR:
                        case E_COMPILE_ERROR:
                        case E_USER_ERROR:
                        case E_RECOVERABLE_ERROR:
                                $log_level = 5;
                                break;
                        case 'warning':
                        case E_WARNING:
                        case E_CORE_WARNING:
                        case E_COMPILE_WARNING:
                        case E_USER_WARNING:
                                $log_level = 4;
                                break;
                        case 'notice':
                        case E_NOTICE:
                        case E_USER_NOTICE:
                                $log_level = 3;
                                break;
                        case E_STRICT:
                                $log_level = 3;
                                break;
                        case 'info':
                                $log_level = 2;
                                break;
                        case 'debug':
                                $log_level = 1;
                                break;
                        case 'critical':
                                $log_level = 6;
                                break;
                        case 'alert':
                                $log_level = 7;
                                break;
                        case 'emergency':
                                $log_level = 8;
                                break;
                        default :
                                $log_level = 1;
                }
                return $log_level;
        }

        /**
         * 异常处理
         * 
         * @param Exception $exception        	
         */
        public function exception_handler($exception)
        {
                $line          = $exception->getLine();
                $code          = $exception->getCode();
                $message       = $exception->getMessage();
                $file          = $exception->getFile();
                $error_message = 'exception error: ' . $message . ' in ' . $file . ' on line ' . $line . '. Code is ' . $code;
                self::send_logger('critical', $error_message, $type          = 'Runtime');
                $this->output_error($error_message);
        }

        /** 生成guid
         * @return string
         */
        function create_guid()
        {
                $charid = strtoupper(md5(uniqid(mt_rand(), true)));
                $hyphen = chr(45); // "-"
                $uuid   = substr($charid, 0, 8) . $hyphen . substr($charid, 8, 4) . $hyphen . substr($charid, 12, 4) . $hyphen . substr($charid, 16, 4) . $hyphen . substr($charid, 20, 12); // "}"
                return $uuid;
        }

}

// END Exceptions Class

/* End of file Exceptions.php */
/* Location: ./system/core/Exceptions.php */
