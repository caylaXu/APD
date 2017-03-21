{include file='admin/layout/header.tpl'}
<!-- Content Header (Page header) -->
<section class="content-header">
	<h1>
		主题管理
		<small></small>
	</h1>
	{*<ol class="breadcrumb">*}
	{*<li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>*}
	{*<li class="active">Dashboard</li>*}
	{*</ol>*}
</section>

<!-- Main content -->
<section class="content">

	<div class="row">
		<div class="col-xs-12">
			<div class="box">
				<div class="box-header">
					<h3 class="box-title">查看主题</h3>
				</div>
				<div class="box-body">
					<table id="themes" class="table table-bordered table-hover">
						<thead>
						<tr>
							<th>ID</th>
							<th>名称</th>
							<th>类型</th>
							<th>状态</th>
							<th>色值</th>
							<th>缩略图</th>
							<th>操作</th>
						</tr>
						</thead>
						<tbody>
						{if $themes}
							{foreach $themes as $theme}
								<tr>
									<td>{$theme['Id']}</td>
									<td>{$theme['Theme']}</td>
									<td>{if $theme['Type'] == 1}Web主题{elseif $theme['Type'] == 2}App主题{/if}</td>
									<td>
										{if $theme['Status'] == 1}
											<span style="color: green">激活</span>
										{elseif $theme['Status'] == 0}
											<span style="color: red">禁用</span>
										{/if}
									</td>
									<td>{$theme['Color']}</td>
									<td>
										{if isset($theme['Thumb'])}<img height="50" src="{$theme['Thumb']}"/>{else}无{/if}
									</td>
									<td>
										<a href="/admin/theme/edit?id={$theme['Id']}">编辑</a><br />
										<a href="/admin/theme/img_change?id={$theme['Id']}&type=1">更换缩略图</a>
										{if $theme['Type'] == 1}
											/
										<a href="/admin/theme/img_change?id={$theme['Id']}&type=2">更换背景图</a>
										{/if}
									</td>
								</tr>
							{/foreach}
						{/if}
						</tbody>
					</table>
				</div>
				<!-- /.box-body -->
			</div>
		</div>
	</div>
	<!-- /.row -->

</section><!-- /.content -->
{include file='admin/layout/footer.tpl'}
<script>
	$(function ()
	{
		$('#themes').DataTable({
			"paging": true,
			"pageLength": 10,
			"lengthChange": false,
			"searching": false,
			"ordering": false,
			"info": true,
			"autoWidth": false,
		});
	});
</script>
</body>
</html>