<?php

namespace d4yii2\d4store\models;

use d3system\dictionaries\SysModelsDictionary;
use d3system\exceptions\D3ActiveRecordException;
use d3system\yii2\db\D3ActiveQuery;

/**
 * This is the ActiveQuery class for [[D4StoreStoreProduct]].
 *
 * @see D3productProduct
 * @method D4StoreAction getStoreActionActiveOne()
 */
class D4StoreStoreProductQuery extends D3ActiveQuery
{
    public function getRemainStoreProductByProduct(int $productId): self
    {
        return $this
            ->where(['d4store_store_product.product_id' => $productId])
            ->andWhere('d4store_store_product.remain_qnt > 0');
    }

    /**
     * @throws D3ActiveRecordException
     */
    public function getFromProcessByModel($model): self
    {
        return $this
            ->innerJoin(
                'd4store_action',
                'd4store_action.`store_product_id` = d4store_store_product.id'
            )
            ->innerJoin(
                'd4store_action_ref',
                'd4store_action.`id` = d4store_action_ref.`action_id`'
            )
            ->where([
            'd4store_action.type' => D4StoreAction::TYPE_FROM_PROCESS,
            'd4store_action_ref.`model_id`' => SysModelsDictionary::getIdByClassName(get_class($model)),
            'd4store_action_ref.`model_record_id`' => $model->id,
        ]);
    }

}
