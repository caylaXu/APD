
//背景图自适应
Unreal.fixedBackground = function(selector){
	selector = selector || ".fixed-background";

	$(window).on("resize.fixedbackground",function(){

		$(selector).each(function(){

			var bg = $(this);

			var ratio = bg.data("ratio");

			if(!ratio)
			{
				return true;
			}

			ratio = ConvertRatio(ratio);

			var baseline = bg.offset().top + bg.outerHeight();
			var footline = bg.data("footline");	//	window or footer or null

			if(footline == "window")
			{
				footline = $(window).height();
			}
			else if(footline == "footer")
			{
				footline = $("footer").offset().top;
			}
			else
			{
				footline = baseline;
			}

			if(baseline < footline)
			{
				bg.css("min-height", bg.height() + footline - baseline);
			}

			if(bg.outerHeight() / bg.outerWidth() < ratio)
			{
				bg.css("background-size","100%");
			}
			else
			{
				bg.css("background-size","auto 100%");
			}
		});
			
	}).trigger("resize.fixedbackground");

}

Unreal.fixedBackground();