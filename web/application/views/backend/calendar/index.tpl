{extends "layouts/layout.tpl"}

{block styles}
<style media="screen">
    #viewport {
        padding: 0;
    }
    /*新建任务的时候页面不缩放*/
    .work-panel-open #viewport{
        margin-right: 0;
    }
    .work-panel-open .header{
        right: 0;
    }

    #dbl-new-task.work-panel{
        bottom: auto;
        width: 400px;
    }
    #dbl-new-task.work-panel .title{
        text-align: center;
        color: #aaaaaa;
    }
    #dbl-new-task.work-panel .close-btn{
        display: none;
    }
    #dbl-new-task.work-panel .content-wrap{
        padding: 20px 10%;
    }
    #dbl-new-task.work-panel .form-panel .submit-buttons{
        margin-top: 20px;
    }
    #dbl-new-task.work-panel .content-wrap{
        overflow: inherit;
    }

    /*修改日历的样式*/
    /*将顶部工具栏固定到菜单栏上*/
    .fc-toolbar {
        position: fixed;
        min-width: 500px;
        top: 10px;
        left: 50%;
        margin-left: -250px;
        margin-bottom: 0;
        z-index: 100;
    }
    /*前后按钮去掉背景色边框*/
    .fc-center .fc-prev-button, .fc-center .fc-next-button{
        background-color: transparent;
        border: none;
        padding: 0;
        color: #cccccc;
    }
    /*标题字体大小*/
    .fc-toolbar h2{
        font-size: 14px;
        padding: 5px;
        letter-spacing: 2px;
        font-family: "Microsoft Yahei", serif;
        color: #fff;
    }
    /*按钮的样式*/
    .fc button{
        padding: 0 1.5em;
        color: #000000;
        -webkit-box-shadow: none;
        -moz-box-shadow: none;
        box-shadow: none;
        text-shadow: none;
        background: #fff none;
        border:1px solid #D9D9D9;
    }
    /*按钮被选中的状态*/
    .fc button.fc-state-active{
        background-color: #01ADFF;
        color: #ffffff;
        border: none;
    }

    /*表头的样式,去掉边框,增大距离*/
    .fc-head .fc-head-container .fc-day-header{
        padding: 5px;
        text-align: left;
        border: none;
        font-weight: 300;
    }
    /*当前天的颜色*/
    .fc-unthemed .fc-today{
        background-color: #EDF9FF;
        color: #01ADFF;
    }
    /*月视图文字方向*/
    .fc-ltr .fc-basic-view .fc-day-number{
        text-align: left;
    }

    /*时间线颜色*/
    .fc-time-grid .fc-now-indicator-line{
        border-color: #79CDF3;
    }
    /*时间线三角颜色*/
    .fc-now-indicator{
        border: 0 solid #79CDF3;
    }

    /*虚线*/
    .fc-time-grid .fc-slats .fc-minor td{
        border-top-style: dotted;
    }
    /*显示出线*/
    tbody,thead,tfoot,tr,th,td{
        border:none;
    }

    /*周视图左边时间的背景色*/
    .fc-view-container .fc-view.fc-agendaWeek-view .fc-axis{
        background-color: #F1F1F1!important;
        width: 41px!important;
    }
    /*周视图任务样式修改*/
    .fc-time-grid .fc-bgevent, .fc-time-grid .fc-event{
        border-top: none;
        border-right: none;
        border-bottom: none;
        border-left-width: 2px;
        -webkit-border-radius:0;
        -moz-border-radius:0;
        border-radius:0;
    }
    /*月视图任务样式修改*/
    .fc-ltr .fc-h-event, .fc-rtl .fc-h-event{
        border-top: none;
        border-right: none;
        border-bottom: none;
        border-left-width: 2px;
        -webkit-border-radius:0;
        -moz-border-radius:0;
        border-radius:0;
    }
    /*去掉任务的等号*/
    .fc-time-grid-event .fc-resizer:after{
        content: "";
    }
    /*字体大小*/
    .fc-event{
        font-size: 1em;
    }
    /*月视图文字方向*/
    a.fc-day-grid-event.fc-h-event.fc-event.fc-start.fc-end.fc-draggable{
        padding: 5px;
        text-align: left;
    }
    /*周视图文字方向*/
    .fc-event .fc-content{
        padding: 5px;
        text-align: left;
    }

    /*周视图任务留出空间给双击添加使用*/
    .fc-ltr .fc-time-grid .fc-event-container{
        margin: 0 10% 0 2px;
    }
    .fc-widget-content .fc-scroller{
        overflow: hidden;
    }
    .fixDiv{
        position: fixed;
        z-index: 10;
    }
    #edit-task-calendar .content-wrap form dl:first-child dt:before{
        content: '';
    }
    .project-selector:hover{
        background-color:rgba(0,0,0,0.3);
    }
</style>
{/block}
{block bg}
    <div class="background" style="display: none;"></div>
{/block}
{block menuTitle}<a href="/backend/calendar/index">日历</a>{/block}

{block content}
    <div id="calendar"></div>
    {include "includes/work-panel.tpl" }
    {include "includes/work-panel-task.tpl" }
    {include "includes/work-panel-sift-task.tpl" }
{/block}
{block setting}
    <script type="text/javascript">
        DataBase.ProjectList = {$ProjectList|@json_encode};
    </script>
{/block}
{block leftMenu}
    <div class="project-selector menu-left-down dropdown">
        <a class="" data-toggle="dropdown" href="#" ><span id="chPro" style="color: #fff;">筛选项目</span><span class="icon icon-arrow-down"></span></a>
        <div class="dropdown-menu project-selector-popover" id="project-popover" >
            <div class="search">
                <input type="text" placeholder="搜索项目">
            </div>
            <div class="list narrow projectlist">
                <ul>
                    {foreach from=$ProjectList key=k item=v name=foo}
                        <li class="item" data-refer="DataBase.ProjectList[{$smarty.foreach.foo.index}]">
                            <dt style="width: 30px;">
                                <label class="checkbox">
                                    <input type="checkbox" name="projectId" value="{$v.Id}">
                                </label>
                            </dt>
                            <dd title="{$v.Title}">
                                {$v.Title}
                            </dd>
                        </li>
                    {/foreach}
                </ul>
            </div>
            <div>
                <button type="button" class="btn btn-large btn-blue get-new-task" style="display: block;margin: 0 auto;">确定</button>
            </div>
        </div>

    </div>

    <div class="project-selector menu-left-down dropdown">
        <a class="" data-toggle="dropdown" href="#"><span id="chPro2" style="color: #fff;">全部</span><span class="icon icon-arrow-down"></span></a>

        <div class="dropdown-menu project-selector-popover" id="project-popover1" >

            {*<div class="search">*}
                {*<input type="text" placeholder="搜索项目">*}
            {*</div>*}
            <div class="list narrow conditionlist">
                <ul>
                    {*<li class="item" data-type="0">*}
                        {*<dt style="width: 30px;">*}
                            {*<label class="checkbox">*}
                                {*<input type="checkbox">*}
                            {*</label>*}
                        {*</dt>*}
                        {*<dd>*}
                            {*全部*}
                        {*</dd>*}
                    {*</li>*}
                    <li class="item" data-type="1">
                        <dt style="width: 30px;">
                            <label class="checkbox">
                                <input type="checkbox">
                            </label>
                        </dt>
                        <dd>
                            我的待办<span class="icon icon-blue"></span>
                        </dd>
                    </li>
                    <li class="item" data-type="3">
                        <dt style="width: 30px;">
                            <label class="checkbox">
                                <input type="checkbox">
                            </label>
                        </dt>
                        <dd>
                            我的关注<span class="icon icon-yellow"></span>
                        </dd>
                    </li>
                    <li class="item" data-type="2">
                        <dt style="width: 30px;">
                            <label class="checkbox">
                                <input type="checkbox">
                            </label>
                        </dt>
                        <dd>
                            我的已办<span class="icon icon-gray"></span>
                        </dd>
                    </li>
                </ul>
            </div>
            <div>
                <button type="button" class="btn btn-large btn-blue get-new-task" style="display: block;margin: 0 auto;">确定</button>
            </div>

        </div>
    </div>
    <div style="display:none;position: fixed;top: 0; right: 0; bottom: 0; left: 0;background-color:transparent;z-index: 35; " id="div_mask">
    </div>
{/block}

{block rightMenu}
    <a href="#new-task" data-toggle="work-panel" class="submenu">
        <i class="iconfont icon-xinjian" style="font-size:19px;margin-right:10px;"></i>新建任务
        {*<span class="icon-new-task-fff icon"></span>*}
    </a>
{/block}

{block scripts}
    {include "includes/datetimepicker.tpl"}
    <link rel='stylesheet' href='{$CommonUrl}/plugins/fullcalendar/dist/fullcalendar.min.css'/>
    <script src="{$CommonUrl}/plugins/fullcalendar/dist/fullcalendar.min.js"></script>

    <script>
        $(function () {
            //扩展tip提示
            setTimeout(function(){
                $(".demoDown").tip_Title();
            },200);

            $(".work-panel").on("set.value", "[name=CompleteProgress]", function(event, data){
                $(this).siblings(".checkbox")
                        .find("input").prop("checked", data.value == 100)
                        .trigger("set.appearance");
            }).on("change","[name=Progress]", function(){
            });

            var lastClickEvent = null;
            var lastClickEventThis = null;
            $('#calendar').fullCalendar({
                defaultView: 'agendaWeek',//默认视图
                //顶部菜单栏
                header: {
                    left: '',
                    center: 'prev, title, next',
                    right: 'agendaDay,agendaWeek,month'
                },
                buttonText: {
                    today: '今天',
                    month: '月',
                    week: '周',
                    day: '日'
                },
                buttonIcons:{
                    prev: 'left-single-arrow',
                    next: 'right-single-arrow'
                },
                timezone: "local",
                isRTL: false, //是否从右到左
                columnFormat: 'M/D ddd', //表头显示格式
                weekNumbers: false,//是否显示第几周
                timeFormat: 'H(:mm)', //时间显示格式
                slotLabelFormat: 'HH:mm', //左边栏显示的格式
                slotDuration: "00:30:00", //时间间隔
                displayEventTime: false, //不显示任务的时间
//                handleWindowResize:true,//是否随浏览器窗口大小变化而自动变化。
                titleFormat: 'YYYY年MM月DD日',
                monthNames: ['一月', '二月', '三月', '四月', '五月', '六月', '七月',
                    '八月', '九月', '十月', '十一月', '十二月'],
                dayNames: ['周日', '周一', '周二', '周三',
                    '周四', '周五', '周六'],
                dayNamesShort: ['周日', '周一', '周二', '周三',
                    '周四', '周五', '周六'],
                allDayText: '全天',
//                aspectRatio: 2,//宽高比, 宽是高的2倍
                contentHeight: 1150,
                //根据不同的视图配置
                views: {
                    month: {
                        titleFormat: 'YYYY年M月',
                        columnFormat: 'ddd'
                    },
                    agendaWeek: {

                    }
                },
                slotEventOverlap: false, //设置任务重叠的时候不重叠
                nowIndicator: true,
                events: function(start, end, timezone, callback) {
                    var calendarUrl = "/backend/calendar/calendar_tasks_by_params";
                    var p = {
                        startDate: start.unix(),
                        DueDate: end.unix(),
                        ProjectId: arrayValue.project || "",
		                Type: arrayValue.condition || ""
                    };

                    if($("#calendar").fullCalendar( 'getView' ).name == 'month'){
                        DataBase.MonthObj = p;
                    }

                    if($("#calendar").fullCalendar( 'getView' ).name == 'agendaWeek'){
                        DataBase.WeekObj = p;
                    }

//                    if($("#calendar").fullCalendar( 'getView' ).name == 'today'){
//                        DataBase.TodayObj = p;
//                    }

                    $.ajax({
                        url: calendarUrl,
                        type: "post",
                        dataType: 'json',
                        data: p,
                        success: function(doc) {
                            var events = [];
                            var data = doc.Data;
                            for(var i = 0, len = data.length; i < len; i++) {
                                switch (parseInt(data[i].Type)){
                                    case 1://我的待办
                                        events.push({
                                            id: data[i].Id,
                                            title: data[i].Title,
                                            start: moment.unix(data[i].StartDate), // will be parsed
                                            end: moment.unix(data[i].DueDate),
                                            color: '#CBEFFF',
                                            backgroundColor: '#CBEFFF',
                                            borderColor: '#00ACFE',
                                            textColor: '#000000'
                                        });
                                        break;
                                    case 2://我的已办
                                        events.push({
                                            id: data[i].Id,
                                            title: data[i].Title,
                                            start: moment.unix(data[i].StartDate), // will be parsed
                                            end: moment.unix(data[i].DueDate),
                                            color: '#CBEFFF',
                                            backgroundColor: '#DEDEDE',
                                            borderColor: '#585858',
                                            textColor: '#000000'
                                        });
                                        break;
                                    case 3://我的关注
                                        events.push({
                                            id: data[i].Id,
                                            title: data[i].Title,
                                            start: moment.unix(data[i].StartDate), // will be parsed
                                            end: moment.unix(data[i].DueDate),
                                            color: '#CBEFFF',
                                            backgroundColor: '#FFF7D8',
                                            borderColor: '#FFD83F',
                                            textColor: '#000000'
                                        });
                                        break;
                                    default:
                                        break;
                                }
                            }
                            callback(events);
                        }
                    });
                },
                //callback，当日程事件渲染时触发，用法：
                eventRender: function(event, element){
                    var data = {
                        Id: event.id,
                        Name: event.title
                    };
                    $(element).data("data", data);
                    $(element).attr({
                        "href": "#edit-task-calendar",
                        "data-toggle": "work-panel"});
                    $(element).addClass("edit-btn");
                    $(element).addClass("item");
                },
                //点击事件(任务)的事件
                eventClick: function(event, jsEvent, view) {
                    if(lastClickEventThis){
                        lastClickEventThis.css("background-color", lastClickEvent.backgroundColor);
                        lastClickEventThis.css("color", lastClickEvent.textColor);
                    }
                    $(this).css("background-color", event.borderColor);
                    $(this).css("color", "white");
                    lastClickEventThis = $(this);
                    lastClickEvent = event;

                },
                editable: true,
                selectable: true,
                ignoreTimezone: false,
                //拖动任务结束时请求ajax
                eventDrop: function(event, delta, revertFunc) {

                    var url = '/backend/task/edit';
                    var params = {
                        Id: event.id,
                        StartDate: event.start.unix(),
                        DueDate: event.end.unix()
                    };

                    $.post(url, params, function(res){
                        if(res.Result == 0){
                            alert(res.Msg);
                        }else{
                            alert(res.Msg);
                            revertFunc();
                        }
                    });

                },
                eventResize: function(event, delta, revertFunc) {
                    var url = '/backend/task/edit';
                    var params = {
                        Id: event.id,
                        DueDate: event.end.unix()
                    };
                    $.post(url, params, function(res){
                        if(res.Result == 0){
                            alert(res.Msg);
                        }else{
                            alert(res.Msg);
                            revertFunc();
                        }
                    });
                }
            });
            var calendar = $("#calendar").fullCalendar("getCalendar");
            var doubleClick = null;
            //双击事件
            calendar.on("dayClick", function(date, jsEvent, view) {
                var singleClick = moment(date).format("YYYY-MM-DD HH:mm:ss");

                $("#dbl-new-task").data("startTime", singleClick);
                if(doubleClick==singleClick){
                    doubleClick = null;

                    var a = $('<a href="#dbl-new-task" data-toggle="work-panel" class="submenu"></a>');
                    $("body").append(a);
                    a.click();
                    a.remove();
                    //400,424
                    var top = jsEvent.pageY- $(window).scrollTop();
                    var left = jsEvent.pageX - $("#dbl-new-task").width()/2;
                    if(left < 400){
                        left = jsEvent.pageX;
                    }
                    if(left > $(window).width() - 400){
                        left = left - 200;
                    }
                    if(top > ($(window).height()-424)){
                        top = top - 424;
                    }
                    $("#dbl-new-task").css({
                        "top":top,
                        "left":left
                    });

                }else{
                    doubleClick=moment(date).format("YYYY-MM-DD HH:mm:ss");
                    clearInterval(clickTimer);
                    var clickTimer = setInterval(function(){
                        doubleClick = null;
                        clearInterval(clickTimer);
                    }, 500);
                }

                var DefaultFormat = "YYYY/MM/DD HH:mm";
                var newStart = moment(singleClick).format(DefaultFormat);
                $("#dbl-new-task").find("[name='StartDate']").val(newStart);

                //在开始时间向后延长1小时
                var endStart = moment(singleClick).add(30,'minute').format(DefaultFormat);
                $("#dbl-new-task").find("[name='DueDate']").val(endStart);
            });
            calendar.on("select",function(startDate, endDate, jsEvent, view){
                startDate=moment(startDate).format(DefaultFormat);
                endDate=moment(endDate).format(DefaultFormat);
                var a = $('<a href="#dbl-new-task" data-toggle="work-panel" class="submenu"></a>');
                $("body").append(a);
                a.click();
                a.remove();
                //400,424
                var top = jsEvent.pageY- $(window).scrollTop();
                var left = jsEvent.pageX - $("#dbl-new-task").width()/2;
                if(left < 400){
                    left = jsEvent.pageX;
                }
                if(left > $(window).width() - 400){
                    left = left - 200;
                }
                if(top > ($(window).height()-424)){
                    top = top - 424;
                }
                $("#dbl-new-task").css({
                    "top":top,
                    "left":left
                });
                $("#dbl-new-task").find("[name='StartDate']").val(startDate);
                $("#dbl-new-task").find("[name='DueDate']").val(endDate);
            });
        });

        //选择项目成员，默认只有自己
        $("#dbl-new-task").on("open reseting", function () {
            var data = [{
                Avatar: DataBase.UserInfo.Avatar,
                UserId: DataBase.UserInfo.Id,
                UserName: DataBase.UserInfo.Name
            }];
            $(this).find("[name=AssignedTo]").setValue(data);
        });
        $("#dbl-new-task").on("open",function(){
            $("#div_mask").show();
        });
        $("#dbl-new-task").on("close",function(){
            $("#div_mask").hide();
        });
        $("#div_mask").on("click",function(){
            $("#div_mask").hide();
        });
        //添加任务成功
        function AddTaskSuccess(json) {
            if (json.Result == 0) {
                alert(json.Msg);
                var data = json.Data;
                //TreeView.InsertTask(GenerateList(data,"#task-tpl"));
                this.get(0).reset();
                $('#dbl-new-task').close();
                $("#calendar").fullCalendar('refetchEvents');
//                $.reload(1.5);
            }
        }
        //点击筛选项目按钮和全部按钮，关闭面板
        $('.get-new-task').on('click',function(){
            $(this).parents('.dropdown').close();
        });
        //隐藏弹出面板中的所属项目
        $('#dbl-new-task .form-group').eq(4).hide();
        //将日历弹出框置于顶层
        $("#dbl-new-task .time-picker-group input").siblings().attr("style","position:fixed;z-index:10;");
    </script>

    {if $TestMode}
        <script type="text/javascript">
            console.info("ProjectList",{$ProjectList|@json_encode});
        </script>
    {/if}

{/block}