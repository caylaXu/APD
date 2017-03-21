
//	ajax提交

$("body").on("submit.ajax",".ajax-form",function(e,data){
	e.preventDefault();
	var form = $(this);

	if(form.is(".instant-form") && !data){
		return false;
	}
	var serialize = data || form.serializeJson();
	console.log(serialize)
	var type = form.attr("method") || "get";
	var url = form.attr("action");
	var success = form.attr("data-success") || function(){};
	var failed = form.attr("data-failed") || function(){};

	form.addClass("waiting").find("[type=submit]").attr("disabled",true);

	var request = form.data("request");
	if( ! request)
	{
		request = new Unreal.ajax();
		form.data("request",request);
	}
	request.start({
		type: type,
		data: serialize,
		url: url,
		success: function(json){
			eval(success).call(form,json,data);
			form.trigger("request.success",json);			
		},
		failed: function(json){
			eval(failed).call(form,json,data);
			form.trigger("request.error",json);
		},
		end: function(){
			form.removeClass("waiting").find("[type=submit]").attr("disabled",false);
		}
	});
});

//	即时修改
$("body").on("change",".instant-form",function(e){

	var target = $(e.target);

	if(target.is("[type=checkbox]") || target.is("[type=radio]"))
	{
		var name = target.attr("name");
		var group = $(this).find('[name="'+name+'"]');
		if(group.length)
		{
			target = group;
		}
	}

	var params = $(this).find(".params input,input.params").add(target);
	params = params.serializeJson();
	$(this).trigger("submit.ajax",params);
});