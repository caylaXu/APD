<div class="header" style="background: {if !empty($UserTheme)}{$UserTheme.Color}{else}{'#13427c;'}{/if}">
    <div id="headerMenu">
        <a href="javascript:void(0);" class="menu" style="display: block;"></a>

        <div class="menu-card hide">
            <div class="triangle">
            </div>
            <ul>
                <li>
                    <a href="/backend/workbench">
                        <img src="{$FileUrl}/img/menu-works.png" alt="">
                        <p><span>工作台</span></p>
                    </a>
                </li>
                <li>
                    <a href="/backend/project/overview">
                        <img src="{$FileUrl}/img/menu-project.png" alt="">
                        <p><span>项目</span></p>
                    </a>
                </li>
                <li>
                    <a href="/backend/workbench/collection">
                        <img src="{$FileUrl}/img/menu-inbox.png" alt="">
                        <p><span>收集箱</span></p>
                    </a>
                </li>
                <li>
                    <a href="/backend/calendar/index">
                        <img src="{$FileUrl}/img/menu-calendar.png" alt="">
                        <p><span>日历</span></p>
                    </a>
                </li>
                <li>
                    <a href="/backend/statistics/index">
                        <img src="{$FileUrl}/img/menu-report.png" alt="">
                        <p><span>报表</span></p>
                    </a>
                </li>
            </ul>
        </div>
    </div>

    <span class="title active">{block menuTitle}工作台{/block}</span>
    {*<span class="title myProject">{block myMenuTitle}{/block}</span>*}
    <span class="title hide"><a href="javascript:window.location.href=location.href;">工作台</a> - 筛选</span>
    {block leftMenu}{/block}
    <div class="pull-right">
        {block rightMenu}{/block}
        <div id="userSetting" style="display: inline">
            <a href="javascript:void(0)" class="user">
                <div class="avatar">
                    <img src="{$UserAvatar}" alt="">
                </div>
            </a>

            <div class="personal-card hide">
                <div class="triangle"></div>
                <ul>
                    <li>
                        <div class="avatar">
                            <a href="/backend/user/update"><img src="{$UserAvatar}" alt="" style="cursor: pointer;"></a>
                        </div>
                    </li>
                    <li>
                        <p style="margin-right: -20px;"><span class="gray nowrap">昵称：</span>{$UserName}</p>
                    </li>
                    {if $UserInfo.Mobile eq null}
                        <li>
                            <p style="margin-right: -20px;"><span class="gray nowrap">邮箱：</span>{$UserInfo.Email}</p>
                        </li>
                    {else}
                        <li>
                            <p style="margin-right: -20px;"><span class="gray nowrap">手机：</span>{$UserInfo.Mobile}</p>
                        </li>
                    {/if}
                    <li class="setting">
                        <p>
                            <a class="gray" href="#replaceSkin" data-toggle="work-panel" href="javascript:void(0)">
                                <span class="text-head">
                                    {*<span class="icon icon-skinBg"></span>*}
                                    <i class="iconfont icon-tubiao1huanfu" style="font-size: 20px;"></i>
                                </span>
                                <label style="cursor: pointer;">更换背景</label>
                            </a>
                        </p>
                    </li>
                    <li class="setting">
                        <p>
                            <a class="gray" href="/backend/user/update">
                                <span class="text-head">
                                    {*<span class="icon icon-setting"></span>*}
                                    <i class="iconfont icon-shezhi" style="font-size: 20px;"></i>
                                </span>
                                <label style="cursor: pointer;">
                                    <a class="gray" href="/backend/user/update">账号设置</a>
                                </label>
                            </a>
                        </p>
                    </li>
                    <li class="setting">
                        <p>
                            <a class="gray" href="javascript:Func.LogOut();">
                                <span class="text-head">
                                    {*<span class="icon icon-exit"></span>*}
                                    <i class="iconfont icon-tuichu" style="font-size: 19px;"></i>
                                </span>
                                <label style="cursor: pointer;">
                                    <a class="gray" href="javascript:Func.LogOut();">退出账号</a>
                                </label>
                            </a>
                        </p>
                    </li>
                </ul>
            </div>
        </div>
    </div>
    {*{/block}*}
</div>