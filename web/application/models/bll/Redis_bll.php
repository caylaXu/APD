<?php
defined('BASEPATH') OR exit('No direct script access allowed');

    /**
     * Class redis_bll
     * @author CaylaXu <caylaxu@motouch.cn>
     */
    class Redis_bll extends CI_Model
    {

        function __construct()
        {
            parent::__construct();
            $this->load->library('scz_predis');
        }

        /**
         * @function 判断验证码是否正确
         * @author CaylaXu
         *
         * @param $mobile
         * @param $random
         *
         * @return bool
         */
        function is_mobile_random_right($mobile, $random)
        {
            $key = 'coach_random' . $mobile;
            $random_code = scz_predis::$predis->get($key);
            if($random === $random_code)
            {
                return true;
            }

            return false;
        }


        /**
         * @function 获取无序集合的所有值
         * @author CaylaXu
         *
         * @param $key
         *
         * @return mixed
         */
        function my_sMembers($key)
        {
            $res = scz_predis::$predis->smembers($key);
            return $res;
        }

        /**
         * @function 无序集合添加值
         * @author CaylaXu
         * @param $key
         * @param $value
         * @return mixed
         */
        function my_aAdd($key, $value)
        {
            $res = scz_predis::$predis->sadd($key, $value);
            return $res;
        }

        /**
         * @function 获取键对应的值
         * @author CaylaXu
         *
         * @param $key
         *
         * @return mixed
         */
        function my_get($key)
        {
            $res = scz_predis::$predis->get($key);
            return $res;
        }

        /**
         * 设置键值对
         *
         * @param $key
         * @param $value
         * @param int $timeout
         * @param string $prefix
         *
         * @return bool
         */
        function my_set($key, $value, $timeout = 0, $prefix = '')
        {
            if($timeout == 0)
            {
                $res = scz_predis::$predis->set($prefix . $key, $value);
            }
            else
            {
                $res = scz_predis::$predis->setex($prefix . $key, $timeout , $value);
            }
            return $res;
        }

        /**
         * 删除指定的$key
         * @param $key
         */
        function my_delete($key)
        {
            return scz_predis::$predis->del($key);
        }

        /**
         * @function 判断key值是否存在
         * @author CaylaXu
         * @param array|int $key
         * @return mixed
         */
        function exists($key)
        {
            return scz_predis::$predis->exists($key);
        }

        /**
         * @function 值自增
         * @author CaylaXu
         * @param $key
         * @return mixed
         */
        function incr($key)
        {
            return scz_predis::$predis->incr($key);
        }

        /**
         * 用于为哈希表中的字段赋值
         *
         * @param $key
         * @param $field
         * @param $value
         *
         * @return int
         */
        function my_hSet($key, $field, $value)
        {
            $res = scz_predis::$predis->hset($key, $field, $value);
            return $res;
        }

        /**
         * 用于获取哈希表中的字段值
         *
         * @param $key
         * @param array $fields
         *
         * @return array
         */
        function my_hmGet($key, array $fields)
        {
            $res = scz_predis::$predis->hmget($key, $fields);
            return $res;
        }

        function my_hGet($key,$fields)
        {
            $res = scz_predis::$predis->hget($key, $fields);
            return $res;
        }

        /**
         *TODO:出队列
         *Author: MartinChen
         * @param $key
         * @return mixed
         */
        function my_rpop($key)
        {
            $res = scz_predis::$predis->rpop($key);
            return $res;
        }

        /**
         *TODO:入队
         *Author: MartinChen
         * @param $key
         * @param $value
         * @return mixed
         */
        function my_rpush($key,$value)
        {
            $res = scz_predis::$predis->rpush($key , $value);
            return $res;
        }

        /**
         * @function 验证手机号和验证码
         * @User: CaylaXu
         * @param $mobile
         * @param $verif
         * @param string $prefix
         * @return bool
         */
        function my_check_mobile_and_verif($mobile, $verif, $prefix = '')
        {
            $key = $prefix . $mobile;
            $s_verif = $this->my_get($key);
            if($verif != $s_verif)
            {
                return false;
            }
            else
            {
                return true;
            }
        }


        /**
         * @function 写入hash值
         * @author CaylaXu
         *
         * @param $key
         * @param $field
         * @param $value
         *
         * @return mixed
         */
        function hset($key, $field, $value)
        {
            $res = scz_predis::$predis->hset($key, $field, $value);
            return $res;
        }

        /**
         * @function 获取hash值
         * @author CaylaXu
         *
         * @param $key
         * @param $field
         *
         * @return mixed
         */
        function hget($key, $field)
        {
            $res = scz_predis::$predis->hget($key, $field);
            return $res;
        }


        /**
         * @function
         * @author CaylaXu
         *
         * @param $custom_session_id
         * @param string $type
         *
         * @return mixed
         */
        function set_online_session($custom_session_id, $type = 'apd')
        {
            $type = strtolower($type);
            $key = 'online:'.$type.':session';
            $value = strtotime("+1 week");
            return scz_predis::$predis->hset($key,$custom_session_id,$value);
        }

        /**
         * @function 获取在线的时间戳过期时间
         * @author CaylaXu
         *
         * @param $custom_session_id
         * @param string $type
         *
         * @return mixed
         */
        function get_online_session($custom_session_id, $type = 'apd')
        {
            $type = strtolower($type);
            $key = 'online:'.$type.':session';
            return scz_predis::$predis->hget($key,$custom_session_id);
        }

        /**
         * @function 设置用户下线
         * @author CaylaXu
         *
         * @param $custom_session_id
         * @param string $type
         *
         * @return mixed
         */
        function set_offline_session($custom_session_id, $type = 'apd')
        {
            $type = strtolower($type);
            $key = 'online:'.$type.':session';
            return scz_predis::$predis->hdel($key,$custom_session_id);
        }
}
