{extends "layouts/layout.tpl"}

{block styles}
    <style>
        body{
            background: #e2e4e4;
        }
        canvas {
            margin: 0 auto;
            margin-top: 30px;
        }
        .panel{
            border-radius: 5px;
        }
        .statistic-panel .title{
            padding: 5px 20px;
            margin-bottom: 20px;
            font-size: 14px;
        }
        .statistic-panel .number{
            font-size: 60px;
            white-space: nowrap;
        }

        .detail-panel{
            padding: 25px;
        }
        .detail-panel strong{
            color: black;
            font-weight: normal;
            font-size: 16px;
            padding: 0 3px;
        }

        .project-selector-popover{
            line-height: 30px;
        }
        .project-selector-popover .content{
            padding: 5px 0;
        }
        .project-selector-popover li{
            border-bottom: 1px solid #f1f1f1;
        }
        .project-selector-popover a{
            display: block;
            padding: 5px 15px;
        }
        .project-selector-popover a:hover{
            background: #f1f1f1;
        }
        .project-selector-popover a.active{
            color: #00AEFF;
        }
        .project-selector:hover{
            background-color:rgba(0,0,0,0.3);
        }
        div.pull-right{
            margin-top:-50px;
        }
    </style>
{/block}
{block setting}
    <script type="text/javascript">
        DataBase.ProjectInfo = {$Data.ProjectInfo|@json_encode};
    </script>
{/block}
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
            <a href="/backend/task/task_tree?ProjectId={$Data.ProjectInfo.Id}">{if !empty($Data.ProjectInfo.Title)}{$Data.ProjectInfo.Title|truncate:15}{else}项目名称{/if}</a>
        </span>
    {else}
        <span class="title active" style="border-left: 0px;height: 50px;overflow:hidden;">
            <a href="/backend/task/task_tree?ProjectId={$Data.ProjectInfo.Id}">{if !empty($Data.ProjectInfo.Title)}{$Data.ProjectInfo.Title|truncate:15}{else}项目名称{/if}</a>
        </span>
    {/if}
    <div class="tab_nav">
        <a href="/backend/task/task_tree?ProjectId={$Data.ProjectInfo.Id}">
            <span>任务</span>
        </a>
        <a href="/backend/milestone?ProjectId={$Data.ProjectInfo.Id}">
            <span>里程碑</span>
        </a>
        <a href="/backend/project/statistics?Id={$Data.ProjectInfo.Id}">
            <span class="selcted_span">统计</span>
        </a>
        <a href="/backend/project/calendar?Id={$Data.ProjectInfo.Id}">
            <span>日历</span>
        </a>
    </div>
{/block}
{block content }
    <div class="panel statistic-panel">
        <dl class="fixed v-bottom">
            <dd>
                <canvas data-value="{$Data.StatisticData.Progress|default:0}" width="100" height="100" data-stroke-width="12" data-stroke-background="#f1f1f1" data-color="#00adff"></canvas>
                <p class="title text-nowrap">项目进度</p>
            </dd>
            <dd>
                <canvas data-value="{$Data.StatisticData.Pass|default:0}" width="100" height="100" data-stroke-width="12" data-stroke-background="#f1f1f1" data-color="#ff3434"></canvas>
                <p class="title text-nowrap">延误率</p>
            </dd>
            <dd>
                <p class="number" style="color:#ffa800;">{$Data.StatisticData.Unfinished}</p>
                <p class="title text-nowrap">未完成任务</p>
            </dd>
            <dd>
                <p class="number" style="color:#50cb47;">{$Data.StatisticData.Completed}</p>
                <p class="title text-nowrap">已完成任务</p>
            </dd>
            <dd>
                <p class="number" style="color:#ff3434;">{$Data.StatisticData.Past}</p>
                <p class="title text-nowrap">过期任务</p>
            </dd>
            <dd>
                <p class="number" style="color:#808080;">{$Data.StatisticData.All}</p>
                <p class="title text-nowrap">总任务</p>
            </dd>
        </dl>
    </div>
    <div class="panel detail-panel">
        <p><span class="icon icon-calendar-gray text-head"></span>今天完成了<strong>{$Data.StatisticData.HaveDown}个任务</strong>，新建了<strong>{$Data.StatisticData.Create}个任务</strong></p>
    </div>
{/block}
{block bg}
    <div style="background: #E3E5E3;">
    </div>
{/block}
{block scripts}
{include "includes/draw-canvas.tpl" selector="canvas"}
    <script src="{$CommonUrl}/plugins/Jcrop/js/jquery.Jcrop.min.js"></script>
    <script type="text/javascript">
        {*{assign aa 456}*}
        {*{assign key 123}*}
        {*console.log({$key});*}
        {*console.log({$aa});*}
        {*console.log({$Data|@json_encode});*}
    </script>
{/block}
