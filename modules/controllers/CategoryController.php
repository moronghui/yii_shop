<?php

namespace app\modules\controllers;

use app\modules\models\Category;
use Yii;

class CategoryController extends \yii\web\Controller
{
    public function actionLists()
    {
        $this->layout='layout1';
        $model=new Category;
        $cates=$model->getTreeList();
        //var_dump($cates); exit();
        return $this->render('lists',['model'=>$model,'cates'=>$cates]);
    }

    public function actionAdd()
    {
        $model=new Category;
        $list=$model->getOptions();
        $this->layout='layout1';
        $request=Yii::$app->request;
        if ($request->isPost) {
            $post=$request->post();
            if ($model->add($post)) {
                Yii::$app->session->setFlash('info','添加成功');
            }
        }
        $list=$model->getOptions();
        $model->title='';
    	return $this->render('add',['model'=>$model,'list'=>$list]);
    }

    public function actionMod()
    {
        $this->layout='layout1';
        $cateid=Yii::$app->request->get('cateid');
        $model=Category::find()->where('cateid= :id',[':id'=>$cateid])->one();
        $request=Yii::$app->request;
        if ($request->isPost) {
            $post=$request->post();
            if ($model->load($post) && $model->save()) {
                Yii::$app->session->setFlash('info','修改成功');
            }
        }
        $list=$model->getOptions();
        return $this->render('add',['model'=>$model,'list'=>$list]);
    }

    public function actionDel()
    {
        try {
            $cateid=Yii::$app->request->get('cateid');
            if (empty($cateid)) {
                throw new \Exception("参数错误");
                
            }
            $data=Category::find()->where('parentid= :id',[':id'=>$cateid])->one();
            if ($data) {
                throw new \Exception("该分类有子分类，不能删除"); 
            }
            if (!Category::deleteAll('cateid= :id',[':id'=>$cateid])) {
                throw new \Exception("删除失败");
                
            }
            
        } catch (\Exception $e) {
            Yii::$app->session->setFlash('info',$e->getMessage());
        }
        return $this->redirect(['category/lists']);
    }

}
