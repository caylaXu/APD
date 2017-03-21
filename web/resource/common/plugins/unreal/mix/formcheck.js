
//	错误提示

$(".form-group").on("error",function(event,msg){
	$(this).focus().addClass("has-error").one("keyup change",function(e){
		if(e.keyCode==13)
		{
			return false;
		}
		$(this).removeClass("has-error");
	});
	if(!!msg)
	{
		$(this).find(".error-feedback").text(msg);
	}

}).on("warning",function(event,msg){
	$(this).focus().addClass("has-warning").one("keyup change",function(e){
		if(e.keyCode==13)
		{
			return false;
		}
		$(this).removeClass("has-warning");
	});
	if(!!msg)
	{
		$(this).find(".warning-feedback").text(msg);
	}
});


//	提交前验证

$("[data-require]").on("click.check",function(){
	var require = $(this).data("require").split(" ");
	var box = $(this).parents("form").get(0) || "body";
	for(var i = 0; i < require.length; i++)
	{
		var target = $("[name="+require[i]+"]",box);
		if(target.length == 0)
		{
			return true;
		}
		if( ! target.val() || target.val() == target.attr("placeholder") )
		{
			target.trigger("warning").focus();
			return false;
		}
	}
	$(this).trigger("validated");
});
