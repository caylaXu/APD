
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
			error: function() {
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
