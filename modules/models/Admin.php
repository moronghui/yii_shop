<?php
namespace app\modules\models;
use yii\db\ActiveRecord;
use Yii;

class Admin extends ActiveRecord
{
	public $rememberMe=true;
	public $repass;
	public $oldpass;
	public static function tableName()
	{
		return "{{%admin}}";
	}
	public function attributeLabels()
	{
		return [
			'adminuser'=>'管理员账号',
			'adminemail'=>'管理员邮箱',
			'adminpass'=>'管理员密码',
			'repass'=>'确认密码',
			'oldpass'=>'原密码',
		];
	}
	public function rules()
	{
		return [
			['adminuser', 'required', 'message'=>'管理员账号不能为空','on'=>['login','seekpass','adminadd','changeemail','changepassword']],
			['adminpass', 'required', 'message'=>'管理员密码不能为空','on'=>['login','changepass','adminadd','changeemail','changepassword']],
			['rememberMe', 'boolean','on'=>['login']],
			['adminpass' , 'validatePass','on'=>['login','changeemail']],
			['adminemail' , 'required','message'=>'邮箱不能为空','on'=>['seekpass','adminadd','changeemail']],
			['adminemail', 'email', 'message'=>'邮箱格式不正确','on'=>['seekpass','adminadd','changeemail']],
			['adminemail', 'validateEmail','on'=>['seekpass']],
			['repass', 'compare' , 'compareAttribute'=>'adminpass', 'message'=>'两次密码输入不一致', 'on'=>['changepass','adminadd','changepassword']],
			['repass', 'required', 'message'=>'确认密码不能为空', 'on'=>['changepass','adminadd','changepassword']],
			['adminuser', 'uniqueUser','on'=>['adminadd']],
			['adminemail', 'uniqueEmail','on'=>['adminadd','changeemail']],
			['oldpass', 'validateOldpass', 'on'=>['changepassword']],
			['oldpass', 'required', 'message'=>'原密码不能为空', 'on'=>['changepassword']],
		];
	}
	public function validatePass()
	{
		if (!$this->hasErrors()) {
			$data=self::find()->where('adminuser= :user and adminpass= :pass ',[':user'=>$this->adminuser,':pass'=>md5($this->adminpass)])->one();
			if (is_null($data)) {
				$this->addError('adminpass','用户名与密码不匹配');
			}
		}
	}
	public function validateOldPass()
	{
		if (!$this->hasErrors()) {
			$data=self::find()->where('adminuser= :user and adminpass= :pass ',[':user'=>$this->adminuser,':pass'=>md5($this->oldpass)])->one();
			if (is_null($data)) {
				$this->addError('oldpass','用户名与密码不匹配');
			}
		}
	}
	public function uniqueUser(){
		if (!$this->hasErrors()) {
			$data=self::find()->where('adminuser= :user',[':user'=>$this->adminuser])->one();
			if (!is_null($data)) {
				$this->addError('adminuser','该账号名已存在');
			}
		}
	}
	public function uniqueEmail(){
		if (!$this->hasErrors()) {
			$data=self::find()->where('adminemail= :email',[':email'=>$this->adminemail])->one();
			if (!is_null($data)) {
				$this->addError('adminemail','该邮箱已被注册');
			}
		}
	}
	public function validateEmail()
	{
		if (!$this->hasErrors()) {
			$data=self::find()->where('adminuser= :user and adminemail= :email',[':user'=>$this->adminuser,':email'=>$this->adminemail])->one();
		}
		if (is_null($data)) {
			$this->addError('adminemail','邮箱与账号不匹配');
		}
	}
	public function login($post)
	{
		$this->scenario='login';
		if ($this->load($post) && $this->validate() ) {
			//登录成功
			$lifetime=$this->rememberMe ? 24*3600 : 0;
			$session=Yii::$app->session;
			//通过设置客户浏览器保存session的cookie的时间长度来实现记住我功能
			session_set_cookie_params($lifetime);
			$session['admin']=[
				'adminuser'=>$this->adminuser,
				'isLogin'=>1,
			];
			$this->updateAll(['logintime'=>time(),'loginip'=>ip2long(Yii::$app->request->userIp)],'adminuser=:user',[':user'=>$this->adminuser]);
			return (bool)$session['admin']['isLogin'];

		}
		return false;
	}
	public function seekPass($data){
		$this->scenario='seekpass';
		if ($this->load($data) && $this->validate()) {
			//发送邮箱
			$time=time();
			$token=$this->createToken($data['Admin']['adminuser'],$time);
			$mailer=Yii::$app->mailer->compose('seekpass',['adminuser'=>$data['Admin']['adminuser'],'time'=>$time,'token'=>$token])
     			->setFrom('18826139825@163.com')
      			->setTo($this->adminemail)
      			->setSubject("慕课商城-找回密码");
      		if($mailer->send()){
      			return true;
      		}
		}
		return false;
	}
	public function createToken($adminuser,$time)
	{
		return md5(md5($adminuser).base64_encode(Yii::$app->request->userIp).md5($time));
	}
	public function changePass($data){
		$this->scenario='changepass';
		if ($this->load($data) && $this->validate()) {
			return (bool)$this->updateAll(['adminpass'=>md5($data['Admin']['adminpass'])],'adminuser= :user',[':user'=>$data['Admin']['adminuser']]);
		}
		return false;
	}
	public function reg($data)
	{
		date_default_timezone_set('PRC');
		$this->scenario='adminadd';
		if ($this->load($data) && $this->validate()) {
			$this->adminuser=$data['Admin']['adminuser'];
			$this->adminpass=md5($data['Admin']['adminpass']);
			$this->adminemail=$data['Admin']['adminemail'];
			$this->createtime=time();
			if ($this->save(false)) {
				return true;
			}
			return false;
		}
		return false;
	}
	public function changeEmail($data)
	{
		$this->scenario='changeemail';
		if ($this->load($data) && $this->validate()) {
			return (bool)$this->updateAll(['adminemail'=>$data['Admin']['adminemail']],'adminuser= :user',[':user'=>$data['Admin']['adminuser']]);
		}
		return false;
	}
	public function changePassword($data){
		$this->scenario='changepassword';
		if ($this->load($data) && $this->validate()) {
			return (bool)$this->updateAll(['adminpass'=>md5($data['Admin']['adminpass'])],'adminuser= :user',[':user'=>$data['Admin']['adminuser']]);
		}
		return false;
	}
}
