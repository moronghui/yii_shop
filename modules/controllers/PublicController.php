<?php

namespace app\modules\controllers;

use yii\web\Controller;
use app\modules\models\Admin;
use Yii;
use app\modules\controllers\CommonController;
/**
 * Public controller for the `admin` module
 */
class PublicController extends CommonController
{
    /**
     * Renders the login view for the module
     * @return string
     */
    public function actionLogin()
    {
        $this->layout=false;
        $model=new Admin;
        $request=Yii::$app->request;
        if ($request->isPost) {
			if($model->login($request->post())){
				$this->redirect(['default/index']);
				Yii::$app->end();
			};        	
        }
        return $this->render('login',['model'=>$model]);
    }
    public function actionLogout()
    {
    	Yii::$app->session->removeAll();
    	if (!isset(Yii::$app->session['admin']['isLogin'])) {
    		$this->redirect(['public/login']);
    		Yii::$app->end();
    	}
    	$this->goback();
    }
    public function actionSeekpassword()
    {
        $this->layout=false;
        $model=new Admin;
        if (Yii::$app->request->isPost) {
            $post=Yii::$app->request->post();
            if($model->seekPass($post)){
                Yii::$app->session->setFlash('info','邮箱发送成功，请查收');
            }
        }
        return $this->render('seekpassword',['model'=>$model]);   
    }
}
