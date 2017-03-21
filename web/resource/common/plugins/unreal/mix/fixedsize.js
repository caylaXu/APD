
//固定尺寸

Unreal.fixedSize = function(obj,setting){
	//setting = "4x3" : width x height or "1.6" : ratio 
	
	obj = obj || "[fixed-size]";
	var defaults = setting || "1";
	
	$(obj).each(function(){

		
		var $this = $(this);
		var fixedHeight;

		if($this.data("f_s_ready"))
		{
			return true;
		}

		$this.data("f_s_ready",true);
		
		function fixSize(){
			
			if( !! $this.data("fixed-height"))
			{
				fixedHeight = $this.data("fixed-height");
				$this.removeData("fixed-height");
			}
			else
			{
				fixedHeight = getFixedHeight();
				if( $this.is("[sametoall]"))
				{
					$this.siblings().data("fixed-height",fixedHeight);
				}
			}
					
			$this.outerHeight(fixedHeight);
		}
		
		function getFixedHeight(){
			
			var setting = $this.attr("fixed-size");
			if( !!setting)
			{
				defaults = setting;
			}
			
			var ratio = ConvertRatio(defaults);
			
			return $this.outerWidth() * ratio;	
		}
		

		$(window).on("resize.fixedsize",function(){			
			fixSize();
		}).trigger("resize.fixedsize");
		
	});
}

Unreal.fixedSize();