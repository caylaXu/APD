
//	checkbox & radio

$("body").on("set.appearance",".checkbox,.radio",function(e){

	e.stopPropagation();

	var $this = $(this);

	if($this.find("input").is(":checked"))
	{
		$this.addClass("checked");
	}
	else
	{
		$this.removeClass("checked");
	}

}).on("change",".checkbox,.radio",function(e){

	var $this = $(this);
	var radio = $this.find("input");
	var name = radio.attr("name");
	var group = $this.parents("form").find('[name="'+name+'"]');

	if(group.length){
		group.trigger("set.appearance");
	}
	else{
		$this.trigger("set.appearance");
	}
		

}).on("click",".checkbox,.radio",function(){

	if($(this).parents("label").length == 0 && !$(this).is("label")){

		var input = $(this).find("input");

		if(input.is("[type=checkbox]") && input.prop("checked")){
			input.prop("checked",false).trigger("set.appearance");
		}
		else{
			input.prop("checked",true).trigger("set.appearance");
		}
	}
});

$(".checkbox,.radio").trigger("set.appearance");

$("form").on("reseting",function(){
	$(this).find(".checkbox,.radio").trigger("set.appearance");
});