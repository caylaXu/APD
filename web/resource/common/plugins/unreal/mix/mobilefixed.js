
//修复 mobile fixed bug

Unreal.mobileFixed = function(selector){

	selector = selector || "#mobile-viewport";

	if( $(selector).length == 0 )
	{
		return false;
	}

	var top = $("header.fixed").outerHeight();
	var bottom = $("footer.fixed").outerHeight();

	$("header.fixed").css({"position":"relative","margin-bottom":(-1)*top});
	$("footer.fixed").css({"position":"relative","margin-top":(-1)*bottom});
	$(selector).css({"padding-top":top,"padding-bottom":bottom});

	$(window).on("resize.mobilefixed",function(){

		var wheight = $(window).height();

		$(selector).outerHeight(wheight);

	}).trigger("resize.mobilefixed");

}

Unreal.mobileFixed();