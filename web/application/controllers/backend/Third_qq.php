<?php
/**
 * Created by PhpStorm.
 * User: Peter
 * Date: 2016/6/4
 * Time: 14:45
 */

require_once(APPPATH . "third_party/login/qq/qqConnectAPI.php");

class Third_qq extends MyController
{
    public function __construct()
    {
        $this->recorder = new Recorder();
        $this->urlUtils = new URL();
        $this->error = new ErrorCase();
    }

    public function get_user_info()
    {
        $qc = new QC();
        $arr = $qc->get_user_info();
        var_dump($arr);
        exit;
    }

}