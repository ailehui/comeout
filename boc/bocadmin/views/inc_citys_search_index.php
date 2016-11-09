<?php 
$provinces_list=get_all_provinces();
?>
<!-- 检索 -->
<form class="form-inline" action="<?php echo site_urlc($this->class.'/search/'.$this->cid); ?>" method="GET">
<?php 
//短信分类
if (isset($_GET['provinces'])) {
	echo ui_btn_select('provinces',set_value("provinces",$_GET['provinces']),$provinces_list);
}else{
	echo ui_btn_select('provinces',set_value("provinces"),$provinces_list);
}
?>
<input class="btn" type="submit" value="检索">
</form>

<!-- 检索 -->
<script type="text/javascript">
require(['adminer/js/ui','adminer/js/media','bootstrap-datetimepicker.zh'],function(ui,media){
    // timepick
    $('.timepicker').datetimepicker({'language':'zh-CN','format':'yyyy/mm/dd hh:ii:ss','todayHighlight':true});
});
</script>

