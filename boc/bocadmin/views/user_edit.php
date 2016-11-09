
<div class="btn-group"><a href="<?php echo site_urlc('user/index');?>" class="btn"> <i class="fa fa-arrow-left"></i> <?php echo lang('back_list')?> </a></div>

<?php include_once 'inc_form_errors.php'; ?>

<div class="boxed">
	<h3> <i class="fa fa-pencil"></i> 会员详情 <span class="badge badge-success pull-right"><?php echo $title; ?></span></h3>
	<?php echo form_open(current_urlc(), array('class' => 'form-horizontal', 'id' => 'frm-edit')); ?>
<?php 


?>
		<div class="boxed-inner seamless">
		
			<div class="control-group">
				<label for="title" class="control-label">会员ID：</label>
				<div class="controls">
					<?php echo $it['id']?>
				</div>
			</div>
			<div class="control-group">
				<label for="title" class="control-label">手机号码：</label>
				<div class="controls">
					<?php echo $it['phone']?>
				</div>
			</div>
			<div class="control-group">
				<label for="title" class="control-label">头像：</label>
				<div class="controls">
				<?php if(is_file(UPLOAD_PATH.$it['thumb'])){?>
					<a class="fancybox-img" href="<?php echo UPLOAD_URL.$it['thumb']; ?>" title=""
                       data-original-title="点击查看">
                        <img src="<?php echo UPLOAD_URL.$it['thumb']; ?>" width="80px">
            		</a>
            	<?php }?>
				</div>
			</div>
			<div class="control-group">
				<label for="title" class="control-label">注册时间：</label>
				<div class="controls">
					<?php echo date("Y-m-d H:i:s", $it['reg_time']); ?>
				</div>
			</div>
			
        </div>

			

		</div>
		<div class="boxed-footer">
			<?php if ($this->ccid): ?>
			<input type="hidden" name="ccid" value="<?php echo $this->ccid ?>">
			<?php endif ?>
			<input type="hidden" name="cid" value="<?php echo $this->cid ?>">
			<input type="hidden" name="id" value="<?php echo $it['id']?>">
			
		</div>
	</form>
</div>

<!-- 注意加载顺序 -->
<?php include_once 'inc_ui_media.php'; ?>
<script type="text/javascript">
require(['adminer/js/ui','adminer/js/media','bootstrap-datetimepicker.zh'],function(ui,media){
	$('.timepicker').datetimepicker({'language':'zh-CN','format':'yyyy/mm/dd hh:ii:ss','todayHighlight':true});
	ui.fancybox_img();
});
</script>
