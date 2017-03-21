{extends "layouts/layout.tpl"}


{block menuTitle}<a href="/backend/workbench/collection">收集箱</a>{/block}

{block rightMenu}
    <a href="#new-task" data-toggle="work-panel" class="submenu">
        <i class="iconfont icon-xinjian" style="font-size:19px;margin-right:10px;"></i>新建任务
        {*<span class="icon-new-task-fff icon"></span>*}
    </a>
    <a href="#sift-panel" data-toggle="work-panel" class="submenu">
        <i class="iconfont icon-shaixuan" style="font-size:22px;margin-right:10px;"></i>筛选
        {*<span class="icon-filter-fff icon"></span>*}
    </a>
{/block}
{*{block bg}*}
    {*<div class="background">*}
    {*</div>*}
{*{/block}*}
{block content }

    <div class="panel panel-blue todo-panel">
        {*<div class="title">今日待办</div>*}
        <div class="list task-list">
            <ul>
                {* 没有待办 *}
            </ul>
        </div>
    </div>
    {*<div class="panel panel-orange mark-panel">*}
        {*<div class="title">我的关注</div>*}
        {*<div class="list task-list">*}
            {*<ul>*}
                {* 没有关注 *}
            {*</ul>*}
        {*</div>*}
    {*</div>*}
    <div class="panel panel-blue sift-panel hide">
        <div class="list task-list">
            <ul>
                {* 没有结果 *}
            </ul>
        </div>
    </div>
    {include "includes/work-panel.tpl" }
    {include "includes/work-panel-task.tpl" }
    {include "includes/work-panel-sift-task.tpl" }

{/block}


{block scripts}
    <style media="screen">
        .todo-panel .avatar {
            display: none;
        }
        .panel{
            background-color: transparent;
            border-top: none;
        }
        .panel .title{
            padding: 15px 0;
            color: #ffffff;
        }
        .panel ul li{
            background-color: #fff;
            margin-bottom: 1px;
            -webkit-border-radius: 5px;
            -moz-border-radius: 5px;
            border-radius: 5px;
        }
        #viewport{
            padding: 10px 30px 20px;
        }
    </style>
{literal}
    <script type="text/template" id="my-task-tpl">
        <li class="item {{if Exceed==1}}red{{/if}}" data-tpl="#my-task-tpl">
            {{include 'task-tpl'}}
        </li>
    </script>
    <script type="text/template" id="my-mark-tpl">
        <li class="item {{if Exceed==1 && CompleteProgress!=100}}red{{/if}}" data-tpl="#my-mark-tpl">
            <dl class="{{if CompleteProgress==100}}completed{{/if}}">
                <dt>
                    <div class="checkbox {{if CompleteProgress==100}}completed{{/if}}"><input type="checkbox" {{if CompleteProgress==100}}checked{{/if}}></div>
                </dt>
                <dd href="{{if Permission==1}}#edit-task{{else}}#view-task{{/if}}" data-toggle="work-panel" class="edit-btn">
                    <span style="max-width:80%;display:inline-block;margin-left:10px;" limit="25">{{Title}}</span>
                    {{if Priority==1}}<span class="icon icon-priority"></span>{{/if}}
                </dd>
                <dt>
                    <!--{{if Permission==1}}<a href="#" class="delete-btn hover-item"><span class="icon icon-delete"></span></a>{{/if}}-->
                    <span class="list-avatar-wrap">
                        {{each RltUser as v i}}
                        <div class="avatar"><img src="{{v.Avatar}}" title="{{v.UserName}}" alt="" {{if v.Status == 0}}class="filtergray"{{/if}}></div>
                        {{/each}}
                    </span>
                    <span class="time">{{StartDateString}} - <span class="orange">{{DueDateString}}</span></span>
                </dt>
            </dl>
        </li>
    </script>
{/literal}

    {include "includes/datetimepicker.tpl"}
    <script type="text/javascript">
        // 待办列表
        $.post('/backend/task/get_task_by_user_id', {
            Type: "collection",
            UserId: DataBase.UserId,
            StartDate: DataBase.Today.start,
            DueDate: DataBase.Today.end
        }).done(function (json) {
            if(json.Data.length < 1){
                $(".todo-panel").find(".title").hide();
            }
            var list = GenerateList(json.Data, '#my-task-tpl');
            $(".todo-panel").find("ul").append(list);
        });

        // 关注列表
        $.post('/backend/task/get_task_by_user_id', {
            Type: "collection",
            UserId: DataBase.UserId,
            StartDate: DataBase.Today.start,
            DueDate: DataBase.Today.end
        }).done(function (json) {
            if(json.Data.length < 1){
                $(".mark-panel").find(".title").hide();
            }
            var list = GenerateList(json.Data, '#my-mark-tpl');
            $(".mark-panel").find("ul").append(list);
        });

        //添加任务成功
        function AddTaskSuccess(json) {
            if (json.Result == 0) {
                alert(json.Msg);
                var data = json.Data;
                //TreeView.InsertTask(GenerateList(data,"#task-tpl"));
                this.get(0).reset();
                $.reload(1.5);
            }
        }

        //筛选相关
        var SiftMode = function () {
            var self = this;
            var title = $(".header").find(".title").eq(0);
            var siftTitle = $(".header").find(".title").eq(1);
            var normalGroup = title.add(".todo-panel").add(".mark-panel");
            var siftGroup = siftTitle.add(".sift-panel");

            this.Enter = function () {
                normalGroup.hide();
                siftGroup.show();
            };
            this.Exit = function () {
                normalGroup.show();
                siftGroup.hide();
            }
        };
        var siftMode = new SiftMode();

        //筛选成功
        function SiftSuccess(json) {
            siftMode.Enter();
            var result = GenerateList(json.Data, "#my-task-tpl");
            $(".sift-panel").find("ul").empty().append(result);
        }

        //任务编辑关闭时监控任务是否被添加进了收集箱
        $("#edit-task").on("close", function () {
            var activeBool = $("#edit-task").find(".collect-button").eq(0).hasClass("active");
            if (activeBool!=true){
                $("div.panel:not(:hidden)").find("li.active").stop().fadeToggle('slow');
            }
        });
    </script>

    <script type="text/javascript">
        //  新建任务时默认放到收集箱
        $("#new-task").on("open reseting",function(){
            $(this).find("[name=IsCollected]").setValue(1);
        });
    </script>
{/block}
