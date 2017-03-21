

// ie中的 placeholder 替代效果

Unreal.placeholder = function(){

	function isPlaceholer(){

		var input = document.createElement('input');

		return "placeholder" in input;

	}

	if( ! isPlaceholer())
	{
		$("[placeholder]").each(function(){

			var $this = $(this);

			if($this.val() == "")
			{
				var text = $this.attr("placeholder");
				var color = $this.css("color");
				
				if($this.is("[type=password]"))
				{
					var clone = $("<input type='text'>").val(text).css("color","#ccc").addClass($this.attr("class"));

					$this.after(clone).hide();
					
					clone.one("focus",function(){
						clone.remove();
						$this.show().focus();
					})
				}
		
				$this
				.val(text)
				.css("color","#ccc")
				.one("focus",function(){
					$this.val("").css("color",color);
				});
				

			}		
		});
	}

	//	重写 $.fn.val()
	if( !Unreal.placeholder.rewriteVal)
	{
		Unreal.placeholder.rewriteVal = true;

		var _val = $.fn.val;
		$.fn.val = function(value){
			if(!arguments.length)
			{
				var text = _val.call(this);
				if(text == $(this).attr("placeholder"))
				{
					return "";
				}
				else
				{
					return _val.call(this);
				}
			}
			else
			{
				return _val.call(this,value);
			}
		}
	}


}
	
Unreal.placeholder();
