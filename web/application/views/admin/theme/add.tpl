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
			<div class="box box-primary">
				<div class="box-header with-border">
					<h3 class="box-title">添加主题</h3>
				</div><!-- /.box-header -->
				<!-- form start -->
				<form role="form" action="/admin/theme/add" method="post" enctype="multipart/form-data">
					<div class="box-body">
						<div class="form-group">
							{if isset($errors)}
								{foreach $errors as $error}
									<div style="text-align: center;color: red">{$error}</div>
								{/foreach}
							{/if}
						</div>
						<div class="form-group">
							<label for="">类型：</label>
							<div class="radio">
								<label>
									<input type="radio" name="type" value="1"
										   {if !isset($data['Type']) || (isset($data['Type']) && $data['Type'] == 1)}checked="checked"{/if}>
									Web主题
								</label>
							</div>
							<div class="radio">
								<label>
									<input type="radio" name="type" value="2"
										   {if isset($data['Type']) && $data['Type'] == 2}checked="checked"{/if}>
									APP主题
								</label>
							</div>
						</div>
						<div class="form-group">
							<label for="">名称：</label>
							<input type="text" class="form-control" name="name"
								   placeholder="" maxlength="64"
								   value="{if isset($data['Name'])}{$data['Name']}{/if}">
						</div>
						<div class="form-group">
							<label for="">导航颜色：</label>
							<input type="text" class="form-control" name="color"
								   placeholder="" maxlength="7"
								   value="{if isset($data['Color'])}{$data['Color']}{/if}">
						</div>
                        <div class="form-group">
                            <label for="">图标颜色：</label>
                            <input type="text" class="form-control" name="icon_color"
                                   placeholder="" maxlength="7"
                                   value="{if isset($data['IconColor'])}{$data['IconColor']}{/if}">
                        </div>
                        <div class="form-group">
                            <label for="">字体颜色：</label>
                            <input type="text" class="form-control" name="font_color"
                                   placeholder="" maxlength="7"
                                   value="{if isset($data['FontColor'])}{$data['FontColor']}{/if}">
                        </div>
                        <div class="form-group">
                            <label for="">背景颜色：</label>
                            <input type="text" class="form-control" name="bg_color"
                                   placeholder="" maxlength="7"
                                   value="{if isset($data['BgColor'])}{$data['BgColor']}{/if}">
                        </div>
						<div class="form-group" name="thumb">
							<label id="file_label" for="">缩略图：</label>
							<input type="file" name="thumb">
						</div>
						<div class="form-group" name="image"
							 style="display:{if isset($data['Type']) && $data['Type'] == 2}none{/if}">
							<label id="file_label" for="">背景图：</label>
							<input type="file" name="image">
						</div>
						<div class="form-group" name="app_type"
							 style="display:{if !isset($data['Type']) || $data['Type'] == 1}none{/if}">
							<label for="">Android安装包：</label>
							<input type="file" name="android">
						</div>
						<div class="form-group" name="app_type"
							 style="display:{if !isset($data['Type']) || $data['Type'] == 1}none{/if}">
							<label for="">IOS安装包：</label>
							<input type="file" name="ios">
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
<script>
    //Colorpicker
    var cp_option = {
        format: 'hex',
    };
    $("input[name$=color]").colorpicker(cp_option);

	$('input[name=type]').change(function ()
	{
		var type = $('input[name=type]:checked').val();
		if (type == 1)
		{
			$('div[name=app_type]').css('display', 'none');
			$('div[name=image]').css('display', '');
		}
		else if (type == 2)
		{
			$('div[name=app_type]').css('display', '');
			$('div[name=image]').css('display', 'none');
		}
	});

</script>
</body>
</html>