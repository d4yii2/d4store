<?php

namespace d4yii2\d4store\models;

use d3system\dictionaries\SysModelsDictionary;
use d3system\yii2\db\D3ActiveQuery;

/**
 * This is the ActiveQuery class for [[D4StoreAction]].
 *
 * @see D4StoreAction
 */
class D4StoreActionQuery extends D3ActiveQuery
{
    public function getReservationsByModel($model): self
    {
        return $this->where([
            'type' => D4StoreAction::TYPE_RESERVATION,
            'ref_model_id' => SysModelsDictionary::getIdByClassName(get_class($model)),
            'ref_model_record_id' => $model->id,
            'is_active' => D4StoreAction::IS_ACTIVE_YES
        ]);
    }

    public function getFromProcessByModel($model): self
    {
        return $this->where([
            'type' => D4StoreAction::TYPE_FROM_PROCESS,
            'ref_model_id' => SysModelsDictionary::getIdByClassName(get_class($model)),
            'ref_model_record_id' => $model->id,
            'is_active' => D4StoreAction::IS_ACTIVE_YES
        ]);
    }

    public function getToProcessByModel($model): self
    {
        return $this->where([
            'type' => D4StoreAction::TYPE_TO_PROCESS,
            'ref_model_id' => SysModelsDictionary::getIdByClassName(get_class($model)),
            'ref_model_record_id' => $model->id
        ]);
    }
}
