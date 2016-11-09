<?php 

?>

<?php include_once 'inc_modules_path.php'; ?>
<?php include_once 'inc_provinces_search_index.php'; ?>
<?php include_once 'inc_ctype_index.php'; ?>

<div class="clearfix"><p></p></div>

<div class="boxed">
	<div class="boxed-inner seamless">

<table class="table table-striped table-hover select-list">
	<thead>
		<tr>
			<th class="width-small"><input id='selectbox-all' type="checkbox" > </th>
			<th>省</th>
			<th class="span1">操作</th>
		</tr>
	</thead>
	<tbody class="sort-list">
		<?php foreach ($list as $v):?>
		<?php 
		
		?>
		<tr data-id="<?php echo $v['id'] ?>" data-sort="<?php echo $v['sort_id'] ?>">
			<td><input class="select-it" type="checkbox" value="<?php echo $v['id']; ?>" ></td>
			<td><?php echo $v['shortname']?></td>
			<td>
				<div class="btn-group">
					<?php include 'inc_ui_audit.php'; ?>
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
		url_audit: "<?php echo site_urlc('provinces/audit'); ?>"
		,url_flag: "<?php echo site_urlc('provinces/flag'); ?>"
	};
	ui.btn_audit(article.url_audit);	// 审核
	ui.btn_flag(article.url_flag);		// 推荐
});
</script>
