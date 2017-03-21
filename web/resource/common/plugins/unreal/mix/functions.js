
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
	console.log(data);

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
	}).fail(function(){
		alert("请求失败，请重试");
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
