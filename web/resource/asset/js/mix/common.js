

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


$("#headerMenu").on("mouseover", function(e){
	var btn = $(this).find(".menu");
	btn.addClass("active");
	$(".menu-card").stop().fadeIn(200);
}).on("mouseout", function(e){
	var btn = $(this).find(".menu");
	btn.removeClass("active");
	$(".menu-card").stop().fadeOut(200);
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
	$(this).parents(".dropdown").toggle();

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
	GetMemberList(DataBase.ProjectId).done(function(json){
		DataBase.set("Member",json.Data);
	});
}

//	获取项目列表

//填充所有项目
$("select[data-listener=Projects]").on("database.change",function(e,data){
	$(this).fillSelect(data.value,"Id","Title");
});

//填充我的项目
$("select[data-listener=MyProjects]").on("database.change",function(e,data){
	$(this).fillSelect(data.value,"Id","Title");
});

//获取所有项目
$.post("/backend/project/all_project").done(function(json){
	DataBase.set("Projects",json.Data.Projects);
	DataBase.set("MyProjects",json.Data.MyProjects);
});
