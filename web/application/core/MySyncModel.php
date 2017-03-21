<?php

/**
 * 需要同步数据的表的model
 *
 * Created by PhpStorm.
 * User: Peter
 * Date: 2016/6/30
 * Time: 16:27
 */
class MySyncModel extends MyModel
{
	public function __construct($table_name)
	{
		parent::__construct($table_name);
		$this->load->model('bll/common_bll');
	}

	public function p_insert($set = NULL, $escape = NULL)
	{
		!isset($set['Status']) && $set['Status'] = 1;
		!isset($set['Method']) && $set['Method'] = 0;
		!isset($set['Modified']) && $set['Modified'] = $this->common_bll->get_max_modified();

		return parent::p_insert($set, $escape);
	}

	public function p_update($set = NULL, $where = NULL, $limit = NULL)
	{
		!isset($set['Method']) && $set['Method'] = 1;
		!isset($set['Modified']) && $set['Modified'] = $this->common_bll->get_max_modified();

		return parent::p_update($set, $where, $limit);
	}

	public function p_delete($where = '', $limit = NULL, $reset_data = TRUE)
	{
		$set['Status'] = -1;
		$set['Method'] = -1;
		$set['Modified'] = $this->common_bll->get_max_modified();

		return parent::p_update($set, $where, $limit);
	}
}