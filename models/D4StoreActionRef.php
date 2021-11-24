<?php

namespace d4yii2\d4store\models;

use d3system\dictionaries\SysModelsDictionary;
use \d4yii2\d4store\models\base\D4StoreActionRef as BaseD4StoreActionRef;

/**
 * This is the model class for table "d4store_action_ref".
 */
class D4StoreActionRef extends BaseD4StoreActionRef
{

    /**
     * @param object $model
     * @param string|null $type
     * @return D4StoreAction[]
     * @throws \d3system\exceptions\D3ActiveRecordException
     */
    public static function findActionsByModelAll(object $model, string $type = null): array
    {
        $d4StoreActionQuery = D4StoreAction::find()
            ->innerJoin(
                'd4store_action_ref',
                'd4store_action_ref.action_id = d4store_action.id'
            )
            ->where([
                'd4store_action_ref.model_id' => SysModelsDictionary::getIdByClassName(get_class($model)),
                'd4store_action_ref.model_record_id' => $model->id
            ]);
        if ($type) {
            $d4StoreActionQuery->andWhere(['d4store_action.type' => $type]);
        }
        return $d4StoreActionQuery
            ->all();
    }
}
