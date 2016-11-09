<?php 

?>
<!-- 检索 -->
<form class="form-inline" action="<?php echo site_urlc($this->class.'/search/'.$this->cid); ?>" method="GET">
<input type="text" name="province" width="50px" value="<?php echo isset($_GET['province'])?$_GET['province']:''?>" placeholder="省"/>
<input class="btn" type="submit" value="检索">
</form>

<!-- 检索 -->
<script type="text/javascript">
require(['adminer/js/ui','adminer/js/media','bootstrap-datetimepicker.zh'],function(ui,media){
    // timepick
    $('.timepicker').datetimepicker({'language':'zh-CN','format':'yyyy/mm/dd hh:ii:ss','todayHighlight':true});
});
</script>

