<?php

namespace d4yii2\d4store\models;

use d3system\exceptions\D3ActiveRecordException;
use d4yii2\d4store\models\base\D4StorePacks as BaseD4StorePacks;
use Yii;

/**
 * This is the model class for table "d4store_packs".
 */
class D4StorePacks extends BaseD4StorePacks
{
    /**
     * @throws \d3system\exceptions\D3ActiveRecordException
     */
    public function addStoreProduct(int $storeProductId): void
    {
        $packProduct = new D4StorePackProduct();
        $packProduct->store_product_id = $storeProductId;
        $packProduct->pack_id = $this->id;
        if (!$packProduct->save()) {
            throw new D3ActiveRecordException($packProduct);
        }
        $history = new D4StorePackProductHistory();
        $history->pack_product_id = $packProduct->id;
        $history->store_product_id = $storeProductId;
        $history->pack_id = $this->id;
        $history->setActionAdd();
        $history->time = date('Y-m-d H:i:s');
        if (Yii::$app->has('user') && $userId = Yii::$app->user->id) {
            $history->user_id = $userId;
        }

        if (!$history->save()) {
            throw new D3ActiveRecordException($history);
        }
    }

}
