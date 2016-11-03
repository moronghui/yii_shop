<?php

namespace app\modules\controllers;
use Yii;
use app\modules\models\Admin;
use yii\data\Pagination;

class ManageController extends \yii\web\Controller
{
    public function actionMailchangepass()
    {
    	$this->layout=false;
    	$request=Yii::$app->request;
    	$time=$request->get('timestamp');
    	$adminuser=$request->get('adminuser');
    	$token=$request->get('token');
    	$model=new Admin;
    	$mytoken=$model->createToken($adminuser,$time);
    	if ($mytoken!=$token) {
    		$this->redirect(['public/login']);
    	}
    	if (time()-$time>300) {
    		$this->redirect(['public/login']);
    	}

        if ($request->isPost) {
            $post=$request->post();
            if ($model->changePass($post)) {
                Yii::$app->session->setFlash('info','修改密码成功');
            }
        }
    	$model->adminuser=$adminuser;
    	return $this->render('mailchangepass',['model'=>$model]);
    }
    public function actionManagers()
   {
        $this->layout='layout1';
        $model=Admin::find();
        $count=$model->count();
        $pageSize=Yii::$app->params['pageSize']['manage'];
        $pager=new Pagination(['totalCount'=>$count,'pageSize'=>$pageSize]);
        $managers=$model->offset($pager->offset)->limit($pager->limit)->all();
        return $this->render('managers',['managers'=>$managers,'pager'=>$pager]);
    }
    public function actionReg()
    {
        $this->layout='layout1';
        $model=new Admin;
        if (Yii::$app->request->isPost ){
            $post=Yii::$app->request->post();
            if ($model->reg($post)) {
                Yii::$app->session->setFlash('info','添加成功');
            }else{
                Yii::$app->session->setFlash('info','添加失败');
            }

        }
        $model->adminpass='';
        $model->repass='';
        return $this->render('reg',['model'=>$model]);
    }
    public function actionDel(){
        $adminid=(int)Yii::$app->request->get('adminid');
        if (empty($adminid)) {
            $this->redirect(['manage/managers']);
        }
        $model=new Admin;
        if ($model->deleteAll('adminid= :adminid',[':adminid'=>$adminid])) {
            Yii::$app->session->setFlash('info','删除成功');
            $this->redirect(['manage/managers']);
        }
    }
    public function actionChangeemail(){
        $this->layout='layout1';
        $model=Admin::find()->where('adminuser= :user',[':user'=>Yii::$app->session['admin']['adminuser']])->one();
        $request=Yii::$app->request;
        if ($request->isPost) {
            $post=$request->post();
            if ($model->changeEmail($post)) {
                Yii::$app->session->setFlash('info','修改成功');
            }
        }
        $model->adminpass='';
        return $this->render('changeemail',['model'=>$model]);
    }
    public function actionChangepass(){
        $this->layout='layout1';
        $model=Admin::find()->where('adminuser= :user',[':user'=>Yii::$app->session['admin']['adminuser']])->one();
        $request=Yii::$app->request;
        if ($request->isPost) {
            $post=$request->post();
            if ($model->changePassword($post)) {
                Yii::$app->session->setFlash('info','修改成功');
            }
        }
        $model->adminpass='';
        $model->repass='';
        $model->oldpass='';
        return $this->render('changepass',['model'=>$model]);
    }

}