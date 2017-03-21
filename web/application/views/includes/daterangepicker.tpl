<!-- DatePicker -->
<link rel="stylesheet" href="{$CommonUrl}/public/adminlte/plugins/daterangepicker/daterangepicker.css">
<script src="{$CommonUrl}/public/adminlte/plugins/daterangepicker/daterangepicker.js"></script>

<script>

    //屏蔽change
    $(".date-range-picker").on("change.prevent",function(e){
        e.stopPropagation();
    });

    $(".date-range-picker").daterangepicker({
        timePicker: false, //不显示下拉选择时间
        timePicker24Hour: true,
        // timePickerIncrement: 10,
        opens: "left",
        "applyClass": "btn-blue",
        "locale": {
            "format": 'YYYY/MM/DD HH:mm',
            "applyLabel": "确定",
            "cancelLabel": "取消",
            "fromLabel": "From",
            "toLabel": "To",
            "customRangeLabel": "Custom",
            "daysOfWeek": [
                "日",
                "一",
                "二",
                "三",
                "四",
                "五",
                "六"
            ],
            "monthNames": [
                "一月",
                "二月",
                "三月",
                "四月",
                "五月",
                "六月",
                "七月",
                "八月",
                "九月",
                "十月",
                "十一月",
                "十二月"
            ],
            "firstDay": 1
        }
    });
    //
    // $(".date-picker").attr("readonly",true).each(function(){
    //     $(this).daterangepicker({
    //         timePicker: true,
    //         timePicker24Hour: true,
    //         // timePickerIncrement: 10,
    //         singleDatePicker:true,
    //         parentEl: $(this).parents(".content"),
    //         "applyClass": "btn-blue",
    //         "locale": {
    //             "format": 'YYYY/MM/DD HH:mm:ss',
    //             "applyLabel": "确定",
    //             "cancelLabel": "取消",
    //             "fromLabel": "From",
    //             "toLabel": "To",
    //             "customRangeLabel": "Custom",
    //             "daysOfWeek": [
    //                 "日",
    //                 "一",
    //                 "二",
    //                 "三",
    //                 "四",
    //                 "五",
    //                 "六"
    //             ],
    //             "monthNames": [
    //                 "一月",
    //                 "二月",
    //                 "三月",
    //                 "四月",
    //                 "五月",
    //                 "六月",
    //                 "七月",
    //                 "八月",
    //                 "九月",
    //                 "十月",
    //                 "十一月",
    //                 "十二月"
    //             ],
    //             "firstDay": 1
    //         }
    //     });
    // });
    //取消屏蔽
    $(".date-range-picker").off("change.prevent");
</script>
