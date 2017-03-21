<?php

/**
 * Created by PhpStorm.
 * User: Peter
 * Date: 2016/4/6
 * Time: 18:10
 */
class Theme extends MyController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('bll/system_config_bll');
    }

    public function index()
    {
        $themes = $this->system_config_bll->select();

        $this->hpf_smarty->assign('themes', $themes);
        $this->hpf_smarty->display('admin/theme/index.tpl');
    }

    public function add()
    {
        if ($this->input->method() === 'get')
        {
            $this->hpf_smarty->display('admin/theme/add.tpl');
        }
        else if ($this->input->post())
        {
            $data['Type'] = intval($this->input->post('type'));
            $data['Name'] = (string)$this->input->post('name');
            $data['Color'] = (string)$this->input->post('color');
            $data['IconColor'] = (string)$this->input->post('icon_color');
            $data['FontColor'] = (string)$this->input->post('font_color');
            $data['BgColor'] = (string)$this->input->post('bg_color');

            $errors = array();
            if ($data['Type'] <= 0 || !$data['Name'] ||
				!$data['Color'] || !$data['IconColor'] || !$data['FontColor'])
            {
                $errors[] = '参数非法';
            }
            if (!Common::is_color($data['Color']))
            {
                $errors[] = '导航颜色值非法';
            }
			if (!Common::is_color($data['IconColor']))
			{
				$errors[] = '图标颜色值非法';
			}
			if (!Common::is_color($data['FontColor']))
			{
				$errors[] = '字体颜色值非法';
			}
			if (!Common::is_color($data['BgColor']))
			{
				$errors[] = '背景颜色值非法';
			}
            $where = array('Value LIKE' => '%"'. $data['Name'] . '"%');
            $fields = 'Value';
            $themes = $this->system_config_bll->select($where, $fields);
            foreach($themes as $theme)
            {
                if ($theme['Theme'] === $data['Name'])
                {
                    $errors[] = '该主题名称已存在';
                    break;
                }
            }
            if ($errors)
            {
                $this->hpf_smarty->assign('errors', $errors);
                $this->hpf_smarty->assign('data', $data);
                $this->hpf_smarty->display('admin/theme/add.tpl');
                return ;
            }

            //上传缩略图
            $field = 'thumb';
            $upload_path = FCPATH . 'resource/upload/theme/' . ($data['Type']===1?'web/':'app/') . 'thumb/';
            $file_name = $data['Name'];
            $thumb_upload = $this->system_config_bll->upload($upload_path, $file_name, $field);
            if (is_array($thumb_upload))
            {
                $this->hpf_smarty->assign('errors', $thumb_upload);
                $this->hpf_smarty->assign('data', $data);
                $this->hpf_smarty->display('admin/theme/add.tpl');
                return;
            }

            if ($data['Type'] === 1)
            {//上传背景图
                $field = 'image';
                $upload_path = FCPATH . 'resource/upload/theme/web/src/';
                $file_name = $data['Name'];
                $image_upload = $this->system_config_bll->upload($upload_path, $file_name, $field);
                if (is_array($image_upload))
                {
                    $this->hpf_smarty->assign('errors', $image_upload);
                    $this->hpf_smarty->assign('data', $data);
                    $this->hpf_smarty->display('admin/theme/add.tpl');
                    return;
                }
            }
            else if ($data['Type'] === 2)
            {//上传主题包
                if (isset($_FILES['android']['tmp_name']) && $_FILES['android']['tmp_name'])
                {
                    $field = 'android';
                }
                else if (isset($_FILES['ios']['tmp_name']) && $_FILES['ios']['tmp_name'])
                {
                    $field = 'ios';
                }
                $upload_path = FCPATH . 'resource/upload/theme/app/package/';
                $file_name = $data['Name'];
                $package_upload = $this->system_config_bll->upload($upload_path, $file_name, $field);
                if (is_array($package_upload))
                {
                    $this->hpf_smarty->assign('errors', $package_upload);
                    $this->hpf_smarty->assign('data', $data);
                    $this->hpf_smarty->display('admin/theme/add.tpl');
                    return;
                }
            }

            //写入数据库
            $param['Type'] = $data['Type'];
            $param['Value'] = array(
                'Theme' => $data['Name'],
                'Color' => $data['Color'],
                'Thumb' => $thumb_upload,
				'IconColor' => $data['IconColor'],
				'FontColor' => $data['FontColor'],
				'BgColor' => $data['BgColor'],
            );
            if ($data['Type'] === 1)
            {
                $param['Value']['BgImg'] = $image_upload;
            }
            else if ($data['Type'] === 2)
            {
                if (isset($_FILES['android']['tmp_name']) && $_FILES['android']['tmp_name'])
                {
                    $param['Value']['Android'] = $package_upload;
                }
                else if (isset($_FILES['ios']['tmp_name']) && $_FILES['ios']['tmp_name'])
                {
                    $param['Value']['IOS'] = $package_upload;
                }
            }
            $param['Status'] = 1;
            if (!$this->system_config_bll->create($param))
            {
                $errors[] = '添加主题失败';
                $this->hpf_smarty->assign('errors', $errors);
                $this->hpf_smarty->assign('data', $data);
                $this->hpf_smarty->display('admin/theme/add.tpl');
                return ;
            }

            header("Location:/admin/theme/index");
        }
    }

    public function edit()
    {
        if ($this->input->get())
        {
            $id = intval($this->input->get('id'));

            if ($id <= 0)
            {
                return ;
            }

            $where = array('Id' => $id);
            $fields = 'Id, Type, Value, Status';
            $theme = $this->system_config_bll->select($where, $fields);
            if (!$theme)
            {
                return ;
            }

            $theme = $theme[0];
            $value = json_decode($theme['Value'],  TRUE);
            $theme['Name'] = $value['Theme'];
            $theme['Color'] = $value['Color'];
			if (isset($value['IconColor']))
			{
				$theme['IconColor'] = $value['IconColor'];
			}
			if (isset($value['FontColor']))
			{
				$theme['FontColor'] = $value['FontColor'];
			}
			if (isset($value['BgColor']))
			{
				$theme['BgColor'] = $value['BgColor'];
			}
            unset($theme['Value']);

            $this->hpf_smarty->assign('data', $theme);
            $this->hpf_smarty->display('admin/theme/edit.tpl');
        }
        else if ($this->input->post())
        {
            $data['Id'] = intval($this->input->post('Id'));
            $data['Status'] = intval($this->input->post('Status'));
            $data['Theme'] = (string)$this->input->post('Name');
            $data['Color'] = (string)$this->input->post('Color');
			$data['IconColor'] = (string)$this->input->post('IconColor');
			$data['FontColor'] = (string)$this->input->post('FontColor');
			$data['BgColor'] = (string)$this->input->post('BgColor');

            $errors = array();
			if ($data['Id'] <= 0 || !$data['Theme'])
			{
				$errors[] = '参数非法';
			}
			if (!Common::is_color($data['Color']))
			{
				$errors[] = '导航颜色值非法';
			}
			if ($data['IconColor'] && !Common::is_color($data['IconColor']))
			{
				$errors[] = '图标颜色值非法';
			}
			if ($data['FontColor'] && !Common::is_color($data['FontColor']))
			{
				$errors[] = '字体颜色值非法';
			}
			if ($data['BgColor'] && !Common::is_color($data['BgColor']))
			{
				$errors[] = '背景颜色值非法';
			}

            $where = array('Id' => $data['Id']);
            $fields = 'Id, Type, Value, Status';
            $theme = $this->system_config_bll->select($where, $fields);
            if (!$theme)
            {
                $errors[] = '该主题不存在';
            }
			$where = array(
				'Id !=' => $data['Id'],
				'Value LIKE' => '%' . $data['Theme'] . '%',
			);
            $fields = 'Value';
            $themes = $this->system_config_bll->select($where, $fields);
            foreach($themes as $t)
            {
                if ($t['Theme'] === $data['Theme'])
                {
                    $errors[] = '该主题名称已存在';
                    break;
                }
            }
            if ($errors)
            {
                $this->hpf_smarty->assign('errors', $errors);
                $this->hpf_smarty->assign('data', $data);
                $this->hpf_smarty->display('admin/theme/edit.tpl');
                return ;
            }

            $theme = $theme[0];
            $value = json_decode($theme['Value'], TRUE);
            $update['Status'] = $data['Status'];
            $update['Value']['Theme'] = $data['Theme'];
            $update['Value']['Color'] = $data['Color'];
            if ($data['IconColor'])
			{
				$update['Value']['IconColor'] = $data['IconColor'];
			}
			if ($data['FontColor'])
			{
				$update['Value']['FontColor'] = $data['FontColor'];
			}
			if ($data['BgColor'])
			{
				$update['Value']['BgColor'] = $data['BgColor'];
			}
            $update['Value']['Thumb'] = $data['Theme'] . '.' . pathinfo($value['Thumb'], PATHINFO_EXTENSION);
            if ($theme['Type'] == 1)
            {
                $update['Value']['BgImg'] = $data['Theme'] . '.' . pathinfo($value['BgImg'], PATHINFO_EXTENSION);
            }
            if (isset($value['Android']) && $value['Android'])
            {
                $update['Value']['Android'] = $data['Theme'] . '.' . pathinfo($value['Android'], PATHINFO_EXTENSION);
            }
            if (isset($value['IOS']) && $value['IOS'])
            {
                $update['Value']['IOS'] = $data['Theme'] . '.' . pathinfo($value['IOS'], PATHINFO_EXTENSION);
            }
            if ($theme['Theme'] !== $data['Theme'])
            {
                //重命名相关文件
                if ($theme['Type'] == 1)
                {
                    $bgimg_path = FCPATH . 'resource/upload/theme/web/src/';
                    $old_name = $theme['Theme'] . '.' . pathinfo($value['BgImg'], PATHINFO_EXTENSION);
                    $new_name = $data['Theme'] . '.' . pathinfo($value['BgImg'], PATHINFO_EXTENSION);
                    if (!$this->system_config_bll->rename_file($old_name, $new_name, $bgimg_path))
                    {
                        $errors[] = '编辑失败，请稍后再试';
                    }

                    $thumb_path = FCPATH . 'resource/upload/theme/web/thumb/';
                    $old_name = $theme['Theme'] . '.' . pathinfo($value['Thumb'], PATHINFO_EXTENSION);
                    $new_name = $data['Theme'] . '.' . pathinfo($value['Thumb'], PATHINFO_EXTENSION);
                    if (!$this->system_config_bll->rename_file($old_name, $new_name, $thumb_path))
                    {
                        $errors[] = '编辑失败，请稍后再试';
                    }
                }
                else if ($theme['Type'] == 2)
                {
                    $thumb_path = FCPATH . 'resource/upload/theme/app/thumb/';
                    $old_name = $theme['Theme'] . '.' . pathinfo($value['Thumb'], PATHINFO_EXTENSION);
                    $new_name = $data['Theme'] . '.' . pathinfo($value['Thumb'], PATHINFO_EXTENSION);
                    if (!$this->system_config_bll->rename_file($old_name, $new_name, $thumb_path))
                    {
                        $errors[] = '编辑失败，请稍后再试';
                    }

                    if (isset($theme['Android']))
                    {
                        $package_path = FCPATH . 'resource/upload/theme/app/package/';
                        $old_package_name = $theme['Theme'] . '.' . pathinfo($value['Android'], PATHINFO_EXTENSION);
                        $new_package_name = $data['Theme'] . '.' . pathinfo($value['Android'], PATHINFO_EXTENSION);
                        if (!$this->system_config_bll->rename_file($old_package_name, $new_package_name, $package_path))
                        {
                            $errors[] = '编辑失败，请稍后再试';
                        }
                    }
                    else if (isset($theme['IOS']))
                    {
                        $package_path = FCPATH . 'resource/upload/theme/app/package/';
                        $old_package_name = $theme['Theme'] . '.' . pathinfo($value['IOS'], PATHINFO_EXTENSION);
                        $new_package_name = $data['Theme'] . '.' . pathinfo($value['IOS'], PATHINFO_EXTENSION);
                        if (!$this->system_config_bll->rename_file($old_package_name, $new_package_name, $package_path))
                        {
                            $errors[] = '编辑失败，请稍后再试';
                        }
                    }
                }
            }
            if ($errors)
            {
                $this->hpf_smarty->assign('errors', $errors);
                $this->hpf_smarty->assign('data', $data);
                $this->hpf_smarty->display('admin/theme/edit.tpl');
                return ;
            }

            //更新数据库
            if (!$this->system_config_bll->update($update, $data['Id']))
            {
                $errors[] = '编辑失败，请稍后再试';
            }
            if ($errors)
            {
                $this->hpf_smarty->assign('errors', $errors);
                $this->hpf_smarty->assign('data', $data);
                $this->hpf_smarty->display('admin/theme/edit.tpl');
                return ;
            }

            header("Location:/admin/theme/index");
        }
    }

    public function img_change()
    {
        if ($this->input->get())
        {
            $data['id'] = intval($this->input->get('id'));
            $data['type'] = intval($this->input->get('type'));

            if ($data['id'] <= 0 || $data['type'] <= 0)
            {
                return ;
            }
            $where = array('Id' => $data['id']);
            $fields = 'Id, Type, Value, Status';
            $theme = $this->system_config_bll->select($where, $fields);
            if (!$theme)
            {
                return ;
            }

            $theme = $theme[0];
            $data['image'] = ($data['type'] === 1 ? $theme['Thumb'] : $theme['BgImg']);

            $this->hpf_smarty->assign('data', $data);
            $this->hpf_smarty->display('admin/theme/img_change.tpl');
        }
        else if ($this->input->post())
        {
            $data['id'] = intval($this->input->post('id'));
            $data['type'] = intval($this->input->post('type'));

            $errors = array();
            if ($data['id'] <= 0 || $data['type'] <= 0)
            {
                $errors[] = '参数非法';
            }
            $where = array('Id' => $data['id']);
            $fields = 'Id, Type, Value, Status';
            $theme = $this->system_config_bll->select($where, $fields);
            if (!$theme)
            {
                $errors[] = '该主题不存在';
            }
            $theme = $theme[0];

            //更换图片
            $field = 'image';
            if ($data['type'] === 1)
            {//更换缩略图
                $upload_path = FCPATH . 'resource/upload/theme/' . ($theme['Type'] == 1 ? 'web/thumb/' : 'app/thumb/');
            }
            else if ($data['type'] === 2)
            {//更换背景图
                $upload_path = FCPATH . 'resource/upload/theme/web/src/';
            }
            $file_name = $theme['Theme'];
            $upload = $this->system_config_bll->upload($upload_path, $file_name, $field, $data['type']);
            if (is_array($upload))
            {
                $data['image'] = ($data['type'] === 1 ? $theme['BgImg'] : $theme['Thumb']);
                $this->hpf_smarty->assign('errors', $upload);
                $this->hpf_smarty->assign('data', $data);
                $this->hpf_smarty->display('admin/theme/img_change.tpl');
                return;
            }

            //更新数据库
            $value = json_decode($theme['Value'], TRUE);
            if ($data['type'] === 1)
            {
                $value['Thumb'] = $upload;
            }
            else if ($data['type'] === 2)
            {
                $value['BgImg'] = $upload;
            }
            $update['Value'] = $value;
            if (!$this->system_config_bll->update($update, $data['id']))
            {
                $errors[] = '更换失败';
            }
            if ($errors)
            {
                $this->hpf_smarty->assign('errors', $errors);
                $this->hpf_smarty->assign('data', $data);
                $this->hpf_smarty->display('admin/theme/edit.tpl');
                return ;
            }

            header('Cache-Control:no-cache,must-revalidate');
            header('Pragma:no-cache');
            header("Location:/admin/theme/index");
        }
    }
}