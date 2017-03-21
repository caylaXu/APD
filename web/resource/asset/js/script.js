

//	基础代码
if (!String.prototype.includes) {
	String.prototype.includes = function(search, start) {
		'use strict';
		if (typeof start !== 'number') {
			start = 0;
		}

		if (start + search.length > this.length) {
			return false;
		} else {
			return this.indexOf(search, start) !== -1;
		}
	};
}
//	AutoComplete off
$("form").attr("autocomplete","off");


//	屏蔽空链接
$("body").on("click","a[href='#']",function(e){
	e.preventDefault();
});


//	公共方法
var Func = {};

//退出登录
Func.LogOut = function(){
	window.location = "/backend/login/logout";
};

//	在元素上
$.fn.isIn = function(selector){
	return this.is(selector) || $(selector).find(this).length>0;
};

//	查找元素
$.fn.findIn = function(selector){
	if(this.is(selector)){
		return this;
	}
	else{
		return this.find(selector);
	}
};

//	读取行数据
$.fn.getRow = function(row){
	row = row || ".item";
	var item = $(this).is(row) ? $(this) : $(this).parents(row).eq(0);
	return item;
};
$.fn.getRowData = function(key,row){
	var item = $(this).getRow(row);
	var data = item.data("data") || eval(item.data("refer")) || {};
	return key ? data[key] : data ;
};
$.fn.setRowData = function(data,row){
	var item = $(this).getRow(row);	
	var olddata = $(this).getRowData(row);
	var newdata = $.extend(olddata,data);
	item.data("data",newdata);
	return item;
};

//	刷新数据
$.fn.refresh = function(){
	var data = $(this).data("data");
	var tpl = $(this).data("tpl");
	if(!tpl){
		return $(this);
	}
	var element = GenerateList(data,tpl);
	$(this).after(element).remove();
	element.trigger("refresh.data");
	return element;
};

//超出的文字自动+省略号
$.fn.limit=function(){
	var self = $("*[limit]");
	self.each(
		function(){
			var objString = $.trim($(this).text());
			var objLength = $.trim($(this).text()).length;
			var num = $(this).attr("limit");
			if(objLength > num){
				$(this).attr("title",objString);
				objString = $(this).text(objString.substring(0,num) + "...");
			}
		}
	)
};

//光标定位到字符最后
$.fn.selectRange = function(start, end) {
	return this.each(function() {
		if (this.setSelectionRange) {
			this.focus();
			this.setSelectionRange(start, end);
		} else if (this.createTextRange) {
			var range = this.createTextRange();
			range.collapse(true);
			range.moveEnd('character', end);
			range.moveStart('character', start);
			range.select();
		}
	});
};

//tip提示插件
$.fn.tip_Title = function () {
	$(this).mouseover(function () {
		var _this = $(this);
		_this.justToolsTip({
			animation: "moveInBottom",
			contents: _this.attr("tip_Title"),
			gravity: 'bottom'
		});
	});
}

//	填充select
$.fn.fillSelect = function(data,value,text){
	var select = $(this);
	var options = '';
	$.each(data,function(i,e){
		options += '<option value="'+e[value]+'">'+e[text]+'</option>'
	});

	select.children().filter(function(){
		var val = $(this).val();
		return val != -1 && val != 0 ;
	}).remove();
	select.append(options);

	var restore = select.data("value");
	if(restore){
		select.find("option").filter(function(){
			var val = $(this).val();
			
			if($.isArray(restore)){
				return restore.includes(val);
			}
			else{
				return val == restore;
			}				
		}).prop("selected",true);			
	}

	return select.trigger("set.data",{data:data}); 
};

// timestamp to php format (10)
moment.fn.getTime = function(count){
	count = count || 13;
	return this.toDate().getTime().toString().slice(0,count);
};

//	打开和关闭（popover,workpanel,dropdown,dialog ...）
$.fn.open = function(){
	$(this).each(function(){
		$(this).addClass("open").trigger("open");
	});
	return this;
};

$.fn.close = function(){
	$(this).each(function(){
		$(this).removeClass("open").trigger("close");
	});
	return this;
};
//$("body a[data-toggle=popover]").on("click",function(){
//	var href=$(this).attr('href');
//	if(!$(""+href+"").hasClass('in')){
//		$(""+href+"").attr("display","block");
//	}
//});


//$.fn.toggle = function(){
//
//	var target = this;
//
//	if(target.is(".open")){
//		target.close();
//	}
//	else{
//		target.open();
//	}
//	return this;
//};


$("body").on("click","[data-dismiss]",function(e){
	var target = $(this).data("dismiss");
	$(this).parents("."+target).close();
	e.stopPropagation();
	if($("#edit-task .popover_move").css('display')=="block"){
		$("#edit-task .popover_move").stop().hide('slow');
	}
});




//	convert json to array by key
function JsonToArray(list,key){

	var array = [];

	if(!list){
		return array;
	}

	$.each(list,function(i,v){
		if(v[key] != undefined){
			array.push(v[key]);
		}
	});

	return array;
}


//	数据处理集
DataBase.handler = $({});
DataBase.set = function(key,value){
	DataBase[key] = value;
	DataBase.handler.trigger("change.all",{value:value});
	DataBase.handler.trigger("change."+key,{value:value});
	$("[data-listener='"+key+"']").trigger("database.change",{value:value});
};

//	状态处理集
State.handler = $({});
State.set = function(key,value){
	State[key] = value;
	State.handler.trigger("change",{value:value});
};
State.on = function(a,b,c,d){
	State.handler.on(a,b,c,d);
}



//	today'time
DataBase.Today = {
    start: function(){       
        return moment().set({
            hour: 0,
            minute: 0,
            second: 0
        }).getTime(10);
    }(),
    end: function(){       
        return moment().set({
            hour: 23,
            minute: 59,
            second: 59
        }).getTime(10);
    }()
};


Unreal.ajax.set({
	error: function(){
		alert("请求失败，请重试");
	},
	badResponse: function(json){
		if(json.Result !=0){
			alert(json.Msg);
			return false;
		}
	}
});

var DefaultFormat = "YYYY/MM/DD HH:mm";


//	通用功能


//	Sidebar高亮

$(".sidebar .nav").find("a").filter(function(){
    return window.location.pathname == $(this).attr("href");
}).addClass("active").parents("li").addClass("active");

if(window.location.pathname.indexOf('/backend/task') ==0 ||
	window.location.pathname.indexOf('/backend/milestone') ==0){
	$(".sidebar").find('a[href="/backend/project/overview"]')
	.addClass("active").parents("li").addClass("active");
}


//	个人信息卡
$(".sidebar .user a").click(function(){
	var btn = $(this);
	$(".personal-card").stop().fadeIn(200);
	$("body").off("click.personalcard.hide").on("click.personalcard.hide",function(e){
		var target = $(e.target);
		if(target.isIn(".personal-card") || target.isIn(btn)){
			return;
		}
		else{
			$(".personal-card").stop().fadeOut(200);
			$("body").off("click.personalcard.hide");
		}
	});
});

/*var personalFirst = 0;
//	个人信息卡
$(".header .user").click(function(){
	var btn = $(this);
	$(".personal-card").stop().fadeIn(200);
	$("body").off("click.personalcard.hide").on("click.personalcard.hide",function(e){
		var target = $(e.target);
		if(target.isIn(".personal-card")){
			return;
		}else if(target.isIn(btn)){
			if(personalFirst == 1){
				$(".personal-card").stop().fadeOut(200);
				$("body").off("click.personalcard.hide");
				personalFirst = 0;
			}else{
				personalFirst++;
			}
		}
		else{
			$(".personal-card").stop().fadeOut(200);
			$("body").off("click.personalcard.hide");
			personalFirst = 0;
		}
	});
});*/

$("#userSetting").on("mouseover", function(){
	var btn = $(this);
	$(".personal-card").stop().fadeIn(200);
}).on("mouseout", function(){
	$(".personal-card").stop().fadeOut(200);
	$("body").off("click.personalcard.hide");
});


/*
var menuFirst = 0;
//菜单
$(".header .menu").click(function(){
	var btn = $(this);
	btn.addClass("active");
	$(".menu-card").stop().fadeIn(200);
	$("body").off("click.menu.hide").on("click.menu.hide",function(e){
		var target = $(e.target);
		if(target.isIn(".menu-card")){
			return;
		}else if(target.isIn(btn)){
			if(menuFirst == 1){
				btn.removeClass("active");
				$(".menu-card").stop().fadeOut(200);
				$("body").off("click.menu.hide");
				menuFirst = 0;
			}else{
				menuFirst++;
			}
		}
		else{
			btn.removeClass("active");
			$(".menu-card").stop().fadeOut(200);
			$("body").off("click.menu.hide");
			menuFirst = 0;
		}
	});
});

*/


$("#headerMenu").on("mouseover", function(){
	$(this).find(".menu").addClass("active");
	$(".menu-card").stop().fadeIn(200);
}).on("mouseout", function(){
	$(this).find(".menu").removeClass("active");
	$(".menu-card").stop().fadeOut(200);
});

$(".header>span.title").on("mouseover",function(){
	$(this).css("background","rgba(0,0,0,0.3)");
}).on("mouseout",function(){
	$(this).css("background","");
});

$(".header div.my-project-selector").on("mouseover",function(){
	$(this).css("background","rgba(0,0,0,0.3)");
}).on("mouseout",function(){
	$(this).css("background","");
});

$("div.pull-right>a").on("mouseover",function(){
	$(this).find("i").css({"font-weight":"bold","font-size":"23px"});
}).on("mouseout",function(){
	$(this).find("i").css({"font-weight":"100","font-size":"19px"});
});


//	popover，dialog淡入淡出动画
$("body").on("open",".popover,.dialog",function(){

	var target = $(this);
	target.show();
	setTimeout(function(){
		target.addClass("in");
	},50);
    if(!target.is(".message-popover")){
        $(".popover").not(".message-popover").not(this).close();
    }
}).on("close",".popover,.dialog",function(){
	var target = $(this);
	target.removeClass("in").transitionEnd(function(){
		if( ! target.is(".in")){
			target.hide();
			if(target.is(".instant")){
				target.remove();
			}
		}
	});
}).on("click",'.dialog .outer',function(e){
	if( $(e.target).is(this) && $(this).data("allow-close")){
		$(this).parents(".dialog").close();
	}
});


//	open popover

$.fn.popover = function(data){
	var base = $(this);
	var target = base.data("target") || base.attr("href");

	var top = base.offset().top - $(window).scrollTop() + 50;
	var left = base.offset().left;

	var maxTop = $(window).height() - 400;
	if(top > maxTop){
		top = maxTop;
	}

	var maxLeft = $(window).width() - 300;
	if(left > maxLeft){
		left = maxLeft;
	}

	//如果是日历页面,则根据work-panel位置确定popover的位置
	if($("#dbl-new-task").hasClass("open")){
		top = $("#dbl-new-task").offset().top + 100;
		left = $("#dbl-new-task").offset().left + 200;
		$(target).css({right: "initial"});
		$(target).data("from",base).css({top:top,left:left}).open().trigger("popover.open",base.data());
		return;
	}

	$(target).data("from",base).css({top:top,left:left}).open().trigger("popover.open",base.data());
};

$("body").on("click",'[data-toggle="popover"]',function(e){

	$(this).popover();

}).on("click",".popover",function(e){
	e.stopPropagation();
}).on("click",function(){
	$(".popover").close();
});


//	open dropdown

$.fn.dropdown = function(eventType){

	if(eventType == "click"){
		//不需要再次绑定
	}
	else if(eventType == "hover"){

		$(this).each(function(){

			var dropdown = $(this);

			dropdown.open();

			if(dropdown.data("active") !== undefined){
				return true;
			}

			dropdown.on("mouseenter",function(){
				dropdown.data("active",true);

			}).on("mouseleave",function(){

				dropdown.data("active",false);
				setTimeout(function(){
					if( !dropdown.data("active")){
						dropdown.close();
					}
				},500);

			});

		});
	}
	return this;
}

//	dropdown 点击打开/关闭
$("body").on("click",'[data-toggle="dropdown"]',function(e){
	e.preventDefault();
	$(this).parents(".dropdown").open();

}).on("click",function(e){
	var dropdown = $(e.target).parents(".dropdown");
	$(".dropdown").not(dropdown).close();
});

//	dropdown hover打开
$("body").on("mouseenter","[data-toggle=dropdown][data-trigger=hover]",function(){
	$(this).parents(".dropdown").dropdown("hover");
});

var arrayProject = [];
var arrayCondition = [];
var arrayValue = {
	project: "",
	condition: ""
};
$(".project-selector-popover").on("change",".list input",function(e){

	//选择成员
	var target = $(this);

	//UI refresh
	var value = function(){

		$(".projectlist").find(".item").filter(function(){
			return $(this).find("input").prop("checked");
		}).each(function(){
			arrayProject.push($(this).getRowData().Id);
		});

		$(".conditionlist").find(".item").filter(function(){
			return $(this).find("input").prop("checked");
		}).each(function(){
			arrayCondition.push($(this).data("type"));
		});
		return {project: arrayProject, condition: arrayCondition};
	}();
	arrayValue = value;

	arrayProject = [];
	arrayCondition = [];

}).on("keyup",".search",function(){

	//搜索成员
	var value = $(this).find("input").val();
	var popover = $(this).parents(".popover");
	var allMember = popover.data("data");

	var selected = popover.data("value");

	var sifted = function(){
		if( !value){
			return allMember;
		}
		else{
			return $(allMember).filter(function(){
				return this.Title.indexOf(value)>=0;
			}).get();
		}
	}();

	popover.trigger("refresh.project",{
		data: sifted
	});

}).on("refresh.project",function(e,data){

	$(this).find(".list ul").empty().append(function(){
		return GenerateList(data.data,"#project-tpl-dropdown");
	});

}).on("click",".get-new-task", function(){
	if(arrayValue == null){
		alert('请至少选择一项');
		return;
	}
	var params = {
		startDate: DataBase.Today.start,
		DueDate: DataBase.Today.end,
		ProjectId: arrayValue.project,
		Type: arrayValue.condition
	};
	var url = '/backend/calendar/calendar_tasks_by_params';

	var fcView = $("#calendar").fullCalendar( 'getView' ).name;
	if(fcView == 'month'){
		params.startDate = DataBase.MonthObj.startDate;
		params.DueDate = DataBase.MonthObj.DueDate;
	}

	if(fcView == 'agendaWeek'){
		params.startDate = DataBase.WeekObj.startDate;
		params.DueDate = DataBase.WeekObj.DueDate;
	}

	$.post(url,params, function(res){
		var data = res.Data;
		var events = [];
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
			}
		}
		$('#calendar').fullCalendar('removeEvents');
		$('#calendar').fullCalendar('addEventSource', events);
		$('#calendar').fullCalendar('rerenderEvents' );

	});

	$(this).parents(".popover").close();
});

//	获取成员列表
function GetMemberList(projectId){
	projectId = projectId || 0;
	return $.post("/backend/user/get_user_by_project_id",{
		ProjectId: projectId
	});
}

if(!DataBase.Member){
	var url=window.location.href;
	var urlArr=url.split('/');
	if(urlArr[urlArr.length-1]=="update" || urlArr[urlArr.length-1]=="workbench"){

	}else {
		GetMemberList(DataBase.ProjectId).done(function (json) {
			DataBase.set("Member", json.Data);
		});
	}
}

//获取项目成员

//填充所有项目
$("select[data-listener=Projects]").on("database.change",function(e,data){
	$(this).fillSelect(data.value,"Id","Title");
});

//填充我的项目
$("select[data-listener=MyProjects]").on("database.change",function(e,data){
	$(this).fillSelect(data.value,"Id","Title");
});

//获取所有项目
;(function(){
	var url=window.location.href;
	var urlArr=url.split('/');
	if(urlArr[urlArr.length-1]=="update"){
		return;
	}else{
		$.post("/backend/project/all_project").done(function(json){
			DataBase.set("Projects",json.Data.Projects);
			DataBase.set("MyProjects",json.Data.MyProjects);
		});
	}
})();

//	列表操作相关

//	列表删除(任务)
$("body").on("click",".task-list .delete-btn",function(e){
	e.preventDefault();
	var row = $(this).getRow();
	var data = row.getRowData();
	if(row.is(".waiting")){
		return false;
	}

	$.confirm("确定要删除“"+data.Title+"”吗？").then(function(){
		row.addClass("waiting");
		this.close();
		$.post('/backend/task/delete',{
			Id: data.Id
		}).done(function(json){
			alert("删除成功");
			row.trigger("row.delete");
		}).always(function(){
			row.removeClass("waiting");
		});
	});
}).on("row.delete",function(e){
	var row = $(e.target);
	row.remove();
});


//	列表删除(任务)
$("body").on("click",".work-panel .delete-btn",function(e){
	e.preventDefault();
	var row = $(this).parents(".work-panel").data("from").getRow();
	var data = row.getRowData();

	if(row.is(".waiting")){
		return false;
	}

	var _this = $(this);
	//延迟获取任务ID，和任务标题
	var taskId = 0,taskTitle="";
	setTimeout(function () {
		var delete_btn = $("div.confirm-dialog.open button.confirm-btn");
		taskId = delete_btn.attr("taskId");
		taskTitle = delete_btn.attr("taskTitle");
	}, 200);

	//删除确定
	$.confirm("确定要删除“"+data.Title+"”吗？").then(function(){
		row.addClass("waiting");
		this.close();
		$.post('/backend/task/delete',{
			Id: data.Id
		}).done(function(json){
			//刷新日历页面
			if(document.getElementById('calendar')){
				//$("#calendar").fullCalendar('refetchEvents');
				$("#calendar").fullCalendar('removeEvents',data.Id);
			}
			alert("" + json.Msg + "");
			row.trigger("row.delete");
			_this.parents(".work-panel").close();
		}).always(function(){
			row.removeClass("waiting");
		});
		//删除撤销操作
		if (taskId!= undefined) {
			var tip_template = $(template("tip_template", {
				Title: "【" + taskTitle + "】已删除",
				Type: "chexiao_delete",
				TypeName: "撤销"
			}));

			tip_template.appendTo("body").animate({"margin-left": "0px"}, 300, "", function () {
				window.clearTimeout(tip_template.trime);
				tip_template.trime = setTimeout(function () {
					tip_template.animate({"margin-left": "-330px"}, 300, "", function () {
						tip_template.remove();
					});
				}, 3000);
			});

			tip_template.find("i.icon-iconfonticonfontclose").on("click",function(){
				window.clearTimeout(tip_template.trime);
				tip_template.animate({"margin-left": "-330px"}, 300, "", function () {
					tip_template.remove();
				});
			});

			tip_template.find(".chexiao_delete").on("click",function(){
				window.clearTimeout(tip_template.trime);
				tip_template.animate({"margin-left": "-330px"}, 300, "", function () {
					tip_template.remove();
				});
				//执行撤销删除操作
				$.post("/backend/task/undo", {
					Id: taskId
				}, function (json) {
					if (json.Result == 0) {
						switch($("#my-project-ul li.activeLi").find('a').attr("datatype")){
							case "":
								$('#my-project-ul li:first-child').trigger("click");
								break;
							case "Created":
								$('#my-project-ul li').eq(1).trigger("click");
								break;
							case "Finished":
								$('#my-project-ul li').eq(3).trigger("click");
								break;
							case "All":
								$('#my-project-ul li').eq(4).trigger("click");
								break;
							default:
								$.reload(1);
								break;
						}
						//扩展tip提示
						$(".demoDown").tip_Title();
					} else {
						alert(json.Msg);
					}
				});
			});
		}
	});
}).on("row.delete",function(e){
	var row = $(e.target);
	row.remove();
});

//	列表编辑
$("body").on("click",".edit-btn",function(){
	var data = null;
	if(document.getElementById('calendar')){
		data = $(this).data("data");
	}else{
		data = $(this).getRowData();
	}
	var target = $(this).data("target") || $(this).attr("href");
	var url = $(target).find("form").attr("action");
	var $this = $(this);
	var nowTime = new Date().getTime();
	var clickTime = $(this).attr("cTime");

	if( clickTime != 'undefined' && (nowTime - clickTime < 600)){
		//console.log("请求失败");
		return false;
	}else{
		//console.log("请求成功");
		$(this).attr("cTime",nowTime);
		$(target).addClass("waiting");
		$.get(url,{
			Id: data.Id
		}).done(function(json){
			$this.setRowData(json.Data);
			$(target).loadData(json.Data).removeClass("waiting");
		});
	}
});


//	编辑成功
function EditSuccess(json){
	if(json.Result == 0){
		var workPanel = $(this).parents(".work-panel");
		var from = workPanel.data("from");
		var row = $(from).getRow();

		from = row.setRowData(json.Data).refresh();
		workPanel.data("from",from);

		//更新完成后，保持当前点击的颜色
		if(row.is('.active')){
			from.addClass('active');
		}

		//刷新日历页面
		if(document.getElementById('calendar')){
			$("#calendar").fullCalendar('removeEvents');
			$("#calendar").fullCalendar('refetchEvents');
		}
	}
}

//新建成功
function AddSuccess(json){
    if(json.Result == 0){
        alert(json.Msg);
        this.get(0).reset();
        $.reload(1.5);
    }
}
function AddProjectSuccess(json){
    if(json.Result==0){
        //重置表单
        $(this).get(0).reset();
        $(this).parents(".work-panel").close();

		//选择成员
        $("#project-member").find("[name=ProjectId]").val(json.Data.Id);
        $("#project-member").open();
    }
}



//	任务列表
$(".task-list").on("click",".progress,.checkbox",function(){
    //标记为已完成
    var $this = $(this);
    var data = $(this).getRowData();
    var progress = data.CompleteProgress == 100 ? 0 : 1;
    var completeProgress = progress ? 100 : 0;
	//设置定时器，用户点击了当前列表300毫秒后自动隐藏
    $.post("/backend/task/change_progress",{
        TaskId: data.Id,
        Progress: completeProgress
    }).done(function(json){
        if(json.Result == 0){
        	alert(json.Msg);
			var obj = $this.trigger("progress.change").setRowData({CompleteProgress: completeProgress}).refresh();
			window.clearTimeout(obj.timer);
			obj.timer = window.setTimeout(function () {
				obj.stop().fadeToggle('slow');
				window.clearTimeout(obj.timer);
				if($('div.work-panel').hasClass('open')==true){
					$('div.work-panel').close();
				}
				if ($("div.todo-panel span#teday_task").find("i").hasClass("icon-xiala") == true) {
					$.post('/backend/workbench', {
						Type: "TodayFinished",
					}).done(function (json) {
						if (json.Data.length > 0) {
							var list = GenerateList(json.Data, '#my-mark-tpl');
							$(".task-panel").show().find("ul").empty().append(list);
							$('.task-panel .task-list li dt .completed').addClass('checked');
						}
						//扩展tip提示
						$(".demoDown").tip_Title();
					});
					setTimeout(function () {
						$("div.task-panel li").css("opacity", "0.7").find("dd span").addClass("time").css("min-width", "0px");
					}, 200);
				}
			}, 300);
        }
    });

	if (completeProgress == 100) {
		var taskId = data.Id, title = data.Title;
		tip_fn(taskId,title);
	}
});

//	侧边栏相关
var flagFirst = 0;
$("body").on("click",'[data-toggle="work-panel"]',function(e){
	e.preventDefault();
	var target = $(this).data("target") || $(this).attr("href");
	$(target).data("from",$(this)).open();
	$(".work-panel").not(target).close();

	//打开面板之后聚焦
	if(target=="#new-task"){
		$(target).find("[name=Title]").focus();
	}

	$(".item").removeClass("active");
	var item = $(this).parents(".item");
	item.addClass("active");
	flagFirst++;
	$("body").off("click.hide.work.panel").on("click.hide.work.panel", function(e){
		var target = $(e.target);
		if(flagFirst == 1){
			flagFirst ++;
			return;
		}
		if(target.isIn(".work-panel") || target.isIn(".edit-btn")
			|| target.isIn(".available") || target.isIn(".next")
			|| target.isIn(".prev") || target.isIn("i.fa.fa-chevron-left")
			|| target.isIn("i.fa.fa-chevron-right") || target.isIn(".erase-btn")
			|| target.isIn(".submenu") || target.isIn(".icon-delete-avatar")
			|| target.isIn(".daterangepicker.dropdown-menu")){
			return;
		}else{
			$(".work-panel").close();
			$("body").off("click.hide.work.panel");
			item.removeClass("active");
			flagFirst = 0;
			if(document.getElementById('calendar')){
				$("#calendar").fullCalendar("rerenderEvents");
			}
		}
	});

}).on("open",".work-panel",function(){

	$("#layout").addClass("work-panel-open");

}).on("close",".work-panel",function(){

	if($(".work-panel").filter(".open").length == 0){
		$("#layout").removeClass("work-panel-open");
	}

}).on("click",'[data-dismiss="work-panel"]',function(e){

	if(document.getElementById('calendar')){
		$("#calendar").fullCalendar("rerenderEvents");
	}
});
////	标记里程碑
//$(".work-panel .is-milestone").on("set.value",function(e,data){
//	var tag = $(this);
//	var data = data.value;
//	//console.log(data);
//	if(!data){
//		return;
//	}
//
//	tag.find("input")
//	.prop("disabled",false)
//	.prop("checked",data.value==1).trigger("set.appearance");
//
//	if(data.value == 1){
//		tag.find(".text").text("已标记为里程碑");
//	}
//	else{
//		tag.find(".text").text("标记为里程碑");
//	}
//
//}).on("change",function(){
//	var tag = $(this);
//	var data = {};
//	data.value = tag.find("input").prop("checked") ? 1 : 0;
//	var form = tag.parents(".work-panel").find("form");
//	form.find("[name='IsMilestone']").val(data.value).trigger("change");
//	tag.setValue(data);
//});
//$(".work-panel").on("set.value","[name='IsMilestone']",function(e,data){
//	var tag = $(this).parents(".work-panel").find(".is-milestone");
//	//data.disabled = $(this).prop("disabled");
//	tag.setValue(data);
//});


//	成员选择
$(".user-selector").on("set.value",function(e,data){
	//console.log(data);
	//显示选中的成员
	$(this).find("span.avatar").remove();
	//特殊模板采用特殊样式
	if($(this).attr('tpl')=="avatar-edit-tpl-user"){
		$(this).addClass("user-selector-Member");
	}
	//更改为不同情况使用不同的模板来渲染
	$(this).prepend(function(){
		var avatar = GenerateList(data.value,$(this).attr('tpl'));
		return avatar;
	});

	//添加按钮(".avatar-add")上存储了成员的id和头像等数据
	//data.value是选中的成员，data.data是全部的成员
	$(this).find(".avatar-add").data("value",data.value);

	//input元素的value是选中的成员id
	$(this).find("input").val(JsonToArray(data.value,"UserId").join());

}).on("set.data",function(e,data){

	//设置全部成员
	$(this).find(".avatar-add").data("data",data.data);
}).on("click",".delete",function(){

	//删除成员
	var target = $(this).parents(".avatar");
	var memberId = target.data("id");
	var input = target.siblings("input");
	var avatarAdd = target.siblings(".avatar-add");
	var selected = avatarAdd.data().value;
	var popover = $(avatarAdd.attr("href"));

	//从"avatar-add"的value中剔除
	$.each(selected,function(i,v){
		if(v.UserId==memberId){
			selected.splice(i,1);
			return false;
		}
	});

	//从input中剔除
	var value = function(){
		var array = input.val().split(",");
		var index = array.indexOf(memberId);
		array.splice(index,1);
		return array.join();
	}();
	input.val(value);

	//请求后台
	var url = popover.data("url");
	var type = popover.data("type");
	var viewerId = target.parents("form").find("[name=Id]").val();

	if(viewerId){
		target.prop("disabled",true).hide();
		$.post(url,{
			UserId: memberId,
			Type: type,
			Id: viewerId
		}).done(function(json){
			target.prop("disabled",false);
			if(json.Result == 0){
				EditSuccess.call(target,json);
				target.remove();
			}
		});
	}
	else{
		target.remove();
	}
});

//	成员选择(弹窗)
$(".user-selector-popover").on("popover.open",function(e,data){
	//加载成员
	var from = $(this).data("from");

	//data来自添加按钮(".avatar-add")
	var allMember = data.data || $(from).parents(".work-panel").data("member") || DataBase.Member;
	//console.log(allMember);
	var selected = data.value;
	//console.log(selected);

	var allMemberIds = JsonToArray(allMember,"UserId");
	var selectedIds = JsonToArray(selected,"UserId");

	//if($(from).attr('href')!="#task-importUser-popover") {
	//选中的成员有可能不在列表中，需要扩展列表
	for (var i = 0; i < selectedIds.length; i++) {
		if(allMemberIds.indexOf(selectedIds[i])<0){
			allMember.push(selected[i]);
		}
	}
	//}
	var value = selectedIds;
	$(this).data("data",allMember).data("value",value).trigger("refresh.user",{
		data: allMember,
		value: value
	});
}).on("refresh.user",function(e,data){
	$(this).find(".list ul").empty().append(function(){
		return GenerateList(data.data,"#member-tpl");
	});
	$(this).loadData({UserId:data.value});
}).on("change",".list input",function(e){

	//选择成员
	var target = $(this);
	var popover = target.parents(".popover");
	var from = popover.data("from");

	//单选
	if(popover.data("limit") == 1){
		popover.find(".list").find("input:checked").not(this).click();
	}

	//UI refresh
	var value = function(){
		var array = [];
		target.parents(".list").find(".item").filter(function(){
			return $(this).find("input").prop("checked");
		}).each(function(){
			array.push($(this).data("data"));
		});
		return array;
	}();
	from.parents(".user-selector").setValue(value);

	//ajax refresh
	var url = popover.data("url");
	var type = popover.data("type");
	var id = from.parents("form").find("[name=Id]").val();
	var userId = target.getRowData("UserId");

	if(id){
		target.prop("disabled",true);
		$.post(url,{
			UserId: userId,
			Type: type,
			Id: id
		}).done(function(json){
			target.prop("disabled",false);
			if(json.Result == 0){
				EditSuccess.call(from,json);
			}
		});
	}
}).on("keyup",".search",function(){
	//搜索成员
	var value = $(this).find("input").val();
	var popover = $(this).parents(".popover");
	var allMember = popover.data("data");
	var selected = popover.data("value");
	var sifted = function(){
		if( !value){
			return allMember;
		}
		else{
			return $(allMember).filter(function(){
				return this.UserName.indexOf(value)>=0;
			}).get();
		}
	}();

	popover.find(".list ul .item").each(function(){
		var userid = $(this).data("data").UserId;
		$(this).hide();
		for(var i = 0, len = sifted.length; i < len; i++){
			if(userid == sifted[i].UserId){
				$(this).show();
			}
		}
	});

	//popover.trigger("refresh.user",{
	//	data: sifted,
	//	value: selected
	//});

}).on("click",".add-button",function(e){
	e.preventDefault();
	$(this).parents(".add-button-wrap").hide()
	.siblings("form").show()
	.find("input").focus();
}).on("blur",".add-member form",function(){
	var form = $(this);
	var timeout = setTimeout(function(){
		form.hide().siblings(".add-button-wrap").show();
	},100);
	form.data("timeout",timeout);
}).on("focus",".add-member form",function(){
	var timeout = $(this).data("timeout");
	clearTimeout(timeout);
});


//	添加成员
function AddMember(json){
	var form = $(this);
	var data = json.Data;
	var popover = form.parents(".popover");
	var workPanel = popover.data("from").parents(".work-panel");

	if(json.Result==0){
		var list = popover.find(".list").find("ul");

		//查重(列表)
		var existed = list.find("li").filter(function(){
			return $(this).getRowData("UserId") == data.UserId;
		});

		if(existed.length){
			var text = form.find("input").val();
			text = '"'+text+'" 已存在';
			alert(text);
			return false;
		}
		else{
			//添加到显示的列表中
			var member = GenerateList([data],"#member-tpl");
			list.prepend(member);
			member.find("input").click();
			alert("添加成功");
			form.find("input").val("");

			//添加到work-panel绑定的member中
			var memberList = workPanel.data("member") || DataBase.Member;
			if(!memberList){
				memberList = [];
			}
			memberList = memberList.slice();
			memberList.push(data);
			workPanel.data("member",memberList);
		}
		//
		// //查重DataBase
		// existed = $(DataBase.Member).filter(function(){
		// 	return this.UserId == data.UserId;
		// });
		//
		// if(!existed.length){
		// 	DataBase.Member.push(data);
		// }
	}
	else{
		alert(json.Msg);
	}
}


//	添加检查项
function ChecklistClone(item){
	var clone = item.clone();
	clone.find("[type=text]").val("");
	clone.find("[type=checkbox]").prop("checked",false).parents(".checkbox").removeClass("checked");
	return clone;
}
$(".checklist-group").on("set.value",function(e,data){
	//赋值
	var tpl = ChecklistClone($(this).find(".item").eq(0));
	var element = function(readonly){
		var elms = $();
		if(readonly){
			$.each(data.value,function(i,v){
				var clone = tpl.clone().data("data",v);
				clone.find(".form-control-text").text(v.Title);
				clone.find("[type=checkbox]").prop("checked",v.IsComplete==1);
				elms = elms.add(clone);
			});
			return elms;
		}
		else{
			$.each(data.value,function(i,v){
				var clone = tpl.clone().data("data",v);
				clone.find("[type=text]").val(v.Title);
				clone.find("[type=checkbox]").prop("checked",v.IsComplete==1);
				elms = elms.add(clone);
			});
			return elms.add(tpl);
		}
	}($(this).is(".readonly"));
	$(this).children("dd").empty().append(element)
	.find("[type=checkbox]").trigger("set.appearance");
}).on("change",function(e){
	e.stopPropagation();
	var input = $(e.target);
	var taskId = input.parents("form").find("[name=Id]").val();

	if(!taskId){
		//新建任务时不操作
		return true;
	}

	var data = input.getRowData();
	var row = input.getRow();
	var value = row.find("[type=text]").val();
	var isComplete = row.find("[type=checkbox]").prop("checked") ? 1 : 0;


	if(data.Id){

		if(input.is("[type=checkbox]")){
			input.trigger("set.appearance");
		}

		//修改
		$.post("/backend/checklist/edit",{
			Id: data.Id,
			Title: value,
			IsComplete: isComplete
		});
	}
	else if(input.is("[type=text]")){

		//新增
		$.post("/backend/checklist/create",{
			TaskId: taskId,
			Title: value
		}).done(function(json){
			if(json.Result == 0){
				input.setRowData(json.Data);
			}
		});
	}
	else if(input.is("[type=checkbox]")){
		input.prop("checked",false);
	}


}).on("keydown",function(e){

	//换行
	if(e.keyCode == 13 && $(e.target).val() != ''){
		e.preventDefault();
		$(e.target).parents(".item").siblings().filter(function(){
			return !$(this).find("[type=text]").val();
		}).remove();
		var clone = ChecklistClone($(e.target).getRow());
		$(this).children("dd").append(clone);
		clone.find("[type=text]").focus();
	}
}).on("click",".erase-btn",function(e){
	e.preventDefault();
	var taskId = $(this).parents("form").find("[name=Id]").val();
	var row = $(this).getRow();
	var data = row.getRowData();

	if(taskId && $(this).parent().find("[name='Checklist']").val() != ''){

		//修改任务时,请求后台删除
		$.post("/backend/checklist/delete",{
			Id: data.Id
		});

		if(row.siblings().length==0){
			row.after(ChecklistClone(row));
		}
		row.remove();
	}
});

//	重写 $.val()，调整时间格式
var _val = $.fn.val;
var specialVal = function(value){

	//对date-picker作特殊处理
	if($(this).is(".date-picker")){

		if(!arguments.length)
		{
			//取val时，时间文本转换为时间戳

			var value = _val.call(this);
			var result;

			if(!value){
				result = "";
			}
			else{
				result = moment(new Date(value)).getTime(10);
			}

			return result;
		}
		else
		{
			//设置val时，时间戳转换为时间文本
			value = value.toString();
			var result;

			if( !value || value==0){
				result = "";
			}
			else{
				//php的时间戳是10位（cayla是个坑比）
				if(value.length == 10){
					value += "000";
				}
				value = parseInt(value);
				var format = this.data("myformat") || DefaultFormat;
				result = moment(value).format(format);
			}

			return _val.call(this,result);
		}
	}
	else{
		if(!arguments.length)
		{
			return _val.call(this);
		}
		else
		{
			return _val.call(this,value);
		}
	}
};

//	加载表单数据 前/后, 调整时间格式
$(".work-panel").on("data.beforeload data.beforeserialize",function(){
	$.fn.val = specialVal;
}).on("data.afterload data.afterserialize",function(){
	$.fn.val = _val;
});

//	选择时间段
void function(){

	var container = $(".time-selector");
	var timeButton = container.find(".time-button");
	var datePicker = container.find(".date-range-picker");

	$(".time-selector").on("change",".date-range-picker",function(){

		container.find('[type="hidden"]').remove();
		timeButton.addClass("disabled");
		datePicker.removeClass("disabled");

		var start = datePicker.val().split("-")[0];
		var end = datePicker.val().split("-")[1];
		start = moment(start).getTime(10);
		end = moment(end).getTime(10);

		var startDate = $('<input type="hidden" name="StartDate" value="">').val(start);
		var endDate = $('<input type="hidden" name="EndDate" value="">').val(end);
		startDate.add(endDate).appendTo(container);

	}).on("change.on",".pill",function(){

		container.find('[type="hidden"]').remove();
		datePicker.addClass("disabled");
		timeButton.removeClass("disabled");

		var time = $(this).data("value");
		$('<input type="hidden" name="Time" value="">').val(time).appendTo(container);

	});
}();





//	项目为空时暂时可以选择（之后变成disabled）
$(".work-panel").on("set.value", "[name=ProjectId]", function(event, data){
	//console.log(data);
	if(data.value == "0"){
		$(this).prop("disabled", false);
	}else{
		//$(this).prop("disabled", true);
	}
	$(this).on("change", function(){
		//$(this).prop("disabled", true);
	});
});

//	task-view 项目readonly特殊显示状态
$(".work-panel").on("set.value","[name='ProjectId'].form-control-text",function(e,data){
	var project = $(DataBase.Projects).filter(function(){
		return this.Id == data.value;
	});
	if(project.length){
		$(this).text(project[0].Title);
	}
	else{
		$(this).text("无");
	}
});

//	收集箱状态
$(".work-panel").on("set.value", "[name=IsCollected]", function(event, data){
	//加入收集箱状态改变
	if(data.value == 1){
		$(this).parents(".no-time-group").show().siblings(".time-picker-group").hide().find("input").val("");
		$(this).parents(".work-panel").find(".collect-button").addClass("active");
		//setTimeout(function(){
		//	$('div.panel:not(:hidden)').find('li.item.active').css('display','none');
		//},200);
	}
	else{
		$(this).parents(".no-time-group").hide().siblings(".time-picker-group").show();
		$(this).parents(".work-panel").find(".collect-button").removeClass("active");
		//setTimeout(function(){
		//	$('div.panel:not(:hidden)').find('li.item.active').css('display','block');
		//},200);
	}
}).on("click",".collect-button",function(){

	//加入收集箱按钮操作
	var value = $(this).hasClass("active") ? 0 : 1;

	$(this).parents(".work-panel").find("[name=IsCollected]").setValue(value);
	//从收集箱移出，设置时间
	if(value==0){
		var startDate = moment().format(DefaultFormat);
		var dueDate = moment().add({hour:1}).format(DefaultFormat);
		$(this).parents(".work-panel").find("[name=StartDate]").val(startDate);
		$(this).parents(".work-panel").find("[name=DueDate]").val(dueDate);
	}

	//请求后台
	$(this).parents(".work-panel").find(".time-picker-group").trigger("change");

}).on("data.beforeload",function(event,data){
	data.IsCollected = data.StartDate==0 && data.DueDate==0 ? 1:0;
});




//	重置表单状态
$(".work-panel").on("reseting",function(){
  	$(this).find(".user-selector,.checklist-group").setValue([]);
  	$(this).find("[name='IsMilestone']").setValue(0);
	$(this).find("[name=IsCollected]").setValue(0);
});


//新建项目和任务时，默认指派给自己
$("#new-task,#new-project").on("open reseting",function(){
	var data = [{
		Avatar: DataBase.UserInfo.Avatar,
		UserId: DataBase.UserInfo.Id,
		UserName: DataBase.UserInfo.Name
	}];
	$(this).find("[name=ProjectManagerId],[name=AssignedTo]").setValue(data);
});

//	新建任务 设置默认时间
$("#new-task").on("open reseting",function(e){
	var url = window.location.href;
	var urlArr = url.split('/');
	var urlName="";
	if (url.indexOf("?") > 0) {
		urlName = urlArr[urlArr.length - 1].split('?')[0];
	}

	var now = moment().format(urlName == "task_tree" ? "YYYY/MM/DD" : DefaultFormat);
	var oneHourLater = moment().add({hour: 1}).format(urlName == "task_tree" ? "YYYY/MM/DD" : DefaultFormat);
	$(this).find("[name='StartDate']").val(now);
	$(this).find("[name='DueDate']").val(oneHourLater);

	$(this).find("[name=DueDate]").datetimepicker({
		format: $(this).data("format") || "yyyy/mm/dd hh:ii",
		autoclose: true,
		language: "zh-CN",
		minView: "month",
		maxView: "decade",
		todayBtn: true,
		startDate: "2016/01/01",
		todayHighlight: false,
		keyboardNavigation: true
	}).on('click', function (ev) {
		$(this).datetimepicker("setStartDate", now);
	});

}).on("change", "[name=StartDate]", function(e){
	var url = window.location.href;
	var urlArr = url.split('/');
	var urlName="";
	if (url.indexOf("?") > 0) {
		urlName = urlArr[urlArr.length - 1].split('?')[0];
	}

	var start = $(this).val();
	var endDate = $(this).parents(".work-panel").find("[name=DueDate]");
	if (moment(start).unix() > moment(endDate).unix()) {
		var oneHourLater = moment(start).add({hour: 1}).format(urlName == "task_tree" ? "YYYY/MM/DD" : DefaultFormat);
		$(this).parents(".work-panel").find("[name=DueDate]").val(oneHourLater);
	}

	$(this).parents(".work-panel").find("[name=DueDate]").datetimepicker({
		format: $(this).data("format") || "yyyy/mm/dd hh:ii",
		autoclose: true,
		language: "zh-CN",
		minView: "month",
		maxView: "decade",
		todayBtn: true,
		startDate: "2016/01/01",
		todayHighlight: false,
		keyboardNavigation:true
	}).on('click',function(ev){
		$(this).datetimepicker("setStartDate",start);
	});
});

//日历页面新建任务时
$("#dbl-new-task").on("open reseting",function(){
	var that=this;
	setTimeout(function(){
		var now = $(that).find("[name='StartDate']").val();
		$(that).find("[name=DueDate]").datetimepicker({
			format: $(this).data("format") || "yyyy/mm/dd hh:ii",
			autoclose: true,
			language: "zh-CN",
			minView: "month",
			maxView: "decade",
			todayBtn: true,
			startDate: "2016/01/01",
			todayHighlight: false,
			keyboardNavigation:true
		}).on('click',function(ev){
			$(this).datetimepicker("setStartDate",now);
		});
	},200);
}).on("change", "[name=StartDate]", function(e){
	var start = $(this).val();
	var endDate=$(this).parents(".work-panel").find("[name=DueDate]");
	if(moment(start).unix()>moment(endDate).unix()){
		var oneHourLater = moment(start).add({hour: 2}).format(DefaultFormat);
		$(this).parents(".work-panel").find("[name=DueDate]").val(oneHourLater);
	}

	$(this).parents(".work-panel").find("[name=DueDate]").datetimepicker({
		format: $(this).data("format") || "yyyy/mm/dd hh:ii",
		autoclose: true,
		language: "zh-CN",
		minView: "month",
		maxView: "decade",
		todayBtn: true,
		startDate: "2016/01/01",
		todayHighlight: false,
		keyboardNavigation:true
	}).on('click',function(){
		$(this).datetimepicker("setStartDate",start);
	});
});

//编辑任务或者编辑项目页面--默认打开时限定结束时间
$("#edit-task,#edit-project,#edit-task-calendar").on("open reseting",function(e){
	var that=this;
	setTimeout(function(){
		var start=$(that).find("[name='StartDate']").val();
		$(that).find("[name=DueDate]").datetimepicker({
			format: $(this).data("format") || "yyyy/mm/dd hh:ii",
			autoclose: true,
			language: "zh-CN",
			minView: "month",
			maxView: "decade",
			todayBtn: true,
			startDate: "2016/01/01",
			todayHighlight: false,
			keyboardNavigation:true
		}).on('click',function(ev){
			$(this).datetimepicker("setStartDate",start);
		});
	},300);
});

//任务编辑
$("#edit-task").on("open reseting",function(){
	var startTime="",endTime="",old_diff="";
	var _that=this;
	setTimeout(function(){
		//选择前的开始时间和结束时间以及时间间隔
		startTime=$(_that).find("[name='StartDate']").val();
		endTime=$(_that).find("[name='DueDate']").val();
		old_diff=moment(endTime).diff(moment(startTime),"minute");
		$("#edit-task").on("change", "[name=StartDate]", function(e){
			//选择后的开始时间
			var $new_start = $(this).val(),$new_endTime="";
			if (moment($new_start).unix() > moment(endTime).unix()) {
				$new_endTime = moment($new_start).add({minute: old_diff}).format(DefaultFormat);
				$(_that).find("[name=DueDate]").val($new_endTime);

				//取到当前项目的ID
				var Id=$(this).parents(".work-panel").find('div.params input[name=Id]').val();
				//将开始时间和结束时间转化为时间戳
				var num1 = moment($new_start).unix();
				var num2 = moment($new_endTime).unix();
				$.post('/backend/task/edit',{'StartDate':num1,'DueDate':num2,'Id':Id}).done(function(json){
					$('div.panel li.item.active').find('span.orange').text(json.Data.DueDateString);
				});
			}
			//编辑任务时如果用户重新选择了开始时间则重新限定结束时间的选择范围
			$(this).parents(".work-panel").find("[name=DueDate]").datetimepicker({
				format: $(this).data("format") || "yyyy/mm/dd hh:ii",
				autoclose: true,
				language: "zh-CN",
				minView: "month",
				maxView: "decade",
				todayBtn: true,
				startDate: "2016/01/01",
				todayHighlight: false,
				keyboardNavigation:true
			}).on('click',function(ev){
				$(this).datetimepicker("setStartDate",$new_start);
			});
		});
	},300);
});

//	编辑任务 监听开始时间改变
//$("#edit-task").on("change", "[name=StartDate]", function(e){
//	var start = $(this).val();
//	var $due = $(this).parents(".work-panel").find("[name=DueDate]");
//	var due = $due.val();
//	if(moment(start).unix() > moment(due).unix()){
//		var a=moment(start);
//		var b=moment(due);
//		//计算开始时间和结束事件相差多少分钟
//		var minute_diff=a.diff(b,'minute');
//		//用开始时间加上原开始时间和结束时间的差值（分钟）
//		var oneHourLater = moment(start).add({minute: minute_diff}).format(DefaultFormat);
//		//将得到的新的结束时间赋值给DUE
//		$due.val(oneHourLater);
//		//取到当前项目的ID
//		var Id=$(this).parents(".work-panel").find('div.params input[name=Id]').val();
//		//将开始时间和结束时间转化为时间戳
//		var num1 = moment(start).unix();
//		var num2 = moment($due.val()).unix();
//		$.post('/backend/task/edit',{'StartDate':num1,'DueDate':num2,'Id':Id}).done(function(json){
//			window.setTimeout(function(){
//				$('div.panel li.item.active').find('span.orange').text(json.Data.DueDateString);
//			},300);
//		});
//	}
//	//编辑任务时如果用户重新选择了开始时间则重新限定结束时间的选择范围
//	$(this).parents(".work-panel").find("[name=DueDate]").datetimepicker({
//		format: $(this).data("format") || "yyyy/mm/dd hh:ii",
//		autoclose: true,
//		language: "zh-CN",
//		minView: "month",
//		maxView: "decade",
//		todayBtn: true,
//		startDate: "2016/01/01",
//		todayHighlight: false,
//		keyboardNavigation:true
//	}).on('click',function(ev){
//		$(this).datetimepicker("setStartDate",start);
//	});
//});

//新建项目 设置默认时间
$("#new-project").on("open reseting",function(e){
	var now = moment().format("YYYY/MM/DD");
	var oneWeekLater = moment().add({day:7}).format("YYYY/MM/DD");
	$(this).find("[name='StartDate']").val(now);
	$(this).find("[name='DueDate']").val(oneWeekLater);
	$(this).find("[name=DueDate]").datetimepicker({
		format: $(this).data("format") || "yyyy/mm/dd hh:ii",
		autoclose: true,
		language: "zh-CN",
		minView: "month",
		maxView: "decade",
		todayBtn: true,
		startDate: "2016/01/01",
		todayHighlight: false,
		keyboardNavigation:true
	}).on('click',function(ev){
		$(this).datetimepicker("setStartDate",now);
	});
}).on("change","input[name=StartDate]",function(){
	var start=$(this).val();
	var endTime=$(this).parents(".work-panel").find("[name=DueDate]").val();
	if (moment(start).unix() > moment(endTime).unix()) {
		var oneWeekLater = moment(start).add({day: 7}).format("YYYY/MM/DD");
		$(this).parents(".work-panel").find("[name=DueDate]").val(oneWeekLater);
	}

	$(this).parents(".work-panel").find("[name=DueDate]").datetimepicker({
		format: $(this).data("format") || "yyyy/mm/dd hh:ii",
		autoclose: true,
		language: "zh-CN",
		minView: "month",
		maxView: "decade",
		todayBtn: true,
		startDate: "2016/01/01",
		todayHighlight: false,
		keyboardNavigation:true
	}).on('click',function(ev){
		$(this).datetimepicker("setStartDate",start);
	});
});


//项目编辑
$("#edit-project").on("open reseting", function () {
	var startTime = "", endTime = "", old_diff = "";
	var _that = this;
	setTimeout(function () {
		startTime = $(_that).find("[name='StartDate']").val();
		endTime = $(_that).find("[name='DueDate']").val();
		old_diff = moment(endTime).diff(moment(startTime), "day");
		$("#edit-project").on("change", "[name=StartDate]", function (e) {
			//选择后的开始时间
			var $new_start = $(this).val(), $new_endTime = "";
			if (moment($new_start).unix() > moment(endTime).unix()) {
				$new_endTime = moment($new_start).add({day: old_diff}).format("YYYY/MM/DD");
				$(_that).find("[name=DueDate]").val($new_endTime);

				//取到当前项目的ID
				var Id=$(this).parents(".work-panel").find('div.params input[name=Id]').val();
				//将开始时间和结束时间转化为时间戳
				var num1 = moment($new_start).unix();
				var num2 = moment($new_endTime).unix();
				$.post('/backend/project/edit',{'StartDate':num1,'DueDate':num2,'Id':Id}).done(function(json){
					if(json.Result==0){
						window.setTimeout(function(){
							$("div.project-container:not(:hidden)").find("div.project-card.item.active span.start-time").html(json.Data.StartDateString).next("span.end-time").html(json.Data.DueDateString);
							alert("修改成功");
						},300);
					}
				});
			}
			$(this).parents(".work-panel").find("[name=DueDate]").datetimepicker({
				format: $(this).data("format") || "yyyy/mm/dd hh:ii",
				autoclose: true,
				language: "zh-CN",
				minView: "month",
				maxView: "decade",
				todayBtn: true,
				startDate: "2016/01/01",
				todayHighlight: false,
				keyboardNavigation:true
			}).on('click',function(ev){
				$(this).datetimepicker("setStartDate",$new_start);
			});
			return false;
		});
	}, 300);
});

//1、此时如果用户选中的开始时间大于结束时间 则改变结束时间，结束时间加2天（项目编辑）结束时间加1小时（日历页面任务编辑）
$("#edit-task-calendar").on("change", "[name=StartDate]", function(e){
	var start = $(this).val(),oneWeekLater,oneHourLater;
	var endTime=$(this).parents(".work-panel").find("[name=DueDate]").val();
	if(moment(start).unix()>moment(endTime).unix()){
		//日历页面任务编辑
		if($(this).parents(".work-panel").attr('id')=="edit-task-calendar"){
			oneHourLater = moment(start).add({hour: 1}).format(DefaultFormat);
			$(this).parents(".work-panel").find("[name=DueDate]").val(oneHourLater);
		}
	}
	$(this).parents(".work-panel").find("[name=DueDate]").datetimepicker({
		format: $(this).data("format") || "yyyy/mm/dd hh:ii",
		autoclose: true,
		language: "zh-CN",
		minView: "month",
		maxView: "decade",
		todayBtn: true,
		startDate: "2016/01/01",
		todayHighlight: false,
		keyboardNavigation:true
	}).on('click',function(ev){
		$(this).datetimepicker("setStartDate",start);
	});
});

//	任务所属项目变更时，获取项目成员
$(".work-panel").on("change","[name=ProjectId]",function(e,data){
	var workPanel = $(this).parents(".work-panel");
	var url=window.location.href;
	var urlArr=url.split('/');
	if(urlArr[urlArr.length-1]=="update" || urlArr[urlArr.length-1]=="workbench"){

	}else {
		GetMemberList(this.value).done(function(json){
			workPanel.data("member",json.Data);
		});
	}
}).on("close",function(){
	//关闭时移除member数据
	$(this).removeData("member");
});

// header 账号设置/更换背景/退出账号 鼠标效果
$("#userSetting li.setting").on("mouseover",function(){
	$(this).find("a").css({"color":"#2BA1D8"});
}).on("mouseout",function(){
	$(this).find("a").css({"color":"#aaa"});
});


$("a.avatar-add").on("mouseover",function(){
	$(this).find("i").css({"color":"#4097FC"});
}).on("mouseout",function(){
	$(this).find("i").css({"color":"#ddd"});
});

$("a.icon-erase").on("mouseover",function(){
	$(this).css("color",'red');
}).on("mouseout",function(){
	$(this).css("color",'#666');
});


//任务已完成
function tip_fn(taskId,title){
	$("body").find("div.tip_box").remove();
	var tip_template = $(template("tip_template", {Title: "" + title + "已完成", Type: "chexiao", TypeName: "撤销"}));
	tip_template.appendTo("body").animate({"margin-left": "0px"}, 300, "", function () {
		window.clearTimeout(tip_template.trime);
		tip_template.trime = setTimeout(function () {
			tip_template.animate({"margin-left": "-330px"}, 300, "", function () {
				tip_template.remove();
			});
		}, 3000);
	});

	tip_template.find("i.icon-iconfonticonfontclose").on("click",function(){
		window.clearTimeout(tip_template.trime);
		tip_template.animate({"margin-left": "-330px"}, 300, "", function () {
			tip_template.remove();
		});
	});

	tip_template.find("a.chexiao").on("click",function(){
		$.post("/backend/task/change_progress",{
			TaskId: taskId,
			Progress: 0
		}).done(function(json) {
			if (json.Result == 0) {
				alert(json.Msg);
				if ($("div.todo-panel span#teday_task").find("i").hasClass("icon-xiala") == true) {
					$("#teday_task").trigger("click");
				}
				switch($("#my-project-ul li.activeLi").find('a').attr("datatype")){
					case "":
						$('#my-project-ul li:first-child').trigger("click");
						break;
					case "Created":
						$('#my-project-ul li').eq(1).trigger("click");
						break;
					case "Finished":
						$('#my-project-ul li').eq(3).trigger("click");
						break;
					case "All":
						$('#my-project-ul li').eq(4).trigger("click");
						break;
					default:
						$.reload(1);
						break;
				}
				//扩展tip提示
				$(".demoDown").tip_Title();
				tip_template.animate({"margin-left": "-330px"}, 300, "", function () {
					tip_template.remove();
				});
			}
		});
	});
}

//加入收集箱去查看
$("#edit-task").find(".collect-button").eq(0).on("click",function(){
	var _that = $(this);
	if (_that.attr("taskType") == "task") {

	} else {
		var title = _that.parents("#edit-task").find("input[name=Title]").val();
		if (_that.hasClass("active") == false) {
			$("body").find("div.tip_box").remove();
			var tip_template = $(template("tip_template", {
				Title: "" + title + "已加入收集箱",
				Type: "chexiao_sjx",
				TypeName: "去查看"
			}));
			tip_template.appendTo("body").animate({"margin-left": "0px"}, 300, "", function () {
				window.clearTimeout(tip_template.trime);
				tip_template.trime = setTimeout(function () {
					tip_template.animate({"margin-left": "-330px"}, 300, "", function () {
						tip_template.remove();
					});
				}, 3000);
			});

			tip_template.find(".chexiao_sjx").on("click", function () {
				tip_template.animate({"margin-left": "-330px"}, 300, "", function () {
					tip_template.remove();
				});
				window.location.href = "/backend/workbench/collection";
			});

			tip_template.find("i.icon-iconfonticonfontclose").on("click", function () {
				window.clearTimeout(tip_template.trime);
				tip_template.animate({"margin-left": "-330px"}, 300, "", function () {
					tip_template.remove();
				});
			});
		}
	}
});

//删除任务
$("#edit-task").on("open", function () {
	var _that = $(this);
	setTimeout(function () {
		var taskId = _that.find("input[name=Id]").val();
		var title = _that.find("input[name=Title]").val();
		_that.find("#project-popover li").eq(1).find("a").on("click", function () {
			setTimeout(function () {
				$("div.confirm-dialog button.confirm-btn").attr("taskId", taskId).attr("taskTitle", title);
			}, 200);
		});
	}, 200);
});





