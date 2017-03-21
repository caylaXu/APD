

//	基础代码


//	AutoComplete off
$("form").attr("autocomplete","off");


//	屏蔽空链接
$("body").on("click","a[href='#']",function(e){
	e.preventDefault();
});


//	公共方法
var Func = {};

//退出登录
Func.LogOut = function(){
	window.location = "/backend/login/logout";
};





//	在元素上
$.fn.isIn = function(selector){
	return this.is(selector) || $(selector).find(this).length>0;
};

//	查找元素
$.fn.findIn = function(selector){
	if(this.is(selector)){
		return this;
	}
	else{
		return this.find(selector);
	}
};

//	读取行数据
$.fn.getRow = function(row){
	row = row || ".item";
	var item = $(this).is(row) ? $(this) : $(this).parents(row).eq(0);
	return item;
};
$.fn.getRowData = function(key,row){
	var item = $(this).getRow(row);
	var data = item.data("data") || eval(item.data("refer")) || {};
	return key ? data[key] : data ;
};
$.fn.setRowData = function(data,row){

	var item = $(this).getRow(row);	
	var olddata = $(this).getRowData(row);
	var newdata = $.extend(olddata,data);
	item.data("data",newdata);

	return item;
};

//	刷新数据
$.fn.refresh = function(){
	var data = $(this).data("data");
	var tpl = $(this).data("tpl");
	if(!tpl){
		return $(this);
	}
	var element = GenerateList(data,tpl);
	$(this).after(element).remove();
	element.trigger("refresh.data");
	return element;
};

//	填充select
$.fn.fillSelect = function(data,value,text){
	var select = $(this);
	var options = '';
	$.each(data,function(i,e){
		options += '<option value="'+e[value]+'">'+e[text]+'</option>'
	});

	select.children().filter(function(){
		var val = $(this).val();
		return val != -1 && val != 0 ;
	}).remove();
	select.append(options);

	var restore = select.data("value");
	if(restore){
		select.find("option").filter(function(){
			var val = $(this).val();
			
			if($.isArray(restore)){
				return restore.includes(val);
			}
			else{
				return val == restore;
			}				
		}).prop("selected",true);			
	}

	return select.trigger("set.data",{data:data}); 
};

// timestamp to php format (10)
moment.fn.getTime = function(count){
	count = count || 13;
	return this.toDate().getTime().toString().slice(0,count);
};



//	打开和关闭（popover,workpanel,dropdown,dialog ...）

$.fn.open = function(){

	$(this).each(function(){

		$(this).addClass("open").trigger("open");

	});

	return this;
	
};

$.fn.close = function(){

	$(this).each(function(){

		$(this).removeClass("open").trigger("close");
		
	});
		
	return this;
};

$.fn.toggle = function(){
	
	var target = this;

	if(target.is(".open")){
		target.close();
	}
	else{
		target.open();		
	}
	return this;
};


$("body").on("click","[data-dismiss]",function(e){
	var target = $(this).data("dismiss");
	$(this).parents("."+target).close();
});




//	convert json to array by key
function JsonToArray(list,key){

	var array = [];

	if(!list){
		return array;
	}

	$.each(list,function(i,v){
		if(v[key] != undefined){
			array.push(v[key]);
		}
	});

	return array;
}


//	数据处理集
DataBase.handler = $({});
DataBase.set = function(key,value){
	DataBase[key] = value;
	DataBase.handler.trigger("change.all",{value:value});
	DataBase.handler.trigger("change."+key,{value:value});
	$("[data-listener='"+key+"']").trigger("database.change",{value:value});
};

//	状态处理集
State.handler = $({});
State.set = function(key,value){
	State[key] = value;
	State.handler.trigger("change",{value:value});
};
State.on = function(a,b,c,d){
	State.handler.on(a,b,c,d);
}



//	today'time
DataBase.Today = {
    start: function(){       
        return moment().set({
            hour: 0,
            minute: 0,
            second: 0
        }).getTime(10);
    }(),
    end: function(){       
        return moment().set({
            hour: 23,
            minute: 59,
            second: 59
        }).getTime(10);
    }()
};


Unreal.ajax.set({
	error: function(){
		alert("请求失败，请重试");
	},
	badResponse: function(json){
		if(json.Result !=0){
			alert(json.Msg);
			return false;
		}
	}
});

var DefaultFormat = "YYYY/MM/DD HH:mm";