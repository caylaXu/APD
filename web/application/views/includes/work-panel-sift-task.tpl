{capture assign="sift"}
    <form action="/backend/task/get_task_by_params" method="post" class="ajax-form form-panel" data-success="SiftSuccess">
        <dl class="form-group time-selector">
            <dt>时<span class="em-2"></span>间：</dt>
            <dd class="tab-container">
                <div class="time-button">
                    <label class="tab pill" data-value="yesterday">昨天</label>
                    <label class="tab pill" data-value="today">今天</label>
                    <label class="tab pill" data-value="tomorrow">明天</label>
                    <label class="tab pill" data-value="thisweek">本周</label>
                    <label class="tab pill" data-value="nextweek">后一周</label>
                    <label class="tab pill" data-value="thismonth">本月</label>
                    <label class="tab pill" data-value="nextmonth">后一个月</label>
                </div>
                <input type="text" class="date-range-picker disabled" readonly>
                {* <input type="hidden" name="Time" value="">
                <input type="hidden" name="StartDate" value="">
                <input type="hidden" name="Time" value=""> *}
            </dd>
        </dl>

        <dl class="form-group">
            <dt>关<span class="em-2"></span>系：</dt>
            <dd>
                <label><span class="radio"><input type="radio" name="RltType" value="responsible" checked></span>我的</label>
                <label><span class="radio"><input type="radio" name="RltType" value="follower"></span>我关注的</label>
                <label><span class="radio"><input type="radio" name="RltType" value="creator"></span>我创建的</label>
                <label><span class="radio"><input type="radio" name="RltType" value="finisher"></span>我完成的</label>
            </dd>
        </dl>

        <dl class="form-group" >
            <dt>状<span class="em-2"></span>态：</dt>
            <dd>
                <label><span class="radio"><input type="radio" name="Progress" value="-1" checked></span>全部</label>
                <label><span class="radio"><input type="radio" name="Progress" value="100"></span>已完成</label>
                <label><span class="radio"><input type="radio" name="Progress" value="0"></span>未完成</label>
            </dd>
        </dl>

        <dl class="form-group">
            <dt>紧<span class="em-05"></span>急<span class="em-05"></span>度：</dt>
            <dd>
                <label><span class="radio"><input type="radio" name="Priority" value="-1" checked></span>全部</label>
                <label><span class="radio"><input type="radio" name="Priority" value="1" ></span>紧急</label>
                <label><span class="radio"><input type="radio" name="Priority" value="0" ></span>不紧急</label>
            </dd>
        </dl>

        <dl class="form-group user-sift">
            <dt>责<span class="em-05"></span>任<span class="em-05"></span>人：</dt>
            <dd>
                {include "includes/user-selector.tpl" Name="DirectorIds" Target="#" Class="readonly"}
            </dd>
        </dl>

        <div class="params">
            <input type="hidden" name="ProjectId" value="{if isset($ProjectId)}{$ProjectId}{else}-1{/if}">
        </div>

        <p class="submit-buttons"><button type="submit" class="btn btn-large btn-blue">提交</button></p>
    </form>
{/capture}

{WorkPanel Title="筛选" Id="sift-panel" Content=$sift }

{include "includes/popover-user-selector.tpl" Title="责任人" Id="project-manager-popover" Type="1" Url="/backend/project/add_rlt_user" Limit="1"}
