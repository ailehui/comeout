<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class interest_model extends MY_Model {
    protected $table = 'interest'; 

    // 检测兴趣名称是否存在
    public function check_title($title,$id="")
    {	
    	if($id!=""){
    		$count = $this->db->where (array('id !='=>$id,'title'=>$title))->from ($this->table)->count_all_results ();
    	}else{
    		$count = $this->db->where (array('title'=>$title))->from ($this->table)->count_all_results ();
    	}
		if($count){
			return true;
		}else{
			return false;
		}
    } 
}