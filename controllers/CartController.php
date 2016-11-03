<?php

namespace app\controllers;

class CartController extends \yii\web\Controller
{
	//public $layout=false;
    public function actionIndex()
    {
        $this->layout='layout1';
        return $this->render('index');
    }

}
