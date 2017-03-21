{*导入合作过的成员*}
<div class="popover user-selector-popover-import" id="{$Id}" data-type="{$Type}" data-url="{$Url}" {if isset($Limit)}data-limit="{$Limit}"{/if}>
    <div class="inner">
        <div class="title">{$Title}</div>
        <div class="content">
            <div class="search">
                <input type="text" placeholder="搜索成员">
            </div>
            <div class="list narrow" style="margin-top: 10px;">
                <ul>

                </ul>
            </div>
            <div style="text-align: center;">
                <button id="import_Btn" type="button" class="btn btn-large btn-blue">确定</button>
            </div>
        </div>
    </div>
</div>
