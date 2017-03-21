
{capture assign="create"}
<form action="/backend/project/create" method="post" class="ajax-form form-panel" data-success="AddProjectSuccess">
    {FormControl Title="项目名称" Name="Title" Required="1" Attribute="maxlength=20"}
    {FormControl Title="项目描述" Name="Description" Type="textarea"}
    <div class="time-picker-group">
        {FormControl Title="开始日期" Name="StartDate" Class="date-picker" Required="1" Attribute='data-minview="2" data-format="yyyy/mm/dd" data-myformat="YYYY/MM/DD"'}
        {FormControl Title="结束日期" Name="DueDate" Class="date-picker" Required="1" Attribute='data-minview="2" data-format="yyyy/mm/dd" data-myformat="YYYY/MM/DD"'}
    </div>
    <dl class="form-group required">
        <dt style="font-size: 12px;color: #999;">项目经理</dt>
        <dd>
            {include "includes/user-selector.tpl" Name="ProjectManagerId" Target="#project-manager-popover"}
        </dd>
    </dl>
    <dl class="form-group">
        <dt style="font-size:12px;color:#999;padding-top:10px;">关&nbsp; 注&nbsp; 人</dt>
        <dd>
            {include "includes/user-selector.tpl" Name="Follwers" Target="#project-follower-popover"}
        </dd>
    </dl>
    <div class="params">
        <input type="hidden" name="CreatorId" value="{$UserId}">
        <input type="hidden" name="ParentId" value="{$ProjectId|default:0}">
    </div>
    {FormControl Type="submit"}
</form>
{/capture}

{capture assign="edit"}
    <form action="/backend/project/edit" method="post" class="ajax-form form-panel instant-form" data-success="EditSuccess">
        {FormControl Title="项目名称" Name="Title" Required="1" Attribute="maxlength=20"}
        {FormControl Title="项目描述" Name="Description" Type="textarea"}
        <div class="time-picker-group">
            {FormControl Title="开始日期" Name="StartDate" Class="date-picker" Required="1" Attribute='data-minview="2" data-format="yyyy/mm/dd" data-myformat="YYYY/MM/DD"'}
            {FormControl Title="结束日期" Name="DueDate" Class="date-picker" Required="1" Attribute='data-minview="2" data-format="yyyy/mm/dd" data-myformat="YYYY/MM/DD"'}
        </div>
        <dl class="form-group required">
            <dt style="font-size: 12px;color: #999;">项目经理</dt>
            <dd>
                {include "includes/user-selector.tpl" Name="ProjectManagerId" Target="#project-manager-popover"}
            </dd>
        </dl>
        <dl class="form-group">
            <dt style="font-size:12px;color:#999;padding-top:10px;">关&nbsp; 注&nbsp; 人</dt>
            <dd>
                {include "includes/user-selector.tpl" Name="Follwers" Target="#project-follower-popover"}
            </dd>
        </dl>
        <div class="params">
            <input type="hidden" name="Id" >
        </div>
    </form>
{/capture}


{capture assign="mainProject"}
    <form action="/backend/project/edit" method="post" class="ajax-form form-panel instant-form" data-success="EditSuccess">
        {FormControl Title="项目名称" Name="Title" Required="1" Attribute="maxlength=20"}
        {FormControl Title="项目描述" Name="Description" Type="textarea" Attribute="disabled"}
        <div class="time-picker-group">
            {FormControl Title="开始日期" Name="StartDate" Class="date-picker" Required="1" Attribute='data-minview="2" data-format="yyyy/mm/dd" data-myformat="YYYY/MM/DD" disabled'}
            {FormControl Title="结束日期" Name="DueDate" Class="date-picker" Required="1" Attribute='data-minview="2" data-format="yyyy/mm/dd" data-myformat="YYYY/MM/DD" disabled'}
        </div>
        <dl class="form-group required">
            <dt style="font-size: 12px;color: #999;">项目经理</dt>
            <dd>
                {include "includes/user-selector.tpl" Name="ProjectManagerId" Target="#project-manager-popover" Class="readonly"}
            </dd>
        </dl>
        <dl class="form-group">
            <dt style="font-size: 12px;color: #999;">关&nbsp; 注&nbsp; 人</dt>
            <dd>
                {include "includes/user-selector.tpl" Name="Follwers" Target="#project-follower-popover" Class="readonly"}
            </dd>
        </dl>
        <div class="params">
            <input type="hidden" name="Id" >
        </div>
    </form>
{/capture}


{capture assign="member"}
    <form action="/backend/project/update_members" method="post" class="ajax-form form-panel" data-success="EditSuccess">

        {include "includes/user-selector.tpl" Name="MemberIds" Target="#project-member-popover" tpl="avatar-edit-tpl-user"}

        <div class="params">
            <input type="hidden" name="ProjectId" >
        </div>

        {FormControl Type="submit"}

    </form>
{/capture}

{WorkPanel Title="新建项目" Id="new-project" Content=$create}
{WorkPanel Title="查看项目" Id="edit-project" Content=$edit}
{WorkPanel Title="查看项目" Id="edit-main-project" Content=$mainProject}

{WorkPanel Title="项目成员" Id="project-member" Content=$member}

{include "includes/popover-user-selector.tpl" Title="项目经理" Id="project-manager-popover" Type="1" Url="/backend/project/add_rlt_user" Limit="1"}
{include "includes/popover-user-selector.tpl" Title="关注人" Id="project-follower-popover" Type="3" Url="/backend/project/add_rlt_user"}
{include "includes/popover-user-selector.tpl" Title="项目成员" Id="project-member-popover" Type="2" Url="/backend/project/add_rlt_user"}
