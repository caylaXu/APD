<!doctype html>
<html>
    <head>
        <meta charset="utf-8" />
        <meta name="renderer" content="webkit">
        {*<meta http-equiv="X-UA-Compatible" content="chrome=1" />*}
        {*<meta http-equiv="X-UA-Compatible" content="IE=8">*}
        {*<meta http-equiv="x-dns-prefetch-control" content="on" />*}
        <meta http-equiv="X-UA-Compatible" content="IE=Edge,chrome=1">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable = no" />
	    <meta property="qc:admins" content="235231020161141163056375636" />
        <title>{block title}APD{/block}</title>
        <!-- Styles -->
        <link rel="stylesheet" href="{$FileUrl}/css/style.min.css">
        <link rel="stylesheet" href="{$FileUrl}/css/icon.min.css">
        <link rel="stylesheet" href="{$FileUrl}/css/iconfont.css">
        <link rel="stylesheet" href="{$FileUrl}/css/just-tip.css">
        <!-- ico -->
        <link rel="shortcut icon" type="image/x-icon" href="{$FileUrl}/img/apdico.ico" />
        <!--[if lt IE 9]>
        <script rel="stylesheet" type="text/javascript" src="{$CommonUrl}/common/public/respondjs/html5shiv.min.js"></script>
        <script rel="stylesheet" type="text/javascript" src="{$CommonUrl}/public/respondjs/respond.min.js"></script>
        <script rel="stylesheet" type="text/javascript" src="{$CommonUrl}/public/jquery/excanvas.js"></script>
        <![endif]-->
        {block styles}{/block}
    </head>
    <body>
        <div id="layout">
            <!-- Content -->
            {block body}{block content}{/block}{/block}
        </div>
        <!-- Script -->
        <script type="text/javascript" src="{$CommonUrl}/public/jquery/jquery-1.11.1.min.js"></script>
        <script type="text/javascript" src="{$CommonUrl}/public/adminlte/plugins/daterangepicker/moment.min.js"></script>
        <script type="text/javascript" src="{$CommonUrl}/plugins/arttemplate/template.js"></script>
        <script type="text/javascript" src="{$CommonUrl}/plugins/unreal/unreal.js?a=1"></script>

        <script type="text/javascript">
            var TestMode = true;
            var HOST = "";
            var DataBase = {};
            var State = {};
        </script>
        <script>
            (function (original) {
                jQuery.fn.clone = function () {
                    var result = original.apply(this, arguments),
                        my_textareas = this.find('textarea').add(this.filter('textarea')),
                        result_textareas = result.find('textarea').add(result.filter('textarea')),
                        my_selects = this.find('select').add(this.filter('select')),
                        result_selects = result.find('select').add(result.filter('select'));
                    for (var i = 0, l = my_textareas.length; i < l; ++i) $(result_textareas[i]).val($(my_textareas[i]).val());
                    for (var i = 0, l = my_selects.length;   i < l; ++i) result_selects[i].selectedIndex = my_selects[i].selectedIndex;
                    return result;
                };
            }) (jQuery.fn.clone);
        </script>
        {block setting}{/block}
        {block commonscripts}{/block}
        {block scripts}{/block}
    </body>
</html>
