<?php 
$ctype_array=array(
		'0'=>array('id'=>1,'title'=>'用户自主'),
		'1'=>array('id'=>2,'title'=>'活动相关'),
);
$send_status_array=array(
		'0'=>array('id'=>1,'title'=>'发送成功'),
		'1'=>array('id'=>2,'title'=>'发送失败'),
);
?>
<!-- 检索 -->
<form class="form-inline" action="<?php echo site_urlc($this->class.'/search/'.$this->cid); ?>" method="GET">
<?php 
//短信分类
if (isset($_GET['ctype'])) {
	echo ui_btn_select('ctype',set_value("ctype",$_GET['ctype']),$ctype_array);
}else{
	echo ui_btn_select('ctype',set_value("ctype"),$ctype_array);
}
if (isset($_GET['send_status'])) {
	echo ui_btn_select('send_status',set_value("send_status",$_GET['send_status']),$send_status_array);
}else{
	echo ui_btn_select('send_status',set_value("send_status"),$send_status_array);
}
?>
<p></p>
<input type="text" name="host" width="50px" value="<?php echo isset($_GET['host'])?$_GET['host']:''?>" placeholder="主办方"/>
<input type="text" name="aname" width="50px" value="<?php echo isset($_GET['aname'])?$_GET['aname']:''?>" placeholder="活动名称"/>

<input class="btn" type="submit" value="检索">
</form>

<!-- 检索 -->
<script type="text/javascript">
require(['adminer/js/ui','adminer/js/media','bootstrap-datetimepicker.zh'],function(ui,media){
    // timepick
    $('.timepicker').datetimepicker({'language':'zh-CN','format':'yyyy/mm/dd hh:ii:ss','todayHighlight':true});
});
</script>

