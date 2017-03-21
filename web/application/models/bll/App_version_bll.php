<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * 版本升级
 * @property app_version_model $app_version_model
 * @property enum_app_version $enum_app_version
 */
class App_version_bll extends CI_Model
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model('dal/db/app_version_model');
		$this->load->model('enum/enum_app_version');
	}

	/**
	 * @param $app_name
	 * @param $app_type
	 * 查询app版本
	 * @return array
	 */
	public function query($app_name,$app_type,$version_name)
	{
		$app_type = $this->convert_type_string_to_name($app_type);
		$res = $this->app_version_model->query($app_name,$app_type,$version_name);

		if($res)
		{
			$target_id = $res['TargetVersionId'];
			if(!empty($target_id))
			{
				$version_info = $res = $this->app_version_model->query_by_id($target_id);
				if($version_info)
				{
					$version_info['ForceUpdate'] = $res['ForceUpdate'];
					if($app_type == 1)
					{
						$version_info['DownloadUrl']	= 'http://'.$_SERVER ['HTTP_HOST'].'/resource/upload/package/'.$res['DownloadUrl'];
					}
					else if($app_type == 2)
					{
						$version_info['DownloadUrl']	= 'http://'.$_SERVER ['HTTP_HOST'].'/resource/upload/package/'.$res['DownloadUrl'];
					}
				}
				return $version_info;
			}
			else
			{
				return false;
			}
		}
		return $res;
	}
	
	/**
	 * @function 获取最新的app
	 * @param unknown $app_name
	 * @param unknown $app_type
	 * @return unknown
	 */
	public function get_newest_app($app_name,$app_type)
	{
		$app_info = $this->app_version_model->get_newest_app($app_name,$app_type);
		if($app_info)
		{
			if($app_name == enum_app_version::EnumAppNameSchool)
			{
				if($app_type == enum_app_version::EnumAppTypeIOS)
				{
					$app_info['DownloadUrl']	= $this->config->item('school_app_ios_directory').'/'.$app_info['DownloadUrl'];
				}
				else if($app_type == enum_app_version::EnumAppTypeAndroid)
				{
					$app_info['DownloadUrl']	= $this->config->item('school_app_android_directory').'/'.$app_info['DownloadUrl'];
				}
				return $app_info;
			}
			else if($app_name == enum_app_version::EnumAppNameCoach)
			{
				if($app_type == enum_app_version::EnumAppTypeIOS)
				{
					$app_info['DownloadUrl']	= $this->config->item('coach_app_ios_directory').'/'.$app_info['DownloadUrl'];
				}
				else if($app_type == enum_app_version::EnumAppTypeAndroid)
				{
					$app_info['DownloadUrl']	= $this->config->item('coach_app_android_directory').'/'.$app_info['DownloadUrl'];
				}
				return $app_info;
			}
			else 
			{
				return false;
			}
		}
		else 
		{
			return false;
		}
	}
	
	public function convert_type_string_to_name($app_type)
	{
		switch ($app_type) {
			case 'IOS':
				$type = enum_app_version::EnumAppTypeIOS;
				break;
			case 'Android':
				$type = enum_app_version::EnumAppTypeAndroid;
				break;
			case 1:
				$type = enum_app_version::EnumAppTypeIOS;
				break;
			case 2:
				$type = enum_app_version::EnumAppTypeAndroid;
				break;
			default:
				$type = 0;
				break;
		}
		return $type;
	}

    public function insert_new($params)
    {
        return $this->app_version_model->insert_new($params);
    }

    /**
     * @function 查询
     * @author Peter
     * @param string $select
     * @param array $where
     * @param int $limit
     * @param int $offset
     * @return mixed
     */
    public function fetch($select = '*', $where = array(), $limit = 10, $offset = 0)
    {
        if (is_array($select))
        {
            $select = implode(', ', $select);
        }
        if (!is_string($select) || !is_array($where) || $limit <= 0 || $offset < 0)
        {
            return array();
        }

        $result = $this->app_version_model->fetch($select, $where, $limit, $offset);

        return $result;
    }

    /**
     * @function 更新
     * @author Peter
     * @param array $where
     * @param array $data
     * @return bool
     */
    public function update( $data = array(), $where = array())
    {
        if (!$where || !$data)
        {
            return FALSE;
        }

        $result = $this->app_version_model->my_exec_update($data, $where);

        return $result;
    }

}

