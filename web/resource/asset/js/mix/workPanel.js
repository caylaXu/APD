

//	侧边栏相关


var flagFirst = 0;

$("body").on("click",'[data-toggle="work-panel"]',function(e){
	e.preventDefault();
	var target = $(this).data("target") || $(this).attr("href");
	$(target).data("from",$(this)).open();
	$(".work-panel").not(target).close();

	//打开面板之后聚焦
	$(target).find("[name=Title]").focus();

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




//	标记里程碑
$(".work-panel .is-milestone").on("set.value",function(e,data){
	var tag = $(this);
	var data = data.value;
	//console.log(data);
	if(!data){
		return;
	}

	tag.find("input")
	.prop("disabled",false)
	.prop("checked",data.value==1).trigger("set.appearance");

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


//	成员选择
$(".user-selector").on("set.value",function(e,data){

	//显示选中的成员
	$(this).find("span.avatar").remove();
	$(this).prepend(function(){
		var avatar = GenerateList(data.value,'#avatar-edit-tpl');
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
	var selected = data.value;

	var allMemberIds = JsonToArray(allMember,"UserId");
	var selectedIds = JsonToArray(selected,"UserId");

	//选中的成员有可能不在列表中，需要扩展列表
	for (var i = 0; i < selectedIds.length; i++) {
		if(allMemberIds.indexOf(selectedIds[i])<0){
			allMember.push(selected[i]);
		}
	}

	var value = selectedIds;
	$(this).data("data",allMember).data("value",value)
	.trigger("refresh.user",{
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
		$(this).parents(".no-time-group")
		.show()
		.siblings(".time-picker-group")
		.hide()
		.find("input").val("");

		$(this).parents(".work-panel")
		.find(".collect-button").addClass("active");
	}
	else{
		$(this).parents(".no-time-group")
		.hide()
		.siblings(".time-picker-group")
		.show();

		$(this).parents(".work-panel")
		.find(".collect-button").removeClass("active");
	}
}).on("click",".collect-button",function(){

	//加入收集箱按钮操作
	var value = $(this).hasClass("active") ? 0 : 1;

	$(this).parents(".work-panel")
	.find("[name=IsCollected]")
	.setValue(value);

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
$("#new-task,#dbl-new-task").on("open reseting",function(e){
	var now = moment().format(DefaultFormat);
	var oneHourLater = moment().add({hour:1}).format(DefaultFormat);
	$(this).find("[name='StartDate']").val(now);
	$(this).find("[name='DueDate']").val(oneHourLater);

}).on("change", "[name=StartDate]", function(e){
	var start = $(this).val();
	var oneHourLater = moment(start).add({hour: 1}).format(DefaultFormat);
	$(this).parents(".work-panel").find("[name=DueDate]").val(oneHourLater);
});

//	编辑任务 监听开始时间改变
$("#edit-task").on("change", "[name=StartDate]", function(e){
	var start = $(this).val();
	var $due = $(this).parents(".work-panel").find("[name=DueDate]");
	var due = $due.val();
	if(start > due){
		var oneHourLater = moment(start).add({hour: 1}).format(DefaultFormat);
		$due.val(oneHourLater);
	}

});


//	新建项目 设置默认时间
$("#new-project").on("open reseting",function(e){
	var now = moment().format("YYYY/MM/DD");
	var oneWeekLater = moment().add({day:7}).format("YYYY/MM/DD");
	$(this).find("[name='StartDate']").val(now);
	$(this).find("[name='DueDate']").val(oneWeekLater);
});

//	任务所属项目变更时，获取项目成员
$(".work-panel").on("change","[name=ProjectId]",function(e,data){
	var workPanel = $(this).parents(".work-panel");
	GetMemberList(this.value).done(function(json){
		workPanel.data("member",json.Data);
	});
}).on("close",function(){
	//关闭时移除member数据
	$(this).removeData("member");
});
