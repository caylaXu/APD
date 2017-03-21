<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
| -------------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------------
| This file lets you re-map URI requests to specific controller functions.
|
| Typically there is a one-to-one relationship between a URL string
| and its corresponding controller class/method. The segments in a
| URL normally follow this pattern:
|
|	example.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details:
|
|	http://codeigniter.com/user_guide/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There are three reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router which controller/method to use if those
| provided in the URL cannot be matched to a valid route.
|
|	$route['translate_uri_dashes'] = FALSE;
|
| This is not exactly a route, but allows you to automatically route
| controller and method names that contain dashes. '-' isn't a valid
| class or method name character, so it requires translation.
| When you set this option to TRUE, it will replace ALL dashes in the
| controller and method URI segments.
|
| Examples:	my-controller/index	-> my_controller/index
|		my-controller/my-method	-> my_controller/my_method
*/
$route['default_controller'] = 'welcome';
$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;

$route['rest_api/(:any)/login'] = 'rest_api_$1/login/login_new';//登陆接口
$route['rest_api/(:any)/quit'] = 'rest_api_$1/login/quit_new';//登出接口
$route['rest_api/(:any)/validate_code'] = 'rest_api_$1/login/validate_code';//获取验证码接口
$route['rest_api/(:any)/register'] = 'rest_api_$1/login/register';//注册接口
$route['rest_api/(:any)/password'] = 'rest_api_$1/login/password';
$route['rest_api/(:any)/third_login'] = 'rest_api_$1/login/third_login';//第三方注册登录接口

//任务
$route['rest_api/(:any)/task'] = 'rest_api_$1/task/task';//增
$route['rest_api/(:any)/task/(:num)'] = 'rest_api_$1/task/task/Id/$2';//删、改
$route['rest_api/(:any)/tasks'] = 'rest_api_$1/task/tasks';//查

//检查项
//任务
$route['rest_api/(:any)/checklist'] = 'rest_api_$1/checklist/checklist';//增
$route['rest_api/(:any)/checklist/(:num)'] = 'rest_api_$1/checklist/checklist/Id/$2';//删、改

//目标
$route['rest_api/(:any)/milestone'] = 'rest_api_$1/milestone/milestone';//增
$route['rest_api/(:any)/milestone/(:num)'] = 'rest_api_$1/milestone/milestone/Id/$2';//删、改
$route['rest_api/(:any)/milestones'] = 'rest_api_$1/milestone/milestones';//查

//项目
$route['rest_api/(:any)/project/(:num)/tasks'] = 'rest_api_$1/task/tasks_by_project/ProjectId/$2';//夏目详情的任务列表
$route['rest_api/(:any)/project/(:num)/users'] = 'rest_api_$1/user/user_by_project_id/ProjectId/$2';
$route['rest_api/(:any)/project'] = 'rest_api_$1/project/project';//增
$route['rest_api/(:any)/project/(:num)'] = 'rest_api_$1/project/project/Id/$2';//删、改
$route['rest_api/(:any)/projects'] = 'rest_api_$1/project/projects';//查
$route['rest_api/(:any)/user/(:num)/projects'] = 'rest_api_$1/project/project_by_user_id/UserId/$2';//获取用户相关的项目列表（名称和Id）
$route['rest_api/(:any)/user/(:num)'] = 'rest_api_$1/user/user/Id/$2';//修改用户信息
$route['rest_api/(:any)/combined_user'] = 'rest_api_$1/user/combined_user';//合并用户
$route['rest_api/(:any)/bound_user'] = 'rest_api_$1/user/bound_user';//绑定第三方登录

//用户相关
$route['rest_api/(:any)/user'] = 'rest_api_$1/user/user';
$route['rest_api/(:any)/users'] = 'rest_api_$1/user/users';

//日历相关接口
$route['rest_api/(:any)/calendars'] = 'rest_api_$1/calendar/tasks';

//进度更新
$route['rest_api/(:any)/progress'] = 'rest_api_$1/common_function/progress';

//添加责任人关注人接口
$route['rest_api/(:any)/rlt_user'] = 'rest_api_$1/common_function/rlt_user';

//同步接口
$route['rest_api/(:any)/sync'] = 'rest_api_$1/sync/sync';//获取同步数据/提交同步数据

//获取版本相关
$route['rest_api/(:any)/app_version'] = 'rest_api_$1/app_version/app_version';//获取版本信息



