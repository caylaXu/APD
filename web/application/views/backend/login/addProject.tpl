{extends "layouts/layout-basic.tpl"}
{block content }
    <div class="background">
        <img src="{$FileUrl}/img/bg.jpg" alt="" class="img-fill"/>
    </div>
    <div class="login-page">
        <div class="login-box">
            <div class="login-logo">
                <img src="{$FileUrl}/img/logo.png" alt=""/>
                <input type="hidden" id="userCount" value="{if !empty($data.user)}{$data.user|@count}{else}0{/if}">
                {if !empty($data.project)}
                    <p class="p_title_top" projectId="{$data.project.Id}">加入【{$data.project.Name}】项目</p>
                {/if}
            </div>
            <!-- /.login-logo -->
            <div class="login-box-body">
                {*已登录*}
                <div class="account_login hide">
                    <div class="img_box">
                        {if !empty($data.user)}
                            <input type="hidden" id="userId" value="{$data.user.UserId}">
                            {if empty($data.user.Email) && empty($data.user.Mobile)}
                                <img src="{$data.user.Avatar}" alt="" style="margin-top: -10px;">
                                <div style="margin-top: -50px;">
                                    <p style="margin-top: 14px;">{$data.user.UserName}</p>
                                </div>
                            {else}
                                <img src="{$data.user.Avatar}" alt="">
                                <div style="margin-top: -50px;">
                                    <p>{$data.user.UserName}</p>
                                    {if !empty($data.user.Email)}
                                        <p>{$data.user.Email}</p>
                                    {else}
                                        <p>{$data.user.Mobile}</p>
                                    {/if}
                                </div>
                            {/if}
                        {/if}
                    </div>
                    <div class="button-row" style="margin-bottom: 0px;">
                        <button class="btn_login" style="border:none;background:#1c97ff;" type="button">进入项目</button>
                        <p style="margin-top: 20px;">
                            <button id="btn_Account" class="btn_login" type="button">使用其他账号</button>
                        </p>
                    </div>
                </div>
                {*未登录*}
                <form id="login_addProject" class="ajax-form hide" autocomplete="off">
                    <div class="form-group has-feedback">
                        <input type="text" class="form-control" name="Mobile" placeholder="手机号或邮箱" id="Mobile">
                        <span class="glyphicon glyphicon-user form-control-feedback"></span>
                    </div>
                    <div class="form-group has-feedback">
                        <div class="input-group">
                            <input type="password" class="form-control" name="Password" placeholder="密码" id="Password">
                            <span class="input-group-addon icon icon-password lookpwd"></span>
                        </div>
                        <span class="glyphicon glyphicon-lock form-control-feedback"></span>
                    </div>
                    <input type="hidden" name="Title" value="">
                    <div class="button-row" style="margin-top: 10px;">
                        <button type="submit">登录，加入项目</button>
                    </div>
                    <p>
                        <button class="btn_login" type="button">注册账号</button>
                    </p>
                    <div style="position: relative;margin-top: 20px;text-align: center;">
                        <div style="width: 30%;height:40px;float: left;">
                            <p style="border-top: 1px solid #fff;height: 20px;margin-top: 20px;"></p>
                        </div>
                        <div style="width: 30%;height:40px;float: right;">
                            <p style="border-top: 1px solid #fff;height: 20px;margin-top: 20px;"></p>
                        </div>
                        <div style="font-size: 12px;">第三方登录</div>
                    </div>
                    <div style="position: relative;display: flex;justify-content: space-between;margin-top: 15px;font-size: 12px;">
                        <div style="width:33.3%\9;text-align: center\9;float: left\9;">
                            <a href="/backend/auth/qq_login?ProjectId={$data.project.Id}" style="text-decoration: none;">
                                <img style="width: 44px;cursor: pointer;" src="{$FileUrl}/img/qq.png">
                            </a>
                            <p style="margin-top: 5px;">QQ</p>
                        </div>
                        <div style="width:33.3%\9;text-align:center\9;float: left\9;">
                            <a href="/backend/auth/wechat_login?ProjectId={$data.project.Id}" style="text-decoration: none;">
                                <img style="width: 44px;cursor: pointer;" src="{$FileUrl}/img/wx.png">
                            </a>
                            <p style="margin-top: 5px;">微信</p>
                        </div>
                        <div style="width:33.3%\9;text-align: center\9;float: left\9;">
                            <a href="/backend/auth/weibo_login?ProjectId={$data.project.Id}" style="text-decoration: none;">
                                <img style="width: 44px;cursor: pointer;" src="{$FileUrl}/img/wb.png">
                            </a>
                            <p style="margin-top: 5px;">微博</p>
                        </div>
                    </div>
                </form>
                {*注册*}
                <div class="addProjectRegister hide">
                    {*手机注册和邮箱注册切换*}
                    <div class="login-box-msg" style="margin-top: -13px;">
                        <a id="a_mobile" href="javascript:void(0)" class="active">手机号注册</a>|<a id="a_email" href="javascript:void(0)">邮箱注册</a>
                    </div>
                    {*手机号注册*}
                    <form action="" method="post" class="ajax-form active" data-success="RegistSuccessMobile" autocomplete="off">
                        <div class="form-group has-feedback">
                            <input type="text" class="form-control" name="Name" placeholder="昵称">
                            <span class="glyphicon glyphicon-user form-control-feedback"></span>
                        </div>
                        <div class="form-group has-feedback">
                            <input type="text" class="form-control" name="Mobile" placeholder="手机号" id="Mobile">
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
                                <button type="button" class="input-group-addon button-get-verify btn" id="getVerifyMobile">获取验证码</button>
                            </div>
                            <span class="glyphicon glyphicon-phone form-control-feedback"></span>
                        </div>
                        <input type="hidden" name="Title" value="">
                        <div class="button-row">
                            <button type="submit">注册，加入项目</button>
                        </div>
                        <p>
                            <button class="btn_login" type="button">已有账号？登录</button>
                        </p>
                    </form>
                    {*邮箱注册*}
                    <form action="" method="post" class="ajax-form" data-success="RegistSuccessEmail" autocomplete="off">
                        <div class="form-group has-feedback">
                            <input type="text" class="form-control" name="Name" placeholder="昵称">
                            <span class="glyphicon glyphicon-user form-control-feedback"></span>
                        </div>
                        <div class="form-group has-feedback">
                            <input type="text" class="form-control" name="Email" placeholder="邮箱" id="Email">
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
                            <button class="btn_login" type="button">已有账号？登录</button>
                        </p>
                    </form>
                </div>
                {*弹框*}
                <div class="message_box animated">
                    <div class="message_content"></div>
                </div>
                {*显示项目经理*}
                {if $data.manager|@count gt 0}
                    <p class="p_hr"></p>
                    <p class="p_title">项目经理</p>
                    <div class="user_box">
                        <div>
                            <img src="{$data.manager.Avatar}" alt="">
                            <p>{$data.manager.UserName}</p>
                            <p>{$data.manager.Email}</p>
                        </div>
                    </div>
                {/if}
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
            margin-top: 30px;
            margin-bottom: 16px;
            padding: 0;
        }

        a:hover {
            text-decoration: underline;
        }

        .login-logo {
            margin-bottom: 50px;
        }

        .login-logo .p_title_top {
            font-size: 18px;
            margin: 10px 0px
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
            padding: 20px;
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

{block setting}
    <script type="text/javascript">
        Data = {$data|@json_encode};
    </script>
{/block}

{block scripts}
    <script>
        {literal}
        window.alert = function(){
            return false;
        };
        //点击切换使用其它方式登录
        $(function () {
            //默认加载（未登录或已登录）
            var userCount = $("#userCount").val();
            if (userCount > 0) {
                $("div.account_login").show();
            } else {
                $("form#login_addProject").show();
            }
            //点击使用其它方式登录（显示未登录页面）
            $("div.account_login button#btn_Account").on("click", function () {
                $(this).parents("div.account_login").hide();
                $("form#login_addProject").show();
            });

            //未登录---去注册
            $("form#login_addProject button.btn_login").on("click", function () {
                $("form#login_addProject").hide();
                $("div.addProjectRegister").show();
            });

            //注册页面（手机注册和邮箱注册切换）
            $("a#a_mobile").on("click", function (e) {
                e.stopPropagation();
                e.preventDefault();

                $(this).addClass("active");
                $("form[data-success=RegistSuccessMobile]").addClass("active");

                $("a#a_email").removeClass("active");
                $("form[data-success=RegistSuccessEmail]").removeClass("active");
            });
            $("a#a_email").on("click", function (e) {
                e.stopPropagation();
                e.preventDefault();

                $(this).addClass("active");
                $("form[data-success=RegistSuccessEmail]").addClass("active");

                $("a#a_mobile").removeClass("active");
                $("form[data-success=RegistSuccessMobile]").removeClass("active");
            });
        });

        //查看密码
        $(function () {
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

        //检查参数是否正确
        $(function(){
            $("form#login_addProject button[type=submit]").on("click",function(){
                var userName = $('form input[name=Mobile]').val(),
                    passWord = $('form input[name=Password]').val(),
                    emailReg = /^([a-zA-Z0-9_-])+@([a-zA-Z0-9_-])+((\.[a-zA-Z0-9_-]{2,3}){1,2})$/,
                    numberReg = /^\d+$/g,
                    telReg = /^(((13[0-9]{1})|(14[0-9]{1})|(15[0-9]{1})|(17[0-9]{1})|(18[0-9]{1}))+\d{8})$/,
                    projectId = $("p.p_title_top").attr("projectid");
                if (userName != "") {
                    if (passWord != "") {
                        if(userName.indexOf("@")>0){
                            if(emailReg.test(userName)){
                                $ajax_addProject(userName, passWord, projectId);
                                return true;
                            }else{
                                $_animated("邮箱格式不正确!");
                                return false;
                            }
                        }else if(numberReg.test(userName)){
                            if(telReg.test(userName)){
                                $ajax_addProject(userName, passWord, projectId);
                                return true;
                            }else{
                                $_animated("手机号格式不正确!");
                                return false;
                            }
                        }else{
                            $_animated("请输入正确格式手机号或邮箱!");
                            return false;
                        }
                    } else {
                        $_animated("密码不能为空!");
                        return false;
                    }
                } else {
                    $_animated("手机号或邮箱不能为空!");
                    return false;
                }
            });

            //手机号注册方式验证参数
            $("form[data-success=RegistSuccessMobile]").find("button[type=submit]").on("click",function(){
                var mobile_tel=$(this).parents('form[data-success=RegistSuccessMobile]').find('input[name=Mobile]').val(),
                        telReg = /^(((13[0-9]{1})|(14[0-9]{1})|(15[0-9]{1})|(17[0-9]{1})|(18[0-9]{1}))+\d{8})$/;
                var name=$(this).parents('form[data-success=RegistSuccessMobile]').find('input[name=Name]').val();
                var password=$(this).parents('form[data-success=RegistSuccessMobile]').find('input[name=Password]').val();
                var authCode=$(this).parents('form[data-success=RegistSuccessMobile]').find('input[name=AuthCode]').val();
                var projectId = $("p.p_title_top").attr("projectid");
                if(mobile_tel!=""){
                    if(name!=""){
                        if(password!=""){
                            if(authCode!=""){
                                if(telReg.test(mobile_tel)){
                                    $ajax_register.$ajax_mobile(name, mobile_tel, password, authCode, projectId);
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
                var projectId = $("p.p_title_top").attr("projectid");
                if(emailVal!=""){
                    if(name!=""){
                        if(password!=""){
                            if(authCode!=""){
                                if(emailReg.test(emailVal)){
                                    $ajax_register.$ajax_email(name, emailVal, password, authCode, projectId);
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

            //已有账号?去登录（切换）
            $("form[data-success=RegistSuccessMobile]").find("button.btn_login").on("click",function(){
                $("div.addProjectRegister").hide();
                $("form#login_addProject").show();
            });
            $("form[data-success=RegistSuccessEmail]").find("button.btn_login").on("click",function(){
                $("div.addProjectRegister").hide();
                $("form#login_addProject").show();
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
        });

        //已登陆--直接进入项目
        $("div.account_login button.btn_login").eq(0).on("click",function(){
            var userId = $("#userId").val();
            var projectId = $("p.p_title_top").attr("projectid");
            //加入项目
            $_addProject(userId,projectId);
        })

        //未登录提交到login中验证
        function $ajax_addProject(userName,passWord,projectId){
            $.post("/backend/login/login", {
                Mobile: userName,
                Password: passWord
            }).done(function (json) {
                if (json.Result == 0) {
                    //加入项目
                    $_addProject(json.Data,projectId);
                } else {
                    $_animated(json.Msg, "error");
                }
            });
        }

        //注册页面提交验证
        var $ajax_register = {
            $ajax_mobile: function (name, mobile, passWord, authCode, projectId) {
                $.post("/backend/login/register_by_mobile", {
                    Name: name,
                    Mobile: mobile,
                    Password: passWord,
                    AuthCode: authCode
                }).done(function (json) {
                    $_register_yes.RegistSuccessMobile(json, mobile, passWord, projectId);
                });
            },
            $ajax_email: function (name, email, passWord, authCode, projectId) {
                $.post("/backend/login/register_by_email", {
                    Name: name,
                    Email: email,
                    Password: passWord,
                    AuthCode: authCode
                }).done(function (json) {
                    $_register_yes.RegistSuccessEmail(json, email, passWord, projectId);
                });
            }
        }

        //注册成功回调并登录
        var $_register_yes={
            RegistSuccessMobile: function (json, mobile, passWord, projectId) {
                if (json.Result == 0) {
                    $_animated("注册成功", "success");
                    $.post("/backend/login/login", {
                        Mobile: mobile,
                        Password: passWord
                    }).done(function (json) {
                        if (json.Result == 0) {
                            $_addProject(json.Data,projectId);
                        }else{
                            $_animated(json.Msg, "error");
                        }
                    });
                }
                else {
                    $_animated(json.Msg, "error");
                }
            },
            RegistSuccessEmail: function (json, email, passWord, projectId) {
                if (json.Result == 0) {
                    $_animated("注册成功", "success");
                    $.post("/backend/login/login", {
                        Mobile: email,
                        Password: passWord
                    }).done(function (json) {
                        if (json.Result == 0) {
                            $_addProject(json.Data,projectId);
                        }else{
                            $_animated(json.Msg, "error");
                        }
                    });
                }
                else {
                    $_animated(json.Msg, "error");
                }
            }
        }

        //加入项目跳转到项目展示页
        function $_addProject(userId,projectId){
            $.post("/backend/login/join_a_project",{
                UserId:userId,
                ProjectId:projectId
            }).done(function(json){
                if(json.Result==0){
                    window.location = '/backend/task/task_tree?ProjectId=' + projectId;
                }else{
                    $_animated(json.Msg,"error");
                }
            });
        }

        $(window).on("resize", function () {
            Unreal.imgFill();
        });

        //弹框
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
