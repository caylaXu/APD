
{function "WorkPanel" Title="" DeleteButton="" Buttons="" Id="" Content=""}
    <div class="work-panel" id="{$Id}">
        <div class="title">
            <span style="float: left;">{$Title}</span>

            {$Buttons}

            <a class="icon icon-close close-btn" data-dismiss="work-panel"></a>
            {$DeleteButton}
        </div>
        <div class="content-wrap">
            <div class="content">
                {$Content}
            </div>
        </div>
    </div>
{/function}

{function "FormControl" Type="input" Class="" Title="" Name="" Value="" Attribute="" Options=[0=>"无"] Required="0"}
    {if $Type=="submit"}
        <p class="submit-buttons"><button type="submit" class="btn btn-large btn-blue">提交</button></p>
    {else}
        <dl class="form-group {if $Required}required{/if}">
            <dt style="color: #999;font-size: 12px;padding-top: 10px;">{$Title}</dt>
            <dd>
                {if $Type=="input"}
                    <input type="text" class="form-control {$Class}" name="{$Name}" value="{$Value}" {$Attribute} placeholder="输入{$Title}">
                {elseif $Type == "textarea" }
                    <textarea name="{$Name}" class="form-control {$Class}" value="{$Value}" {$Attribute} placeholder="{$Title}"></textarea>
                    <textarea id="shadow_txt" style="position: absolute; border-width: 0px; padding: 0px; visibility: hidden;"></textarea>
                {elseif $Type == "select"}
        			<select name="{$Name}" class="form-control {$Class}" {$Attribute}>
        				{foreach $Options as $v=>$t}
        					<option value="{$v}">{$t}</option>
                        {foreachelse}
        				{/foreach}
        			</select>
                    {*<span style="position: absolute;top: 1px;right: 5px;width: 30px;height: 34px; background-color: #ffffff;"></span>*}
                    {*<span class="icon icon-arrow-down" style="position: absolute;top: 8px;right: 8px;"></span>*}
                {elseif  $Type == "text"}
                    <span style="width: 90%;" class="form-control-text {$Class}" name="{$Name}" {$Attribute}>{$Value}</span>
                {/if}
            </dd>
        </dl>
    {/if}
{/function}
