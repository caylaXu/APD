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
    </style>
{/block}

{block menuTitle}<a href="/backend/statistics/index">报表</a>{/block}
{block leftMenu}
    <div class="project-selector menu-left-down dropdown">
        <a class="" data-toggle="dropdown" href="#"><span class="text-head text-overflow" style="max-width: 8em;display:inline-block;vertical-align:middle;color: #fff;">{$Data.ProjectInfo.Title}</span><span class="icon icon-arrow-down"></span></a>

        <div class="dropdown-menu project-selector-popover t-left" id="project-popover" >
            <div class="list narrow projectlist" style="width:200px;">
                <ul>
                    {foreach from=$Data.Projects key=k item=v name=foo}
                        <li>
                            <a href="/backend/statistics/index?ProjectId={$v.Id}" class="text-overflow {if $v.Id==$Data.ProjectInfo.Id}active{/if}">{$v.Title}</a>
                        </li>
                    {/foreach}
                </ul>
            </div>

        </div>
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
