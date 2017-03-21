{extends "layouts/layout.tpl"}
{block styles}
<style media="screen">
    .no-data .add-project-button{
        display: block;
        width: 305px;
        height: 305px;
        text-align: center;
        padding-top: 120px;
    }
    .no-data .add-project-button:hover{
        background: rgba(0, 0, 0, 0.5);
    }
    .group-title{
        color: white;
        padding: 15px 10px;
        font-size: 16px;
    }
    .project-card canvas{
        position: relative;
        cursor: pointer;
        font-size: 18px;
        color: #00bb41;
    }
       .project-card .title{
        /*margin-top: 80px\9;*/
    }

    #project-member .avatar .delete{
        display: none;
    }
    /*我负责的*/
    /*.header .title.myProject{*/
        /*border-left: 0px;*/
        /*border-right: 1px solid #cccccc;*/
        /*line-height: 50px;*/
        /*color: #00AEFF;*/
    /*}*/

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
    }
    .dropdown-menu .list ul li{
        color: #666;
        line-height: 35px;
        text-align: center;
        -webkit-border-radius: 5px;
        -moz-border-radius: 5px;
        border-radius: 5px;;
    }
    .dropdown-menu .list ul li:hover{
        color: #fff;
        background-color: #1CAAEC;
    }
    .project-card{
        -webkit-border-radius: 5px;
        -moz-border-radius: 5px;
        border-radius: 5px;
    }
    .project-card .progress{
        width: 58%;
        bottom: 15px;
        margin: 0 auto;
        background-color: #ccc;
    }
    .form-panel .time-picker-group .required:last-child dt:before{
        content: '';
    }
    .form-panel .required dt:before{
        content: '';
    }
</style>
{/block}

{block menuTitle}<a href="/backend/project/overview">项目</a>{/block}

{*{block myMenuTitle}<a href="javascript:void(0)">我负责的 <span class="icon icon-arrow-down"></span></a>{/block}*}

{block leftMenu}
    <div class="my-project-selector menu-left-down dropdown">
        <a class="" data-toggle="dropdown" href="#">
            <span class="text-head text-overflow" id="proSelectID" style="max-width: 8em;display:inline-block;vertical-align:middle;font-size: 14px;color: #fff;">我负责的</span>
            <span class="icon icon-arrow-down"></span>
        </a>
        <div class="dropdown-menu project-selector-popover t-left" id="project-popover" >
            <div class="list narrow projectlist" style="width:85px;margin-bottom: 0px;">
                <ul id="my-project-ul">
                    <li><a href="javascript:void(0)" class="text-overflow" dataType="">我负责的</a></li>
                    <li><a href="javascript:void(0)" class="text-overflow" dataType="participant">我参与的</a></li>
                    <li><a href="javascript:void(0)" class="text-overflow" dataType="attention">我关注的</a></li>
                </ul>
            </div>
        </div>
    </div>
{/block}
{block rightMenu}
    <a class="submenu" href="#new-project" data-toggle="work-panel">
        <i class="iconfont icon-xinjian" style="font-size:19px;margin-right:10px;"></i>新建项目
    </a>
    {*<a class="submenu" href="#sift-panel" data-toggle="work-panel"><span class="icon-filter icon"></span>筛选</a>*}
{/block}

{*{block bg}*}
    {*<div class="background">*}
    {*</div>*}
{*{/block}*}


{block content }
    {*<div class="group-title">我负责的 (<span class="amount">{count($MyProject)}</span>)</div>*}
    {*{if count($MyProject)>0}hide{/if}*}
    <div class="row project-container my-project-list">
        <div class="no-data" style="display: none;" data-count="{count($MyProject)|default:0}">
            <a class="bg-glass border-radius add-project-button" href="#new-project" data-toggle="work-panel">
                <p>
                    {*<span class="icon icon-plus-circle-white"></span>*}
                    <i class="iconfont icon-xinjian" style="font-size:30px;"></i>
                </p>
                <p class="large" style="margin-top:10px;">新建项目</p>
            </a>
        </div>
	</div>

    {*<div class="group-title">我参与的 (<span class="amount">{count($Participant)}</span>)</div>*}
    {*{if count($Participant)>0}hide{/if}*}
    <div class="row project-container participant-project-list">
        <div class="no-data" style="display: none;" data-count="{count($Participant)|default:0}">
            <a class="bg-glass border-radius add-project-button" href="#new-project" data-toggle="work-panel">
                <p>
                    {*<span class="icon icon-plus-circle-white"></span>*}
                    <i class="iconfont icon-xinjian" style="font-size:30px;"></i>
                </p>
                <p class="large" style="margin-top:10px;">新建项目</p>
            </a>
        </div>
    </div>

    {*<div class="group-title">我关注的 (<span class="amount">{count($AttProject)}</span>)</div>*}
    {*{if count($AttProject)>0}hide{/if}*}
    <div class="row project-container mark-project-list">
        <div class="no-data" style="display:none;" data-count="{count($AttProject)|default:0}">
            <a class="bg-glass border-radius add-project-button" href="#new-project" data-toggle="work-panel">
                <p>
                    {*<span class="icon icon-plus-circle-white"></span>*}
                    <i class="iconfont icon-xinjian" style="font-size:30px;"></i>
                </p>
                <p class="large" style="margin-top:10px;">新建项目</p>
            </a>
        </div>
    </div>

    <div class="dialog confirm-dialog" id="project-merge-dialog" style="display:none;">
        <div class="outer" data-allow-close="true">
            <div class="inner">
                <form class="ajax-form" action="/backend/project/create" method="post" data-success="AddSuccess">
                    <div class="title">合并项目</div>
                    <div class="content">
                        <input type="text" name="Title" placeholder="项目名称">
                        <input type="hidden" name="ChildIds" value="0">
                    </div>
                    <div class="button-row">
                        <button type="submit" class="btn btn-blue confirm-btn">保存</button>
                        <button type="button" class="btn" data-dismiss="dialog">取消</button>
                    </div>
                </form>

            </div>
        </div>
    </div>

{include "includes/work-panel.tpl" }
{include "includes/work-panel-project.tpl" }

{include "includes/work-panel-task.tpl" }
{include "includes/work-panel-sift-task.tpl" }

{/block}

{block setting}
<script type="text/javascript">
    DataBase.MyProject = {$MyProject|@json_encode};
    DataBase.Participant = {$Participant|@json_encode};
    DataBase.AttProject = {$AttProject|@json_encode};
</script>

{/block}

{block scripts}


{literal}
<script type="text/template" id="project-tpl">
    <div class="project-card item" {{if !ChildCount}}draggable=true{{/if}} data-src="/backend/task/task_tree?ProjectId={{Id}}" data-tpl="#project-tpl">
        {{if ChildCount > 0}}<span class="tag tag-orange absolute top left" style="top:15px;left:15px;">{{ChildCount}}个子项目</span>{{/if}}
        <canvas data-value="{{CompleteProgress}}" width="110" height="110">{{CompleteProgress}}</canvas>
        <p class="title text-nowrap"><a href="/backend/task/task_tree?ProjectId={{Id}}">{{Title}}</a></p>
        <div>
            <div class="avatar"><img src="{{Avatar}}" class="demo demoDown" tip_Title="{{ProjectManager}}" alt="" {{if Status == 0}}class="filtergray"{{/if}}></div>
        </div>
        <div class="buttons">
            <a href="{{if ChildCount > 0}}#edit-main-project{{else}}#edit-project{{/if}}" data-toggle="work-panel" class="edit-btn demo demoDown" tip_Title="编辑">
                <i class="iconfont icon-miaoshu" style="font-size:18px;"></i>
            </a>
            <a href="/backend/statistics?ProjectId={{Id}}" class="demo demoDown" tip_Title="报表">
                <i class="iconfont icon-baobiao" style="font-size:20px;"></i>
            </a>
            <a href="/backend/milestone?ProjectId={{Id}}" class="demo demoDown" tip_Title="里程碑">
                <span class="icon icon-milestone"></span>
            </a>
            <a href="#" class="delete-btn absolute top right demo demoDown" tip_Title="删除" style="top:10px;right:10px;">
                <i class="iconfont icon-shanchu" style="font-size:27px;"></i>
            </a>
        </div>
        <div class="row time-range">
            <span class="start-time pull-left" timeVal="{{StartDate}}">{{StartDateString}}</span>
            <span class="end-time pull-right" timeVal="{{DueDate}}">{{DueDateString}}</span>
        </div>
        <div class="progress">
            <div class="bar" style="width:{{DateProgress}}%;background-color: #00bb41;"></div>
        </div>
    </div>
</script>
{/literal}

{include "includes/datetimepicker.tpl"}
{include "includes/draw-canvas.tpl" selector=".project-container canvas"}

<script type="text/javascript">
    //初始渲染
    $(function(){
        $('.my-project-list').css('display','block');
        $('.participant-project-list').css('display','none');
        $('.mark-project-list').css('display','none');
        //如果“我负责的”没有数据就显示“我负责的”项目的新建按钮
        if($('.my-project-list .no-data').attr('data-count')==0){
            $('.my-project-list .no-data').removeClass('hide');
        }

        //扩展tip提示
        $(".demoDown").tip_Title();
    });

    //我负责的
    $(".my-project-list").append(function(){
        var array = $();
        $.each(DataBase.MyProject,function(k,v){
            var item = GenerateList(v,"#project-tpl");
            array = array.add(item);
        });
        return array;
    });

    //效果 淡出
    $('.my-project-selector').hover(function(){
        $('#project-popover').css('display','block').addClass('fadeInShow');
    },function(){
        $('#project-popover').css('display','none').removeClass('fadeInShow');
    });

    //项目切换点击
    $('#my-project-ul li').on('click',function(){
        var type=$(this).children().attr('dataType');
        switch (type){
            case 'participant':
                $('#proSelectID').text('我参与的');
                myProjectAjax(type);
                break;
            case 'attention':
                $('#proSelectID').text('我关注的');
                myProjectAjax(type);
                break;
            default:
                $('#proSelectID').text('我负责的');
                myProjectAjax(type);
                break;
        }
    });
    //切换项目调用公用Ajax
    function myProjectAjax(type){
        $.ajax({
            type:'post',
            url:'/backend/project/overview',
            data:{
                Type:type
            },
            cache:false,
            success:function(data){
                var obj=JSON.parse(data);
                if(obj.Result==0){
                    switch (type){
                        case 'participant':
                            if(obj.Data.length>0){
//                                $('.participant-project-list .no-data').hide();
                                $('.participant-project-list').show();
                                $('.participant-project-list').html(GenerateList(obj.Data, '#project-tpl'));
                                DrawCanvas(".participant-project-list canvas");
                            }else{
                                $('.participant-project-list').show();
                                $('.participant-project-list .no-data').show()
                            }
                            $('.my-project-list,.mark-project-list').hide();
                            //扩展tip提示
                            $(".demoDown").tip_Title();
                            break;
                        case 'attention':
                            if(obj.Data.length>0) {
//                                $('.mark-project-list .no-data').hide();
                                $('.mark-project-list').show();
                                $('.mark-project-list').html(GenerateList(obj.Data, '#project-tpl'));
                                DrawCanvas(".mark-project-list canvas");
                            }else{
                                $('.mark-project-list').show();
                                $('.mark-project-list .no-data').show();
                            }
                            $('.my-project-list,.participant-project-list').hide();
                            //扩展tip提示
                            $(".demoDown").tip_Title();
                            break;
                        default:
                            if(obj.Data.length>0) {
//                                $('.my-project-list .no-data').hide();
                                $('.my-project-list').show();
                                $('.my-project-list').html(GenerateList(obj.Data, '#project-tpl'));
                                DrawCanvas(".my-project-list canvas");
                            }else{
                                $('.my-project-list').show();
                                $('.my-project-list .no-data').show();
                            }
                            $('.participant-project-list,.mark-project-list').hide();
                            //扩展tip提示
                            $(".demoDown").tip_Title();
                            break;
                    }
                }else{
                    alert(obj.Msg);
                }
            },
            error:function(){
                alert('服务器异常!');
            }
        });
    }

//我参与的
//$(".participant-project-list").append(function(){
//    var array = $();
//    $.each(DataBase.Participant,function(k,v){
//        var item = GenerateList(v,"#project-tpl");
//        array = array.add(item);
//    });
//    return array;
//});
////我关注的
//$(".mark-project-list").append(function(){
//    var array = $();
//    $.each(DataBase.AttProject,function(k,v){
//        var item = GenerateList(v,"#project-tpl");
//        array = array.add(item);
//    });
//    return array;
//});


//  事件绑定
$(".project-container").on("click",".project-card",function(e){

    //进入项目
    if($(e.target).is("canvas"))
    {
        window.location = $(this).data("src");
    }
}).on("click",".delete-btn",function(e){

    //删除项目
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
}).on("row.delete",function(){

    //删除成功
    var container = $(this);
    setTimeout(function(){
        container.trigger("refresh.amount");
    },100);
}).on("refresh.amount",function(){

    //刷新项目数量
    var amount = $(this).find(".project-card").length;
    $(this).prev(".group-title").find(".amount").text(amount);
    if(amount ==0){
        $(this).find(".no-data").show();
    }
    else{
        $(this).find(".no-data").hide();
    }
}).on("refresh.data",function(e){

    //刷新数据数据
    var canvas = $(e.target).find("canvas");
    DrawCanvas(canvas);
    //扩展tip提示
    $(".demoDown").tip_Title();
}).trigger("refresh");

//  项目新建成功
$("#new-project").on("request.success",function(e,json){
    //添加项目
    var item = GenerateList(json.Data,"#project-tpl");
    item.appendTo(".my-project-list").trigger("refresh");
    $("#project-member").data("from",item);
    //扩展tip提示
    $(".demoDown").tip_Title();
    setTimeout(function(){
        var htmlVal="";
        var follwers=json.Data.Follwers;//项目关注人
        var projectManagerId=json.Data.ProjectManagerId;//项目经理
        var userIdList=new Array();
        if(projectManagerId.length>0){
            for(var i= 0,len=projectManagerId.length;i<len;i++){
                htmlVal+='<span class="avatar" data-id="'+projectManagerId[i].UserId+'"><img src="'+projectManagerId[i].Avatar+'" title="'+projectManagerId[i].UserName+'" alt=""><a href="#" class="delete"><span class="icon icon-delete-avatar"></span></a><p style="display: inline-block;font-weight: bold;margin-top: 5px;"><span style="display: inline-block;width: 50px;overflow: hidden;height: 20px;text-transform:capitalize;">'+projectManagerId[i].UserName+'</span></p></span>';

                userIdList.push(projectManagerId[i].UserId);
            }
        };
        if(follwers.length>0){
            for(var i= 0,len=follwers.length;i<len;i++){
                htmlVal+='<span class="avatar" data-id="'+follwers[i].UserId+'"><img src="'+follwers[i].Avatar+'" title="'+follwers[i].UserName+'" alt=""><a href="#" class="delete"><span class="icon icon-delete-avatar"></span></a><p style="display: inline-block;font-weight: bold;margin-top: 5px;"><span style="display: inline-block;width: 50px;overflow: hidden;height: 20px;text-transform:capitalize;">'+follwers[i].UserName+'</span></p></span>';

                userIdList.push(follwers[i].UserId);
            }
        };
        $('#project-member').find("div.user-selector-Member>span").remove();
        $('#project-member').find("div.user-selector-Member").prepend(htmlVal);
        $('#project-member').find("input[name=MemberIds]").val(userIdList.join());
    },200);
});

//项目创建  提交时改变项目时间进度条
$('#new-project button[type=submit]').on('click',function(){
    var objDiv=$('div.project-container'),$obj_div;
    for(var i=0;i<3;i++){
        if(objDiv.eq(i).css('display')=='block'){
            $obj_div=objDiv.eq(i);
        }
    }
    setTimeout(function(){
        var div_new = $obj_div.find('div.item:last-child');
        var startTime = div_new.find('span.start-time').attr('timeval');
        var endTime = div_new.find('span.end-time').attr('timeval');
        var dq_timq = moment().unix();
        var bfb_num=((dq_timq-startTime)/(endTime-startTime)).toFixed(2)*100;
        div_new.find('div.progress>div.bar').css("width",bfb_num);
    },200);
});

$('#project-member button[type=submit]').on('click',function(){
    var objDiv=$('div.project-container:not(:hidden)'),$obj_div;
    for(var i=0;i<3;i++){
        if(objDiv.eq(i).css('display')=='block'){
            $obj_div=objDiv.eq(i);
        }
    }
    setTimeout(function(){
        var div_new = objDiv.find('div.item:last-child');
        var startTime = div_new.find('span.start-time').attr('timeval');
        var endTime = div_new.find('span.end-time').attr('timeval');
        var dq_timq = moment().unix();
        var bfb_num=((dq_timq-startTime)/(endTime-startTime)).toFixed(2)*100;
        div_new.find('div.progress>div.bar').css("width",bfb_num+"%");

        //扩展tip提示
        $(".demoDown").tip_Title();
    },200);
});
</script>

<script type="text/javascript">
    var DragHandler = function(){
        var self ={};
        var container = $(".my-project-list");
        var selector = ".project-card";
        var target;

        container.on("dragstart",selector,function(e){
            //console.log(e);
            target = $(this);
        });
        container.on("dragover",selector,function(e){
            //console.info(e.originalEvent.offsetY);
            e.preventDefault();
        });
        container.on("drop",selector,function(e){
            //console.log(e);
            var dragItem = target;
            var dropItem = $(e.currentTarget);
            if(dragItem.is(dropItem)){
                return;
            }
            if(dropItem.is("[draggable]")){
                //两项目合并
                var dialog = $("#project-merge-dialog");
                var ids = function(){
                    var id1 = dragItem.getRowData("Id");
                    var id2 = dropItem.getRowData("Id");
                    return id1 + "," + id2;
                }
                dialog.find('[name="ChildIds"]').val(ids);
                dialog.open().find('[name="Title"]').focus();
            }
            else{
                //添加到目标项目
                $.post("/backend/project/consolidated_project",{
                    ProjectOne: dragItem.getRowData("Id"),
                    ProjectTwo: dropItem.getRowData("Id")
                }).done(function(json){
                    if(json.Result ==0){
                        alert("合并成功！");
                        $.reload(1);
                    }
                })
            }
        });

        return self;
    }();
</script>

<script type="text/javascript">

//根据项目取成员
$("#edit-project").on("data.beforeload",function(e,data){
    var workPanel = $(this);
    GetMemberList(data.Id).done(function(json){
        workPanel.data("member",json.Data);
    });
});

//选择项目成员，默认只有自己
$("#project-member").on("open reseting",function(){
    $(this).find(".user-selector-Member").show();
    var data = [{
		Avatar: DataBase.UserInfo.Avatar,
		UserId: DataBase.UserInfo.Id,
		UserName: DataBase.UserInfo.Name
	}];
	$(this).find("[name=MemberIds]").setValue(data);
});

//项目成员选择完成后关闭
$("#project-member").on("request.success",function(e,data){
    if(data.Result==0){
        alert(data.Msg);
        $(this).close();
    }
});
</script>

{if $TestMode}
<script type="text/javascript">
    {*console.info("MyProject",{$MyProject|@json_encode});*}
    {*console.log("AttProject",{$AttProject|@json_encode});*}
    {*console.log("Participant",{$Participant|@json_encode});*}
</script>
{/if}
{/block}
