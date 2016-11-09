
<!-- 检索 -->
<form class="form-inline" action="<?php echo site_urlc($this->class.'/search/'.$this->cid); ?>" method="GET">
<input type="text" name="id" width="50px" value="<?php echo isset($_GET['id'])?$_GET['id']:''?>" placeholder="输入会员ID"/>
<input type="text" name="phone" width="50px" value="<?php echo isset($_GET['phone'])?$_GET['phone']:''?>" placeholder="输入手机号码"/>
<p></p>
<input type="text" name="sid" width="50px" value="<?php echo isset($_GET['sid'])?$_GET['sid']:''?>" placeholder="开始ID"/>至
<input type="text" name="eid" width="50px" value="<?php echo isset($_GET['eid'])?$_GET['eid']:''?>" placeholder="结束ID"/>
<p></p>
 <div class="input-append date timepicker">
    <input type="text"  placeholder="开始时间" value="<?php echo isset($_GET['starttime'])?$_GET['starttime']:''?>"  id="starttime" name="starttime" data-date-format="yyyy-mm-dd hh:ii:ss">
    <span class="add-on"><i class="icon-th"></i></span>
</div>至
 <div class="input-append date timepicker">
  
    <input type="text" placeholder="结束时间" value="<?php echo isset($_GET['endtime'])?$_GET['endtime']:''?>" id="endtime" name="endtime" data-date-format="yyyy-mm-dd hh:ii:ss">
    <span class="add-on"><i class="icon-th"></i></span>
</div>
<input class="btn" type="submit" value="检索">
</form>

<!-- 检索 -->
<script type="text/javascript">
require(['adminer/js/ui','adminer/js/media','bootstrap-datetimepicker.zh'],function(ui,media){
    // timepick
    $('.timepicker').datetimepicker({'language':'zh-CN','format':'yyyy/mm/dd hh:ii:ss','todayHighlight':true});
});
</script>

