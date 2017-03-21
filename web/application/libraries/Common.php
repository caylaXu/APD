<?php
set_time_limit(30);
/**
 * Created by PhpStorm.
 * User: cayla
 * Date: 2015/11/8
 * Time: 17:43
 */
require_once (APPPATH . "/third_party/messagecenter/MessageCenter.php");
class Common
{
    static function return_json($result=0,$msg='success', $data=NULL,$exit = false)
    {

        $json['Result'] = $result ; //0代表成功
        $json['Msg'] =  $msg ; //0代表成功
        $json['Data'] =  $data; //0代表成功
        echo json_encode($json);
        if ($exit)
        {
            exit;
        }
    }

    static function check_get_post($items = array(), $type = 'post', $is_top_level = true,$array = null)
    {
        switch ($type)
        {
            case 'post':
                $array = $_POST;
                break;
            case 'get':
                $array = $_GET;
                break;
            case 'all':
                $array = $_POST + $_GET;
                break;
            case 'other':
                $array;
                break;
            default:
                $array = $_POST;
                break;
        }

        if ($is_top_level)
        {
            if (count($items) != count($array))
            {
                return false;
            }
        }

        $result = array();

        foreach ($items as $key => $item)
        {
            if (isset($array[$key]))
            {
                $result[$key] = $array[$key];
                if ($item)
                {
                    if (strlen($array[$key]) < 1)
                    {
                        return false;
                    }
                }
            }
            else
            {
                return false;
            }
        }
        return $result;
    }

    static function check_params($items = array(), $array = null,$is_top_level = true)
    {
        if ($is_top_level)
        {
            if (count($items) != count($array))
            {
                return false;
            }
        }

        foreach ($items as $key => $item)
        {
            if (isset($array[$key]))
            {
                if ($item)
                {
                    if (strlen($array[$key]) < 1)
                    {
                        return false;
                    }
                }
            }
            else
            {
                return false;
            }
        }
        return $array;
    }

    /**
     * 检查密码是否正确
     * @param $coach_info
     * @param $password
     * @return bool
     */
    static function check_password($info,$password)
    {
        $info = is_array($info) ? $info : get_object_vars($info);
        $md5_password = $info['Password'];
        $Salt = $info['Salt'];
        $md5_salt = self::hash($password, $Salt);

        if(strcmp($md5_salt['hash'], $md5_password) == 0)
        {
            return true;
        }
        return false;
    }

    static public  function hash($str, $salt = NUll)
    {
        $md5_slt['salt'] = !empty($salt) ? $salt : rand(1, 1000000); //定义一个salt值，随机数字
        $str_slt         = $str . $md5_slt['salt'];  //把密码和salt连接
        $md5_slt['hash'] = md5($str_slt);  //执行MD5散列
        return $md5_slt;  //返回散列以及盐
    }

    /**
     * @function 生成会话id
     * @author CaylaXu
     * 规则：时间戳+用户id+三位随机数
     * @param $user_id
     *
     * @return string
     */
    static function generate_session_id($user_id,$user_type)
    {
        return $user_id.$user_type.mt_rand(1000,9999);
    }


    static function is_timestamp($timestamp)
    {
        return ctype_digit($timestamp) && $timestamp <= 2147483647;
//        if(strtotime(date('m-d-Y H:i:s',$timestamp)) === $timestamp) {
//            return $timestamp;
//        } else return false;
    }

    /**
     * 校验手机号
     * @date: 2014-11-26
     * @author: caylaxu
     * @return:
     */
    static function is_mobile($str)
    {
        return preg_match("/^13[0-9]{1}[0-9]{8}$|146[0-9]{8}$|147[0-9]{8}$|15[0-9]{1}[0-9]{8}$|17[0-9]{1}[0-9]{8}$|18[0-9]{9}$/", $str);
    }

    public static function is_email($str)
    {
        return preg_match('/^([a-z0-9]*[-_]?[a-z0-9]+)*@([a-z0-9]*[-_]?[a-z0-9]+)+[\.][a-z]{2,3}([\.][a-z]{2})?$/', $str);
    }

    static  function create_uuid($prefix = "01")
    {
         $str = $prefix.time().mt_rand(9,100);
         return $str;
    //可以指定前缀
//        $str = md5(uniqid(mt_rand(), true));
//        $uuid = substr($str, 0, 8).'-';
//        $uuid .= substr($str, 8, 4).'-';
//        $uuid .= substr($str, 12, 4).'-';
//        $uuid .= substr($str, 16, 4).'-';
//        $uuid .= substr($str, 20, 12);
//        return $prefix.$uuid;
    }

    static function sortArrayAsc($preData,$sortType='Modified'){
        $sortData = array();
        foreach ($preData as $key_i => $value_i){
            $price_i = $value_i[$sortType];
            $min_key = '';
            $sort_total = count($sortData);
            foreach ($sortData as $key_j => $value_j){
                if($price_i<$value_j[$sortType]){
                    $min_key = $key_j+1;
                    break;
                }
            }
            if(empty($min_key)){
                array_push($sortData, $value_i);
            }else {
                $sortData1 = array_slice($sortData, 0,$min_key-1);
                array_push($sortData1, $value_i);
                if(($min_key-1)<$sort_total){
                    $sortData2 = array_slice($sortData, $min_key-1);
                    foreach ($sortData2 as $value){
                        array_push($sortData1, $value);
                    }
                }
                $sortData = $sortData1;
            }
        }
        return $sortData;
    }

    /**
     * @function 取中英文首字母
     * @User: CaylaXu
     * @param $s0
     * @return string
     */
    static function get_first_char($s0)
    {
        if(is_numeric($s0[0]))
        {
            return 'default';
        }

        if($s0[0]=='I' || $s0[0]=='i')
        {
            return "i";
        }
        elseif($s0[0]=='U' || $s0[0]=='u')
        {
            return 'u';
        }
        elseif($s0[0]=='V' || $s0[0]=='v')
        {
            return 'v';
        }
        else
        {
            $fchar = ord($s0{0});
            if($fchar >= ord("A") and $fchar <= ord("z") )return strtolower($s0{0});
            $s1 = iconv("UTF-8","gb2312", $s0);
            $s2 = iconv("gb2312","UTF-8", $s1);
            if($s2 == $s0){$s = $s1;}else{$s = $s0;}
            $asc = ord($s{0}) * 256 + ord($s{1}) - 65536;
            if($asc >= -20319 and $asc <= -20284) return "a";
            if($asc >= -20283 and $asc <= -19776) return "b";
            if($asc >= -19775 and $asc <= -19219) return "c";
            if($asc >= -19218 and $asc <= -18711) return "d";
            if($asc >= -18710 and $asc <= -18527) return "e";
            if($asc >= -18526 and $asc <= -18240) return "f";
            if($asc >= -18239 and $asc <= -17923) return "g";
            if($asc >= -17922 and $asc <= -17418) return "h";
            if($asc >= -17417 and $asc <= -16475) return "j";
            if($asc >= -16474 and $asc <= -16213) return "k";
            if($asc >= -16212 and $asc <= -15641) return "l";
            if($asc >= -15640 and $asc <= -15166) return "m";
            if($asc >= -15165 and $asc <= -14923) return "n";
            if($asc >= -14922 and $asc <= -14915) return "o";
            if($asc >= -14914 and $asc <= -14631) return "p";
            if($asc >= -14630 and $asc <= -14150) return "q";
            if($asc >= -14149 and $asc <= -14091) return "r";
            if($asc >= -14090 and $asc <= -13319) return "s";
            if($asc >= -13318 and $asc <= -12839) return "t";
            if($asc >= -12838 and $asc <= -12557) return "w";
            if($asc >= -12556 and $asc <= -11848) return "x";
            if($asc >= -11847 and $asc <= -11056) return "y";
            if($asc >= -11055 and $asc <= -10247) return "z";
            return 'default';
        }
    }


    /**
     * @function 计算进度
     * @User: CaylaXu
     * @param $array 树形数组
     * @return float
     */
    static function get_progress($array)
    {
        if(!isset($array['Child']) || empty($array['Child']))
        {
            if(isset($array['CompleteProgress']))
            {
                $progress =  round($array['CompleteProgress']);
            }
            else
            {
                return 0;
            }
        }
        else
        {
            $denominator = count($array['Child']);
            $molecule = 0;

            foreach($array['Child'] as $k => $v)
            {
                $molecule += self::get_progress($v);
            }

            $progress = round($molecule/$denominator);
        }
        return $progress;
    }

    /**
     * @function 生成树
     * @User: CaylaXu
     * @param $cate
     * @param int $id
     * @param int $level
     * @return array
     */
    static function generate_tree($cate,$id=0,$level=0){//一棵树形成一个数组
        $arr=array();
        $return = array();
        foreach($cate as $v){
            if($v['ParentId'] == $id)
            {
                $v['Child'] = self::generate_tree($cate,$v['Id'],1);
                if(empty($v['Child']))
                {
                    array_pop($v);
                }
                $arr[] = $v;
            }

            if($level == 0 && $v['Id'] == $id)
            {
                $return = $v;
            }
        }

        if($level == 0)
        {
            $return['Child'] = $arr;
            return $return;
        }
        return $arr;
    }

    /**
     * @function 获取子集id
     * @User: CaylaXu
     * @param $cate
     * @param int $id
     * @param array $arr
     * @param int $level
     * @return array
     */
    static function get_child_id($cate,$id=0,$arr = array(),$level = 0)
    {
        static $arr = array();

        if($level == 0)
        {
            $arr = array();
        }

        foreach($cate as $v)
        {
            if($v['ParentId'] == $id)
            {
                $arr[] = $v['Id'];
                self::get_child_id($cate,$v['Id'],$arr,1);
            }
        }
        return $arr;
    }

    /**
     * @function
     * @User: CaylaXu
     * @param string $scene 使用场景avatar头像，common公共资源
     * @param string $name
     * @param string $type 资源类型picture图片，video视频，file文件
     * @return string
     */
    static function resources_full_path($scene='',$name='',$type = 'picture')
    {
        $CI =& get_instance();
        $CI->config->load('upload_remote_servers', TRUE, TRUE);
        $config = $CI->config->item('upload_remote_servers');
        $path = '';
        //读取远程服务器
        if (isset($config['is_remote_upload']) && $config['is_remote_upload'] == TRUE)
        {
            switch ($type)
            {
                case 'picture':
                    $path .= $config['pic_cdn_url'];
                    break;
                case 'video':
                    $path .= $config['video_cdn_url'];
                    break;
                case 'file':
                    $path .= $config['file_cdn_url'];
                    break;
                default:
                    $path .= $config['cdn_url'];
                    break;
            }

            //如果不是公共前端资源
            if($scene != "common_js")
            {
                $path .= '/'.$_SERVER["HTTP_HOST"];
            }
        }
        else
        {
                $path.= 'http://'.$_SERVER["HTTP_HOST"].'/resource';
        }

        switch ($scene)
        {
            case 'avatar':
                $path .= '/upload/avatar/';
                break;
            case 'video':
                $path .= '/resource/upload/resource/';
                break;
            case 'common_js':
                $path .= '/common';
                break;
            case 'theme':
                $path .= '/upload/theme/';
                break;
            case 'thumb':
                $path .= '/upload/theme/app/';
                break;
            default:
                $path .= '';
                break;
        }
        $path .= $name;
        return $path;
    }

    /**
     * @function 时间转换
     * @User: CaylaXu
     */
    static function time_cycle($time)
    {
        if(empty($time))
        {
            return "无";
        }

        $today_day = date("Ymd",time());//
        $today_year = date("Y",time());
        //1、是当天则显示时间
        if(date("Ymd",$time) == $today_day)
        {
            return date("H:i",$time);
        }
        //2、是当年则显示月日时间
        if(date("Y",$time) == $today_year)
        {
            //获取今天凌晨的时间戳
            $day = strtotime(date('Y-m-d',time()));
            //获取昨天凌晨的时间戳
            $pday = strtotime(date('Y-m-d',strtotime('-1 day')));
            //获取前天凌晨的时间戳
            $ppday = strtotime(date('Y-m-d',strtotime('-2 day')));
            //获取明天凌晨的时间戳
            $tday = strtotime(date('Y-m-d',strtotime('+1 day')));
            //获取后天凌晨的时间戳
            $ttday = strtotime(date('Y-m-d',strtotime('+2 day')));
            //获取大后天凌晨的时间戳
            $tttday = strtotime(date('Y-m-d',strtotime('+3 day')));
            if($time >= $ppday && $time < $pday)
            {
                return "前天 ".date("H:i",$time);
            }
            else if($time >= $pday && $time < $day)
            {
                return "昨天 ".date("H:i",$time);
            }
            else if($time >= $tday && $time < $ttday)
            {
                return "明天 ".date("H:i",$time);
            }
            else if($time >= $ttday && $time < $tttday)
            {
                return "后天 ".date("H:i",$time);
            }
            else
            {
                return date("m/d H:i",$time);
            }
        }

        return date("Y/m/d H:i",$time);
    }

    /**
     * @function 检查字符串长度
     * @User: CaylaXu
     * @param $string
     * @param $length
     */
    static function check_string_length ($min, $max, $string)
    {
//        $gb_name = iconv("UTF-8", "GB2312//IGNORE", $string);
        $string = trim($string);
        $length = strlen($string);
        if ($length >= $min && $length <= $max)
        {
            return TRUE;
        }
        else
        {
            return FALSE;
        }
    }


    /**
     * @function 图片生成唯一名称
     * @User: CaylaXu
     * @return string
     */
    static function create_new_guid()
    {
        $charid = strtoupper(md5(uniqid(mt_rand(), true)));
        $hyphen = chr(45);
        $uuid   = substr($charid, 0, 8) . $hyphen . substr($charid, 8, 4) . $hyphen . substr($charid, 12, 4) . $hyphen . substr($charid, 16, 4) . $hyphen . substr($charid, 20, 12); // "}"
        return $uuid;
    }

    /**
     * @function 返回资源相对路径
     * @author CaylaXu
     * @param string $type 资源类别
     * @param string $name 资源名称
     * @param bool $is_remote 是否远程资源
     * @return string
     */
    static function resources_relative_path($scene='',$name='',$is_remote = FALSE)
    {
        $path = '';

        if($is_remote == TRUE)
        {
            $path = $_SERVER["HTTP_HOST"].'/';
        }
        else
        {
            $path = 'resource/';
        }

        switch ($scene)
        {
            case 'avatar':
                $path .= 'upload/avatar/';
                break;
            case 'thumb':
                $path .= 'upload/theme/app/thumb/';
            default:
                $path .= '';
                break;
        }

        $path .= $name;
        return $path;
    }

    /**
     * @function 发送邮件
     * @User: CaylaXu
     * @param $subject
     * @param $content
     * @param $email
     * @return int
     */
    static function send_email($subject, $content, $email)
    {
        $MessageCenter = MessageCenter::getInstance();
        $res           = $MessageCenter->sendEmail($subject, $content, $email);
        return $res;
    }

    /**
     * @function 发送短信
     * @User: CaylaXu
     * @param $content
     * @param $mobiles
     * @return int
     */
    static function send_sms($content, $mobiles)
    {
        $MessageCenter = MessageCenter::getInstance();
        $msg           = $content;
        $res           = $MessageCenter->sendSms($msg, $mobiles);
        return $res;
    }

    static function app_push_new($app_type='APD',$receiver = 'all', $title = '', $content = '',$builder_id = 0,$platform = array('android', 'ios'),array $params = array(),$apns_production = 1,$m_time = '86400',$sound='default')
    {
        $MessageCenter = MessageCenter::getInstance();
        $res           = $MessageCenter->appPush_new($app_type,$receiver, $title, $content,$builder_id,$platform,$params,$apns_production,$m_time,$sound);
        return $res;
    }

    /**
     * @function 返回两个时间戳相差的天数
     * @User: CaylaXu
     * @param $start 开始日期
     * @param $end 截止日期
     */
    static function count_days($start,$end)
    {
        $start = intval($start);

        if($start == 0 || $start>=$end)
        {
            return 0;
        }
        $date_1=date('Y-m-d',$start);
        $date_2=date('Y-m-d',$end);
        $d1=strtotime($date_1);
        $d2=strtotime($date_2);
        $days=round(($d2-$d1)/3600/24);
        return $days;
    }

    public static function get_random_code()
    {
        $chars     = '0123456789';
        mt_srand((double) microtime() * 1000000 * getmypid()); // 根据PHP的进程pid * (double)microtime() 保证微妙级不重复
        // mt_srand( 微妙级不重复的数字 ) 播下一个更好的随机数发生器种子(指定随机数不动了)
        $CheckCode = "";
        while (strlen($CheckCode) < 4)
            $CheckCode .= substr($chars, (mt_rand() % strlen($chars)), 1); // 生成4位数字
        return $CheckCode;
    }

    /**
     * @function 获取图片信息
     * @User: CaylaXu
     * @param $img
     * @return array|bool
     */
    public static function getImageInfo($img) {
        $imageInfo = getimagesize($img);
        if( $imageInfo!== false) {
            $imageType = strtolower(substr(image_type_to_extension($imageInfo[2]),1));
            $imageSize = filesize($img);
            $info = array(
                "width"		=>$imageInfo[0],
                "height"	=>$imageInfo[1],
                "type"		=>$imageType,
                "size"		=>$imageSize,
                "mime"		=>$imageInfo['mime'],
            );
            return $info;
        }else {
            return false;
        }
    }

    /**
     * @function 裁剪缩略图
     * @User: CaylaXu
     * @param $image 原图路径 /upload/test.jpg
     * @param $thumb 目标路径 /upload/thumb.jpg
     * @param $x 裁剪位置x坐标
     * @param $y 裁剪位置y坐标
     * @param $w 裁剪宽度
     * @param $h 裁剪高度
     * @return bool 失败返回false
     */
    public static function thumb($image, $thumb , $x, $y, $w, $h){
        $is_save=true;
        // 获取原图信息
        $maxWidth=500;
        $maxHeight=500;
        $interlace=true;
        $info  = self::getImageInfo($image);
        if($info !== false) {
            $width  = $info['width'];
            $height = $info['height'];
            $type = $info['type'];
            $type = strtolower($type);
            $interlace  =  $interlace? 1:0;
            unset($info);
            // 载入原图
            $createFun = 'ImageCreateFrom'.($type=='jpg'?'jpeg':$type);
            $srcImg     = $createFun($image);

            //创建缩略图
            if($type!='gif' && function_exists('imagecreatetruecolor'))
                $thumbImg = imagecreatetruecolor($width, $height);
            else
                $thumbImg = imagecreate($width, $height);
            // 复制图片
            if(function_exists("ImageCopyResampled"))
                imagecopyresampled($thumbImg, $srcImg, 0, 0, 0, 0, $width, $height, $width,$height);
            else
                imagecopyresized($thumbImg, $srcImg, 0, 0, 0, 0, $width, $height,  $width,$height);
            if('gif'==$type || 'png'==$type) {
                $background_color  =  imagecolorallocate($thumbImg,  0,255,0);  //  指派一个绿色
                imagecolortransparent($thumbImg,$background_color);  //  设置为透明色，若注释掉该行则输出绿色的图
            }
            // 对jpeg图形设置隔行扫描
            if('jpg'==$type || 'jpeg'==$type) 	imageinterlace($thumbImg,$interlace);
            // 生成图片
            $imageFun = 'image'.($type=='jpg'?'jpeg':$type);
            //裁剪
            $level = 100;
            if('png'==$type) $level=9;
            $imageFun($thumbImg,$thumb,$level);
            $thumbImg01 = imagecreatetruecolor(190,195);
            imagecopyresampled($thumbImg01,$thumbImg,0,0,$x,$y,190,195,$w,$h);
            $imageFun($thumbImg01,$thumb,$level);
            imagedestroy($thumbImg01);
            imagedestroy($thumbImg);
            imagedestroy($srcImg);
            return $thumb;//返回图片路径
        }
        return false;
    }

    /**
     * @function 判断是否色值
     * @author Peter
     * @param $str
     * @return bool
     */
    public static function is_color($str)
    {
        if (strlen($str) != 7 && strlen($str) != 4)
        {
            return FALSE;
        }
        if (strpos($str, '#') !== 0)
        {
            return FALSE;
        }

        $chars = str_split(substr($str, 1), 1);
        foreach($chars as $char)
        {
            if ($char < '0' || $char > 'f')
            {
                return FALSE;
            }
        }

        return TRUE;
    }

    static function put_file_from_url_content($url, $saveName, $path) {
        // 设置运行时间为无限制
        set_time_limit ( 0 );
        $url = trim ( $url );
        $curl = curl_init ();
        // 设置你需要抓取的URL
        curl_setopt ( $curl, CURLOPT_URL, $url );
        // 设置header
        curl_setopt ( $curl, CURLOPT_HEADER, 0 );
        // 设置cURL 参数，要求结果保存到字符串中还是输出到屏幕上。
        curl_setopt ( $curl, CURLOPT_RETURNTRANSFER, 1 );
        // 运行cURL，请求网页
        $file = curl_exec ( $curl );
        // 关闭URL请求
        curl_close ( $curl );
        // 将文件写入获得的数据
        $filename = $path . $saveName;
        $write = @fopen ( $filename, "w" );
        if ($write == false) {
            return false;
        }
        if (fwrite ( $write, $file ) == false) {
            return false;
        }
        if (fclose ( $write ) == false) {
            return false;
        }
        return true;
    }

    /**
     * @function base64转图片存放到指定位置
     * @author CaylaXu
     *
     * @param $image_data
     * @param $full_file_path_name
     */
    static function save_base64_jpeg_data_to_files($image_data, $full_file_path_name)
    {
        $find    = array("data:image/png;base64,", "data:image/jpeg;base64,", " ");
        $replace = array("", "", "+");
        $image   = base64_decode(str_replace($find, $replace, $image_data));
        return file_put_contents($full_file_path_name, $image);
    }
}