<?php

namespace app\modules\controllers;

use yii\web\Controller;
use Yii;
use app\models\User;
use app\models\Profile;
use yii\data\Pagination;
use Exception;

/**
 * User controller for the `admin` module
 */
class UserController extends Controller
{
    public function actionUsers()
    {
        $this->layout='layout1';
        $model=User::find()->joinWith('profile');
        $count=$model->count();
        $pageSize=Yii::$app->params['pageSize']['user'];
        $pager=new Pagination(['totalCount'=>$count,'pageSize'=>$pageSize]);
        $users=$model->offset($pager->offset)->limit($pager->limit)->all();
        return $this->render('users',['users'=>$users,'pager'=>$pager]);
    }
    public function actionReg()
    {
        $this->layout='layout1';
        $model=new User;
        $request=Yii::$app->request;
        if ($request->isPost) {
            $post=$request->post();
            if ($model->reg($post)) {
                Yii::$app->session->setFlash('info','添加成功');
            }
        }
        $model->userpass='';
        $model->repass='';
        return $this->render('reg',['model'=>$model]);
    }
    public function actionDel()
    {
        try {
            $id=(int)Yii::$app->request->get('id');
            if (empty($id)) {
                throw new Exception();
            }
            $trans=Yii::$app->db->beginTransaction();
            if ($obj=Profile::find()->where('userid= :id',[':id'=>$id])->one()) {
                $res=Profile::deleteAll('userid= :id',[':id'=>$id]);
                if (empty($res)) {
                    throw new Exception("Error Processing Request", 1); 
                }
            }
            if (!User::deleteAll('userid= :id',[':id'=>$id])) {
                throw new Exception("Error Processing Request", 1);
            }
            $trans->commit();
        } catch (Exception $e) {
            if (Yii::$app->db->getTransaction()) {
                $trans->rollback();
            }
        }
        $this->redirect(['user/users']);
    }
}
