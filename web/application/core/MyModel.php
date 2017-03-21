<?php
/**
 * 	加载第三方库
 * 		一些自己写的公用model(全局系统应用使用)
 * 		一些系统配置
 * 			system/core 等库都已经在CI框架 run()运行就加载了。
 *
 * @author pengfei
 * @property CI_Loader $load
 * @property CI_DB_active_record $db
 * @property CI_Config $config
 * @property hpf_zend $hpf_zend
 * @property hpf_logger $hpf_logger
 * @property hpf_smarty $hpf_smarty
 */

require_once 'MySyncModel.php';

class MyModel extends CI_Model
{
    protected $table_name = '';

	public function __construct($table_name = '')
	{
		parent::__construct ();
        $this->table_name = $table_name;
//		$this->load->model('enum/enum');
//		$this->load->model ( "dal/db/common_model" );
		$this->load->database ();
	}

    /**
     * @param mixed        $identifier
     * @param string|array $fields
     * @return array
     */
    public function my_find($identifier, $fields = null)
    {

        $identifier = $this->my_identifier_to_where($identifier);

        foreach ($identifier as $key => $val) {
            $this->db->where($key, $val);
        }

        if ($fields != null) {
            $this->db->select($fields);
        }

        /** @var CI_Db_Result $query */
        $query = $this->db->get($this->table_name);

        return $query->row_array();
    }

    /**
     * @param string $select
     * @param null   $escape
     * @return $this
     */
    public function my_select($select = '*', $escape = null)
    {
        $this->db->select($select, $escape);

        return $this;
    }

    /**
     * @param string $key
     * @param null   $value
     * @param null   $escape
     * @return $this
     */
    public function my_where($key, $value = null, $escape = null)
    {
        $this->db->where($key, $value, $escape);

        return $this;
    }

    /**
     * @param int $value
     * @param int $offset
     * @return $this
     */
    public function my_limit($value, $offset = 0)
    {
        $this->db->limit($value, $offset);

        return $this;
    }


    /**
     * @param Pagination $pagination
     * @return $this
     */
    public function my_page(Pagination $pagination)
    {
        $this->my_limit($pagination->limit, $pagination->offset);

        return $this;
    }

    /**
     * @return array
     */
    public function my_fetch_all()
    {
        $result = $this->db->get($this->table_name);

        return $result->result_array();
    }

    /**
     * @return int
     */
    public function my_count()
    {
        $this->db->select('COUNT(*) AS A');
        /** @var CI_Db_Result $query */
        $query = $this->db->get($this->table_name);

        return $query->row_array()['A'];
    }

    /**
     * @param array     $data
     * @param array|int $identifier
     * @return int
     * @throws Exception
     */
    public function my_exec_update(array $data, $identifier)
    {
        $where = $this->my_identifier_to_where($identifier);
        $query = $this->db->update($this->table_name, $data, $where);
        if ($query) {
            return $this->db->affected_rows() ? $this->db->affected_rows() : true;
        } else {
            return false;
        }
    }

    /**
     * @param array $data
     * @return bool|int
     */
    public function my_exec_insert(array $data)
    {
        $state = $this->db->insert($this->table_name, $data);

        if ($state) {
            return $this->db->insert_id();
        } else {
            return false;
        }
    }

    /**
     * @param int|array $identifier
     * @return object
     * @throws Exception
     */
    public function my_delete($identifier)
    {
        $where = $this->my_identifier_to_where($identifier);
        /** @var CI_Db_Result $query */
        $query = $this->db->delete($this->table_name, $where);
        if ($query) {
            return $this->db->affected_rows();
        } else {
            return false;
        }
    }

    /**
     * @param $sql
     * @return mixed
     */
    public function my_query($sql)
    {
        $query = $this->db->query($sql);

        return $query->result_array();
    }

    /**
     * @param $sql
     * @return mixed
     */
    public function my_query_one($sql)
    {
        $query = $this->db->query($sql);

        return $query->row_array();
    }

    /**
     * @param $identifier
     * @return array
     * @throws InvalidArgumentException
     */
    private function my_identifier_to_where($identifier)
    {
        if (is_numeric($identifier)) {
            $identifier = array(
                'Id' => $identifier
            );
        }

        if (!is_array($identifier)) {
            throw new InvalidArgumentException('参数错误');
        }

        return $identifier;
    }

    /**
     * @param $key
     * @return mixed
     * @throws ErrorException
     */
    public function __get($key)
    {

        if ($key == 'table_name') {
            throw new ErrorException('请设置表名');
        }

        return parent::__get($key);
    }

    /**
     * 返回数据库操作结果
     * @param $query
     * @return bool|array
     */
    public function my_return_query($query)
    {
        if($query)
        {
            return $query->result_array();
        }
        else
        {
            return false;
        }
    }

    /**
     * 返回数据库操作结果，失败返回空数组
     * @param $query
     * @return array
     */
    public function my_return_array($query)
    {
        if($query)
        {
            return $query->result_array();
        }
        else
        {
            return array();
        }
    }

	//------------ Peter ------------

	public function p_fetch($one = FALSE)
	{
		if ($one)
		{
			$this->db->limit(1);
		}
		$query = $this->db->get($this->table_name);

		if (!$query)
		{
			return array();
		}

		return $one ? $query->row_array() : $query->result_array();
	}

	public function p_exist($where = array())
	{
		if ($where)
		{
			$this->db->where($where);
		}
		$query = $this->db
			->select('Id')
			->limit(1)
			->get($this->table_name);
		if (!$query)
		{
			return FALSE;
		}

		return $query->row_array() ? TRUE : FALSE;
	}

	public function p_insert($set = NULL, $escape = NULL)
	{
		$query = $this->db->insert($this->table_name, $set, $escape);
		if ($query)
		{
			return $this->db->insert_id();
		}
		else
		{
			return FALSE;
		}
	}

	public function p_insert_batch($set = NULL, $escape = NULL)
	{
		return $this->db->insert_batch($this->table_name, $set, $escape);
	}

	public function p_update($set = NULL, $where = NULL, $limit = NULL)
	{
		$query = $this->db->update($this->table_name, $set, $where, $limit);
		if ($query)
		{
			return $this->db->affected_rows();
		}
		else
		{
			return FALSE;
		}
	}

	public function p_delete($where = '', $limit = NULL, $reset_data = TRUE)
	{
		$query = $this->db->delete($this->table_name, $where, $limit, $reset_data);
		if ($query)
		{
			return $this->db->affected_rows();
		}
		else
		{
			return FALSE;
		}
	}

	public function p_select($select = '*', $escape = NULL)
	{
		$this->db->select($select, $escape);

		return $this;
	}

	public function p_where($key, $value = NULL, $escape = NULL)
	{
		$this->db->where($key, $value, $escape);

		return $this;
	}

	public function p_or_where($key, $value = NULL, $escape = NULL)
	{
		$this->db->or_where($key, $value, $escape);

		return $this;
	}

	public function p_where_in($key = NULL, $values = NULL, $escape = NULL)
	{
		$this->db->where_in($key, $values, $escape);

		return $this;
	}

	public function p_like($field, $match = '', $side = 'both', $escape = NULL)
	{
		$this->db->like($field, $match, $side, $escape);

		return $this;
	}

	public function p_order_by($order_by, $direction = '', $escape = NULL)
	{
		$this->db->order_by($order_by, $direction, $escape);

		return $this;
	}

	public function p_limit($length, $offset = 0)
	{
		$this->db->limit($length, $offset);

		return $this;
	}
}