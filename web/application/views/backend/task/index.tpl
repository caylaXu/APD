{extends "layouts/layout.tpl"}
{block styles}
<style media="screen">
    .tree-view{
        -webkit-user-drag: none;
    }
    .tree-view .h-line, .tree-view .node-collapse, .tree-view .v-line{
        z-index: 1;
    }
    .tree-view .checkbox{
        background-color: white;
        z-index: 2;
    }
    .panel.panel-blue{
        border-top: none;
        background-color: transparent;
    }

    .panel ul li {
        background-color: #fff;
        margin-bottom: 1px;
        -webkit-border-radius: 5px;
        -moz-border-radius: 5px;
        border-radius: 5px;
    }
    #viewport{
        padding-bottom: 200px;
    }

    #project-member .avatar .delete,
    #sift-panel .avatar .delete{
        display: none;
    }
    #sift-panel .avatar{
        overflow: hidden;
    }
    .avatar .mask{
        border-radius: 100%;
        background: rgba(0, 0, 0, 0.5);
        text-align: center;
        opacity: 0;
        transition: all 0.2s;
    }
    .avatar.selected .mask{
        opacity: 1;
    }
    .project-card{
        margin: 0px 10px 30px 0px;
    }
    .project-card canvas{
        cursor: pointer;
    }
    .no-data{
        text-align: center;
        padding-top: 10px;
    }
    .no-data .add-task-input {
        padding-right: 50px;
    }
    .no-data .add-task-input input {
        background: white;
        height: 50px;
        padding: 0 1em;
        outline: none;
    }
    .add-task-button {
        display: inline-block;
        width: 600px;
        height: 50px;
        line-height: 50px;
        background: rgba(0, 0, 0, 0.3);
        color: #fff;
        transition: .3s;
        border-radius: 5px;
        font-size: 18px!important;
    }
    .btn_qd_click{
        -webkit-border-radius: 5px;
        -moz-border-radius: 5px;
        border-radius: 5px;
        background: #2BA1D8;
        width: 120px;
        height: 35px;
        line-height: 35px;
        color: #fff;
        font-size: 14px;
        outline: none;
        border: 0px;
    }
    .dialog .inner{
        height: 180px;
    }
    body{
        overflow-y:auto;
    }
    .panel li.main-task{
        min-width: 1130px;
    }
    .form-panel .time-picker-group .required:last-child dt:before{
        content: '';
    }
    .form-panel .required dt:before{
        content: '';
    }
    div.mini .dialogD{
        position: absolute;
        top: 0px;
        left: 0px;
        right: 0px;
        bottom: 0px;
        background: #000\9;
        opacity: 0.7\9;
        filter:alpha(opacity=70);
        -moz-opacity:0.7;
        -khtml-opacity: 0.7;
        background: rgba(0, 0, 0, 0.7);
        border-radius: 5px 5px 0px 0px;
        color: #fff;
        z-index: 5;
    }
    div.pull-right{
        margin-top:-50px;
    }
</style>
{/block}

{*{block header}*}
{*<span class="title">{$ProjectName}</span>*}
{*<span class="title hide"><a href="javascript:siftMode.Exit()">{$ProjectName}</a> - 筛选</span>*}
{*<a href="/backend/milestone?ProjectId={$ProjectId}" class="btn btn-orange btn-small"><span class="icon icon-milestone-white"></span>里程碑</a>*}
{*<span class="pull-right">*}
    {*{if $Permission==1}<a class="btn btn-blue" href="#new-task" data-toggle="work-panel">新建任务</a>{/if}*}
    {*<a class="btn btn-orange" href="#sift-panel" data-toggle="work-panel">筛选</a>*}
{*</span>*}
{*{/block}*}

{block menuTitle}<a href="/backend/project/overview">项目</a>{/block}
{block leftMenu}
    {if !empty($ParentProject)}
        <span class="title active" style="height: 50px;overflow:hidden;border: 0px;">
            <a href="/backend/task/task_tree?ProjectId={$ParentProject.Id}">{$ParentProject.Title|truncate:15}</a>
        </span>
        <span style="float:left;height: 50px;line-height:50px;overflow:hidden;border: 0px;color:#fff;">
            <i class="iconfont icon-jiantou" style="font-size:12px;padding-top:2px;"></i>
        </span>
        <span class="title active" style="border-left: 0px;height: 50px;overflow:hidden;">
            <a href="/backend/task/task_tree?ProjectId={$ProjectId}">{if !empty($ProjectName)}{$ProjectName|truncate:15}{else}项目名称{/if}</a>
        </span>
    {else}
        <span class="title active" style="border-left: 0px;height: 50px;overflow:hidden;">
            <a href="/backend/task/task_tree?ProjectId={$ProjectId}">{if !empty($ProjectName)}{$ProjectName|truncate:15}{else}项目名称{/if}</a>
        </span>
    {/if}
    <span class="tabs pull-left" style="display:none;">
        <a href="#" style="color:{if !empty($UserTheme)}{$UserTheme.Color}{else}{'#13427c;'}{/if};background: #fff;">列表</a>
        <a href="/backend/milestone?ProjectId={$ProjectId}">里程碑</a>
    </span>
    <div class="tab_nav">
        <a href="/backend/task/task_tree?ProjectId={$ProjectId}">
            <span class="selcted_span">任务</span>
        </a>
        <a href="/backend/milestone?ProjectId={$ProjectId}">
            <span>里程碑</span>
        </a>
        <a href="/backend/project/statistics?Id={$ProjectId}">
            <span>统计</span>
        </a>
        <a href="/backend/project/calendar?Id={$ProjectId}">
            <span>日历</span>
        </a>
    </div>
{/block}
{*<span class="icon-new-task-fff icon"></span>新建任务<span class="icon-dropdown-fff icon"></span>更多
<span class="icon-filter-small icon"></span>筛选<span class="icon-layer icon"></span>子项目<span class="icon-users icon"></span>项目成员*}
{block rightMenu}
    {if $Permission==1}
        <a class="submenu" href="#new-task" data-toggle="work-panel">
            <i class="iconfont icon-xinjian" style="font-size:19px;margin-right:10px;"></i>新建任务
        </a>
    {/if}
    <span class="submenu dropdown right-menu-more">
        <a href="#" data-toggle="dropdown" data-trigger="hover">更多
            <i class="iconfont icon-xiala" style="font-size: 19px;margin-top:3px;"></i>
        </a>
        <div class="dropdown-menu" style="color: #666;">
            <a class="menu-item" href="#sift-panel" data-toggle="work-panel">
                <i class="iconfont icon-shaixuan" style="font-size:22px;margin-right:10px;"></i>筛选
            </a>
            {if $ProjectParentId==0}
                <a class="menu-item" href="#new-project" data-toggle="work-panel">
                    <i class="iconfont icon-xiangmu" style="font-size: 20px;margin-right:10px;"></i>新建子项目
                </a>
            {/if}
            <a class="menu-item" href="#project-member" data-toggle="work-panel">
                <i class="iconfont icon-chengyuan" style="font-size: 20px;margin-right: 10px;"></i>项目成员
            </a>
            </ul>
        </div>
    </span>
{/block}

{*{block bg}*}
    {*<div class="background">*}
    {*</div>*}
{*{/block}*}

{block content }

    <div class="row project-container">
        {foreach $Childs as $i => $v}
            <div class="project-card mini item" data-access="{$v.power}" data-src="{if $v.power==0}{else}/backend/task/task_tree?ProjectId={$v.Id}{/if}" data-refer="DataBase.ChildProject[{$i}]" data-tpl="#mini-project-tpl">
                <dl class="fixed">
                    <dd style="width:100px;padding-left:20px;">
                        <input id="new_projectId" type="hidden" value="{$v.Id}">
                        <canvas data-value="{$v.CompleteProgress|default:0}" width="70" height="70"></canvas>
                    </dd>
                    <dd class="v-top" style="padding-right:30px;">
                        <p class="title text-nowrap"><a href="{if $v.power==0}{else}/backend/task/task_tree?ProjectId={$v.Id}{/if}">{$v.Title}</a></p>
                        <div>
                            <div class="avatar"><img src="{$v.Avatar}" class="demo demoDown" tip_Title="{$v.ProjectManager}" alt=""></div>
                        </div>
                    </dd>
                </dl>
                <div class="buttons">
                    <a href="#edit-project" data-toggle="work-panel" class="edit-btn demo demoDown" tip_Title="编辑">
                        {*<span class="icon icon-edit"></span>*}
                        <i class="iconfont icon-miaoshu" style="font-size:18px;"></i>
                    </a>
                    <a href="/backend/statistics?ProjectId={$v.Id}" class="demo demoDown" tip_Title="报表">
                        {*<span class="icon icon-statistic"></span>*}
                        <i class="iconfont icon-baobiao" style="font-size:20px;"></i>
                    </a>
                    <a href="/backend/milestone?ProjectId={$v.Id}" class="demo demoDown" tip_Title="里程碑">
                        <span class="icon icon-milestone"></span>
                    </a>
                    <a href="#" class="delete-btn absolute top right demo demoDown" style="top:10px;right:10px;" tip_Title="删除">
                        <i class="iconfont icon-shanchu" style="font-size:27px;"></i>
                        {*<span class="icon icon-delete"></span>*}
                    </a>
                </div>
                <div class="row time-range">
                    <span class="start-time pull-left" timeval="{$v.StartDate}">{$v.StartDateString}</span>
                    <span class="end-time pull-right" timeval="{$v.DueDate}">{$v.DueDateString}</span>
                </div>
                <div class="progress">
                    <div class="bar" style="width:{$v.DateProgress}%;background-color: #00bb41;"></div>
                </div>
                {if $v.power==0}
                    <div class="dialogD" style="display: none;">
                        <div><i class="iconfont icon-jinzhi" style="font-size:70px;margin-top:18px;"></i></div>
                        <div style="margin-top:20px;letter-spacing:2px;">暂无权限</div>
                    </div>
                {/if}
            </div>
        {/foreach}
    </div>
{*
    {function Item Data='' Refer='' SubTask='main-task'}
    <li class="open {$SubTask}" {if $SubTask=="main-task"}draggable=true{/if}>
        <div class="item" data-refer="{$Refer}" data-tpl="#treeview-tpl">
            <dl>
                <dt><div class="checkbox {if $Data.CompleteProgress==100}checked{/if}"><input type="checkbox" {if $Data.CompleteProgress==100}checked{/if} {if $Permission!=1}disabled{/if}></div></dt>
                <dd>
                    <a href="{if $Permission==1}#edit-task{else}#view-task{/if}" data-toggle="work-panel" class="edit-btn">{$Data.Title}</a>
                     - {$Data.CompleteProgress}%
                    {if $v.Priority==1}<span class="icon icon-priority"></span>{/if}

                </dd>
                <dt>
                    {if $Permission==1}
                        <a title="子任务" class="hover-item add-sub-task"  href="#new-sub-task" data-toggle="work-panel"><span class="icon icon-sub-task"></span></a>
                        <a title="前移" href="#" class="move-left-btn hover-item"><span class="icon icon-move-left"></span></a>
                        <a title="后移" href="#" class="move-right-btn hover-item"><span class="icon icon-move-right"></span></a>
                        <a title="删除" href="#" class="delete-btn hover-item"><span class="icon icon-delete"></span></a>
                    {/if}
                    <span class="list-avatar-wrap">
                    {foreach $Data.RltUser as $i => $v}
                    <div class="avatar"><img src="{$v.Avatar}" title="{$v.UserName}" alt=""></div>
                    {/foreach}
                    </span>
                    <span class="time">{$Data.StartDateString} - <span class="orange">{$Data.DueDateString}</span></span>
                </dt>
            </dl>
        </div>
        {if isset($Data.Child)}
        <ul>
            {foreach $Data.Child as $i => $v}
            {Item Data=$v Refer=$Refer|cat:".Child["|cat:$i|cat:"]" SubTask="sub-task"}
            {/foreach}
        </ul>
        {/if}

    </li>
    {/function} *}
    <div class="panel panel-blue task-panel">
		<div class="list tree-view task-list" id="sort-list">
			<ul></ul>
            {*<div class="no-data {if count($Tasks)>0||count($Childs)>0}hide{/if}" style="color: #ffffff;">*}
                {*<div class="director">*}
    				{*<img src="/resource/asset/img/nodata.png" alt="" />*}
    				{*<p class="text bold">这里还没有东西，快去创建任务吧~</p>*}
    				{*<p class="link">*}
    					{*<a href="#new-task" data-toggle="work-panel">创建任务 >></a>*}
    				{*</p>*}
    			{*</div>*}
            {*</div>*}
            <div class="no-data {if count($Tasks)>0||count($Childs)>0}hide{/if}">
                <a href="javascript:void(0);" class="add-task-button">
                    <span class="icon icon-plus-white text-head"></span>新建任务
                </a>
                <div class="add-task-input hide">
                    <form action="/backend/task/create" method="post" class="ajax-form form-panel" data-success="AddTaskSuccess">
                        <input type="text" name="Title" placeholder="请输入任务内容并按Enter结束">
                        <input type="hidden" name="Description" value="">
                        <input type="hidden" name="ProjectId" value="{$ProjectId}">
                        <input type="hidden" name="ProjectUniCode" value="{$ProjectUniCode}">
                        <input type="hidden" name="StartDate" value="{$smarty.now}">
                        <input type="hidden" name="DueDate" value="{$smarty.now + 3600}">
                        <input type="hidden" name="Checklist" value="">
                        <input type="hidden" name="ParentId" value="0">
                        <input type="hidden" name="IsMilestone" value="0">
                        <input type="hidden" name="CreatorId" value="{$UserId}">
                        <input type="hidden" name="AssignedTo" value="{$UserId}">
                        <input type="hidden" name="Follwers" value="">
                    </form>
                </div>
            </div>
		</div>
	</div>
    <div class="panel panel-blue sift-panel hide">
        <div class="list task-list">
            <ul>
                {* 没有结果 *}
            </ul>
        </div>
    </div>

{include "includes/work-panel.tpl" }
{include "includes/work-panel-task.tpl" Date='day'}
{include "includes/work-panel-sift-task.tpl" }
{include "includes/work-panel-project.tpl" }
{/block}

{block setting}
<script type="text/javascript">
    DataBase.Task = {$Tasks|@json_encode};
    DataBase.ProjectId = "{$ProjectId}";
    DataBase.ProjectName = "{$ProjectName}";
    DataBase.ChildProject = {$Childs|@json_encode};
    DataBase.Directors = {$Directors|default:[]|@json_encode};
</script>
{/block}

{block scripts}

{literal}
<script type="text/template" id="mini-project-tpl">
    <div class="project-card mini item" data-src="/backend/task/task_tree?ProjectId={{Id}}" data-tpl="#mini-project-tpl">
        <dl class="fixed">
            <dd style="width:100px;padding-left:20px;">
                <input id="new_projectId" type="hidden" value="{{Id}}">
                <canvas data-value="{{CompleteProgress}}" width="70" height="70"></canvas>
            </dd>
            <dd class="v-top" style="padding-right:30px;">
                <p class="title text-nowrap"><a href="/backend/task/task_tree?ProjectId={{Id}}">{{Title}}</a></p>
                <div>
                    <div class="avatar"><img src="{{Avatar}}" class="demo demoDown" tip_Title="{{ProjectManager}}" alt=""></div>
                </div>
            </dd>
        </dl>
        <div class="buttons">
            <a href="#edit-project" data-toggle="work-panel" class="edit-btn demo demoDown" tip_Title="编辑">
                <i class="iconfont icon-miaoshu" style="font-size:18px;"></i>
            </a>
            <a href="/backend/statistics?ProjectId={{Id}}" class="demo demoDown" tip_Title="报表">
                <i class="iconfont icon-baobiao" style="font-size:20px;"></i>
            </a>
            <a href="/backend/milestone?ProjectId={{Id}}" class="demo demoDown" tip_Title="里程碑">
                <span class="icon icon-milestone"></span>
            </a>
            <a href="#" class="delete-btn absolute top right demo demoDown" style="top:10px;right:10px;" tip_Title="删除">
                <i class="iconfont icon-shanchu" style="font-size:27px;"></i>
            </a>
        </div>

        <div class="row time-range">
            <span class="start-time pull-left" timeval="{{StartDate}}">{{StartDateString}}</span>
            <span class="end-time pull-right" timeval="{{DueDate}}">{{DueDateString}}</span>
        </div>
        <div class="progress">
            <div class="bar" style="width:{{DateProgress}}%;background-color:#00bb41;"></div>
        </div>
    </div>
</script>
{/literal}

{include "includes/draw-canvas.tpl" selector=".project-container canvas"}

{include "includes/datetimepicker.tpl"}
{include "includes/daterangepicker.tpl"}

<script type="text/javascript">
    //设置默认项目ID
    $('[name="ProjectId"]').setValue(DataBase.ProjectId);
    $("form").on("reseting",function(){
        $(this).find('[name="ProjectId"]').setValue(DataBase.ProjectId);
    });

    $(function(){
        //扩展tip提示
        $(".demoDown").tip_Title();

        $("span.title.hide a").text("项目");
    });
</script>

<script type="text/javascript">
    $(".no-data").on("click",".add-task-button",function(){
        $(this).hide();
        $(".add-task-input").show().find("input").eq(0).focus();
    });
    //  项目相关操作
    $(".project-container").on("click",".project-card",function(e){
        if($(e.target).is("canvas"))
        {
            window.location = $(this).data("src");
        }
    }).on("click",".delete-btn",function(e){
        e.preventDefault();
        var item = $(this).getRow();
        var data = item.getRowData();

        if(item.is(".waiting")){
            return false;
        }

        //移除项目
        $.confirm(DataBase.ProjectName,"#remove-project-tpl").then(function(){
            item.addClass("waiting");
            this.close();
            var choice = this.find("[name=choice]:checked").val();

            if(choice=="remove"){
                //移除子项目
                $.post('/backend/project/edit',{
                    Id: data.Id,
                    ParentId: 0
                }).done(function(){
                    alert("移除成功");
                    item.trigger("row.delete").remove();
                }).always(function(){
                    item.removeClass("waiting");
                });
            }
            else if(choice=="delete"){
                //删除项目
                $.post('/backend/project/delete',{
                    Id: data.Id
                }).done(function(json){
                    alert("删除成功");
                    item.trigger("row.delete").remove();
                }).always(function(){
                    item.removeClass("waiting");
                });
            }

        });
    }).on("refresh.data",function(e){
        var canvas = $(e.target).find("canvas");
        DrawCanvas(canvas);
    });

    // 项目新建成功
    $("#new-project").on("request.success",function(e,json){
        //添加项目
        var item = GenerateList(json.Data,"#mini-project-tpl");
        item.appendTo(".project-container").trigger("refresh");
        $("#project-member").data("from",item);

       setTimeout(function(){
           //扩展tip提示
           $(".demoDown").tip_Title();
       },200);
    });
</script>

{literal}
    <script type="text/template" id="my-task-tpl">
        <li class="item {{if Exceed==1}}{{/if}} {{if CompleteProgress!=100}}omg{{/if}}" data-tpl="#my-task-tpl">
            {{include 'task-tpl'}}
        </li>
    </script>
<script type="text/template" id="sub-task-tpl">
    <div class="item">
        <dl>
            <dd>
                <form action="/backend/task/create" method="post" class="ajax-form instant-form" data-success="AddSubTaskSuccess">
                    <div class="params">
                        <input type="text" placeholder="输入子任务名称" name="Title" style="width:200px;height:33px;line-height:33px;">
                        <input type="hidden" name="ParentId" value="{{ParentId}}">
                        <input type="hidden" name="StartDate" value="{{StartDate}}">
                        <input type="hidden" name="DueDate" value="{{DueDate}}">
                        <input type="hidden" name="ProjectId"  value="{{ProjectId}}">
                        <input type="hidden" name="Description" >
                        <input type="hidden" name="AssignedTo" >
                        <input type="hidden" name="Follwers" >
                        <input type="hidden" name="Priority" value="0">
                        <input type="hidden" name="Checklist" >
                        <input type="hidden" name="IsMilestone" value="0">
                        {/literal}<input type="hidden" name="CreatorId" value="{$UserId}">
                    </div>
                </form>
            </dd>
        </dl>
    </div>
    </script>
    {*<span class="icon icon-sub-task"></span>添加子任务*}
{if $ProjectPower==1}
    {literal}
    <script type="text/template" id="treeview-tpl">
    	<div class="item" data-tpl="#treeview-tpl">
    		<dl class="{{if CompleteProgress==100}}completed{{/if}}">
    			<dt>
                    <div class="checkbox {{if CompleteProgress==100}}completed checked{{/if}} demo demoDown" {{if CompleteProgress==100}}tip_Title="点击重做"{{else}}tip_Title="标记完成"{{/if}}>
                        <input style="cursor:pointer;" type="checkbox" {{if CompleteProgress==100}}checked{{/if}}>
                    </div>
                </dt>
                <dd href="#edit-task" data-toggle="work-panel" style="cursor:pointer;" class="edit-btn">
                    <span style="{{if CompleteProgress==100}}color:#666;{{else}}color: #262626;{{/if}}max-width: 80%;display: inline-block;height: 18px;overflow:hidden;min-width:0px;margin-left: 0px;">{{Title}}</span>
                    <a href="javascript:void(0);" style="display: inline-block;margin-top: 2px;position: absolute;">- {{CompleteProgress}}%</a>
                    {{if Priority==1}}<span class="icon icon-priority" style="margin-top: 4px;margin-left: 55px;"></span>{{/if}}
                </dd>
    			<dt>
                    <span class="hover-item" style="{{if CompleteProgress==100}}display:none;{{/if}}">
                        <a tip_Title="添加子任务" class="add-sub-task handler-button demo demoDown"  href="#new-sub-task" data-toggle="work-panel">
                            <i class="iconfont icon-zirenwu" style="font-size: 24px;"></i>
                        </a>
                        <span class="dropdown more-handler">
                            <a class="handler-button"  href="#" data-toggle="dropdown" data-trigger="hover"><span class="icon icon-more"></span></a>
                            <div class="dropdown-menu">
                                <a href="#" class="menu-item move-left-btn"><span class="icon icon-move-left"></span>左移</a>
                                <a href="#" class="menu-item move-right-btn"><span class="icon icon-move-right"></span>右移</a>
                                <a href="#" class="menu-item clone-btn"><span class="icon icon-copy"></span>复制</a>
                                <a href="#" class="menu-item delete-btn"><i class="iconfont icon-shanchu" style="font-size: 23px;"></i>&nbsp;删除</a>
                            </div>
                        </span>
                    </span>
                    <span class="list-avatar-wrap">
                    {{each AssignedTo as v i}}
                        <div class="avatar"><img src="{{v.Avatar}}" tip_Title="{{v.UserName}}" alt="" {{if v.Status == 0}}class="filtergray demo demoDown"{{else}}class="demo demoDown"{{/if}}></div>
                    {{/each}}
                    </span>
                    <span class="time">{{StartDateString}} - <span class="orange">{{DueDateString}}</span></span>
    			</dt>
    		</dl>
    	</div>
    </script>
    {/literal}
{else}
    {literal}
    <script type="text/template" id="treeview-tpl">
        <div class="item" data-tpl="#treeview-tpl">
            <dl class="{{if CompleteProgress==100}}completed{{/if}}">
                <dt>
                    <i class="iconfont icon-xuanzekuanghoucopy" style="font-size:20px;margin-top:4px;margin-right:5px;color:#666;"></i>
                </dt>
                <dd href="#view-task" data-toggle="work-panel" class="edit-btn" style="cursor: pointer;">
                    <span style="margin-left:0px;" class="{{if CompleteProgress==100}}time{{/if}}">{{Title}}</span>
                     - {{CompleteProgress}}%
                    {{if Priority==1}}<span class="icon icon-priority"></span>{{/if}}
                </dd>
                <dt>j
                    <span class="list-avatar-wrap">
                    {{each RltUser as v i}}
                        <div class="avatar"><img src="{{v.Avatar}}" title="{{v.UserName}}" alt="" {{if v.Status == 0}}class="filtergray"{{/if}}></div>
                    {{/each}}
                    </span>
                    <span class="time">{{StartDateString}} - <span class="orange">{{DueDateString}}</span></span>
                </dt>
            </dl>
        </div>
    </script>
    {/literal}
{/if}
{literal}
<script type="text/javascript">
//    $(function() {
//        var url = window.location.href;
//        var urlArr = url.split('/');
//        if (url.indexOf("?") > 0) {
//            var urlName = urlArr[urlArr.length - 1].split('?')[0];
//            if (urlName == "task_tree") {
//                $("div#new-task form div.time-picker-group dl input[type=text]").attr({
//                    "Required":"1",
//                    "Attribute": 'data-minview="2" data-format="yyyy/mm/dd" data-myformat="YYYY/MM/DD"'
//                });
//            }
//        }
//    });
//	Tree View
function TreeViewObject(selector,data){
    var self = this;
    var _container = $(selector);
    var _data = data;

    this.container = _container;
    this.State = {};
    this.State.preventReDraw = false;

    //重绘
    this.ReDraw = function(notPrevented){
        if(notPrevented){
            self.State.preventReDraw=false;
        }
        if(self.State.preventReDraw){
            return;
        }

        //绘制虚线
        _container.find("ul>li ul").each(function(){
            var ul = $(this);

            //重置状态
            if( !ul.find("li").length){
                ul.siblings(".node-collapse").remove();
                if( !ul.parent().is(_container)){
                    ul.remove();
                }
            }
            if( !ul.children(".v-line").length){
                ul.append('<div class="h-line"></div><div class="v-line"></div>');
                ul.before('<div class="node-collapse demo demoDown" style="cursor:pointer;" tip_Title="双击展开/关闭"></div>');
            }

            /*
            //检查每一个 ul，如果它的最后一个 li呈展开状态，则需要调整 v-line的长度
            //bottom = li(last).height - 90
            var last = ul.children("li").eq(-1);
            var vline = ul.children(".v-line");
            var bottom = 25;
            if(last.is(".open")){
                bottom = last.outerHeight()-75;
            }
            if(bottom<0){
                bottom = 25;
            }
            vline.css("bottom",bottom);*/

            // if( !ul.children(".v-line").length){
            //     ul.before('<div class="node-collapse"></div>');
            // }
            //
            // ul.find("li").each(function(){
            //     $(this).append("<div class='v-line'></div><div class='h-line'></div>");
            // });
        });

        //为第一个有子元素的添加竖线
        _container.find(">ul>li").each(function(){
            var li = $(this);
            if(li.find("ul").length && !li.children(".v-line").length){
                li.append('<div class="v-line"></div>');
            }
//            var lastLi = li.find("li").eq(-1);
//            var hWidth = $(".tree-view").width() - lastLi.width();
//            lastLi.append('<div class="h-line" style="width: ' + hWidth + 'px;"></div>');
        });

        //触发重绘事件
        _container.trigger("redraw");

    };

    //阻止重绘
    this.PreventReDraw = function(){
        self.State.preventReDraw = true;
    };


    //插入节点
    this.Insert = function(item,box){
        if(box){
            var li = item.wrap("<li class='sub-task'></li>").parent();
            _Insert(li,box);
        }
        else{
            var li = item.wrap("<li class='main-task'></li>").parent();
            _Insert(li,_container);
        }
        self.ReDraw();
    };

    //在某节点之后插入
    this.InsertAfter = function(item,origin){
        var li = item.wrap("<li></li>").parent();
        var className = origin.parent().attr("class");
        li.addClass(className);
        origin.parent().after(li);

        self.ReDraw();
    };

    function _Insert(li,box){
        var ul;
        if(box.children("ul").length){
            ul = box.children("ul");
        }
        else{
            ul = $("<ul></ul>").appendTo(box);
            _container.trigger("treeview.node.created",ul);
        }

        ul.append(li);
        box.addClass("open");
    }

    //删除节点
    this.Delete = function(item){
        item.parent().remove();
        self.ReDraw();
//        if($('#viewport')[0].scrollHeight>850){
//            $('body').css('overflow-y','scroll');
//        }else{
//            $('body').css('overflow-y','hidden');
//        }
    };

    //向上一级
    this.LevelUpper = function(item){
        var li = item.parent();
        var brothers = li.nextAll("li");
        var upperLi = li.parent("ul").parent("li");
        if(upperLi.length){
            _Insert(brothers,li);
            upperLi.after(li);
            self.ReDraw();
        }
    };

    //向下一级
    this.LevelLower = function(item){
        var li = item.parent();
        var before = li.prev("li");
        if(before.length){
            _Insert(li,before);
            self.ReDraw();
        }
    };

    //刷新数据
    this.Refresh = function(data){
        if(data){
            self.PreventReDraw();
            _container.children("ul").remove();

            var insert = function(v,box){
                var item = GenerateList(v,"#treeview-tpl");
                self.Insert(item,box);
                if(v.Child){
                    $.each(v.Child,function(i,v){
                        insert(v,item.parent());
                    });
                }
            }

            $.each(data,function(i,v){
                insert(v);
            });
        }
        self.ReDraw(true);
    }

    //初始化
    this.Init = function(){
        self.Refresh(_data);
    };

    var timeFn=null;

    //event
    _container.on("dblclick","div.node-collapse",function(e){
        var _that=$(this).parents("li").eq(0);
        //展开子树
        if( ! $(e.target).parents("li").eq(0).is(_that)){
            return true;
        }
        if($(e.target).is("a,.icon,.checkbox,input")){
            return true;
        }
        if(_that.is(".open")){
            _that.removeClass("open").triggerHandler("treeview.close");
            _that.find("li").add(this).triggerHandler("treeview.collapse");
        }
        else{
            _that.addClass("open").triggerHandler("treeview.open");
        }
        self.ReDraw();
//        $(this).find("dl dd").on("click", function (ev) {
//            ev.stopPropagation();
////            setTimeout(function () {
////                ev.stopPropagation = null;
////            }, 100);
//        });
        var typeBth=1;
    });
    this.Init();
}

var TreeView = new TreeViewObject(".tree-view",DataBase.Task);

TreeView.container.on("click",".add-sub-task",function(){
    //添加子任务
    var row = $(this).getRow();
    var data = row.getRowData();
    var box = row.parent();

    if(box.data("subItem")){
        return;
    }

    var subData = {
        ParentId: data.Id,
        ProjectId: data.ProjectId,
        StartDate: moment().getTime(10),
        DueDate: moment().add({ hour:1 }).getTime(10)
    };
    var item = GenerateList(subData,"#sub-task-tpl");
    TreeView.Insert(item,box);
    item.find("[name='Title']").focus();

    box.data("subItem",item);
    box.one("treeview.collapse",function(){
        setTimeout(function(){
            TreeView.Delete(item);
            box.removeData("subItem");
        },200);
    });
    setTimeout(function(){
        if($("li.main-task dl.completed").length>0){
            $("li.main-task dl.completed").parents("li.main-task").css("opacity","0.7").find("ul").parents("li.main-task").css({"opacity":"1"}).find("div.item dl.completed").parents("div.item").css("background","rgba(0,0,0,0.1)");
        }
    },200);
}).on("row.delete",function(e){
    //删除任务
    e.stopPropagation();
    var row = $(e.target);
    TreeView.Delete(row);
    setTimeout(function(){
        if($("li.main-task dl.completed").length>0){
//            $("li.main-task dl.completed").parents("li.main-task").css("opacity","0.7");
            $("li.main-task dl.completed").parents("li.main-task").css("opacity","0.7").find("ul").parents("li.main-task").css({"opacity":"1"}).find("div.item dl.completed").parents("div.item").css("background","rgba(0,0,0,0.1)");
        }
    },200);
}).on("click",".move-left-btn",function(e){
    //向上一层
    e.preventDefault();
    var btn = $(this);
    if(btn.is(".waiting")){
        return false;
    }

    var item = btn.getRow();
    if(item.parents("li").length < 2){
        return false;
    }
    var data = item.getRowData();
    var brothers = item.parent().nextAll("li").map(function(){
        return $(this).children(".item").getRowData("Id");
    }).get().join();

    btn.addClass("waiting");
    $.post("/backend/task/move_left",{
        Id: data.Id,
        Brothers: brothers
    }).done(function(json){
        //btn.removeClass("waiting");
        //TreeView.LevelUpper(item);
        RefreshTaskData()
    });
    setTimeout(function(){
        if($("li.main-task dl.completed").length>0){
//            $("li.main-task dl.completed").parents("li.main-task").css("opacity","0.7");
            $("li.main-task dl.completed").parents("li.main-task").css("opacity","0.7").find("ul").parents("li.main-task").css({"opacity":"1"}).find("div.item dl.completed").parents("div.item").css("background","rgba(0,0,0,0.1)");
        }
    },200);
}).on("click",".move-right-btn",function(e){
    //向下一层
    e.preventDefault();
    var btn = $(this);
    if(btn.is(".waiting")){
        return false;
    }

    var item = btn.getRow();
    if(item.parent().prev("li").length == 0){
        return false;
    }
    var data = item.getRowData();
    var brotherId = item.parent().prev("li").children(".item").getRowData("Id");

    btn.addClass("waiting");
    $.post("/backend/task/move_right",{
        Id: data.Id,
        BrotherId: brotherId
    }).done(function(json){
        //btn.removeClass("waiting");
        //TreeView.LevelLower(item);
        RefreshTaskData()
    });
    setTimeout(function(){
        if($("li.main-task dl.completed").length>0){
//            $("li.main-task dl.completed").parents("li.main-task").css("opacity","0.7");
            $("li.main-task dl.completed").parents("li.main-task").css("opacity","0.7").find("ul").parents("li.main-task").css({"opacity":"1"}).find("div.item dl.completed").parents("div.item").css("background","rgba(0,0,0,0.1)");
        }
    },200);
}).on("redraw",function(){
    //列表为空时显示 no-data
    if($(this).find("li").length || $(".project-container").find(".project-card").length){
        $(this).find(".no-data").hide();
    }
    else{
        $(this).find(".no-data").show();
    }
    setTimeout(function(){
        if($("li.main-task dl.completed").length>0){
//            $("li.main-task dl.completed").parents("li.main-task").css("opacity","0.7");
            $("li.main-task dl.completed").parents("li.main-task").css("opacity","0.7").find("ul").parents("li.main-task").css({"opacity":"1"}).find("div.item dl.completed").parents("div.item").css("background","rgba(0,0,0,0.1)");
        }
    },200);
}).on("click",".clone-btn",function(){

    //复制任务
    var item = $(this).getRow();
    var clone = item.clone();
    clone.find(".avatar").remove();
    TreeView.InsertAfter(clone,item);
    Animation.FadeIn(clone);

    //滚动到指定位置
    var offsetTop = clone.offset().top;
    var scrollTop = $("body").scrollTop();
    if(scrollTop+400<offsetTop){
        $("html,body").animate({"scrollTop":offsetTop-400},600);
    }

    //请求后台
    $.post("/backend/task/copy",{
        CreatorId: DataBase.UserId,
        Id: item.getRowData("Id")
    }).done(function(json){
        if(json.Result==0){
            clone.setRowData(json.Data);
//            if($('#viewport')[0].scrollHeight>850){
//                $('body').css('overflow-y','scroll');
//            }else{
//                $('body').css('overflow-y','hidden');
//            }
            //复制完成后打开编辑窗口
            clone.find('.edit-btn').click();
        }
        else{
            alert(json.Msg);
            TreeView.Delete(clone);
        }
    });
    setTimeout(function(){
        if($("li.main-task dl.completed").length>0){
//            $("li.main-task dl.completed").parents("li.main-task").css("opacity","0.7");
            $("li.main-task dl.completed").parents("li.main-task").css("opacity","0.7").find("ul").parents("li.main-task").css({"opacity":"1"}).find("div.item dl.completed").parents("div.item").css("background","rgba(0,0,0,0.1)");
        }
    },200);
});

//刷新所有任务
function RefreshTaskData(){
    $.get("/backend/task/get_task_tree",{
        ProjectId: DataBase.ProjectId
    }).done(function(json){
        if(json.Result==0){
            TreeView.Refresh(json.Data);
            SortList.refresh();
        }
    });
    setTimeout(function(){
        if($("li.main-task dl.completed").length>0){
//            $("li.main-task dl.completed").parents("li.main-task").css("opacity","0.7");
            $("li.main-task dl.completed").parents("li.main-task").css("opacity","0.7").find("ul").parents("li.main-task").css({"opacity":"1"}).find("div.item dl.completed").parents("div.item").css("background","rgba(0,0,0,0.1)");
        }
    },200);
}

//添加任务成功
function AddTaskSuccess(json){
    if(json.Result == 0){
        alert(json.Msg);
        var data = json.Data;
        TreeView.Insert(GenerateList(data,"#treeview-tpl"));
        this.get(0).reset();
    }
    setTimeout(function(){
        if($("li.main-task dl.completed").length>0){
//            $("li.main-task dl.completed").parents("li.main-task").css("opacity","0.7");
            $("li.main-task dl.completed").parents("li.main-task").css("opacity","0.7").find("ul").parents("li.main-task").css({"opacity":"1"}).find("div.item dl.completed").parents("div.item").css("background","rgba(0,0,0,0.1)");
        }

        //扩展tip提示
        $(".demoDown").tip_Title();
    },200);
}

//添加子任务成功
function AddSubTaskSuccess(json){
    if(json.Result == 0){
        alert(json.Msg);
        var data = json.Data;
        var box = $(this).parents("li").eq(1);
        var item = box.data("subItem");

        //TreeView.Delete($(item));
        //box.removeData("subItem");

        var clickItem = GenerateList(data,"#treeview-tpl");
        TreeView.Insert(clickItem,box);
        clickItem.find('.edit-btn').click();

        var li = $(item).parent();
        var ul = li.parent();
        li.appendTo(ul);
        $(item).find("[name='Title']").val("").focus();

        //扩展tip提示
        $(".demoDown").tip_Title();
    }
}

//筛选相关
var SiftMode = function(){
    var self = this;
    var title = $(".header").find(".title").eq(0);
    var siftTitle = $(".header").find(".title").eq(1);
    var normalGroup = title.add(".task-panel");
    var siftGroup = siftTitle.add(".sift-panel");
    this.Enter = function(){
        normalGroup.hide();
        siftGroup.show();
    };
    this.Exit = function(){
        normalGroup.show();
        siftGroup.hide();
    }
};
var siftMode = new SiftMode();

//筛选成功
function SiftSuccess(json){
    siftMode.Enter();
    var result = GenerateList(json.Data,"#my-task-tpl");
    $(".sift-panel").find("ul").empty().append(result);
    $("div.sift-panel li:not(.omg)").css("opacity", "0.7").find("dd span").addClass("time").css("min-width","0px");
    setTimeout(function(){
        $("*[limit]").limit();
        $(".title_task").mouseover(function(){
            $(this).addClass("task_titleColor");
        }).mouseout(function(){
            $(this).removeClass("task_titleColor");
        });

        //扩展tip提示
        $(".demoDown").tip_Title();
    },200);
}

//更新进度后刷新
$(".tree-view").on("progress.change",function(){
    RefreshTaskData();
});
</script>
{/literal}
{* 排序 *}
<style media="screen">
    .sortable-ghost.sortable-chosen{
        opacity: 0.5;
    }
</style>
<script type="text/javascript" src="{$CommonUrl}/public/zeroClipboard/ZeroClipboard.js"></script>
<script type="text/javascript" src="{$CommonUrl}/plugins/Sortable/Sortable.min.js"></script>
<script type="text/javascript">
    var SortList = new function(selector){
            var container = $(selector);
            var groupCount = 0;
            var handlerList = [];
            this.container = container;
            var sortCreate = function(sortList){
                groupCount++;
                var handler = Sortable.create(sortList,{
                    animation: 250,
                    group: "group"+groupCount,
                    onUpdate: function (e) {
                       // console.log(e);
                       if(e.newIndex == e.oldIndex){
                          return;
                       }
                       var li = $(e.item);  // dragged HTMLElement
                       var id = li.children(".item").getRowData("Id");
                       var preSort = li.prev().children(".item").getRowData("Sort") || 0;
                       var nextSort = li.next().children(".item").getRowData("Sort") || 0;
                       $.post("/backend/task/sort",{
                           Id: id,
                           PreSort: preSort,
                           NextSort: nextSort
                       });
                    }
                });
                handler.destroy = Sortable.prototype.destroy;
                handlerList.push(handler);
            }
            this.init = function(){
                groupCount = 0;
                handlerList = [];
                container.find("ul").each(function(i){
                    sortCreate(this);
                });
                container.off("treeview.node.created.sort").on("treeview.node.created.sort",function(e,data){
                    sortCreate(data);
                });
            }
            this.refresh = function(){
                $.each(handlerList,function(i,handler){
                    handler.destroy();
                });
                this.init();
            }
            this.init();
    }("#sort-list");
</script>

<script type="text/javascript">

    //责任人筛选
    void function(){

        //加载责任人
        var container = $("#sift-panel").find(".user-sift");
        var directorIds = $("#sift-panel").find("[name=DirectorIds]");
        directorIds.setValue(DataBase.Directors);
        container.find(".avatar").append("<div class='mask'><span class='icon icon-selected'></span></div>");

        //点击效果
        container.on("click",".avatar",function(){
            $(this).toggleClass("selected");
            dataTransform();
        });

        //数据转换
        function dataTransform(){
            var selected = container.find(".avatar").filter(".selected");
            if(selected.length==0){
                selected = container.find(".avatar");
            }
            var data = selected.map(function(){
                return $(this).data("id");
            }).get().join();
            directorIds.val(data);
        }
    }();

</script>

<script type="text/javascript">
    //更改项目成员，加载Member
    $("#project-member").on("open",function(){
        var $this = $(this);
        $this.find("[name=ProjectId]").setValue(DataBase.ProjectId);
//        $this.find("[name=ProjectId]").setValue(DataBase.Member);
        $this.find("[name=MemberIds]").setValue(DataBase.Member);
//        var obj=$('#edit-task form>.form-group').eq(2).find('a.avatar').data().value;
//        if(obj==undefined){
//            $this.find("[name=MemberIds]").setValue(DataBase.Member);
//        }else{
//            $this.find("[name=MemberIds]").setValue(obj);
//        }
    });

    //项目成员选择完成后关闭
    $("#project-member").on("request.success",function(e,data){
        if(data.Result==0){
            alert(data.Msg);
            $(this).close();

            //重新获取项目成员
            GetMemberList(DataBase.ProjectId).done(function(json){
//                console.log(json);
                DataBase.set("Member",json.Data);
                $(this).find("[name=MemberIds]").setValue(DataBase.Member);
            });
        }
    });

//导入合作过的成员
$('#project-member>.title').append('<a href="#task-importUser-popover" data-toggle="popover" style="position: relative;font-size: 15px;color:#000;margin-left: 100px;"><span class="icon-users icon"></span>&nbsp;导入合作过的成员<span id="getData" style="background: transparent;position: absolute;top: 0px;left: 0px;width: 140px;height: 23px;"></span></a><a id="alink_code" href="#task-user-qrCode" data-toggle="popover" style="font-size: 15px;color:#000;margin-left:50px;"><span class="icon-codeEwm icon" style=margin-right: 10px;></span>&nbsp;链接邀请</a>');
</script>

{* 任务插入动画 *}
<style media="screen">
    .transition-05{
        transition: all 0.5s;
    }
    .highlight-blue{
        background: #d5edff !important;
    }
</style>
<script type="text/javascript">
    var Animation = {
        FadeIn: function(target){
            target.addClass("transition-05 highlight-blue");
            setTimeout(function(){
                target.removeClass("highlight-blue").transitionEnd(function(){
                    target.removeClass("transition-05");
                });
            },2000);
        }
    }
    var btnNum=0;
    //点击隐藏按钮，获取数据绑定数据，触发弹出框事件
    $("#getData").on("click",function(event){
        event=event||window.event;
        var that=$(this),da={},dt={};
        $.ajax({
        	type:"GET",
        	url:"/backend/user/get_cooperators",
        	data:"",
        	success:function(dataJson){
                //转化成Josn对象
        		dataJson=JSON.parse(dataJson);
        		if(dataJson.Result==0){
                    da.value=dataJson.Data;
                    that.parents('a').data(da);
                    that.parents(".work-panel").data("member",da.value);
                    //触发点击事件
                    that.parents('a').trigger('click');
                    //隐藏添加用户输入
                    $('#task-importUser-popover').find('.add-member').hide();
                    $('#task-importUser-popover').find('.list').attr('style','margin-top:10px');
                    var checkboxs = $('#task-importUser-popover').find('.list li input[type=checkbox]'),i= 0,len;
                    var selectObj = $('#project-member input[name=MemberIds]').val().split(',');
                    for(len=checkboxs.length;i<len;i++){
                        if($(checkboxs[i]).val()=={$UserInfo.Id}){
                            //如果 popover 列表中显示了自己，则被隐藏掉
                            $(checkboxs[i]).parents('li').hide();
                        }
                        if (selectObj.indexOf("" + $(checkboxs[i]).val() + "")<0) {
                            //如果不在已保存列表中则不被选中
                            $(checkboxs[i]).not(this).click();
                        }
                    }
                    //保证每次打开只添加一个“确定”按钮
                    if(window.btnNum==0){
                        $('#task-importUser-popover').find('.content').append("<div style='text-align:center;'><input class='btn_qd_click' type='button' value='确定' onclick='getCheckUserID();'></div>");
                        window.btnNum=1;
                    }
        		}else{
        			alert(data.Msg);
        		}
        	},error:function(){
        		alert("服务器异常!");
        	}
        });
        event.stopPropagation();
    });

   function getCheckUserID(){
       //取到用户选择的UserId
       var obj = $('#task-importUser-popover').find('.list li input[type=checkbox]'),arr=[],i= 0;
       for (var len = obj.length; i < len; i++) {
           if ($(obj[i]).parent().hasClass('checked')) {
               arr.push(obj[i].value);
           }
       }
       //取到面板已经显示的UserId
       var selectObj = $('#project-member input[name=MemberIds]').val().split(','), j = 0, differenceArr = [];
       //找出用户选中的值中面板上没有的UserId(并过滤掉重复数据)
       for (var lens = arr.length; j < lens; j++) {
           if (selectObj.indexOf("" + arr[j] + "") < 0) {
               differenceArr.push(arr[j]);
           }
       }
       //去掉差异比较出用户选中的状态
       var m= 0,dataDiff=[];
       if (arr.length > 0) {
           for (var lenss = obj.length; m < lenss; m++) {
               if ($(obj[m]).parent().hasClass('checked')) {
                   if (differenceArr.indexOf("" + obj[m].value + "") >= 0) {
                       var objDiff = new Object();
                       objDiff.UserId = obj[m].value;
                       objDiff.srcUrl = $(obj[m]).parent().next('.avatar').find('img').attr('src');
                       objDiff.UserName = $(obj[m]).parent().parent().next('dd').attr('title');
                       dataDiff.push(objDiff);
                   }
               }
           }
           if (dataDiff.length > 0) {
               var htmlVal = "", i = 0, setMemberId = $('#project-member input[name=MemberIds]'), userIds = ",";
               for (var len = dataDiff.length; i < len; i++) {
                   htmlVal += "<span class='avatar' data-id='" + dataDiff[i].UserId + "'>";
                   htmlVal += "<img src='" + dataDiff[i].srcUrl + "' title='" + dataDiff[i].UserName + "' alt=''>";
                   htmlVal += "<a href='#' class='delete'><span class='icon icon-delete-avatar'></span></a>";
                   htmlVal += "<p style='display: inline-block;font-weight: bold;margin-top: 5px;'><span style='display: inline-block;width: 50px;overflow: hidden;height: 20px;text-transform:capitalize;'>" + dataDiff[i].UserName + "</span></p>";
                   htmlVal += "</span>";
                   userIds += dataDiff[i].UserId + ',';
               }
               //把用户选中的人加入列表中
               $('#project-member div.user-selector-Member').find('span.avatar:last').after(htmlVal);
               //把用户选中的人员ID   保存在MemberIds 中用于提交
               $(setMemberId).val(($(setMemberId).val() + userIds.substring(0, userIds.length - 1)));
           }
       }
       //关闭窗口
       $('#task-importUser-popover').close();
   }

    $(function(){
        $('#new-task').on('open',function(){
            $('#new-task div.title div#menu_li>a').eq(0).hide();
            $('#new-task form>dl').eq(2).show();
        });
    });

    //打开更多中的（项目成员）在重新获取项目成员加进列表中，及时更新
    $(".pull-right .dropdown-menu>a[href=#project-member]").on("click", function () {
        var projectId = window.location.href.split("=")[1].split("#")[0] * 1, span_list = '', len = 0, i = 0, objDt, useridArr = [];
        GetMemberList(projectId).done(function (json) {
            if (json.Result == 0) {
                objDt = json.Data;
                if (objDt.length > 0) {
                    for (len = objDt.length; i < len; i++) {
                        span_list += "<span class='avatar' data-id='" + objDt[i].UserId + "'>";
                        span_list += "<img src='" + objDt[i].Avatar + "' title='" + objDt[i].UserName + "' alt=''>";
                        span_list += "<a href='#' class='delete'><span class='icon icon-delete-avatar'></span></a>";
                        span_list += "<p style='display: inline-block;font-weight: bold;margin-top: 5px;'>";
                        span_list += "<span style='display: inline-block;width: 50px;overflow: hidden;height: 20px;text-transform:capitalize;'>" + objDt[i].UserName + "</span>";
                        span_list += "</p></span>";
                        useridArr.push(objDt[i].UserId);
                    }
                    if (objDt.length > $("div#project-member .user-selector>span").length) {
                        $("div#project-member .user-selector>span").remove();
                        $("div#project-member .user-selector>a").before(span_list);
                        $("div#project-member .user-selector>input[name=MemberIds]").val(useridArr.join(","));
                    }
                }
            }
        });
    });
</script>
<script type="text/javascript">
    //项目创建  提交时改变项目时间进度条
    $('#new-project button[type=submit]').on('click',function(){
        var objDiv=$('div.project-container:not(:hidden)');

        setTimeout(function(){
            var div_new = objDiv.find('div.item:last-child');
            var startTime = div_new.find('span.start-time').attr('timeval');
            var endTime = div_new.find('span.end-time').attr('timeval');
            var dq_timq = moment().unix();
            var bfb_num=((dq_timq-startTime)/(endTime-startTime)).toFixed(2)*100;
            div_new.find('div.progress>div.bar').css("width",bfb_num+"%");
        },200);
    });

    //改变任务完成状态
    $('#edit-task form>dl:first-child dt label').on('click',function(){
        if($(this).hasClass('checked')){
            $(this).removeClass('checked');
            $('div.panel:not(:hidden)').find('li.main-task>div.active dl>dt:first-child').find('div').removeClass('checked');
            var id=$(this).parents('div.content').find('div.params input[name=Id]').val(),progress=0;
            checkbox_post(progress,id);
            return false;
        }else{
            $(this).addClass('checked');
            $('div.panel:not(:hidden)').find('li.main-task>div.active dl>dt:first-child').find('div').addClass('checked');
            var id=$(this).parents('div.content').find('div.params input[name=Id]').val(),progress='on';
            checkbox_post(progress,id);
            return false;
        }
    });

    function checkbox_post(progress,id){
        $.post('/backend/task/change_progress',{
            Progress:progress,
            TaskId:id
        },function(ret){
            alert(ret.Msg);
        });
    }

//    $(function(){
//        if($('#viewport')[0].scrollHeight>850){
//            $('body').css('overflow-y','scroll');
//        }else{
//            $('body').css('overflow-y','hidden');
//        }
//    });

    //提交任务时计算一下浏览器滚动条高度，如果大于设定值则出现滚动条
//    $('#new-task button[type=submit]').on('click',function(){
//        if($('#viewport')[0].scrollHeight>850){
//            $('body').css('overflow-y','scroll');
//        }else{
//            $('body').css('overflow-y','hidden');
//        }
//    });

    //点击邀请二维码（生成链接）
    $("div#project-member a#alink_code").on("click",function(){
        var projectUniCode = $("div.add-task-input input[name=ProjectUniCode]").val();
        var doMainName = "http://" + window.location.host + "/backend/login/join_by_link?Id=" + projectUniCode;
        $("div#task-user-qrCode").find("input#ctrl_v_txt").val(doMainName);
    });

    //添加子項目改变项目成员ID
    $("div#new-project button[type=submit]").on("click", function () {
        var new_projectId = 0;
        //取到子项目的ID
        setTimeout(function () {
            new_projectId = $("div.project-container>div:last-child").find("input#new_projectId").val();

            $("div#project-member div.params input[name=ProjectId]").val(new_projectId);
        }, 200);
    });

    setTimeout(function(){
        if($("li.main-task dl.completed").length>0){
//            $("li.main-task dl.completed").parents("li.main-task").css("opacity","0.7");
            $("li.main-task dl.completed").parents("li.main-task").css("opacity","0.7").find("ul").parents("li.main-task").css("opacity","1").find("div.item dl.completed").parents("div.item").css("background","rgba(0,0,0,0.1)");
        }
    },200);

    $("div.project-container>div").mouseover(function(){
        $(this).find("div.dialogD").show();
    }).mouseout(function(){
        $(this).find("div.dialogD").hide();
    });

    $(".header a[data-toggle='dropdown']").mouseover(function(){
        $(this).find(".icon-xiala").css("font-size","23px").css("font-weight","bold");
    }).mouseout(function(){
        $(this).find(".icon-xiala").css("font-size","19px").css("font-weight","100");
    });

    $(".header .dropdown-menu>a").mouseover(function(){
        $(this).css("color","#1CAAEC");
    }).mouseout(function(){
        $(this).css("color","#666");
    });

//    $("#new-task").on("open",function(){
//        var time=$(this).find("input[name=DueDate]").val();
//        $(this).find("input[name=DueDate]").val(time+" 23:59").attr("disabled","disabled");
//    });
    $("#new-task button[type=submit]").on("click", function () {
        var dueData = $(this).parents("#new-task").find("input[name=DueDate]").val();
        $(this).parents("#new-task").find("input[name=DueDate]").val(dueData + " 23:59");
    });
    $("#edit-task").on("open", function () {
        var _that = $(this);
        _that.find("input[name=DueDate]").on("change", function () {
            var _this = $(this);
            _this.val(_this.val() + " 23:59");
        });
    });
</script>
<script type="text/javascript">
    $('div.user-selector-popover-qrCode').on('open',function(){
        setTimeout(function(){
            new ZeroClipboard(document.getElementById('ctrl_C_btn'),{
                moviePath:"{$CommonUrl}/public/zeroClipboard/ZeroClipboard.swf"
            }).on('complete',function(client,args){
                alert('复制成功!');
                setTimeout(function(){
                    $('div.user-selector-popover-qrCode').close();
                    $('div#project-member').close();
                },500);
            });
        },300);
    });
</script>
{* 删除项目确认对话框 *}
{literal}
<script type="text/template" id="remove-project-tpl">
    <div class="dialog confirm-dialog instant">
        <div class="outer" data-allow-close="true">
            <div class="inner">
                <div class="content t-left">
                    <br>
                    <form>
                        <p>
                            <label><span class="radio checked"><input type="radio" name="choice" value="remove" checked></span>
                            解除与“{{Title}}”项目的关系</label>
                        </p>
                        <br>
                        <p>
                            <label><span class="radio"><input type="radio" name="choice" value="delete"></span>
                            删除该项目</label>
                        </p>
                    </form>

                </div>
                <div class="button-row t-right">
                    <button class="btn btn-blue confirm-btn">确定</button>
                    <button class="btn" data-dismiss="dialog">取消</button>
                </div>

            </div>
        </div>
    </div>
</script>
{/literal}
{if $TestMode}
<script type="text/javascript">
    {*console.info("Projects",{$Projects|@json_encode});*}
    {*console.info("Tasks",{$Tasks|@json_encode});*}
    {*console.info("Directors",{$Directors|default:[]|@json_encode});*}
    {*console.info("ProjectName",{$ProjectName|default:[]|@json_encode});*}
    {*console.info("ProjectId",{$ProjectId|default:[]|@json_encode});*}
    {*console.info("ParentProject",{$ParentProject|default:[]|@json_encode});*}
</script>
{/if}
{/block}
