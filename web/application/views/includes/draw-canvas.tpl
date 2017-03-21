<script type="text/javascript">

function DrawCanvas(selector){

	$(selector).each(function(){

		var background = $(this).data("background") || "none";
		var strokeWidth = $(this).data("stroke-width") || 2;
		var strokeBackground = $(this).data("stroke-background") || "#cccccc";
		var value = $(this).data("value") || 0;
		var mainColor = $(this).data("color") || (value > 50 ? "#50cb47" : "#ff6565");
        var canvasWidth = $(this).width() == 0 ? 110 : $(this).width();
		var fontSize = (canvasWidth+30)/5;

		//兼容IE处理
		var ctx,canvas=this;
		//调用IE  模拟canvas画图
		if (typeof window.G_vmlCanvasManager!="undefined") {
			canvas=window.G_vmlCanvasManager.initElement(canvas);
			ctx=canvas.getContext("2d");
		}else {
			ctx=canvas.getContext("2d");
		}

//		var ctx = this.getContext("2d");

		var DrawCircle = function(r,percent,color){

			ctx.restore();
			var x = canvasWidth/2;
			var y = canvasWidth/2;
			var start = -Math.PI/2;
			var end = Math.PI * 2 * percent/100 - Math.PI/2;

			if(color == "none"){
				ctx.globalCompositeOperation = "destination-out";
			}

			ctx.fillStyle = color;
			ctx.beginPath();
			ctx.moveTo(x,y);
			ctx.arc(x,y,r,start,end);
			ctx.closePath();
			ctx.fill();
			ctx.globalCompositeOperation = "source-over";
			ctx.save();
		}

		var DrawStroke = function(percent,color,strokeWidth){
			var r = canvasWidth/2;
			DrawCircle(r,100,strokeBackground);
			DrawCircle(r,value,mainColor);
			DrawCircle(r-strokeWidth,100,background);
		}

		var DrawText = function(text,color){
			ctx.restore();
			var x = canvasWidth/2;
			var y = canvasWidth/2 + 10;

			ctx.fillStyle = color;
			ctx.textAlign = "center";
			ctx.font= fontSize+'px Arial';
			ctx.fillText(text,x,y);
			ctx.save();
		}

		DrawStroke(value,mainColor,strokeWidth);
		DrawText(value+"%",mainColor);

	});
}

DrawCanvas("{$selector}");

</script>
