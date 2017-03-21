{literal}

<!-- 提示弹窗 -->
<script type="text/template" id="message-tpl">
    <div class="popover message-popover instant">
        <div class="inner">
            <div class="title">提示信息</div>
            <div class="content">
                {{Title}}
            </div>
        </div>
    </div>
</script>

<!-- 确认弹窗<div class="title">确认信息</div>-->
<script type="text/template" id="confirm-tpl">
    <div class="dialog confirm-dialog instant">
        <div class="outer" data-allow-close="true">
            <div class="inner">
                <div class="content" valHtml="{{Title}}">
                    删除后不可找回，确认删除？
                </div>
                <div class="button-row">
                    <button class="btn" data-dismiss="dialog">取消</button>
                    <button class="btn btn-blue confirm-btn">确定</button>
                </div>
            </div>
        </div>
    </div>
</script>

<script type="text/javascript">

    $.message = function(str,type){
        type = type || "info";
        var popover = $(template("message-tpl",{Title:str}));
        popover.appendTo("body").open();
        setTimeout(function(){
            popover.close();
        },1500);
    }
    window.alert = $.message;

    //  $.confirm("确定吗？").then(callback);
    $.confirm = function(str,tpl){

        tpl = tpl || "confirm-tpl";
        tpl = tpl.replace("#",""); 
        var dialog = $(template(tpl,{Title:str,button:true}));
        // dialog.close = function(){
        //     this.modal("hide");
        // }
        dialog.callback = function(){
            this.close();
        }
        dialog.then = function(callback){
            this.callback = callback;
        }
        dialog.on("click",".confirm-btn",function(){
            dialog.callback.call(dialog);
        });
        dialog.appendTo("body").open();
        dialog.find(".confirm-btn").focus();
        return dialog;
    }

</script>
{/literal}
