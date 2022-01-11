<?php

namespace d4yii2\d4store\models;

use d4yii2\d4store\dictionaries\D4StoreStackDictionary;
use \d4yii2\d4store\models\base\D4StoreStack as BaseD4StoreStack;
use yii\web\HttpException;

/**
 * This is the model class for table "d4store_stack".
 */
class D4StoreStack extends BaseD4StoreStack
{
    public function afterSave($insert, $changedAttributes): void
    {
        parent::afterSave($insert, $changedAttributes);
        D4StoreStackDictionary::clearCache();
    }

    public function afterDelete(): void
    {
        parent::afterDelete();
        D4StoreStackDictionary::clearCache();
    }

    /**
     * @throws \yii\web\HttpException
     */
    public static function findForController(int $id, int $companyId): self
    {
        $model = self::findOne($id);
        if (!$model) {
            throw new HttpException(404, 'The requested page does not exist.');
        }

        if ($model->store->company_id !== $companyId) {
            throw new HttpException(404, 'The requested page does not exist.');
        }
        return $model;
    }
}
