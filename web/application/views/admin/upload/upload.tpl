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
			<div class="box box-primary">
				<div class="box-header with-border">
					<h3 class="box-title">上传</h3>
				</div><!-- /.box-header -->
				<!-- form start -->
				<form role="form" action="/admin/upload/upload" method="post" enctype="multipart/form-data">
					<div class="box-body">
						<div class="form-group">
							{if isset($errors)}
								{foreach $errors as $error}
									<div style="text-align: center;color: red">{$error}</div>
								{/foreach}
							{/if}
						</div>
						<div class="form-group">
							<label for="">名称：</label>
							<input type="text" class="form-control" name="app_name"
								   placeholder="" maxlength="64"
								   value="{if isset($data['AppName'])}{$data['AppName']}{/if}">
						</div>
						<div class="form-group">
							<label for="">类型：</label>
							<div class="radio">
								<label>
									<input type="radio" name="app_type" value="1"
										   {if isset($data['AppType']) && $data['AppType'] == 1}checked="checked"{/if}>
									IOS
								</label>
							</div>
							<div class="radio">
								<label>
									<input type="radio" name="app_type" value="2"
										   {if !isset($data['AppType']) || (isset($data['AppType']) && $data['AppType'] == 2)}checked="checked"{/if}>
									Android
								</label>
							</div>
						</div>
						<div class="form-group">
							<label for="">版本号：</label>
							<input type="text" class="form-control" name="version_code" placeholder=""
								   value="{if isset($data['VersionCode'])}{$data['VersionCode']}{/if}">
						</div>
						<div class="form-group">
							<label for="">版本名称：</label>
							<input type="text" class="form-control" name="version_name" placeholder=""
								   value="{if isset($data['VersionName'])}{$data['VersionName']}{/if}"">
						</div>
						<div class="form-group">
							<label for="">强制升级：</label>
							<div class="radio">
								<label>
									<input type="radio" name="force_update" value="1"
										   {if isset($data['ForceUpdate']) && $data['ForceUpdate'] == 1}checked="checked"{/if}>
									是
								</label>
							</div>
							<div class="radio">
								<label>
									<input type="radio" name="force_update" value="0"
										   {if !isset($data['ForceUpdate']) || (isset($data['ForceUpdate']) && $data['ForceUpdate'] == 0)}checked="checked"{/if}>
									否
								</label>
							</div>
						</div>
						<div class="form-group">
							<label for="">上传：</label>
							<input type="file" name="package">
						</div>
						<div class="form-group">
							<label for="">更新说明：</label>
							<textarea class="form-control" name="update_info" rows="5" placeholder="">{if isset($data['UpdateInfo'])}{$data['UpdateInfo']}{/if}</textarea>
						</div>
					</div><!-- /.box-body -->

					<div class="box-footer">
						<button type="submit" class="btn btn-primary">Submit</button>
					</div>
				</form>
			</div><!-- /.box -->

		</div>
	</div>
	<!-- /.row -->

</section><!-- /.content -->
{include file='admin/layout/footer.tpl'}
</body>
</html>