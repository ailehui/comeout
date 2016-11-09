<?php 
$provinces_list=get_all_provinces();
?>
<!-- 检索 -->
<form class="form-inline" action="<?php echo site_urlc($this->class.'/search/'.$this->cid); ?>" method="GET">
<select name="provinces" onchange='change_citys();' id='provinces'>
	<option value ="">全部</option>
	<?php if(isset($provinces_list) && !empty($provinces_list)){foreach($provinces_list as $k=>$v){?>
	<option value ="<?php echo $v['id']?>" ><?php echo $v['title']?></option>
	<?php }}?>
</select>
<select name="citys" onchange='change_areas();' id='citys'>
	<option value ="">全部</option>
</select>
<select name="areas"  id='areas'>
	<option value ="">全部</option>
</select>
<input class="btn" type="submit" value="检索">
</form>

<!-- 检索 -->
<script type="text/javascript">
require(['adminer/js/ui','adminer/js/media','bootstrap-datetimepicker.zh'],function(ui,media){
    // timepick
    $('.timepicker').datetimepicker({'language':'zh-CN','format':'yyyy/mm/dd hh:ii:ss','todayHighlight':true});
    $("#provinces option").eq(0).attr("selected","selected");
});
function change_citys(){
	$.ajax({
	url: '<?php echo ADMINER_URL.'index.php/citys/changeCitys/'.$this->cid;?>',
	type: 'POST',
	dataType: 'JSON',
	data: {
		data:$('#provinces').val(),
		'<?php echo $this->security->get_csrf_token_name()?>':'<?php echo $this->security->get_csrf_hash()?>'
	}	
	})
	.done(function(data) {
		
		var text = "<option value =''>全部</option>";
		for (var i=0;i<data.length;i++)
		{
			text += "<option value ="+data[i].id+">"+data[i].title+"</option>";
		}
		$('#citys').empty();
		$('#citys').prepend(text);
		$('#areas').empty();
		$('#areas').prepend("<option value =''>全部</option>");
			
	})
}
function change_areas(){
	$.ajax({
	url: '<?php echo ADMINER_URL.'index.php/citys/changeAreas/'.$this->cid;?>',
	type: 'POST',
	dataType: 'JSON',
	data: {
		data:$('#citys').val(),
		'<?php echo $this->security->get_csrf_token_name()?>':'<?php echo $this->security->get_csrf_hash()?>'
	}	
	})
	.done(function(data) {
		
		var text = "<option value =''>全部</option>";
		for (var i=0;i<data.length;i++)
		{
			text += "<option value ="+data[i].id+">"+data[i].title+"</option>";
		}
		$('#areas').empty();
		$('#areas').prepend(text);
		
	})
}
</script>

