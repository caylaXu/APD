
<div class="popover user-selector-popover" id="{$Id}" data-type="{$Type}" data-url="{$Url}" {if isset($Limit)}data-limit="{$Limit}"{/if}>
    <div class="inner">
        <div class="title">{$Title}</div>
        <div class="content">
            <div class="search">
                <input type="text" placeholder="搜索成员">
                {*<button class="btn btn-blue btn-small">添加</button>*}
            </div>
            <div class="add-member">
                <div class="add-button-wrap">
                    <a href="#" class="add-button"><span class="icon icon-add"></span>添加新成员</a>
                </div>
                <form action="/backend/user/input_user" method="post" class="ajax-form hide" data-success="AddMember">
                    <dl>
                        <dd><input type="text" placeholder="邮箱" name="Email"></dd>
                        <dt><button class="btn btn-blue btn-small">添加</button></dt>
                    </dl>
                </form>
            </div>
            <div class="list narrow">
                <ul>

                </ul>
            </div>
        </div>
    </div>
</div>
