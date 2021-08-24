<?php

namespace d4yii2\d4store\models;

use d3system\yii2\db\D3ActiveQuery;

/**
 * This is the ActiveQuery class for [[D4StoreStoreProduct]].
 *
 * @see D4StoreStoreProduct
 */
class D4StoreStoreProductQuery extends D3ActiveQuery
{
    public function getRemainStoreProductByProduct(int $productId): self
    {
        return $this
            ->where(['d4store_store_product.product_id' => $productId])
            ->andWhere('d4store_store_product.remain_qnt > 0');
    }
}
