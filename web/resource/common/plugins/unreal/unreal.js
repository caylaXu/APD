

var Unreal = {};


//ratio转化	—— width x height or height/width
function ConvertRatio(ratio){

	if(typeof ratio == "string" && ratio.indexOf("x") >= 0)
	{
		var temp = ratio.split("x");
		var width = parseInt(temp[0]);
		var height = parseInt(temp[1]);
		
		ratio = height / width;
	}
	else
	{
		ratio = parseFloat(ratio);			
	}

	return ratio;
}



//	根据文字内容计算元素宽度
function GetTextWidth(el){

	el = $(el);
	var pl = parseInt(el.css("padding-left"));
	var pr = parseInt(el.css("padding-right"));
	var fs = parseInt(el.css("font-size"));
	var ls = parseInt(el.css("letter-spacing"));
	var str = el.text();
	var len = str.length;
	var mb_len = mb_strlen(str);

	var full_width = mb_len-len;
	var half_width = len - full_width;

	var text_len = full_width * fs 
				   + half_width * Math.floor(fs/2+1) 
				   + ls * len
				   + pl + pr + 1;

	return text_len;

}

/** 
 * 函数名：计算字符串长度 
 * 函数说明：计算字符串长度，半角长度为1，全角长度为2 
 * @param str 字符串 
 * @return 字符串长度 
 */  
function mb_strlen(str){  
    var len = 0;  
    var i;  
    var c;  
    for (var i=0;i<str.length;i++){  
        c = str.charCodeAt(i);  
        if (isDbcCase(c)) { //半角  
            len = len + 1;  
        } else { //全角  
            len = len + 2;  
        }  
    }  
    return len;  
}  
  
/** 
 * 函数名：判断字符是全角还是半角 
 * 函数说明：判断字符是全角还是半角 
 * @param c 字符 
 * @return true：半角 false：全角 
 */  
function isDbcCase(c) {  
    // 基本拉丁字母（即键盘上可见的，空格、数字、字母、符号）  
    if (c >= 32 && c <= 127) {  
        return true;  
    }   
    // 日文半角片假名和符号  
    else if (c >= 65377 && c <= 65439) {  
        return true;  
    }  
    return false;  
}  


// CSS TRANSITION SUPPORT (Shoutout: http://www.modernizr.com/)
// ============================================================

$.support.transition = function(){

    var el = document.createElement('bootstrap')

    var transEndEventNames = {
      WebkitTransition : 'webkitTransitionEnd',
      MozTransition    : 'transitionend',
      OTransition      : 'oTransitionEnd otransitionend',
      transition       : 'transitionend'
    }

    for (var name in transEndEventNames) {
      if (el.style[name] !== undefined) {
        return { end: transEndEventNames[name] }
      }
    }

    return false // explicit for ie8 (  ._.)
}();

$.fn.transitionEnd = function(callback){
	var self = this;

	if ( ! $.support.transition){
		callback.call(self);
	}
	else{
		$(this).one($.support.transition.end,function(){
			callback.call(self);
		})
	}
}


//	Serialize To Json
$.fn.serializeJson = function(){

	var form = $(this);

	if( !form.is("form")){
		form.eq(0).trigger("data.beforeserialize");
		var json = $("<form></form>").append(form.clone()).serializeJson();
		form.eq(0).trigger("data.afterserialize",json);
		return json;
	}
	
	form.trigger("data.beforeserialize");

	//disabled字段也要提交
	var disabledItem = form.find("[disabled]");
	disabledItem.prop("disabled",false);

	//单独勾选框checkbox未勾选时也要提交
	var singleCheckbox = form.find(".single-checkbox").not(":checked");
	singleCheckbox.prop("checked",true).val("0");

	//字段push到json
	var json = {};
	function push(key,value){
		if(json[key] == undefined){
			json[key] = value;
		}
		else if($.isArray(json[key])){
			!!value && json[key].push(value);
		}
		else{
			var array = [];
			!!json[key] && array.push(json[key]);
			!!value && array.push(value);
			json[key] = array;
		}
	}

	//序列化
	var serializeArray = form.serializeArray();
		
	$.each(serializeArray,function(i,e){
		push(e.name,e.value);
	});

	//还原更改
	disabledItem.prop("disabled",true);	
	singleCheckbox.prop("checked",false).val("1");
	
	form.trigger("data.afterserialize",json);

	return json;
	
};


//	表单渲染

$.fn.setValue = function(value){

	var target = $(this);
	
	if(target.is("select")){
		target.find("option").filter(function(){
			var val = $(this).val();
			
			if($.isArray(value)){
				return value.includes(val);
			}
			else{
				return val == value;
			}				
		}).prop("selected",true);
		target.data("value",value);
	}
	else if(target.is("[type=radio]")){
		target.filter(function(){
			return $(this).val() == value;
		}).prop("checked",true);
		target.trigger("set.appearance");
	}
	else if(target.is("[type=checkbox]")){

		target.filter(function(){
			return value.includes($(this).val());
		}).prop("checked",true);
		target.filter(function(){
			return ! value.includes($(this).val());
		}).prop("checked",false);
		target.trigger("set.appearance");
	}
	else if(target.is(".form-control-text")){
		target.text(value);
	}
	else{
		target.val(value);		
	}

	target.trigger("set.value",{value:value});

	return target;
}

$.fn.loadData = function(data,lowercase){
	var form = $(this);

	if(!data){
		return form;
	}

	form.trigger("data.beforeload",data);

	$.each(data,function(key,value){
		// if($.isArray(value)){
		// 	key += "[]";
		// }
		var target = form.find("[name='"+key+"']");

        if(lowercase){
            target = form.find("[name]").filter(function(){
                var name = $(this).attr("name").toLowerCase();
                return name == key;
            });
        }

        if(target.length == 0){
        	return true;
        }

		target.setValue(value);
	});
	
	form.trigger("data.afterload",data);
	
	return form;
}


//	重写 $.post()
var _post = $.post;
$.post = function(url,data,callback,type){
	if(url.indexOf("/")==0 && window.HOST){
		url = HOST + url;
	}
	if(typeof data == "function"){
		type = callback;
		callback = data;
		data = null;
	}
	type = type || "json";
	return _post(url,data,callback,type).done(function(json){
		if(json.Result != 0){
			alert(json.Msg,"error");
		}
	}).fail(function(xhr){
		//console.log(xhr.status);
		if(xhr.status=="0"){

		}else{
			alert("请求失败，请重试");
		}
	});
}

//	重写 $.get()
var _get = $.get;
$.get = function(url,data,callback,type){
	if(url.indexOf("/")==0 && window.HOST){		
		url = HOST + url;
	}
	if(typeof data == "function"){
		type = callback;
		callback = data;
		data = null;
	}
	type = type || "json";
	return _get(url,data,callback,type).done(function(json){
		if(json.Result != 0){
			alert(json.Msg,"error");
		}
	}).fail(function(){
		alert("请求失败，请重试");
	});
}


//	window.location.reload
$.reload = function(time){
	time = time || 0;
	time = time * 1000;
	setTimeout(function(){
		window.location.reload();
	},time);
}


//	Array.includes
if (!Array.prototype.includes) {
	Array.prototype.includes = function(val){
		return $.inArray(val,this) > -1;
	}
}


//生成list
function GenerateList(list,tpl){ 
	var array = $();
	var generate = function(data,t){
		return template(t,data);
	}

	if(typeof tpl == "string" && tpl[0] == "#"){
		tpl = tpl.slice(1);
	}
	else if(typeof tpl == "string" && tpl[0] == "<"){
		generate = template.compile(tpl);
	}

	if( ! $.isArray(list)){
		list = [list];
	}

	$.each(list,function(_i,_v){
		var item = generate(_v,tpl);
		item = $(item).data("data",_v);
		array = array.add(item);
	});
	return array;
}


$("body").on("reset","form",function(){
	var form = $(this);
	setTimeout(function(){
		form.trigger("reseting");
	},10);
});



//ajax

Unreal.ajax = function(_setting) {

	var self = this;

	this.eventHandler = $({});

	this.id = 0;

	this.start = function(setting) {

		var defaults = $.extend(true,{}, this._defaults, _setting);

		defaults = $.extend(true,{}, defaults, setting);


		//	方法转换

		this.onsuccess = function(event, json) {

			if (defaults.badResponse(json) !== false) 
			{
				defaults.success(json);
			}
			else
			{
				defaults.failed(json);
			}
		}

		this.onerror = defaults.error;

		this.onend = defaults.end;


		//	事件替换

		var id = this.id;

		this.eventHandler
			.off("success." + id)
			.off("error." + id)
			.off("end." + id);

		if(defaults.onlyLast)	//	只有最后一次执行
		{
			this.id++;
			id = this.id;
		}			

		this.eventHandler
			.on("success." + id, this.onsuccess)
			.on("error." + id, this.onerror)
			.on("end." + id, this.onend);
		
		defaults.loading.start();

		$.ajax({
			type: defaults.type,
			dataType: defaults.dataType,
			url: defaults.url,
			async: true,
			data: defaults.data,
			beforeSend: defaults.beforeSend,
			success: function(json) {
				self.eventHandler.trigger("success." + id, json);
			},
			error: function(xhr) {
				self.eventHandler.trigger("error." + id);
			}
		})
		.always(function(){
			defaults.loading.end();
			self.eventHandler.trigger("end." + id);
		});
		

	};
};

Unreal.ajax.prototype._defaults = {
	type: "get",
	dataType: "json",
	data: "",
	url: "/",
	beforeSend: function(){},		//请求前
	error: function(){},			//请求失败
	badResponse: function(json) {},	//验证返回码
	success: function(json) {},		//验证通过
	failed: function(json){},		//验证未通过
	end: function(){},				//请求结束
	onlyLast: true,	//同时多次请求时，只允许最后一次执行callback
	loading: {
		start: function(){},
		end: function(){}
	}
}

Unreal.ajax.set = Unreal.ajax.prototype.set= function(option){
	$.extend(true, Unreal.ajax.prototype._defaults, option);
};


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

Unreal.message = function(str,state,time){
	
	var img = function(){
		if(!!Unreal.message.state && !!Unreal.message.state[state])
		{
			return '<img src="'+Unreal.message.state[state]+'" >';
		}
		else
		{
			return "";
		}
	}();

	var margin = function(){

		if($(".sidebar").length == 0)
		{
			return "";
		}
		else if($(".sidebar").is(".open"))
		{
			return 'style="margin-left: 210px;"';
		}
		else
		{
			return 'style="margin-left: 80px;"';
		}
	};

	var message = $('<div class="message ie-shadow"><div '+ margin() +'>'+ img + '<p>'+ str +'</p></div></div>');
	
	message.appendTo("body").show().addClass("on");
	
	var t = time;

	if(typeof t == "undefined")
	{
		t = 2500;
	}

	if(t != 0)
	{
		setTimeout(OffMessage,t);	//自动消失
	}
	
	
	//点击消失，异步操作，防止误点
	setTimeout(function(){
		$("body").on("click.offmessage",function(){
			OffMessage();
			$("body").off("click.offmessage");
		});
	},0);
	
	function OffMessage(){
		message.removeClass("on");
		setTimeout(function(){
			message.remove();
		},500);
	}
	
	
}
Unreal.message.set = function(setting){
	if( ! Unreal.message.state)
	{
		Unreal.message.state = {};
	}
	for(var key in setting)
	{
		Unreal.message.state[key] = setting[key];
	}
}


Unreal.loading = function(str){
	
	var self = this;
	
	var img = function(){
		if(!!Unreal.loading.img)
		{
			return '<img src="'+Unreal.loading.img+'" >';
		}
		else
		{
			return "";
		}
	}();

	var margin = function(){

		if($(".sidebar").length == 0)
		{
			return "";
		}
		else if($(".sidebar").is(".open"))
		{
			return 'style="margin-left: 210px;"';
		}
		else
		{
			return 'style="margin-left: 80px;"';
		}
	};

	var message;
	
	this.start = function(temp){
		
		if( !! message)
		{
			message.remove();
		}		
		message = $('<div class="message ie-shadow"><div '+ margin() +'>'+ img + '<p>'+ str +'</p></div></div>');
		
		message.appendTo("body").show().addClass("on");
		
		return self;
	}
	this.end = function(){
		message.removeClass("on");
		setTimeout(function(){
			message.remove();
		},500);
	}
	
	return self;
}
Unreal.loading.set = function(url){
	Unreal.loading.img = url;	
}


Unreal.upload = function(setting){


	var defaults = {
		target: "[type=file]",
		url: "",
		onstart: function(){console.log("上传开始");},
		callback: function(){console.log("上传成功");},
		onend: function(){console.log("上传结束");},
		params: {},
		autoUpload: true,
		maxSize: 10,
		clip: false
	}

	$.extend(true,defaults,setting);

	if( ! Unreal.upload.handler)
	{
		Unreal.upload.handler = $({});	//事件处理集
	}

	if( ! Unreal.upload.requestId )
	{
		Unreal.upload.requestId = 1;
	}

	if( ! Unreal.upload.iframe)
	{
		Unreal.upload.iframe = $('<iframe name="upload-iframe" class="remove"></iframe>');
		Unreal.upload.iframe.appendTo("body");
	}

	// if( ! Unreal.upload.clipReady && defaults.clip )	//引用Jcrop.js
	// {
	// 	Unreal.upload.clipReady = true;

	// 	var required = '<link rel="stylesheet" href="'+$cdn_url+'js/Jcrop/jquery.Jcrop.min.css" />\
	// 		<script type="text/javascript" src="'+$cdn_url+'js/Jcrop/jquery.Jcrop.min.js" ></script>';

	// 	$("body").append(required);
	// }

	var iframe = Unreal.upload.iframe;

	$("body").on("change",defaults.target,function(){

		var input = $(this);

		if( ! input.val())
		{
			return false;
		}

		var files = this.files;
		var fileSize = 0;

		if(!!files)
		{
			fileSize = files[0].size;
		}
		else
		{
			try{
				var objFSO = new ActiveXObject("Scripting.FileSystemObject");
		        var e = objFSO.getFile( this.value);
		        fileSize = e.size;
		    }catch(e){}
		}

		if(fileSize > defaults.maxSize * 1024 * 1024)
		{
			Unreal.message("文件大小不能超过"+ defaults.maxSize +"M","error");
			return false;
		}

		var requestId = Unreal.upload.requestId;

		var form = $('<form enctype="multipart/form-data" target="upload-iframe" class="remove" action="'+defaults.url+'" method="post"></form>');
		form.appendTo("body");
		$('<input type="hidden" name="RequestId" >').val(requestId).appendTo(form);
		$('<input type="hidden" name="CallbackFunction" >').val("window.parent.Unreal.upload.callback").appendTo(form);

		for(var key in defaults.params)
		{
			var params = defaults.params[key];
			if(typeof params == "function")
			{
				params = params.call(input);
			}

			var item = $('<input type="hidden" name="'+ key +'" >').val(params);
			item.appendTo(form);
		}

		Unreal.upload.handler.on("success"+requestId,function(event,data){

			// if(data.Result != 0) 	//上传失败
			// {
			// 	Unreal.message(data.data,"error");
			// }
			// else
			// {
			// 	if(defaults.clip && data.data != 1)	//图片裁剪（data.data=1是文件）
			// 	{

			// 		var clip_dialog = $('<div class="dialog-box clip-dialog hide">\
			// 			<div class="dialog">\
		 //    				<h1>请对图片进行裁剪</h1>\
		 //    				<div class="clip-container">\
		 //    					<img src="'+ $img_url + data.img_url +'?a=view" id="clip-img" alt="" />\
		 //    				</div>\
		 //    				<p class="button-group">\
		 //        				<input type="button" class="btn-gray btn-large" name="cancel" value="取消">\
		 //        				<input type="button" class="btn-orange btn-large" name="confirm" value="确定">\
		 //    				</p>\
			// 			</div>\
			// 		</div>').appendTo("body");

			// 		var clip_img = $("#clip-img");

			// 		clip_dialog.fadeIn(250,function(){

			// 			var width =defaults.expansion.width;
			// 			var height =defaults.expansion.height;

			// 			if(typeof width == "function")
			// 			{
			// 				width = width();
			// 			}
			// 			if(typeof height == "function")
			// 			{
			// 				height = height();
			// 			}

			// 			var ratio = width/height || 1;

			// 			var JcropHandler;

			// 			function updateCoords(c)
			// 			{
			// 				clip_dialog.data("coords",c);
			// 			}

			// 			clip_dialog.data({"width":data.width,"height":data.height});

			// 			clip_img.Jcrop({
			// 				aspectRatio : ratio,
			// 				onSelect : updateCoords
			// 			},function(){

			// 				JcropHandler = this;

			// 				var imgwidth = clip_img.width();
			// 				var imgheight = clip_img.height();

			// 				if(imgwidth > imgheight * ratio)
			// 				{
			// 					//超宽
			// 					var fixedwidth = imgheight * ratio;
			// 					var fixedheight = imgheight;
			// 					var offsetleft = (imgwidth - fixedwidth)/2;
			// 					JcropHandler.setSelect([offsetleft,0,offsetleft+fixedwidth,fixedheight]);
			// 				}
			// 				else if(imgwidth < imgheight * ratio)
			// 				{
			// 					//超高
			// 					var fixedwidth = imgwidth;
			// 					var fixedheight = imgwidth/ratio;
			// 					var offsettop = (imgheight - fixedheight)/2;
			// 					JcropHandler.setSelect([0,offsettop,fixedwidth,offsettop+fixedheight]);
			// 				}
			// 				else
			// 				{
			// 					//尺寸适合
			// 					JcropHandler.setSelect([0,0,imgwidth,imgheight]);
			// 				}



			// 			});

			// 			$(".clip-dialog [name=cancel]").on("click",function(){
			// 				clip_dialog.fadeOut(250,function(){
			// 					clip_dialog.remove();
			// 				});
			// 			});

			// 			$(".clip-dialog [name=confirm]").on("click",function(){

			// 				var $this = $(this);

			// 				var coords = clip_dialog.data("coords");

			// 				if( ! coords )
			// 				{
			// 					Unreal.message("请裁剪后再提交","error");
			// 					return false;
			// 				}

			// 				function GetOriginalImgWidth(img){
			// 					// var clone = $("<img>").attr("src",img.attr("src")).css({
			// 					// 	"position":"absolute",
			// 					// 	"left":"-1000%"
			// 					// }).appendTo("body");
			// 					// setTimeout(function(){
			// 					// 	clone.remove();
			// 					// },100);
			// 					// return clone.width();
			// 					return clip_dialog.data("width");
			// 				}

			// 				var img_ratio =  clip_img.width() / GetOriginalImgWidth(clip_img);

			// 				var send = {
			// 					pid : data.data,
			// 					x : coords.x / img_ratio,
			// 					y : coords.y / img_ratio,
			// 					w : coords.w / img_ratio,
			// 					h : coords.h / img_ratio,
			// 					width : width,
			// 					height : height
			// 				}

			// 				$this.attr("disabled",true);

			// 				var request = new Unreal.ajax();
			// 				request.start({
			// 					path : $web_url + "/image/cut",
			// 					send : send,
			// 					dataType : "text",
			// 					badResponse : function(url){
			// 						if(typeof url == "number")
			// 						{
			// 							Unreal.message("请求失败，请重试","error");
			// 							return true;
			// 						}
			// 					},
			// 					callback : function(url){

			// 						data.img_url = url;

			// 						defaults.callback(data,input);

			// 						clip_dialog.fadeOut(250,function(){
			// 							clip_dialog.remove();
			// 						});
			// 					},
			// 					end : function(){
			// 						$this.attr("disabled",false);
			// 					}

			// 				});


			// 			});


			// 		});


			// 	}
			// 	else
			// 	{
			// 		defaults.callback(data,input);
			// 	}
			// }

			defaults.callback.call(input,data);

			input.removeAttr("disabled");

			defaults.onend.call(input);

		});

		Unreal.upload.start = function(){

			if(defaults.onstart.call(input) !== false)
			{
				var clone = input.clone().insertAfter(input);
				input.appendTo(form);
				form.submit();
				Unreal.upload.requestId++;
				input.val("").attr("disabled","disabled").insertAfter(clone);
				clone.add(form).remove();
			}


		}

		if(defaults.autoUpload)
		{
			Unreal.upload.start();
		}

	});

	Unreal.upload.callback = function(requestId,result,msg,src,imgId){
		Unreal.upload.handler.trigger("success"+requestId,{Result:result,Msg:msg,Data:{ImgSrc:src,ImgId:imgId}});
	}

}



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

//	错误提示

$(".form-group").on("error",function(event,msg){
	$(this).focus().addClass("has-error").one("keyup change",function(e){
		if(e.keyCode==13)
		{
			return false;
		}
		$(this).removeClass("has-error");
	});
	if(!!msg)
	{
		$(this).find(".error-feedback").text(msg);
	}

}).on("warning",function(event,msg){
	$(this).focus().addClass("has-warning").one("keyup change",function(e){
		if(e.keyCode==13)
		{
			return false;
		}
		$(this).removeClass("has-warning");
	});
	if(!!msg)
	{
		$(this).find(".warning-feedback").text(msg);
	}
});


//	提交前验证

$("[data-require]").on("click.check",function(){
	var require = $(this).data("require").split(" ");
	var box = $(this).parents("form").get(0) || "body";
	for(var i = 0; i < require.length; i++)
	{
		var target = $("[name="+require[i]+"]",box);
		if(target.length == 0)
		{
			return true;
		}
		if( ! target.val() || target.val() == target.attr("placeholder") )
		{
			target.trigger("warning").focus();
			return false;
		}
	}
	$(this).trigger("validated");
});


//	ajax提交

$("body").on("submit.ajax",".ajax-form",function(e,data){
	e.preventDefault();
	var form = $(this);

	if(form.is(".instant-form") && !data){
		return false;
	}
	var serialize = data || form.serializeJson();
	var type = form.attr("method") || "get";
	var url = form.attr("action");
	var success = form.attr("data-success") || function(){};
	var failed = form.attr("data-failed") || function(){};

	form.addClass("waiting").find("[type=submit]").attr("disabled",true);

	var request = form.data("request");
	if( ! request)
	{
		request = new Unreal.ajax();
		form.data("request",request);
	}
	request.start({
		type: type,
		data: serialize,
		url: url,
		success: function(json){
			eval(success).call(form,json,data);
			form.trigger("request.success",json);			
		},
		failed: function(json){
			eval(failed).call(form,json,data);
			form.trigger("request.error",json);
		},
		end: function(){
			form.removeClass("waiting").find("[type=submit]").attr("disabled",false);
		}
	});
});

//	即时修改
$("body").on("change",".instant-form",function(e){

	var target = $(e.target);

	if(target.is("[type=checkbox]") || target.is("[type=radio]"))
	{
		var name = target.attr("name");
		var group = $(this).find('[name="'+name+'"]');
		if(group.length)
		{
			target = group;
		}
	}

	var params = $(this).find(".params input,input.params").add(target);
	params = params.serializeJson();
	$(this).trigger("submit.ajax",params);
});


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










