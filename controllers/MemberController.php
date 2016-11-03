<?php

namespace app\controllers;

use app\models\User;
use Yii;

class MemberController extends \yii\web\Controller
{
	//public $layout=false;
    public function actionAuth()
    {
        $this->layout='layout2';
        $model=new User;
        $request=Yii::$app->request;
        if ($request->isPost) {
            $post=$request->post();
            if ($model->login($post)) {
                $this->redirect(['index/index']);
                Yii::$app->end();
            }
                
        }
        return $this->render('auth',['model'=>$model]);
    }
    public function actionReg(){
       $this->layout='layout2';
        $model=new User;
        $request=Yii::$app->request;
        if ($request->isPost) {
            $post=$request->post();
            if ($model->emailReg($post)) {
                Yii::$app->session->setFlash('info','创建成功');
            }else{
                Yii::$app->session->setFlash('info','创建失败');
            }
        }
        $model->useremail='';
        return $this->render('auth',['model'=>$model]); 
    }
    public function actionLogout()
    {
        $session=Yii::$app->session;
        $session->remove('loginname');
        $session->remove('isLogin');
        if (!$session['isLogin']) {
            return $this->goBack(Yii::$app->request->referrer);
        }

    }

}
