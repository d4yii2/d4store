<?php

namespace d4yii2\d4store\models;

use d4yii2\d4store\dictionaries\D4StoreStoreDictionary;
use \d4yii2\d4store\models\base\D4StoreStore as BaseD4StoreStore;

/**
 * This is the model class for table "d4store_store".
 */
class D4StoreStore extends BaseD4StoreStore
{
    public function afterSave($insert, $changedAttributes): void
    {
        parent::afterSave($insert, $changedAttributes);
        D4StoreStoreDictionary::clearCache();
    }

    public function afterDelete(): void
    {
        parent::afterDelete();
        D4StoreStoreDictionary::clearCache();
    }

    public static function optsUnit(): array
    {
        return D4StoreStoreDictionary::getList();
    }
}
