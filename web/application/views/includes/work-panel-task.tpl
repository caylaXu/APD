{capture assign="create"}
    <form action="/backend/task/create" method="post" class="ajax-form form-panel" data-success="AddTaskSuccess">
        {* {FormControl Title="任务名称" Name="Title" Required="1"} *}
        <dl class="form-group">
            <dt style="min-width: 10px;">
                <label class="checkbox demo demoDown" style="margin-top: 5px;" tip_Title="点击完成/撤销">
                    <input type="checkbox" name="Progress" class="single-checkbox">
                </label>
                <input type="hidden" name="CompleteProgress" value="0">
            </dt>
            {*<dt style="min-width: 10px;display: none;"></dt>*}
            <dd>
                <input type="text" style="padding: 0 26px 0px 10px;display: block;word-break:break-all;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;" name="Title" placeholder="输入任务名称">
                <label><span id="urgency_btn" typeBtn="0" class="checkbox checkbox-prior demo demoDown" tip_Title="优先处理" style="position: absolute;right: 5px;top: 8px;cursor:pointer;border:none;width: 12px;"><input type="checkbox" name="Priority" value="1" class="single-checkbox" style="cursor: pointer;"></span></label>
                {*<a href="#" class="collect-button"><span class="icon icon-collection"></span></a>*}
            </dd>
        </dl>
        {FormControl Title="添加描述" Name="Description" Type="textarea"}
        <div class="time-picker-group">
            {if isset($Date)  && $Date== "day"}
                {FormControl Title="开始日期" Name="StartDate" Class="date-picker" Attribute='data-minview="2" data-format="yyyy/mm/dd" data-myformat="YYYY/MM/DD"'}
                {FormControl Title="结束日期" Name="DueDate" Class="date-picker" Attribute='data-minview="2" data-format="yyyy/mm/dd" data-myformat="YYYY/MM/DD"'}
            {else}
                {FormControl Title="开始日期" Name="StartDate" Class="date-picker"}
                {FormControl Title="结束日期" Name="DueDate" Class="date-picker"}
            {/if}
            <a href="#" class="collect-button"><span class="iconfont icon-xiangziline demo demoDown" style="font-size:22.5px;color:#999;" tip_Title="将任务放入收集箱" ></span></a>
        </div>
        <div class="no-time-group hide" style="position: relative;">
            {FormControl Title="任务时间"  Value="已加入收集箱" Type="text" }
            <input type="hidden" Name="IsCollected" value="0">
            <a href="#" class="collect-button"><span class="icon icon-collection demo demoDown" tip_Title="撤销"></span></a>
        </div>
        <dl class="form-group">
            <dt style="color:#999;font-size:12px;">责&nbsp; 任&nbsp; 人</dt>
            <dd>
                {include "includes/user-selector.tpl" Name="AssignedTo" Target="#task-assigned-to-popover"}
            </dd>
        </dl>
        <dl class="form-group">
            <dt style="color: #999;font-size: 12px;padding-top: 10px;">关&nbsp; 注&nbsp; 人</dt>
            <dd>
                {include "includes/user-selector.tpl" Name="Follwers" Target="#task-follower-popover"}
            </dd>
        </dl>
        <dl class="form-group" style="margin-bottom: 0px;">
            {*<dt>紧急度：</dt>*}
            {*<dd>*}
                {*<label>*}
                    {*<span class="checkbox checkbox-prior">*}
                        {*<input type="checkbox" name="Priority" value="1" class="single-checkbox">*}
                    {*</span>*}
                    {*紧急*}
                {*</label>*}
            {*</dd>*}
        </dl>
        <dl class="form-group checklist-group">
            <dt style="color: #999;font-size: 12px;padding-top: 10px;">子&nbsp; 任&nbsp; 务</dt>
            <dd>
                <div class="item">
                    <input type="text" name="Checklist" class="form-control" placeholder="按Enter换行添加下一项">
                    <a href="#" class="icon icon-erase erase-btn demo demoDown" tip_Title="删除检查项"></a>
                </div>
            </dd>
        </dl>
        {FormControl Title="所属项目" Name="ProjectId" Type="select" Attribute="data-listener='MyProjects'"}
        <input type="hidden" name="IsMilestone" value="0">
        <input type="hidden" name="ParentId" value="0">
        <div class="params">
            <input type="hidden" name="CreatorId" value="{$UserId}">
        </div>
        {FormControl Type="submit"}
    </form>
{/capture}

{capture assign="dblcreate"}
    <form action="/backend/task/create" method="post" class="ajax-form form-panel" data-success="AddTaskSuccess">
        {* {FormControl Title="任务名称" Name="Title" Required="1"} *}
        <dl class="form-group">
            {*<dt></dt>*}
            <dd>
                <input type="text" name="Title" style="padding: 0 26px 0px 10px;display: block;word-break:break-all;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;" placeholder="请输入任务名称">
                <a href="#" class="collect-button"><span class="iconfont icon-xiangziline demo demoDown" style="font-size:22.5px;color:#999;" tip_Title="将任务放入收集箱"></span></a>
            </dd>
        </dl>
        <div class="time-picker-group">
            {FormControl Title="开始日期" Name="StartDate" Class="date-picker" Required="1"}
            {FormControl Title="结束日期" Name="DueDate" Class="date-picker" Required="1"}
        </div>
        <div class="no-time-group hide">
            {FormControl Title="任务时间" Value="已加入收集箱" Type="text" }
            <input type="hidden" Name="IsCollected" value="0">
        </div>

        {FormControl Title="所属项目" Name="ProjectId" Type="select" Attribute="data-listener='MyProjects'"}

        <dl class="form-group required" style="display:none;">
            <dt style="color: #999;font-size: 12px;">责&nbsp; 任&nbsp; 人</dt>
            <dd>
                {include "includes/user-selector.tpl" Name="AssignedTo" Target="#task-assigned-to-popover"}
            </dd>
        </dl>

        <input type="hidden" name="IsMilestone" value="0">
        <input type="hidden" name="ParentId" value="0">
        <input type="hidden" name="Follwers" value="">
        <input type="hidden" name="Checklist" value="">
        <input type="hidden" name="Description" value="">
        <input type="hidden" name="Priority" value="0">

        <div class="params">
            <input type="hidden" name="CreatorId" value="{$UserId}">
        </div>

        <div style="width: 100%;text-align: center;height: 40px;">
            <button type="submit" class="btn btn-blue btn-medium" style="display: inline-block;">确定</button>
            <button type="button" class="btn btn-medium" data-dismiss="work-panel" style="display: none;">取消</button>
        </div>
    </form>
{/capture}

{capture assign="edit"}
    <form action="/backend/task/edit" method="post" class="ajax-form form-panel instant-form" data-success="EditSuccess">
          {* {FormControl Title="任务名称" Name="Title" Required="1"} *}
          <dl class="form-group">
              <dt style="min-width: 0;">
                  <label class="checkbox demo demoDown" style="margin-top:5px;" tip_Title="点击完成/撤销">
                      <input type="checkbox" name="Progress" class="single-checkbox">
                  </label>
                  <input type="hidden" name="CompleteProgress" value="0">
              </dt>
              <dd>
                  <input type="text" name="Title" style="padding: 0 26px 0px 10px;font-size: 18px;color: #000;line-height: 38px;display: block;word-break:break-all;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;margin-left:5px;">
                  {*icon icon-urgency-h*}
                  <label><span id="urgency_btn" typeBtn="0" class="checkbox checkbox-prior demo demoDown" tip_Title="优先处理" style="position: absolute;right: 5px;top: 8px;cursor:pointer;border:none;width: 12px;"><input type="checkbox" name="Priority" value="1" class="single-checkbox" style="cursor: pointer;"></span></label>
                  {*<span class="shen_lufu" style="position: absolute;background-color: #fff;top: 9px;right: 22px;font-weight: bold;display: none;">...</span>*}
              </dd>
          </dl>
        {FormControl Title="添加描述" Name="Description" Type="textarea"}
        <div class="time-picker-group">
        {if isset($Date)  && $Date== "day"}
            {FormControl Title="开始日期" Name="StartDate" Class="date-picker" Attribute='data-minview="2" data-format="yyyy/mm/dd" data-myformat="YYYY/MM/DD"'}
            {FormControl Title="结束日期" Name="DueDate" Class="date-picker" Attribute='data-minview="2" data-format="yyyy/mm/dd" data-myformat="YYYY/MM/DD"'}
        {else}
            {FormControl Title="开始日期" Name="StartDate" Class="date-picker"}
            {FormControl Title="结束日期" Name="DueDate" Class="date-picker"}
        {/if}
            <a href="#" class="collect-button" {if isset($Date)  && $Date== "day"}taskType="task"{/if}><span class="iconfont icon-xiangziline demo demoDown" style="font-size:22.5px;color:#999;" tip_Title="将任务放入收集箱"></span></a>
        </div>
        <div class="no-time-group hide" style="position: relative;">
            {FormControl Title="任务时间"  Value="已加入收集箱" Type="text"}
            <input type="hidden" Name="IsCollected" value="0">
            <a href="#" class="collect-button"><span class="icon icon-collection demo demoDown" tip_Title="撤销"></span></a>
        </div>
        <dl class="form-group">
            <dt style="color:#999;font-size:12px;">责&nbsp; 任&nbsp; 人</dt>
            <dd>
                {include "includes/user-selector.tpl" Name="AssignedTo" Target="#task-assigned-to-popover"}
            </dd>
        </dl>
        <dl class="form-group">
            <dt style="color: #999;font-size: 12px;padding-top: 10px;">关&nbsp; 注&nbsp; 人</dt>
            <dd>
                {include "includes/user-selector.tpl" Name="Follwers" Target="#task-follower-popover"}
            </dd>
        </dl>
        <dl class="form-group" style="margin-bottom: 0px;">
            {*<dt>紧急度：</dt>*}
            {*<dd>*}
                {*<label>*}
                    {*<span class="checkbox checkbox-prior"><input type="checkbox" name="Priority" value="1"*}
                                                                 {*class="single-checkbox"></span>*}
                    {*紧急*}
                {*</label>*}
            {*</dd>*}
        </dl>
        <dl class="form-group checklist-group">
            <dt style="color: #999;font-size: 12px;padding-top: 10px;">子&nbsp; 任&nbsp; 务</dt>
            <dd>
                <dl class="item">
                    <dt><label class="checkbox"><input type="checkbox"></label></dt>
                    <dd>
                        <input type="text" name="Checklist" class="form-control" placeholder="按Enter换行添加下一项">
                        <a href="#" class="icon icon-erase erase-btn demo demoDown" tip_Title="删除检查项"></a>
                    </dd>
                </dl>
            </dd>
        </dl>
        {FormControl Title="所属项目" Name="ProjectId" Type="select" Attribute="data-listener='MyProjects'"}
        <dl class="form-group">
            <p class="p_old_title">操作日志</p>
            <div class="history_box" style="overflow:hidden;position:relative;">
                <img id="loading" src="../../../resource/asset/img/loading.gif" style="margin-left:180px;">
                <div id="history_box_cont">

                </div>
            </div>
        </dl>
        <input type="hidden" name="IsMilestone" value="0">
        <div class="params">
            <input type="hidden" name="Id">
        </div>
    </form>
{/capture}

{capture assign="editcalendar"}
    <form action="/backend/task/edit" method="post" class="ajax-form form-panel instant-form" data-success="EditSuccess">
        <dl class="form-group required">
            <dt style="min-width: 0;">
                <label class="checkbox demo demoDown" tip_Title="点击完成/撤销" style="margin-top:5px;">
                    <input type="checkbox" name="Progress" class="single-checkbox">
                </label>
                <input type="hidden" name="CompleteProgress">
            </dt>
            <dd>
                <input type="text" class="form-control" name="Title" style="padding: 0 26px 0px 10px;font-size: 18px;color: #000;line-height: 38px;display: block;word-break:break-all;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;margin-left:5px;">
                <label><span id="urgency_btn" typeBtn="0" class="checkbox checkbox-prior demo demoDown" tip_Title="优先处理" style="position: absolute;right: 5px;top: 8px;cursor:pointer;border:none;width: 12px;"><input type="checkbox" name="Priority" value="1" class="single-checkbox" style="cursor: pointer;"></span></label>
                {*<span class="shen_lufu" style="position: absolute;background-color: #fff;top: 9px;right: 22px;font-weight: bold;display: none;">...</span>*}
            </dd>
        </dl>

        {FormControl Title="添加描述" Name="Description" Type="textarea"}
        <div class="time-picker-group">
            {FormControl Title="开始日期" Name="StartDate" Class="date-picker"}
            {FormControl Title="结束日期" Name="DueDate" Class="date-picker"}
            <a href="#" class="collect-button"><span class="iconfont icon-xiangziline demo demoDown" style="font-size:22.5px;color:#999;" tip_Title="将任务放入收集箱"></span></a>
        </div>
        <div class="no-time-group hide" style="position: relative;">
            {FormControl Title="任务时间"  Value="已加入收集箱" Type="text" }
            <input type="hidden" Name="IsCollected" value="0">
            <a href="#" class="collect-button"><span class="icon icon-collection demo demoDown" tip_Title="撤销"></span></a>
        </div>
        <dl class="form-group">
            <dt style="color: #999;font-size: 12px;">责&nbsp; 任&nbsp; 人</dt>
            <dd>
                {include "includes/user-selector.tpl" Name="AssignedTo" Target="#task-assigned-to-popover"}
            </dd>
        </dl>
        <dl class="form-group">
            <dt style="color: #999;font-size: 12px;padding-top: 10px;">关&nbsp; 注&nbsp; 人</dt>
            <dd>
                {include "includes/user-selector.tpl" Name="Follwers" Target="#task-follower-popover"}
            </dd>
        </dl>
        <dl class="form-group" style="margin-bottom: 0px;">
            {*<dt>紧急度：</dt>*}
            {*<dd>*}
                {*<label>*}
                    {*<span class="checkbox checkbox-prior">*}
                        {*<input type="checkbox" name="Priority" value="1" class="single-checkbox">*}
                    {*</span>紧急*}
                {*</label>*}
            {*</dd>*}
        </dl>
        <dl class="form-group checklist-group">
            <dt style="color: #999;font-size: 12px;padding-top: 10px;">子&nbsp; 任&nbsp; 务</dt>
            <dd>
                <dl class="item">
                <dt><label class="checkbox"><input type="checkbox"></label></dt>
            <dd>
                <input type="text" name="Checklist" class="form-control" placeholder="按Enter换行添加下一项">
                <a href="#" class="icon icon-erase erase-btn demo demoDown" tip_Title="删除检查项"></a>
            </dd>
        </dl>
        </dd>
        </dl>
        {FormControl Title="所属项目" Name="ProjectId" Type="select" Attribute="disabled data-listener='Projects'"}
        <input type="hidden" name="IsMilestone" value="0">
        <div class="params">
            <input type="hidden" name="Id">
        </div>
    </form>
{/capture}

{capture assign="isMilestone"}
    <label class="is-milestone">
    <span class="checkbox freestyle">
        <span class=" icon icon-milestone-view orange"></span>
        <input type="checkbox" value="1">
    </span>
        <span class="text">标记为里程碑</span>
    </label>
{/capture}

{capture assign="view"}
    <form action="/backend/task/edit" method="post" class="ajax-form form-panel instant-form readonly"
          data-success="EditSuccess">
          {* {FormControl Title="任务名称" Name="Title" Required="1"} *}
          <dl class="form-group">
              <dt style="min-width: 0;">
                  <label class="checkbox" style="margin-top:5px;">
                      {*<input type="checkbox" name="Progress" class="single-checkbox" readonly="true">*}
                  </label>
                  <input type="hidden" name="CompleteProgress" value="0">
              </dt>
              <dd>
                  <input type="text" name="Title" readonly="true" style="padding: 0 26px 0px 10px;font-size: 18px;color: #000;line-height: 38px;display: block;word-break:break-all;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;margin-left:5px;">
                  <label>
                      <span id="urgency_btn" typeBtn="0" class="checkbox checkbox-prior demo demoDown" tip_Title="优先处理" style="position: absolute;right: 5px;top: 8px;cursor:pointer;border:none;width: 12px;">
                          {*<input type="checkbox" name="Priority" value="1" class="single-checkbox" style="cursor: pointer;">*}
                      </span>
                  </label>
                  {*<span class="shen_lufu" style="position: absolute;background-color: #fff;top: 9px;right: 22px;font-weight: bold;display: none;">...</span>*}
              </dd>
          </dl>
        {FormControl Title="添加描述" Name="Description" Type="text"}
        <div class="time-picker-group">
            {FormControl Title="开始日期" Name="StartDateString" Type="text"}
            {FormControl Title="结束日期" Name="DueDateString" Type="text"}
            {*class="collect-button"*}
            <a href="#" style="position:absolute;top:8px;right:8px;">
                <span class="iconfont icon-xiangziline" style="font-size:22.5px;color:#999;"></span>
            </a>
        </div>
        <div class="no-time-group hide" style="position: relative;">
            {FormControl Title="任务时间"  Value="已加入收集箱" Type="text"}
            <input type="hidden" Name="IsCollected" value="0">
            <a href="#" class="collect-button">
                <span class="icon icon-collection"></span>
            </a>
        </div>
        <dl class="form-group">
            <dt style="color: #999;font-size: 12px;padding-top: 10px;">责&nbsp; 任&nbsp; 人</dt>
            <dd>
                {include "includes/user-selector.tpl" Name="AssignedTo" Target="#task-assigned-to-popover" Class="readonly"}
            </dd>
        </dl>
        <dl class="form-group">
            <dt style="color: #999;font-size: 12px;padding-top: 10px;">关&nbsp; 注&nbsp; 人</dt>
            <dd>
                {include "includes/user-selector.tpl" Name="Follwers" Target="#task-follower-popover" Class="readonly"}
            </dd>
        </dl>
        <dl class="form-group" style="margin-bottom: 0px;">
            {*<dt>紧急度：</dt>
            <dd>
                <label>
                    <span class="checkbox checkbox-prior"><input type="checkbox" name="Priority" value="1"
                                                                 class="single-checkbox" disabled></span>
                    紧急
                </label>
            </dd>*}
        </dl>
        <dl class="form-group checklist-group readonly">
            <dt style="color: #999;font-size: 12px;padding-top: 10px;">子&nbsp; 任&nbsp; 务</dt>
            <dd>
                <dl class="item">
                    <dt><label class="checkbox"><input type="checkbox" disabled></label></dt>
                    <dd>
                        <span class="form-control-text" name="Checklist"></span>
                        <a href="#" class="icon icon-erase erase-btn demo demoDown" tip_Title="删除检查项"></a>
                    </dd>
                </dl>
            </dd>
        </dl>
        {FormControl Title="所属项目" Name="ProjectId" Type="text"}
        <dl class="form-group">
            <p class="p_old_title">操作日志</p>
            <div class="history_box" style="overflow:hidden;position:relative;">
                <img id="loading" src="../../../resource/asset/img/loading.gif" style="margin-left:180px;">
                <div id="history_box_cont">

                </div>
            </div>
        </dl>
        <input type="hidden" name="IsMilestone" value="0" disabled>
        <div class="params">
            <input type="hidden" name="Id">
        </div>
    </form>
{/capture}
{capture assign="delBtn"}
    <a href="#" class="delete-btn hover-item pull-right" style="margin-right: 40px;"><span class="icon icon-delete-red"></span></a>
{/capture}
{WorkPanel Title="新建任务" Buttons=$isMilestone Id="new-task" Content=$create }
{WorkPanel Title="查看任务" Buttons=$isMilestone DeleteButton=$delBtn Id="edit-task" Content=$edit }

{WorkPanel Title="新建任务" Id="dbl-new-task" Content=$dblcreate }
{WorkPanel Title="查看任务" Buttons=$isMilestone DeleteButton=$delBtn Id="edit-task-calendar" Content=$editcalendar }

{WorkPanel Title="查看任务" Buttons=$isMilestone Id="view-task" Content=$view }

{*1:项目经理 2：项目成员 3;项目关注人*}
{include "includes/popover-user-selector.tpl" Title="责任人" Id="task-assigned-to-popover" Type="1" Url="/backend/task/add_rlt_user"}
{include "includes/popover-user-selector.tpl" Title="关注人" Id="task-follower-popover" Type="3" Url="/backend/task/add_rlt_user"}
{include "includes/popover-user-selector.tpl" Title="导入合作过的成员" Id="task-importUser-popover" Type="2" Url="/backend/task/add_rlt_user"}
<!--旧版 导入合作过的人-->
{*{include "includes/popover-user-import.tpl" Title="导入合作过的成员" Id="task-importUser-popover" Type="1" Url="/backend/task/add_rlt_user"}*}
<!--二维码-->
{include "includes/popover-user-qrcode.tpl" Id="task-user-qrCode" Type="1" Url="/backend/task/add_rlt_user"}
