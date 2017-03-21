

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
	$.confirm("确定要删除“"+data.Title+"”吗？").then(function(){

		row.addClass("waiting");
		this.close();

		$.post('/backend/task/delete',{
			Id: data.Id
		}).done(function(json){
			//刷新日历页面
			if(document.getElementById('calendar')){
				$("#calendar").fullCalendar('refetchEvents');
			}
			alert("删除成功");
			row.trigger("row.delete");
			_this.parents(".work-panel").close();
		}).always(function(){
			row.removeClass("waiting");
		});


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
	$(target).addClass("waiting");
	$.get(url,{
		Id: data.Id
	}).done(function(json){
		$this.setRowData(json.Data);
		$(target).loadData(json.Data).removeClass("waiting");
	});
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

    $.post("/backend/task/change_progress",{
        TaskId: data.Id,
        Progress: completeProgress
    }).done(function(json){
        if(json.Result == 0){
        	alert(json.Msg);
            $this.trigger("progress.change").setRowData({CompleteProgress: completeProgress}).refresh();
        }
    });
});
