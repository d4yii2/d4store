<?php

namespace d4yii2\d4store\models;

use d3system\dictionaries\SysModelsDictionary;
use \d4yii2\d4store\models\base\D4StoreAction as BaseD4StoreAction;

/**
 * This is the model class for table "d4store_action".
 */
class D4StoreAction extends BaseD4StoreAction
{
    public function getModelIdFromRef(string $modelClassName): ?int
    {
        return self::find()
            ->select(['d4store_action_ref.model_record_id'])
            ->innerJoin(
                'd4store_action_ref',
                'd4store_action.id = d4store_action_ref.action_id'
            )
            ->where([
                'd4store_action.store_product_id' => $this->store_product_id,
                'd4store_action_ref.model_id' => SysModelsDictionary::getIdByClassName($modelClassName)
            ])
            ->scalar();
    }
}
