

Unreal.loading = function(str){
	
	var self = this;
	
	var img = function(){
		if(!!Unreal.loading.img)
		{
			return '<img src="'+Unreal.loading.img+'" >';
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

	var message;
	
	this.start = function(temp){
		
		if( !! message)
		{
			message.remove();
		}		
		message = $('<div class="message ie-shadow"><div '+ margin() +'>'+ img + '<p>'+ str +'</p></div></div>');
		
		message.appendTo("body").show().addClass("on");
		
		return self;
	}
	this.end = function(){
		message.removeClass("on");
		setTimeout(function(){
			message.remove();
		},500);
	}
	
	return self;
}
Unreal.loading.set = function(url){
	Unreal.loading.img = url;	
}
