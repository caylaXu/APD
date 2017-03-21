<div class="sidebar">
    <div class="user">
        <a href="javascript:void(0)">
            <div class="avatar">
                <img src="{$UserAvatar}"  alt="">
            </div>
            <p>
                <span class="name" >{$UserName}</span>
                {* <span class="separator"></span>
                <span class="icon icon-setting"></span> *}
            </p>
        </a>
    </div>
    <div class="personal-card hide">
        <dl>
            <dt>
                <div class="avatar">
                    <img src="{$UserAvatar}"  alt="">
                </div>
            </dt>
            <dd>
                <p>
                    {$UserName}
                    <a class="pull-right gray" href="/backend/user/update">
                        <span class="text-head"><span class="icon icon-setting"></span></span>设置
                    </a>
                </p>
                <p>
                    {$UserInfo.Mobile}
                    <a class="pull-right gray" href="javascript:Func.LogOut();">
                        <span class="text-head"><span class="icon icon-exit"></span></span>退出
                    </a>
                </p>
            </dd>
        </dl>
        <p>邮箱：{$UserInfo.Email}</p>
    </div>
    <div class="nav">
        <ul>
            <li>
                <a href="/backend/workbench"><span class="icon icon-home"></span>工作台</a>
            </li>
            <li>
                <a href="/backend/project/overview"><span class="icon icon-item"></span>项目</a>
            </li>
            <li>
                <a href="#"><span class="icon icon-calendar"></span>日历</a>
            </li>
            <li>
                <a href="#"><span class="icon icon-chart"></span>报表</a>
            </li>
        </ul>
    </div>
</div>
