{include file='admin/layout/header.tpl'}
<!-- Content Header (Page header) -->
<section class="content-header">
	<h1>
		APP安装包
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
					<h3 class="box-title">上传历史</h3>
				</div>
				<div class="box-body">
					<table id="package_history" class="table table-bordered table-hover">
						<thead>
						<tr>
							<th>ID</th>
							<th>名称</th>
							<th>类型</th>
							<th>版本号</th>
							<th>版本名称</th>
							<th>下载地址</th>
							<th>强制升级</th>
							<th>更新说明</th>
							<th>目标版本</th>
							<th>操作</th>
						</tr>
						</thead>
						<tbody>
						{if $versions}
							{foreach $versions as $version}
								<tr>
									<td>{$version['Id']}</td>
									<td>{$version['AppName']}</td>
									<td>{if $version['AppType'] == 1}IOS{else}Android{/if}</td>
									<td>{$version['VersionCode']}</td>
									<td>{$version['VersionName']}</td>
									<td>{$version['DownloadUrl']}</td>
									<td>{if $version['ForceUpdate'] == 1}是{else}否{/if}</td>
									<td>{$version['UpdateInfo']}</td>
									<td>{$version['TargetVersionId']}</td>
									<td>
										<a href="/admin/upload/edit?id={$version['Id']}">编辑</a>
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
	$('#package_history').DataTable({
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