

//	数据监听
//	data-render:渲染目标
//	data-container:目标所在区域（iframe/body/null）
//	例如：<input type="text" data-render=".target" data-container="iframe">



$("body").on("change keyup","[data-render]",function(e){

	e.stopPropagation();

	var $this = $(this);
	var data = $this.data();

	function render(ele,target){
		if(ele.is("img"))
		{
			target.attr("src",ele.attr("src"));
		}
		else
		{
			target.text(ele.val());
		}
	}	
	

	if( ! data.container)
	{
		var target = $(data.render);
		render($this,target);
	}
	else
	{
		$(data.container).each(function(){

			var container = $(this);

			if(container.is("iframe"))
			{
				container = $(this.contentDocument);
			}

			var target = container.find(data.render);
			render($this,target);

		});
	}

});
