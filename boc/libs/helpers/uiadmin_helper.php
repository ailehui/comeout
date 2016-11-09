<?php
// 主要放置后台 UI 生成器

if (!function_exists('ui_btn_switch')) {
	/**
	 * btn swtich 生成器
	 * @param  string field 字段
	 * @param  any default 默认值
	 * @param  array $arr 列表表  [{title:xxx,value:xxx}],分类 会将 value 取 id
	 * @return html
	 */
	function ui_btn_switch($field=false,$default=false,$arr=false){
		if ($field ===false or $arr===false or $default===false) {
			return false;
		}
		$tmp = '<div class="btn-group btn-switch">';
		// 保证array中有  title ,value
		foreach ($arr as $k => $v) {
			// 针对分类
			if (isset($v['id'])) {
				$value = $v['id'];
			}else{
				$value = $v['value'];
			}

			$class= set_switch($field,$value,$default,'btn-primary','');
			$tmp .= '<a href="#" data-value="'.$value.'" class="btn '.$class.'">'.$v['title'].'</a>';
		}
		$tmp .= '</div>';
		$tmp .= '<input type="hidden" id="'.$field.'" name="'.$field.'" value="'.set_value($field,$default).'">';
		return $tmp;
	}
}


/**
 * btn checkedbox 生成器
 * @param  string field 字段
 * @param  any default 默认值
 * @param  array $arr 列表表  [{title:xxx,value:xxx}],分类 会将 value 取 id
 * @return html
 * 例子: 对多选分类 , 在 表单验证规则中使用 `ctype[]` 带有 `[]`的样式进行处理.
 	<div class="control-group">
        <label for="ctype[]" class="control-label">类型:</label>
        <div class="controls">
        <?php 
        $ctype=list_coltypes($this->cid,0,'ctype');
        echo ui_btn_checkedbox('ctype[]','',$ctype);
        ?>
        </div>
    </div>
 * 
 */
function ui_btn_checkedbox($field=false,$default=false,$arr=false){
	if ($field ===false or $arr===false or $default===false) {
		return false;
	}
	$str = '<div class="btn-group ui-checkbox" data-toggle="buttons-checkbox">';

	$check_list  =   set_value($field,$default);
    if (!is_array($check_list)) {
        $check_list = explode(',',$check_list);
    }
    foreach ($arr as $k => $v){
    	if (isset($v['id'])) {
    		$value = $v['id'];
    	}else{
    		$value = $v['value'];
    	}
        if (in_array($value,$check_list) ) {
            $str .= '<button type="button" class="btn btn-info active" >';
            $str .= '<input type="checkbox" name="'.$field.'" value="'.$value.'" checked class="hide"> ';
        }else{
            $str .= '<button type="button" class="btn btn-info" >';
            $str .= '<input type="checkbox" name="'.$field.'" value="'.$value.'" class="hide"> ';
        }
        $str .= $v['title'].'</button>';
    } 
	$str.='</div>';;
	return $str;

}

if (!function_exists('ui_btn_select')) {
	/**
	 * btn select 生成器
	 * @param  string field 字段
	 * @param  any default 默认值
	 * @param  array $arr 列表表  [{title:xxx,value:xxx}],分类 会将 value 取 id
	 * @return html
	 */
	function ui_btn_select($field=false,$default=false,$arr=false){
		if ($field ===false or $arr===false or $default===false) {
			return false;
		}

		// $fn = function($v,$o){
		// 	return
		// };

		// $fn = '<option title="{{$v["title"]}}" value="{{$v["value"]}}" '.set_switch($o['field'], $v['value'], $o['default'], ' selected="selected" class="option-select" ','').'>'.$v['op_header'].'&nbsp;'. $v['title'].'</option>'

		$tmp = '<select name="'.$field.'" id="'.$field.'" class="bselect" data-size="auto" data-live-search="true">';
		$tmp .= ui_tree($arr,array('field'=>$field,'default'=>$default));
		$tmp .= '</select>';
		return $tmp;
	}
}


if (!function_exists('ui_btn_coltypes')) {
	/**
	 * 类别按钮
	 * @param  string $ids 上传列表值
	 * @return array       数组
	 */
	function ui_btn_coltypes($cid=0,$field=false){
		if (!is_numeric($cid) or !$field) {
			return false;
		}
		$CI =& get_instance();
		$url = site_url('coltypes/index/').'?cid='.$cid.'&field='.$field.'&rc='.$CI->class;
		$tmp = '<a href="'.$url.'" class="btn btn-info" title="'.lang($field).'">管理'.lang($field).'</a>';
		return $tmp;
	}
}

// TODO:废弃
if (!function_exists('ui_btns_coltypes')) {
	/**
	 * 类别列表按钮
	 * @param  integer $cid     cid
	 * @param  boolean $field   类别字段
	 * @param  boolean $baseurl 基本地址
	 * @return string           按钮组
	 */
	function ui_btns_coltypes($cid=0,$field=false,$baseurl=false){

		$tmp = '<div class="btn-group">';
		$active = false;
		if (isset($_GET[$field])) {
			$active = $_GET[$field];
		}
		if (!$active) {
			$tmp .= '<a href="'.$baseurl.'" class="btn btn-primary">所有</a>';
		}else{
			$tmp .= '<a href="'.$baseurl.'" class="btn">所有</a>';
		}
		$CI =& get_instance();
		$arr = list_coltypes($cid,'typea');
		foreach ($arr as $k => $v) {
			$tmp .='<a href="'.$baseurl.'&'.$field.'='.$v['id'].'" class="btn';
			if ($v['id'] == $active) {
				$tmp .= " btn-primary";
			}
			$tmp .='">'.$v['title'].'</a>';
		}
		$tmp .="</div>";
		return $tmp;
	}
}

// 获取栏目名称
if(!function_exists('tag_columns'))
{
	function tag_columns($id,$column='title')
	{
		static $a=array();
		$id=intval($id);
		if(!isset($a[$id])){
			$CI=&get_instance();
			$CI->load->database();
			$a[$id]=$CI->db->get_where('columns',array('id'=>$id));
			if($a[$id]->num_rows()<1){
				$a[$id]=array();
			}else{
				$a[$id]=$a[$id]->row_array();
			}
		}
		if(isset($a[$id][$column])){
			return $a[$id][$column];
		}
		return '';
	}
}

// 树形数组结构输入
// 栏目列表组织
// list 结构数据
// padding 默认追加的内容
// fn 函数 function($v,$o); $v 为 list 单个信息， $fn_o 为额外内容
function ui_tree($list=false,$fn_o=false,$padding = array()){
	//$option=array('field'=>'','default'=>0)) {

	if (!$list) {
		return "";
	}

	$tree = "";

	foreach ($list as $k => $v) {

		// 追加头部
		$op_header = "";
		// 针对分类-
		if (!isset($v['value'])) {
			$v['value'] = $v['id'];
		}

		$op_header.=implode('', $padding);

		// 如果当序列是最后一个
		if (isset($v['depth']) and $v['depth']) {
			// 如果有下一个
			if (isset($list[$k+1]) and $list[$k+1]['depth'] == $v['depth']) {
					$op_header .= '├';
			} else {
				// 结尾
				$op_header .= '└';
			}
			$op_header .= '';
		}

		if (isset($v['more']) and $v['more']) {
			$op_header .='&nbsp;<span class="fa">&#xf13a;</span>';
		}else{
			$op_header .='&nbsp;<span class="fa">&#xf10c;</span>';
		}

		$v['op_header'] = $op_header;

		// if ($fn !== false) {
			// $tree .= $fn($v,$fn_o);
			$tree.=	'<option title="'.$v['title'].'" value="'.$v['value'].'" '.set_switch($fn_o['field'], $v['value'], $fn_o['default'], ' selected="selected" class="option-select" ','').'>'.$v['op_header'].'&nbsp;'. $v['title'].'</option>';
		// }

		if (isset($v['more']) and is_array($v['more']) ) {
			$p = $padding;
			if (isset($list[$k+1]) and $v['depth']) {
				array_push($p, '│');
			}else{
				if ($v['depth']) {
					array_push($p, '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;');
				}
			}
			$tree .= ui_tree($v['more'],$fn_o,$p);
		}
	}

	return $tree;

}

function ui_tree_col($list=false,$padding = array()){
	//$option=array('field'=>'','default'=>0)) {

	if (!$list) {
		return "";
	}

	$tree = "";

	foreach ($list as $k => $v) {

		// 追加头部
		$op_header = "";
		// 针对分类-
		if (!isset($v['value'])) {
			$v['value'] = $v['id'];
		}

		$op_header.=implode('', $padding);

		// 如果当序列是最后一个
		if (isset($v['depth']) and $v['depth']) {
			// 如果有下一个
			if (isset($list[$k+1]) and $list[$k+1]['depth'] == $v['depth']) {
					$op_header .= '├';
			} else {
				// 结尾
				$op_header .= '└';
			}
			$op_header .= '';
		}

		if (isset($v['more']) and $v['more']) {
			$op_header .='&nbsp;<span class="fa">&#xf13a;</span>';
		}else{
			$op_header .='&nbsp;<span class="fa">&#xf10c;</span>';
		}

		$v['op_header'] = $op_header;


// center
		$tree.= '<li data-id="'.$v['cid'].'" data-sort="'.$v['csort_id'].'">';
		$tree.= '<span> <input class="select-it" type="checkbox" value="'.$v['cid'].'" > </span>';
		// $tree.= '<span class="label"> '.$v['cid'].'</span>';

		$tree .= $v['op_header'];

		$tree.= ' <a href="'.GLOBAL_URL.'index.php/'.$v['path'].'" target="_blank"> <span> '.$v['ctitle'] .' </span></a> - <span class="label label-success">'.$v['cid'].'</span><span class="label" title="'.$v['path'].'" >'.$v['cidentify'].'</span>';
		if ( ENVIRONMENT == "development") {
			$tree.= '<span class="label label-info">'.$v['temp_index'].'</span><span class="label">'.$v['temp_show'].'</span>' ;;
		}
		$tree.= '<div class="btn-group pull-right">';
		if ($v['cshow']){
			$tree .= '<a href="#" class="btn btn-primary btn-small btn-ajax-show" data-id="'.$v['cid'].'" data-show="0">  <i class="fa fa-eye"></i></a>';
		}else{
			$tree .='<a href="#" class="btn btn-small btn-ajax-show" data-id="'.$v['cid'].'" data-show="1"> <i class="fa fa-eye-slash"></i></a>';
		}
		$tree.= '<a class="btn btn-small" href="'.site_url('columns/edit/'.$v['cid']).' " title="'.lang('edit').'"> <i class="fa fa-pencil"></i> </a>';
		$tree.= '<a class="btn btn-small" href="'.site_url('coltypes/index/').'?c='.$v['cid'].'&field=ctype&rc='.$v['controller'].' " title="分类管理"> <i class="fa fa-magnet "></i> </a>';
		$tree.= '<a class="btn btn-danger btn-small btn-del" href="#" title="'.lang('del').'" data-id="'.$v['cid'].'"> <i class="fa fa-times"></i> </a>';
		$tree.= '</div>';
		$tree.= '</li>';
// end center;

		if (isset($v['more']) and is_array($v['more']) ) {
			$p = $padding;
			if (isset($list[$k+1]) and $v['depth']) {
				array_push($p, '│');
			}else{
				if ($v['depth']) {
					array_push($p, '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;');
				}
			}
			$tree .= ui_tree_col($v['more'],$p);
		}
	}

	return $tree;

}

// 类型列表
function ui_tree_coltypes($list=false,$padding = array()){
	//$option=array('field'=>'','default'=>0)) {

	if (!$list) {
		return "";
	}

	$tree = "";

	foreach ($list as $k => $v) {

		// 追加头部
		$op_header = "";
		// 针对分类-
		if (!isset($v['value'])) {
			$v['value'] = $v['id'];
		}

		$op_header.=implode('', $padding);

		// 如果当序列是最后一个
		if (isset($v['depth']) and $v['depth']) {
			// 如果有下一个
			if (isset($list[$k+1]) and $list[$k+1]['depth'] == $v['depth']) {
					$op_header .= '├';
			} else {
				// 结尾
				$op_header .= '└';
			}
			$op_header .= '';
		}

		if (isset($v['more']) and $v['more']) {
			$op_header .='&nbsp;<span class="fa">&#xf13a;</span>';
		}else{
			$op_header .='&nbsp;<span class="fa">&#xf10c;</span>';
		}

		$v['op_header'] = $op_header;

		// center
		$tree.= '<li data-id="'.$v['id'].'" data-sort="'.$v['sort_id'].'">';
		$tree.= '<span> <input class="select-it" type="checkbox" value="'.$v['id'].'" > </span>';

		$tree .= $v['op_header'];

		$tree.= '<span> '.$v['title'] .' </span>';
		if ( ENVIRONMENT == "development") {
			$tree.= '<span class="label label-info"> '.$v['id'].'</span>';
		}
		$tree.= '<div class="btn-group pull-right">';
		$tree.= '<a class="btn btn-small" href="'.site_urlc('coltypes/edit/'.$v['id']).' " title="'.lang('edit').'"> <i class="fa fa-pencil"></i> </a>';
		$tree.= '<a class="btn btn-danger btn-small btn-del" href="#" title="'.lang('del').'" data-id="'.$v['id'].'"> <i class="fa fa-times"></i> </a>';
		$tree.= '</div>';
		$tree.= '</li>';
		// end center;

		if (isset($v['more']) and is_array($v['more']) ) {
			$p = $padding;
			if (isset($list[$k+1]) and $v['depth']) {
				array_push($p, '│');
			}else{
				if ($v['depth']) {
					array_push($p, '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;');
				}
			}
			$tree .= ui_tree_coltypes($v['more'],$p);
		}
	}

	return $tree;

}
function getSMS_type($sms_type){
	if(!in_array($sms_type,array(1,2,3))){
		return '未知';
	}
	if(in_array($sms_type,array(1,2,3))){
		return '用户自主';
	}
	if(in_array($sms_type,array(5))){
		return '活动相关';
	}
}
function get_all_city($city)
    {
        $array=array();
        if (is_array($city))
        {
            for ($i='A';$i<='Z';$i++)
            {
                foreach ($city as $k=>$vo)
                {
                    if ($i == $vo['letter'])
                    {
                        $array[$i][$k] = $vo;
                    }
                }

                if($i == 'Z')
                {
                    break;
                }
            }
        }
        return $array;
    }

function getFirstCharter($str)
{
    if (empty($str)) {
        return '';
    }
    
    $fchar = ord($str{0});
    
    if ($fchar >= ord('A') && $fchar <= ord('z'))
        return strtoupper($str{0});
    
    $s1 = iconv('UTF-8', 'gb2312', $str);
    
    $s2 = iconv('gb2312', 'UTF-8', $s1);
    
    $s = $s2 == $str ? $s1 : $str;
    
    $asc = ord($s{0}) * 256 + ord($s{1}) - 65536;
    
    if ($asc >= -20319 && $asc <= -20284)
        return 'A';
    
    if ($asc >= -20283 && $asc <= -19776)
        return 'B';
    
    if ($asc >= -19775 && $asc <= -19219)
        return 'C';
    
    if ($asc >= -19218 && $asc <= -18711)
        return 'D';
    
    if ($asc >= -18710 && $asc <= -18527)
        return 'E';
    
    if ($asc >= -18526 && $asc <= -18240)
        return 'F';
    
    if ($asc >= -18239 && $asc <= -17923)
        return 'G';
    
    if ($asc >= -17922 && $asc <= -17418)
        return 'H';
    
    if ($asc >= -17417 && $asc <= -16475)
        return 'J';
    
    if ($asc >= -16474 && $asc <= -16213)
        return 'K';
    
    if ($asc >= -16212 && $asc <= -15641)
        return 'L';
    
    if ($asc >= -15640 && $asc <= -15166)
        return 'M';
    
    if ($asc >= -15165 && $asc <= -14923)
        return 'N';
    
    if ($asc >= -14922 && $asc <= -14915)
        return 'O';
    
    if ($asc >= -14914 && $asc <= -14631)
        return 'P';
    
    if ($asc >= -14630 && $asc <= -14150)
        return 'Q';
    
    if ($asc >= -14149 && $asc <= -14091)
        return 'R';
    
    if ($asc >= -14090 && $asc <= -13319)
        return 'S';
    
    if ($asc >= -13318 && $asc <= -12839)
        return 'T';
    
    if ($asc >= -12838 && $asc <= -12557)
        return 'W';
    
    if ($asc >= -12556 && $asc <= -11848)
        return 'X';
    
    if ($asc >= -11847 && $asc <= -11056)
        return 'Y';
    
    if ($asc >= -11055 && $asc <= -10247)
        return 'Z';
    
    return null;
    
}
//获得城市的省份   通过parentid
function get_province_for_city($parentid){
	$CI=&get_instance();
	$provinces=$CI->db->select('shortname')->where(array('id'=>$parentid))->get('provinces')->row_array();
	$provinces=isset($provinces['shortname'])&&!empty($provinces['shortname'])?$provinces['shortname']:'';
	return $provinces;
}
//获得区的城市   通过parentid
function get_city_for_area($parentid){
	$CI=&get_instance();
	$city=$CI->db->select('shortname')->where(array('id'=>$parentid))->get('citys')->row_array();
	$city=isset($city['shortname'])&&!empty($city['shortname'])?$city['shortname']:'';
	return $city;
}
//获得区的省   通过parentid
function get_province_for_area($parentid){
	$CI=&get_instance();
	$city_parentid=$CI->db->select('parentid')->where(array('id'=>$parentid))->get('citys')->row_array();
	$city_parentid=isset($city_parentid['parentid'])&&!empty($city_parentid['parentid'])?$city_parentid['parentid']:'';
	if(empty($city_parentid)){
		return '';
	}
	return get_province_for_city($city_parentid);
}
//获得所有省份
function get_all_provinces(){
	$CI=&get_instance();
	$query = $CI->db->query('select id,shortname as title from boc_provinces order by id asc');
	return $query->result_array();
}
//获得城市列表 根据省份id
function get_citys_list($provinces_id){
	$CI=&get_instance();
	if(empty($provinces_id)){
		return array();
	}
	$query = $CI->db->query('select id,shortname as title from boc_citys where parentid = '.$provinces_id.' order by id asc');
	return $query->result_array();
}
//获得区列表 根据城市id
function get_areas_list($citys_id){
	$CI=&get_instance();
	if(empty($citys_id)){
		return array();
	}
	$query = $CI->db->query('select id,shortname as title from boc_areas where parentid = '.$citys_id.' order by id asc');
	return $query->result_array();
}