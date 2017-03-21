<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Task_model extends MyModel
{
    function __construct()
    {
        parent::__construct('Tasks');
    }

    function create(array $params)
    {
        if (empty($params) || !is_array($params))
        {
            return false;
        }

        $this->db->insert($this->table_name, $params);
        return $this->db->insert_id();
    }

    /**
     * 获取用户信息
     * @param type $params array查询字段
     * @param type $wheres array|string查询条件 | 没有查询条件时默认为空数组
     * @param type $flag true:返回多条数据集，false:返回一条数据
     */
    public function select_info(array $select, array $where, $flag = true)
    {
        if (count($select) > 0)
        {
            $select_str = implode(',', $select);
            $this->db->select($select_str);
        }
        $this->db->where($where);
        $query = $this->db->get($this->table_name);
        if ($flag)
        {
            return $query->result_array();
        }
        else
        {
            return $query->row_array();
        }
    }

    public function update_info(array $data, array $where)
    {
        $this->db->where($where);
        $result = $this->db->update($this->table_name, $data);
        return $result;
    }

    public function delete_info(array $where, $modified)
    {
        $this->db->where($where);
        $data = array(
            'Status' => '-1',
            'Method' => '-1',
            'Modified' => $modified
        );
        return $this->db->update($this->table_name, $data);
    }

    public function task_list($params)
    {
        $sql = "SELECT t.* ,p.Title as ProjectName FROM Tasks AS t
                LEFT JOIN Projects as p on p.Id=t.ProjectId
                LEFT JOIN Milestone as m on m.Id = t.MilestoneId
                WHERE t.Status = 1 and t.ParentId=0";
        $sql_arr = array();

        if (isset($params['MilestoneId']) && $params['MilestoneId'] != -1)
        {
            $sql .= " and t.MilestoneId = ? ";
            $sql_arr[] = $params['MilestoneId'];
        }

        $query = $this->db->query($sql, $sql_arr);
        return $query->result_array();
    }

    public function child_task($parent_id, $params)
    {
        $sql = "SELECT t.* ,p.Title as ProjectName FROM Tasks AS t
                LEFT JOIN Projects as p on p.Id=t.ProjectId
                LEFT JOIN Milestone as m on m.Id = t.MilestoneId
                WHERE t.Status = 1 and t.ParentId= ? ";
        $query = $this->db->query($sql, array($parent_id));
        return $query->result_array();
    }

    /**
     * @function 任务详情
     * @author CaylaXu
     */
    public function task_info($id)
    {
        $sql = "select t.*,p.Title as ProjectTitle
                from Tasks as t
                left join Projects as p on p.Id=t.ProjectId
                where t.Id= ?  limit 1";
        $query = $this->db->query($sql, array($id));
        return $query->result_array();
    }

    public function get_task_by_user_id($user_id, $params)
    {
        $sql = "SELECT t.* FROM `Tasks` as t
                LEFT JOIN RltTaskUser as rtu on rtu.TaskId = t.Id
                WHERE t.Id not in (select ParentId from Tasks where Status=1)
                AND rtu.UserId = ?
                AND rtu.Status > 0
                AND t.Status = 1";

        $sql_arr = array($user_id);

        if (isset($params['Progress']) && $params['Progress'] != -1)
        {
            $sql .= " AND t.CompleteProgress = ? ";
            $sql_arr[] = $params['Progress'];
        }
        else
        {
            $sql .= " AND t.CompleteProgress = 0 ";
        }

        if (isset($params['Type']) && strtolower($params['Type']) == 'collection')
        {
            $sql .= " AND t.StartDate = 0 and t.DueDate = 0 ";
//            $sql_arr[] = $params['StartDate'];
//            $sql_arr[] = $params['StartDate'];
        }
        else
        {
            //1、我的待办
            if (isset($params['Type']) && strtolower($params['Type']) == 'backlog')
            {
                $sql .= " AND rtu.Type in (1,2)";
            }//2、我的关注
            else if(isset($params['Type']) && strtolower($params['Type']) == 'attention')
            {
                $sql .= " AND rtu.Type=3";
            }

            if (isset($params['DueDate']) && $params['DueDate'] != -1)
            {
                $sql .= " AND t.StartDate <= ? AND t.DueDate != 0";
                $sql_arr[] = $params['DueDate'];
//                $sql_arr[] = $params['StartDate'];
            }
            else if(isset($params['StartDate']) && $params['StartDate'] !=-1)
            {
                $sql.= " AND t.StartDate <= ? and t.DueDate >= ?";
                $sql_arr[] = $params['StartDate'];
                $sql_arr[] = $params['StartDate'];
            }
        }

        $sql .= " GROUP BY t.Id ORDER BY t.StartDate";
        $query = $this->db->query($sql, $sql_arr);
        return $query->result_array();
    }

    public function paging_get_task_by_user_id($page = 0, $limit = 15, $params = array())
    {
        $sql = "SELECT t.* FROM `Tasks` as t
                LEFT JOIN RltTaskUser as rtu on rtu.TaskId = t.Id
                WHERE t.Id not in (select ParentId from Tasks where Status=1)
                AND rtu.UserId = ?
                AND rtu.Status > 0
                AND t.Status = 1";
        $sql_arr = array($params["UserId"]);
        if (isset($params['Progress']) && $params['Progress'] != -1)
        {
            $sql .= " AND t.CompleteProgress = ? ";
            $sql_arr[] = $params['Progress'];
        }
        else
        {
            $sql .= " AND t.CompleteProgress = 0 ";
        }

        if (isset($params['Type']) && strtolower($params['Type']) == 'collection')
        {
            $sql .= " AND t.StartDate = ? and t.DueDate = ? ";
            $sql_arr[] = $params['StartDate'];
            $sql_arr[] = $params['StartDate'];
        }
        else
        {
            //1、我的待办
            if (isset($params['Type']) && strtolower($params['Type']) == 'backlog')
            {
                $sql .= " AND rtu.Type in (1,2)";
            }

            //2、我的关注
            else if (isset($params['Type']) && strtolower($params['Type']) == 'attention')
            {
                $sql .= " AND rtu.Type=3";
                $params['StartDate'] = strtotime(date("Y-m-d"));
                $params['DueDate'] = strtotime(date("Y-m-d", strtotime("+1 day"))) - 1;
            }

            if (isset($params['DueDate']) && $params['DueDate'] != -1)
            {
                $sql .= " AND t.StartDate <= ? AND t.DueDate != 0";
                $sql_arr[] = $params['DueDate'];
//                $sql_arr[] = $params['StartDate'];
            }
            else if (isset($params['StartDate']) && $params['StartDate'] != -1)
            {
                $sql .= " AND t.StartDate <= ? and t.DueDate >= ?";
                $sql_arr[] = $params['StartDate'];
                $sql_arr[] = $params['StartDate'];
            }
        }

        $sql .= " GROUP BY t.Id ORDER BY t.StartDate";
        $offset = $page * $limit;
        //        $count = $this->db->query($sql,$sql_arr)->num_rows();
        $sql .= " limit " . $offset . "," . $limit;
        $query = $this->db->query($sql, $sql_arr);
        $query = $query->result_array();
        //        $return['Rows'] = $query;
        return $query;
    }


    /**
     * @function 获取项目下的任务
     * @User: CaylaXu
     * @param $project_id
     * @param bool $flag 是否获取子项目下的任务
     * @return mixed
     */
    public function tasks_by_project_id($project_id,$flag = false)
    {
        $sql = "SELECT t.Id,t.Title,t.ParentId,t.ProjectId,t.CompleteProgress,t.Priority,t.StartDate,t.DueDate,t.IsMilestone,t.TrueDueDate,t.Sort
                FROM `Tasks` AS t";
        if($flag)
        {
            $sql .= " LEFT JOIN Projects AS p on p.Id = t.ProjectId WHERE (t.ProjectId = ? OR p.ParentId = ?)";
            $sql_arr = array($project_id,$project_id);
        }
        else
        {
           $sql .= " WHERE t.ProjectId = ? ";
           $sql_arr = array($project_id);
        }
        $sql .= "AND t.Status = 1 ORDER BY t.Sort";
        $query = $this->db->query($sql,$sql_arr);
        return $query->result_array();
    }

    public function milestone_list($project_id)
    {
        //取项目下的里程碑和大项目下的小项目的里程碑
        $sql = "SELECT t.Id,t.Title,t.StartDate,t.Priority,t.CompleteProgress,t.DueDate,
                CASE t.StartDate WHEN 0 THEN  '无' ELSE FROM_UNIXTIME(t.`StartDate`, '%Y-%m-%d') END as Day
                FROM Tasks as t LEFT JOIN Projects AS p on p.Id = t.ProjectId
                WHERE (t.ProjectId = ? OR p.ParentId = ?)
                AND t.IsMilestone = 1
                AND t.Status =1
                ORDER BY StartDate DESC";
        $query = $this->db->query($sql, array($project_id, $project_id));
        return $query->result_array();
    }

    public function delete_by_id($ids, $modified)
    {
        if (is_array($ids))
        {
            $this->db->where_in('Id', $ids);
        }
        else
        {
            $this->db->where('Id', $ids);
        }

        $data = array(
            'Status' => '-1',
            'Method' => '-1',
            'Modified' => $modified
        );

        return $this->db->update($this->table_name, $data);
    }

    public function update_by_id($data, $ids)
    {
        if (is_array($ids))
        {
            $this->db->where_in('Id', $ids);
        }
        else
        {
            $this->db->where('Id', $ids);
        }
        return $this->db->update($this->table_name, $data);
    }


    /**
     * @function 任务筛选接口
     * @User: CaylaXu
     * @param $user_id
     * @param $params
     * @return mixed
     */
    public function get_task_by_params($user_id, $params)
    {
        $sql = "SELECT t.* FROM `Tasks` as t
                LEFT JOIN RltTaskUser as rtu on rtu.TaskId = t.Id
                WHERE rtu.Status > 0
                AND t.Status = 1";
        $sql_arr = array();

        if (isset($params['ProjectId']) && $params['ProjectId'] != -1)
        {
            $sql .= " AND t.ProjectId = ? ";
            $sql_arr[] = $params['ProjectId'];
        }
        else
        {
            $sql .= " AND t.Id not in (select ParentId from Tasks where Status=1) ";
        }

        if (isset($params['DirectorIds']))//选择了责任人筛选则其他筛选不生效
        {
            $sql .= ' AND rtu.UserId IN ? AND rtu.Type = 1 ';
            $sql_arr[] = $params['DirectorIds'];
        }
        else
        {
            if (isset($params['RltType']) && $params['RltType'] != -1)
            {
                if ($params['RltType'] == 'follower')//我关注的
                {
                    $sql .= " AND rtu.UserId = ? AND rtu.Type=3";
                    $sql_arr[] = $user_id;
                }
                else if($params['RltType'] == 'creator')//我创建的
                {
                    $sql .= " AND t.CreatorId = ? ";
                    $sql_arr[] = $user_id;
                }
                else if($params['RltType'] == 'finisher')//我完成的
                {
                    $sql .= " AND t.FinisherId = ? ";
                    $params['Progress'] = 100;
                    $sql_arr[] = $user_id;
                }
                else//指派给我的
                {
                    $sql.=" AND rtu.UserId = ? AND rtu.Type IN (1,2)";
                    $sql_arr[] = $user_id;
                }
            }
        }

        if (isset($params['Time']) && $params['Time'] != -1)
        {
            switch (strtolower($params['Time']))
            {
                case 'yesterday':
                    $params['StartDate'] = strtotime(date('Y-m-d', strtotime("-1 day")));
                    $params['DueDate'] = strtotime(date('Y-m-d', strtotime("today")));
                    break;
                case 'today':
                    $params['StartDate'] = strtotime(date('Y-m-d', strtotime("today")));
                    $params['DueDate'] = strtotime(date('Y-m-d', strtotime("+1 day")));
                    break;
                case 'tomorrow':
                    $params['StartDate'] = strtotime(date('Y-m-d', strtotime("+1 day")));
                    $params['DueDate'] = strtotime(date('Y-m-d', strtotime("+2 day")));
                    break;
                case 'thisweek':
                    $params['StartDate'] = mktime(0, 0, 0, date("m"), date("d") - date("w") + 1, date("Y"));
                    $params['DueDate'] = mktime(23, 59, 59, date("m"), date("d") - date("w") + 7, date("Y"));
                    break;
                case 'nextweek':
                    $params['StartDate'] = strtotime(date('Y-m-d', strtotime('+1 week last monday')));
                    $params['DueDate'] = strtotime(date('Y-m-d', strtotime('+2 week last monday')));
                    break;
                case 'thismonth':
                    $params['StartDate'] = mktime(0, 0, 0, date('m'), 1, date('Y'));
                    $params['DueDate'] = mktime(23, 59, 59, date('m'), date('t'), date('Y'));
                    break;
                case 'nextmonth':
                    $params['StartDate'] = strtotime(date('Y-m-01', time()) . ' +1 month');//每个月的开始日期肯定是1号
                    $params['DueDate'] = strtotime(date('Y-m-01', time()) . ' +2 month -1 day');
                    break;
                default:
                    $params['StartDate'] = -1;
                    $params['DueDate'] = -1;
            }
        }

        if (isset($params['StartDate']) && $params['StartDate'] != -1)
        {
            $sql .= " AND t.StartDate >= ? ";
            $sql_arr[] = $params['StartDate'];
        }

        if (isset($params['DueDate']) && $params['DueDate'] != -1)
        {
            $sql .= " and t.StartDate <= ?";
            $sql_arr[] = $params['DueDate'];
        }

        if (isset($params['Progress']) && $params['Progress'] != -1)
        {
            $sql .= " AND t.CompleteProgress = ? ";
            $sql_arr[] = $params['Progress'];
        }

        if (isset($params['Priority']) && $params['Priority'] != -1)
        {
            $sql .= " AND t.Priority = ? ";
            $sql_arr[] = $params['Priority'];
        }

        if (isset($params['Owner']) && $params['Owner'] != -1)
        {
            if (strtolower($params['Owner']) == 'creator')
            {
                $sql .= " AND t.CreatorId = ? ";
                $sql_arr[] = $user_id;
            }
        }

//        if (isset($params['DirectorIds']))
//        {
//            $sql .= ' AND rtu.UserId IN ? AND rtu.Type = 1 ';
//            $sql_arr[] = $params['DirectorIds'];
//        }

        $sql .= " GROUP BY t.Id ORDER BY t.StartDate";
        $query = $this->db->query($sql, $sql_arr);
        return $query->result_array();
    }

    public function paging_tasks_by_project($project_id, $page = 0, $limit = 15, $params = array())
    {
        $sql = "SELECT t.* FROM `Tasks` as t
                LEFT JOIN RltTaskUser as rtu on rtu.TaskId = t.Id
                WHERE t.Id not in (select ParentId from Tasks where Status = 1)
                AND t.ProjectId = ?
                AND t.Status = 1";

        $sql_arr = array($project_id);

        $sql .= " GROUP BY t.Id ORDER BY t.CompleteProgress,t.StartDate";
        $offset = $page * $limit;
        //        $count = $this->db->query($sql,$sql_arr)->num_rows();
        $sql .= " limit " . $offset . "," . $limit;
        $query = $this->db->query($sql, $sql_arr);
        $query = $query->result_array();
        return $query;
    }


    /**
     * @function 分页获取我的已办
     * @User: CaylaXu
     * @param int $page
     * @param int $limit
     * @param array $params
     * @return mixed
     */
    public function paging_get_task_done_by_user_id($page = 0, $limit = 15, $params = array())
    {
        $sql = "SELECT t.* FROM `Tasks` as t
                LEFT JOIN RltTaskUser as rtu on rtu.TaskId = t.Id
                WHERE t.Id not in (select ParentId from Tasks where Status=1)
                AND rtu.Type = 1
                AND rtu.UserId = ?
                AND rtu.Status > 0
                AND t.Status = 1
                AND t.CompleteProgress = 100";
        $sql_arr = array($params["UserId"]);
        $sql .= " GROUP BY t.Id ORDER BY t.StartDate desc";
        $offset = $page * $limit;
        $sql .= " limit " . $offset . "," . $limit;
        $query = $this->db->query($sql, $sql_arr);
        $query = $query->result_array();
        return $query;
    }


    public function paging_get_task_by_params($page, $limit, $params)
    {
        $sql = "SELECT t.* FROM `Tasks` as t
                LEFT JOIN RltTaskUser as rtu on rtu.TaskId = t.Id
                WHERE t.Id not in (select ParentId from Tasks where Status=1)
                AND rtu.UserId = ?
                AND rtu.Status > 0
                AND t.Status = 1";
        $sql_arr = array($params["UserId"]);

        if (isset($params['Time']) && $params['Time'] != -1)
        {
            switch (strtolower($params['Time']))
            {
                case 'yesterday':
                    $params['StartDate'] = strtotime(date('Y-m-d', strtotime("-1 day")));
                    $params['DueDate'] = strtotime(date('Y-m-d', strtotime("today")));
                    break;
                case 'today':
                    $params['StartDate'] = strtotime(date('Y-m-d', strtotime("today")));
                    $params['DueDate'] = strtotime(date('Y-m-d', strtotime("+1 day")));
                    break;
                case 'tomorrow':
                    $params['StartDate'] = strtotime(date('Y-m-d', strtotime("+1 day")));
                    $params['DueDate'] = strtotime(date('Y-m-d', strtotime("+2 day")));
                    break;
                case 'thisweek':
                    $params['StartDate'] = mktime(0, 0, 0, date("m"), date("d") - date("w") + 1, date("Y"));
                    $params['DueDate'] = mktime(23, 59, 59, date("m"), date("d") - date("w") + 7, date("Y"));
                    break;
                case 'nextweek':
                    $params['StartDate'] = strtotime(date('Y-m-d', strtotime('+1 week last monday')));
                    $params['DueDate'] = strtotime(date('Y-m-d', strtotime('+2 week last monday')));
                    break;
                case 'thismonth':
                    $params['StartDate'] = mktime(0, 0, 0, date('m'), 1, date('Y'));
                    $params['DueDate'] = mktime(23, 59, 59, date('m'), date('t'), date('Y'));
                    break;
                case 'nextmonth':
                    $params['StartDate'] = strtotime(date('Y-m-01', time()) . ' +1 month');//每个月的开始日期肯定是1号
                    $params['DueDate'] = strtotime(date('Y-m-01', time()) . ' +2 month -1 day');
                    break;
                default:
                    $params['StartDate'] = -1;
                    $params['DueDate'] = -1;
            }
        }

        if (isset($params['StartDate']) && $params['StartDate'] != -1)
        {
            $sql .= " AND t.StartDate >= ? ";
            $sql_arr[] = $params['StartDate'];
        }

        if (isset($params['DueDate']) && $params['DueDate'] != -1)
        {
            $sql .= " and t.StartDate <= ?";
            $sql_arr[] = $params['DueDate'];
        }
        $sql .= " AND t.CompleteProgress = 0 ";
        $sql .= " GROUP BY t.Id ORDER BY t.StartDate";
        $offset = $page * $limit;
        $sql .= " limit " . $offset . "," . $limit;
        $query = $this->db->query($sql, $sql_arr);
        return $query->result_array();
    }

    /**
     * @function 任务筛选接口
     * @User: CaylaXu
     * @param $user_id
     * @param $params
     * @return mixed
     */
    public function calendar_tasks_by_params($user_id, $params)
    {
        $sql = "SELECT t.*,rtu.Type FROM `Tasks` as t
                LEFT JOIN RltTaskUser as rtu on rtu.TaskId = t.Id
                WHERE t.Id not in (select ParentId from Tasks where Status=1)
                AND rtu.Status > 0
                AND t.Status = 1
                AND rtu.UserId = ?";
        $sql_arr = array($user_id);
        //类别筛选 1:我的待办 2：我的已办 3:我的关注
        if (isset($params['Type']) && !empty($params['Type']))
        {
            if (is_array($params['Type']))
            {
                //我的待办
                if (in_array(1, $params['Type']))
                {
                    $params['RltType'][] = 1;
                    $params['Progress'][] = 0;
                }

                //我的已办
                if (in_array(2, $params['Type']))
                {
                    $params['RltType'][] = 1;
                    $params['Progress'][] = 100;
                }

                //我的关注
                if (in_array(3, $params['Type']))
                {
                    $params['RltType'][] = 3;
                    $params['Progress'][] = 0;
                }
            }
        }

        //项目筛选
        if (isset($params['ProjectId']) && !empty($params['ProjectId']))
        {
            $project_ids = implode(',', $params['ProjectId']);
            $sql .= " AND t.ProjectId in (" . $project_ids . ")";
        }

        if (isset($params['StartDate']) && $params['StartDate'] != -1)
        {
            $sql .= " AND t.DueDate >= ? ";
            $sql_arr[] = $params['StartDate'];
        }

        if (isset($params['DueDate']) && $params['DueDate'] != -1)
        {
            $sql .= " and t.StartDate <= ?";
            $sql_arr[] = $params['DueDate'];
        }

        if (isset($params['RltType']) && !empty($params['RltType']) && is_array($params['RltType']))
        {
            $types = implode(',', $params['RltType']);
            $sql .= " AND rtu.Type in (" . $types . ") ";
        }

        if (isset($params['Progress']) && !empty($params['Progress']) && is_array($params['Progress']))
        {
            $progress = implode(',', $params['Progress']);
            $sql .= " AND t.CompleteProgress in (" . $progress . ") ";
        }
        $sql .= " GROUP BY t.Id";
        $query = $this->db->query($sql, $sql_arr);
        return $query->result_array();
    }

    /**
     * @function 获取当前id排序的下一条任务的详情
     * @User: CaylaXu
     * @param $project_id
     * @param $parent_id
     * @param $sort
     * @return mixed
     */
    public function get_next_sort_task($project_id, $parent_id, $sort)
    {
        $sql = "SELECT * FROM Tasks WHERE ProjectId = ? AND ParentId = ? AND Sort >= ? ORDER BY Sort LIMIT 1";
        $query = $this->db->query($sql, array($project_id, $parent_id, $sort));
        return $query->row_array();
    }

    /**
     * @function 统计项目下任务
     * @User: CaylaXu
     * @param $project_id 项目Id
     * @param $type 1:未完成 2：已完成 3：所有 4：过期任务 5：今天完成了几个任务 6：新建了几个任务 7：延期的
     * @return mixed
     */
    public function count_task_by_project($project_id, $type = '')
    {
        $sql = "SELECT count(Id) as Sum FROM `Tasks`
                WHERE  Id not in (select ParentId from Tasks where Status=1)
                AND ProjectId = ?
                AND Status = 1
                ";
        if ($type == 1)//未完成
        {
            $sql .= " AND CompleteProgress=0 ";
        }
        else if($type == 2)//已完成
        {
            $sql .= " AND CompleteProgress=100 ";
        }
        else if($type == 4)//过期任务
        {
            $sql .= " AND CompleteProgress=0 AND DueDate < ".time();
        }
        else if($type == 5)
        {
            $sql .= " AND CompleteProgress=100 AND TrueDueDate >=".strtotime("today")." AND TrueDueDate < ".strtotime("+1 day");
        }
        else if($type == 6)
        {
            $sql .= " AND CreateTime >=".strtotime("today")." AND CreateTime < ".strtotime("+1 day");
        }
        else if($type == 7)//延期的
        {
            $sql .= " AND ((CompleteProgress=0 AND DueDate < ".time().") OR TrueDueDate > DueDate)";
        }
        $query = $this->db->query($sql,array($project_id));
        return $query->row_array();
    }

    public function get_max_child_sort($id)
    {
        $this->db->select_max('Sort');
        $this->db->where('ParentId', $id);
        $this->db->where('Status', 1);
        $query = $this->db->get($this->table_name);
        return $query->row_array();
    }

    /**
     * @function 根据项目ID查出所有任务责任人
     * @author Peter
     * @param $project_id
     * @return mixed
     */
    public function get_directors_by_project_id($project_id)
    {
        $sql = "SELECT u.ID AS UserId, u.Name AS UserName, u.Avatar AS Avatar
                FROM Users AS u
                WHERE u.Id IN (
                    SELECT UserId
                    FROM RltTaskUser AS rtu
                    WHERE rtu.Type = 1
                    AND rtu.Status > 0
                    AND rtu.TaskId IN (
                      SELECT Id
                      FROM Tasks AS t
                      WHERE t.ProjectId = ?
                    )
                )";

        $query = $this->db->query($sql, array($project_id));

        return $query ? $query->result_array() : array();
    }

    /**
     * @function 获取当前任务
     * @User: CaylaXu
     */
    public function get_current_tasks($user_id)
    {
        $time = strtotime(date('Y-m-d', strtotime("+1 day")));
        $sql = "SELECT rtu.TaskId,rtu.Duration FROM `RltTaskUser` as rtu
                LEFT JOIN Tasks as t on rtu.TaskId = t.Id
                WHERE rtu.UserId = ? AND rtu.Status = 2
                AND t.Status = 1
                AND t.CompleteProgress = 0
                AND t.StartDate <= '{$time}' AND t.DueDate != 0
                AND t.Id not in (select ParentId from Tasks where Status=1)
                ";

        $query = $this->db->query($sql, array($user_id));

        return $query ? $query->result_array() : array();
    }

    /**
     * @function 根据user_id获取与之相关的任务ID
     * @author Peter
     * @param int $user_id
     * @param int $anchor
     * @return mixed
     */
    public function get_rlt_task_ids($user_id, $anchor = 0)
    {
        $sql = "SELECT TaskId FROM RltTaskUser as rtu
				LEFT JOIN Tasks as t on t.Id = rtu.TaskId
				WHERE (rtu.Modified > ? or t.Modified > ? ) AND rtu.UserId = ? AND TaskId != 0
                UNION
                SELECT DISTINCT t.Id FROM Tasks AS t
                LEFT JOIN RltProjectUser AS rpu ON rpu.ProjectId=t.ProjectId
                WHERE t.Modified > ? AND rpu.UserId = ? AND t.Id != 0";
        $bind = array($anchor, $anchor,$user_id, $anchor, $user_id);
        $query = $this->db->query($sql, $bind);
        return $query->result_array();
    }

    /**
     * @function 获取子任务ID
     * @author Peter
     * @param array $parent_ids
     * @param int $anchor
     * @return array
     */
    public function get_sub_task_ids($parent_ids = array(), $anchor = 0)
    {
        $sql = "SELECT Id FROM Tasks WHERE Modified > ? AND ParentId IN ?";
        $bind = array($anchor, $parent_ids);
        $query = $this->db->query($sql, $bind);

        return $query->result_array();
    }

    /**
     * @function 根据ID获取任务
     * @author Peter
     * @param $task_ids
     * @param $anchor
     * @return mixed
     */
    public function get_by_ids(array $task_ids, $anchor = 0)
    {
        $sql = "SELECT * FROM Tasks WHERE Modified > ? AND Id IN ?";
        $bind = array($anchor, $task_ids);
        $query = $this->db->query($sql, $bind);

        return $query->result_array();
    }


    /**
     * @function 获取今日相关任务包括今日待办今日关注和全部关注
     * @User: CaylaXu
     * @param $user_id
     * @param string $type
     * @return mixed
     */
    public function get_task_by_type($user_id,$type = '',$params = array())
    {
        $sql = "SELECT t.* FROM `Tasks` as t
                LEFT JOIN RltTaskUser as rtu on rtu.TaskId = t.Id
                WHERE t.Id not in (select ParentId from Tasks where Status=1)
                AND rtu.UserId = ?
                AND rtu.Status > 0
                AND t.Status = 1
                AND t.CompleteProgress = 0
                ";

        if(strtolower($type) == 'concerned')//关注
        {
            $sql .= ' AND rtu.Type = 3';
        }
        else
        {
            $sql .= ' AND rtu.Type in (1,2)';
        }

        $sql_arr = array($user_id);

        if(strtolower($type) == 'all')
        {
            $sql .= " AND t.StartDate !=0 AND t.DueDate != 0 GROUP BY t.Id ORDER BY t.StartDate";
            $page          = isset($params['Page']) ? intval($params['Page']) : 1;
            $limit         = isset($params['Rows']) ? intval($params['Rows']) : 20;
            $offset = ($page-1)*$limit;
            $sql .= " LIMIT ".$offset." ,".$limit;
        }
        else
        {
            $sql .= " AND t.StartDate < ? AND t.DueDate != 0 GROUP BY t.Id ORDER BY t.StartDate";
            $sql_arr[] = strtotime(date('Y-m-d', strtotime("+1 day")));
        }

        $query = $this->db->query($sql, $sql_arr);
        return $query->result_array();
    }

    /**
     * @function 获取我创建的任务按创建时间倒序
     * @User: CaylaXu
     * @param $user_id
     * @return mixed
     */
    public function get_task_by_creator_id($user_id,$params = array())
    {
        $page          = isset($params['Page']) ? intval($params['Page']) : 1;
        $limit         = isset($params['Rows']) ? intval($params['Rows']) : 20;
        $offset = ($page-1)*$limit;

        $sql = "SELECT t.* FROM `Tasks` as t
                WHERE t.Id not in (select ParentId from Tasks where Status=1)
                AND t.CreatorId = ?
                AND t.Status = 1
                ORDER BY t.CreateTime DESC";
        $sql .= " LIMIT ".$offset." ,".$limit;
        $sql_arr = array($user_id);
        $query = $this->db->query($sql, $sql_arr);
        return $query->result_array();
    }

    public function get_task_by_finisher_id($user_id,$type = '',$params =array())
    {
        $page          = isset($params['Page']) ? intval($params['Page']) : 1;
        $limit         = isset($params['Rows']) ? intval($params['Rows']) : 20;
        $offset = ($page-1)*$limit;

        if($type == 'concerned')
        {
            $sql = "SELECT t.* FROM `Tasks` as t
                    LEFT JOIN RltTaskUser as rtu ON rtu.TaskId = t.Id AND rtu.Status > 0
                    WHERE t.Id not in (select ParentId from Tasks where Status=1)
                    AND rtu.UserId = ?
                    AND rtu.Type = 3";
        }
        else
        {
            $sql = "SELECT t.* FROM `Tasks` as t
                WHERE t.Id not in (select ParentId from Tasks where Status=1)
                AND t.FinisherId = ?";
        }

        $sql .= " AND t.Status = 1 AND t.CompleteProgress = 100 ";

        $sql_arr = array($user_id);

        if(isset($params['TrueDueDate']) && !empty($params['TrueDueDate']))
        {
            //获取当天的年份
            $y = date("Y",$params['TrueDueDate']);
            $m = date("m",$params['TrueDueDate']);
            $d = date("d",$params['TrueDueDate']);
            $start= mktime(0,0,0,$m,$d,$y);
            $end = mktime(23, 59, 59, $m,$d,$y);
            $sql.= 'AND t.TrueDueDate>= ? AND t.TrueDueDate<= ?';
            $sql_arr[] = $start;
            $sql_arr[] = $end;
        }
        $sql .= " ORDER BY t.TrueDueDate DESC";

        if(strtolower($type) == 'all')
        {
            $page          = isset($params['Page']) ? intval($params['Page']) : 1;
            $limit         = isset($params['Rows']) ? intval($params['Rows']) : 20;
            $offset = ($page-1)*$limit;
            $sql .= " LIMIT ".$offset." ,".$limit;
        }
        $query = $this->db->query($sql, $sql_arr);
        return $query->result_array();
    }
}
