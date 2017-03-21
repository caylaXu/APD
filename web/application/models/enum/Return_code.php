<?php

/**
 * @TODO 定义全局返回码
 * Class Return_code
 * @author CaylaXu <caylaxu@motouch.cn>
 */
class Return_code
{
    //通用全局返回码
    static $CommonCode = array(
        'Success' => array('Result' => 0, 'Msg' => '操作成功'),
        'Fail' => array('Result' => -1, 'Msg' => '系统繁忙'),
        'InvalidArgument'=>array('Result'=>11012,'Msg' => '参数非法')
    );

    //移动端全局返回码
    static $AppCode = array(
        'UserOffline' => array('Result' => 15000, 'Msg' => "用户未登录"),
        'PassDue' => array('Result'=> 15001,'Msg'=>'缓存过期')
    );
}

