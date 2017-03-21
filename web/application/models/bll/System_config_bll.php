<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Created by PhpStorm.
 * User: Peter
 * Date: 2016/4/6
 * Time: 18:23
 */
class System_config_bll extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('dal/db/system_config_model');
        $this->load->model('bll/common_bll');
    }

    public function select($where = array(), $fields = '*')
    {
        $this->system_config_model->my_select($fields);
        $this->db->where($where);
        $this->db->order_by('Id', 'DESC');
        $themes = $this->system_config_model->my_fetch_all();

        foreach ($themes as &$theme)
        {
            if (isset($theme['Value']) && $theme['Value'])
            {
                $theme = $this->parse_value($theme);
            }
        }

        return $themes;
    }

    public function create($data)
    {
        if (isset($data['Value']))
        {
            $data['Value'] = json_encode($data['Value']);
        }
        $data['Method'] = 0;
        $data['Modified'] = $this->common_bll->get_max_modified();
        $data['UniCode'] = common::create_uuid();

        return $this->system_config_model->my_exec_insert($data);
    }

    public function update($data, $id)
    {
        if (isset($data['Value']))
        {
            $data['Value'] = json_encode($data['Value']);
        }
        $data['Method'] = 1;
        $data['Modified'] = $this->common_bll->get_max_modified();

        return $this->system_config_model->my_exec_update($data, $id);
    }

    /**
     * @function 上传图片
     * @author Peter
     * @param $upload_path
     * @param $file_name
     * @param $field
     * @return array
     */
    public function upload($upload_path, $file_name, $field)
    {
        //上传图片
        $upload['upload_path'] = $upload_path;
        $upload['file_name'] = $file_name;
        $upload['allowed_types'] = 'gif|jpg|jpeg|png|apk';
        $upload['file_ext_tolower'] = TRUE;
        $upload['mod_mime_fix'] = FALSE;
        $upload['overwrite'] = TRUE;
        $controller = &get_instance();
        $controller->load->library('upload', $upload);
        $this->upload->initialize($upload);
        if (!$controller->upload->do_upload($field))
        {
            return array($controller->upload->display_errors());
        }

        return $controller->upload->data('file_name');
    }

    /**
     * @function 生成缩略图
     * @author Peter
     * @param $src_img
     * @param $thumb_img
     */
    private function create_thumb($src_img, $thumb_img)
    {
        if (!file_exists($src_img))
        {
            return ;
        }

        if (file_exists($thumb_img))
        {
            unlink($thumb_img);
        }

        $thumb['image_library'] = 'gd2';
        $thumb['source_image'] = $src_img;
        $thumb['new_image'] = $thumb_img;
        $thumb['create_thumb'] = TRUE;
        $thumb['thumb_marker'] = '';
        $thumb['maintain_ratio'] = TRUE;
        $thumb['width'] = 100;
        $controller = &get_instance();
        $controller->load->library('image_lib', $thumb);
        $controller->image_lib->initialize($thumb);
        $controller->image_lib->resize();
    }

    /**
     * @function 解析SystemConfig的Value字段
     * @author Peter
     * @param $theme
     */
    private function parse_value($theme)
    {
        if (!isset($theme['Value']) || !$theme['Value'])
        {
            return ;
        }

        $value = json_decode($theme['Value'], TRUE);
        $theme['Theme'] = $value['Theme'];
        $theme['Color'] = $value['Color'];
        if (isset($value['Thumb']) && $value['Thumb'])
        {
            $dir = (isset($value['BgImg'])) ? 'web/' : 'app/';
//            var_dump($value);exit;
            $theme['Thumb'] = common::resources_full_path('theme', $dir . 'thumb/' . $value['Thumb'], 'picture');
        }
        if (isset($value['BgImg']) && $value['BgImg'])
        {
            $theme['BgImg'] = common::resources_full_path('theme', 'web/src/' . $value['BgImg'], 'picture');
        }
        if (isset($value['Android']) && $value['Android'])
        {
            $theme['Android'] = common::resources_full_path('theme', 'app/package/' . $value['Android'], 'picture');
        }
        if (isset($value['IOS']) && $value['IOS'])
        {
            $theme['IOS'] = common::resources_full_path('theme', 'app/package/' . $value['IOS'], 'picture');
        }

        return $theme;
    }

    /**
     * @function 重命名文件
     * @author Peter
     * @param $old_name
     * @param $new_name
     * @param $path
     * @return bool
     */
    public function rename_file($old_name, $new_name, $path)
    {
        if (substr($path, -1) != '/')
        {
            $path .= '/';
        }

        $old_file = $path . $old_name;
        $new_file = $path . $new_name;
        if (!file_exists($old_file))
        {
            return false;
        }

        return rename($old_file, $new_file);
    }

    public function get_sync_sys_configs($anchor = 0)
    {
        if ($anchor < 0)
        {
            return array();
        }

        $system_configs = $this->system_config_model->get_sync_sys_configs($anchor);

        array_walk($system_configs, function (&$val, $key, $table_name)
        {
            $val['Table'] = $table_name;
            $val['Url'] = '/' . common::resources_relative_path('thumb');
        }, 'SystemConfig');

        return $system_configs;
    }
}