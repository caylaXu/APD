{extends "layouts/layout-basic.tpl"}
{block content }
    <div class="background">
        <img src="{$FileUrl}/img/bg.jpg" alt="" class="img-fill"/>
    </div>
    <div class="login-page">
        <div class="login-box">
            <div class="login-logo" style="margin-bottom: 75px;">
                <img src="{$FileUrl}/img/logo.png" alt=""/>
                <p style="font-size: 18px;margin: 10px 0px">加入【官网交易链】项目</p>
            </div>
            <!-- /.login-logo -->
            <div class="login-box-body">
                {*手机注册和邮箱注册切换*}
                <div class="login-box-msg">
                    {if $Source == "mobile"}
                        <a href="" class="active">手机号注册</a>
                    {elseif $Source == "email"}
                        <a href="" class="active">邮箱注册</a>
                    {else}
                        <a href="" class="active">手机号注册</a>
                        |
                        <a href="">邮箱注册</a>
                    {/if}
                </div>
                {*手机号注册*}
                <form action="/backend/login/register_by_mobile"
                      method="post" class="ajax-form {if $Source == 'mobile'}active{elseif $Source=="email"}{else}active{/if}"
                      data-success="RegistSuccessMobile" autocomplete="off">
                    <div class="form-group has-feedback">
                        <input type="text" class="form-control" name="Mobile" placeholder="手机号" id="Mobile">
                        <span class="glyphicon glyphicon-user form-control-feedback"></span>
                    </div>
                    <div class="form-group has-feedback">
                        <input type="text" class="form-control" name="Name" placeholder="昵称">
                        <span class="glyphicon glyphicon-user form-control-feedback"></span>
                    </div>
                    <div class="form-group has-feedback">
                        <div class="input-group">
                            <input type="password" class="form-control" name="Password" placeholder="密码" id="Password">
                            <span class="input-group-addon icon icon-password lookpwd"></span>
                        </div>
                        <span class="glyphicon glyphicon-lock form-control-feedback"></span>
                    </div>
                    <div class="form-group has-feedback">
                        <div class="input-group">
                            <input type="text" class="form-control"  name="AuthCode" placeholder="验证码">
                            <button type="button" class="input-group-addon button-get-verify btn" id="getVerifyMobile">获取验证码
                            </button>
                        </div>
                        <span class="glyphicon glyphicon-phone form-control-feedback"></span>
                    </div>
                    <input type="hidden" name="Title" value="">
                    <div class="button-row">
                        <button type="submit">注册，加入项目</button>
                    </div>
                    <p>
                        <button class="btn_login" type="button" onclick="javascript:location.href='/backend/login/addProject'">已有账号？登录</button>
                    </p>
                </form>
                {*邮箱注册*}
                <form action="/backend/login/register_by_email" method="post" class="ajax-form {if $Source == 'mobile'}{elseif $Source=="email"}active{else}{/if}"
                      data-success="RegistSuccessEmail" autocomplete="off">
                    <div class="form-group has-feedback">
                        <input type="text" class="form-control" name="Email" placeholder="邮箱" id="Email">
                        <span class="glyphicon glyphicon-user form-control-feedback"></span>
                    </div>
                      <div class="form-group has-feedback">
                          <input type="text" class="form-control" name="Name" placeholder="昵称">
                          <span class="glyphicon glyphicon-user form-control-feedback"></span>
                      </div>

                    <div class="form-group has-feedback">
                        <div class="input-group">
                            <input type="password" class="form-control" name="Password" placeholder="密码"
                                   id="EmailPassword">
                            <span class="input-group-addon icon icon-password lookpwd"></span>
                        </div>
                        <span class="glyphicon glyphicon-lock form-control-feedback"></span>
                    </div>
                    <div class="form-group has-feedback">
                        <div class="input-group">
                            <input type="text" class="form-control" name="AuthCode" placeholder="验证码">
                            <button type="button" class="input-group-addon button-get-verify btn" id="getVerifyEmail">获取验证码
                            </button>
                        </div>
                        <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
                    </div>

                    <input type="hidden" name="Title" value="">
                    <div class="button-row">
                        <button type="submit">注册，加入项目</button>
                    </div>
                    <p>
                        <button class="btn_login" type="button" onclick="javascript:location.href='/backend/login/addProject'">已有账号？登录</button>
                    </p>
                </form>
                {*显示项目中部分成员*}
                <p class="p_hr"></p>
                <p class="p_title">项目经理</p>
                <div class="user_box">
                    <div>
                        <img src="../../../../resource/asset/img/avatar.jpg" alt="">
                        <p>Caylaxu</p>
                        <p>542***@qq.com</p>
                    </div>
                </div>
                <div class="message_box animated">
                    <div class="message_content"></div>
                </div>
            </div>
            <!-- /.login-box-body -->
        </div>
        <!-- /.login-box -->
    </div>
{/block}


{block styles}
    <style media="screen">
        body {
            background: #4070ae;
            font-family: Arial, "Microsoft Yahei", sans-serif;
            font-size: 14px;
            color: white;
        }

        #layout {
            background: none;
        }

        .background {
            position: fixed;
            z-index: -1;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
        }

        [type=email], [type=password], [type=text], [type=submit] {
            width: 100%;
            border-radius: 2px;
            font: inherit;
            -moz-box-sizing: border-box;
            box-sizing: border-box;
        }

        [type=email], [type=password], [type=text] {
            background: white;
            height: 42px;
            line-height: 22px;
            line-height: 42px\9;
            border: 1px solid #dddddd;
            padding: 0 0.5em;
        }

        [type=submit] {
            background: #1c97ff;
            height: 40px;
            color: white;
            border: none;
        }

        .login-page {
            text-align: center;
            margin: 120px auto 0px;
            width: 280px;
        }

        .form-group {
            color: black;
            height: 54px;
        }

        .input-group {
            position: relative;
        }

        .input-group input[name="Mobile"], .input-group input[name="Email"],input[name="AuthCode"] {
            float: left;
            padding-right: 100px;
        }

        .input-group .input-group-addon {
            right: 0;
            position: absolute;
            height: 42px;
            line-height: 42px;
            vertical-align: middle;
            padding: 0 12px;
            display: inline-block;
            border-top-left-radius: 0;
            border-bottom-left-radius: 0;
        }

        .button-get-verify {
            color: #ffffff;
            background-color: #1c97ff;
        }

        .button-get-verify.disable {
            background-color: #cccccc;
        }

        .white {
            color: white;
        }

        .button-row {
            margin-top: 20px;
            margin-bottom: 16px;
            padding: 0;
        }

        a:hover {
            text-decoration: underline;
        }

        .login-logo {
            margin-bottom: 50px;
        }

        .login-box-msg {
            margin: -40px 0 0;
            text-align: center;
        }

        .login-box-msg a {
            padding: 10px;
            color: #DDDDDD;
        }

        .login-box-msg a.active, .login-box-msg a:hover {
            color: #ffffff;
            text-decoration: underline;
        }

        .login-box-body form {
            display: none;
        }

        .login-box-body form.active {
            display: block;
        }
        .btn{
            border-radius: 2px;
        }
        .btn_login{
            width: 100%;
            height: 40px;
            border: 1px solid #fff;
            outline: none;
            border-radius: 2px;
            background-color: rgba(0,0,0,0);
            background-color: #fff\9;
            filter: alpha(opacity=0);
        }
        div.account_login{
            position: relative;
            overflow:hidden;
        }
        .account_login .img_box {
            text-align: left;
            padding: 25px;
            background: rgba(0, 0, 0, 0.3);
            border-radius: 5px;
            font-size: 14px;
        }
        .account_login .img_box img {
            border-radius: 100%;
            width: 50px;
            height: 50px;
        }
        .account_login .img_box p{
            margin-left: 60px;
            height: 25px;
        }
        .p_hr {
            border-top: 1px solid #fff;
            margin-top: 30px;
        }
        .p_title {
            font-size: 16px;
            color: #fff;
            margin: 22px 0px;
            text-align: left;
        }
        .user_box{
            position: relative;
        }
        .user_box div{
            text-align:left;
            font-size: 14px;
        }
        .user_box div img {
            position: absolute;
            border-radius: 100%;
            width: 40px;
            height: 40px;
        }
        .user_box div p{
            margin-left: 50px;
        }
        div.message_box{
            display: none;
            color: #fff;
            background-color:rgba(0,0,0,0.5);
            border-radius: 5px;
            position: fixed;
            width: 280px;
            top: 256px;
            filter:alpha(opacity=50);
            -moz-opacity:0.5;
            -khtml-opacity: 0.5;
            opacity: 0.5;
        }
        .animated {
            /*动画完成一个周期所需的时间*/
            -webkit-animation-duration: 0.5s;
            animation-duration: 0.5s;
            -webkit-animation-fill-mode: both;
            animation-fill-mode: both;
        }
        /*动画次数*/
        .animated.infinite {
            -webkit-animation-iteration-count: 1;
            animation-iteration-count: 1;
        }
        .animated.hinge {
            -webkit-animation-duration: 0.5s;
            animation-duration: 0.5s;
        }
        .animated.flipOutX,
        .animated.flipOutY,
        .animated.bounceIn,
        .animated.bounceOut {
            -webkit-animation-duration: .75s;
            animation-duration: .75s;
        }
        @-webkit-keyframes fadeInDown {
            from {
                opacity: 0;
                -webkit-transform: translate3d(0, -100%, 0);
                transform: translate3d(0, -100%, 0);
            }

            to {
                opacity: 1;
                -webkit-transform: none;
                transform: none;
            }
        }
        @keyframes fadeInDown {
            from {
                opacity: 0;
                -webkit-transform: translate3d(0, -100%, 0);
                transform: translate3d(0, -100%, 0);
            }

            to {
                opacity: 1;
                -webkit-transform: none;
                transform: none;
            }
        }
        .fadeInDown {
            -webkit-animation-name: fadeInDown;
            animation-name: fadeInDown;
        }

        @-webkit-keyframes fadeOutUp {
            from {
                opacity: 1;
            }

            to {
                opacity: 0;
                -webkit-transform: translate3d(0, -100%, 0);
                transform: translate3d(0, -100%, 0);
            }
        }
        @keyframes fadeOutUp {
            from {
                opacity: 1;
            }

            to {
                opacity: 0;
                -webkit-transform: translate3d(0, -100%, 0);
                transform: translate3d(0, -100%, 0);
            }
        }
        .fadeOutUp {
            -webkit-animation-name: fadeOutUp;
            animation-name: fadeOutUp;
        }
    </style>
{/block}

{block scripts}
    <script>
        {literal}
        //手机号注册方式验证参数
        $("form[data-success=RegistSuccessMobile]").find("button[type=submit]").on("click",function(){
            var mobile_tel=$(this).parents('form[data-success=RegistSuccessMobile]').find('input[name=Mobile]').val(),
                telReg = /^(((13[0-9]{1})|(14[0-9]{1})|(15[0-9]{1})|(17[0-9]{1})|(18[0-9]{1}))+\d{8})$/;
            var name=$(this).parents('form[data-success=RegistSuccessMobile]').find('input[name=Name]').val();
            var password=$(this).parents('form[data-success=RegistSuccessMobile]').find('input[name=Password]').val();
            var authCode=$(this).parents('form[data-success=RegistSuccessMobile]').find('input[name=AuthCode]').val();

            if(mobile_tel!=""){
                if(name!=""){
                    if(password!=""){
                        if(authCode!=""){
                            if(telReg.test(mobile_tel)){
                                return true;
                            }else{
                                $_animated("手机号格式有问题!");
                                return false;
                            }
                        }else{
                            $_animated("请输入验证码!");
                            return false;
                        }
                    }else{
                        $_animated("请输入密码!");
                        return false;
                    }
                }else{
                    $_animated("请输入昵称!");
                    return false;
                }
            }else{
                $_animated("请输入手机号!");
                return false;
            }
        });

        //邮箱注册方式验证参数
        $("form[data-success=RegistSuccessEmail]").find("button[type=submit]").on("click",function(){
            var emailVal=$(this).parents('form[data-success=RegistSuccessEmail]').find('input[name=Email]').val(),
                emailReg=/^([a-zA-Z0-9_-])+@([a-zA-Z0-9_-])+((\.[a-zA-Z0-9_-]{2,3}){1,2})$/;
            var name=$(this).parents('form[data-success=RegistSuccessEmail]').find('input[name=Name]').val();
            var password=$(this).parents('form[data-success=RegistSuccessEmail]').find('input[name=Password]').val();
            var authCode=$(this).parents('form[data-success=RegistSuccessEmail]').find('input[name=AuthCode]').val();
            if(emailVal!=""){
                if(name!=""){
                    if(password!=""){
                        if(authCode!=""){
                            if(emailReg.test(emailVal)){
                                return true;
                            }else{
                                $_animated("邮箱格式不正确!");
                                return false;
                            }
                        }else{
                            $_animated("请输入验证码!");
                            return false;
                        }
                    }else{
                        $_animated("请输入密码!");
                        return false;
                    }
                }else{
                    $_animated("请输入昵称!");
                    return false;
                }
            }else{
                $_animated("请输入邮箱!");
                return false;
            }
        });

        $(function () {
            //切换注册方式
            $(".login-box-msg a").on("click", function (e) {
                e.stopPropagation();
                e.preventDefault();
                $(".login-box-msg a").removeClass("active");
                $(this).addClass("active");
                $(".ajax-form").removeClass("active");
                $($(".ajax-form")[$(this).index()]).addClass("active");
            });

            //获取验证码（手机号注册）
            $("#getVerifyMobile").on("click", function () {
                var numberReg=/^\d+$/g,
                    telReg=/^(((13[0-9]{1})|(14[0-9]{1})|(15[0-9]{1})|(17[0-9]{1})|(18[0-9]{1}))+\d{8})$/,
                    telCode=$(this).parents('form[data-success=RegistSuccessMobile]').find('input[name=Mobile]').val();
                if(telCode!=""){
                    if(numberReg.test(telCode)){
                        if(telReg.test(telCode)){
                            $(this).addClass("disable");
                            $(this).prop("disabled", true);
                            var _this = $(this);
                            var time = 60;
                            _this.text("剩余" + (time) + "秒");

                            var $Mobile = $("#Mobile");
                            var url = "/backend/login/get_validate_code?Mobile=" + $Mobile.val();
                            console.log(url);
                            $.get(url, null, function (data) {

                            });
                            var timer = setInterval(function () {
                                _this.text("剩余" + (time--) + "秒");
                                if (time < 0) {
                                    _this.removeClass("disable");
                                    _this.prop("disabled", false);
                                    _this.text("重新发送");
                                    clearInterval(timer);
                                }
                            }, 1000);
                            return true;
                        }else{
                            $_animated("手机号格式不正确!");
                            return false;
                        }
                    }else{
                        $_animated("手机号格式不正确!");
                        return false;
                    }
                }else{
                    $_animated("手机号不能为空!");
                    return false;
                }
            });

            //获取验证码（邮箱注册）
            $("#getVerifyEmail").on("click", function () {
                var emailVal=$(this).parents('form[data-success=RegistSuccessEmail]').find('input[name=Email]').val(),
                        emailReg=/^([a-zA-Z0-9_-])+@([a-zA-Z0-9_-])+((\.[a-zA-Z0-9_-]{2,3}){1,2})$/;
                if(emailVal!=""){
                    if(emailReg.test(emailVal)){
                        $(this).addClass("disable");
                        $(this).prop("disabled", true);
                        var _this = $(this);
                        var time = 60;
                        _this.text("剩余" + (time) + "秒");

                        var $Email = $("#Email");
                        var url = "/backend/login/get_validate_code?Email=" + $Email.val();
                        console.log(url);
                        $.get(url, null, function (data) {

                        });
                        var timer = setInterval(function () {
                            _this.text("剩余" + (time--) + "秒");
                            if (time < 0) {
                                _this.removeClass("disable");
                                _this.prop("disabled", false);
                                _this.text("重新发送");
                                clearInterval(timer);
                            }
                        }, 1000);
                        return true;
                    }else{
                        $_animated("邮箱格式不正确!");
                        return false;
                    }
                }else{
                    $_animated("请输入邮箱!");
                    return false;
                }
            });

            //查看密码
            $(".lookpwd").on("click", function () {
                $(this).toggleClass("seeable");
                var pwd = $("input[name='Password']");
                if (pwd.attr("type") == "text") {
                    pwd.attr("type", "password");
                } else {
                    pwd.attr("type", "text");
                }
            });
        });

        //手机号注册成功（回调）
        function RegistSuccessMobile(json) {
            if (json.Result == 0) {
                alert("注册成功", "success");
                $.post("/backend/login/login", {
                    Mobile: $("#Mobile").val(),
                    Password: $("#Password").val()
                }).done(function (json) {
                    if (json.Result == 0) {
                        window.location = '/backend/workbench';
                    }
                });
            }
            else {
                alert(json.Msg, "error");
            }
        }

        //邮箱注册成功（回调）
        function RegistSuccessEmail(json) {
            if (json.Result == 0) {
                alert("注册成功", "success");
                console.log(json);
                $.post("/backend/login/login", {
                    Mobile: $("#Email").val(),
                    Password: $("#EmailPassword").val()
                }).done(function (json) {
                    if (json.Result == 0) {
                        window.location = '/backend/workbench';
                    }
                });
            }
            else {
                alert(json.Msg, "error");
            }
        }

        $(window).on("resize", function () {
            Unreal.imgFill();
        });

        function $_animated(html){
            $("div.message_box").show().addClass("fadeInDown").find(".message_content").html(html);
            setTimeout(function(){
                $("div.message_box").removeClass("fadeInDown").addClass("fadeOutUp");
            },1500);
            setTimeout(function(){
                $("div.message_box").removeClass("fadeOutUp").hide();
            },1800);
        }
        {/literal}
    </script>
{/block}
