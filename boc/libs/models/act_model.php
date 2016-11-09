<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class act_model extends MY_Model {
	
    protected $table = 'act'; 
    protected $sms_table = 'sms';
    protected $interest_table = 'interest';

    // 获取后台所有活动类型
    public function get_all_interest()
    {
    	$data=$this->db->select('id,title')->where(array('audit'=>1))
    	->order_by('sort_id', 'DESC')->get($this->interest_table)->result_array();
    	return $data;
    }
}


	
    
