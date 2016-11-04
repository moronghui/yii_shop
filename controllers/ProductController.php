<?php

namespace app\controllers;

class ProductController extends \yii\web\Controller
{
	public $layout=false;
    public function actionIndex()
    {
        $this->layout='layout2';
        return $this->render('index');
    }
    public function actionDetail()
    {
    	$this->layout='layout2';
    	return $this->render('detail');
    }

}
