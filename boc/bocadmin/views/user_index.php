

<?php include_once 'inc_modules_path.php'; ?>
<?php include_once 'inc_user_search_index.php'; ?>
<?php include_once 'inc_ctype_index.php'; ?>

<div class="clearfix"><p></p></div>

<div class="boxed">
	<div class="boxed-inner seamless">

<table class="table table-striped table-hover select-list">
	<thead>
		<tr>
			<th class="width-small"><input id='selectbox-all' type="checkbox" > </th>
			<th>会员ID</th>
			<th>手机号码</th>
			<th>注册时间</th>
			
			<th class="span1">操作</th>
		</tr>
	</thead>
	<tbody class="sort-list">
		<?php foreach ($list as $v):?>
		
		<tr data-id="<?php echo $v['id'] ?>" data-sort="<?php echo $v['sort_id'] ?>">
			<td><input class="select-it" type="checkbox" value="<?php echo $v['id']; ?>" ></td>
			<td><?php echo $v['id']?></td>
			<td><?php echo $v['phone']?></td>
			<td><?php echo date("Y-m-d H:i:s", $v['reg_time']); ?></td>
			</td>
			<td>
				<div class="btn-group">
					<?php include 'inc_ui_audit.php'; ?>
					<a class='btn btn-small' href=" <?php echo site_urlc( $this->router->class.'/edit/'.$v['id']) ?> " title="<?php echo lang('edit') ?>"> <i class="fa fa-pencil"></i> <?php // echo lang('edit') ?></a>
					<a class='btn btn-danger btn-small btn-del' data-id="<?php echo $v['id'] ?>" href="#"  title="<?php echo lang('del') ?>"> <i class="fa fa-times"></i> <?php // echo lang('del') ?></a>
				</div>
			</td>
		</tr>
		<?php endforeach;?>
	</tbody>
</table>

	</div>
</div>



<?php echo $pages; ?>

<script>
require(['adminer/js/ui'],function(ui){
	var article = {
		url_audit: "<?php echo site_urlc('user/audit'); ?>"
	};
	ui.btn_audit(article.url_audit);	// 审核
});
</script>
