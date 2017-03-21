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
                    <h3 class="box-title">更换图片</h3>
                </div><!-- /.box-header -->
                <!-- form start -->
                <form role="form" action="/admin/theme/img_change" method="post" enctype="multipart/form-data">
                    <div class="box-body">
                        <div class="form-group">
                            {if isset($errors)}
                            {foreach $errors as $error}
                            <div style="text-align: center;color: red">{$error}</div>
                            {/foreach}
                            {/if}
                        </div>
                        <div class="form-group">
                            <label for="">图片：</label>
                            <input type="file" name="image" onchange="preview(this)">
							<img src="{if isset($data['image'])}{$data['image']}{/if}"
								 style="margin: 10px 0; width: 100px;"/>
							<span style="margin: 0 20px;">==></span>
							<div id="preview" style="display: inline-block"/>
                        </div>
                    </div><!-- /.box-body -->

                    <div class="box-footer">
						{if isset($data['id'])}<input type="hidden" name="id" value="{$data['id']}"/>{/if}
						{if isset($data['type'])}<input type="hidden" name="type" value="{$data['type']}"/>{/if}
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
	function preview(file)
	{
		var prevDiv = document.getElementById('preview');
		if (file.files && file.files[0])
		{
			var reader = new FileReader();
			reader.onload = function (evt)
			{
				prevDiv.innerHTML = '<img width="100" src="' + evt.target.result + '" />';
			}
			reader.readAsDataURL(file.files[0]);
		}
		else
		{
			prevDiv.innerHTML = '<div class="img" style="filter:progid:DXImageTransform.Microsoft.AlphaImageLoader(sizingMethod=scale,src=\'' + file.value + '\'"></div>';
		}
	}
</script>
</body>
</html>