<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Class Article extends CI_Controller
 * @author
 * 信息类
 */
class Sms extends Modules_Controller
{

	function __construct()
	{
		parent::__construct();

		

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
	
    //搜索
    public function search($cid=false,$page=1){
    	$vdata['cpath']= $this->mcol->get_path_more($this->cid);
    	$vdata['cchildren'] = $this->mcol->get_cols($this->cid);
    	$title = $this->mcol->get_one($this->cid,"title");
    	$vdata['title'] = $title['title'];
    	$limit = $this->page_limit;
    	$this->input->get('limit',TRUE) and is_numeric($this->input->get('limit')) AND $limit = $this->input->get('limit');
    	$order = $this->input->get('order',TRUE) ? $this->input->get('order',TRUE):FALSE;
    	$order='sort_id desc';
    	$where = array();
    	$where['cid'] = $this->cid;
    	if ($wh = $this->_search_where()) {
    		$where = array_merge($where,$wh);
    	}
    	//短信分类
    	if($this->input->get('ctype')){
    		$where=array_merge($where,array('ctype'=>$this->input->get('ctype')));
    	}
    	//发送状态
    	if($this->input->get('send_status')){
    		$where=array_merge($where,array('send_status'=>$this->input->get('send_status')));
    	}
    	//主办方
    	if($this->input->get('host')){
    		$where=array_merge($where,array('host like'=>'%'.$this->input->get('host',TRUE).'%'));
    	}
    	//活动名称
    	if($this->input->get('aname')){
    		$where=array_merge($where,array('aname like'=>'%'.$this->input->get('aname',TRUE).'%'));
    	}
    	
    	$vdata['pages'] = $this->_pages(site_url($this->class.'/search/'.$this->cid.'/'),$limit,$where,4);
    	$vdata['list'] = $this->model->get_list($limit,$limit*($page-1),$order,$where);
    	$this->_display($vdata,$this->class.'_index.php');
    }
}
