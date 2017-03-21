<!-- DatePicker -->
<link rel="stylesheet" href="{$CommonUrl}/plugins/datetimepicker/bootstrap-datetimepicker.min.css">
<script src="{$CommonUrl}/plugins/datetimepicker/bootstrap-datetimepicker.min.js"></script>
<style media="screen">
    .datetimepicker{
        position: absolute;
        top: 100%;
        left: 0;
        z-index: 1000;
        display: none;
        float: left;
        min-width: 210px;
        padding: 5px 0;
        margin: 2px 0 0;
        list-style: none;
        background-color: #fff;
        border: 1px solid #ccc;
        border: 1px solid rgba(0,0,0,0.2);
        -webkit-border-radius: 6px;
        -moz-border-radius: 6px;
        border-radius: 6px;
        -webkit-box-shadow: 0 5px 10px rgba(0,0,0,0.2);
        -moz-box-shadow: 0 5px 10px rgba(0,0,0,0.2);
        box-shadow: 0 5px 10px rgba(0,0,0,0.2);
        -webkit-background-clip: padding-box;
        -moz-background-clip: padding;
        background-clip: padding-box;

        padding: 10px;
        line-height: 20px;
        /*white-space: nowrap;*/
        top: 0 !important;
        left: 0 !important;
    }
    .datetimepicker td, .datetimepicker th{
        padding: 4px 5px;
    }
    .glyphicon-arrow-left:before {
        content: "<";
    }

    .glyphicon-arrow-right:before {
        content: ">";
    }
    table {
        max-width: 100%;
        background-color: transparent;
        border-collapse: collapse;
        border-spacing: 0;
    }
    tfoot{
        display: table-row-group;
        vertical-align: middle;
        border-color: inherit;
    }
    .datetimepicker tfoot{
        /*display: block;*/
    }
    .datetimepicker tfoot tr:last-child{
        display: none;
    }
</style>
<script>

    //设置语言
    $.fn.datetimepicker.dates['zh-CN'] = {
		days: ["星期日", "星期一", "星期二", "星期三", "星期四", "星期五", "星期六", "星期日"],
		daysShort: ["周日", "周一", "周二", "周三", "周四", "周五", "周六", "周日"],
		daysMin:  ["日", "一", "二", "三", "四", "五", "六", "日"],
		months: ["一月", "二月", "三月", "四月", "五月", "六月", "七月", "八月", "九月", "十月", "十一月", "十二月"],
		monthsShort: ["一月", "二月", "三月", "四月", "五月", "六月", "七月", "八月", "九月", "十月", "十一月", "十二月"],
		today: "今天",
		suffix: [],
		meridiem: ["上午", "下午"]
	};

    //生成日期选择器
    $(".date-picker").attr("readonly",true).each(function(){
        //主程序
        $(this).datetimepicker({
            format: $(this).data("format") || "yyyy/mm/dd hh:ii",
            autoclose: true,//选择一个日期之后是否立即关闭此日期时间选择器。
            language: "zh-CN",
            minView: $(this).data("minview") || 0,
            todayBtn: true,//今天按钮
            startDate: "2016/01/01",
            todayHighlight: true, //高亮当前日期。
            keyboardNavigation:true //是否允许通过方向键改变日期。
//            showMeridian:true
        });

        //调整位置
        var picker = $(this).data().datetimepicker.picker;
        var box = $("<div></div").css({ "position":"relative"});
        box.insertAfter(this).append(picker);

        //防止事件污染
        picker.on("open",function(e){
            e.stopPropagation();
        });
    });
</script>
