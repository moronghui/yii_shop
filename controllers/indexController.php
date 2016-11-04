<?php

namespace app\controllers;

class IndexController extends \yii\web\Controller
{
    public function actionIndex()
    {
    	//$this->layout=false;
    	$this->layout='layout1';
        return $this->render('index');
    }

}
