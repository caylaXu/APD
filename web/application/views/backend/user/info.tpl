{extends "layouts/layout.tpl"}
{block styles}
    <link rel="stylesheet" href="{$CommonUrl}/plugins/Jcrop/css/jquery.Jcrop.min.css">
    <style>
        .panel-info {
            /*padding-bottom: 100px;*/
            margin: 20px auto 0px auto;
            width: 940px;
        }
        .panel-info .avatar {
            float: left;
            width: 62px;
            height: 62px;
        }
        .panel-info .box_shadow{
            box-shadow: 0px 0px 5px #cccccc;
            padding: 50px 20px;
            border-radius:5px;
        }
        .panel {
            border-top: none;
        }

        .form-userInfo-panel {
            margin-top: 30px;
        }
        .form-userInfo-panel dl {
            border-bottom: 1px solid #D8D8D8;
            padding-bottom: 15px;
        }
        .form-userInfo-panel #gr_info{
            text-align:left;
        }
        .form-userInfo-panel #gr_info .input_name {
            float: left;
            line-height: 60px;
            margin-left: 30px;
            font-size: 16px;
        }
        .form-userInfo-panel #gr_info .input_name input[type=text]{
            height:38px;
            line-height:38px;
        }
        .form-userInfo-panel #gr_info .btn_miaoshu {
            float: left;
            line-height: 60px;
            margin-left: 20px;
        }
        .form-userInfo-panel #email {
            text-align: left;
            padding-top: 15px;
            position: relative;
        }
        .form-userInfo-panel #email .s_email{
            font-size:16px;color:#333;
        }
        .form-userInfo-panel #email .s_email_n{
            font-size:16px;
            color:#2BA1D8;
        }
        .form-userInfo-panel #telPhone {
            text-align: left;
            padding-top: 15px;
            position: relative;
        }
        .form-userInfo-panel #telPhone .e_telPhone{
            font-size:16px;
            color:#333;
        }
        .form-userInfo-panel #telPhone .e_telPhone_n{
            font-size:16px;
            color:#2BA1D8;
        }
        .form-userInfo-panel #password {
            text-align: left;
            padding-top: 15px;
            position: relative;
        }
        .form-userInfo-panel #password .s_password{
            font-size:16px;
            color:#333;
        }

        .p_tip{
            font-size:12px;margin-top: 8px;color:#333;
        }
        .btn_update {
            position: absolute;
            width: 80px;
            height: 34px;
            right: 20px;
            top: 24px;
            background-color: #2BA1D8;
            color: #fff;
            font-size: 14px;
            outline: none;
            border:none;
        }

        .login_api{
            text-align:left;padding-top:35px;
        }
        .login_api dl{
            position:relative;
            text-align:left;
        }
        .login_api input[type=button] {
            position: absolute;
            width: 80px;
            height: 34px;
            right: 20px;
            bottom: 24px;
            background-color: #2BA1D8;
            color: #fff;
            font-size: 14px;
            outline: none;
            border:none;
        }
        .api_name {
            float: left;
            line-height: 44px;
            margin-left: 15px;
            font-size: 16px;
            font-weight: bold;
        }

        .jie_Bind {
            position: absolute;
            color: #D0021B;
            font-size: 14px;
            right: 46px;
            bottom: 32px;
        }
        .jie_Bind_None{
            color:#fff!important;
        }
        .bind_api {
            float: left;
            line-height: 44px;
            margin-left: 15px;
            font-size: 16px;
            font-weight: bold;
        }
        .bind_api p:first-child{
            font-size:16px;
            color:#808080;
        }
        .bind_api p:last-child{
            font-size:12px;
            color:#333;
        }
        dd p {
            color: #000;
            padding: 0 0 10px;
        }
        .form-panel input[type=text], .form-panel input[type=password] {
            width: 300px;
            margin-bottom: 20px;
        }
        .input-group {
            margin:15px auto;
            /*width: 300px;*/
            position: relative;
            overflow:hidden;
        }

        .input-group input {
            float: left;
            padding-right: 100px;
        }
        .input-group .input-group-addon {
            right: 0;
            position: absolute;
            height: 40px;
            line-height: 40px;
            vertical-align: middle;
            padding: 0 12px;
            display: inline-block;
            border-top-left-radius: 0;
            border-bottom-left-radius: 0;
        }
        .button-get-verify {
            color: #ffffff;
            background-color: #2BA1D8;
        }
        .button-get-verify.disable {
            background-color: #cccccc;
        }
        .crop:after{
            content: '';
            clear: both;
            display: block;
        }
        .jcrop-holder{
            float: left;
            margin-bottom: 20px;
        }
        .crop > img{
            float: left;
        }
        .preview{
            float: left;
            text-align: center;
            margin-left: 50px;
        }
        .preview-img{
            width: 60px;
            height: 60px;
            overflow: hidden;
            border-radius: 50%;
        }
        .preview-img img{
            max-width: none;
        }

        .merge{
            position: fixed;
            z-index: 100;
            top: 0;
            right: 0;
            bottom: 0;
            left: 0;
            background-color: transparent;
        }
        .merge .outer{
            width: 400px;
            height: 320px;
            border: 1px solid #E0E0E0;
            background-color: #fff;
            position: fixed;
            left: 50%;
            top: 50%;
            margin-left: -200px;
            margin-top: -160px;
            padding: 20px;
            text-align: center;
        }
        .merge .outer img{
            width: 60px;
            height: 60px;
            -webkit-border-radius: 50%;
            -moz-border-radius: 50%;
            border-radius: 50%;
            border:1px solid #aaaaaa;
        }
        .merge .outer p{
            width: 200px;
            margin: 0 auto;
            padding: 10px 0 20px;
            font-size: 16px;
        }
        .merge .outer span{
            margin-top: 8px;
            text-align: left;
            display: inline-block;
            padding: 0 18px;
            font-size: 10px;
        }

        /*弹出层*/
        .dialog_Div {
            position: fixed;
            display:none;
            top: 0px;
            left: 0px;
            right: 0px;
            bottom: 0px;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 1;
        }
        .email_box {
            width: 400px;
            min-height: 250px;
            margin: -300px auto 0px auto;
            background-color: #fff;
            border-radius: 5px;
            box-shadow: 0px 0px 5px #666;
            padding: 20px;
        }
        .email_box .dl1{
            text-align:left;
            font-size:16px;
            color:#333;
        }
        .mobile_box{
            width: 400px;
            min-height: 250px;
            margin: -300px auto 0px auto;
            background-color: #fff;
            border-radius: 5px;
            box-shadow: 0px 0px 5px #666;
            padding: 20px;
        }
        .mobile_box .dl1{
            text-align:left;
            font-size:16px;
            color:#333;
        }
        .password_box{
            width: 400px;
            min-height: 270px;
            margin: -300px auto 0px auto;
            background-color: #fff;
            border-radius: 5px;
            box-shadow: 0px 0px 5px #666;
            padding: 20px;
        }
        .password_box .dl1{
            text-align:left;
            font-size:16px;
            color:#333;
        }

        .unBind_box{
            width: 400px;
            min-height: 180px;
            margin: -300px auto 0px auto;
            background-color: #fff;
            border-radius: 5px;
            box-shadow: 0px 0px 5px #666;
            padding: 36px 50px;
        }
        .unBind_box input[type=button]{
            width: 125px;
            height: 34px;
            background-color: #2BA1D8;
            color: #fff;
            font-size: 14px;
            outline: none;
        }
        .unBind_box .dl_title {
            font-size: 16px;
            color: #333;
            letter-spacing: 1px;
            line-height: 25px;
        }
        .unBind_box .jb_bind{
            float:left;
        }
        .unBind_box .cancel{
            float:right;
            background-color:#fff!important;
            border:1px solid #ccc!important;
            color:#666!important;
        }
        .tip_error{
            float: left;
            line-height: 35px;
            font-size:12px;
            color:red;
        }
    </style>
{/block}
{block menuTitle}<a href="/backend/user/update">个人资料</a>{/block}
{block content }
    <div class="panel panel-blue panel-info">
        <div class="box_shadow">
            <h2>个人资料</h2>
            <div id='UserInfo' class="form-userInfo-panel">
                <dl id="gr_info">
                    <div id="changeAvatar" style="position:relative;">
                        <div class="avatar">
                            <img src="{$Info.Avatar}" alt="头像" tip_Title="点击上传头像" class="demo demoDown">
                            <input type="text" hidden="hidden" class="form-control" name="Avatar" value="{$Info.AvatarName}">
                        </div>
                        <label class="demo demoDown" tip_Title="点击上传头像" style="cursor: pointer;position: absolute;left: 0px;display: inline-block;height: 62px;width: 62px;border-radius: 50%;">
                            <div style="display: none;">
                                <input type="file" name="Img" accept="image/*" autocomplete="off">
                            </div>
                        </label>
                    </div>
                    <div class="input_name input_txt" style="font-weight:bold;">{$Info.UserName}</div>
                    <div class="input_name input_div hide">
                        <input id="user_name" type="text" placeholder="{$Info.UserName}" value="{$Info.UserName}">
                    </div>
                    <div class="btn_miaoshu">
                        <i id="bj_Name" class="iconfont icon-miaoshu demo demoDown" tip_Title="点击编辑" style="cursor:pointer;"></i>
                    </div>
                </dl>
                <dl id="email">
                    <div>
                        <p><span class="s_email">邮箱：</span><span class="s_email_n">{$Info.Email}</span></p>
                        <p class="p_tip">安全邮箱可用于登录APD，重置密码或其它安全验证</p>
                        <input id="btn_email" class="btn_update" type="button" value="{if $Info.Email!="无"}修改{else}绑定{/if}">
                    </div>
                </dl>
                <dl id="telPhone">
                    <div>
                        <p><span class="e_telPhone">手机：</span><span class="e_telPhone_n">{$Info.Mobile}</span></p>
                        <p class="p_tip">安全手机可用于登录APD，重置密码或其它安全验证</p>
                        <input id="btn_telPhone" class="btn_update" type="button" value="{if $Info.Mobile!="无"}修改{else}绑定{/if}">
                    </div>
                </dl>
                <dl id="password">
                    <div>
                        <p><span class="s_password">密码设置</span></p>
                        <p class="p_tip">设置密码可保护账户安全</p>
                        <input id="btn_password" class="btn_update" type="button" value="{if $Info.HasPwd==1}修改{else}设置{/if}">
                    </div>
                </dl>
                <div class="login_api">
                    <p>第三方账号</p>
                    <dl style="margin-top: 40px;">
                        <div style="float:left;">
                            <i class="iconfont icon-qq-copy" style="font-size:44px;color:#808080;{if isset($Info.qq)}color:#008DE9!important;{/if}"></i>
                        </div>
                        {if isset($Info.qq)}
                            <div class="api_name">{$Info.qq}</div>
                            {if isset($Info.wechat) || isset($Info.weibo)}
                                <a class="jie_Bind qq_unBind" href="#">解绑</a>
                            {else}
                                <a class="jie_Bind" href="#"></a>
                            {/if}
                        {else}
                            <div class="bind_api">
                                <p>QQ</p>
                                <p>绑定后可使用QQ登录您的账号</p>
                            </div>
                            <input id="btn_qq" type="button" value="绑定" onclick="javascript:window.location.href='/backend/auth/qq_login?UserId={$Info.UserId}'">
                        {/if}
                    </dl>
                    <dl style="margin-top:15px;">
                        <div style="float:left;">
                            <i class="iconfont icon-weixin" style="font-size:44px;margin-top: -3px;color:#808080;{if isset($Info.wechat)}color:#008DE9!important;{/if}"></i>
                        </div>
                        {if isset($Info.wechat)}
                            <div class="api_name">{$Info.wechat}</div>
                            {if isset($Info.qq) || isset($Info.weibo)}
                                <a class="jie_Bind wx_unBind" href="#">解绑</a>
                            {else}
                                <a class="jie_Bind" href="#"></a>
                            {/if}
                        {else}
                            <div class="bind_api">
                                <p>微信</p>
                                <p>绑定后可使用微信登录您的账号</p>
                            </div>
                            <input id="btn_wx" type="button" value="绑定" onclick="javascript:window.location.href='/backend/auth/wechat_login?UserId={$Info.UserId}'">
                        {/if}
                    </dl>
                    <dl style="margin-top:15px;">
                        <div style="float:left;">
                            <i class="iconfont icon-weibo" style="font-size:44px;color:#808080;{if isset($Info.weibo)}color:#008DE9!important;{/if}"></i>
                        </div>
                        {if isset($Info.weibo)}
                            <div class="api_name">{$Info.weibo}</div>
                            {if isset($Info.qq) || isset($Info.wechat)}
                                <a class="jie_Bind wb_unBind" href="#">解绑</a>
                            {else}
                                <a class="jie_Bind" href="#"></a>
                            {/if}
                        {else}
                            <div class="bind_api">
                                <p>微博</p>
                                <p>绑定后可使用微博登录您的账号</p>
                            </div>
                            <input id="btn_wb" type="button" value="绑定" onclick="javascript:window.location.href='/backend/auth/weibo_login?UserId={$Info.UserId}'">
                        {/if}
                    </dl>
                </div>
            </div>

            {*Email修改弹出框*}
            <div class="dialog_Div email_Dialog">
                <div class="email_box">
                    <dl class="dl1"><span>当前绑定的邮箱为：{$Info.Email}</span></dl>
                    <div class="input-group">
                        <input type='text' name="Email" placeholder="新邮箱" style="height:40px;font-size:14px;">
                        <button type="button" class="input-group-addon button-get-verify btn getVerify">获取验证码</button>
                    </div>
                    <div class="input-group">
                        <input type='text' value="" name="AuthCode" placeholder="验证码" style="height:40px;font-size:14px;">
                    </div>
                    <div class="input-group" style="text-align:right;margin-top:30px;">
                        <span class="tip_error"></span>
                        <button type='submit' class='btn' style="background-color:#2BA1D8;">确定</button>
                        <button type='button' class='btn btn-gray btn-reverse'>取消</button>
                    </div>
                </div>
            </div>

            {*手机号修改弹出框*}
            <div class="dialog_Div mobile_Dialog">
                <div class="mobile_box">
                    <dl class="dl1">
                        {if $Info.Mobile!=null && $Info.Mobile!="无"}
                            <span>当前绑定的手机为：{$Info.Mobile}</span>
                        {else}
                            <span style="height:30px;">&nbsp;</span>
                        {/if}
                    </dl>
                    <div class="input-group">
                        <input type='text' name="Mobile" placeholder="新手机号码" style="height:40px;font-size:14px;">
                        <button type="button" class="input-group-addon button-get-verify btn getVerify">获取验证码</button>
                    </div>
                    <div class="input-group">
                        <input type='text' value="" name="AuthCode" placeholder="验证码" style="height:40px;font-size:14px;">
                    </div>
                    <div class="input-group" style="text-align:right;margin-top:30px;">
                        <span class="tip_error"></span>
                        <button type='submit' class='btn' style="background-color:#2BA1D8;">确定</button>
                        <button type='button' class='btn btn-gray btn-reverse'>取消</button>
                    </div>
                </div>
            </div>

            {*密码弹出框*}
            <div class="dialog_Div password_Dialog">
                <div class="password_box">
                    {if $Info.HasPwd==1}
                        <div class="input-group">
                            <input type='text' name="OldPassword" placeholder="原始密码" style="height:40px;font-size:14px;">
                        </div>
                    {/if}
                    <div class="input-group">
                        <input type="password" name="Password" placeholder="{if $Info.HasPwd==1}新密码{else}密码{/if}" style="height:40px;font-size:14px;">
                    </div>
                    <div class="input-group">
                        <input type="password" name="RePassword" placeholder="再次输入{if $Info.HasPwd==1}新密码{else}密码{/if}" style="height:40px;font-size:14px;">
                    </div>
                    <div class="input-group" style="text-align:right;margin-top:30px;">
                        <span class="tip_error"></span>
                        <button type='submit' class='btn' style="background-color:#2BA1D8" haspwd="{$Info.HasPwd}">确定</button>
                        <button type='button' class='btn btn-gray btn-reverse'>取消</button>
                    </div>
                </div>
            </div>

            {*解绑弹出框*}
            <div class="dialog_Div unBind_Dialog">
                <div class="unBind_box">
                    <dl class="dl_title">解绑后您将不能通过该方式登录当前帐号，是否继续解绑？</dl>
                    <dl style="margin-top:20px;">
                        <input class="jb_bind" type="button" value="解绑" unBind="">
                        <input class="cancel" type="button" value="取消">
                    </dl>
                </div>
            </div>
        </div>
    </div>
{/block}
{block bg}
    <div class="background" style="display: none;"></div>
{/block}

{block scripts}
    <script src="{$CommonUrl}/plugins/Jcrop/js/jquery.Jcrop.min.js"></script>
{literal}
    <script type="text/template" id="edit_avatar">
        <div class="crop">
            <img src="{{imgsrc}}" alt="" id="cropImg">
            <div class="preview">
                <div class="preview-img">
                    <img src="{{imgsrc}}" alt="" id="preview">
                </div>
                <span>头像预览区</span>
            </div>

        </div>
        <div>
            <label class="btn btn-gray btn-reverse" style="width:120px;">重新选择<div class="remove"><input type="file" name="Img"></div></label>
            <button type='button' class='btn btn-blue' id="btnCutImg" style="width:120px;">确定</button>
            <button type='button' class='btn btn-gray btn-reverse cancel' style="width:120px;" onclick="javascript:$.reload(1);">取消</button>
        </div>
    </script>
    <script type="text/template" id="merge-tpl">
        <div class="merge hide">
            <div class="outer">
                <p>该{{Title}}已被以下账号绑定, 是否合并账号?</p>
                <img src="{{imgSrc}}" alt="">
                <p>{{name}}</p>
                <div class="buttons">
                    <button class="btn btn-large btn-blue" id="btnMerge">合并账号</button>
                    <button class="btn btn-large btn-gray btn-reverse" id="btnNoMerge">不合并</button>
                </div>
                <span>选择不合并则原来的账号以后不可登录，并且以后不可再进行合并。</span>
            </div>
        </div>
    </script>
{/literal}
    <script type="text/javascript">
        {literal}
        //扩展tip提示
        $(".demoDown").tip_Title();
        //上传头像
        Unreal.upload({
            url: "/backend/user/upload_avatar",
            callback: function (data) {
                var target = $(this);
                if (data.Result == 0) {
                    var container = target.parents("label").siblings(".avatar");
                    container.find("img").attr("src", data.Data.ImgSrc);
                    container.find("input").val(data.Data.ImgId);

                    var tplData = {
                        action: "",
                        imgsrc: data.Data.ImgSrc
                    };

                    var html = template("edit_avatar", tplData);
                    $("#changeAvatar").html(html);

                    var jcrop_api,
                            boundx,
                            boundy,

                            $pcnt = $('.preview-img'),
                            $pimg = $('#preview'),
                            xsize = $pcnt.width(),
                            ysize = $pcnt.height();

                    function updatePreview(c){
                        if (parseInt(c.w) > 0) {
                            var rx = xsize / c.w;
                            var ry = ysize / c.h;
                            cutImg = c;
                            $pimg.css({
                                width: Math.round(rx * boundx) + 'px',
                                height: Math.round(ry * boundy) + 'px',
                                marginLeft: '-' + Math.round(rx * c.x) + 'px',
                                marginTop: '-' + Math.round(ry * c.y) + 'px'
                            });
                        }
                    }
                    $('#cropImg').Jcrop({
                        onChange: updatePreview,
                        onSelect: updatePreview,
                        aspectRatio: xsize / ysize,
                        boxHeight: 200,
                        setSelect: [0,0,60,60]
                    },function(){
                        var bounds = this.getBounds();
                        boundx = bounds[0];
                        boundy = bounds[1];
                        jcrop_api = this;
                    });

                    var cutImg = null;

                    $("#btnCutImg").on("click", function(){
//                        console.log(cutImg);
                        var cutData = {
                            Image: data.Data.ImgId,
                            X: cutImg.x,
                            Y: cutImg.y,
                            W: cutImg.w,
                            H: cutImg.h
                        };
//                        console.log(cutData);
                        $.post("/backend/user/cut_avatar",cutData, function(res){
                            if(res.Result == 0){
                                alert(res.Msg);
                                $.reload(1.5);
                            }
                        });
                    });
                }
                else {
                    alert(data.Msg);
                    target.prop("disabled",false);
                }
            }
        });

        //修改用户名
        $("#bj_Name").on("click",function(){
            $(".input_txt").hide();
            var len=$(".input_div").show().find("input[type=text]").attr("value").length;
            $(".input_div").show().find("input[type=text]").selectRange(len,len);
        });
        $("#user_name").keypress(function(e){
            e = e || window.event;
            // 回车键事件
            if(e.which == 13) {
                if ($.trim($(this).val()).length > 0) {
                    var userName=$.trim($(this).val());
                    $.post("/backend/user/update",{
                        Name:userName
                    }).done(function(json){
                        if(json.Result==0){
                            alert(json.Msg);
                            $.reload(1);
                        }else{
                            alert(json.Msg);
                        }
                    });
                }else{
                    $(".input_txt").show();
                    $(".input_div").hide();
                }
            }
        });
        $("#user_name").on("blur",function(){
            if ($.trim($(this).val()).length > 0) {
                var userName=$.trim($(this).val());
                $.post("/backend/user/update",{
                    Name:userName
                }).done(function(json){
                    if(json.Result==0){
                        alert(json.Msg);
                        $.reload(1);
                    }else{
                        alert(json.Msg);
                    }
                });
            }else{
                $(".input_txt").show();
                $(".input_div").hide();
            }
        });

        //修改邮箱
        $("#btn_email").on("click",function(){
            $("div.email_Dialog").show();
            $(".email_box").stop().show().animate({margin:"150px auto 0px auto;"},300);
        });
        $(".email_box button[type=button]").eq(1).on("click",function(){
            $(".email_box").stop().animate({margin:"-300px auto 0px auto;"},300,"",function(){
                $(".email_box").hide();
                $("div.email_Dialog").hide();
            });
        });
        $(".email_box button[type=submit]").on("click",function(){
            var email=$(".email_box").find("input[name=Email]").val();
            var authCode=$(".email_box").find("input[name=AuthCode]").val();
            var tip_error=$(this).parents(".input-group").find("span.tip_error");
            if(email!=""){
                if(authCode!=""){
                    $.post("/backend/user/change_email",{
                        Email:email,
                        AuthCode:authCode
                    }).done(function(json){
                        changeSuccess(json,tip_error);
                    });
                }else{
                    tip_error.text("请输入验证码!");
                }
            }else{
                tip_error.text("请输入Email!");
            }
        });

        //修改手机号
        $("#btn_telPhone").on("click",function(){
            $("div.mobile_Dialog").show();
            $(".mobile_box").stop().show().animate({margin:"150px auto 0px auto;"},300);
        });
        $(".mobile_box button[type=button]").eq(1).on("click",function(){
            $(".mobile_box").stop().animate({margin:"-300px auto 0px auto;"},300,"",function(){
                $(".mobile_box").hide();
                $("div.mobile_Dialog").hide();
            });
        });
        $(".mobile_box button[type=submit]").on("click",function(){
            var mobile=$(".mobile_box").find("input[name=Mobile]").val();
            var authCode=$(".mobile_box").find("input[name=AuthCode]").val();
            var tip_error=$(this).parents(".input-group").find("span.tip_error");
            if(mobile!=""){
                if(authCode!=""){
                    $.post("/backend/user/change_mobile",{
                        Mobile:mobile,
                        AuthCode:authCode
                    }).done(function(json){
                        changeSuccess(json,tip_error);
                    });
                }else{
                    tip_error.text("请输入验证码!");
                }
            }else{
                tip_error.text("请输入手机号码!");
            }
        });

        //修改密码
        $("#btn_password").on("click",function(){
            $("div.password_Dialog").show();
            $(".password_box").stop().show().animate({margin:"150px auto 0px auto;"},300);
        });
        $(".password_box button[type=button]").on("click",function(){
            $(".password_box").stop().animate({margin:"-300px auto 0px auto;"},300,"",function(){
                $(".password_box").hide();
                $("div.password_Dialog").hide();
            });
        });
        $(".password_box button[type=submit]").on("click",function(){
            var haspwd = $(this).attr("haspwd");
            var oldPassword = $(".password_box").find("input[name=OldPassword]").val();
            var password = $(".password_box").find("input[name=Password]").val();
            var rePassword = $(".password_box").find("input[name=RePassword]").val();
            var tip_error = $(this).parents(".input-group").find("span.tip_error");
            if (haspwd == 1) {
                if (oldPassword != "") {
                    if (password != "") {
                        if (rePassword != "") {
                            if (password == rePassword) {
                                $.post("/backend/user/update", {
                                    OldPassword: oldPassword,
                                    Password: password,
                                    RePassword: rePassword
                                }).done(function (json) {
                                    if (json.Result == 0) {
                                        Success(json,tip_error);
                                    } else {
                                        tip_error.text(json.Msg);
                                    }
                                });
                            } else {
                                tip_error.text("两次密码输入不一致!");
                            }
                        } else {
                            tip_error.text("请再次确认新密码!");
                        }
                    } else {
                        tip_error.text("请输入新密码!");
                    }
                } else {
                    tip_error.text("请输入原始密码!");
                }
            }else{
                if (password != "") {
                    if (rePassword != "") {
                        if (password == rePassword) {
                            $.post("/backend/user/update", {
                                Password: password,
                                RePassword: rePassword
                            }).done(function (json) {
                                if (json.Result == 0) {
                                    Success(json,tip_error);
                                } else {
                                    tip_error.text(json.Msg);
                                }
                            });
                        } else {
                            tip_error.text("两次密码输入不一致!");
                        }
                    } else {
                        tip_error.text("请再次确认密码!");
                    }
                } else {
                    tip_error.text("请输入密码!");
                }
            }
        });

        //修改成功
        function Success(json,tip_error) {
            $(tip_error).text(json.Msg);
            if (json.Result == 0) {
                if ($("[name=Password]").length) {
                    setTimeout(Func.LogOut, 1500);
                }
                else {
                    $.reload(1);
                }
            }
        }

        //点击遮罩层关闭弹出层
        $(".dialog_Div").on("click", function (e) {
            e = e || window.event;
            var obj = e.target || e.srcElement;
            var that = $(this);
            if ($(obj).hasClass("email_Dialog")) {
                $(this).find("div:first-child").stop().animate({margin: "-300px auto 0px auto;"}, 300, "", function () {
                    $(this).hide();
                    that.hide();
                });
            }
            if ($(obj).hasClass("mobile_Dialog")) {
                $(this).find("div:first-child").stop().animate({margin: "-300px auto 0px auto;"}, 300, "", function () {
                    $(this).hide();
                    that.hide();
                });
            }
            if ($(obj).hasClass("password_Dialog")) {
                $(this).find("div:first-child").stop().animate({margin: "-300px auto 0px auto;"}, 300, "", function () {
                    $(this).hide();
                    that.hide();
                });
            }
            if ($(obj).hasClass("unBind_Dialog")) {
                $(this).find("div:first-child").stop().animate({margin: "-300px auto 0px auto;"}, 300, "", function () {
                    $(this).hide();
                    that.hide();
                });
            }
        });

        //第三方解除绑定
        $("a.jie_Bind").on("click",function(e){
            e = e || window.event;
            var obj = e.target || e.srcElement;
            if($(obj).hasClass("qq_unBind")){
                $("div.unBind_Dialog").show();
                $(".unBind_box").stop().show().animate({margin:"150px auto 0px auto;"},300);
                $(".unBind_box").find(".jb_bind").attr("unBind","qq");
            }
            if($(obj).hasClass("wx_unBind")){
                $("div.unBind_Dialog").show();
                $(".unBind_box").stop().show().animate({margin:"150px auto 0px auto;"},300);
                $(".unBind_box").find(".jb_bind").attr("unBind","wechat");
            }
            if($(obj).hasClass("wb_unBind")){
                $("div.unBind_Dialog").show();
                $(".unBind_box").stop().show().animate({margin:"150px auto 0px auto;"},300);
                $(".unBind_box").find(".jb_bind").attr("unBind","weibo");
            }
        });
        $(".unBind_box").find(".jb_bind").on("click",function(){
            unBind($(this).attr("unBind"));
        });
        $(".unBind_box").find(".cancel").on("click",function(){
            $(".unBind_box").stop().animate({margin:"-300px auto 0px auto;"},300,"",function(){
                $(".unBind_box").hide();
                $("div.unBind_Dialog").hide();
            });
        });

        function unBind(type){
            $.post("/backend/user/unbind",{
                Type:type
            }).done(function(json){
                if(json.Result==0){
                    alert(json.Msg);
                    $.reload(1);
                }else{
                    alert(json.Msg);
                }
            });
        }

        //获取验证码
        $(function () {
            $(document).on("click", ".getVerify", function(){
                var $MobileOrEmail = $(this).parent().find("input");
                var tip_error=$(this).parents(".input-group").siblings().last().find("span.tip_error");
                if($MobileOrEmail.val()!=""){
                    $(this).addClass("disable");
                    $(this).prop("disabled", true);
                    var _this = $(this);
                    var time = 60;
                    _this.text("剩余" + (time) + "秒");
                    var url = "/backend/login/get_validate_code?"+$MobileOrEmail.attr("name")+"=" + $MobileOrEmail.val();
                    _this.text("正在发送...");
                    $.get(url).done(function(data){
                        if(data.Result==0){
                            var timer = setInterval(function () {
                                _this.text("重新发送(" + (time--) + ")");
                                if (time < 0) {
                                    _this.removeClass("disable");
                                    _this.prop("disabled", false);
                                    _this.text("重新发送");
                                    clearInterval(timer);
                                }
                            }, 1000);
                        }
                        else{
                            _this.removeClass("disable");
                            _this.prop("disabled", false);
                            _this.text("获取验证码");
                        }
                    }).fail(function(){
                        tip_error.text("请求失败");
                        _this.removeClass("disable");
                        _this.prop("disabled", false);
                        _this.text("获取验证码");
                    });
                }else{
                    if ($MobileOrEmail.attr("name") == "Email") {
                        tip_error.text("邮箱不能为空!");
                    }
                    if ($MobileOrEmail.attr("name") == "Mobile") {
                        tip_error.text("手机号不能为空!");
                    }
                }
            });
        });

        //合并账号
        function changeSuccess(json,tip_error) {
            var data = json.Data;
            var tip_error = $(tip_error);
            if (json.Result == 0) {
                //邮箱修改合并用户
                if(data.Result == 10011){
                    var mergeData = {
                        Title: '邮箱',
                        imgSrc: data.Avatar,
                        name: data.UserName
                    };
                    var html = template("merge-tpl", mergeData);
                    $("body").append(html);
                    $(".merge").stop().fadeIn(200);
                    $("body").off("click.merge.hide").on("click.merge.hide",function(e){
                        e = e || window.event;
                        var obj = e.target || e.srcElement;
                        var target = $(obj);
                        if(target.isIn(".merge .outer")){
                            return;
                        }
                        else{
                            $(".merge").stop().fadeOut(200);
                            $("body").off("click.merge.hide");
                            $(".merge").remove();
                        }
                    });

                    $("#btnMerge").on("click", function(){
                        var postData = {
                            CombinedId: data.UserId,
                            IsCombined: 1,
                            Email: data.Email
                        };
                        $.post("/backend/user/combined_user", postData, function(res){
                            tip_error.text(res.Msg);
                            $.reload(1.5);
                        });
                    });

                    $("#btnNoMerge").on("click", function(){
                        var postData = {
                            CombinedId: data.UserId,
                            IsCombined: 0,
                            Email: data.Email
                        };
                        $.post("/backend/user/combined_user", postData, function(res){
                            if(res.Result == 0){
                                tip_error.text(res.Msg);
                                $.reload(1.5);
                            }
                        });
                    });

                }
                //手机修改合并用户
                else if(data.Result == 10010){
                    var mergeData = {
                        Title: '手机号',
                        imgSrc: data.Avatar,
                        name: data.UserName
                    };
                    var html = template("merge-tpl", mergeData);
                    $("body").append(html);
                    $(".merge").stop().fadeIn(200);
                    $("body").off("click.merge.hide").on("click.merge.hide",function(e){
                        e = e || window.event;
                        var obj = e.target || e.srcElement;
                        var target = $(obj);
                        if(target.isIn(".merge .outer")){
                            return;
                        }
                        else{
                            $(".merge").stop().fadeOut(200);
                            $("body").off("click.merge.hide");
                            $(".merge").remove();
                        }
                    });

                    $("#btnMerge").on("click", function(){
                        var postData = {
                            CombinedId: data.UserId,
                            IsCombined: 1,
                            Mobile: data.Mobile
                        };
                        $.post("/backend/user/combined_user", postData, function(res){
                            tip_error.text(res.Msg);
                            $.reload(1.5);
                        });
                    });

                    $("#btnNoMerge").on("click", function(){
                        var postData = {
                            CombinedId: data.UserId,
                            IsCombined: 0,
                            Mobile: data.Mobile
                        };
                        $.post("/backend/user/combined_user", postData, function(res){
                            if(res.Result == 0){
                                tip_error.text(res.Msg);
                                $.reload(1.5);
                            }
                        });
                    });
                }
                //返回修改成功
                else{
                    tip_error.text(json.Msg);
                    $.reload(1);
                }
            }else{
                tip_error.text(json.Msg);
            }
        }

        {/literal}
    </script>
{/block}
{block setting}
    <script type="text/javascript">
        DataBase.Info = {$Info|@json_encode};
    </script>
{/block}