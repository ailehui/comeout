<!-- 
<?php if(in_array($_GET['c'],array(0))){?>
<?php }?>
-->
<div class="btn-group"><a href="<?php echo site_urlc('interest/index');?>" class="btn"> <i class="fa fa-arrow-left"></i> <?php echo lang('back_list')?> </a></div>

<?php include_once 'inc_form_errors.php'; ?>

<div class="boxed">
	<h3> <i class="fa fa-pencil"></i> 编辑 <span class="badge badge-success pull-right"><?php echo $title; ?></span></h3>
	<?php echo form_open(current_urlc(), array('class' => 'form-horizontal', 'id' => 'frm-edit')); ?>

		<div class="boxed-inner seamless">

			<div class="control-group">
				<label for="title" class="control-label">标题</label>
				<div class="controls">
					<input type="text" name="title" id="title" value="<?php echo set_value('title',$it['title']); ?>"  placeholder="栏目名称" required=1>
					
					<span class="help-inline"></span>
				</div>
			</div>

			

			<div class="control-group">
				<label for="title" class="control-label">更新时间</label>
				<div class="controls">
					<div class="input-append date timepicker">
						<input type="text" value="<?php echo date("Y/m/d H:i:s",set_value('timeline',$it['timeline'])); ?>" id="timeline" name="timeline">
						<span class="add-on"><i class="icon-th"></i></span>
					</div>
				</div>
			</div>

			<!-- switch demo -->
			<div class="control-group">
				<label class="control-label" for="status"><?php echo lang('status') ?></label>
				<div class="controls">
					<?php
					$status = array(
						array(
							'title' => "草稿"
							,'value' => '0'
						)
						,array(
							'title' => "发布"
							,'value' => '1'
						)
					);
					echo ui_btn_switch('status',$it['status'],$status);
					?>
					<span class="help-inline"></span>
				</div>
			</div>
		</div>
		<div class="boxed-footer">
			<?php if ($this->ccid): ?>
			<input type="hidden" name="ccid" value="<?php echo $this->ccid ?>">
			<?php endif ?>
			<input type="hidden" name="cid" value="<?php echo $this->cid ?>">
			<input type="hidden" name="id" value="<?php echo $it['id']?>">
			<input type="submit" value="<?php echo lang('submit') ?>" class="btn btn-primary">
			<input type="reset" value="<?php echo lang('reset') ?>" class="btn btn-danger">
		</div>
	</form>
</div>

<!-- 多图上传修改
<input class="fileupload" type="file" accept="" multiple="multiple" data-for="photo" data-size="">
data-more="1"
list_upload
-->

<!-- 注意加载顺序 -->
<?php include_once 'inc_ui_media.php'; ?>
<script type="text/javascript">
require(['adminer/js/ui','adminer/js/media','bootstrap-datetimepicker.zh'],function(ui,media){
	$('.timepicker').datetimepicker({'language':'zh-CN','format':'yyyy/mm/dd hh:ii:ss','todayHighlight':true});
	ui.editor_create('content');

	media.init();
	var articles_photos = <?php echo json_encode(one_upload($it['photo'])) ?>;
	media.show(articles_photos,"photo");
});
</script>
