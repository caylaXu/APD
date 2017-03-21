<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Config_model extends MyModel
{
    function __construct()
    {
        parent::__construct('Config');
    }

    /**
     * @function 获取需要同步的配置
     * @author Peter
     * @param $user_id
     * @param $anchor
     * @return mixed
     */
    public function get_sync_configs($user_id, $anchor)
    {
        $sql = "SELECT * FROM Config WHERE Modified > ? AND UserId = ? AND Type IN (1, 3, 4)";
        $bind = array($anchor, $user_id);
        $query = $this->db->query($sql, $bind);
        return $query->result_array();
    }
}
