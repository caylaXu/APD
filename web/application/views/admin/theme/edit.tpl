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
					<h3 class="box-title">编辑主题</h3>
				</div><!-- /.box-header -->
				<!-- form start -->
				<form role="form" action="/admin/theme/edit" method="post">
					<div class="box-body">
						<div class="form-group">
							{if isset($errors)}
								{foreach $errors as $error}
									<div style="text-align: center;color: red">{$error}</div>
								{/foreach}
							{/if}
						</div>
						<div class="form-group">
							<label for="">状态：</label>
							<div class="radio">
								<label>
									<input type="radio" name="Status" value="1"
										   {if !isset($data['Status']) || (isset($data['Status']) && $data['Status'] == 1)}checked="checked"{/if}>
									激活
								</label>
							</div>
							<div class="radio">
								<label>
									<input type="radio" name="Status" value="0"
										   {if isset($data['Status']) && $data['Status'] == 0}checked="checked"{/if}>
									禁用
								</label>
							</div>
						</div>
						<div class="form-group">
							<label for="">名称：</label>
							<input type="text" class="form-control" name="Name"
								   placeholder="" maxlength="64"
								   value="{if isset($data['Theme'])}{$data['Theme']}{/if}">
						</div>
						<div class="form-group">
							<label for="">导航颜色：</label>
							<input type="text" class="form-control" name="Color"
								   placeholder="" maxlength="7"
								   value="{if isset($data['Color'])}{$data['Color']}{/if}">
						</div>
                        <div class="form-group">
                            <label for="">图标颜色：</label>
                            <input type="text" class="form-control" name="IconColor"
                                   placeholder="" maxlength="7"
                                   value="{if isset($data['IconColor'])}{$data['IconColor']}{/if}">
                        </div>
                        <div class="form-group">
                            <label for="">字体颜色：</label>
                            <input type="text" class="form-control" name="FontColor"
                                   placeholder="" maxlength="7"
                                   value="{if isset($data['FontColor'])}{$data['FontColor']}{/if}">
                        </div>
                        <div class="form-group">
                            <label for="">背景颜色：</label>
                            <input type="text" class="form-control" name="BgColor"
                                   placeholder="" maxlength="7"
                                   value="{if isset($data['BgColor'])}{$data['BgColor']}{/if}">
                        </div>
					</div><!-- /.box-body -->

					<div class="box-footer">
						{if isset($data['Id'])}<input type="hidden" name="Id" value="{$data['Id']}"/>{/if}
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
    $("input[name$=Color]").colorpicker();
</script>
</body>
</html>