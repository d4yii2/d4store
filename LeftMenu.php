<?php
namespace d4yii2\d4store;

use Yii;
class LeftMenu {

    public function list()
    {
        return [
            [
                'label' => Yii::t('d4store', '????'),
                'type' => 'submenu',
                //'icon' => 'truck',
                'url' => ['/d4store/????/index'],
            ],
        ];
    }
}
