<?php

/**
 * common modelClass
 *
 * 
 *
 * @package		CodeIgniter
 * @subpackage  	Libraries
 * @category             Community_img_model
 * @author		sunnuy
 * @link		
 */
class Common_model extends MyModel
{

        private static $_instance;

        public function __construct()
        {
        	
        }

        public static function getInstance()
        {
                if (!(self::$_instance instanceof self))
                {
                        self::$_instance = new self();
                }
                return self::$_instance;
        }

        static public function insert($table, $data)
        {
                $instance = self::getInstance();
                return $instance->db->insert($table, $data);
        }
	
	static public function insert_batch($table, $data)
	{
		$instance = self::getInstance();
                return $instance->db->insert_batch($table, $data);
	}
	
        static public function insert_by_sql($sql)
        {
                $instance = self::getInstance();
                return $instance->db->query($sql);
        }
        static public function query_by_sql($sql)
        {
                $instance = self::getInstance();
                return $instance->db->query($sql);
		
        }

        static public function return_insert_id($table, $data)
        {
                $instance = self::getInstance();
                $bool = $instance->db->insert($table, $data);
                return $instance->db->insert_id();
        }

        /**
         * to do update 
         * @access	public
         * $param array data 需要更新的值
         * @param array where 更新的条件
         */
        static function update($table, $data, $where)
        {
                $instance = self::getInstance();
                return $instance->db->update($table, $data, $where);
                
        }

        /**
         * to do update 
         * @access	public
         * $param array data 需要更新的值
         * @param array where 更新的条件
         */
        static function delete($table, $where)
        {
                $instance = self::getInstance();
                return $instance->db->delete($table, $where);
        }

        static function get($table, $limit = 0, $offset = 0)
        {
                $instance = self::getInstance();
                $data = array();
                if (empty($offset))
                {
                        $query = $instance->db->get($table);
                }
                else
                {
                        $query = $instance->db->get($table, $limit, $offset);
                }

                foreach ($query->result() as $row)
                {
                        $data[] = $row;
                }
                return $data;
        }
          static function arr_get($table,$select='*',$limit = 0,$offset = 0)
        {
               $instance = self::getInstance();
                $data = array();
                $instance->db->select($select);
                if (empty($offset))
                {
                        $query = $instance->db->get($table);
                }
                else
                {
                        $query = $instance->db->get($table, $limit, $offset);
                }

                foreach ($query->result() as $row)
                {
                        $data[] = $row;
                }
                return $query->result_array(); 
        }
        
        //get_where('mytable', array('id' => $id), $limit, $offset);
        static function get_where($table, $arr_where, $limit = 0, $offset = 0)
        {
                $data = array();
                $instance = self::getInstance();
                if (empty($offset))
                {
                        $query = $instance->db->get_where($table, $arr_where);
                }
                else
                {
                        $query = $instance->db->get_where($table, $arr_where, $limit, $offset);
                }
                foreach ($query->result() as $row)
                {
                        $data[] = $row;
                }
                return $query->result_array();
        }

        //get one id  有bug,记得修复下
        static function get_id_by_where($table, $arr_where, $limit = 0, $offset = 0)
        {
                $data = false;
                $instance = self::getInstance();
                if (empty($offset))
                {
                        $query = $instance->db->get_where($table, $arr_where);
                }
                else
                {
                        $query = $instance->db->get_where($table, $arr_where, $limit, $offset);
                }
                $query = $instance->db->get_where($table, $arr_where);
                foreach ($query->result() as $row)
                {
                        return $row->Id;
                }
                return false;
        }


    /**
     * 根据条件查询数据
     * @author flower
     * @param string $distinct 去重标志
     * @param string $fields select选取的字段
     * @param string $table 数据库表名
     * @param array $arr_where where条件数组
     * @param string $like_fields 模糊搜索的字段
     * @param string $like_value 模糊搜索的字段的值
     * @param string $join_table 连接的表名
     * @param string $join_on join链接的字段:A.Id=B.Id
     * @param string $join_direct join的方式:left,right..
     * @param int $limit limit记录数
     * @param int $offset 开始取的记录(limit $offset,$limit)
     * @param string $order_by 排序的字段
     * @param string $order 排序方法
     * @param string $group_by 分组的字段
     * @param array $where_in where in限制条件数组
     * @return array
     */
    static function get_fileds_by_where($distinct='',$fields='*', $table, $arr_where, $like_fields='',$like_value='', $join_table='', $join_on='', $join_direct='',
                                        $limit=0, $offset=0, $order_by='', $order='DESC', $group_by='',$where_in=array())
    {
        $instance = self::getInstance();
        $db = $instance->db;

        $db->select($fields);
        //连接
        if(!empty($join_table) && !empty($join_on))
        {
            if(!empty($join_direct))
            {
                $db->join($join_table,$join_on);
            }
            else
            {
                $db->join($join_table,$join_on,$join_direct);
            }
        }
        //like模糊查询
        if(!empty($like_fields) && !empty($like_value))
        {
            $db->like($like_fields,$like_value);
        }
        //分组
        if(!empty($group_by))
        {
            $db->group_by($group_by);
        }
        //排序
        if(!empty($order_by))
        {
            $db->order_by($order_by,$order);
        }
        //去重
        if(!empty($distinct))
        {
            $db->distinct($distinct);
        }
        //where_in
        if(!empty($where_in))
        {
            foreach($where_in as $key=>$value)
            {
                if(!empty($value['Value']))
                {
                    $db->where_in($value['Filed'],$value['Value']);
                }
            }
        }
        //where,limit筛选
        if (empty($offset))
        {
            $query = $db->get_where($table,$arr_where);
        }
        else
        {
            $query = $db->get_where($table,$arr_where,$limit,$offset);
        }
        return $query->result_array ();
    }


    /**
     * 数据库in范围查询
     * @author flower
     * @param string $table_name 表名
     * @param string $select select字段
     * @param array $where_in_arr in数组组合
     * @param string $order 排序方法:desc
     * @param string $order_by 排序字段
     * @return array
     */
    public function get_where_in($table_name,$select,$where_in_arr,$order,$order_by)
    {
        $db = $this->db;
        if(!empty($where_in_arr))
        {
            foreach($where_in_arr as $key=>$value)
            {
                if(!empty($value['Value']))
                {
                    $db->where_in($value['Filed'],$value['Value']);
                }
            }
        }
        $db->select($select);
        if(!empty($order_by))
        {
            $db->order_by($order_by,$order);
        }
        $query = $db->get($table_name);
        return $query->result_array();
    }
}
