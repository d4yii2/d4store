<?php

namespace d4yii2\d4store;

use d3system\yii2\base\D3Module;
use Yii;
use yii\caching\FileCache;

/**
 * @property FileCache $cache - for product list storing in runtime
 */
class Module extends D3Module
{
    public $controllerNamespace = 'd4yii2\d4store\controllers';

    public $leftMenu = 'd4yii2\d4store\LeftMenu';

    public function getLabel(): string
    {
        return Yii::t('d4store', 'd4store');
    }
}
