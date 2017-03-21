/**
 * Created by Administrator on 2016/7/7.
 */

//第一次进入时判断是否缓存了效果图，如果没有则加载，否则读取缓存
$(function(){
    if(window.sessionStorage["skinsData"]){
        var data=JSON.parse(window.sessionStorage["skinsData"]);
        if(data.Result==0){
            skinCallBack(data);
        }else{
            alert(data.Msg);
        }
    }else{
        loadSkin();
    }
});

//换肤缩略图
function loadSkin(){
    $.ajax({
        type:"GET",
        url:"/backend/theme/get_themes",
        data:'',
        cache:false,
        success:function(data){
            data = JSON.parse(data);
            if(data.Result==0){
                window.sessionStorage["skinsData"]=JSON.stringify(data);
                skinCallBack(data);
            }else{
                alert(data.Msg);
            }
        },error:function(){
            alert('服务器异常!');
        }
    });
}

function skinCallBack(dt) {
    var json = dt, i = 0, htmlVal = '', dataCount = 0;
    for (dataCount = json.Data.length; i < dataCount; i++) {
        if ($('#themeId').val() == json.Data[i].Id) {
            htmlVal += " <div class='divClickImg' themeID='" + json.Data[i].Id + "' headerBg='" + json.Data[i].Color + "' bgSrc='" + json.Data[i].BgImg + "' onclick='click_btn(this);'><img alt='skin" + i + "' src='" + json.Data[i].Thumb + "'/></div>";
        } else {
            htmlVal += " <div class='divImg' themeID='" + json.Data[i].Id + "' headerBg='" + json.Data[i].Color + "' bgSrc='" + json.Data[i].BgImg + "' onclick='click_btn(this);'><img alt='skin" + i + "' src='" + json.Data[i].Thumb + "'/></div>";
        }
    }
    $('#skinBox').empty().append(htmlVal);
}

//点击缩略图选择喜欢的皮肤
function click_btn(obj){
    $(obj).attr('class','divClickImg').siblings().attr('class','divImg');
    $('#viewport>.background').css({"background":"url("+$(obj).attr('bgSrc')+") no-repeat center center"});
    $('#layout>.header').css({"background":""+$(obj).attr('headerBg')+""});
    //$("#replaceSkin .btn-blue").css({"background":""+$(obj).attr('headerBg')+""});
}

//提交设置的皮肤到数据库
$('#replaceSkin button[type=button]').on('click',function(){
    $('#skinBox>div').each(function(){
        if($(this).attr('class')=='divClickImg'){
            setSkin($(this).attr('themeID'));
        }
    });
});

//提交到数据库
function setSkin(themeID){
    $.ajax({
        type:"POST",
        url:"/backend/user/set_theme",
        data:{ThemeId:themeID},
        success:function(data){
            data=JSON.parse(data);
            if(data.Result==0){
                alert(data.Msg);
                $('#replaceSkin').close();
            }else{
                alert(data.Msg);
            }
        },error:function(){
            alert("服务器异常!");
        }
    });
}
var showNum=0;
//新建任务编辑任务
//<span class='icon icon-zre-user'></span>
//<span class='icon icon-gzr-user'></span>
//<span class='icon icon-jcx'></span>
//<span class='icon icon-xm'></span>
$("#new-task,#edit-task,#view-task,#edit-task-calendar").on("open", function () {
    var a_list = "<div id='menu_li'><a href='#' type_btn='zrr' class='a_li demo demoDown' tip_Title='这条任务将出现在被指派人的待办列表里'><i class='iconfont icon-ren' style='font-size:16px;'></i><span class='span_txt'>责任人</span></a><a href='#' type_btn='gzr' class='a_li demo demoDown' tip_Title='这条任务将出现在关注人的关注列表里'><i class='iconfont icon-guanzhuren01' style='font-size:16px;'></i><span class='span_txt'>关注人</span></a><a href='#' type_btn='jcx' class='a_li demo demoDown' tip_Title='给这条任务添加一个子任务'><i class='iconfont icon-zirenwu' style='font-size: 16px;'></i><span class='span_txt'>子任务</span></a><a href='#' type_btn='xm' class='a_li demo demoDown' tip_Title='添加一个项目'><i class='iconfont icon-xiangmu' style='font-size:16px;'></i><span class='span_txt'>项目</span></a><a class='move_btn demo demoDown' onclick='move_btn(this)' href='#' tip_Title='更多操作'><span class='span_txt'>更多</span><i class='iconfont icon-xiala' style='font-size:16px;margin-top:2px;'></i></a></div>";

    var popoverHtml="<div class='popover_move' id='project-popover'><div class='list narrow projectlist'><ul id='my-project-ul'>" +"<li><label class='is-milestone' style='margin-left: 0px;font-size:12px!important; color: #505050;'><span class='checkbox freestyle' style='background: #fff;'><span class='icon icon-milestone-view orange' style='top:-8px;'></span><input type='checkbox' value='1'></span><span class='text'>标记为里程碑</span></label></li><li><a href='#' class='delete-btn hover-item' style='color: #505050;font-size: 12px!important;'><i class='iconfont icon-shanchu' style='font-size: 16px;'></i><span style='margin-left: 3px;'>删除</span></a></li></ul></div></div>";
    if(showNum==0){
        //添加元素
        $('#new-task div.title span').eq(0).after(a_list);
        $('#edit-task div.title span').eq(0).after(a_list);
        $('#view-task div.title span').eq(0).after(a_list);
        $('#edit-task-calendar div.title span').eq(0).after(a_list);

        $('#new-task div.title').append(popoverHtml);
        $('#edit-task div.title').append(popoverHtml);
        $('#view-task div.title').append(popoverHtml);
        $('#edit-task-calendar div.title').append(popoverHtml);
        //隐藏“标记为里程碑”
        $('#new-task div.title label.is-milestone').eq(0).remove();
        $('#edit-task div.title label.is-milestone').eq(0).remove();
        $('#view-task div.title label.is-milestone').eq(0).remove();
        $('#edit-task-calendar div.title label.is-milestone').eq(0).remove();
        //隐藏修改页面删除按钮
        $('#edit-task div.title a.delete-btn').hide();
        $('#edit-task-calendar div.title a.delete-btn').hide();
        //缩小删除按牛“X”
        $('#new-task div.title a.close-btn').css({'width':'12px','height':'12px','margin-top':'5px'});
        $('#edit-task div.title a.close-btn').css({'width':'12px','height':'12px','margin-top':'5px'});
        $('#view-task div.title a.close-btn').css({'width':'12px','height':'12px','margin-top':'5px'});
        $('#edit-task-calendar div.title a.close-btn').css({'width':'12px','height':'12px','margin-top':'5px'});

        styleUpdate();
        showNum=1;
        //标记为里程碑
        $(this).find(".is-milestone").on("set.value",function(e,data){
            var tag = $(this);
            var data = data.value;
            if(!data){
                return;
            }

            tag.find("input").prop("disabled",false).prop("checked",data.value==1).trigger("set.appearance");

            if(data.value == 1){
                tag.find(".text").text("已标记为里程碑");
            }
            else{
                tag.find(".text").text("标记为里程碑");
            }
        }).on("change",function(){
            var tag = $(this);
            var data = {};
            data.value = tag.find("input").prop("checked") ? 1 : 0;
            var form = tag.parents(".work-panel").find("form");
            form.find("[name='IsMilestone']").val(data.value).trigger("change");
            tag.setValue(data);
        });
        $(".work-panel").on("set.value","[name='IsMilestone']",function(e,data){
            var tag = $(this).parents(".work-panel").find(".is-milestone");
            //data.disabled = $(this).prop("disabled");
            tag.setValue(data);
        });

        //页面打开初始化
        show_none($(this).attr('id'));
        //点击展开
        click_show($(this).attr('id'));
    }
    //标记为里程碑
    //sign_lcb();

    $('div#new-task div.title div.popover_move').hide();
    $('div#edit-task div.title div.popover_move').hide();
    $('div#view-task div.title div.popover_move').hide();
    $('div#edit-task-calendar div.title div.popover_move').hide();
    if($(this).attr('id')!="view-task"){
        var text=$('#'+$(this).attr('id')+' textarea.form-control')[0];
        var shadow_txt=$('#'+$(this).attr('id')+' textarea#shadow_txt')[0];
        textarea_ht(text,shadow_txt);
    }else{
        if($('#view-task  textarea.form-control')[0]!=undefined){
            var text=$('#view-task  textarea.form-control')[0];
            var shadow_txt=$('#view-task  textarea#shadow_txt')[0];
            textarea_ht(text,shadow_txt);
        }
        setTimeout(function(){
            $("#view-task a.delete").hide();
        },200);
    }

    var _this = $(this);

    if(_this.attr("id")!="view-task"){
        setTimeout(function () {
            var workObj = $("#" + _this.attr('id') + "");
            var user_selector = workObj.find("div.user-selector").eq(0);//项目经理
            user_selector.find("a.delete").remove();
            user_selector.css({"margin-top": "-7px"}).find("a.avatar-add").removeAttr("data-toggle").attr("href","#").hide();

            var user_selector2 = workObj.find("div.user-selector").eq(1);//关注人
            user_selector2.find("a.delete").remove();

            //单人指派
            assignManager(_this.attr('id'));
            //多人指派
            moveAssignGzr(_this.attr('id'));
            //绑定下拉列表
            li_userSelect(_this.attr('id'));
        }, 200);
    }
}).on("close",function(){
    var id = $(this).attr('id');
    assignRemoveManager(id);
});

//标记里程碑
function sign_lcb(){
    $(".work-panel .is-milestone").on("set.value",function(e,data){
        var tag = $(this);
        var data = data.value;
        if(!data){
            return;
        }

        tag.find("input").prop("disabled",false).prop("checked",data.value==1).trigger("set.appearance");

        if(data.value == 1){
            tag.find(".text").text("已标记为里程碑");
        }
        else{
            tag.find(".text").text("标记为里程碑");
        }
        console.log(456);
    }).on("change",function(){
        var tag = $(this);
        var data = {};
        data.value = tag.find("input").prop("checked") ? 1 : 0;
        var form = tag.parents(".work-panel").find("form");
        form.find("[name='IsMilestone']").val(data.value).trigger("change");
        tag.setValue(data);
        console.log(123);
    });
    $(".work-panel").on("set.value","[name='IsMilestone']",function(e,data){
        var tag = $(this).parents(".work-panel").find(".is-milestone");
        //data.disabled = $(this).prop("disabled");
        tag.setValue(data);
    });
}

//点击更多
function move_btn(that) {
    var work_id = $(that).parents('div.work-panel').attr('id');
    if (work_id == "new-task") {
        $('#new-task div.title').find('ul#my-project-ul li').eq(1).hide();
        $('#new-task div.title').find('ul#my-project-ul li').eq(0).css({'border-bottom': '0px'});
        if ($('div#new-task div.title div.popover_move').is(":hidden")) {
            $('div#new-task div.title div.popover_move').show();
        } else {
            $('div#new-task div.title div.popover_move').hide();
        }
    } else {
        if (work_id != "view-task") {
            $('#' + work_id + ' div.title').find('ul#my-project-ul li').eq(1).find('a.delete-btn').show();
            $('#' + work_id + ' div.title').find('ul#my-project-ul li').eq(0).css({'border-bottom': '1px solid #f1f1f1'});
            if ($('#' + work_id + ' div.title div.popover_move').is(":hidden")) {
                $('#' + work_id + ' div.title div.popover_move').show();
            } else {
                $('#' + work_id + ' div.title div.popover_move').hide();
            }
        }
    }
}

//点击面板隐藏 popover弹出窗口
$('.work-panel div.content-wrap').on('click', function () {
    $('.work-panel div.title div.popover_move').stop().hide('300');
});

//面板样式修正
function styleUpdate(){
    //{'width':'93.3%','margin-left':'33px','min-height':'45px'}
    $('#new-task div.content-wrap form dl').eq(0).find('dt').css({'display':'block'}).next('dd').find('input[type=text]').css({'width':'96.6%','margin-left':'16px','font-size':'18px','color':'#000'});
    $('#new-task div.content-wrap form dl').eq(1).find('dt').css({'display':'none'}).next('dd').find('textarea').css({'width':'93.3%','margin-left':'33px','resize':'none','overflow':'hidden','padding-bottom':'10px'});
    $('#new-task div.content-wrap form div.time-picker-group').css({'width':'100%','display':'flex','position':'relative','margin-bottom':'10px'}).find('dl').eq(0).css({'width':'58%'}).find('dt').text('任务时间').css({'color':'#999','font-size':'12px','padding-top':'10px'}).next('dd').find('input:first').css({'width':'175px'});
    $('#new-task div.content-wrap form div.time-picker-group').find('dl').eq(0).css({'margin-bottom':'6px'});
    $('#new-task div.content-wrap form div.time-picker-group').find('dl').eq(1).css({'width':'50%','margin-left':'-28px','margin-bottom':'6px'}).find('dt').css({'min-width':'10px'}).text('').before("<span style='display:inline-block;line-height: 34px;margin-left:-10px;'>-</span>").next('dd').find('input:first').css({'width':'175px'});
    $('#new-task div.content-wrap form button[type=submit]').html("新建");
    //$('#edit-task div.content-wrap form dl').eq(0).find('dt').css({'display':'none'}).next('dd').find('input[type=text]').css({'width':'93.3%','margin-left':'33px','font-size':'18px','color':'#000'});
    $('#edit-task div.content-wrap form dl').eq(1).find('dt').css({'display':'none'}).next('dd').find('textarea').css({'width':'94.5%','margin-left':'33px','resize':'none','overflow':'hidden','padding-bottom':'10px'});
    $('#edit-task div.content-wrap form div.time-picker-group').css({'width':'100%','display':'flex','position':'relative','margin-bottom':'10px'}).find('dl').eq(0).css({'width':'58%'}).find('dt').text('任务时间').css({'color':'#999','font-size':'12px','padding-top':'10px'}).next('dd').find('input:first').css({'width':'175px'});
    $('#edit-task div.content-wrap form div.time-picker-group').find('dl').eq(0).css({'margin-bottom':'6px'});
    $('#edit-task div.content-wrap form div.time-picker-group').find('dl').eq(1).css({'width':'50%','margin-left':'-28px','margin-bottom':'6px'}).find('dt').css({'min-width':'10px'}).text('').before("<span style='display:inline-block;line-height: 34px;margin-left:-10px;'>-</span>").next('dd').find('input:first').css({'width':'175px'});

    //$('#view-task div.content-wrap form dl').eq(0).find('dt').css({'display':'none'}).next('dd').find('input[type=text]').css({'width':'93.3%','margin-left':'33px','font-size':'18px','color':'#000'});
    $('#view-task div.content-wrap form dl').eq(1).find('dt').css({'display':'none'}).next('dd').find('span').css({'width':'94.3%','margin-left':'33px','min-height':'70px','overflow':'hidden'});
    $('#view-task div.content-wrap form div.time-picker-group').css({'width':'100%','display':'flex','position':'relative','margin-bottom':'10px'}).find('dl').eq(0).css({'width':'58%'}).find('dt').text('任务时间').css({'color':'#999','font-size':'12px','padding-top':'10px'}).next('dd').find('span:first').css({'width':'175px'});
    $('#view-task div.content-wrap form div.time-picker-group').find('dl').eq(1).css({'width':'50%','margin-left':'-28px'}).find('dt').css({'min-width':'10px'}).text('').before("<span style='display:inline-block;line-height: 34px;margin-left:-10px;'>-</span>").next('dd').find('span:first').css({'width':'175px'});

    //$('#edit-task-calendar div.content-wrap form dl').eq(0).find('dt').css({'display':'none'}).next('dd').find('input[type=text]').css({'width':'93.3%','margin-left':'36px','font-size':'18px','color':'#000'});
    $('#edit-task-calendar div.content-wrap form dl').eq(1).find('dt').css({'display':'none'}).next('dd').find('textarea').css({'width':'94.3%','margin-left':'37px','min-height':'45px','resize':'none','overflow':'hidden','padding-bottom':'20px'});
    $('#edit-task-calendar div.content-wrap form div.time-picker-group').css({'width':'100%','display':'flex','position':'relative','margin-bottom':'10px'}).find('dl').eq(0).css({'width':'58%'}).find('dt').text('任务时间').css({'color':'#999','font-size':'12px','padding-top':'10px'}).next('dd').find('input:first').css({'width':'175px'});
    $('#edit-task-calendar div.content-wrap form div.time-picker-group').find('dl').eq(1).css({'width':'50%','margin-left':'-28px'}).find('dt').css({'min-width':'10px'}).text('').before("<span style='display:inline-block;line-height: 34px;margin-left:-10px;'>-</span>").next('dd').find('input:first').css({'width':'175px'});
}

//任务描述 文本框自适应高度
function textarea_ht(txt,shaow) {
    var text = txt; //用户看到的文本框$('textarea.form-control')[0];
    var shadow = shaow; //隐藏的文本框$('textarea#shadow_txt')[0];
    text.oninput = text.onpropertychange = onchange;
    function onchange() {
        shadow.value = text.value;
        setHeight();
        setTimeout(setHeight, 0);
        //针对IE 6/7/8的延迟, 否则有时会有一个字符的出入
        function setHeight() { text.style.height = shadow.scrollHeight + "px";}
    }
};

//判断是否显示
function show_none(that){
    var dl_List = $('#' + that + ' form>dl');
    //责任人,关注人,检查项,所属项目（块）
    var dl_zrr = dl_List.eq(2),dl_gzr = dl_List.eq(3),dl_jcx = dl_List.eq(5),dl_ssxm = dl_List.eq(6);
    switch (that){
        case 'new-task':
            $('#'+that+' div.title div#menu_li>a[type_btn]').show();
            dl_zrr.hide();
            dl_gzr.hide();
            dl_jcx.hide();
            dl_ssxm.hide();
            break;
        default:
            $('#'+that+' div.title div#menu_li>a[type_btn]').hide();
            $('#'+that+'').on('set.value',function(){
                //数据
                var dl_zrrDt = dl_zrr.find('input[name=AssignedTo]').val();
                var dl_gzrDt = dl_gzr.find('input[name=Follwers]').val();
                var dl_jcxDt = '';
                if (dl_jcx.find('dl[class=item]').length != 0) {
                    dl_jcxDt = dl_jcx.find('dl[class=item]').eq(0).data().data;
                } else {
                    dl_jcxDt = undefined;
                }
                var dl_ssxmDt ='';
                if(dl_ssxm.find('select[name=ProjectId] option:selected').length!=0){
                    dl_ssxmDt = dl_ssxm.find('select[name=ProjectId] option:selected').text();
                }else{
                    dl_ssxmDt = dl_ssxm.find('dd span:first').text();
                }
                //btn
                var menu_List = $('#' + that + ' div.title div#menu_li>a[type_btn]');
                var btn_zrr = menu_List.eq(0);
                var btn_gzr = menu_List.eq(1);
                var btn_jcx = menu_List.eq(2);
                var btn_xm = menu_List.eq(3);
                if(dl_zrrDt==0){
                    dl_zrr.hide();btn_zrr.show();
                }else{
                    dl_zrr.show();btn_zrr.hide();
                }
                if(dl_gzrDt==0){
                    dl_gzr.hide();btn_gzr.show();
                }else{
                    dl_gzr.show();btn_gzr.hide();
                }
                if(dl_jcxDt==undefined){
                    dl_jcx.hide();btn_jcx.show();
                }else{
                    dl_jcx.show();btn_jcx.hide();
                }
                if(dl_ssxmDt=="无"){
                    dl_ssxm.hide();btn_xm.show();
                }else{
                    dl_ssxm.show();btn_xm.hide();
                }
            });
            break;
    }
}

//点击显示
function click_show(that){
    if (that != "view-task") {
        var dl_List = $('#' + that + ' form>dl');
        //责任人
        var dl_zrr = dl_List.eq(2);
        //关注人
        var dl_gzr = dl_List.eq(3);
        //检查项
        var dl_jcx = dl_List.eq(5);
        //所属项目
        var dl_ssxm = dl_List.eq(6);
        $('#'+that+' div.title div#menu_li>a[type_btn]').on('click',function(){
            var a_obj=$(this);
            switch($(this).attr('type_btn')){
                case 'zrr':
                    dl_zrr.stop().show(function(){
                        a_obj.stop().hide();
                    });
                    break;
                case 'gzr':
                    dl_gzr.stop().show(function(){
                        a_obj.stop().hide();
                    });
                    break;
                case 'jcx':
                    dl_jcx.stop().show(function(){
                        dl_jcx.find("input[name=Checklist]").focus();
                        a_obj.stop().hide();
                    });
                    break;
                case 'xm':
                    dl_ssxm.stop().show(function(){
                        a_obj.stop().hide();
                    });
                    break;
                default:
                    break;
            }
        });
    }
}

var oneNumber=0,twoNumber=0,threeNumber=0;
//项目新建面板监听
$('#new-project').on('open',function(){
    var id = $(this).attr('id');
    var dl_list = $('#' + id + ' form>dl');
    if (oneNumber == 0) {
        //去掉项目描述
        dl_list.eq(0).find('dt').css({'display':'none'}).next('dd').find('input[name=Title]').css({'width':'93.3%','margin-left': '33px','font-size': '18px','color': '#000'});
        dl_list.eq(1).find('dt').css({'display':'none'}).next('dd').find('textarea').css({'width':'93.3%','margin-left':'33px','min-height': '70px', 'resize':'none','overflow':'hidden'});
        $('#' + id + ' div.content-wrap form div.time-picker-group').css({'width': '100%','display': 'flex','position': 'relative','margin-bottom':'10px'}).find('dl').eq(0).css({'width': '60%'}).find('dt').text('项目周期').next('dd').find('input:first').css({'width': '188px'});
        $('#' + id + ' div.content-wrap form div.time-picker-group').find('dl').eq(1).css({'width': '46%','margin-left': '-28px'}).find('dt').css({'min-width': '10px'}).text('').before("<span style='display:inline-block;line-height: 34px;margin-left:-8px;'>-</span>").next('dd').find('input:first').css({'width': '188px'});
        $(this).find("button[type=submit]").text("新建");
    }
    oneNumber = 1;
    var text=$('#'+$(this).attr('id')+' textarea.form-control')[0];
    var shadow_txt=$('#'+$(this).attr('id')+' textarea#shadow_txt')[0];
    textarea_ht(text, shadow_txt);

    setTimeout(function(){
        var workObj = $("#" + id + "");
        var user_selector = workObj.find("div.user-selector").eq(0);//项目经理
        user_selector.find("a.delete").remove();
        user_selector.css({"margin-top": "-7px"}).find("a.avatar-add").removeAttr("data-toggle").attr("href","#").hide();

        var user_selector2 = workObj.find("div.user-selector").eq(1);//关注人
        user_selector2.find("a.delete").remove();
    },200);

    assignManager(id);
    moveAssignGzr(id);
    //绑定下拉列表
    li_userSelect(id);
}).on("close",function(){
    var id = $(this).attr('id');
    assignRemoveManager(id);
});

//项目编辑面板监听
$('#edit-project').on('open',function(){
    var id = $(this).attr('id');
    var dl_list = $('#' + id + ' form>dl');
    if (twoNumber == 0) {
        //去掉项目描述
        dl_list.eq(0).find('dt').css({'display':'none'}).next('dd').find('input[name=Title]').css({'width':'93.3%','margin-left': '33px','font-size': '18px','color': '#000'});
        dl_list.eq(1).find('dt').css({'display':'none'}).next('dd').find('textarea').css({'width':'93.3%','margin-left':'33px','min-height': '70px', 'resize':'none','overflow':'hidden'});
        $('#' + id + ' div.content-wrap form div.time-picker-group').css({'width': '100%','display': 'flex','position': 'relative','margin-bottom':'10px'}).find('dl').eq(0).css({'width': '60%'}).find('dt').text('项目周期').next('dd').find('input:first').css({'width': '188px'});
        $('#' + id + ' div.content-wrap form div.time-picker-group').find('dl').eq(1).css({'width': '46%','margin-left': '-28px'}).find('dt').css({'min-width': '10px'}).text('').before("<span style='display:inline-block;line-height: 34px;margin-left:-8px;'>-</span>").next('dd').find('input:first').css({'width': '188px'});
    }
    twoNumber = 1;
    var text=$('#'+$(this).attr('id')+' textarea.form-control')[0];
    var shadow_txt=$('#'+$(this).attr('id')+' textarea#shadow_txt')[0];
    textarea_ht(text, shadow_txt);

    setTimeout(function(){
        var workObj = $("#" + id + "");
        var user_selector = workObj.find("div.user-selector").eq(0);//项目经理
        user_selector.find("a.delete").remove();
        user_selector.css({"margin-top": "-7px"}).find("a.avatar-add").removeAttr("data-toggle").attr("href","#").hide();

        var user_selector2 = workObj.find("div.user-selector").eq(1);//关注人
        user_selector2.find("a.delete").remove();

        assignManager(id);
        moveAssignGzr(id);
        //绑定下拉列表
        li_userSelect(id);
    },200);
}).on("close",function(){
    var id = $(this).attr('id');
    assignRemoveManager(id);
});

$('#edit-main-project').on('open',function(){
    var id = $(this).attr('id');
    var dl_list = $('#' + id + ' form>dl');
    if (threeNumber == 0) {
        //去掉项目描述
        dl_list.eq(0).find('dt').css({'display':'none'}).next('dd').find('input[name=Title]').css({'width':'93.3%','margin-left': '33px','font-size': '18px','color': '#000'});
        dl_list.eq(1).find('dt').css({'display':'none'}).next('dd').find('textarea').css({'width':'93.3%','margin-left':'33px','min-height': '70px', 'resize':'none','overflow':'hidden'});
        $('#' + id + ' div.content-wrap form div.time-picker-group').css({'width': '100%','display': 'flex','position': 'relative','margin-bottom':'10px'}).find('dl').eq(0).css({'width': '60%'}).find('dt').text('项目周期').next('dd').find('input:first').css({'width': '188px'});
        $('#' + id + ' div.content-wrap form div.time-picker-group').find('dl').eq(1).css({'width': '46%','margin-left': '-28px'}).find('dt').css({'min-width': '10px'}).text('').before("<span style='display:inline-block;line-height: 34px;margin-left:-8px;'>-</span>").next('dd').find('input:first').css({'width': '188px'});
    }
    threeNumber = 1;
    var text=$('#'+$(this).attr('id')+' textarea.form-control')[0];
    var shadow_txt=$('#'+$(this).attr('id')+' textarea#shadow_txt')[0];
    textarea_ht(text, shadow_txt);

    setTimeout(function(){
        var workObj = $("#" + id + "");
        var user_selector = workObj.find("div.user-selector").eq(0);//项目经理
        user_selector.find("a.delete").remove();
        user_selector.css({"margin-top": "-7px"}).find("a.avatar-add").removeAttr("data-toggle").attr("href","#").hide();

        var user_selector2 = workObj.find("div.user-selector").eq(1);//关注人
        user_selector2.find("a.delete").remove();
    },200);
});

//$("#project-member").on("open",function(){
//    setTimeout(function(){
//        assignManager(id);
//        moveAssignGzr(id);
//        //绑定下拉列表
//        li_userSelect(id);
//    },200);
//}).on("close",function(){
//
//});


//监听项目编辑页面标题文本框是否含有特殊字符，并进行转码操作
//$("#edit-task").on("set.value", "input[name=Title]", function () {
//	var obj_input = $('#edit-task form>dl').eq(0).find('dd input[name=Title]'), title_val = obj_input.val();
//	if (title_val.indexOf('&') > 0) {
//		obj_input.val(title_val.replace(/&gt;/g, '>').replace(/&lt;/g, '<').replace(/&amp;/g, '&'));
//	}
//});
//$("#edit-task").on("set.value", "textarea[name=Description]", function () {
//	var obj_input = $('#edit-task form>dl').eq(1).find('dd textarea[name=Description]'), title_val = obj_input.val();
//	if (title_val.indexOf('&') > 0) {
//		obj_input.val(title_val.replace(/&gt;/g, '>').replace(/&lt;/g, '<').replace(/&amp;/g, '&'));
//	}
//});
//$("#edit-task form>dl").find('input[name=Checklist]').on("set.value", function () {
//	var obj_input = $('#edit-task form>dl').find('input[name=Checklist]'), len = obj_input.length, i = 0, title_val = "";
//	for (; i < len; i++) {
//		title_val = obj_input.eq(i).val();
//		if (title_val != "") {
//			obj_input.eq(i).val(title_val.replace(/&gt;/g, '>').replace(/&lt;/g, '<').replace(/&amp;/g, '&'));
//		}
//		console.log(i + ":" + title_val);
//	}
//});
$("#edit-task").on("open",function(){
    $('#edit-task form textarea[name=Description]').css({'height':'80px'});
    $(this).find("#menu_li>a").mouseover(function(){
        $(this).css({"color":"#2BA1D8"}).find("span").css({"color":"#2BA1D8"});
    }).mouseout(function(){
        $(this).css({"color":"#999"}).find("span").css({"color":"#999"});
    });
});

$("#edit-task").on("set.value","textarea[name=Description]",function(){
    if($('#edit-task form textarea[name=Description]')[0].scrollHeight>80) {
        $('#edit-task form textarea[name=Description]').height($('#edit-task form textarea[name=Description]')[0].scrollHeight - 10);
    }
});

$("#edit-task-calendar").on("open",function(){
    $('#edit-task-calendar form textarea[name=Description]').css({'height':'80px'});
});

$("#edit-task-calendar").on("set.value", "textarea[name=Description]", function () {
    if ($('#edit-task-calendar form textarea[name=Description]')[0].scrollHeight > 80) {
        $('#edit-task-calendar form textarea[name=Description]').height($('#edit-task-calendar form textarea[name=Description]')[0].scrollHeight - 10);
    }
});

$('#new-task').on("open", function () {
    setTimeout(function () {
        $("#new-task").find("input[name=Title]").focus();
    }, 200);

    $(this).find("#menu_li>a").mouseover(function(){
        $(this).css({"color":"#2BA1D8"}).find("span").css({"color":"#2BA1D8"});
    }).mouseout(function(){
        $(this).css({"color":"#999"}).find("span").css({"color":"#999"});
    });
});

$('#dbl-new-task').on("open", function () {
    setTimeout(function () {
        $("#dbl-new-task").find("input[name=Title]").focus();
    }, 200);
});

$('#new-project').on("open", function () {
    setTimeout(function () {
        $("#new-project").find("input[name=Title]").focus();
    }, 200);
});

$("div#edit-task input[name=Title]").on('click',function(){
    if($('div#edit-task span.shen_lufu').css('display')=="block"){
        $('div#edit-task span.shen_lufu').hide();
    }
});

$("div#view-task input[name=Title]").on('click',function(){
    if($('div#view-task span.shen_lufu').css('display')=="block"){
        $('div#view-task span.shen_lufu').hide();
    }
});

$("div#edit-task-calendar input[name=Title]").on('click',function(){
    if($('div#edit-task-calendar span.shen_lufu').css('display')=="block"){
        $('div#edit-task-calendar span.shen_lufu').hide();
    }
});

$("#edit-task").on("set.value","input[name=CompleteProgress]",function(e,data){
    if(data.value==100){
        $('#edit-task form>dl:first-child dt').find('label').addClass('checked');
    }else{
        $('#edit-task form>dl:first-child dt').find('label').removeClass('checked');
    }
});

//关闭任务修改面板的时候，关闭popover选择框
$("div.work-panel").find("a.close-btn").on("click",function(){
    $("div.popover").close();
});

//$("#new-task").on("click","input[name=Progress]",function(){
//	if($(this).parents("label").attr('checked')){
//		$(this).parents("label").removeClass('checked').next("input[name=CompleteProgress]").val(0);
//	}else{
//		$(this).parents("label").addClass('checked').next("input[name=CompleteProgress]").val(100);
//	}
//});

////侧边栏显示动画效果（清除动画积累）
//$("div.work-panel").on("open",function(){
//    $(this).stop().animate({width:'576px'},300,'',function(){
//        $(this).removeAttr('style');
//    });
//});

//单人指派流程-(项目新增，项目编辑)
function assignManager(workPanelId) {
    var workObj = $("#" + workPanelId + "");
    var userList = new Object();
    workObj.find("div.user-selector").show();
    var user_selector = workObj.find("div.user-selector").eq(0);//项目经理

    //移除多个责任人保留一个
    if (user_selector.find("span.avatar").length > 1) {
        var span_List = user_selector.find("span.avatar");
        for (var i = 0, len = span_List.length; i < len; i++) {
            if (i > 0) {
                user_selector.find("span.avatar").eq(i).remove();
            }
        }
    }

    user_selector.css({"margin-top": "-7px"}).find("a.avatar-add").removeAttr("data-toggle").attr("href","#").hide();
    user_selector.find("#label_name").remove();
    if (user_selector.find("img").length > 0) {
        user_selector.append("<label id='label_name'>" +  user_selector.find('img').attr('tip_title') + "</label>");
    } else {
        user_selector.find("a.avatar-add").attr("tip_Title","点击添加").addClass("demo demoDown").show();
    }
    user_selector.show().find("a.delete").remove();
    user_selector.find("span.avatar").append("<div class='click_div demo demoDown' tip_Title='点击更换' style='position:absolute;top:0px;left:0px;width:140px;height:40px;cursor:pointer;'></div>");
    user_selector.parent().find("#jl_divBox").remove();
    user_selector.parent().append("<div id='jl_divBox' style='display:none;'><div class='input_div'><input id='jl_input' type='text'></div></div>");
    $("#jl_divBox").append("<div class='dialog_dr'></div><div id='List_panel'></div><div id='form_box'><dl class='dl_in'><a class='icon icon-close close-btn'></a></dl><dl class='dl_in'><input type='text' placeholder='昵称' class='userName'></dl><dl class='dl_in'><input type='text' placeholder='手机号或者邮箱' class='emailMobile'></dl><dl class='dl_in'><input type='button' value='邀请'></dl></div>");
    //<span class='qx_btn'>取消</span>
    if(userList.UserId==undefined){
        userList.UserId = user_selector.find("span.avatar").attr("data-id");
        userList.Avatar = user_selector.find("img").attr("src");
        userList.UserName = user_selector.find("img").attr("tip_title");
        userList.UserEmail = $("#userSetting ul li").eq(2).find('p').text().split("：")[1];
    }

    //扩展tip提示
    $(".demoDown").tip_Title();

    $(".click_div").mouseover(function(){
        user_selector.find("#label_name").css({"color":"#2BA1D8"});
    }).mouseout(function(){
        user_selector.find("#label_name").css({"color":"#666"});
    });
    //var projectId = 0,typeAPI = "all";
    //if (workPanelId == "new-task" || workPanelId == "edit-task" || workPanelId == "edit-task-calendar") {
    //    projectId = $("#" + workPanelId + "").find("select[name=ProjectId] option:selected").val();
    //    if (projectId != 0) {
    //        typeAPI = "one";
    //    } else {
    //        typeAPI = "all";
    //    }
    //}else{
    //    projectId = workObj.find("input[name=Id]").length > 0 ? workObj.find("input[name=Id]").val() : 0;
    //}
    ////生成选取项目经理列表
    //get_user(projectId,typeAPI,$("#List_panel"));

    //取消邀请 span.qx_btn,
    $(".dialog_dr").on("click",function(){
        workObj.find("#jl_divBox,#form_box").hide();
        workObj.find("div.user-selector").show();
        //关闭并清理文本框
        $("#jl_divBox").find("input#jl_input").val("");
        $("#jl_divBox").find("input.userName").val("");
        $("#jl_divBox").find("input.emailMobile").val("");
    });

    //关闭邀请面板
    $("#form_box .close-btn").on("click",function(){
        workObj.find("#jl_divBox,#form_box").hide();
        workObj.find("div.user-selector").show();
        //关闭并清理文本框
        $("#jl_divBox").find("input#jl_input").val("");
        $("#jl_divBox").find("input.userName").val("");
        $("#jl_divBox").find("input.emailMobile").val("");
    });

    //搜索效果
    $("#jl_divBox input[type=text]").on("keyup",function(){
        var keyVal=$(this).val().toLowerCase();//转小写
        $("#List_panel dl.hui .p2 span").text(keyVal);
        if($("#List_panel .div_list").length>0){
            var len=$("#List_panel .div_list").length;
            var p_names=$("#List_panel .div_list>.p_name");
            var p_emails=$("#List_panel .div_list>.p_email");
            var p_mobiles=$("#List_panel .div_list>.p_mobile");
            for (var i = 0; i < len; i++) {
                var p_n = p_names.eq(i).text().toLowerCase(),
                    p_e = p_emails.eq(i).text().toLowerCase(),
                    p_m = p_mobiles.eq(i).text().toLowerCase();
                if (p_n.indexOf(keyVal) < 0 && p_e.indexOf(keyVal) < 0 && p_m.indexOf(keyVal) < 0) {
                    p_names.eq(i).parents("dl.dl_li").hide();
                } else {
                    p_names.eq(i).parents("dl.dl_li").show();
                }
            }
        }
    });

    //点击邀请
    $("#form_box input[type=button]").on("click",function(){
        var userName = $(this).parents("#form_box").find("input.userName").val();
        var emailMobile = $(this).parents("#form_box").find("input.emailMobile").val();
        var createId = workObj.find("input[name=CreatorId]").val();
        var proId = 0, taskId = 0,urlApi="",parameterObj = new Object();//项目ID（新建任务或新建项目）
        var emailReg = /^([a-zA-Z0-9_-])+@([a-zA-Z0-9_-])+((\.[a-zA-Z0-9_-]{2,3}){1,2})$/;
        var telReg = /^(((13[0-9]{1})|(14[0-9]{1})|(15[0-9]{1})|(17[0-9]{1})|(18[0-9]{1}))+\d{8})$/;
        if (workPanelId == "new-project" || workPanelId == "edit-project" || workPanelId == "edit-main-project") {
            urlApi="/backend/user/invite_to_project";
            proId = $("#" + workPanelId + " input[name=Id]").val() == "" ? 0 : $("#" + workPanelId + " input[name=Id]").val();
            parameterObj = new Object();
            parameterObj.UserName = userName;
            parameterObj.Contact = emailMobile;
            parameterObj.ProjectId = proId;
            parameterObj.Role = 1;
        }
        if (workPanelId == "new-task" || workPanelId == "edit-task" || workPanelId == "edit-task-calendar") {
            urlApi="/backend/user/invite_to_task";
            proId = $("#" + workPanelId + "").find("select[name=ProjectId] option:selected").val();
            taskId = $("#" + workPanelId + " input[name=Id]").val() == "" ? 0 : $("#" + workPanelId + " input[name=Id]").val();
            parameterObj = new Object();
            parameterObj.UserName = userName;
            parameterObj.Contact = emailMobile;
            parameterObj.ProjectId = proId;
            parameterObj.TaskId = taskId;
            parameterObj.Role = 1;
        }
        if(userName!=""){
            if(emailMobile!=""){
                if(emailReg.test(emailMobile)){
                    $.get(urlApi,parameterObj).done(function(json){
                        if(json.Result==0){
                            alert(json.Msg);
                            user_selector.find("span.avatar").attr("data-id", json.Data.UserId);
                            user_selector.find("img").attr("src",json.Data.Avatar).attr("title",json.Data.UserName);
                            user_selector.find("input[name=ProjectManagerId]").val(json.Data.UserId);
                            user_selector.find("input[name=AssignedTo]").val(json.Data.UserId);
                            user_selector.find("#label_name").text(json.Data.UserName);

                            $("#jl_divBox").hide();
                            $("#form_box").hide();
                            workObj.find("div.user-selector").show();
                            //if (workPanelId != "new-task" && workPanelId != "new-project") {
                            //    var Id = $("#" + workPanelId + " input[name=Id]").val();
                            //    var url="";
                            //    if (workPanelId == "edit-project") {
                            //        url = "/backend/project/add_rlt_user";
                            //    } else {
                            //        url = "/backend/task/add_rlt_user";
                            //    }
                            //    $.post(url,{
                            //        UserId:json.Data.UserId,
                            //        Type:1,
                            //        Id:Id
                            //    },function(dt){
                            //        if(dt.Result==0){
                            //            alert(dt.Msg);
                            //        }else{
                            //            alert(dt.Msg);
                            //        }
                            //    });
                            //}
                        }else{
                            alert(json.Msg);
                        }
                    });
                }
                else if(telReg.test(emailMobile)){
                    $.get(urlApi,parameterObj).done(function(json){
                        if(json.Result==0){
                            alert(json.Msg);
                            user_selector.find("span.avatar").attr("data-id", json.Data.UserId);
                            user_selector.find("img").attr("src",json.Data.Avatar).attr("title",json.Data.UserName);
                            user_selector.find("input[name=ProjectManagerId]").val(json.Data.UserId);
                            user_selector.find("input[name=AssignedTo]").val(json.Data.UserId);
                            user_selector.find("#label_name").text(json.Data.UserName);

                            $("#jl_divBox").hide();
                            $("#form_box").hide();
                            workObj.find("div.user-selector").show();
                            //if (workPanelId != "new-task" && workPanelId != "new-project") {
                            //    var Id = $("#" + workPanelId + " input[name=Id]").val();
                            //    var url="";
                            //    if (workPanelId == "edit-project") {
                            //        url = "/backend/project/add_rlt_user";
                            //    } else {
                            //        url = "/backend/task/add_rlt_user";
                            //    }
                            //    $.post(url,{
                            //        UserId:json.Data.UserId,
                            //        Type:1,
                            //        Id:Id
                            //    },function(dt){
                            //        if(dt.Result==0){
                            //            alert(dt.Msg);
                            //        }else{
                            //            alert(dt.Msg);
                            //        }
                            //    });
                            //}
                        }else{
                            alert(json.Msg);
                        }
                    });
                }
                else{
                    alert("请输入正确格式的手机或邮箱!");
                }
            }else{
                alert("请输入邮箱或手机号!");
            }
        }else{
            alert("请输入昵称!");
        }
    });

    //user_selector.css({"margin-top": "-7px"}).find("a.avatar-add")
    //点击头像出现下拉列表
    user_selector.find(".click_div,.avatar-add").on("click", function () {
        var selectDefaultID = user_selector.find("span.avatar").attr("data-id");
        var List_panelS = $("#jl_divBox").find("#List_panel .dl_li");
        if(selectDefaultID!=undefined){
            for (var i = 0, len = List_panelS.length; i < len; i++) {
                if ($(List_panelS[i]).find("img").attr("userid") == selectDefaultID) {
                    $(List_panelS[i]).css({"color": "#fff", "background-color": "#2BA1D8"}).siblings().attr("style","");
                    break;
                }
            }
        }

        user_selector.hide();
        workObj.find("div.user-selector").eq(1).show().find("a.avatar-add").show().parents("dd").find(".gzr_box").hide();
        $("#jl_divBox").show().find("#List_panel").show().find(".dl_li").show();
        $("#jl_input").focus();
        //阻止事件冒泡及默认行为
        $("input#jl_input").on("change", function (e) {
            e = e || window.event;
            if (e.stopPropagation) {
                e.stopPropagation();
                e.preventDefault();
            } else {
                e.cancelBubble = true;
                e.returnValue = false;
            }
        });
        $("input.userName").on("change", function (e) {
            e = e || window.event;
            if (e.stopPropagation) {
                e.stopPropagation();
                e.preventDefault();
            } else {
                e.cancelBubble = true;
                e.returnValue = false;
            }
        });
        $("input.emailMobile").on("change", function (e) {
            e = e || window.event;
            if (e.stopPropagation) {
                e.stopPropagation();
                e.preventDefault();
            } else {
                e.cancelBubble = true;
                e.returnValue = false;
            }
        });

        //setTimeout(function(){
            //选择出现的人
            $("#List_panel>dl:not(:last)").on("click",function(){
                var _that=$(this),user_id=_that.find("img").attr("userid");
                var userMgId = 0;
                    //selectGzr = workObj.find("div.user-selector").eq(1);//关注人
                if (workPanelId == "edit-task" || workPanelId == "new-task" || workPanelId == "edit-task-calendar") {
                    userMgId = user_selector.find("input[name=AssignedTo]").val();
                }
                if (workPanelId == "new-project" || workPanelId == "edit-project" || workPanelId == "edit-main-project") {
                    userMgId = user_selector.find("input[name=ProjectManagerId]").val();
                }

                if (userMgId !=user_id) {
                    //先删除原有的责任人或项目经理
                    if(user_selector.find("span.avatar").attr("data-id")==undefined) {
                        user_selector.append('<span class="avatar" data-id="24"><img src="#" tip_title="" alt=""><div class="click_div" title="点击更换" style="position:absolute;top:0px;left:0px;width:100px;height:40px;cursor:pointer;"></div></span><label id="label_name" style="color: rgb(102, 102, 102);"></label>');
                    }
                    //取到用户选择的人
                    var userId = _that.find("img").attr("userid"),
                        avatar = _that.find("img").attr("src"),
                        name = _that.find(".p_name").text();
                    user_selector.find("a.avatar-add").hide();
                    user_selector.find("span.avatar").attr("data-id", userId);
                    user_selector.find("img").attr("src", avatar).attr("tip_title", name);
                    user_selector.find("input[name=ProjectManagerId]").val(userId);
                    user_selector.find("input[name=AssignedTo]").val(userId);
                    user_selector.find("#label_name").text(name);
                    //在添加进责任人中
                    if (workPanelId != "new-task" && workPanelId != "new-project") {
                        var Id = $("#" + workPanelId + " input[name=Id]").val(), url = "";
                        if (workPanelId == "edit-project" || workPanelId == "edit-main-project") {
                            url = "/backend/project/add_manager";
                        }
                        if (workPanelId == "edit-task" || workPanelId == "edit-task-calendar") {
                            url = "/backend/task/add_director";
                        }
                        $.post(url, {
                            UserId: userId,
                            Id: Id
                        }, function (json) {
                            if (json.Result == 0) {
                                alert(json.Msg);
                                user_selector.find(".avatar-add").hide();
                            } else {
                                alert(json.Msg);
                            }
                        });
                    }
                    workObj.find("#jl_divBox").hide();
                    user_selector.show();
                }else{
                    workObj.find("#jl_divBox,#form_box").hide();
                    workObj.find("div.user-selector").show();
                    //关闭并清理文本框
                    $("#jl_divBox").find("input#jl_input").val("");
                    $("#jl_divBox").find("input.userName").val("");
                    $("#jl_divBox").find("input.emailMobile").val("");
                }
            });

            //邀请没有出现过的人
            $("#List_panel>dl:last").on("click",function(){
                $("div.user-selector").hide();
                $("#jl_divBox").show();
                $("#jl_divBox").find("#List_panel").hide();
                $("#jl_divBox").find("#jl_input").show();

                var yqVal=$(this).find(".p2 span").text();
                $("#form_box").show().find("input[type=text]:first").val(yqVal);
            });
        //},200);
    });
}

//多人指派流程-(项目新增，项目编辑)
function moveAssignGzr(workPanelId){
    var workObj = $("#" + workPanelId + "");
    var user_selector = workObj.find("div.user-selector").eq(1);//关注人
    user_selector.parents("dd").find(".gzr_box").remove();

    user_selector.find("a.avatar-add").removeAttr("data-toggle").attr("href","#").attr("tip_Title","编辑关注者").addClass("demo demoDown");

    user_selector.parents("dd").append("<div class='gzr_box' style='display:none;'><input class='gzr_input' type='text' placeholder='昵称'><div class='dialog_drs'></div><div class='dl_listBox'></div><div class='form_input'><dl class='dl_in'><a class='icon icon-close close-btn'></a></dl><dl class='dl_in'><input type='text' placeholder='昵称' class='userName'></dl><dl class='dl_in'><input type='text' placeholder='手机号或者邮箱' class='emailMobile'></dl><dl class='dl_in'><input type='button' value='邀请'></dl></div></div>");
    //<span class='btn_qd'>确定</span><span class='btn_qx'>取消</span>
    var defaultSpan = user_selector.find("span.avatar");
    user_selector.find("a.delete").remove();
    if(defaultSpan.length>0){
        var htmlSpan="";
        for(var i=0,len=defaultSpan.length;i<len;i++){
            var userid=$(defaultSpan[i]).attr("data-id");
            var avatar=$(defaultSpan[i]).find("img").attr("src");
            var name=$(defaultSpan[i]).find("img").attr("tip_title");

            htmlSpan += '<span class="span_boxDl"><div class="avatar"><img userid="' +userid+ '" src="' + avatar + '" alt=""></div><span class="nc_title">' + name + '</span><span class="delete_sp">X</span></span>';
        }
        $(".gzr_box").prepend(htmlSpan);
        //点击删除
        $(".span_boxDl .delete_sp").on('click',function(){
            $(this).parents(".span_boxDl").hide();
            var that=$(this);
            if (workPanelId != "new-task" && workPanelId != "new-project") {
                var proID = $("#" + workPanelId + "").find("input[name=Id]").val();
                var userid = that.parents("span.span_boxDl").find("img").attr("userid");
                var url = "";
                if (workPanelId == "edit-project" || workPanelId == "edit-main-project") {
                    url = "/backend/project/add_rlt_user";
                }
                if (workPanelId == "edit-task" || workPanelId == "edit-task-calendar") {
                    url = "/backend/task/add_rlt_user";
                }
                $.post(url,{
                    UserId:userid,
                    Type:3,
                    Id:proID
                },function(json){
                    if(json.Result==0){
                        alert(json.Msg);
                    }else{
                        alert(json.Msg);
                    }
                });
            }
            setTimeout(function(){
                that.parents(".span_boxDl").remove();
            },200);
        });
    }

    $(".demoDown").tip_Title();
    //点击取消,.dialog_drs
    //$(".gzr_box").find(".btn_qx").on("click",function(){
    //    $(".gzr_box").hide();
    //    $(".gzr_box .form_input").hide();
    //    user_selector.show().find("a.avatar-add").show();
    //});

    //搜索效果
    $(".gzr_box input[type=text]").on("keyup",function(){
        var keyVal=$(this).val().toLowerCase();//转小写
        $(".dl_listBox dl.hui .p2 span").text(keyVal);
        if($(".dl_listBox .div_list").length>0){
            var len=$(".dl_listBox .div_list").length;
            var p_names=$(".dl_listBox .div_list>.p_name");
            var p_emails=$(".dl_listBox .div_list>.p_email");
            var p_mobiles=$(".dl_listBox .div_list>.p_mobile");
            for (var i = 0; i < len; i++) {
                var p_n = p_names.eq(i).text().toLowerCase(),
                    p_e = p_emails.eq(i).text().toLowerCase(),
                    p_m = p_mobiles.eq(i).text().toLowerCase();
                if (p_n.indexOf(keyVal) < 0 && p_e.indexOf(keyVal) < 0 && p_m.indexOf(keyVal) < 0) {
                    p_names.eq(i).parents("dl.dl_li").hide();
                } else {
                    p_names.eq(i).parents("dl.dl_li").show();
                }
            }
        }
    });

    //关闭邀请面板
    $(".form_input .close-btn").on("click",function(){
        $(".gzr_box").hide();
        $(".gzr_box .form_input").hide();
        user_selector.show().find("a.avatar-add").show();
    });

    //点击邀请
    $(".form_input input[type=button]").on("click", function () {
        var userName = $(".form_input").find(".userName").val();
        var emailMobile = $(".form_input").find(".emailMobile").val();
        var proId=0,taskId=0,urlApi="",parameterObj = new Object();//项目ID（新建任务或新建项目）
        var emailReg = /^([a-zA-Z0-9_-])+@([a-zA-Z0-9_-])+((\.[a-zA-Z0-9_-]{2,3}){1,2})$/;
        var telReg = /^(((13[0-9]{1})|(14[0-9]{1})|(15[0-9]{1})|(17[0-9]{1})|(18[0-9]{1}))+\d{8})$/;
        if(workPanelId=="new-project" || workPanelId=="edit-project" || workPanelId=="edit-main-project"){
            urlApi="/backend/user/invite_to_project";
            proId = $("#" + workPanelId + " input[name=Id]").val() == "" ? 0 : $("#" + workPanelId + " input[name=Id]").val();
            parameterObj = new Object();
            parameterObj.UserName = userName;
            parameterObj.Contact = emailMobile;
            parameterObj.ProjectId = proId;
            parameterObj.Role = 3;
        }else{
            if (workPanelId == "new-task" || workPanelId == "edit-task" || workPanelId == "edit-task-calendar") {
                urlApi="/backend/user/invite_to_task";
                proId = $("#" + workPanelId + "").find("select[name=ProjectId] option:selected").val();
                taskId = $("#" + workPanelId + " input[name=Id]").val() == "" ? 0 : $("#" + workPanelId + " input[name=Id]").val();
                parameterObj = new Object();
                parameterObj.UserName = userName;
                parameterObj.Contact = emailMobile;
                parameterObj.ProjectId = proId;
                parameterObj.TaskId = taskId;
                parameterObj.Role = 3;
            }
        }
        if(userName!=""){
            if(emailMobile!=""){
                if(emailReg.test(emailMobile)){
                    $.get(urlApi,parameterObj,function(json){
                        if(json.Result==0){
                            var userid = json.Data.UserId;
                            var avatar = json.Data.Avatar;
                            var name = json.Data.UserName;
                            var htmlSpan = '<span class="span_boxDl"><div class="avatar"><img userid="' +userid+ '" src="' + avatar + '" alt=""></div><span class="nc_title">' + name + '</span><span class="delete_sp">X</span></span>';
                            $(".gzr_box").prepend(htmlSpan);

                            $(".form_input").find(".userName").val("");
                            $(".form_input").find(".emailMobile").val("");

                            $(".span_boxDl .delete_sp").on('click',function(){
                                $(this).parent().hide();
                                var that=$(this);
                                setTimeout(function(){
                                    that.parent().remove();
                                    if (workPanelId != "new-task" && workPanelId != "new-project") {
                                        var proID = $("#" + workPanelId + "").find("input[name=Id]").val();
                                        var url="";
                                        if (workPanelId == "edit-project") {
                                            url = "/backend/project/add_rlt_user";
                                        } else {
                                            url = "/backend/task/add_rlt_user";
                                        }
                                        $.post(url,{
                                            UserId:userid,
                                            Type:3,
                                            Id:proID
                                        },function(json){
                                            if(json.Result==0){
                                                alert(json.Msg);
                                            }else{
                                                alert(json.Msg);
                                            }
                                        })
                                    }
                                },200);
                            });
                            $(".form_input").hide();
                            $(".dl_listBox").show();
                            ////修改项目时先邀请，在调用添加接口
                            //if (workPanelId != "new-task" && workPanelId != "new-project") {
                            //    var proID = $("#" + workPanelId + "").find("input[name=Id]").val();
                            //    var url="";
                            //    if (workPanelId == "edit-project") {
                            //        url = "/backend/project/add_rlt_user";
                            //    } else {
                            //        url = "/backend/task/add_rlt_user";
                            //    }
                            //    $.post(url,{
                            //        UserId:userid,
                            //        Type:3,
                            //        Id:proID
                            //    },function(json){
                            //        if(json.Result==0){
                            //            alert(json.Msg);
                            //        }else{
                            //            alert(json.Msg);
                            //        }
                            //    })
                            //}
                        }else{
                            alert(json.Msg);
                        }
                    });
                }
                else if(telReg.test(emailMobile)){
                    $.get(urlApi,parameterObj,function(json){
                        if(json.Result==0){
                            var userid = json.Data.UserId;
                            var avatar = json.Data.Avatar;
                            var name = json.Data.UserName;
                            var htmlSpan = '<span class="span_boxDl"><div class="avatar"><img userid="' +userid+ '" src="' + avatar + '" alt=""></div><span class="nc_title">' + name + '</span><span class="delete_sp">X</span></span>';
                            $(".gzr_box").prepend(htmlSpan);

                            $(".form_input").find(".userName").val("");
                            $(".form_input").find(".emailMobile").val("");

                            $(".span_boxDl .delete_sp").on('click',function(){
                                $(this).parent().hide();
                                var that=$(this);
                                setTimeout(function(){
                                    that.parent().remove();
                                    if (workPanelId != "new-task" && workPanelId != "new-project") {
                                        var proID = $("#" + workPanelId + "").find("input[name=Id]").val();
                                        var url="";
                                        if (workPanelId == "edit-project") {
                                            url = "/backend/project/add_rlt_user";
                                        } else {
                                            url = "/backend/task/add_rlt_user";
                                        }
                                        $.post(url,{
                                            UserId:userid,
                                            Type:3,
                                            Id:proID
                                        },function(json){
                                            if(json.Result==0){
                                                alert(json.Msg);
                                            }else{
                                                alert(json.Msg);
                                            }
                                        })
                                    }
                                },200);
                            });
                            $(".form_input").hide();
                            $(".dl_listBox").show();
                            ////修改项目时先邀请，在调用添加接口
                            //if (workPanelId != "new-task" && workPanelId != "new-project") {
                            //    var proID = $("#" + workPanelId + "").find("input[name=Id]").val();
                            //    var url="";
                            //    if (workPanelId == "edit-project") {
                            //        url = "/backend/project/add_rlt_user";
                            //    } else {
                            //        url = "/backend/task/add_rlt_user";
                            //    }
                            //    $.post(url,{
                            //        UserId:userid,
                            //        Type:3,
                            //        Id:proID
                            //    },function(json){
                            //        if(json.Result==0){
                            //            alert(json.Msg);
                            //        }else{
                            //            alert(json.Msg);
                            //        }
                            //    })
                            //}
                        }else{
                            alert(json.Msg);
                        }
                    });
                }
                else {
                    alert("请输入正确格式的手机号或邮箱!");
                }
            }else{
                alert("请输入邮箱或电话号码!");
            }
        }else{
            alert("请输入昵称!");
        }
    });

    //关注人点击添加按钮》展开下拉面板
    user_selector.find("a.avatar-add").on("click",function(){
        workObj.find("div.user-selector").eq(0).parents("dd").find("#jl_divBox").hide();
        $(".gzr_box").show().find(".dl_listBox").show();
        $(this).hide();
        $(this).parent().hide();
        $(".gzr_box").show();
        $("input.gzr_input").focus();
        $("input.gzr_input").on("change", function (e) {
            e = e || window.event;
            if (e.stopPropagation) {
                e.stopPropagation();
                e.preventDefault();
            } else {
                e.cancelBubble = true;
                e.returnValue = false;
            }
        });
        $("input.userName").on("change", function (e) {
            e = e || window.event;
            if (e.stopPropagation) {
                e.stopPropagation();
                e.preventDefault();
            } else {
                e.cancelBubble = true;
                e.returnValue = false;
            }
        });
        $("input.emailMobile").on("change", function (e) {
            e = e || window.event;
            if (e.stopPropagation) {
                e.stopPropagation();
                e.preventDefault();
            } else {
                e.cancelBubble = true;
                e.returnValue = false;
            }
        });

        setTimeout(function(){
            //选择出现的人
            $(".dl_listBox>dl:not(:last)").on("click",addGzrBox);

            //邀请没有出现过的人
            $(".dl_listBox>dl:last").on("click",function(e){
                e = e || window.event;
                e.stopPropagation();
                $(".gzr_box").find(".dl_listBox").hide();

                var yqVal=$(this).find(".p2 span").text();
                $(".form_input").show().find("input[type=text]:first").val(yqVal);
            });
        },200);
    });

    //添加关注人
    function addGzrBox(){
        //var userManager = workObj.find("div.user-selector").eq(0);
            //,userMgId=0;
        var userId = $(this).find("img").attr("userid");
        var avatar=$(this).find("img").attr("src");
        var name=$(this).find(".p_name").text();
        //var tipAlert="";
        //$(this).hide().unbind("click");

        //if (workPanelId == "edit-task" || workPanelId == "new-task" || workPanelId == "edit-task-calendar") {
        //    userMgId = userManager.find("input[name=AssignedTo]").val();
        //    tipAlert="您已经是责任人了!";
        //}
        //if (workPanelId == "new-project" || workPanelId == "edit-project" || workPanelId == "edit-main-project") {
        //    userMgId = userManager.find("input[name=ProjectManagerId]").val();
        //    tipAlert="您已经是项目经理了";
        //}

        //if(userId!=userMgId){
            var htmlSpan = '<span class="span_boxDl"><div class="avatar"><img userid="' + userId + '" src="' + avatar + '" alt=""></div><span class="nc_title">' + name + '</span><span class="delete_sp">X</span></span>';

            var spanLi=$(".gzr_box .span_boxDl:not(:hidden)");
            if(spanLi.length>0){
                var userIdArr=new Array();
                for(var i=0,len=spanLi.length;i<len;i++){
                    userIdArr.push($(spanLi[i]).find("img").attr("userid"));
                }
                if($.inArray(userId,userIdArr)<0){
                    $(".gzr_box").prepend(htmlSpan);
                    if (workPanelId != "new-task" && workPanelId != "new-project") {
                        var proID = $("#" + workPanelId + "").find("input[name=Id]").val();
                        var url="";
                        if (workPanelId == "edit-project" || workPanelId=="edit-main-project") {
                            url = "/backend/project/add_rlt_user";
                        }
                        if(workPanelId == "edit-task" || workPanelId == "edit-task-calendar"){
                            url = "/backend/task/add_rlt_user";
                        }
                        $.post(url,{
                            UserId:userId,
                            Type:3,
                            Id:proID
                        },function(json){
                            if(json.Result==0){
                                alert(json.Msg);
                            }else{
                                alert(json.Msg);
                            }
                        })
                    }
                }
            }else{
                $(".gzr_box").prepend(htmlSpan);
                if (workPanelId != "new-task" && workPanelId != "new-project") {
                    var proID = $("#" + workPanelId + "").find("input[name=Id]").val();
                    var url="";
                    if (workPanelId == "edit-project" || workPanelId == "edit-main-project") {
                        url = "/backend/project/add_rlt_user";
                    }
                    if(workPanelId == "edit-task" || workPanelId == "edit-task-calendar"){
                        url = "/backend/task/add_rlt_user";
                    }
                    $.post(url,{
                        UserId:userId,
                        Type:3,
                        Id:proID
                    },function(json){
                        if(json.Result==0){
                            alert(json.Msg);
                        }else{
                            alert(json.Msg);
                        }
                    })
                }
            }
        //}else{
        //    alert(tipAlert);
        //}

        $(".span_boxDl .delete_sp").on('click',function(){
            $(this).parent().hide();
            var that=$(this);
            setTimeout(function(){
                that.parent().remove();
                if (workPanelId != "new-task" && workPanelId != "new-project") {
                    var proID = $("#" + workPanelId + "").find("input[name=Id]").val();
                    var url="";
                    if (workPanelId == "edit-project" || workPanelId=="edit-main-project") {
                        url = "/backend/project/add_rlt_user";
                    }
                    if(workPanelId == "edit-task" || workPanelId == "edit-task-calendar"){
                        url = "/backend/task/add_rlt_user";
                    }
                    $.post(url,{
                        UserId:userId,
                        Type:3,
                        Id:proID
                    },function(json){
                        if(json.Result==0){
                            alert(json.Msg);
                        }else{
                            alert(json.Msg);
                        }
                    })
                }
            },200);
        });
    }

    $(".dialog_drs").on("click",function(){
        var selectDl = $(".gzr_box .span_boxDl:not(:hidden)"), htmlSapn = "", userIdList = new Array();
        for (var i = 0, len = selectDl.length; i < len; i++) {
            var userid = $(selectDl[i]).find("img").attr("userid");
            var src = $(selectDl[i]).find("img").attr("src");
            var username = $(selectDl[i]).find(".nc_title").text();
            userIdList.push(userid);
            htmlSapn += '<span class="avatar" data-id="' + userid + '"><img src="' + src + '" tip_title="' + username + '" alt=""></span>';
        }
        $(".gzr_box").hide().find(".form_input").hide();
        user_selector.show().find("a.avatar-add").show();
        user_selector.find("span.avatar").remove();
        user_selector.prepend(htmlSapn).find("input[name=Follwers]").val(userIdList.join());
    });

    ////点击确定
    //$(".gzr_box .btn_qd").on("click", function () {
    //    var selectDl = $(".gzr_box .span_boxDl:not(:hidden)"), htmlSapn = "", userIdList = new Array();
    //    for (var i = 0, len = selectDl.length; i < len; i++) {
    //        var userid = $(selectDl[i]).find("img").attr("userid");
    //        var src = $(selectDl[i]).find("img").attr("src");
    //        var username = $(selectDl[i]).find(".nc_title").text();
    //        userIdList.push(userid);
    //        htmlSapn += '<span class="avatar" data-id="' + userid + '"><img src="' + src + '" title="' + username + '" alt=""></span>';
    //    }
    //    $(".gzr_box").hide().find(".form_input").hide();
    //    user_selector.show().find("a.avatar-add").show();
    //    user_selector.find("span.avatar").remove();
    //    user_selector.prepend(htmlSapn).find("input[name=Follwers]").val(userIdList.join());
    //});
}

//移除控件
function assignRemoveManager(workPanelId){
    var workObj = $("#" + workPanelId + "");
    workObj.find("#jl_divBox").remove();
    var user_selector= workObj.find(".user-selector").eq(0);
    user_selector.find("a.avatar-add,a.delete").show();
    user_selector.find("img").removeAttr("id").unbind("click");
    user_selector.find("#label_name").remove();
    user_selector.show();

    var user_selector2=workObj.find(".user-selector").eq(1);
    workObj.find(".gzr_box").remove();
    //$(".gzr_box .form_input").hide();

    user_selector2.show().find("a.avatar-add").show();
}

//绑定单人多人下拉列表
function li_userSelect(workPanelId) {
    var workObj = $("#" + workPanelId + "");
    //取项目ID
    var projectId = 0, typeAPI = "all";
    if (workPanelId == "new-task" || workPanelId == "edit-task" || workPanelId == "edit-task-calendar") {
        projectId = $("#" + workPanelId + "").find("select[name=ProjectId] option:selected").val();
        if (projectId != 0) {
            typeAPI = "one";
        } else {
            typeAPI = "all";
        }
    } else {
        projectId = workObj.find("input[name=Id]").length > 0 ? workObj.find("input[name=Id]").val() : 0;
    }
    //生成选取项目经理列表
    get_user(projectId, typeAPI, workObj.find(".dl_listBox"),workObj.find("#List_panel"));
}

//获取项目关联的User
function get_user(projectId, typeAPI, dl_listBox, list_panel) {
    var userHtml = "", urlAPI = "";
    if (typeAPI == "all") {
        urlAPI = "/backend/user/get_cooperators";
        $.get(urlAPI, {}).done(function (json) {
            if (json.Result == 0) {
                var jsonDt = json.Data;
                if (jsonDt.length > 0) {
                    for (var i = 0, len = jsonDt.length; i < len; i++) {
                        userHtml += "<dl class='dl_li'><div class='avatar'><img userId='" + jsonDt[i].UserId + "' src='" + jsonDt[i].Avatar + "' alt=''><div class='div_list'>";
                        if (jsonDt[i].Email != null) {
                            userHtml += "<p class='p_name'>" + jsonDt[i].UserName + "</p><p class='p_email'>" + jsonDt[i].Email + "</p>";
                        } else if (jsonDt[i].Mobile != null) {
                            userHtml += "<p class='p_name'>" + jsonDt[i].UserName + "</p><p class='p_mobile'>" + jsonDt[i].Mobile + "</p>";
                        } else {
                            userHtml += "<p class='p_name' style='margin-top:8px;'>" + jsonDt[i].UserName + "</p>";
                        }
                        userHtml += "</div></div></dl>";
                    }
                    userHtml += "<dl class='dl_li hui'><p class='p1'>没有找到我要找的人</p><p class='p2'>邀请我的好友<span></span></p></dl>";
                } else {
                    userHtml += "<dl class='dl_li hui'><p class='p1'>没有找到我要找的人</p><p class='p2'>邀请我的好友<span></span></p></dl>";
                }
            }
            dl_listBox.html(userHtml);
            list_panel.html(userHtml);
        });
    }
    else {
        urlAPI = "/backend/user/get_user_by_project_id";
        $.post(urlAPI, {
            ProjectId: projectId
        }).done(function (json) {
            if (json.Result == 0) {
                var jsonDt = json.Data;
                if (jsonDt.length > 0) {
                    for (var i = 0, len = jsonDt.length; i < len; i++) {
                        userHtml += "<dl class='dl_li'><div class='avatar'><img userId='" + jsonDt[i].UserId + "' src='" + jsonDt[i].Avatar + "' alt=''><div class='div_list'>";
                        if (jsonDt[i].Email != null) {
                            userHtml += "<p class='p_name'>" + jsonDt[i].UserName + "</p><p class='p_email'>" + jsonDt[i].Email + "</p>";
                        } else if (jsonDt[i].Mobile != null) {
                            userHtml += "<p class='p_name'>" + jsonDt[i].UserName + "</p><p class='p_mobile'>" + jsonDt[i].Mobile + "</p>";
                        } else {
                            userHtml += "<p class='p_name' style='margin-top:8px;'>" + jsonDt[i].UserName + "</p>";
                        }
                        userHtml += "</div></div></dl>";
                    }
                    userHtml += "<dl class='dl_li hui'><p class='p1'>没有找到我要找的人</p><p class='p2'>邀请我的好友<span></span></p></dl>";
                } else {
                    userHtml += "<dl class='dl_li hui'><p class='p1'>没有找到我要找的人</p><p class='p2'>邀请我的好友<span></span></p></dl>";
                }
            }
            dl_listBox.html(userHtml);
            list_panel.html(userHtml);
        });
    }
}

//任务日志
$("#edit-task,#view-task").on("open", function () {
    var _that = $(this);
    _that.find("img#loading").show();
    _that.find("div.history_box>#history_box_cont").empty();
    setTimeout(function () {
        var id = _that.find("input[name=Id]").val();
        var history_box = _that.find("div.history_box>#history_box_cont");
        var loading = _that.find("img#loading");
        taskHistoryAjax(_that,id, history_box, loading);
    }, 800);
});
function taskHistoryAjax(_that,taskId,history_box,loading){
    $.get("/backend/task/get_log",{
        Id:taskId
    },function(json){
        if(json.Result==0){
            if(json.Data.length>0){
                //console.log(json);
                taskHistoryHtml(json.Data,history_box,loading);
            }else{
                _that.find("img#loading").hide();
            }
        }else{
            alert(json.Msg);
        }
    });
}

function taskHistoryHtml(dt, history_box,loading) {
    var htmlVal = "";
    for (var i = 0, len = dt.length; i < len; i++) {
        htmlVal += '<div class="old_Record"><div class="old_left"><span class="old_span_name">' + dt[i].User + '</span>';
        htmlVal += '<span class="old_span_title">' + dt[i].TypeName + '了任务</span>';
        if (dt[i].Type == 2) {
            var descArr = new Array(), descArr = dt[i].Desc;
            if (descArr != null && descArr != undefined && descArr.length > 1) {
                if(Object.prototype.toString.call(descArr) === '[object Array]'){
                    for (var j = 0, lens = descArr.length; j < lens; j++) {
                        if (descArr[j].New == "") {
                            htmlVal += '<p class="old_span_con" style="font-size:12px;color:#999;margin-left:0px;">加入收集箱中</p>';
                            break;
                        } else {
                            htmlVal += '<p class="old_span_con">';
                            htmlVal += '' + descArr[j].Field + '&nbsp;&nbsp;为：' + descArr[j].New + '';
                            htmlVal += '</p>';
                        }
                    }
                }else{
                    htmlVal+='<p class="old_span_con">数据异常</p>';
                }
            } else {
                if (descArr != null && descArr != undefined && descArr.length > 0) {
                    if (descArr[0].New == "") {
                        htmlVal += '<span class="old_span_con" style="font-size:12px;color:#999;margin-left:0px;">加入收集箱中</span>';
                    } else {
                        if (descArr[0].Field == "名称") {
                            htmlVal += '<span style="font-size:12px;color:#999;">' + descArr[0].Field + '为：</span><p class="old_span_con">' + descArr[0].New + '</p>';
                        } else if (descArr[0].Field == "优先级") {
                            if(descArr[0].New == 1){
                                htmlVal += '<span style="font-size:12px;color:#999;">为紧急</span>';
                            }else{
                                htmlVal += '<span style="font-size:12px;color:#999;">取消紧急</span>';
                            }
                        } else if (descArr[0].Field == "是否里程碑") {
                            if (descArr[0].New == 1) {
                                htmlVal += '<span style="font-size:12px;color:#999;">为里程碑</span>';
                            } else {
                                htmlVal += '<span style="font-size:12px;color:#999;">取消标记为里程碑</span>';
                            }
                        } else {
                            htmlVal += '<span class="old_span_con" style="font-size:12px;color:#999;margin-left:0px;">';
                            htmlVal += '' + descArr[0].Field + '为：' + descArr[0].New + '';
                            htmlVal += '</span>';
                        }
                    }
                }
            }
        }
        htmlVal += '</div><div class="old_right"><p>' + dt[i].CreateTime + '</p></div></div>';
    }
    loading.hide();
    history_box.empty().append(htmlVal);
}



