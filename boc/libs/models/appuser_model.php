<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class appuser_model extends MY_Model {
	
    protected $table = 'user'; 
    protected $sms_table = 'sms';
    protected $interest_table = 'interest';

    // 检测兴趣名称是否存在
    public function check_title($title,$id="")
    {	
    	if($id!=""){
    		$where=array('id !='=>$id,'title'=>$title);
    	}else{
    		$where=array('title'=>$title);
    	}
    	$count = $this->countSql($where,$this->table);
		if($count){
			return true;
		}else{
			return false;
		}
    } 
    
    // 检查手机号码 注册
    public function check_phone($phone)
    {
    	if(empty($phone)){
    		return array('status'=>-1,'msg'=>'请输入手机号');
    	}
    	if (preg_match('/^1[34578]\d{9}$/',$phone) && strlen($phone)==11) {
    		
    	}else{
    		return array('status'=>-1,'msg'=>'请输入有效的手机号');
    	}
    	$isset=$this->is_set(array('phone'=>$phone));
    	if($isset){
    		return array('status'=>-1,'msg'=>'该手机号已经注册');
    	}
    	return array('status'=>0,'msg'=>'OK');
    }
    
    // 检查 登录
    public function check_login($phone,$pwd)
    {
    	$data=array('status'=>0,'msg'=>'OK');
    	if(empty($phone)){
    		$data=array('status'=>-1,'msg'=>'请输入手机号');
    		return $data;
    	}
    	$isset=$this->is_set(array('phone'=>$phone));
    	if(!$isset){
    		$data=array('status'=>-1,'msg'=>'手机号不存在');
    		return $data;
    	}
    	if(empty($pwd)){
    		$data=array('status'=>-1,'msg'=>'请输入密码');
    		return $data;
    	}
    	$isset2=$this->is_set(array('phone'=>$phone,'pwd'=>$this->md5_pwd($pwd)));
    	if(!$isset2){
    		$data=array('status'=>-1,'msg'=>'密码不正确');
    		return $data;
    	}
    	$isset3=$this->is_set(array('phone'=>$phone,'pwd'=>$this->md5_pwd($pwd),'audit'=>1));
    	if(!$isset3){
    		$data=array('status'=>-1,'msg'=>'该用户已被锁定');
    		return $data;
    	}
    	return $data;
    }
    //加密用户密码
    public function md5_pwd($pwd)
    {
    	if(empty($pwd)){
    		$data='';
    	}else{
    		$data=md5(md5(HMACPWD).md5($pwd));
    	}
    	return $data;
    }
    
    //检查是否存在
    public function is_set($where){
    	if($this->countSql($where,$this->table)){
    		return true;
    	}
    	return false;
    }
    //是否可以发送验证码
    public function whetherCanSendSMS($phone,$sms_ctype){
    	$one=$this->getSMSTime($phone,$sms_ctype);
    	if(isset($one['time'])&&!empty($one['time'])){
    		$interval = time() - $one['time'];
    		if($interval > MININTERVAL){
    			$data=array('status'=>0,'msg'=>'发送过，过了间隔，可以再次发送了');
    		}else{
    			$data=array('status'=>-1,'msg'=>'发送过于频繁');
    		}
    	}else{
    		$data=array('status'=>0,'msg'=>'没发送过，可以发送');
    	}
    	return $data;
    }
    
    
    //生成6位邀请码
	function getInviteCode($userid,$l='6') {
		if(empty($userid)||$userid<=0){
			return false;
		}
		$str16=sprintf("%X",$userid);
		$str16_lenght=strlen($str16);
		if($str16_lenght<$l){
			$new_lenght=$l-$str16_lenght;
			$str16_time=substr(sprintf("%X",time()),0,$new_lenght);
			$data=$str16.$str16_time;
		}else{
			$data=$str16;
		}
		return $data;
	}
	
	
  
	//发送短信验证码
	public function sendSMS($phone,$verify,$sms_ctype) {
		$text='';
		if($sms_ctype==1){
			$text.=REG_SMS_TEXT;
		}
		if($sms_ctype==2){
			$text.=LOGIN_SMS_TEXT;
		}
		if($sms_ctype==3){
			$text.=FORGET_PWD_SMS_TEXT;
		}
		$content = $text . $verify;
		$getUrl = "http://120.26.69.248/msg/HttpSendSM?account=".SMS_ACCOUNT."&pswd=".SMS_PASSWORD."&mobile=" .$phone . "&msg=" . $content . "&needstatus=true";
		$backMsg = file_get_contents ( $getUrl );
		$backArr = explode ( "\n", $backMsg );
		$back = $backArr [0];
		$back = explode ( ',', $back );
		$time=time();
		$ctype=$this->smstypeTotype($sms_ctype);
		if ($back [1] == 0) {
			// 发送成功，更新数据
			//插入短信表
			$insert_data=array(
				'phone'=>$phone,
				'content'=>$content,
				'ctype'=>$ctype,
				'sms_type'=>$sms_ctype,
				'time'=>$time,
				'code'=>$verify,
				'send_status'=>1   //发送状态  成功1 失败2
			);
			if($vcode_id=$this->create($insert_data,$this->sms_table)){
				$data=array('status'=>0,'msg'=>'发送成功','vcode_id'=>$vcode_id);
			}else{
				$data=array('status'=>-1,'msg'=>'数据库操作失败','vcode_id'=>null);
			}
		} else {
			// 发送成功，更新数据
			//插入短信表
			$insert_data=array(
					'phone'=>$phone,
					'content'=>$content,
					'ctype'=>$ctype,
					'sms_type'=>$sms_ctype,
					'time'=>$time,
					'code'=>$verify,
					'send_status'=>2   //发送状态  成功1 失败2
			);
			if($vcode_id=$this->create($insert_data,$this->sms_table)){
				$data=array('status'=>-1,'msg'=>'发送失败,错误码:'.$back [1],'vcode_id'=>$vcode_id);
			}else{
				$data=array('status'=>-1,'msg'=>'发送失败,错误码:'.$back [1].',数据库操作失败','vcode_id'=>null);
			}
			
		}
		return $data;
	}
	
	
	//插入数据库
	public function create($data,$table=''){
		if(empty($table)){
			$table=$this->table;
		}
		$this->db->insert($table, $data);
		if ($this->db->affected_rows()) {
			return $this->db->insert_id();
		}
		return 0;
	}
	
	// 检查短信验证码
	public function check_vcode($phone,$vcode,$sms_ctype)
	{
		$data=$this->check_phone($phone);
		if ($data['status']==-1){
			return $data;
		}
		if(empty($vcode)){
			$data=array('status'=>-1,'msg'=>'请输入短信验证码');
			return $data;
		}
		$codeinfo=$this->getSMSTime($phone,$sms_ctype);
		if(!empty($codeinfo)){
			if(isset($codeinfo['code'])&&!empty($codeinfo['code'])){
				if($codeinfo['code']!=$vcode){
					return array('status'=>-1,'msg'=>'短信验证码错误');
				}else{
					if(isset($codeinfo['time'])&&!empty($codeinfo['time'])){
						if(time()-$codeinfo['time'] > EXPIRE){
							return array('status'=>-1,'msg'=>'短信验证码已过期');
						}
					}else{
						return array('status'=>-1,'msg'=>'短信验证码错误');
					}
				}
			}else{
				return array('status'=>-1,'msg'=>'短信验证码错误');
			}
		}else{
			return array('status'=>-1,'msg'=>'短信验证码错误');
		}		
	}
	// 获取短信发送时间
	public function getSMSTime($phone,$sms_ctype){
		$query = $this->db
		->select('time,id,code')
		->from($this->sms_table)
		->where(array('phone'=>$phone,'sms_type'=>$sms_ctype,'use'=>0,'send_status'=>1))
		->order_by('time', 'DESC')->get();
		return $query->row_array();
	}
	// 获取邀请码所有者id
	public function getIcodeUid($submit_icode){
		$query = $this->db
		->select('id')
		->from($this->table)
		->where('icode',$submit_icode)
		->get();
		return $query->row_array();
	}
	
	public function countSql($where,$table=''){
		if(empty($table)){
			$table=$this->table;
		}
		return $this->db->where ($where)->from ($table)->count_all_results ();
	}
	
	// 检查邀请码
	public function check_submit_icode($submit_icode)
	{	
		$where=array('icode'=>$submit_icode);
		$one=$this->countSql($where);
		if($one){
			$data=array('status'=>0,'msg'=>'正确的邀请码');
		}else{
			$data=array('status'=>-1,'msg'=>'邀请码错误');
		}
		return $data;
	}
	
	// 设置密码 注册  检查app端发送来的短信信息是否符合
	public function check_vcode_info($phone,$vcode,$time,$vcode_id)
	{
		$where=array(
			'phone'=>$phone,'code'=>$vcode,'time'=>$time,'id'=>$vcode_id,'use'=>0
		);
		$count=$this->countSql($where,$this->sms_table);
		if($count){
			$data=array('status'=>0,'msg'=>'正确的信息');
		}else{
			$data=array('status'=>-1,'msg'=>'信息不正确');
		}
		return $data;
	}
	
	// 设置密码 注册  检查nickname 昵称
	public function check_nickname($nickname)
	{
		//加载help
		$this->load->helpers('uisite_helper');
		$data=array('status'=>0,'msg'=>'正确的昵称');
		if(empty($nickname)){
			$data=array('status'=>-1,'msg'=>'请输入昵称');
			return $data;
		}
		$leng=abslength($nickname);
		if($leng > 8){
			$data=array('status'=>-1,'msg'=>'昵称格式不正确');
			return $data;
		}
		if (!preg_match('/^[a-zA-Z0-9\x{4e00}-\x{9fa5}]+$|^[a-zA-Z0-9\x{4e00}-\x{9fa5}][a-zA-Z0-9_\s\ \x{4e00}-\x{9fa5}\.]*[a-zA-Z0-9\x{4e00}-\x{9fa5}]+$/u',$nickname)) {
			$data=array('status'=>-1,'msg'=>'昵称格式不正确');
			return $data;
		}
		return $data;
	}
	
	// 设置密码 注册  检查密码
	public function check_pwd($pwd)
	{	
		$data=array('status'=>0,'msg'=>'密码正确');
		if (!preg_match("/^[0-9_a-zA-Z]{6,12}$/",$pwd)) {
			$data=array('status'=>-1,'msg'=>'请输入6-12位密码');
			return $data;
		}
		return $data;
	}
	
	// 设置密码 注册  检查确认密码
	public function check_rpwd($pwd,$rpwd)
	{
		$data=array('status'=>0,'msg'=>'确认密码正确');
		if ($rpwd!=$pwd) {
		$data=array('status'=>-1,'msg'=>'两次输入的密码不同');
			return $data;
		}
		return $data;
	}
	
	// 更新
	public function update($data,$where,$table='')
	{
		if(empty($table)){
			$table=$this->table;
		}
		if(empty($data)||empty($where)){
			return false;
		}
		$this->db->update($table,$data,$where);
		return $this->db->affected_rows();
	}
	// 查询兴趣列表
	public function getInterestList($select='id,title',$where=array('audit'=>1,'status'=>1),$sort='sort_id DESC')
	{
		$list=$this->db->select($select)
		->where($where)
		->order_by($sort)
		->get($this->interest_table)
		->result_array();
		return $list;
	}
	// 检查userid
	public function check_userid($userid)
	{
		$data=array('status'=>0,'msg'=>'OK');
		if(empty($userid)){
			$data=array('status'=>-1,'msg'=>'缺少用户ID');
			return $data;
		}
		$user=$this->get_one(array('id'=>$userid),'audit');
		if(!$user){
			$data=array('status'=>-1,'msg'=>'不存在的用户ID');
			return $data;
		}
		if($user['audit'] != 1){
			$data=array('status'=>-1,'msg'=>'该用户已被锁定');
			return $data;
		}
		return $data;
	}
	
	// 获取单个用户的信息
	public function get_one($where,$filed='*')
	{	
		$one=$this->db->select($filed)->where($where)
		->get($this->table)->row_array();
		return $one;
	}
	//生成token
	public function token($token)
	{
		if(empty($token)){
			return '';
		}
		return md5($token);
	}
	// 检查手机号码  短信登录
	public function check_phone_vcode_login($phone)
	{
		if(empty($phone)){
			return array('status'=>-1,'msg'=>'请输入手机号');
		}
		$isset=$this->is_set(array('phone'=>$phone));
		if(!$isset){
			return array('status'=>-1,'msg'=>'手机号不存在');
		}
		return array('status'=>0,'msg'=>'OK');
	}
	// 检查 短信登录
	public function check_vcode_login($phone,$vcode,$sms_type)
	{
		if(empty($phone)){
			return array('status'=>-1,'msg'=>'请输入手机号');
		}
		$isset=$this->is_set(array('phone'=>$phone));
		if(!$isset){
			return array('status'=>-1,'msg'=>'手机号不存在');
		}
		if(empty($vcode)){
			return array('status'=>-1,'msg'=>'请输入短信验证码');
		}
		$codeinfo=$this->getSMSTime($phone,$sms_type);
		if(!empty($codeinfo)){
			if(isset($codeinfo['code'])&&!empty($codeinfo['code'])){
				if($codeinfo['code']!=$vcode){
					return array('status'=>-1,'msg'=>'短信验证码错误');
				}else{
					if(isset($codeinfo['time'])&&!empty($codeinfo['time'])){
						if(time()-$codeinfo['time'] > EXPIRE){
							return array('status'=>-1,'msg'=>'短信验证码已过期');
						}
					}else{
						return array('status'=>-1,'msg'=>'短信验证码错误');
					}
				}
			}else{
				return array('status'=>-1,'msg'=>'短信验证码错误');
			}
		}else{
			return array('status'=>-1,'msg'=>'短信验证码错误');
		}
		$isset3=$this->is_set(array('phone'=>$phone,'audit'=>1));
		if(!$isset3){
			$data=array('status'=>-1,'msg'=>'该用户已被锁定');
			return $data;
		}
		return array('status'=>0,'msg'=>'OK','vcode_id'=>$codeinfo['id'],'time'=>$codeinfo['time']);
	}
	//短信状态转化   
	//sms_type to ctype
	function smstypeTotype($sms_type){
		if(!in_array($sms_type,array(1,2,3))){
			return false;
		}
		if(in_array($sms_type,array(1,2,3))){
			return 1;
		}
		if(in_array($sms_type,array(5))){
			return 2;
		}
	}
	
	//检查user token
	function check_user($userid,$token){
		if(empty($userid)){
			return array('status'=>-1,'msg'=>'缺少用户ID');
		}
		if(empty($token)){
			return array('status'=>-1,'msg'=>'缺少TOKEN');
		}
		$user=$this->get_one(array('id'=>$userid),'audit,token');
		if(!$user){
			return array('status'=>-1,'msg'=>'不存在的用户ID');
		}
		if($user['audit'] != 1){
			return array('status'=>-1,'msg'=>'该用户已被锁定');
		}
		if($user['token'] != $token){
			return array('status'=>-1,'msg'=>'TOKEN错误');
		}
		return array('status'=>0,'msg'=>'OK');
	}
}


	
    
