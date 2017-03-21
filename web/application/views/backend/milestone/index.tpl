{extends "layouts/layout.tpl"}
{block styles}
<style media="screen">
     .panel.panel-blue{
         border-top: none;
         background-color: transparent;
         margin-left: 20px;
     }
     .panel .list .item{
         border-top: none;
     }
     .milestone ul li{
         margin-bottom: 1px;
         -webkit-border-radius: 5px;
         -moz-border-radius: 5px;
         border-radius: 5px;
         background-color: #fff;
     }
     .milestone-list .milestone-info .time{
         color: #ffffff;
     }
     .milestone-list{
         border-left: none transparent;
         position: relative;
     }
     .grayline{
         width: 2px;
         background-color: #cccccc;
         height:100%;
         position: absolute;
         margin-top: 70px;
         left: -2px;
         top: 0;
     }
     .milestone-list .project-info .time-progress {
         color: #ffffff;
     }
     .project-card{
         margin: 0px 10px 30px 0px;
     }
     .project-card canvas{
         cursor: pointer;
     }
    div#viewport{
        overflow: hidden;
    }
     div.pull-right{
         margin-top:-50px;
     }
</style>
{/block}
{*{block header}*}
{*<span class="title">{$ProjectName}</span>*}
{*<a href="/backend/task/task_tree?ProjectId={$ProjectId}" class="btn btn-orange btn-small"><span class="icon icon-list-white"></span>列表</a>*}
{*<span class="pull-right">*}
    {*<a class="btn btn-blue" href="#new-task" data-toggle="work-panel">新建任务</a>*}
    {*<a class="btn btn-orange" href="#sift-panel" data-toggle="work-panel">筛选</a>*}
{*</span>*}
{*{/block}*}
{block menuTitle}<a href="/backend/project/overview">项目</a>{/block}
{block leftMenu}
    {if !empty($ParentProject)}
        <span class="title active" style="border: 0px;height: 50px;overflow:hidden;">
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
        <a href="/backend/task/task_tree?ProjectId={$ProjectId}">列表</a>
        <a href="#" style="color:{if !empty($UserTheme)}{$UserTheme.Color}{else}{'#13427c;'}{/if};background: #fff;">里程碑</a>
    </span>
    <div class="tab_nav">
        <a href="/backend/task/task_tree?ProjectId={$ProjectId}">
            <span>任务</span>
        </a>
        <a class="selected_a" href="/backend/milestone?ProjectId={$ProjectId}">
            <span class="selcted_span">里程碑</span>
        </a>
        <a href="/backend/project/statistics?Id={$ProjectId}">
            <span>统计</span>
        </a>
        <a href="/backend/project/calendar?Id={$ProjectId}">
            <span>日历</span>
        </a>
    </div>
{/block}

{block rightMenu}
    <a class="submenu" href="#new-task" data-toggle="work-panel">
        <i class="iconfont icon-xinjian" style="font-size: 19px; margin-right: 10px; font-weight: 100;"></i>新建任务
    </a>
    {*<a class="submenu" href="#sift-panel" data-toggle="work-panel"><span class="icon-filter icon"></span>筛选</a>*}
{/block}

{*{block bg}*}
    {*<div class="background">*}
    {*</div>*}
{*{/block}*}


{block content}
    <div class="row project-container">
        {foreach $Childs as $i => $v}
            <div class="project-card mini item" data-src="/backend/task/task_tree?ProjectId={$v.Id}" data-refer="DataBase.ChildProject[{$i}]" data-tpl="#mini-project-tpl">
                <dl class="fixed">
                    <dd style="width:100px;padding-left:20px;">
                        <canvas data-value="{$v.CompleteProgress|default:0}" width="70" height="70">{$v.CompleteProgress|default:0}</canvas>
                    </dd>
                    <dd class="v-top" style="padding-right:30px;">
                        <p class="title text-nowrap"><a href="/backend/task/task_tree?ProjectId={$v.Id}">{$v.Title}</a></p>
                        <div>
                            <div class="avatar"><img src="{$v.Avatar}" class="demo demoDown" tip_Title="{$v.ProjectManager}" alt=""></div>
                        </div>
                    </dd>
                </dl>
                <div class="buttons">
                    <a href="#edit-project" data-toggle="work-panel" class="edit-btn demo demoDown" tip_Title="编辑">
                        <i class="iconfont icon-miaoshu" style="font-size:18px;"></i>
                    </a>
                    <a href="/backend/statistics?ProjectId={$v.Id}" title="报表" class="demo demoDown" tip_Title="报表">
                        <i class="iconfont icon-baobiao" style="font-size:20px;"></i>
                    </a>
                    <a href="/backend/milestone?ProjectId={$v.Id}" class="demo demoDown" tip_Title="里程碑">
                        <span class="icon icon-milestone"></span>
                    </a>
                    <a href="#" class="delete-btn absolute top right demo demoDown" style="top:10px;right:10px;" tip_Title="删除">
                        <i class="iconfont icon-shanchu" style="font-size:27px;"></i>
                    </a>
                </div>
                <div class="row time-range">
                    <span class="start-time pull-left">{$v.StartDateString}</span>
                    <span class="end-time pull-right">{$v.DueDateString}</span>
                </div>
                <div class="progress">
                    <div class="bar" style="width:{$v.DateProgress}%;background-color: #00bb41;"></div>
                </div>
            </div>
        {/foreach}
    </div>

<div class="milestone-list">
    <div class="grayline"></div>
	{if $Milestones!=null}
        {if $Milestones|@count}
            <div class="project-info">
                <canvas data-value="{if $Milestones!=null}{$Milestones.Progress}{else}0{/if}" data-stroke-background="white" width="70" height="70" class="project-progress"></canvas>
                <div class="avatar" style="display: none;"><img src="{$Project.Avatar}" title="{$Project.ProjectManager}" alt=""></div>
                <div class="time-progress" style="display: none;">
                    <p>
                        <span>{$Project.StartDateString}</span>
                        <span class="pull-right">{$Project.DueDateString}</span>
                    </p>
                    <div class="progress">
                        <div class="bar" style="width:{$Project.DateProgress}%;background-color: #00bb41;"></div>
                    </div>
                </div>
            </div>
            {foreach $Milestones.Rows as $key => $value}
    <div class="milestone">
        <div class="milestone-info">
            <span class="time">{$key}</span>
            <span class="progress progress-circle milestone-progress">
                <div class="mask"><div class="bar" style="height:{$value.CompleteProgress}%"></div></div>
            </span>
        </div>
        <div class="panel panel-blue">
            <div class="list task-list">
                <ul>
                    {foreach $value.Milestones as $i => $v}
                    <li class="item" data-refer="DataBase.Milestones.Rows['{$key}'].Milestones[{$i}]" data-tpl="#milestone-tpl">
                        <dl>
                            <dt>
                                <div class="checkbox {if $v.CompleteProgress==100}checked{/if} demo demoDown" {if  $v.CompleteProgress==100}tip_Title="点击重做"{else}tip_Title="标记完成"{/if}>
                                    <input type="checkbox" {if $v.CompleteProgress==100}checked{/if}>
                                </div>
                            </dt>
                            <dd>
                                <p>
                                    <a href="{if $Permission==1}#edit-task{else}#view-task{/if}" data-toggle="work-panel" class="edit-btn">{$v.Title}</a>
                                    {if $v.Priority==1}<span class="icon icon-priority"></span>{/if}
                                </p>
                            </dd>
                            <dt>
                                {if $Permission==1}
                                    <a href="#" class="delete-btn hover-item demo demoDown" tip_Title="删除">
                                        <i class="iconfont icon-shanchu" style="font-size:27px;"></i>
                                    </a>
                                {/if}
                                <span class="time">{$v.StartDateString} - <span class="orange">{$v.DueDateString}</span></span>
                            </dt>
                        </dl>
                    </li>
                    {/foreach}

                </ul>
            </div>
        </div>
    </div>
    {/foreach}
        {else}
            <p class="white" style="padding-top:40px;padding-left:40px;">
                没有里程碑
            </p>
        {/if}
	{else}

	{/if}
</div>


{include "includes/work-panel.tpl" }
{include "includes/work-panel-task.tpl" }
{include "includes/work-panel-project.tpl" }

{/block}



{block setting}
<script type="text/javascript">
    DataBase.Milestones = {$Milestones|@json_encode};
    DataBase.Project = {$Project|@json_encode};
    DataBase.ProjectId = "{$ProjectId}";
    DataBase.ChildProject = {$Childs|@json_encode};
</script>
{/block}

{block scripts}


{literal}
<script type="text/template" id="mini-project-tpl">
    <div class="project-card mini item" data-src="/backend/task/task_tree?ProjectId={{Id}}" data-tpl="#mini-project-tpl">
        <dl class="fixed">
            <dd style="width:100px;padding-left:20px;">
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
            <span class="start-time pull-left">{{StartDateString}}</span>
            <span class="end-time pull-right">{{DueDateString}}</span>
        </div>
        <div class="progress">
            <div class="bar" style="width:{{DateProgress}}%;background-color: #00bb41;"></div>
        </div>
    </div>
</script>
{/literal}

{include "includes/datetimepicker.tpl"}
{include "includes/draw-canvas.tpl" selector=".project-info canvas,.project-container canvas"}

<script type="text/javascript">
    //任务所属项目不可更改
    $('[name="ProjectId"]').setValue(DataBase.ProjectId);
    $("form").on("reseting",function(){
        $(this).find('[name="ProjectId"]').setValue(DataBase.ProjectId);
    });
</script>


<script type="text/javascript">
//$(function(){
//    if(DataBase.Milestones.length>0){
//         $('.milestone-list').show();
//    }
//    else{
//        $('.milestone-list').hide();
//    }
//})
$(function(){
    //扩展tip提示
    $(".demoDown").tip_Title();
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

    $.confirm("确定要删除“"+data.Title+"”吗？").then(function(){
    	item.addClass("waiting");
        this.close();

    	$.post('/backend/project/delete',{
    		Id: data.Id
    	}).done(function(json){
    		alert("删除成功");
    		item.trigger("row.delete").remove();
    	}).always(function(){
    		item.removeClass("waiting");
    	});
    });
}).on("refresh.data",function(e){
    var canvas = $(e.target).find("canvas");
    DrawCanvas(canvas);
});

</script>

{literal}
<script type="text/template" id="milestone-tpl">
	<li class="item" data-tpl="#milestone-tpl">
		{{include 'task-tpl'}}
	</li>
</script>
{/literal}

<script type="text/javascript">
//添加任务成功
function AddTaskSuccess(json){
	if(json.Result == 0){
		alert(json.Msg);
		var data = json.Data;
		//TreeView.InsertTask(GenerateList(data,"#milestone-tpl"));
		this.get(0).reset();
		$.reload(1.5);
	}
    setTimeout(function(){
        //扩展tip提示
        $(".demoDown").tip_Title();
    },200);
}

//更新进度后刷新
$(".milestone-list").on("progress.change",function(){
    $.reload(1);
});

</script>

{if $TestMode}
<script type="text/javascript">
    console.info("ProjectId",{$ProjectId|@json_encode});
    console.info("Project",{$Project|@json_encode});
    console.info("Milestones",{$Milestones|@json_encode});
</script>
{/if}


{/block}
