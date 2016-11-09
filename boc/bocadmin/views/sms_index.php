

<?php include_once 'inc_modules_path.php'; ?>
<?php include_once 'inc_sms_search_index.php'; ?>
<?php include_once 'inc_ctype_index.php'; ?>

<div class="clearfix"><p></p></div>

<div class="boxed">
	<div class="boxed-inner seamless">

<table class="table table-striped table-hover select-list">
	<thead>
		<tr>
			<th>短信分类</th>
			<th>主办方</th>
			<th>活动名称</th>
			<th>接收手机</th>
			<th>发送内容</th>
			<th>发送状态</th>
			<th>发送时间</th>
		</tr>
	</thead>
	<tbody class="sort-list">
		<?php foreach ($list as $v):?>
		<?php 
		$sms_type=getSMS_type($v['sms_type']);
		$send_status=$v['send_status']==1?'发送成功':'发送失败';
		?>
		<tr>
			<td><?php echo $sms_type?></td>
			<td><?php echo $v['host']?></td>
			<td><?php echo $v['aname']?></td>
			<td><?php echo $v['phone']?></td>
			<td><?php echo $v['content']?></td>
			<td><?php echo $send_status?></td>
			<td><?php echo date("Y-m-d H:i:s", $v['time']); ?></td>
		</tr>
		<?php endforeach;?>
	</tbody>
</table>

	</div>
</div>



<?php echo $pages; ?>

<script>

</script>
