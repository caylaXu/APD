
//通用tab切换

Unreal.tab = function(setting){
	
	var defaults = {
		tab: ".tab",
		tab_active: "active",
		trigger: "click",
		group: "siblings",
		during: 250
	}
	
	defaults = $.extend(true, defaults, setting);
	
	function _switch(old,current,during){
		var dur = during || 250;
		if(old.length>0)
		{
			old.fadeOut(dur,function(){
				current.fadeIn(dur).trigger("change.open");
			}).trigger("change.close");
		}
		else
		{
			current.fadeIn(dur).trigger("change.open");
		}
			
	}

	if(!Unreal.tab.init)
	{
		Unreal.tab.init = true;
		
		$("body").on(defaults.trigger,defaults.tab,function(){
			
			var $this = $(this);

			var data = $.extend(true, {}, defaults, $this.data());

			if(data.trigger == "hover")
			{
				data.trigger = "mouseover";
			}

			if($this.is("."+data.tab_active))
			{
				$this.trigger("change.on");
				return true;
			}
			
			var group = function(){

				if(data.group == "siblings")
				{
					return $this.siblings();
				}
				else
				{
					return $(".tab[data-group="+ data.group +"]");
				}

			}();

			var old = group.filter("."+data.tab_active);
			
			//tab切换
			old.removeClass(data.tab_active).trigger("change.off");
			$this.addClass(data.tab_active).trigger("change.on");
			
			//content切换
			_switch($(old.data("for")),$($this.data("for")),data.during);

		});
	}		
}

Unreal.tab();