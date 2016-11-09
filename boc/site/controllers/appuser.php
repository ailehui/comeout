<?php
if (! defined ( 'BASEPATH' ))
	exit ( 'No direct script access allowed' );
class Appuser extends MY_Controller {
	
	public $post;
	
	function __construct() {
		parent::__construct ();
		header('Content-type: application/json');
		
		$this->post=$this->input->post(NULL,TRUE);
		$this->post=doTrim($this->post);
		

		//加载help
		$this->load->helpers('uisite_helper');
        //加载model
        $this->load->model('appuser_model','appuser');
        //load_AES
        $this->load->library('AES');
	}
	//验证手机号码 注册
	public function verifyPhone(){
		
		$data = $this->post;
		$phone=isset($data['phone'])?$data['phone']:'';
		$vcode=isset($data['vcode'])?$data['vcode']:'';
		$submit_icode=isset($data['submit_icode'])?$data['submit_icode']:'';
		$agree=isset($data['agree'])?$data['agree']:'';
		
		//检查手机号
		$vdata=$this->appuser->check_phone($phone);
		if ($vdata['status']==-1){
			$output=array ('msg'=>$vdata['msg'],'returnCode'=>-401,'data'=>null);
			exit(json_encode($output));
		}
		//检查短信验证码
		$vdata=$this->appuser->check_vcode($phone,$vcode,1);
		if ($vdata['status']==-1){
			$output=array ('msg'=>$vdata['msg'],'returnCode'=>-401,'data'=>null);
			exit(json_encode($output));
		}
		//返回验证通过短信验证码数据库id和提交时间戳
		$vcodeinfo=array('time'=>$vdata['time'],'vcode_id'=>$vdata['id']);
		//检查邀请码
		if(!empty($submit_icode)){
			$vdata=$this->appuser->check_submit_icode($submit_icode);
			if ($vdata['status']==-1){
				$output=array ('msg'=>$vdata['msg'],'returnCode'=>-401,'data'=>null);
				exit(json_encode($output));
			}
		}
		//阅读并同意《用户服务使用协议》
		if($agree != 1){
			$output=array ('msg'=>'请阅读并同意《用户服务使用协议》','returnCode'=>-401,'data'=>null);
			exit(json_encode($output));
		}
		$output=array ('msg'=>'跳转下一步','returnCode'=>200,'data'=>AES::encrypt(json_encode($vcodeinfo), KEY));
		exit(json_encode($output));
	}
	//设置密码
	public function setPwd(){
		$data = $this->post;
		$phone=isset($data['phone'])?$data['phone']:'';
		$vcode=isset($data['vcode'])?$data['vcode']:'';
		$time=isset($data['time'])?$data['time']:'';
		$vcode_id=isset($data['vcode_id'])?$data['vcode_id']:'';
		$nickname=isset($data['nickname'])?$data['nickname']:'';
		$pwd=isset($data['pwd'])?$data['pwd']:'';
		$rpwd=isset($data['rpwd'])?$data['rpwd']:'';
		$submit_icode=isset($data['submit_icode'])?$data['submit_icode']:'';
		
		//检查手机号
		$vdata=$this->appuser->check_phone($phone);
		if ($vdata['status']==-1){
			$output=array ('msg'=>'信息不正确','returnCode'=>-402,'data'=>null);
			exit(json_encode($output));
		}
		
		//检查短信验证码信息
		$vdata=$this->appuser->check_vcode_info($phone,$vcode,$time,$vcode_id);
		if ($vdata['status']==-1){
			$output=array ('msg'=>$vdata['msg'],'returnCode'=>-402,'data'=>null);
			exit(json_encode($output));
		}
		
		//检查昵称
		$vdata=$this->appuser->check_nickname($nickname);
		if ($vdata['status']==-1){
			$output=array ('msg'=>$vdata['msg'],'returnCode'=>-401,'data'=>null);
			exit(json_encode($output));
		}
		
		//检查密码
		$vdata=$this->appuser->check_pwd($pwd);
		if ($vdata['status']==-1){
			$output=array ('msg'=>$vdata['msg'],'returnCode'=>-401,'data'=>null);
			exit(json_encode($output));
		}
		
		//检查在输入密码
		$vdata=$this->appuser->check_rpwd($pwd,$rpwd);
		if ($vdata['status']==-1){
			$output=array ('msg'=>$vdata['msg'],'returnCode'=>-401,'data'=>null);
			exit(json_encode($output));
		}
		
		//完成注册
		$now_time=time();
		$token=$this->appuser->token(KEY.$phone);
		$insert_data=array(
				'nickname'=>$nickname,
				'phone'=>$phone,
				'reg_time'=>$now_time,
				'submit_icode'=>$submit_icode,
				'pwd'=>$this->appuser->md5_pwd($pwd),
				'token'=>$token
		);
		$userid=$this->appuser->create($insert_data);
		if($userid){
			//以userid为参数更新邀请码
			//生成邀请码 跟新邀请码
			$icode=$this->appuser->getInviteCode($userid);
			$this->appuser->update(array('icode'=>$icode),array('id'=>$userid));
			//更新对应短信状态
			$this->appuser->update(array('use'=>1),array('id'=>$vcode_id),'sms');
			
			$userinfo=array('userid'=>$userid,'token'=>$token);
			$output=array ('msg'=>'完成注册','returnCode'=>200,'data'=>AES::encrypt(json_encode($userinfo), KEY));
		}else{
			$output=array ('msg'=>'操作失败','returnCode'=>-401,'data'=>null);
		}
		exit(json_encode($output));
	}
	
	//注册 发送短信验证码
	public function sendSMS(){
		$data = $this->post;
		$phone=isset($data['phone'])?$data ['phone']:'';
		//检查手机号
		$vdata=$this->appuser->check_phone($phone);
		if ($vdata['status']==-1){
			$output=array ('msg'=>$vdata['msg'],'returnCode'=>-401,'data'=>null);
			exit(json_encode($output));
		}
		//检查是否可以发送短信
		$vdata=$this->appuser->whetherCanSendSMS($phone,1);
		if ($vdata['status']==-1){
			$output=array ('msg'=>$vdata['msg'],'returnCode'=>-401,'data'=>null);
			exit(json_encode($output));
		}
		//发送验证码
		$verify = rand(100000,999999);
		$vdata=$this->appuser->sendSMS($phone,$verify,1);
		if ($vdata['status']==-1){
			$output=array ('msg'=>$vdata['msg'],'returnCode'=>-401,'data'=>null);
		}else{
			$output=array ('msg'=>$vdata['msg'],'returnCode'=>200,'data'=>null);
		}
		exit(json_encode($output));
	}
	//注册 注册成功 选择兴趣
	public function choiceInterest(){
		$data = $this->post;
		$userid=isset($data['userid'])?$data ['userid']:'';
		$interest=isset($data['interest'])?$data ['interest']:'';
		
		$this->content=getallheaders();
				if(empty($this->content['platform'])){
					$re = array('returnCode'   => '401',
							'msg' => 'platform不能为空',
							'secure' => 'false',
							'data'   => null);
					echo json_encode($re);
					exit;
				}
				if(empty($this->content['token'])){
					$re = array('returnCode'   => '401',
							'msg' => 'token不能为空',
							'secure' => 'false',
							'data'   => null);
					echo json_encode($re);
					exit;
				}
		
		//检查userid
		$vdata=$this->appuser->check_user($userid,$this->content['token']);
		if ($vdata['status']==-1){
			$output=array ('msg'=>$vdata['msg'],'returnCode'=>-401,'data'=>null);
			exit(json_encode($output));
		}
		$this->appuser->update(array('interest'=>$interest),array('id'=>$userid));
		$output=array ('msg'=>'完成','returnCode'=>200,'data'=>null);
		exit(json_encode($output));
	}
	
	//返回兴趣列表
	public function interestList(){
		$list=$this->appuser->getInterestList();
		$output=array ('msg'=>'获取成功','returnCode'=>200,'data'=>AES::encrypt(json_encode($list), KEY));
		exit(json_encode($output));
	}
	
	
	//登录  普通登录 密码登录
	public function pwdLogin(){
		$data = $this->post;
		$phone=isset($data['phone'])?$data ['phone']:'';
		$pwd=isset($data['pwd'])?$data ['pwd']:'';
		
		//检查登录手机和密码
		$vdata=$this->appuser->check_login($phone,$pwd);
		if ($vdata['status']==-1){
			$output=array ('msg'=>$vdata['msg'],'returnCode'=>-401,'data'=>null);
			exit(json_encode($output));
		}
		$update_data=array(
			'login_ip'=>get_ip()
		);
		$this->appuser->update($update_data,array('phone'=>$phone));
		$userinfo=$this->appuser->get_one(array('phone'=>$phone),'id,phone');
		$output=array ('msg'=>'登录成功','returnCode'=>200,'data'=>AES::encrypt(json_encode($userinfo), KEY));
		exit(json_encode($output));
	}
	
	//登录  短信登录 动态密码登录
	public function vcodeLogin(){
		$data = $this->post;
		$phone=isset($data['phone'])?$data ['phone']:'';
		$vcode=isset($data['vcode'])?$data ['vcode']:'';
	
		//检查登录手机和密码
		$sms_type=2;
		$vdata=$this->appuser->check_vcode_login($phone,$vcode,$sms_type);
		if ($vdata['status']==-1){
			$output=array ('msg'=>$vdata['msg'],'returnCode'=>-401,'data'=>null);
			exit(json_encode($output));
		}
		$update_data=array(
				'login_ip'=>get_ip()
		);
		$this->appuser->update($update_data,array('phone'=>$phone));
		//更新对应短信状态
		$this->appuser->update(array('use'=>1),array('id'=>$vdata['vcode_id']),'sms');
		
		$userinfo=$this->appuser->get_one(array('phone'=>$phone),'id,phone');
		$output=array ('msg'=>'登录成功','returnCode'=>200,'data'=>AES::encrypt(json_encode($userinfo), KEY));
		exit(json_encode($output));
	}
	//app 短信登录发送短信验证码
	public function vcodeLoginSendSMS(){
		$data = $this->post;
		$phone=isset($data['phone'])?$data ['phone']:'';
		//检查手机号
		$vdata=$this->appuser->check_phone_vcode_login($phone);
		if ($vdata['status']==-1){
			$output=array ('msg'=>$vdata['msg'],'returnCode'=>-401,'data'=>null);
			exit(json_encode($output));
		}
		//检查是否可以发送短信
		$vdata=$this->appuser->whetherCanSendSMS($phone,2);
		if ($vdata['status']==-1){
			$output=array ('msg'=>$vdata['msg'],'returnCode'=>-401,'data'=>null);
			exit(json_encode($output));
		}
		//发送验证码
		$verify = rand(100000,999999);
		$vdata=$this->appuser->sendSMS($phone,$verify,2);
		if ($vdata['status']==-1){
			$output=array ('msg'=>$vdata['msg'],'returnCode'=>-401,'data'=>null);
		}else{
			$output=array ('msg'=>$vdata['msg'],'returnCode'=>200,'data'=>null);
		}
		exit(json_encode($output));
	}
	//app忘记密码发送短信
	public function forgetPwdSendSMS(){
		$data = $this->post;
		$phone=isset($data['phone'])?$data ['phone']:'';
		//检查手机号
		$vdata=$this->appuser->check_phone_vcode_login($phone);
		if ($vdata['status']==-1){
			$output=array ('msg'=>$vdata['msg'],'returnCode'=>-401,'data'=>null);
			exit(json_encode($output));
		}
		//检查是否可以发送短信
		$vdata=$this->appuser->whetherCanSendSMS($phone,3);
		if ($vdata['status']==-1){
			$output=array ('msg'=>$vdata['msg'],'returnCode'=>-401,'data'=>null);
			exit(json_encode($output));
		}
		//发送验证码
		$verify = rand(100000,999999);
		$vdata=$this->appuser->sendSMS($phone,$verify,3);
		if ($vdata['status']==-1){
			$output=array ('msg'=>$vdata['msg'],'returnCode'=>-401,'data'=>null);
		}else{
			$output=array ('msg'=>$vdata['msg'],'returnCode'=>200,'data'=>null);
		}
		exit(json_encode($output));
	}
	
	//app忘记密码
	public function forgetPwd(){
		$data = $this->post;
		$phone=isset($data['phone'])?$data ['phone']:'';
		$vcode=isset($data['vcode'])?$data ['vcode']:'';
	
		//检查登录手机和密码
		$sms_type=3;
		$vdata=$this->appuser->check_vcode_login($phone,$vcode,$sms_type);
		if ($vdata['status']==-1){
			$output=array ('msg'=>$vdata['msg'],'returnCode'=>-401,'data'=>null);
			exit(json_encode($output));
		}
		$userinfo=array('time'=>$vdata['time'],'vcode_id'=>$vdata['vcode_id']);
		$output=array ('msg'=>'OK','returnCode'=>200,'data'=>AES::encrypt(json_encode($userinfo), KEY));
		exit(json_encode($output));
	}
	//app忘记密码 重置密码
	public function resetPwd(){
		$data = $this->post;
		$phone=isset($data['phone'])?$data ['phone']:'';
		$vcode=isset($data['vcode'])?$data ['vcode']:'';
		$time=isset($data['time'])?$data ['time']:'';
		$vcode_id=isset($data['vcode_id'])?$data ['vcode_id']:'';
		$pwd=isset($data['pwd'])?$data ['pwd']:'';
		$rpwd=isset($data['rpwd'])?$data ['rpwd']:'';
		//检查手机号是否正确
		$vdata=$this->appuser->check_phone_vcode_login($phone);
		if ($vdata['status']==-1){
			$output=array ('msg'=>$vdata['msg'],'returnCode'=>-402,'data'=>null);
			exit(json_encode($output));
		}
		//检查短信验证码信息
		$vdata=$this->appuser->check_vcode_info($phone,$vcode,$time,$vcode_id);
		if ($vdata['status']==-1){
			$output=array ('msg'=>$vdata['msg'],'returnCode'=>-402,'data'=>null);
			exit(json_encode($output));
		}
		//检查密码
		$vdata=$this->appuser->check_pwd($pwd);
		if ($vdata['status']==-1){
			$output=array ('msg'=>$vdata['msg'],'returnCode'=>-401,'data'=>null);
			exit(json_encode($output));
		}
		//检查在输入密码
		$vdata=$this->appuser->check_rpwd($pwd,$rpwd);
		if ($vdata['status']==-1){
			$output=array ('msg'=>$vdata['msg'],'returnCode'=>-401,'data'=>null);
			exit(json_encode($output));
		}
		//更新对应短信状态
		$this->appuser->update(array('use'=>1),array('id'=>$vcode_id),'sms');
		//更新用户密码
		$this->appuser->update(array('pwd'=>$this->appuser->md5_pwd($pwd)),array('phone'=>$phone));
		
		$output=array ('msg'=>'重置成功，请重新登录','returnCode'=>200,'data'=>null);
		exit(json_encode($output));
	}
}
