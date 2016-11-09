<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Class Article extends CI_Controller
 * @author
 * 信息类
 */
class Areas extends Modules_Controller
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
    	$order='id asc';
    	$where = array();
    	$where['cid'] = $this->cid;
    	if ($wh = $this->_search_where()) {
    		$where = array_merge($where,$wh);
    	}
    	$where = array_merge($where,array('level'=>3));
    	
    	
    	
    	if($this->input->get('areas')){
    		$where = array_merge($where,array('id'=>$this->input->get('areas')));
    	}else{
    		if($this->input->get('citys')){
    			$str='';
    			$areas_list=get_areas_list($this->input->get('citys'));
    			if(!empty($areas_list)){
    				foreach ($areas_list as $key2 => $value2) {
    					$str .= $value2['id'].',';
    				}
    			}
    			$all_str=explode(',',rtrim($str,','));
    			$where = array_merge($where,array('in'=>array('id',$all_str)));
    		}else{
    			if($this->input->get('provinces')){
    				$str='';
    				$area_list=array();
    				$citys_list=get_citys_list($this->input->get('provinces'));
    				if(!empty($citys_list)){
    					foreach ($citys_list as $key => $value) {
    						$area_list= get_areas_list($value['id']);
    						if(!empty($area_list)){
    							foreach ($area_list as $key2 => $value2) {
    								$str .= $value2['id'].',';
    							}
    						}
    					}
    				}
    				$all_str=explode(',',rtrim($str,','));
    				$where = array_merge($where,array('in'=>array('id',$all_str)));
    			}
    		}
    	}
    	$vdata['pages'] = $this->_pages(site_url($this->class.'/search/'.$this->cid.'/'),$limit,$where,4);
    	$vdata['list'] = $this->model->get_list($limit,$limit*($page-1),$order,$where);
    	$this->_display($vdata,$this->class.'_index.php');
    }
}
