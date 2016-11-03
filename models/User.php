<?php
namespace app\models;
use yii\db\ActiveRecord;
use Yii;
use app\models\Profile;

class User extends ActiveRecord
{
    public $repass;
    public $rememberMe=true;
    public $loginname;
    public static function tableName()
    {
        return "{{%user}}";
    }
    public function attributeLabels()
    {
        return [
            'username'=>'用户名',
            'useremail'=>'用户邮箱',
            'userpass'=>'密码',
            'repass'=>'确认密码',
            'oldpass'=>'原密码',
        ];
    }
    public function rules()
    {
        return [
            ['loginname', 'required', 'message'=>'登录名不能为空', 'on'=>['login']],
            ['username', 'required', 'message'=>'用户名不能为空' , 'on'=>['reg','regByEmail']],
            ['username', 'uniqueUser', 'message'=>'该用户名已被注册','on'=>['reg','regByEmail']],
            ['userpass', 'required', 'message'=>'密码不能为空','on'=>['reg','regByEmail','login']],
            ['useremail' , 'required','message'=>'邮箱不能为空','on'=>['reg','regByEmail']],
            ['useremail', 'email', 'message'=>'邮箱格式不正确','on'=>['reg','regByEmail']],
            ['useremail', 'uniqueEmail', 'message'=>'该邮箱已被注册','on'=>['reg','regByEmail']],
            ['repass', 'compare' , 'compareAttribute'=>'userpass', 'message'=>'两次密码输入不一致','on'=>['reg']],
            ['repass', 'required', 'message'=>'确认密码不能为空','on'=>['reg']],
            ['userpass', 'validatePass','on'=>['login']]
        ];
    }

    public function validatePass()
    {
        if (!$this->hasErrors()) {
            $username='username';
            if (preg_match('/@/', $this->loginname)) {
                $username='useremail';
            }
            $data=self::find()->where($username.'= :name and userpass = :pass',[':name'=>$this->loginname,':pass'=>md5($this->userpass)])->one();
            if (is_null($data)) {
                $this->addError('userpass','密码和账号不匹配');
            }
        }
    }
    public function uniqueUser(){
        if (!$this->hasErrors()) {
            $data=self::find()->where('username= :user',[':user'=>$this->username])->one();
            if (!is_null($data)) {
                $this->addError('username','该账号名已存在');
            }
        }
    }
    public function uniqueEmail(){
        if (!$this->hasErrors()) {
            $data=self::find()->where('useremail= :email',[':email'=>$this->useremail])->one();
            if (!is_null($data)) {
                $this->addError('useremail','该邮箱已被注册');
            }
        }
    }
    public function reg($data,$scenario='reg')
    {
        date_default_timezone_set("PRC");
        $this->scenario=$scenario;
        if ($this->load($data) && $this->validate()) {
            $this->userpass=md5($data['User']['userpass']);
            $this->createtime=time();
            if ($this->save(false)) {
                return true;
            }
            return false;
        }
        return false;
    }
    public function getProfile()
    {
        return $this->hasOne(Profile::className(),['userid'=>'userid']);
    }
    public function emailReg($data)
    {
        $userpass=$this->getUniquePass();
        $username='imooc_'.$userpass;
        $data['User']['userpass']=$userpass;
        $data['User']['username']=$username;
        if ($this->reg($data,'regByEmail')) {
            if (!$this->hasErrors()) {
                $mailer=Yii::$app->mailer->compose('createMember',['userpass'=>$userpass,'username'=>$username])
                    ->setFrom('18826139825@163.com')
                    ->setTo($data['User']['useremail'])
                    ->setSubject('慕课商城-注册');
                if ($mailer->send()) {
                    return true;
                }
            }
            return false;
        }
        return false;
    }
    public function getUniquePass(){
        $str='0123456789';
        $str=str_shuffle($str);
        $str=substr($str,0,6);
        return $str;
    }
    public function login($data)
    {
        $this->scenario='login';
        if ($this->load($data) && $this->validate()) {      
           $lifetime=$this->rememberMe? 3600*7 :0;
           $session=Yii::$app->session;
           session_set_cookie_params($lifetime);
           $session['loginname']=$this->loginname;
           $session['isLogin']=1;
           return (bool)$session['isLogin'];
        }
        return false;
    }
}
