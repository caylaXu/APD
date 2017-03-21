{extends "layouts/layout-basic.tpl"}
{block content}

<div class="background">
    <img src="{$FileUrl}/img/bg.jpg" alt="" class="img-fill" />
</div>
<div class="login-page">
    <div class="login-box">
      <div class="login-logo">
        <img src="{$FileUrl}/img/logo.png" alt="" />
      </div><!-- /.login-logo -->
      <div class="login-box-body">
        <form action="" method="post" class="ajax-form" data-success="Success" autocomplete="off">
          <div class="form-group has-feedback">
            <input type="text" class="form-control" name="Mobile" placeholder="邮箱或手机号">
            <span class="glyphicon glyphicon-user form-control-feedback"></span>
          </div>
          <div class="form-group has-feedback">
            <input type="password" class="form-control" name="Password" placeholder="密码">
            <span class="glyphicon glyphicon-lock form-control-feedback"></span>
          </div>
          <div class="button-row">
              <button type="submit">登录</button>
          </div>
          <p style="overflow: hidden;">
              <a href="/backend/login/register" class="text-center pull-left">注册账号</a>
              <a href="/backend/login/forget_password" class="text-center pull-right">忘记密码?</a>
          </p>
          <div style="position: relative;margin-top: 20px;text-align: center;">
              <div style="width: 30%;height:40px;float: left;">
                  <p style="border-top: 1px solid #fff;height: 20px;margin-top: 20px;"></p>
              </div>
              <div style="width: 30%;height:40px;float: right;">
                  <p style="border-top: 1px solid #fff;height: 20px;margin-top: 20px;"></p>
              </div>
              <div style="font-size: 12px;">其他方式登陆</div>
          </div>
          <div style="position: relative;display: flex;justify-content: space-between;margin-top: 15px;font-size: 12px;">
            <div style="width:33.3%\9;text-align: center\9;float: left\9;">
                <a href="/backend/auth/qq_login" style="text-decoration: none;">
                    <img style=""
                    <img style="width: 44px;cursor: pointer;" src="{$FileUrl}/img/qq.png">
                </a>
                <p style="margin-top: 5px;">QQ</p>
            </div>
            <div style="width:33.3%\9;text-align:center\9;float: left\9;">
                <a href="/backend/auth/wechat_login" style="text-decoration: none;">
                    <img style="width: 44px;cursor: pointer;" src="{$FileUrl}/img/wx.png">
                </a>
                <p style="margin-top: 5px;">微信</p>
            </div>
            <div style="width:33.3%\9;text-align: center\9;float: left\9;">
                <a href="/backend/auth/weibo_login" style="text-decoration: none;">
                    <img style="width: 44px;cursor: pointer;" src="{$FileUrl}/img/wb.png">
                </a>
                <p style="margin-top: 5px;">微博</p>
            </div>
          </div>
            <div class="message_box animated">
                <div class="message_content"></div>
            </div>
        </form>
        <br>
      </div><!-- /.login-box-body -->
    </div><!-- /.login-box -->
    </div>
{/block}

{block styles}
    <style media="screen">
body{
    background: #4070ae;
    font-family: Arial,"Microsoft Yahei",sans-serif;
    font-size: 14px;
    color: white;
}
#layout{
    background: none;
}
.background{
    position: fixed;
    z-index: -1;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
}
[type=email], [type=password], [type=text],[type=submit]{
    width: 100%;
    border-radius: 2px;
    font: inherit;
    -moz-box-sizing: border-box;
    box-sizing: border-box;
}
[type=email], [type=password], [type=text]{
    background: white;
    height: 42px;
    line-height: 22px;
    line-height: 42px\9;
    border: 1px solid #dddddd;
    padding: 0 0.5em;
}
[type=submit]{
    background: #1c97ff;
    height: 40px;
    color: white;
    border: none;
}
.login-page{
    text-align: center;
    margin: 120px auto;
    width: 280px;
}
.form-group{
    margin-bottom: 10px;
    color: black;
}
.white{
    color: white;
}
.button-row{
    margin-top: 30px;
    margin-bottom: 16px;
    padding: 0;
}
a:hover{
    text-decoration: underline;
}
.login-logo{
    margin-bottom: 50px;
}
.login-box-msg {
    margin: 0;
    margin-top: -40px;
    margin-bottom: 20px;
    text-align: left;
}
div.message_box{
    display: none;
    color: #fff;
    background-color:rgba(0,0,0,0.5);
    border-radius: 5px;
    position: fixed;
    width: 280px;
    top: 217px;
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
  $('form.ajax-form button[type=submit]').on('click', function () {
      var userName=$('form input[name=Mobile]').val(),
          passWord=$('form input[name=Password]').val(),
          emailReg=/^([a-zA-Z0-9_-])+@([a-zA-Z0-9_-])+((\.[a-zA-Z0-9_-]{2,3}){1,2})$/,
          numberReg=/^\d+$/g,
          telReg=/^(((13[0-9]{1})|(14[0-9]{1})|(15[0-9]{1})|(17[0-9]{1})|(18[0-9]{1}))+\d{8})$/;
          if (userName != "") {
              if (passWord != "") {
                  if (userName.indexOf('@') > 0) {
                      if (emailReg.test(userName)) {
                          return true;
                      } else {
                          $_animated("邮箱格式错误!");
                          return false;
                      }
                  } else if (numberReg.test(userName)) {
                      if (telReg.test(userName)) {
                          return true;
                      }else {
                          $_animated("手机号格式不正确!");
                          return false;
                      }
                  } else {
                      $_animated("请输入正确格式手机号或邮箱!");
                      return false;
                  }
              }
              else {
                  $_animated("密码不能为空!");
                  return false;
              }
          } else {
              $_animated("邮箱或手机号不能为空!");
              return false;
          }
  });

  function Success(json){
    if(json.Result == 0){
      window.location = '/backend/workbench';
    }else{
        $_animated(json.Msg,"error");
    }
  }

  $(window).on("resize",function(){
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
