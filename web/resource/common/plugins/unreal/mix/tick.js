
//	计时器

Unreal.tick = function(setting){

	var defaults = {
		start : function(){},	//	计时开始时执行
		tick : function(){},	//	计时中循环执行
		end : function(){},		//	计时结束时执行
		during : 60,			//	持续时间(秒)
		interval : 1000			//	间隔时间(毫秒)
	}
	
	defaults = $.extend(true, defaults, setting);


	var tick = defaults.during;

	defaults.start();
	defaults.tick(tick);

	var interval = setInterval(function(){

		tick--;

		defaults.tick(tick);

		if(tick <= 0)
		{
			clearInterval(interval);
			defaults.end();
			return false;
		}
	},defaults.interval);
}