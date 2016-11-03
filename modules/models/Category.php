<?php

namespace app\modules\models;

use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "{{%category}}".
 *
 * @property string $cateid
 * @property string $title
 * @property string $parentid
 * @property string $createtime
 */
class Category extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%category}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['parentid', 'createtime'], 'integer'],
            [['title'], 'string', 'max' => 32],
            ['parentid','required','message'=>'上级分类不能为空'],
            ['title','required','message'=>'分类名称不能为空']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'cateid' => 'Cateid',
            'title' => '分类名称',
            'parentid' => '上级分类',
            'createtime' => 'Createtime',
        ];
    }

    /**
    *添加分类
    */
    public function add($data)
    {
        $data['Category']['createtime']=time();
        if ($this->load($data) && $this->save()) {
            return true;
        }
        return false;
    }

    public function getData()
    {
        $cates=self::find()->all();
        $cates=ArrayHelper::toArray($cates);
        return $cates;
    }

    public function getTree($cates,$pid=0)
    {
        $tree=[];
        foreach ($cates as $cate) {
            if ($cate['parentid']==$pid) {
                $tree[] =$cate;
                $tree=array_merge($tree,$this->getTree($cates,$cate['cateid']));
            }
        }
        return $tree;
    }

    public function setPrefix($data,$p='|.....')
    {
        $tree=[];
        $num=1;
        $prefix=[0=>1];
        while ($val=current($data)) {
            $key=key($data);
            if ($key>0) {
                if ($data[$key-1]['parentid']!=$val['parentid']) {
                    $num++;
                }
            }
            if (array_key_exists($val['parentid'],$prefix)) {
                $num=$prefix[$val['parentid']];
            }
            $val['title']=str_repeat($p,$num).$val['title'];
            $prefix[$val['parentid']]=$num;
            $tree[]=$val;
            next($data);
        }
        return $tree;
    }

    public function getOptions()
    {
        $data=$this->getData();
        $tree=$this->getTree($data);
        $tree=$this->setPrefix($tree);
        $options=['添加顶级分类'];
        foreach ($tree as $cate) {
            $options[$cate['cateid']]=$cate['title'];
        }
        return $options;
    }

    public function getTreeList()
    {
        $data=$this->getData();
        $tree=$this->getTree($data);
        $tree=$this->setPrefix($tree);
        return $tree;
    }
}
