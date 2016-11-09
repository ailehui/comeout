<?php 

?>

<?php include_once 'inc_modules_path.php'; ?>
<?php include_once 'inc_citys_search_index.php'; ?>
<?php include_once 'inc_ctype_index.php'; ?>

<div class="clearfix"><p></p></div>

<div class="boxed">
	<div class="boxed-inner seamless">

<table class="table table-striped table-hover select-list">
	<thead>
		<tr>
			<th class="width-small"><input id='selectbox-all' type="checkbox" > </th>
			<th>省</th>
			<th>市</th>
			<th>首字母</th>
			<th class="span1">操作</th>
		</tr>
	</thead>
	<tbody class="sort-list">
		<?php foreach ($list as $v):?>
		
		<tr data-id="<?php echo $v['id'] ?>" data-sort="<?php echo $v['sort_id'] ?>">
			<td><input class="select-it" type="checkbox" value="<?php echo $v['id']; ?>" ></td>
			<td><?php echo get_province_for_city($v['parentid']);?></td>
			<td><?php echo $v['shortname']?></td>
			<td><input type="text" class="sortid szm" value="<?php echo $v['szm'];?>" data-id="<?php echo $v['id'] ?>"></td>
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
		url_audit: "<?php echo site_urlc('citys/audit'); ?>"
		,url_flag: "<?php echo site_urlc('citys/flag'); ?>"
		,url_szm_change: "<?php echo site_urlc('citys/szm_change'); ?>"
		
	};
	ui.btn_audit(article.url_audit);	// 审核
	ui.btn_flag(article.url_flag);		// 推荐
	ui.szm_change(article.url_szm_change); // input 排序
});
</script>
