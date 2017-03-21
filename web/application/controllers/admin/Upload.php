<?php

/**
 * Created by PhpStorm.
 * User: Peter
 * Date: 2016/3/28
 * Time: 14:56
 */
class Upload extends MyController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('bll/app_version_bll');
    }

    public function index()
    {
        $offset = intval($this->input->get('offset'));

        if ($offset < 0)
        {
            return ;
        }

        //查询版本信息
        $select = 'Id, AppName, AppType, VersionCode, VersionName,
                    DownloadUrl, ForceUpdate, UpdateInfo, TargetVersionId';
        $where = array();
        $limit = 10;
        $versions = $this->app_version_bll->fetch($select, $where, $limit, $offset);

        //生成分页信息
        $this->load->library('pagination');
        $config['base_url'] = "/admin/upload/index";
        $config['total_rows'] = $versions['count'];
        $config['per_page'] = $limit;
        $config['page_query_string'] = TRUE;
        $config['attributes'] = array('class' => 'box');
        $config['query_string_segment'] = 'offset';
        $this->pagination->initialize($config);

        $this->hpf_smarty->assign('versions', $versions['data']);
        $this->hpf_smarty->assign('page_info', $this->pagination->create_links());
        $this->hpf_smarty->display('admin/upload/index.tpl');
    }

    public function upload()
    {
        if ($this->input->method() === 'get')
        {
            $this->hpf_smarty->display('admin/upload/upload.tpl');
        }
        else if ($this->input->method() === 'post')
        {//提交表单
            //获取并过滤输入
            array_walk_recursive($_POST, function (&$value)
            {
                $value = trim($value);
            });
            $app_name = $this->input->post('app_name');
            $app_type = intval($this->input->post('app_type'));
            $version_code = intval($this->input->post('version_code'));
            $version_name = $this->input->post('version_name');
            $force_update = intval($this->input->post('force_update'));
            $update_info = $this->input->post('update_info');

            //验证输入
            $errors = array();
            if (!Common::check_string_length(1, 60, $app_name))
            {
                $errors[] = 'APP名称长度非法';
            }
            if (!in_array($app_type, array(1, 2)))
            {
                $errors[] = 'APP类型非法';
            }
            if ($version_code <= 0)
            {
                $errors[] = '版本号非法';
            }
            if (!Common::check_string_length(1, 120, $version_name))
            {
                $errors[] = '版本名称长度非法';
            }
            if (!in_array($force_update, array(0, 1)))
            {
                $errors[] = '强制升级非法';
            }
            if (!Common::check_string_length(0, 255, $update_info))
            {
                $errors[] = '更新说明长度非法';
            }
            if ($errors)
            {
                $this->hpf_smarty->assign('errors', $errors);
                $this->hpf_smarty->assign('data', $this->input->post());
                $this->hpf_smarty->display('admin/upload/upload.tpl');
                return ;
            }

            //上传文件
            $config['upload_path'] = FCPATH . 'resource/upload/package/';
            $config['allowed_types'] = 'apk';
            $config['file_ext_tolower'] = TRUE;
            $config['mod_mime_fix'] = FALSE;
            $config['overwrite'] = TRUE;
            $this->load->library('upload', $config);
            if (!$this->upload->do_upload('package'))
            {
                $errors = $this->upload->display_errors();
                $this->hpf_smarty->assign('errors', $errors);
                $this->hpf_smarty->assign('data', $this->input->post());
                $this->hpf_smarty->display('admin/upload/upload.tpl');
                return ;
            }

            //写入数据库
            $data['AppName'] = $app_name;
            $data['AppType'] = $app_type;
            $data['VersionCode'] = $version_code;
            $data['VersionName'] = $version_name;
            $data['DownloadUrl'] = $this->upload->data()['file_name'];
            $data['ForceUpdate'] = $force_update;
            $data['UpdateInfo'] = $update_info;
            if (!$this->app_version_bll->insert_new($data))
            {
                $errors[] = '写入失败，请稍后再试';
                $this->hpf_smarty->assign('errors', $errors);
                $this->hpf_smarty->assign('data', $this->input->post());
                $this->hpf_smarty->display('admin/upload/upload.tpl');
                return ;
            }

            header("Location:/admin/upload");
        }
    }

    public function edit()
    {
        if ($this->input->method() === 'get')
        {
            $id = intval($this->input->get('id'));

            if ($id <= 0)
            {
                return ;
            }

            $select = 'Id, AppName, AppType, VersionCode, VersionName,
                    DownloadUrl, ForceUpdate, UpdateInfo, TargetVersionId';
            $where = array('Id' => $id);
            $result = $this->app_version_bll->fetch($select, $where);
            if (!$result)
            {
                return ;
            }

            $this->hpf_smarty->assign('data', $result['data'][0]);
            $this->hpf_smarty->display('admin/upload/edit.tpl');
        }
        else if ($this->input->method() === 'post')
        {//提交表单
            //获取并过滤输入
            array_walk_recursive($_POST, function (&$value)
            {
                $value = trim($value);
            });
            $id = intval($this->input->post('id'));
            $app_name = $this->input->post('app_name');
            $app_type = intval($this->input->post('app_type'));
            $version_code = intval($this->input->post('version_code'));
            $version_name = $this->input->post('version_name');
            $force_update = intval($this->input->post('force_update'));
            $update_info = $this->input->post('update_info');
            $target_version_id = $this->input->post('target_version_id');

            //验证输入
            $errors = array();
            if ($id <= 0)
            {
                return ;
            }
            if (!Common::check_string_length(1, 60, $app_name))
            {
                $errors[] = 'APP名称长度非法';
            }
            if (!in_array($app_type, array(1, 2)))
            {
                $errors[] = 'APP类型非法';
            }
            if ($version_code <= 0)
            {
                $errors[] = '版本号非法';
            }
            if (!Common::check_string_length(1, 120, $version_name))
            {
                $errors[] = '版本名称长度非法';
            }
            if (!in_array($force_update, array(0, 1)))
            {
                $errors[] = '强制升级非法';
            }
            if (!Common::check_string_length(0, 255, $update_info))
            {
                $errors[] = '更新说明长度非法';
            }
            if ($target_version_id && $target_version_id <= 0)
            {
                $errors[] = '目标版本号非法';
            }
            if ($errors)
            {
                $this->hpf_smarty->assign('errors', $errors);
                $this->hpf_smarty->assign('data', $this->input->post());
                $this->hpf_smarty->display('admin/upload/edit.tpl');
                return ;
            }

            //更新数据库
            $data['AppName'] = $app_name;
            $data['AppType'] = $app_type;
            $data['VersionCode'] = $version_code;
            $data['VersionName'] = $version_name;
            $data['ForceUpdate'] = $force_update;
            $data['UpdateInfo'] = $update_info;
            if ($target_version_id)
            {
                $data['TargetVersionId'] = intval($target_version_id);
            }
            else
            {
                $data['TargetVersionId'] = NULL;
            }
            $where = array('Id' => $id);
            if (!$this->app_version_bll->update($data, $where))
            {
                $errors[] = '更新失败，请稍后再试';
                $this->hpf_smarty->assign('errors', $errors);
                $this->hpf_smarty->assign('data', $this->input->post());
                $this->hpf_smarty->display('admin/upload/edit.tpl');
                return ;
            }

            header("Location:/admin/upload");
        }
    }
}