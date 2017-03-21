{extends "layouts/layout-basic.tpl" }

{block body }
    {include "includes/header.tpl"}
    {*{include "includes/sidebar.tpl"}*}
    <div id="viewport">
        <!-- Content -->
        {block bg}
            <div class="background" style="background: url({if !empty($UserTheme)}{$UserTheme.BgImg}{else}{'../../../resource/upload/theme/web/src/12.jpg'}{/if}) no-repeat center center;">
                <input id="themeId" type="hidden" value="{if !empty($UserTheme)}{$UserTheme.ThemeId}{else}{'26'}{/if}">
            </div>
        {/block}
        {block content}{/block}
        <div class="work-panel" id="replaceSkin" style="min-width:350px!important;">
            <div class="title">
                <label>更换背景</label>
                <a class="icon icon-close close-btn demo demoDown" tip_Title="关闭" data-target="#replaceSkin" data-dismiss="work-panel"></a>
            </div>
            <div class="content-wrap">
                <div class="content" id="skinBox" style="overflow: hidden;">

                </div>
                <div style="width: 100%;text-align: center;margin-top: 50px;">
                    <button type="button" class="btn btn-large btn-blue">确定</button>
                </div>
            </div>
        </div>
    </div>
{/block}

{block commonscripts}

{literal}
<script type="text/template" id="member-tpl">
    <li class="item">
        <dl class="fixed">
            <dt>
                <label class="checkbox"><input type="checkbox" name="UserId" value="{{UserId}}"></label>
                <div class="avatar"><img src="{{Avatar}}" title="{{UserName}}" alt="" {{if Status == 0}}class="filtergray"{{/if}}></div>
            </dt>
            <dd title="{{UserName}}">{{UserName}}</dd>
        </dl>
    </li>
</script>
<script type="text/template" id="avatar-edit-tpl">
    <span class="avatar" data-id="{{UserId}}">
        <img src="{{Avatar}}" tip_Title="{{UserName}}" alt="" {{if Status == 0}}class="filtergray demo demoDown"{{else}}class="demo demoDown"{{/if}}>
        <a href="#" class="delete"><span class="icon icon-delete-avatar"></span></a>
    </span>
</script>
<script type="text/template" id="avatar-edit-tpl-user">
    <span class="avatar" data-id="{{UserId}}">
        <img src="{{Avatar}}" title="{{UserName}}" alt="" {{if Status == 0}}class="filtergray"{{/if}}>
        <a href="#" class="delete"><span class="icon icon-delete-avatar"></span></a>
        <p style="display: inline-block;font-weight: bold;margin-top: 5px;">
            <span style="display: inline-block;width: 50px;overflow: hidden;height: 20px;text-transform:capitalize;">{{UserName}}</span>
        </p>
    </span>
</script>
<script type="text/template" id="tip_template">
    <div class="tip_box">
        <div class="tip_box_top"></div>
        <div class="tip_close">
            <i class="iconfont icon-iconfonticonfontclose" style="cursor:pointer;"></i>
        </div>
        <div class="tip_txt">{{Title}}</div>
        <div class="tip_btns">
            <a href="#" class="{{Type}}">{{TypeName}}</a>
        </div>
    </div>
</script>
{/literal}

{literal}
<script type="text/template" id="project-tpl-dropdown">
    <li class="item">
        <dl class="fixed">
            <dt>
                <label class="checkbox"><input type="checkbox" name="Id" value="{{Id}}"></label>
            </dt>
            <dd title="{{Title}}">{{Title}}</dd>
        </dl>
    </li>
</script>
{/literal}
    {*<span style="width:40px;height:40px;display:inline-block;line-height:40px;text-align:center;">{{UserName}}</span>*}

{if !isset($Permission)}
    {literal}
        <script type="text/template" id="task-tpl">
            <dl class="{{if CompleteProgress==100}}completed{{/if}}">
                <dt><div class="checkbox {{if CompleteProgress==100}}checked{{/if}}"><input type="checkbox" {{if CompleteProgress==100}}checked{{/if}}></div></dt>
                <dd href="{{if Permission==1}}#edit-task{{else}}#view-task{{/if}}" data-toggle="work-panel" class="edit-btn" style="cursor:pointer;">
                    <span style="max-width:80%;display:inline-block;margin-left:10px;" limit="25">{{Title}}</span>
                    {{if Priority==1}}<span class="icon icon-priority"></span>{{/if}}
                </dd>
                <dt>
                    <!--{{if Permission==1}}<a href="#" class="delete-btn hover-item"><span class="icon icon-delete"></span></a>{{/if}}-->
                    <span class="list-avatar-wrap">
                    {{each AssignedTo as v i}}
                        <div class="avatar"><img src="{{v.Avatar}}" title="{{v.UserName}}" alt="" {{if v.Status == 0}}class="filtergray"{{/if}}></div>
                    {{/each}}
                    </span>
                    <span class="time">{{StartDateString}} - <span class="orange">{{DueDateString}}</span></span>
                </dt>
            </dl>
        </script>
    {/literal}
{elseif $Permission==1}
    {literal}
        <script type="text/template" id="task-tpl">
            <dl>
                <dt><div class="checkbox {{if CompleteProgress==100}}checked{{/if}}"><input type="checkbox" {{if CompleteProgress==100}}checked{{/if}}></div></dt>
                <dd href="#edit-task" data-toggle="work-panel" class="edit-btn" style="cursor:pointer;">
                    <span style="max-width:80%;display:inline-block;margin-left:10px;" limit="25">{{Title}}</span>
                    {{if Priority==1}}<span class="icon icon-priority"></span>{{/if}}
                </dd>
                <dt>
                    <!--<a href="#" class="delete-btn hover-item"><span class="icon icon-delete"></span></a>-->
                    <span class="list-avatar-wrap">
                    {{each AssignedTo as v i}}
                        <div class="avatar"><img src="{{v.Avatar}}" title="{{v.UserName}}" alt="" {{if v.Status == 0}}class="filtergray"{{/if}}></div>
                    {{/each}}
                    </span>
                    <span class="time">{{StartDateString}} - <span class="orange">{{DueDateString}}</span></span>
                </dt>
            </dl>
        </script>
    {/literal}
{else}
    {literal}
        <script type="text/template" id="task-tpl">
            <dl>
                <dt>
                    <div class="checkbox {{if CompleteProgress==100}}checked{{/if}}">
                        <input type="checkbox" {{if CompleteProgress==100}}checked{{/if}}>
                    </div>
                </dt>
                <dd href="#view-task" data-toggle="work-panel" class="edit-btn" style="cursor:pointer;">
                    <span style="max-width:80%;display:inline-block;margin-left:10px;" limit="25">{{Title}}</span>
                    {{if Priority==1}}<span class="icon icon-priority"></span>{{/if}}
                </dd>
                <dt>
                    <span class="list-avatar-wrap">
                    {{each AssignedTo as v i}}
                        <div class="avatar"><img src="{{v.Avatar}}" title="{{v.UserName}}" alt="" {{if v.Status == 0}}class="filtergray"{{/if}}></div>
                    {{/each}}
                    </span>
                    <span class="time">{{StartDateString}} - <span class="orange">{{DueDateString}}</span></span>
                </dt>
            </dl>
        </script>
    {/literal}
{/if}

<script type="text/javascript">
    DataBase.UserId = "{$UserId}";
    DataBase.UserName = "{$UserName}";
    DataBase.UserTheme = '{$UserTheme|@json_encode}';
</script>
<script src="{$FileUrl}/js/script.js"></script>
<script src="{$FileUrl}/js/script_panel.js"></script>
<script src="{$FileUrl}/js/justTools.js"></script>
{include "includes/popover-message.tpl"}

<script type="text/javascript">
    DataBase.UserInfo = {$UserInfo|@json_encode};
</script>

{assign TestMode 1}
{if $TestMode}
<script type="text/javascript">
    {*console.info("UserInfo",{$UserInfo|@json_encode});*}
    {*console.info("Data",{$Data|@json_encode|default:[]});*}
    {*console.log("UserTheme",{$UserTheme|@json_encode});*}
</script>
{/if}

{/block}