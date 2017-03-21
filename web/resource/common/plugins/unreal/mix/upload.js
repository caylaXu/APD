
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
