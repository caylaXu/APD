{*二维码邀请*}
<div class="popover user-selector-popover-qrCode" id="{$Id}" data-type="{$Type}" data-url="{$Url}" {if isset($Limit)}data-limit="{$Limit}"{/if}>
    <div class="inner">
        <div class="content">
            <div style="text-align: center;margin-top: 30px;margin-bottom: 30px;">
                <img src="../../../resource/upload/theme/web/thumb/03.jpg" style="display: none;">
                <p style="font-size:12px;color: #c0c0c0;margin-top: 20px;">扫描二维码加入项目或分享链接邀请成员</p>
                <div style="margin-top: 20px;">
                <input id="ctrl_v_txt" type="text" readonly="readonly" autocomplete="off" style="width: 65%;height: 33px;line-height: 33px;border-radius: 3px 0px 0px 3px;" value="https://www.baidu.com"><input id="ctrl_C_btn" type="button" style="width: 33%;height: 33px;margin-left: -5px;line-height: 33px;color: #fff;background: #2BA1D8;outline: none;border: 0px;font-size: 12px;border-radius: 0px 3px 3px 0px;" data-clipboard-target="ctrl_v_txt" value="复制链接"></div>
            </div>
        </div>
    </div>
</div>
