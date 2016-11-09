<?php
if (! defined ( 'BASEPATH' ))
	exit ( 'No direct script access allowed' );
class Sales extends MY_Controller {
	
	public $post;
	protected $table = 'Sales';
	protected $interest_table = 'interest';
	protected $upload_table = 'upload';
	protected $compress_width_height = 1000;//图片压缩宽高
	protected $compress_size = 300;//图片压缩大小
	
	function __construct() {
		parent::__construct ();
		header('Content-type: application/json');
		
		$this->post=$this->input->post(NULL,TRUE);
		$this->post=doTrim($this->post);
		
		$this->content=getallheaders();
// 		if(empty($this->content['platform'])){
// 			$re = array('returnCode'   => '401',
// 					'msg' => 'platform不能为空',
// 					'secure' => 'false',
// 					'data'   => null);
// 			echo json_encode($re);
// 			exit;
// 		}
// 		if(empty($this->content['token'])){
// 			$re = array('returnCode'   => '401',
// 					'msg' => 'token不能为空',
// 					'secure' => 'false',
// 					'data'   => null);
// 			echo json_encode($re);
// 			exit;
// 		}
		//加载help
		$this->load->helpers('uisite_helper');
        //加载model
        $this->load->model('appuser_model','appuser');
        $this->load->model('sales_model','appuser');
        //load_AES
        $this->load->library('AES');
	}
	//发布促销
	public function releaseSales(){
		
		$data = $this->post;
		$userid=isset($data['userid'])?$data ['userid']:'';//用户id
		$title=isset($data['title'])?$data['title']:'';//促销标题
		$dead_line=isset($data['dead_line'])?$data['dead_line']:'';//报名截至时间
		$start_time=isset($data['start_time'])?$data['start_time']:'';//促销开始时间
		$end_time=isset($data['end_time'])?$data['end_time']:'';//促销结束时间
		$phone=isset($data['phone'])?$data['phone']:'';//联系电话
		$content=isset($data['content'])?$data['content']:'';//描述
		$video=isset($data['video'])?$data['video']:'';//视频地址
		$interest=isset($data['interest'])?$data['interest']:'';//促销类型
		//报名费用
		$online_pay=isset($data['online_pay'])?$data['online_pay']:'';//是否在线支付
		$refund=isset($data['refund'])?$data['refund']:'';//支持退款
		$fee_item=isset($data['fee_item'])?$data['fee_item']:'';//费用项
		//报名信息
		$reg_info=isset($data['reg_info'])?$data['reg_info']:'';//报名信息
		$agree=isset($data['agree'])?$data['agree']:'';//同意《出来吧发布协议》
		//举办地址
		$place=isset($data['place'])?$data['place']:'';//举报地址
		$lal=isset($data['lal'])?$data['lal']:'';//经纬度
		
		$now_time=time();
		
		//检查userid
		// 		$vdata=$this->appuser->check_user($userid,$this->content['token']);
		// 		if ($vdata['status']==-1){
		// 			$output=array ('msg'=>$vdata['msg'],'returnCode'=>-401,'data'=>null);
		// 			exit(json_encode($output));
		// 		}
		
		if(empty($title)){
			$output=array ('msg'=>'请填写促销主题','returnCode'=>-401,'data'=>null);
			exit(json_encode($output));
		}
		$title_len=mb_strlen($title);
		if($title_len<8||$title_len>20){
			$output=array ('msg'=>'请填写8-20个字符的促销主题','returnCode'=>-401,'data'=>null);
			exit(json_encode($output));
		}
		$photo=array();
		//判断照片
		
		$date = date('Y/m/d', $now_time);
		if ($_FILES) {
			$uploadPath = UPLOAD_PATH . '/' . $date;
			if (!file_exists($uploadPath)) {
				$res = mkdir($uploadPath, 0777, true);
			}
			$config['upload_path'] = './upload/' . $date . '/';
			$config['encrypt_name'] = TRUE;
			/*$config['max_width']  = '1024';
			 $config['max_height']  = '768';*/
			$config['allowed_types'] = 'gif|jpg|png|jpeg';
			$config['max_size'] = '10240';
			$this->load->library('upload');
			$this->upload->initialize($config);
			//计算图片上传上限
			$photoCount=0;
			foreach ($_FILES as $k => $val) {
				if (strpos($k, 'hoto')) {
					if (!empty($val['name'])) {
						$photoCount++;
					}
				}
			}
						if(!$photoCount){
							$output = array(
									'msg' => '请上传促销首图',
									'return_code' => -401
							);
							exit(json_encode($output));
						}
						if($photoCount>3){
							$output = array(
									'msg' => '最多上传3张促销首图',
									'return_code' => -401
							);
							exit(json_encode($output));
						}
			//执行图片上传
			foreach ($_FILES as $k => $val) {
				$field_name = $k;
				if (strpos($field_name, 'hoto')) {
					if ($photoCount <= 3) {//上传图片
						if (!empty($val['name'])) {
							if ($this->upload->do_upload($field_name)) {
								$temp = $this->upload->data();
								$photo[] = $temp;
								$photoCount++;
							}
						}
					}
				}
			}
		}
				else{
					$output = array(
							'msg' => '请上传促销首图',
							'return_code' => -401
					);
					exit(json_encode($output));
				}
		
		foreach ($photo as $k => $item) {
			$photoInsert[$k] = array(
					'name' => $item['file_name'],
					'size' => $item['file_size'] * 1024,
					'type' => $item['file_type'],
					'thumb' => $date . '/' . $item['file_name'],
					'url' => $date . '/' . $item['file_name'],
					'deleteUrl' => '?file=' . $item['file_name'] . '&dt=' . $date,
					'timeline' => now()
			);
			$this->imgResize(UPLOAD_PATH.$date.'/'.$item['file_name']);
			
			
			$this->db->insert('upload', $photoInsert[$k]);
			$photoInsertId[] = $this->db->insert_id();
		}
		//新增记录！
		if(isset($photoInsertId)){
			$photo=trim(implode(',',$photoInsertId),',');
		}else{
			$photo='';
		}
		if(empty($dead_line)){
			$output=array ('msg'=>'请填写报名截至时间','returnCode'=>-401,'data'=>null);
			exit(json_encode($output));
		}
		if(empty($start_time)){
			$output=array ('msg'=>'请填写促销开始时间','returnCode'=>-401,'data'=>null);
			exit(json_encode($output));
		}
		if($start_time < $dead_line){
			$output=array ('msg'=>'促销开始时间不能早于报名截至时间','returnCode'=>-401,'data'=>null);
			exit(json_encode($output));
		}
		if(empty($end_time)){
			$output=array ('msg'=>'请填写促销结束时间','returnCode'=>-401,'data'=>null);
			exit(json_encode($output));
		}
		if($end_time < $start_time){
			$output=array ('msg'=>'促销结束时间不能早于促销开始时间','returnCode'=>-401,'data'=>null);
			exit(json_encode($output));
		}
		if(empty($phone)){
			$output=array ('msg'=>'请填写联系电话','returnCode'=>-401,'data'=>null);
			exit(json_encode($output));
		}
		if (!preg_match('/^1[34578]\d{9}$/',$phone) && !preg_match('/^(0[0-9]{2,3}-)?([2-9][0-9]{6,7})+(-[0-9]{1,4})?$/',$phone)) {
			$output=array ('msg'=>'联系电话格式不正确','returnCode'=>-401,'data'=>null);
			exit(json_encode($output));
		}
		if(empty($place)||empty($lal)){
			$output=array ('msg'=>'请填写促销举办地点','returnCode'=>-401,'data'=>null);
			exit(json_encode($output));
		}
		if(empty($content)){
			$output=array ('msg'=>'请填写促销详细描述','returnCode'=>-401,'data'=>null);
			exit(json_encode($output));
		}
		$content_len=mb_strlen($content);
		if($content_len<8||$content_len>1000){
			$output=array ('msg'=>'请填写8-1000个字符的促销详细描述','returnCode'=>-401,'data'=>null);
			exit(json_encode($output));
		}
		if(empty($interest)){
			$output=array ('msg'=>'请选择促销类型','returnCode'=>-401,'data'=>null);
			exit(json_encode($output));
		}
		$where=array('id'=>$interest);
		if(!$this->appuser->countSql($where,$this->interest_table)){
			$output=array ('msg'=>'促销类型不正确','returnCode'=>-401,'data'=>null);
			exit(json_encode($output));
		}
		//
		
		$insertData = array(
				'sort_id'=>maxSortid($this->table),
				'photo'=>$photo,
				'userid'=>$userid,
				'title'=>$title,
				'dead_line'=>$dead_line,
				'start_time'=>$start_time,
				'end_time'=>$end_time,
				'phone'=>$phone,
				'content'=>$content,
				'video'=>$video,
				'interest'=>$interest,
				'online_pay'=>$online_pay,
				'refund'=>$refund,
				'fee_item'=>json_encode($fee_item),
				'reg_info'=>json_encode($reg_info),
				'place'=>$place,
				'lal'=>$lal,
				'submit_time'=>$now_time	
		);
		$this->db->insert($this->table, $insertData );
		$iid = $this->db->insert_id();
		$rdata=array('aid'=>$iid);
		if($iid){
			$output=array ('msg'=>'发布成功','returnCode'=>200,'data'=>AES::encrypt(json_encode($rdata), KEY));
			exit(json_encode($output));
		}else{
			//发布失败 删除先前上传的图片
			if(isset($photoInsertId)){
				foreach ($photoInsertId as $key => $value) {
					$url=$this->db->select('url')->where(array('id'=>$value))->get($this->upload_table)->row_array();
					if(isset($url['url'])&&!empty($url['url'])){
						@unlink(UPLOAD_PATH.$url['url']);
					}
					$this->db->delete($this->upload_table, array('id'=>$value));
				}
			}
			$output=array ('msg'=>'发布失败','returnCode'=>-401,'data'=>null);
			exit(json_encode($output));
		}
	}
	
	//返回图片路径
	public function returnPath(){
		$now_time=time();
		$date = date('Y/m/d', $now_time);
		if ($_FILES) {
			$uploadPath = UPLOAD_PATH . '/' . $date;
			if (!file_exists($uploadPath)) {
				$res = mkdir($uploadPath, 0777, true);
			}
			$config['upload_path'] = './upload/' . $date . '/';
			$config['encrypt_name'] = TRUE;
			/*$config['max_width']  = '1024';
			 $config['max_height']  = '768';*/
			$config['allowed_types'] = 'gif|jpg|png|jpeg';
			$config['max_size'] = '10240';
			$this->load->library('upload');
			$this->upload->initialize($config);
			//计算图片上传上限
			$photoCount=0;
			foreach ($_FILES as $k => $val) {
				if (strpos($k, 'hoto')) {
					if (!empty($val['name'])) {
						$photoCount++;
					}
				}
			}
						if(!$photoCount){
							$output = array(
									'msg' => '请上传图片',
									'return_code' => -401
							);
							exit(json_encode($output));
						}
						if($photoCount>3){
							$output = array(
									'msg' => '请上传图片',
									'return_code' => -401
							);
							exit(json_encode($output));
						}
			//执行图片上传
			foreach ($_FILES as $k => $val) {
				$field_name = $k;
				if (strpos($field_name, 'hoto')) {
					if ($photoCount <= 3) {//上传图片
						if (!empty($val['name'])) {
							if ($this->upload->do_upload($field_name)) {
								$temp = $this->upload->data();
								$photo[] = $temp;
								$photoCount++;
							}
						}
					}
				}
			}
		}
				else{
					$output = array(
							'msg' => '请上传图片',
							'return_code' => -401
					);
					exit(json_encode($output));
				}
		
		foreach ($photo as $k => $item) {
			$photoInsert[$k] = array(
					'name' => $item['file_name'],
					'size' => $item['file_size'] * 1024,
					'type' => $item['file_type'],
					'thumb' => $date . '/' . $item['file_name'],
					'url' => $date . '/' . $item['file_name'],
					'deleteUrl' => '?file=' . $item['file_name'] . '&dt=' . $date,
					'timeline' => now()
			);
			$this->imgResize(UPLOAD_PATH.$date.'/'.$item['file_name']);
			$this->db->insert('upload', $photoInsert[$k]);
			$photoInsertId[] = UPLOAD_URL.$date . '/' . $item['file_name'];
		}
		$output=array ('msg'=>'获取成功','returnCode'=>200,'data'=>AES::encrypt(json_encode($photoInsertId), KEY));
		exit(json_encode($output));
	}
	
	//压缩图片
	public function imgResize($filename)
    {
        $this->load->library('image_lib');
        $arr=@getimagesize($filename);
        $width=$arr[0];
        $height=$arr[1];
        $size=filesize($filename)/1000;
//         if($size > $this->compress_size){
//         	$Multiple=$size/$this->compress_size;
//         	$quality=100/$Multiple;
//         }
//         $width=$width*$quality/100;
//         $height=$height*$quality/100;
        if($width>1000 || $height>1000){
        	$resize['width'] = $this->compress_width_height;
        	$resize['height'] = $this->compress_width_height;
        }else{
        	$resize['width'] = $width;
        	$resize['height'] = $height;
        }
        
       
        $resize['image_library'] = 'GD2';
        $resize['source_image'] = $filename;
        $resize['maintain_ratio'] = TRUE;
        $resize['master_dim'] = 'auto';
        
        $resize['quality'] = 100;
       // exit($config['quality']);
        $this->image_lib->initialize($resize);
        $this->image_lib->resize();
    }
   
}
