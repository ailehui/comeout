<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * 画册
 */
class gallery extends Modules_Controller
{
   	protected $rules = array(
		"rule" => array(
			array(
				"field" => "title",
				"label" => "标题",
				"rules" => "trim"
			),
			array(
				"field" => "photo",
				"label" => "图片",
				"rules" => "trim"
			),
			array(
				"field" => "thumb",
				"label" => "缩略图",
				"rules" => "trim"
			)
		)
	);

	public function _edit_data()
	{
		$form=$this->input->post();
		$form['timeline'] = time();
		return $form;
	}

	// 删除条目时删除文件
	protected function _rm_file($ids)
	{
		// 获取文件字段
		$fids = array() ;
		if (is_numeric($ids)) {
			$tmp = $this->model->get_one($ids,'photo');
			$fids = explode(',',$tmp['photo']);
		}else if(is_array($ids)){
			// 使用 字符串where时
			$tmp = $this->model->get_all("`id` in (".implode(',', $ids).")",'photo');
			foreach ($tmp as $key => $v) {
				$fids = array_merge($fids, explode(',',$v['photo']));
			}
		}
		// adminer funs helpers
		// 删除文件
		unlink_upload($fids);
	}
}
