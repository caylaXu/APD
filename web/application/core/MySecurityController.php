<?php
class MySecurityController extends CI_Controller
{
        private $NativePost;//post原始数据
        private $NativeGet;//get原始数据
        public function __construct()
        {
                parent::__construct();
                $this->post_get_xss_clean();
        }

        /**
         * @function xss过滤
         * @User: CaylaXu
         */
        function post_get_xss_clean()
        {
                $this->NativePost = $_POST;
                $this->NativeGet = $_GET;
                if($this->config->item('enable_xss') == TRUE)
                {
                        $_POST = $this->cleanArray($_POST);
                        $_GET = $this->cleanArray($_GET);
                }
        }

        function cleanArray($array)
        {
                if(empty($array))
                {
                        return $array;
                }
                foreach($array as $key=>$value)
                {
                        if(is_array($value))
                        {
                                $array[$key] = $this->cleanArray($value);
                        }
                        else
                        {
                                $html_clean = htmlspecialchars($value,ENT_QUOTES);//html过滤
                                $array[$key] = $this->security->xss_clean($html_clean);//js过滤
                        }
                }
                return $array;
        }
}
