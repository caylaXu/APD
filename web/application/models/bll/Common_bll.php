<?php
/**
 * Created by PhpStorm.
 * User: CaylaXu <caylaxu@motouch.cn>
 * Date: 2015/11/5
 * Time：19:51
 */
class Common_bll extends MyModel
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('bll/redis_bll');
    }


    /**
     * @function 取modified
     * @User: CaylaXu
     * @param bool $incr 是否需要自增
     * @return mixed
     */
    public function get_max_modified($incr = true)
    {
        $modified = $this->redis_bll->exists('Modified');

        //1、值存在自增并返回
        if(!empty($modified))
        {
            if($incr)
            {
                return $this->redis_bll->incr('Modified');
            }
           else
           {
               return $this->redis_bll->my_get('Modified');
           }
        }

        //2、值不存在则取数据库里取
        $tables = array('Groups','Checklist','Projects','RltGroupUser','RltProjectUser','RltTag','RltTaskUser','Tags','Tasks','Users');
        $modified = array();
        foreach($tables as $table_name)
        {
            $sql = "SELECT MAX(`Modified`) as Modified FROM ".$table_name;
            $query = $this->db->query($sql);
            $array = $query->row_array();
            $modified = array_merge($modified,array_values($array));
        }
        $max_modified =  intval(max($modified));
        $this->redis_bll->my_set('Modified',$max_modified);

        if($incr)
        {
            return $this->redis_bll->incr('Modified');
        }
        else
        {
            return $max_modified;
        }
    }
}