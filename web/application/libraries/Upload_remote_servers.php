<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of upload_remote_servers
 *
 * @package		CodeIgniter
 * @subpackage  	Libraries
 * @author SunnySun <Sunchangzhi, 331942828@qq.com> 
 * @date   16:56:57
 */
//define('LOCAL_IMG_PATH', 'F:\test_img\\');
//define('CDN_POST_URL', 'http://studycar.me/sync.php');
if (!function_exists('curl_file_create'))
{

        function curl_file_create($filename, $mimetype = '', $postname = '')
        {
                return "@$filename;filename="
                        . ($postname ? : basename($filename))
                        . ($mimetype ? ";type=$mimetype" : '');
        }

}

class Upload_remote_servers
{
        //远程api地址 
        private $remote_resource_api_url;
        private $is_remote_upload;
        /**
         *  用CURL发送资源更新请求到CDN对应接口
         *  $url CND资源更新接口地址
         *  $dir 需要存放到CND根目录下面对应的目录文件夹
         *  $img_path 需要上传的本地图片真实路径
         */
        
        function __construct($param)
        {
                $this->remote_resource_api_url=isset($param['remote_resource_api_url'])?$param['remote_resource_api_url']:'http://studycar.me/sync.php';              
                $this->is_remote_upload=isset($param['remote_resource_api_url'])?$param['is_remote_upload']:false;
        }
        /**
        *  用CURL发送资源更新请求到CDN对应接口
        *  $url CND资源更新接口地址
        *  $dir 需要存放到CND根目录下面对应的目录文件夹
        *  $img_path 需要上传的本地图片真实路径
       */
        function curl_img($cdn_storage_dir, $local_img_path,$is_delete_loacal_img=false)
        {
                
                if($this->is_remote_upload===false)
                {
                        return false;
                }
                $post_data = array(
                    "dir" => $cdn_storage_dir,
                    "img" => curl_file_create($local_img_path),
                );
               // print_r($post_data);
              //  print_r($this->remote_resource_api_url);exit;
                $curl   = curl_init();
                curl_setopt($curl, CURLOPT_URL, $this->remote_resource_api_url);
                curl_setopt($curl, CURLOPT_POST, 1);
                curl_setopt($curl, CURLOPT_POSTFIELDS, $post_data);
                curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 0);
                curl_setopt($curl, CURLOPT_TIMEOUT, 300);    // 超时
                //curl_setopt($curl, CURLOPT_USERAGENT, "Mozilla/4.0");
                $result = curl_exec($curl);
                $error  = curl_error($curl);
               // print_r($error);
                //print_r($result);exit;
                curl_close($curl);
                if($error)
                {
                       return  $error;
                }
                else
                {
                       if($is_delete_loacal_img==true)
                       {
                                @unlink($local_img_path);
                       }
                }
                return  $result;
        }

        //put your code here
}
