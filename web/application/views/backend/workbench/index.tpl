{extends "layouts/layout.tpl"}

{block styles}
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
        overflow-y: hidden;
    }
    .no-data{
        text-align: center;
        padding-top: 30px;
    }
    .no-data .add-task-button{
        display: inline-block;
        width: 600px;
        height: 50px;
        line-height: 50px;
    }
    .no-data .add-task-input{
        padding-right: 50px;
    }
    .no-data .add-task-input input{
        background: white;
        height: 50px;
        padding: 0 1em;
    }
    #sift-panel .user-sift{
        display: none;
    }
    .completed .play-btn{
        display: none !important;
    }
    .exceed .time{
        color: red;
    }
    .exceed .time .orange{
        color: red;
    }
</style>
<style media="screen">
    #task-player{
        background: #030c1a;
        filter:alpha(opacity=65)\9;
        background: rgba(0, 0, 0, 0.8);
        text-align: center;
        color: white;
        z-index: 100;
        position: fixed;
        padding-top: 180px;
        padding-bottom: 40px;
        overflow: auto;
    }

    #task-player h1{
        font-size: 36px;
    }

    #task-player .btn{
        width: 180px;
        height: 78px;
        font-size: 36px;
        line-height: 1em;
        border-radius: 20px;
        box-shadow: none;
        outline: none;
        transition: all 0.5s;
    }
    #task-player .button-group .btn+.btn{
        margin-left: 220px;
    }

    #task-player .btn-white{
        color: white;
        border-color: white;
    }
    #task-player .btn-white:focus{
        border-color: white !important;
    }

    #task-player .finish-btn{
        opacity: 0.3;
    }
    #task-player .finish-btn:hover,
    #task-player .finish-btn:focus{
        opacity: 1;
        border-color: #00bb41 !important;
        background: #00bb41;
    }
    #task-player .clock-plate{
        margin: 90px 0;
    }
    #task-player .text{
        margin-top: 65px;
    }
    #task-player .time{
        font-size: 46px;
        line-height: 1em;
        margin-top: 30px;
    }
    #task-player .buttons{
        margin-top: 40px;
    }
    #task-player .hover-item{
        opacity: 0;new-project
        transition: opacity 0.5s;
    }
    #task-player .clock-plate:hover .hover-item{
        opacity: 1;
    }
    .dropdown-menu{
        top: 48px;
        -webkit-border-radius: 5px;-moz-border-radius: 5px;border-radius: 5px;
    }
    .dropdown-menu:before{
        content: '';
        width: 0px;
        height:0px;
        border-left: 9px solid transparent;
        border-right: 9px solid transparent;
        border-bottom: 9px solid #eee;
        position: absolute;
        left: 25px;
        top: -9px;
    }
    .dropdown-menu:after{
        content: '';
        border-left: 7px solid transparent;
        border-right: 7px solid transparent;
        border-bottom: 7px solid #fff;
        position: absolute;
        left: 27px;
        top: -7px;
    }
    .dropdown .dropdown-menu{
        display: none;
        position: absolute;
        z-index: 1;
        left: 11px;
        background: #fff;
        box-shadow: 0 0 7px rgba(0,0,0,.3);
        padding: 10px 20px;
        border:1px solid #b6b6b6\9;!important;
    }
    .dropdown-menu .list ul li{
        color: #666;
        line-height: 30px;
        margin-bottom: 5px;
        text-align: center;
        -webkit-border-radius: 5px;
        -moz-border-radius: 5px;
        border-radius: 5px;
        cursor: pointer;
    }
    .dropdown-menu .list ul li:hover{
        color: #1CAAEC;
    }
    .dropdown-menu .list ul .activeLi{
        color: #fff!important;
        background-color: #1CAAEC;
    }
    .project-card{
        -webkit-border-radius: 5px;
        -moz-border-radius: 5px;
        border-radius: 5px;;
    }
    .project-card .progress{
        width: 58%;
        bottom: 15px;
        margin: 0 auto;
        background-color: #ccc;
    }
    .header .personal-card{
        border:1px solid #b6b6b6\9;!important;
    }
    div.content-wrap form div.time-picker-group dl:first-child{
        width: 50%\9!important;
        float: left;\9!important;
        color: #000\9!important;
    }
    .task_titleColor{
        color: #4097FC;
    }
</style>
{/block}

{block menuTitle}<a href="/backend/workbench">工作台</a>{/block}
{block leftMenu}
    <div class="my-project-selector menu-left-down dropdown">
        <a class="" data-toggle="dropdown" href="#">
            <span class="text-head text-overflow" id="proSelectID" dataType="" style="max-width: 8em;display:inline-block;vertical-align:middle;font-size: 14px;color: #fff;">今日待办</span>
            <span class="icon icon-arrow-down"></span>
        </a>
        <div class="dropdown-menu project-selector-popover t-left" id="project-popover" >
            <div class="list narrow projectlist" style="width:85px;margin-bottom: 0px;">
                <ul id="my-project-ul">
                    <li class="activeLi"><a href="javascript:void(0)" class="text-overflow" dataType="">今日待办</a></li>
                    <li><a href="javascript:void(0)" class="text-overflow" dataType="Created">我创建的</a></li>
                    <li><a href="javascript:void(0)" class="text-overflow" dataType="Concerned">我关注的</a></li>
                    <li><a href="javascript:void(0)" class="text-overflow" dataType="Finished">我完成的</a></li>
                    <li><a href="javascript:void(0)" class="text-overflow" dataType="All">全部任务</a></li>
                </ul>
            </div>
        </div>
    </div>
{/block}
{block rightMenu}
    {*<span class="icon-new-task-fff icon"></span>*}
    <a href="#new-task" data-toggle="work-panel" class="submenu">
        <i class="iconfont icon-xinjian" style="font-size:19px;margin-right:10px;"></i>新建任务
    </a>
    {*<span class="icon-filter-fff icon"></span>*}
    <a href="#sift-panel" data-toggle="work-panel" class="submenu">
        <i class="iconfont icon-shaixuan" style="font-size:22px;margin-right:10px;"></i>筛选
    </a>
{/block}
{*{block bg}*}
    {*<div class="background">*}
    {*</div>*}
{*{/block}*}
{block content }
    <div class="no-data hide">
        <a href="#" class="add-task-button bg-glass border-radius large" >
            <span class="icon icon-plus-white text-head"></span>新建任务
        </a>
        <div class="add-task-input hide">
            <form action="/backend/task/create" method="post" class="ajax-form form-panel" data-success="AddTaskSuccess">
                <input type="text" name="Title" placeholder="请输入任务内容并按Enter结束">
                <input type="hidden" name="Description" value="">
                <input type="hidden" name="ProjectId" value="0">
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

    <div class="panel panel-blue todo-panel hide">
        {*<div class="title">今日待办</div>*}
        <div class="list task-list">
            <ul>
                {* 没有待办 *}
            </ul>
        </div>
        <div style="padding: 10px 0px;margin-top: 30px;margin-bottom: -25px;">
            <span id="teday_task" typeTask="0" class="demo demoDown" tip_Title="展开最近完成任务" style="width: 70px;height: 24px;background: rgba(0,0,0,0.4);background:#000\9;opacity:0.4\9;color:#fff;padding:5px 10px;border-radius:4px;cursor:pointer;">已完成<i class="iconfont icon-jiantou" style="font-size:15px;margin: 3px 0px 0px 5px;"></i></span>
        </div>
    </div>
    <div class="panel panel-orange mark-panel hide">
        {*<div class="title">我的关注</div>*}
        <div class="list task-list">
            <ul>
                {* 没有关注 *}
            </ul>
        </div>
        <div id="mark_div" style="padding: 10px 0px;margin-top: 30px;margin-bottom: -25px;display:none;">
            <span id="mark_panel_task" typeTask="0" class="demo demoDown" tip_Title="展开最近完成任务" style="width: 70px;height: 24px;background: rgba(0,0,0,0.4);background:#000\9;opacity:0.4\9;color:#fff;padding:5px 10px;border-radius:4px;cursor:pointer;">已完成<i class="iconfont icon-jiantou" style="font-size:15px;margin: 3px 0px 0px 5px;"></i></span>
        </div>
    </div>
    {*已完成*}
    <div class="panel panel-orange task-panel hide">
        {*<div class="title">我的关注</div>*}
        <div class="list task-list">
            <ul>
                {* 没有关注 *}
            </ul>
        </div>
    </div>
    <div class="panel panel-blue sift-panel hide">
        <div class="list task-list">
            <ul>
                {* 没有结果 *}
            </ul>
        </div>
    </div>


    <div class="mask hide" id="task-player">
        <h1 class="title">敏捷研发平台需求评审</h1>
        <div class="clock-plate relative">
            <canvas width="280" height="280"></canvas>
            <div class="text-center mask">
                <p class="text">专注之美</p>
                <p class="time"></p>
                <p class="buttons"><a href="#" class="pause-btn hover-item"><span class="icon icon-pause"></span></a></p>
            </div>
        </div>
        <div class="button-group">
            <button type="button" class="btn btn-large btn-white btn-reverse finish-btn">完成</button>
        </div>
        <div class="button-group">
            <button type="button" class="btn btn-large btn-white btn-reverse give-up-btn">放弃</button>
            <button type="button" class="btn btn-large btn-green continue-btn">继续</button>
        </div>
    </div>
    {include "includes/work-panel.tpl" }
    {include "includes/work-panel-task.tpl" }
    {include "includes/work-panel-sift-task.tpl" }
{/block}

{block setting}
<script type="text/javascript">
    {*DataBase.CurrentTask = {$CurrentTask|@json_encode};*}
</script>
{/block}

{block scripts}
    {*<span class="icon icon-layer"></span>项目图标*}
    {*<span class="icon icon-play"></span>立即执行*}
    {*class="hover-item"*}
{literal}
    <script type="text/template" id="my-task-tpl">
        <li class="item {{if CompleteProgress!=100}}omg{{/if}}" data-tpl="#my-task-tpl">
            <dl class="{{if CompleteProgress==100}}completed{{/if}} {{if Exceed==1}}exceed{{/if}}">
                <dt>
                    {{if Permission==1}}
                        <div class="checkbox {{if CompleteProgress==100}}checked{{/if}} demo demoDown" {{if CompleteProgress==100}}tip_Title="点击重做"{{else}}tip_Title="标记完成"{{/if}}>
                            <input type="checkbox" {{if CompleteProgress==100}}checked{{/if}}>
                        </div>
                    {{else}}
                        <i class="iconfont icon-xuanzekuanghoucopy demo demoDown" tip_Title="没有权限" style="font-size:20px;margin-top:4px;margin-right:5px;color:#666;"></i>
                    {{/if}}
                </dt>
                <dd href="{{if Permission==1}}#edit-task{{else}}#view-task{{/if}}" data-toggle="work-panel" class="edit-btn" style="z-index:0;cursor:pointer;">
                    <span style="{{if CompleteProgress==100}}color: #666;{{else}}color:#262626;{{/if}}max-width:80%;display:inline-block;margin-left:10px;" limit="25">{{Title}}</span>
                    {{if Priority==1}}
                        <span class="icon icon-priority demo demoDown" tip_Title="紧急" style="margin-top:0px;"></span>
                    {{/if}}
                    {{if ProjectName!=null && ProjectName!="" && ProjectName!="无"}}
                        <a class="title_task" href="{{if ProjectId==0}}{{#}}{{else}}/backend/task/task_tree?ProjectId={{ProjectId}}{{/if}}" style="z-index: 2;margin-left:5px;">
                            <i projectName="{{ProjectName}}" class="iconfont icon-wenjianjia" style="font-size: 18px;"></i>
                            <span style="margin-left:5px;" class="demo demoDown" tip_Title="查看【{{ProjectName}}】的所有任务">{{ProjectName}}</span>
                        </a>
                    {{else}}
                        <i class="iconfont icon-wenjianjia" style="font-size:18px;color:#fff;opacity:0;filter:Alpha(opacity=0);-moz-opacity:0;"></i>
                    {{/if}}
                    <a href="#" tip_Title="立即执行" class="hover-item play-btn text-foot demo demoDown" style="z-index:2;">
                        <i class="iconfont icon-icon6" style="font-size:18px;margin-top:0px;"></i>
                    </a>
                </dd>
                <dt>
                    <!--{{if Permission==1}}<a href="#" class="delete-btn hover-item"><span class="icon icon-delete"></span></a>{{/if}}-->
                    <span class="list-avatar-wrap">
                    {{each AssignedTo as v i}}
                        <div class="avatar"><img src="{{v.Avatar}}" class="demo demoDown" tip_Title="{{v.UserName}}" alt="" {{if v.Status == 0}}class="filtergray"{{/if}}></div>
                    {{/each}}
                    </span>
                    <span class="time">{{StartDateString}} - <span class="orange">{{DueDateString}}</span></span>
                </dt>
            </dl>
        </li>
    </script>
    <script type="text/template" id="my-mark-tpl">
        <li class="item {{if CompleteProgress!=100}}red{{/if}}" data-tpl="#my-mark-tpl" permission="{{Permission}}">
            <dl class="{{if CompleteProgress==100}}completed{{/if}}">
                <dt>
                    {{if Permission==1}}
                        <div class="checkbox {{if CompleteProgress==100}}checked{{/if}} demo demoDown" {{if CompleteProgress==100}}tip_Title="点击重做"{{else}}tip_Title="标记完成"{{/if}}>
                            <input type="checkbox" {{if CompleteProgress==100}}checked{{/if}}>
                        </div>
                    {{else}}
                        <i class="iconfont icon-xuanzekuanghoucopy demo demoDown" tip_Title="没有权限" style="font-size:20px;margin-top:4px;margin-right:5px;color:#666;"></i>
                    {{/if}}
                </dt>
                <dd href="{{if Permission==1}}#edit-task{{else}}#view-task{{/if}}" data-toggle="work-panel" class="edit-btn" style="cursor:pointer;">
                    <span style="{{if CompleteProgress==100}}color: #666;{{else}}color:#000;{{/if}}margin-left:10px;" limit="25">{{Title}}</span>
                    {{if Priority==1}}<span class="icon icon-priority" style="margin-top:0px;"></span>{{/if}}
                </dd>
                <dt>
                    <!--{{if Permission==1}}<a href="#" class="delete-btn hover-item"><span class="icon icon-delete"></span></a>{{/if}}-->
                    <span class="list-avatar-wrap">
                        {{each RltUser as v i}}
                        <div class="avatar"><img src="{{v.Avatar}}" tip_Title="{{v.UserName}}" class="demo demoDown" alt="" {{if v.Status == 0}}class="filtergray"{{/if}}></div>
                        {{/each}}
                    </span>
                    <span class="time" style="color: #666;">{{StartDateString}} - <span class="orange">{{DueDateString}}</span></span>
                </dt>
            </dl>
        </li>
    </script>
{/literal}

    {include "includes/datetimepicker.tpl"}
    {include "includes/daterangepicker.tpl"}
    <script type="text/javascript">
        //默认加载
        $(function(){
            //我的待办
            myStayTask($('#proSelectID').attr('dataType'));
        });
        //扩展tip提示
        setTimeout(function(){
            $(".demoDown").tip_Title();
        },200);
        //效果 淡出
        $('.my-project-selector').hover(function(){
            $('#project-popover').css('display','block').addClass('fadeInShow');
        },function(){
            $('#project-popover').css('display','none').removeClass('fadeInShow');
        });

        //今日待办中的--已完成
        $("#teday_task").on("click", function () {
            if ($(this).attr("typeTask") == 0) {
                $(this).attr("typeTask", "1").attr("tip_title","收起最近完成任务").find("i").removeClass('icon-jiantou').addClass('icon-xiala');
                var nowTime = new Date().getTime();
                var clickTime = $(this).attr("cTime");
                if( clickTime != 'undefined' && (nowTime - clickTime < 500)){
                    return false;
                }else{
                    $(this).attr("cTime",nowTime);
                    $.post('/backend/workbench', {
                        Type: "TodayFinished"
                    }).done(function (json) {
                        if (json.Data.length > 0) {
                            var list = GenerateList(json.Data, '#my-mark-tpl');
                            $(".task-panel").show().find("ul").empty().append(list);
                            $('.task-panel .task-list li dt .completed').addClass('checked');
                            $("div.task-panel li").css("opacity", "0.7").find("dd span").addClass("time").css("min-width", "0px");
                        }
                        //扩展tip提示
                        $(".demoDown").tip_Title();
                    });
                }
            } else {
                $('.task-panel').hide();
                $(this).attr("typeTask", "0").attr("tip_title","展开最近完成的任务").find("i").removeClass('icon-xiala').addClass('icon-jiantou');
            }
        });

        //我关注中的--已完成
        $("#mark_panel_task").on("click",function(){
            if($(this).attr("typeTask")==0){
                $.post('/backend/workbench', {
                    Type: "ConcernedFinished",
                }).done(function (json) {
                    if (json.Data.length>0) {
                        var list = GenerateList(json.Data, '#my-mark-tpl');
                        $(".task-panel").show().find("ul").empty().append(list);
                        $('.task-panel .task-list li dt .completed').addClass('checked');
                    }
                    //扩展tip提示
                    $(".demoDown").tip_Title();
                });
                $(this).attr("typeTask","1").attr("tip_title","收起最近完成任务").find("i").removeClass('icon-jiantou').addClass('icon-xiala');
                setTimeout(function() {
                    $("div.task-panel li").css("opacity", "0.7").find("dd span").addClass("time").css("min-width","0px");
                },200);
            }else{
                $('.task-panel').hide();
                $(this).attr("typeTask","0").attr("tip_title","展开最近完成的任务").find("i").removeClass('icon-xiala').addClass('icon-jiantou');
            }
        });

        //项目切换点击
        $('#my-project-ul li').on('click',function(){
            $(this).addClass("activeLi").siblings().removeClass("activeLi");
            var type=$(this).find('a').attr('dataType');
            switch (type){
                case 'Created':
                    $('#proSelectID').text('我创建的');
                    $("div#sift-panel form>dl").eq(2).show();
                    $("#mark_div,.task-panel").hide();
                    $("div.todo-panel span#teday_task").find("i").removeClass("icon-xiala").addClass("icon-jiantou");
                    taskLis(type);
                    break;
                case 'Concerned':
                    $('#proSelectID').text('我关注的');
                    $("div#sift-panel form>dl").eq(2).show();
                    $(".task-panel").hide();
                    $("#mark_div").show();
                    $("div.todo-panel span#teday_task").find("i").removeClass("icon-xiala").addClass("icon-jiantou");
                    taskLis(type);
                    break;
                case 'Finished':
                    $('#proSelectID').text('我完成的');
                    $("div#sift-panel form>dl").eq(2).hide();
                    $("#mark_div,.task-panel").hide();
                    $("div.todo-panel span#teday_task").find("i").removeClass("icon-xiala").addClass("icon-jiantou");
                    taskLis(type);
                    break;
                case 'All':
                    $('#proSelectID').text('全部任务');
                    $("div#sift-panel form>dl").eq(2).show();
                    $("#mark_div,.task-panel").hide();
                    $("div.todo-panel span#teday_task").find("i").removeClass("icon-xiala").addClass("icon-jiantou");
                    taskLis(type);
                    break;
                default:
                    $('#proSelectID').text('今日待办');
                    $("div#sift-panel form>dl").eq(2).show();
                    myStayTask(type);
                    //鼠标移入项目名称上时变色
                    setTimeout(function(){
                        $(".title_task").mouseover(function(){
                            $(this).addClass("task_titleColor");
                        }).mouseout(function(){
                            $(this).removeClass("task_titleColor");
                        });

//                        $(".demoDown").tip_Title();
                    },200);
                    break;
            }
            setTimeout(function(){
                //限制文字数
                $("span[limit]").limit();

                //扩展tip提示
                $(".demoDown").tip_Title();
            },200);
        });
        /*
         Parameter 待办任务
         */
        function myStayTask(type) {
            $.post('/backend/workbench', {
                Type: type
            }).done(function (json) {
                if (json.Data.length > 0) {
                    var list = GenerateList(json.Data, '#my-task-tpl');
                    $(".no-data,.mark-panel,.sift-panel").hide();
                    $(".todo-panel").show().find("ul").empty().append(list);

                    setTimeout(function () {
                        $("*[limit]").limit();
                        $(".title_task").mouseover(function () {
                            $(this).addClass("task_titleColor");
                        }).mouseout(function () {
                            $(this).removeClass("task_titleColor");
                        });
                    }, 200);
                }
                else {
                    $(".mark-panel,.todo-panel,.sift-panel").hide();
                    $('.no-data').show();
                    State.set("noTodoData", true);
                }
            }).done(function () {
                //立即执行
                $(".todo-panel").find(".item").filter(function () {
                    return $(this).getRowData("Id") == DataBase.TaskId;
                }).find(".play-btn").click();
            });
        }

        // 关注、创建、完成列表
        function taskLis(type) {
            $.post('/backend/workbench', {
                Type: type,
            }).done(function (json) {
                if (json.Data.length > 0) {
                    var list = GenerateList(json.Data, '#my-mark-tpl');
                    $(".no-data,.todo-panel,.sift-panel").hide();
                    $(".mark-panel").show().find("ul").eq(0).empty().append(list);
                    $('.mark-panel .task-list li dt .completed').addClass('checked');
                    $("div.mark-panel li:not(.red)").css("opacity", "0.7").find("dd span").addClass("time").css("min-width", "0px");
                    setTimeout(function () {
                        $("*[limit]").limit();
                        $(".title_task").mouseover(function () {
                            $(this).addClass("task_titleColor");
                        }).mouseout(function () {
                            $(this).removeClass("task_titleColor");
                        });
                    }, 200);
                }
                else {
                    $(".mark-panel,.todo-panel,.sift-panel").hide();
                    $('.no-data').show();
                    State.set("noMarkData", true);
                }
            });
        }

        //添加任务成功
        function AddTaskSuccess(json) {
            if (json.Result == 0) {
                alert(json.Msg);
                var data = json.Data;
                //TreeView.InsertTask(GenerateList(data,"#task-tpl"));
                this.get(0).reset();
                //定时1.5秒刷新页面
                //$.reload(1.5);
                //我的待办
                myStayTask("");
                $("#new-task").close();
                $('#proSelectID').text('今日待办');
                //重置默认下拉切换样式变回（今日待办）
                $("#my-project-ul li").eq(0).addClass("activeLi").siblings().removeClass("activeLi");
                setTimeout(function(){
                    //扩展tip提示
                    $(".demoDown").tip_Title();
                },200);
            }
        }

        //筛选相关
        $("#sift-panel").find(".user-sift").remove();
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
            $(".no-data,.task-panel,.my-project-selector").hide();
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

        //没有数据
        State.on("change",function(){
            if(State.noTodoData && State.noMarkData){
                $(".no-data").show();
            }
        });
        $(".todo-panel").on("row.delete",function(){
            if($(this).find("li").length == 1){
                $(this).hide();
                State.set("noTodoData",true);
            }
        });
        $(".mark-panel").on("row.delete",function(){
            if($(this).find("li").length == 1){
                $(this).hide();
                State.set("noMarkData",true);
            }
        });
        $(".no-data").on("click",".add-task-button",function(){
            $(this).hide();
            $(".add-task-input").show().find("input").eq(0).focus();
        });
        $("div.task-panel .task-list").on("click",".progress,.checkbox",function(){
            setTimeout(function(){
                myStayTask("");
            },200);
        });
    </script>

    <script type="text/javascript">
        //根据项目取成员
        $("#edit-task").on("data.beforeload",function(e,data){
            var workPanel = $(this);
            var url=window.location.href;
            var urlArr=url.split('/');
            if(urlArr[urlArr.length-1]=="update" || urlArr[urlArr.length-1]=="workbench"){

            }else {
                GetMemberList(data.ProjectId).done(function(json){
                    workPanel.data("member",json.Data);
                });
            }
        });
    </script>

    <script type="text/javascript">
        //设置cookie
    	$.setCookie = function(key,val,time){
    		var date=new Date(); //获取当前时间
    		var expiresDays=time;  //将date设置为n天以后的时间
    		date.setTime(date.getTime()+expiresDays*24*3600*1000); //格式化为cookie识别的时间
    		document.cookie=key + "=" + val +";expires="+date.toGMTString();  //设置cookie
    	};
    	$.getCookie = function(key){
    		/*获取cookie参数*/
    		var getCookie = document.cookie.replace(/[ ]/g,"");  //获取cookie，并且将获得的cookie格式化，去掉空格字符
    		var arrCookie = getCookie.split(";")  //将获得的cookie以"分号"为标识 将cookie保存到arrCookie的数组中
    		var tips;  //声明变量tips
    		for(var i=0;i<arrCookie.length;i++){   //使用for循环查找cookie中的tips变量
    			var arr=arrCookie[i].split("=");   //将单条cookie用"等号"为标识，将单条cookie保存为arr数组
    			if(key==arr[0]){  //匹配变量名称，其中arr[0]是指的cookie名称，如果该条变量为tips则执行判断语句中的赋值操作
    				tips=arr[1];   //将cookie的值赋给变量tips
    				break;   //终止for循环遍历
    			}
    		}
    		return tips;
    	};
        $.deleteCookie = function(key){
            document.cookie=key + "=" +"; expires=Thu, 01 Jan 1970 00:00:00 UTC";   //设置到过去的时间
        }
    </script>

    <script type="text/javascript">
        //执行任务
        $(".todo-panel").on("click",".play-btn",function(){
            //执行任务
            PlayTask.call(this);
        });

        var TaskPlayer;
        function PlayTask(){
            var item = $(this).getRow();

            //任务控制器
            TaskPlayer = new function(task){
                var self = this;

                var _documentTitle = document.title;

                this.task = task;
                this.data = task.getRowData();
                this.container = $("#task-player");
                this.btn = {
                    pause: self.container.find(".pause-btn"),
                    continue_a: self.container.find(".continue-btn"),
                    giveUp: self.container.find(".give-up-btn"),
                    finish: self.container.find(".finish-btn")
                }

                //时长
                this.totalTime =function(){
                    //剩余时间
                    var nowTime = moment().getTime(10);
                    var startDate = parseInt(self.data.StartDate);
                    var dueDate = parseInt(self.data.DueDate);
                    var totalTime = dueDate - startDate;
                    if(totalTime < 1*60*60){
                        totalTime = 1*60*60;
                    }
                    return totalTime;
                }();
                this.pastTime = $.getCookie("task_pasttime"+self.data.Id) || DataBase.Duration || 0;
                this.restTime = this.totalTime - this.pastTime;
                if(this.restTime<0){
                    this.restTime = 3600;
                }

                //时间转换
                this.GetTimeString = function(time){
                    var second = time % 60;
        			var minute = Math.floor(time/60);
                    var hour = Math.floor(minute/60);
                    minute = minute % 60;

                    hour =  hour<10 ? '0'+hour : hour;
        			minute = minute<10 ? '0'+minute : minute;
        			second = second<10 ? '0'+second : second;

                    if(hour=="00"){
                        return minute + ":" + second;
                    }
                    else{
                        return hour + ":" + minute + ":" + second;
                    }

        		}

                //表盘刻度
                this.ClockPlate = new ClockPlate({
                    element: self.container.find("canvas")
                });

                //时间控制器
                this.Clock = new ClockObject({
                    restTime: self.restTime,
            		onTick: function(){

                        //更新时间
                        self.restTime = this.restTime;
                        self.pastTime++;
                        var pastTimeString = self.GetTimeString(self.pastTime);
                        self.container.find(".time").text(pastTimeString);

                        //更新网页title
                        document.title = pastTimeString+" - "+self.data.Title;

                        //更新刻度
                        var progress = function(){
                            if(self.pastTime > self.totalTime){
                                return (self.pastTime%self.totalTime)/self.totalTime;
                            }
                            else{
                                return self.pastTime / self.totalTime;
                            }

                        }();
                        self.ClockPlate.Update(progress);

                        //pastTime存放到cookie
                        $.setCookie("task_pasttime"+self.data.Id , self.pastTime);
                    },
            		onPause: function(){},
            		onContinue: function(){},
            		onEnd: function(){
                        self.ClockPlate.ChangeStyle();
                        this.Start(self.totalTime);    //计时结束，继续执行
                    }
                });

                //状态切换
                this.state = "normal";
                this.ChangeState = function(state){
                    self.container.removeClass(self.state).addClass(state);
                    self.state = state;
                    if(state=="playing"){
                        self.btn.pause.show();
                        self.btn.finish.show();
                        self.btn.giveUp.hide();
                        self.btn.continue_a.hide();
                    }
                    else if(state=="paused"){
                        self.btn.giveUp.show();
                        self.btn.continue_a.show();
                        self.btn.pause.hide();
                        self.btn.finish.hide();
                    }
                }

                //调整布局
                this.Adjust = function(test){
                    var base = ($(window).height()-450)/5;
                    if(base<20){
                        base=20;
                    }
                    self.container.css({ "padding-top":base*2});
                    self.container.find(".clock-plate").css({ "margin-top":base,"margin-bottom":base});
//                    console.log(base);
                }

                //开始
                this.Start = function(){
                    self.Clock.Start();
                    self.ChangeState("playing");

                    window.onbeforeunload = function(){ return "有任务正在执行"}

                    $.post("/backend/task/set_status",{
                        TaskId: self.data.Id,
                        UserId: DataBase.UserId,
                        Status: 2,
                        Duration: 0
                    });
                }

                //暂停
                this.Pause = function(){
                    self.Clock.Pause();
                    self.ChangeState("paused");
                    $.post("/backend/task/set_status",{
                        TaskId: self.data.Id,
                        UserId: DataBase.UserId,
                        Status: 2,
                        Duration: self.pastTime
                    });
                }

                //继续
                this.Continue_a = function(){
                    self.Clock.Continue_a();
                    self.ChangeState("playing");
                }

                //放弃任务
                this.GiveUp = function(){
                    $.post("/backend/task/set_status",{
                        TaskId: self.data.Id,
                        UserId: DataBase.UserId,
                        Status: 1,
                        Duration: 0
                    });
                    self.Quit();
                }

                //完成任务
                this.Finish = function(){
                    self.Quit();
                    self.task.find(".checkbox").click();
                }

                //退出
                this.Quit = function(){
                    $.deleteCookie("task_pasttime"+self.data.Id);   //清除cookie
                    self.Clock.Destroy();   //清除计时器
                    self.container.fadeOut();   //退出
                    self.ChangeState("normal"); //状态重置
                    document.title = _documentTitle;    //页面title
                    window.onbeforeunload = null;   //关闭离开提醒
                }

                //事件绑定
                this.EventBind = function(){
                    self.btn.pause.off("click.taskplayer").on("click.taskplayer",self.Pause);
                    self.btn.continue_a.off("click.taskplayer").on("click.taskplayer",self.Continue_a);
                    self.btn.giveUp.off("click.taskplayer").on("click.taskplayer",self.GiveUp);
                    self.btn.finish.off("click.taskplayer").on("click.taskplayer",self.Finish);
                }

                //初始化
                this.Init = function(){
                    self.EventBind();
                    self.container.find("h1").text(self.data.Title);
                    self.container.fadeIn();
                    self.Adjust();
                    self.Start();
                }

                this.Init();

            }(item);
        }

    	//计时器
    	var ClockObject = function(opt){
    		var self = this;
    		var _interval = null;

    		this.restTime = opt.restTime || 60;	//60秒

    		this.Tick = function(){
    			self.restTime--;
    			self.onTick();
    			if(self.restTime <= 0){
    				clearInterval(_interval);
                    _interval = null;
    				self.onEnd();
    			}
    		}

    		this.Start = function(time){
    			if(time){
    				self.restTime = time;
    			}
    			if(self.restTime <= 0){
    				return false;
    			}
    			if(!_interval){
    				_interval = setInterval(function(){
    					self.Tick();
    				},1000);
    			}
    		}
    		this.Pause = function(){
    			clearInterval(_interval);
    			_interval = null;
    			self.onPause();
    		}
            this.Continue_a = function(){
                self.Start();
                self.onContinue();
            }
            this.Destroy = function(){
                clearInterval(_interval);
                _interval = null;
            }
            this.Init = function(){
    			self.onTick();
    		}

    		this.onTick = $.proxy(opt.onTick,self);
    		this.onPause = $.proxy(opt.onPause,self);
    		this.onContinue = $.proxy(opt.onContinue,self);
    		this.onEnd = $.proxy(opt.onEnd,self);

    		this.Init();

    	}

        //表盘
        function ClockPlate(opt){

            var self = this;
            this.element = opt.element;
            this.progress = opt.progress || 0;


            var ctx,canvas=this.element.get(0);
            if (typeof window.G_vmlCanvasManager!="undefined") {
                canvas=window.G_vmlCanvasManager.initElement(canvas);
                ctx=canvas.getContext("2d");
            }else {
                ctx=canvas.getContext("2d");
            }

//          var ctx = this.element.get(0).getContext("2d");
            var canvasWidth = this.element.get(0).width;

            var _background = "#161e28";
            var _strokeColor = "#0087ff";

    		var DrawStroke = function(progress,color,strokeWidth,offset){
    			var r = canvasWidth/2 - offset - strokeWidth/2;
                var x = canvasWidth/2;
    			var y = canvasWidth/2;
    			var start = -Math.PI/2;
    			var end = Math.PI * 2 * progress - Math.PI/2;

    			ctx.strokeStyle = color;
                ctx.lineWidth = strokeWidth;
    			ctx.beginPath();
    			ctx.arc(x,y,r,start,end);
    			ctx.stroke();
    		}

            this.Update = function(progress){
                //更新刻度
                ctx.clearRect(0,0,canvasWidth,canvasWidth);
                DrawStroke(1,_background,6,0);
                DrawStroke(progress,_strokeColor,6,0);
                DrawStroke(progress,_strokeColor,1,11);
            }

            this.SetStyle = function(background,strokeColor){
                _background = background;
                _strokeColor = strokeColor;
            }

            this.ChangeStyle = function(){
                var background = _strokeColor;
                var r,g,b;

                var randPosition = function(num){
                    var rand = Math.random()*num;
                    rand = Math.floor(rand);
                    return rand;
                }
                var randVal = function(){
                    var rand = Math.random()*99;
                    rand = Math.floor(rand);
                    if(rand<10){
                        rand = "0"+rand;
                    }
                    return String(rand);
                }
                var array = ["00","ff",randVal()];

                r = array.splice(randPosition(3),1)[0];
                g = array.splice(randPosition(2),1)[0];
                b = array[0];

                var strokeColor = "#"+r+g+b;
                self.SetStyle(background,strokeColor);
            }

            this.Init = function(){
                self.Update(self.progress);
            }
            this.Init();

        }

        $('#edit-task form>dl:first-child dt label').on('click',function(){
            if($(this).hasClass('checked')){
                $(this).removeClass('checked');
                $('div.panel:not(:hidden)').find('li.item.active').find('dt:first-child>div').removeClass('checked');
                var id=$(this).parents('div.content').find('div.params input[name=Id]').val(),progress=0;
                $('div.panel:not(:hidden)').find('li.item.active>dl').removeClass('completed');
                checkbox_post(progress,id);
                return false;
            }else{
                $(this).addClass('checked');
                $('div.panel:not(:hidden)').find('li.item.active').find('dt:first-child>div').addClass('checked');
                var id=$(this).parents('div.content').find('div.params input[name=Id]').val(),progress='on';
                $('div.panel:not(:hidden)').find('li.item.active>dl').addClass('completed');
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

        $("body").on("click",'div.todo-panel li.item dd a.title_task',function(e){
            var e = e || window.event;
            if(e.stopPropagation){
                e.stopPropagation();
            }else{
                e.cancelBubble = true;
            }
        });

        $("body").on("click",'div.sift-panel li.item dd a.title_task',function(e){
            var e = e || window.event;
            if(e.stopPropagation){
                e.stopPropagation();
            }else{
                e.cancelBubble = true;
            }
        });

        //触发任务(项目Select)编辑时 异步刷新 我的待办任务列表
        $("div#edit-task form select[name=ProjectId]").on("change",function(){
            setTimeout(function(){
                myStayTask("");
            },200);
        });

        //点击筛选按钮提交后隐藏 上方的下拉列表
        //$("div#sift-panel button[type=submit]").on("click",function(){
        //$("div.my-project-selector").hide();
        //});

        //截取字符  为7
        setTimeout(function(){
            //截取字符
            $("*[limit]").limit();

            //鼠标移入项目名称上时变色
            $(".title_task").mouseover(function(){
                $(this).addClass("task_titleColor");
            }).mouseout(function(){
                $(this).removeClass("task_titleColor");
            });
        },200);

        //任务编辑关闭时监控任务是否被添加进了收集箱
        $("#edit-task").on("close", function () {
            var activeBool = $("#edit-task").find(".collect-button").eq(0).hasClass("active");
            if (activeBool==true){
                $("div.panel:not(:hidden)").find("li.active").stop().fadeToggle('slow');
            }
        });

        //比较任务编辑面板标题与列表中标题是否一致，不一致以编辑面板标题为主
        $("#edit-task").on("open",function(){
//            switch($("#my-project-ul li.activeLi").find('a').attr("datatype")){
//                case "":
//                    $('#my-project-ul li:first-child').trigger("click");
//                    break;
//                case "Created":
//                    $('#my-project-ul li').eq(1).trigger("click");
//                    break;
//                case "Finished":
//                    $('#my-project-ul li').eq(3).trigger("click");
//                    break;
//                case "All":
//                    $('#my-project-ul li').eq(4).trigger("click");
//                    break;
//                default:
//                    $.reload(1);
//                    break;
//            }
            var _that = $(this), title_a = "",title_b="",urgency_a,urgency_b;
            setTimeout(function () {
                title_a = _that.find("input[name=Title]").val();
                title_b = $("div.panel:not(:hidden)").find("li.active dd>span").eq(0).text();
                urgency_a = _that.find("#urgency_btn").hasClass("checked");
                urgency_b = $("div.panel:not(:hidden)").find("li.active dd>span").eq(1);
                if (title_a != title_b) {
                    $("div.panel:not(:hidden)").find("li.active dd>span").eq(0).text(title_a);
                }
                if (urgency_a == true) {
                    if (urgency_b.length == 0) {
                        $("div.panel:not(:hidden)").find("li.active dd>span").eq(0).after('<span class="icon icon-priority" style="margin-top:0px;"></span>');
                    }
                }
            }, 200);
        });
    </script>
{/block}
