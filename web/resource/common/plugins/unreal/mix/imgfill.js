
//图片填充

Unreal.imgFill = function(selector){
	selector = selector || ".img-fill";
	$(selector).each(function(){
		var img = $(this);
		img.one("load",function(){
			img.css({
				"width":"100%",
				"height":"auto",
				"position":"relative",
				"top":0,
				"left":0
			});
			img.parent().css({"overflow":"hidden"});
			
			var width = img.width();
			var height = img.height();
			var fixedHeight = img.parent().height();

			var ratio = img.data("ratio");
			if(ratio)
			{
				fixedHeight = width * ratio;
			}

			var fixedWidth = fixedHeight * width / height;

			

			if( height > fixedHeight )
			{
				var offset = (-1) * (height - fixedHeight)/2;
				img.css({
					"top":offset					
				});
			}
			else if( height < fixedHeight )
			{
				var offset = (-1) * (fixedWidth - width)/2;
				img.css({
					"width":"auto",
					"max-width":"none",
					"height":fixedHeight,
					"left":offset
				})
			}
		});
		if(this.complete)
		{
			img.trigger("load");
		}

	});
}
Unreal.imgFill();