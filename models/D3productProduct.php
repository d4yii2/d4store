<?php

namespace d4yii2\d4store\models;

use d3yii2\d3product\models\D3productProduct as BaseModel;
use yii\db\ActiveQuery;

/**
 *
 * @property-read ActiveQuery $d4StoreStoreProducts
 */
class D3productProduct extends BaseModel
{
    /**
     * @return ActiveQuery
     */
    public function getD4StoreStoreProducts(): ActiveQuery
    {
        return $this
            ->hasMany(D4StoreStoreProduct::class, ['product_id' => 'id'])
            ->inverseOf('product');
    }
}