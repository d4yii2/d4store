<?php

namespace d4yii2\d4store\models;

use d3yii2\d3product\models\D3productProduct as BaseModel;

class D3productProduct extends BaseModel
{
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getD4StoreStoreProducts()
    {
        return $this
            ->hasMany(D4StoreStoreProduct::class, ['product_id' => 'id'])
            ->inverseOf('product');
    }
}