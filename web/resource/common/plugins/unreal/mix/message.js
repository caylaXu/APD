
Unreal.message = function(str,state,time){
	
	var img = function(){
		if(!!Unreal.message.state && !!Unreal.message.state[state])
		{
			return '<img src="'+Unreal.message.state[state]+'" >';
		}
		else
		{
			return "";
		}
	}();

	var margin = function(){

		if($(".sidebar").length == 0)
		{
			return "";
		}
		else if($(".sidebar").is(".open"))
		{
			return 'style="margin-left: 210px;"';
		}
		else
		{
			return 'style="margin-left: 80px;"';
		}
	};

	var message = $('<div class="message ie-shadow"><div '+ margin() +'>'+ img + '<p>'+ str +'</p></div></div>');
	
	message.appendTo("body").show().addClass("on");
	
	var t = time;

	if(typeof t == "undefined")
	{
		t = 2500;
	}

	if(t != 0)
	{
		setTimeout(OffMessage,t);	//自动消失
	}
	
	
	//点击消失，异步操作，防止误点
	setTimeout(function(){
		$("body").on("click.offmessage",function(){
			OffMessage();
			$("body").off("click.offmessage");
		});
	},0);
	
	function OffMessage(){
		message.removeClass("on");
		setTimeout(function(){
			message.remove();
		},500);
	}
	
	
}
Unreal.message.set = function(setting){
	if( ! Unreal.message.state)
	{
		Unreal.message.state = {};
	}
	for(var key in setting)
	{
		Unreal.message.state[key] = setting[key];
	}
}