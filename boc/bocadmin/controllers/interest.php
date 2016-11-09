<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Class Article extends CI_Controller
 * @author
 * 信息类
 */
class Interest extends Modules_Controller
{

	function __construct()
	{	
		
		parent::__construct();
		$this->load->model('interest_model', 'interest');
		$this->rules = array(
			"rule" => array(
				array(
					"field" => "title",
					"label" => lang('title'),
					"rules" => "trim|required|callback_title_check"
				)
				,array(
					"field" => "timeline",
					"label" => lang('time'),
					"rules" => "trim|strtotime"
				)
			)
		);
	}
	public function title_check() {
		$form=$this->input->post();
		//获取手机验证码
		if (isset($form['id'])) {
			if ($this->interest->check_title($form['title'],$form['id'])) {
				$this->form_validation->set_message('title_check', '兴趣名称不能重复');
				return FALSE;
			}
			return TRUE;
		}else{
			if ($this->interest->check_title($form['title'])) {
				$this->form_validation->set_message('title_check', '兴趣名称不能重复');
				return FALSE;
			}
			return TRUE;
		}
	}
	public function copypro()
    {
        $ids = $this->input->post('ids');
       
        $rs=$this->model->get_one($ids);

        unset($rs['id']);
        unset($rs['sort_id']);
        unset($rs['timeline']);
        
        $id = $this->model->create($rs);
        if ($id) {
            $vdata['msg'] = '复制成功，请刷新查看';
            $vdata['status'] = 1;
        }else{
            $vdata['msg'] = '复制失败，请刷新后重试';
            $vdata['status'] = 0;
        }
        $this->output->set_content_type('application/json')->set_output(json_encode($vdata));
    }

}
