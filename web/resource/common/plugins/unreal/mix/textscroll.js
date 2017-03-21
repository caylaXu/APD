
//	文字滚动
Unreal.textScroll = function(el){

	el = $(el);		
	var inner = el.wrapInner("<span></span>").children();
	
	el.css({
		"white-space": "nowrap",	    
    	"overflow": "hidden"
	});
	inner.css({
		"display":"block",
		"position":"relative"
	});		

	function animate(){
		var width = GetTextWidth(inner);
		var time = width/60 * 1000; //ms
		var opacity = 0;

		if(width < inner.outerWidth() +1){
			width = 0;
			opacity = 1;
		}

		inner.animate({"left": -width},time,"linear",function(){
			inner.css({
				"left":0,
				"opacity":opacity
			});
			inner.animate({"opacity": 1},500,animate);
		});
	}
	
	animate();

}

Unreal.textScroll(".text-scroll");